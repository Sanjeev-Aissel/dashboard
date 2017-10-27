require 'fileutils'
require 'byebug'

task :reports, [:max_records] => [:environment] do |t, args|

  REPORTS_CONFIG = YAML.load_file("#{Rails.root}/config/reports.yml")[Rails.env]
  
  @start_time = Time.now
  @reports = REPORTS_CONFIG[:reports]
  @logfile = Rails.root.to_s + "/log/" + REPORTS_CONFIG[:reports_log_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  #@reportfile = Rails.root.to_s + "/log/" + REPORTS_CONFIG[:report_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  
  logit "Subject: #{Rails.env.capitalize} Report log"
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  logit "Start of Report task at " + @start_time.to_s
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  
  run_reports

end

def run_reports
  #cmd = "cat #{@logfile}|sendmail \"#{recipient}\""
  db_config   = Rails.configuration.database_configuration
  db_host     = db_config[Rails.env]["host"]
  db_database = db_config[Rails.env]["database"]
  db_username = db_config[Rails.env]["username"]
  db_password = db_config[Rails.env]["password"]  
  
  @reports.each do |report|
  
    @reportfile = Rails.root.to_s + "/log/" + report.to_s + REPORTS_CONFIG[:report_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
    File.write(@reportfile, "Subject: #{Rails.env.capitalize} #{report.to_s} Report" + "\n\n", mode: 'a')
    
    cmd = "psql --host #{db_host} --username #{db_username} --no-password -f #{Rails.root.to_s + "/db/reports/" + report.to_s + ".sql"} #{db_database} >> #{@reportfile}"
  
    if system(cmd)
      logit "Report file #{@reportfile} has been created"
      
      # send report emails   
      total_recipients = 0   
      
      begin
        REPORTS_CONFIG[:email_recipients].each {|recipient|
          total_recipients += 1
          if File.exist?(@reportfile)
            cmd = "cat #{@reportfile}|sendmail \"#{recipient}\""
          else
            cmd = "echo 'No report file exists'|sendmail \"#{recipient}\""
          end

          logit "Email command: " + cmd.to_s

          if system(cmd)
            logit "Report email sent successfully to #{recipient}"
          else
            logit "Problem sending report email to #{recipient}"
          end
        }    
      rescue
        logit "Terminal error sending email"        
      end
      
    else
      logit "There was a problem creating the report file"
    end
  
  end # reports
  
end

def logit(message)

  File.write(@logfile, message.to_s + "\n", mode: 'a')
  
end


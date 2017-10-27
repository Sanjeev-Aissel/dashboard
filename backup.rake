require 'fileutils'
require 'byebug'

task :backup, [:max_records] => [:environment] do |t, args|

  BACKUP_CONFIG = YAML.load_file("#{Rails.root}/config/backup.yml")[Rails.env]
  
  @start_time = Time.now
  @logfile = Rails.root.to_s + "/log/" + BACKUP_CONFIG[:backup_log_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  @backupfile = Rails.root.to_s + 
                BACKUP_CONFIG[:backup_directory] + 
                BACKUP_CONFIG[:backup_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S")).gsub("(env)", Rails.env.to_s)
  
  logit "Subject: #{Rails.env.capitalize} DB backup log"
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  logit "Start of DB backup task at " + @start_time.to_s
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  
  db_backup
  
  send_backup_email

end

def db_backup
  #cmd = "cat #{@logfile}|sendmail \"#{recipient}\""
  db_config   = Rails.configuration.database_configuration
  db_host     = db_config[Rails.env]["host"]
  db_database = db_config[Rails.env]["database"]
  db_username = db_config[Rails.env]["username"]
  db_password = db_config[Rails.env]["password"]  
  
  cmd = "pg_dump --host #{db_host} --username #{db_username} --verbose --clean --no-owner --no-acl #{db_database}|zip > #{@backupfile}"
  
  if system(cmd)
    logit "DB has been backup up to file #{@backupfile}"
  else
    logit "There was a problem performing the backup to file"
  end
  
end

def logit(message)

  File.write(@logfile, message.to_s + "\n", mode: 'a')
  
end

def send_backup_email

  logit " "
  logit "---------------------------------------------------------------"
  logit "Email send started"
  
#  recipients = ""
  total_recipients = 0

  BACKUP_CONFIG[:email_recipients].each {|recipient|
    total_recipients += 1
    if File.exist?(@logfile)
      cmd = "cat #{@logfile}|sendmail \"#{recipient}\""
    else
      cmd = "echo 'No log file exists'|sendmail \"#{recipient}\""
    end

    logit "Email command: " + cmd.to_s

    if system(cmd)
      logit "Email sent successfully to #{recipient}"
    else
      logit "Problem sending email to #{recipient}"
    end
  }

rescue

  logit "Terminal error sending email"

end

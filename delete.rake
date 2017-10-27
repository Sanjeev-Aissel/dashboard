require 'fileutils'
require 'byebug'

task :delete, [:max_records] => [:environment] do |t, args|

  DELETE_CONFIG = YAML.load_file("#{Rails.root}/config/delete.yml")[Rails.env]

  @access_key_id = ENV["ACCESS_KEY_ID"]
  @secret_access_key = ENV["SECRET_ACCESS_KEY"]
  
  @total_find_time = 0
  @start_time = Time.now
  @logfile = Rails.root.to_s + "/log/" + DELETE_CONFIG[:delete_log_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  
  logit "Subject: #{Rails.env.capitalize} bucket delete log"
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  logit "Start of bucket deletion task at " + @start_time.to_s
  logit "---------------------------------------------------------------"
  logit "---------------------------------------------------------------"
  
  @total_records_deleted = 0
  @total_records_skipped = 0
  @total_records_read    = 0
  @total_errors          = 0
  @total_files           = 0
  
  bucket_delete
  
  send_delete_email

end

def bucket_delete

  logit "---------------------------------------------------------------"
	logit "Create an instance of the s3 client"
	s3 = AWS::S3.new(access_key_id: @access_key_id, secret_access_key: @secret_access_key)
  logit " "
  
	logit "Get the AWS bucket..."
	logit "Starting at " + Time.now.to_s
	bucket = s3.buckets['plantprodline']
	logit "Ending at " + Time.now.to_s
	logit "Done getting the AWS bucket"
	logit " "
	
	logit "Start processing bucket objects..."
	logit " "
	
	bucket_keys = []
	
	if Rails.env == 'development'
	  @max_delete_recs = 0
	end
	
	bucket.objects.each do |object|
	
	  #if @max_records > 0 && @total_records_read == @max_records
    #  end_stats	  
	  #  exit
	  #end
	
	  @total_records_read += 1
	  
    params = Hash.from_xml(object.read)

    begin
      params["PUBLISH"] = params["PUBLISH"].each_with_object({}) do |(k, v), h|
        h[k.downcase] = v
      end
    rescue  	  
      @total_errors += 1
      logit "---------------------------------------------------------------"
      logit "### Unable to parse parameters for record #" + @total_records_read.to_s + " ###"
      next
    end
    	
    lpn = params["PUBLISH"]["lpn"]||nil
    if lpn && !lpn.blank?
      # do nothing
    else
      @total_errors += 1
      logit "---------------------------------------------------------------"
      logit "### No LPN found in record # " + @total_records_read.to_s + " ###"
      next
    end
      
    find_time_start = Time.now
	  @flag = Post.where("lpn" => params["PUBLISH"]["lpn"])
	  find_time_end = Time.now
	
	  @total_find_time += find_time_end - find_time_start
	
	  if @flag.blank? # post not found for lpn
		  
		  @total_records_skipped += 1
		
    else

      logit "---------------------------------------------------------------"
      #logit "*** LPN " + lpn + " added to deletion queue ***" 
      
      #bucket_keys << object.key.to_s
      #byebug
      #bucket_keys << object

      #if object.delete
      object.delete
      logit "*** Bucket deleted for LPN " + lpn + " ***"      
      @total_records_deleted += 1
      #else
      #  logit "*** Error deleting bucket for LPN " + lpn + " ***"
      #  @total_errors += 1
      #end
      
    end	
    	
  end # bucket.objects.each
  
  #byebug
  #puts bucket_keys.to_s
  
  #if !bucket_keys.empty?
  #  logit "---------------------------------------------------------------"
  #      
  #  if bucket.objects.delete(bucket_keys)
  #    logit "*** Bucket objects deleted ***"
  #  else
  #    logit "*** Error deleting bucket objects ***"
  #  end
  #end

rescue

  logit "Terminal error on bucket deletion"

end

def logit(message)

  File.write(@logfile, message.to_s + "\n", mode: 'a')
  
end

def send_delete_email

  logit " "
  logit "---------------------------------------------------------------"
  logit "Email send started"
  
#  recipients = ""
  total_recipients = 0

  DELETE_CONFIG[:email_recipients].each {|recipient|
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

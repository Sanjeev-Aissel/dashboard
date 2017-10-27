require 'aws-sdk-v1'
require 'byebug'
require 'fileutils'
require 'csv'

#task :import => :environment do
task :import, [:max_records] => [:environment] do |t, args|

  IMPORT_CONFIG = YAML.load_file("#{Rails.root}/config/import.yml")[Rails.env]

  @start_time = Time.now
  @total_find_time = 0
  @total_create_time = 0
  @logfile = Rails.root.to_s + "/log/" + IMPORT_CONFIG[:import_log_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  @emailfile = Rails.root.to_s + "/log/" + IMPORT_CONFIG[:import_email_file].gsub("(timestamp)", Time.now.strftime("%Y-%m-%d-%H:%M:%S"))
  @processed_files = []
  
  log_it "Subject: #{Rails.env.capitalize} import summary log", true
  log_it "---------------------------------------------------------------", true
  log_it "---------------------------------------------------------------"
  log_it "Start of data import task at " + @start_time.to_s, true
  log_it "---------------------------------------------------------------", true
  log_it "---------------------------------------------------------------"
  
  if File.exist? IMPORT_CONFIG[:import_lock_file]
  
    log_it "Lock file exists...new import process halted", true
    
  else
  
    log_it "Create lock file..."
    FileUtils.touch IMPORT_CONFIG[:import_lock_file]

    @total_posts_created   = 0
    @total_records_skipped = 0
    @total_records_read    = 0
    @total_errors          = 0
    @total_files           = 0
    @total_deleted_buckets = 0

    @max_records = args[:max_records].to_i
    if @max_records
      log_it "Max number of records set at " + @max_records.to_s
    end

    if IMPORT_CONFIG[:import_files]
      file_import
    else
      log_it "File import disabled in import configuration", true
    end
    if IMPORT_CONFIG[:import_buckets]
      bucket_import
    else 
      log_it "Bucket import disabled in import configuration", true
    end
    end_stats
    
    log_it "Remove lock file..."
    FileUtils.rm IMPORT_CONFIG[:import_lock_file]
  
  end
  
  # Move to independent task, so can be scheduled once per day
  #db_backup
  
  send_import_email 

end

def delete_processed_files

  log_it "---------------------------------------------------------------"
  log_it "---------------------------------------------------------------"
  log_it " Remove processed files", true
  log_it "---------------------------------------------------------------"
  log_it "---------------------------------------------------------------"  

  @processed_files.each do |filename|
  
    log_it "Removing file " + filename.to_s  
  
    if FileUtils.rm(filename)
      log_it "File #{filename} removed"
    else
      log_it "Unable to remove file #{filename}"
    end
    
  end
  
end

def file_import

  # Get a list of all files in configured upload directories
  filenames = Dir[IMPORT_CONFIG[:files_to_import]]

  # Process each file
  filenames.each do |filename|
  
    filetype = nil
    
    begin
      begin
        csv_count = CSV.read(filename, { :col_sep => "\t" }).first.count
      rescue
        csv_count = 0
      end
      if csv_count > 1
        filetype = 'TAB'
      else
        case File.extname(filename)
          when /txt|TXT/
            filetype = 'TXT' 
          when /csv|CSV/
            filetype = 'CSV'
          when /xml|XML/
            filetype = 'XML'        
          else 
            logit "Unknown file type: #{filename}"
            next
        end        
      end
    rescue Exception => e
      raise "Error determining file type: #{filename}"
      next      
    end
    
    log_it "---------------------------------------------------------------"
    log_it "Processing file " + filename.to_s
    @total_files += 1
    
    parser = Nori.new
    file_data = ""
    
    log_it "Opening file " + filename.to_s + " and reading file data"    
    if filetype == 'CSV'
      file_data = CSV.read(filename, { :col_sep => ',' })
    elsif filetype == 'TAB'
      file_data = CSV.read(filename, { :col_sep => "\t" })
    else # XML
      filetype == 'XML'
      File.open(filename, 'r') do |opened_file|
        while line = opened_file.gets
          file_data << line
        end
      end
    end
    
    if !file_data.blank?
      
      lpn = ""
          
      begin
        case filetype
          when 'TXT'
            parsed_data = parse_fixed_length_data(file_data) 
          when 'CSV'
            parsed_data = parse_delimited_data(file_data)
          when 'TAB'
            parsed_data = parse_delimited_data(file_data)            
          when 'XML'
            parsed_data = parser.parse(file_data)
            if parsed_data["PUBLISH"]
              parsed_data = parsed_data["PUBLISH"]
            else
              log_it "File not in proper format...skipping"
              next
            end               
          else 
            # do nothing
        end          
      rescue
        log_it "ERROR parsing data for file"
        @total_errors += 1
        next
      end
      
      if parsed_data.class.to_s == 'Array'
        parsed_data.each do |p_data|
          handle_parsed_data(p_data)
        end
      elsif parsed_data.class.to_s == 'Hash'
        handle_parsed_data(parsed_data)
      end   
      
    end #XML data empty?

    log_it "Moving file to processed"
    renamed_filename = filename.to_s + "." + Time.now.to_s
    File.rename(filename, renamed_filename)
    @processed_files << renamed_filename.to_s   

    log_it "Done processing file"
    log_it "---------------------------------------------------------------"

  end
  
  log_it "Done processing all files", true
  
rescue

  log_it "Terminal error on import", true

end # FILE_IMPORT

def handle_parsed_data(parsed_data)

  if !parsed_data.blank?
  
    @total_records_read += 1
    
    create_time_start = Time.now
    post = Post.new

    begin     
      parsed_data.each do |attr_name, attr_value|
        attr_name = attr_name.downcase
        if attr_name == 'endpointkey'
          attr_name = 'sourcepointkey'
        end
        if post.has_attribute?(attr_name)
          if attr_name == "lpn"
            lpn = attr_value
          end
          post.send("#{attr_name}=", attr_value)
        end
      end
    rescue
      log_it "Error processing parsed file data"
      @total_errors += 1
      return
    end
  
    begin
      
      if Key.valid?('publisher', post.companykey, post.publisherkey, post.sourcepointkey)
        if post.save
        
          c = ConsumedLpn.new("lpn"=>post.lpn, "keytype"=>'publisher')
          c.companykey ||= post.companykey if post.companykey
          c.agentkey ||= post.publisherkey if post.publisherkey
          c.endpointkey ||= post.sourcepointkey if post.sourcepointkey
          #c.plant ||= params[:plant] if params[:plant]
          c.trans_type = 'publisher'
          #c.parm1 ||= params[:parm1] if params[:parm1]
          c.save            
        
          log_it "*** Create new post for LPN " + post.lpn + " ***"
          @total_posts_created += 1                  
        else # this is a dupe
          log_it "*** No post created for LPN " + post.lpn + " ***"
          @total_records_skipped += 1
        end
      else # keys not valid
        log_it "Invalid keys for LPN " + post.lpn.to_s
        @total_errors += 1
      end
    rescue
      log_it "Error saving post for LPN " + post.lpn
      @total_errors += 1
    end
  
    create_time_end = Time.now
    @total_create_time += create_time_end - create_time_start
    
  end #parsed data empty?
rescue
  log_it "Error handling parsed data"
  @total_errors += 1
end

def parse_fixed_length_data(file_data)
  
 #"COMPANYKEY"
 #"PUBLISHERKEY"
 #"SOURCEPOINTKEY" 
 #"LPN",
 #"Date",
 #"TIME",
 #"PARTNUMBER",
 #"QUANTITY",
 #"PRODUCTIONDATE",
 #"EXPIRATIONDATE",
 #"PARTDESCRIPTION1",
 #"BATCH",
 #"UPC12"  
  
  out_data = []
  fields = [12,12,14,13,10,8,80,14,14,14,80,30,50] # TODO replace with configuration
  field_pattern = "A#{fields.join('A')}"
  @col_headers = []
  rownum = 0
  
  file_data.split( /\r?\n/ ).each do |file_line|
    if rownum == 0
      @col_headers = file_line.unpack(field_pattern)
      rownum += 1
    else
    
      h = Hash.new
      colnum = 0
      file_line = file_line.unpack(field_pattern)
      
      @col_headers.size.times do
        h[@col_headers[colnum].upcase] = file_line[colnum]
        colnum += 1
      end  
    
      out_data << h
    end
  end    

  return out_data
  
end

def parse_delimited_data(file_data)
  
  out_data = []
  @col_headers = []
  rownum = 0
  
  file_data.each do |file_line|
    if rownum == 0
      @col_headers = file_line
      rownum += 1
    else
    
      h = Hash.new
      colnum = 0
      
      @col_headers.size.times do
        if @col_headers[colnum]
          h[@col_headers[colnum].upcase] = file_line[colnum]
          colnum += 1
        end
      end  
    
      out_data << h
    end
  end    

  return out_data
  
end

def bucket_import

  @access_key_id = ENV["ACCESS_KEY_ID"]
  @secret_access_key = ENV["SECRET_ACCESS_KEY"]
  @bucket_name = ENV["BUCKET_NAME"]

  log_it "---------------------------------------------------------------"
	log_it "Create an instance of the s3 client"
	
  # Aws gem version update	
  s3 = AWS::S3.new(access_key_id: @access_key_id, secret_access_key: @secret_access_key)
	#s3 = Aws::S3::Client.new(access_key_id: @access_key_id, secret_access_key: @secret_access_key)
	#s3 = Aws::S3::Resource.new(access_key_id: @access_key_id, secret_access_key: @secret_access_key)
  log_it " "
  
	log_it "Get the AWS bucket..."
	log_it "Starting at " + Time.now.to_s
	bucket = s3.buckets[@bucket_name]
	#bucket = s3.list_objects(bucket: 'plantprodline')
	#bucket = s3.buckets(name: 'plantprodline')
	#bucket = s3.bucket('plantprodline')
	
	log_it "Ending at " + Time.now.to_s
	log_it "Done getting the AWS bucket"
	log_it " "
	
	log_it "Start processing bucket objects...", true
	log_it " "
	
	bucket.objects.each do |object|
	
	  if @max_records > 0 && @total_records_read == @max_records
      end_stats	  
	    exit
	  end
	
	  @total_records_read += 1
	  
	  begin
      params = Hash.from_xml(object.read)
    rescue Exception => exc
      params = nil
      @total_errors += 1
      log_it("Error reading bucket object #{object.key}: #{exc.message}")
      next
    end 
    
    if params
      begin
        params["PUBLISH"] = params["PUBLISH"].each_with_object({}) do |(k, v), h|
          h[k.downcase] = v
        end
      rescue  	  
        @total_errors += 1
        log_it "---------------------------------------------------------------"
        log_it "### Unable to parse parameters for record #" + @total_records_read.to_s + " ###"
        next
      end
      	
      lpn = params["PUBLISH"]["lpn"]||nil
      if lpn && !lpn.blank?
        # do nothing
      else
        @total_errors += 1
        log_it "---------------------------------------------------------------"
        log_it "### No LPN found in record # " + @total_records_read.to_s + " ###"
        next
      end
        
      find_time_start = Time.now
	    @flag = Post.where("lpn" => params["PUBLISH"]["lpn"])
	    find_time_end = Time.now
	
	    @total_find_time += find_time_end - find_time_start
	
	    if @flag.blank?
	      log_it "---------------------------------------------------------------"
	      log_it "*** Create new post for LPN " + lpn + " ***"
	      create_time_start = Time.now
		    @post = Post.new(params["PUBLISH"])
		    if @post.save
		    
          c = ConsumedLpn.new("lpn"=>@post.lpn, "keytype"=>'publisher')
          c.companykey ||= @post.companykey if @post.companykey
          c.agentkey ||= @post.publisherkey if @post.publisherkey
          c.endpointkey ||= @post.sourcepointkey if @post.sourcepointkey
          #c.plant ||= params[:plant] if params[:plant]
          c.trans_type = 'publisher'
          #c.parm1 ||= params[:parm1] if params[:parm1]
          c.save            		    
		    
		      create_time_end = Time.now
		      log_it " "
		      @total_create_time += create_time_end - create_time_start
		      @total_posts_created += 1
		      
		      delete_object(object)
		    
		    end
      else
        #log_it "No post created...LPN " + lpn + " already exists"
        @total_records_skipped += 1
        
	      delete_object(object)      
        
      end	
    else # no params
      log_it "Unable to read bucket object key " + object.key.to_s
      @total_records_skipped += 1      
    end #params
    	
  end # bucket.objects.each
  
  log_it "Done processing bucket objects", true

rescue Exception => exc

  log_it "Terminal error on bucket import: #{exc.message}", true

end

def delete_object(object)
  # Delete bucket object, if configured for environment
  begin
    if IMPORT_CONFIG[:delete_buckets]
      begin
        object.delete
        @total_deleted_buckets += 1
      rescue Exception => exc
        @total_errors += 1
        log_it "Unable to delete bucket object key #{object.key.to_s}: #{exc.message}"
      end
    end
  rescue
    # do nothing
  end    
end

def end_stats
  log_it "---------------------------------------------------------------", true
  log_it "End of data import task at " + Time.now.to_s, true
  log_it "Overall execution took     " + (Time.now - @start_time).to_s, true
  log_it "Total search time was      " + @total_find_time.to_s, true
  log_it "Total create time was      " + @total_create_time.to_s, true
  log_it "Total records read:        " + @total_records_read.to_s, true
  log_it "Total records skipped:     " + @total_records_skipped.to_s, true
  log_it "Total posts created:       " + @total_posts_created.to_s, true
  log_it "Total errors:              " + @total_errors.to_s, true
  log_it " ", true
  log_it "Total files processed:     " + @total_files.to_s, true

rescue

  log_it "Terminal error in stat reporting", true

end

def send_import_email

  log_it " ", true
  log_it "---------------------------------------------------------------"
  log_it "Email send started", true
  
  total_recipients = 0 

  IMPORT_CONFIG[:email_recipients].each {|recipient|
    total_recipients += 1
    
    if IMPORT_CONFIG[:import_email_detail]
      if File.exist?(@logfile)
        cmd = "cat #{@logfile}|sendmail \"#{recipient}\""
      else
        cmd = "echo 'No log file exists'|sendmail \"#{recipient}\""
      end    
    else
      if File.exist?(@emailfile)
        cmd = "cat #{@emailfile}|sendmail \"#{recipient}\""
      else
        cmd = "echo 'No log file exists'|sendmail \"#{recipient}\""
      end    
    end

    log_it "Email command: " + cmd.to_s

    if system(cmd)
      log_it "Email sent successfully to #{recipient}"
    else
      log_it "Problem sending email to #{recipient}"
    end
  }

rescue

  log_it "Terminal error sending email", true

end

def log_it(message, email_it=false)

  File.write(@logfile, message.to_s + "\n", mode: 'a')
  
  if email_it
    File.write(@emailfile, message.to_s + "\n", mode: 'a')
  end
  
end

    	<?php $chat_resource_url = 'http://localhost/chat_app_v1/'; ?>
        
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>chat/style.css" />
        <script src="<?php echo base_url(); ?>js/jquery/2.2.4/jquery.min.js"></script>
  		<script>
        var chat_base_url = '<?php echo $chat_resource_url;?>';
        var live_support_url	= '<?php echo $chat_resource_url;?>';
        var browser = '<?php echo $_SERVER['HTTP_USER_AGENT']; ?>';
        var ip_add = '<?php echo $_SERVER['REMOTE_ADDR']; ?>';
        </script>
        <script type="text/javascript">
  		var base_url = '<?php echo base_url(); ?>';
		var BASE_URL = chat_base_url;
  		var Broadcast = {
							POST : "<?php echo POST; ?>",
							BROADCAST_URL : "<?php echo BROADCAST_URL; ?>",
							BROADCAST_PORT : "<?php echo BROADCAST_PORT; ?>",
  						};
  		</script>
  		<link rel="stylesheet" href="<?php echo $chat_resource_url; ?>chat/style.css" />
  	<style>
  	.ats-error {
    box-shadow: 0 0 2px red;
	}
	.ats-chat-container{
	position: fixed;
    width: 0px;
    height: 0px;
    bottom: 0px;
    right: 0px;
    z-index: 2147483647;
	}
	.ats-chat-wrapper{
    bottom: 10;
    position: fixed;
    float: right;
    width: 50px;
	height: 50px;
	right: 20px;
	background: #1F8CEB;
	border-radius: 10%;
	cursor: pointer;
	}
	#ats-message-container{
	background-image: url('<?php echo $chat_resource_url; ?>images/background.jpg');	
	background-size: 328px 400px;
    background-repeat: no-repeat;
    }
	.aissel-chat-open-icon{
	background-image: url('<?php echo $chat_resource_url; ?>images/message.png');
    background-size: 32px 30px;
    background-repeat: no-repeat;
    background-position: center 11px;
    width: 50px;
    height: 50px;
		}
	.chat-icon-remove{
		line-height:2 !important;	
		}	
	img.ats-chat-logo {
	vertical-align:middle;
    width: 35px;
	}
	.move-to-chat{
    padding: 10px;
    font-weight: 700;
	}
	.message1{
	 background: #f4f7f9;
    color: #003366;
    padding: 10px 10px;
    line-height: 25px;
    font-size: 13px;
    border-radius: 5px;
    margin-bottom: 15px;
    min-width: 200px;
    max-width: 86%;
    position: relative;
    float: left;
    margin-left: 5px;

	}
	.message2{
	 background: #dff0d8;
    color: #003366;
    padding: 10px 10px;
    line-height: 25px;
    font-size: 13px;
    border-radius: 5px;
    margin-bottom: 15px;
    min-width: 200px;
    max-width: 86%;
    position: relative;
    float: left;
    margin-left: 5px;

	}
	.ats-block-footer{
		background:none !important;
		padding:0 !important;
		}
	.ats-previous-chat-list {
    background: #f4f4f4;
    padding: 10px;
    margin-bottom: 5px;
}	
a#ats-send-message {
    height: 48px;
    background: #f5f5f5;
    font-weight: 700;
    /* font-size: 12px; */
    line-height: 3;
}
img.ats-chat-send {
    width: 29px;
    margin: 4px 0px;
    opacity: 0.6;
}
a.ats-previous-chat{
	text-decoration:none;
	}
.conmsg{
	margin-right: 5px;
    margin-top: 4px;
    float: left;
	}
.chat-new-message-count {
    display: inline-block;
    min-width: 10px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    background-color: #DA4F49;
    color: #ecf0f1;
    border-radius: 10px;
}	
	</style>
</head>
<body>
	<div class="container"><a href="<?php echo base_url(); ?>user/logout">logout</a></div>
    <div class="container"><a href="javascript:void(0)" class="activechat">Show</a></div>
    
    <!--chat inital script starts from below line. includes script-->
    	<div class="ats-container ats-chat">
            <!--chat window start--> 
            <div id="ats-chat-window" class="ats-chat-window" style="margin-left:10px; display:none;">
                <!--chat Initialization start-->
                <div class="ats-block ats-block-default ats-chat-init">
                    <div class="ats-block-heading ats-top-bar ats-init-top ats-block-collapsed">
                        <div class="ats-chat-title">
                            <h3 class="ats-block-title">
                            	<span class="toggle">
                                <img src="<?php echo $chat_resource_url; ?>images/user_logo.png" class="ats-chat-logo"/>
                                </span> Aissel Support
                            </h3>
                        </div>
                        <div class="ats-chat-button" style="text-align: right;">
                            <a href="javascript:void(0)"><span class="ats-icon-close">X</span></a>
                        </div>
                    </div>
                    <div class="ats-block-body ats-message-block ats-block-collapsed">
                        <div id="ats-chat-init-viewport"></div>
                    </div>
                    <div class="ats-block-footer ats-block-collapsed">
                        <div id="ats-chat-input" class="ats-input move-to-chat" style="text-align:center"></div>
                    </div>
                </div>
                <!--chat Initialization end-->
                <!--chat box start-->
                <div class="ats-block ats-block-default ats-chat-box"  style="display:none;">
                	<div class="ats-block-heading ats-top-bar ats-box-top">
                   		<div class="ats-chat-title">
                			<h3 class="ats-block-title">
                            <span class="toggle">
                            <img src="<?php echo $chat_resource_url; ?>images/user_logo.png" class="ats-chat-logo"/></span> Aissel Support</h3>
                        <span id="ats-new-message" class="ats-new-message chat-new-message-count" style="display:none;"></span></div>
                        <div class="ats-chat-button" style="text-align: right;">
                            <a href="javascript:void(0)"><span class="ats-icon-close">X</span></a>
                        </div>
                    </div>
                    <div id="ats-message-container" class="ats-block-body ats-message-block" style="display:block !important;">
                         <div class="ats-row">
                                <div class="ats-col-full">
                                	<p class="text-center-wc ats-new-chat-message text-center"></p>
                                    <p id="ats-new-chat-date" class="ats-chat-init-date text-center"></p>
                                </div>
                         </div>
                        <input id="ats-chat-token" class="ats-chat-token" type="hidden">
                        <input id="ats-chat-id" class="ats-chat-id" type="hidden">   
                        <div id="ats-chat-box-viewport"></div>
              		</div>
                    <div class="ats-block-footer" style="display:block !important;">
                        <div id="ats-chat-input" class="ats-input ats-chat-input">
                           <input id="ats-chat-message" type="text" class="form-control ats-chat-input ats-chat-message" placeholder="Send a message...">
                            <a class="ats-button ats-button-default btn-sm" id="ats-send-message"><img src="<?php echo $chat_resource_url; ?>images/send.png" class="ats-chat-send"/></a>   
                           </div>
                    </div>
                </div>
               <!--chat box end-->
            </div>
		<script>
			var client_name = "<?php echo $this->session->userdata('client_name'); ?>";
			var user_name = "<?php echo $this->session->userdata('user_name'); ?>";
			var user_id = "<?php echo $this->session->userdata('user_id'); ?>";
			var user_email = "<?php echo $this->session->userdata('email'); ?>";
			var device_info = "<?php echo $_SERVER['HTTP_USER_AGENT']; ?>";
        </script>
        <script src="<?php echo $chat_resource_url; ?>chat/achat.js"></script>
		<script src="<?php echo $chat_resource_url; ?>node_modules/socket.io/node_modules/socket.io-client/socket.io.js"></script>
        
		<script>
        var socket;
		var channel;
		var message;
		var alignClass;
		var user;
		var chatCloseStatus;
		$(document).ready(function(){		
		//socket = io.connect( 'https://chat.aisselkolm.com:3000' );	
		socket = io.connect( 'http://'+window.location.hostname+':3000' );				   
		socket.on( 'chat_message', function( data ) {	
			if(data.utype == 'user'){
				alignClass	= 'left';
				type	= 'message1';
				user	= 'Me';
			}else{
				alignClass	= 'right';
				type	= 'message2';
				user	= data.user;
				 }
			
			message	= '<div class="ats-row">';
			message += '<div class="ats-col-full">';
			message += '<div class="ats-message" style="float:'+alignClass+';margin-top:5px;">';
			message += '<img class="ats-message-object ats-img-circle" src="<?php echo $chat_resource_url; ?>images/'+data.uimage+'" alt="" width="35px;">';
			message += '</div>';
			message += '<div class="ats-message-body '+type+'">';
			message += '<h5 class="ats-message-title">'+user+' <time>('+data.time+')</time></h5>';
			message += '<p>'+data.umessage+'</p>';
			message += '</div>';
			message += '</div>';
			message += '</div>';
				  $('.ats-new-chat-message').hide();	
				  $("#ats-chat-box-viewport").append(message);
			  	  var objDiv = document.getElementById("ats-message-container");
				  objDiv.scrollTop = objDiv.scrollHeight;
			  });

		//New Chat Count  
		socket.on( 'user_new_message_count', function( data ) {
			$('.ats-chat-room-'+data.chatId).show();	
			$('.ats-chat-room-'+data.chatId).html(data.count);	
		});
		
		socket.on( 'close_chat_conversation_user', function( data ) {	
			if(data.set_status == 'chatClose'){
					$(".ats-block-footer").hide();
					$('#ats-new-message').val('');
					$('#ats-new-message').hide();
					$('div#ats-chat-box-viewport').append('<div class="ats-row ats-chat-close">This conversation has been closed. <span id="new-chat-link"><a href="javascript:void(0);" class="new-conversation new-con">Start New</a></span>');
				}
		});

		socket.on( 'reopen_chat_conversation_user', function( data ) {	
			if(data.set_status == 'chatReOpen'){
					$('.ats-chat-close').hide();
					$(".ats-block-footer").show();
				}
		});
		});
		function createChatRoom(){
			//var user	= user_id;
			var user	= $("#ats-chat-window").attr("chat-user-id");
			var unique_id	= $(".ats-chat-token").val();
			var support_client	= "-aissel";
			channel = user+'-'+unique_id+support_client;
			socket.emit('join', {email: channel});
			return channel;
			}
		$('input#ats-chat-message').keypress(function(){
				$('input#ats-chat-message').removeClass('ats-error');
			 });
		$('a#ats-send-message').click(function(){ 
            var chat_message = $('input#ats-chat-message').val();
			$('#ats-new-message').val('');
			$('#ats-new-message').hide();
            if(chat_message!=''){    
            var chat_id = $('input#ats-chat-id').val();
            var client_name = '1';
            $.ajax({
                type:"POST",
                url:chat_base_url+"user/insert_message",
                /* contentType: "application/x-www-form-urlencoded",
                crossDomain:true, */
                data:{
                    'message': chat_message,
                    'client' : client_name,
                    //'user' : user_id,
                    'user' : $("#ats-chat-window").attr("chat-user-id"),
                    'chat_token' : chat_id,
					'channel' : channel
                },
                success:function(data){
                	var value = JSON.parse(data);
				   	if(value.status === 'ok'){
                   		 //chat_init_id = value['content'].chat_init_id;
                         //$('.userdisplaydate').text(value['content'].initiated_on);
                         //$('div#chat_viewport').html(data.content);
                         //$("div.msg_container_base").animate({ scrollTop: $('div.msg_container_base').prop("scrollHeight")}, 1000);
						 $('input#ats-chat-message').val('');
                     	 socket.emit('chat_message', { 
										channel: channel,
										utype: value['content'].type,
										user: value['content'].user,
										uimage: value['content'].uimage,
										umessage: value['content'].umessage,
										time: value['content'].time,
										class: value['content'].class
										});	
					 	socket.emit('new_chat_count', {
										channel: channel,
										chatId: value['content'].chat_init,
										chatType: value.chat_type,
										count: value['content'].count										
										});
					 }
                },
				error: function(xhr, status, error) {
              				//alert(error);
            	},
             });
            }else{
            	$('input#ats-chat-message').addClass('ats-error');
                }
            });	
		$(document).on('click', '.ats-previous-chat', function(e){
			e.preventDefault();
        	var chatInitId = $(this).attr('cid');
        	$.ajax({
        		type:"POST",
                url:chat_base_url+"user/get_chat_messages",
                /* contentType: "application/x-www-form-urlencoded",
                crossDomain:true, */
                data:{
                    'chatinitid': chatInitId,
                },
                success:function(value){
                	var data = JSON.parse(value);
                    if(data.status === 'ok'){
                	   $(".ats-chat-init").hide();
		                $(".ats-chat-token").val(data.unique_chat_id);
						$(".ats-chat-id").val(data.chat_init_id);
						$(".chat-new-message-count").addClass("ats-chat-room-"+data.chat_init_id);
						createChatRoom();
						$('.ats-chat-init-date').text("Initiated on "+data.chat_init_date);
                        $('div#ats-chat-box-viewport').html(data.content);
						if(data.chat_status === 'close'){
							$(".ats-block-footer").hide();
							$('div#ats-chat-box-viewport').append('<div class="ats-row ats-chat-close">This conversation has been closed. <span id="new-chat-link"><a href="javascript:void(0);" class="new-conversation new-con">Start New</a></span>');
							chatCloseStatus = 1;
							//alert(chatCloseStatus);
							}
						$(".ats-chat-box").show(); 
						var objDiv = document.getElementById("ats-message-container");
				  		objDiv.scrollTop = objDiv.scrollHeight;
						 //$('input#chat_message').val('');
                     }else{
                         //there was an error while fecthing data 
                     }
                }	
            	});
            });
		$(document).on('click', '.new-conversation', function(e){
				e.preventDefault();
				var user_id = $("#ats-chat-window").attr("chat-user-id");
				var dataString = "type='new_conversation'&user_name="+user_name+"&user_email="+user_email+"&user_id="+user_id+"&app_url="+base_url+"&browser="+browser+"&ip_address="+ip_add;
				$.ajax({
					type:"POST",
	                url:chat_base_url+"user/new_chat_initial",
	                data:dataString,
	                beforeSend: function() {
	                    $('#new-chat-link').html('Processing...');
	                },
	                success:function(data){
	    				$("#ats-chat-box-viewport").html('');
	    				$(".ats-block-footer").show();
		                var value = JSON.parse(data);
		                var conversationId	= value.unique_chat_id;
		                $(".ats-chat-init").hide();
		                $(".ats-chat-token").val(conversationId);
						$(".ats-chat-id").val(value.id);
						$(".chat-new-message-count").addClass("ats-chat-room-"+value.id);
						createChatRoom();
						$(".ats-chat-box").show();
						$('.ats-new-chat-message').text("Welcome to Aissel Support. Text your query, one of our Executive will help you out !");
						$('.ats-chat-init-date').text("Initiated on "+value.initiated_on);
		                socket.emit('new_chat_initiate', { 
							channel: channel,
							chatId: value.id,
							userName: value.user_name
							});	
		                socket.emit('created_new_conversation', { 
							channel: channel,
							cnc_count: value.new_conversation_count
							});	
		                },
		            error:function(){
			            }    
					});
			});		
        </script>
        <!-- chat script ends on above below line --> 
</body>
</html>
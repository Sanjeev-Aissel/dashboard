$( "#ats-chat-window" ).hide();
//ready function started
$(document).ready(function(){
    
	var support_url = live_support_url;
    var chat_init_id = '';

	if(sessionStorage['liveSupportVisibility']==1){
         $( "#ats-chat-window" ).show();
    }else{
		 $( "#ats-chat-window" ).hide();
    }
           
	//get cookies
    $('.activechat').click(function(){
         $( "#ats-chat-window" ).show();
         sessionStorage['liveSupportVisibility']  = 1;
     }); 
     
	 $(document).on('click', '.ats-init-top', function (e) {
          var $this = $(this);
               if(!$this.hasClass('ats-block-collapsed')){
                       $this.parents('.ats-block').find('.ats-block-body').slideUp();
                       $this.addClass('ats-block-collapsed');
                       $this.parents('.ats-block').find('.ats-block-footer').slideUp();
                       $this.addClass('ats-block-collapsed');
                   } else {
					 	$('#ats-chat-init-viewport').html('Loading...');
                	   // ajax call to fetch details
                       $.post(support_url + "user/check_chat", {client : client_name, user : user_name}, function(data){
                       if(data.rstatus === true){
                            if(data['progress'].status === 'available'){
                            		$('.move-to-chat').html('<a href="#" class="ats-previous-chat" cid="'+data['progress'].chat_init_id+'">Start on '+data['progress'].chat_init_date+'</a>');
                            		$('.ats-previous-chat').click();
                            }else{
                        			$('.move-to-chat').html('<span id="new-chat-link"><a class="new-conversation" href="">New Conversation</a></span>');
                        		}
                       		}	
	                       if(data['closed'].cstatus === 'ok'){
	                       	var list = data['closed'].clisting;
	                       	var listing = '';
	                           	for (i = 0; i < list.length; i++) { 
	                           		/*console.log(list[i].initiated_on);*/
	                           		listing += '<div class="ats-previous-chat-list"><img src="'+BASE_URL+'images/message.png" class="conmsg"/><a href="#" class="ats-previous-chat" cid="'+list[i].id+'">';
	                           		listing += '<span><b>Last Conversation </b>';//+list[i].initiated_on;    
										listing += '</span><br/><i>Status : Closed</i>';
	                           		listing += '</a></div>';
	                           	}
	                           	$('#ats-chat-init-viewport').html(listing);
	                           	/*console.log(listing);*/
	                       	}else{
	                       		$('#ats-chat-init-viewport').html('<p class="text-center text-muted small">'+data['closed'].message+'</p>');
	                       	}
	                       	$('#ats-chat-window').attr('chat-user-id', data.suid);
                        }, "json");
                       $this.parents('.ats-block').find('.ats-block-body').slideDown();
                       $this.removeClass('ats-block-collapsed');
                       $this.parents('.ats-block').find('.ats-block-footer').slideDown();
                       $this.removeClass('ats-block-collapsed');
                   }
               });
	 
       $(document).on('click', '.ats-box-top', function (e) {
          var $this = $(this);
               if(!$this.hasClass('ats-block-collapsed')){
                       $this.parents('.ats-block').find('.ats-block-body').slideUp();
                       $this.addClass('ats-block-collapsed');
					   if(chatCloseStatus != 1){
                       $this.parents('.ats-block').find('.ats-block-footer').slideUp();
                       $this.addClass('ats-block-collapsed');
					   }else{
						$this.parents('.ats-block').find('.ats-block-footer').hide();   
						   }
				   } else {
                       $this.parents('.ats-block').find('.ats-block-body').slideDown();
                       $this.removeClass('ats-block-collapsed');
					   if(chatCloseStatus != 1){
                       $this.parents('.ats-block').find('.ats-block-footer').slideDown();
                       $this.removeClass('ats-block-collapsed');
					   }else{
						 $this.parents('.ats-block').find('.ats-block-footer').hide();  
						   }
				   }
               });
        
		$(document).on('focus', '.ats-block-footer input.ats-chat-input', function (e) {
                   var $this = $(this);
                   if ($('#minim-chat-box-window').hasClass('ats-block-collapsed')) {
                       $this.parents('.ats-block').find('.ats-block-body').slideDown();
                       $('#minim-chat-box-window').removeClass('ats-block-collapsed');
                       /*$('#minim_chat_window').removeClass('chat-icon-plus').addClass('chat-icon-minus');*/
                   }
        });
               
        $('input#ats-chat-message').keypress(function(e){
                    if(e.which === 13){
                        $('a#ats-send-message').click();
                        return false;
                    }
        }); 
                   
        $(document).on('click', '.ats-icon-close', function (e) {
                    //$(this).parent().parent().parent().parent().remove();
                    $( "#ats-chat-window" ).hide();
                    sessionStorage['liveSupportVisibility'] = 0;
        });
//ready function end		
    });
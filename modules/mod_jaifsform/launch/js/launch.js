jQuery(function($){
	
	
    // Intro text for mailchimp
	var error_message = document.createElement('span');
	error_message.style.color = '#FF0000';
	error_message.id = 'mailchimp_error_message';
	
	

   $('.txt_email input').val('Your email ...');
   $('.txt_name input').val('Your name ...');
   
   $(".txt_email input").focus(function(){
     if(this.value == 'Your email ...') {
       this.value='';
     }
   }).blur(function(){
     if(this.value == '') {
       this.value='Your email ...';
     }
   })	  
   
   $(".txt_name input").focus(function(){
     if(this.value == 'Your name ...') {
       this.value='';
     }
   }).blur(function(){
     if(this.value == '') {
       this.value='Your name ...';
     }
   });
	jQuery("#frm_sub").submit(function() {
      var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
      var validated = emailPattern.test(jQuery('.txt_email input').val());
      var messages = [];
      if(!validated) {
      //  messages.push('Email is not valid');
        $(".txt_email input").addClass('red-border');
      }
      else {
        $(".txt_email input").removeClass('red-border');
      }
      if($('.txt_name input').val() == 'Your name ...') {
        $('.txt_name input').addClass('red-border');
        validated = false;
        //messages.push('Name is required');
      }
      else {
        $('.txt_name input').removeClass('red-border');
      }
      
      if(!validated) {
        jQuery('#mailchimp_error_message').html('<br/>' + messages.join('<br/>'));
       return false;     
	 }
      else {
        jQuery('#mailchimp_error_message').text('');
      }
        $('#lb_message').html(' <span class="loading">Sending</span>');
		$.post(baseform,$('#frm_sub').serialize(),function(data){
		   if(data =='Done'){
		     $('#lb_message').html('Thanks for your interest in testing our themes. You will receive an email with an invitation code to access our pages.').next().hide() ;
			  $('#system-messages-wrapper').hide();
			 }
			else{
			    $('#lb_message').html('<div class="error">Error! Your request has not been sent</div>')
			}
			
		})
		
	  return false;
    });		
	// hide input form
	if($('.success').length>0){
	   $('#system-messages-wrapper').hide();
	   $('.region-panel-fifth-2 form').html('<p>Thank you, you have been successfully subscribed.</p>');
	  
	  
	}
	
	$('.cms_invite').toggle(function(){
	 $('#lb_message').html('Be the first one to preview it.').next().show() ;
	 
   $('.txt_email input').val('Your email ...');
   $('.txt_name input').val('Your name ...');
   
	    $('.region-panel-fifth-2').quickFlipper();$(this).text('Go Back'); 
		
	  return false;},function(){$('.region-panel-fifth-2').quickFlipper();$(this).text('Have invitation code ?'); return false;})

	
	
});
$(window).load(function(){
$('.region-panel-fifth-2').quickFlip();
})
<div class="ifsform-intro">
  <?php echo $ifsintro ?>
</div>
<div class="ifsform-success hide">
  <?php echo $ifssuccess ?>
</div>
  
<form class="form-t3-signup" id="frm-signup">
    <div class="input-prepend input-append">
      <span class="add-on"><i class="icon-envelope"></i></span>
      <input class="input-large" id="prependedInput" size="16" type="email" placeholder="Your email">
      <button class="btn btn-large btn-primary" type="submit"><?php echo $ifsbtntitle ?></button>
    </div>
</form>

<script type="text/javascript">
    var link = location.href;
    if(link.indexOf('#')>-1){
            link = link.substr(0, link.indexOf('#'));
    }
    if(link.indexOf('?')>-1) link += '&';
    else link += '?';
    link += 'jaifs_ajax=1'; 
    
    jQuery(function(){
        //reset form 
        jQuery('#frm-signup .btn').removeAttr('disabled')
        jQuery('#frm-signup').submit(function(){
         
           var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
           var validated = emailPattern.test(jQuery('#prependedInput').val());
             if(!validated) {

              //  messages.push('Email is not valid');
               jQuery("#prependedInput").addClass('red-border');
                return false; 
              }
            var email = jQuery('#prependedInput').val();
            var fname = email.split('@')[0];
            jQuery('#frm-signup .btn').addClass('disabled').attr("disabled",'disabled');
           jQuery.post(link,{inf_field_FirstName:fname,inf_field_Email:email},function(){
              jQuery('#frm-signup').hide();
              jQuery('.ifsform-intro').hide();
              jQuery('.ifsform-success').fadeIn();
           })
          return false;
        })

    });
</script>

var $ja = jQuery.noConflict();

!function($){
	$(document).ready(function(){
		$(document.body).on('click', '[data-toggle="dropdown"]' ,function(){
			if(!$(this).parent().hasClass('open') && this.href && this.href != '#'){
				window.location.href = this.href;
			}
		});

		$('#downloads').appendTo(document.body);
	});
}(window.jQuery)
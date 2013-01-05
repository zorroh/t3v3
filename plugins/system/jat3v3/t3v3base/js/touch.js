!function($){		
	var isTouch = 'ontouchstart' in window && !(/hp-tablet/gi).test(navigator.appVersion);
	
	if(isTouch){
		$.fn.jatouchMenu = function(){
			return this.each(function(){	
				var	jitems = $(this).find('li.parent'),
					onTouch = function(e){
						$(document.body).addClass('hoverable');
						e.stopPropagation();
						
						var val = !$(this).data('noclick');
						// reset all
						jitems.data('noclick', 0);
						$(this).data('noclick', val);
						
						if($(this).data('noclick')){
							$(this).addClass('open').parentsUntil('.nav').filter('li.parent').addClass('open');
						}
						
						this.focus();
					},
					onClick = function(e){
						e.stopPropagation();
						
						if($(this).data('noclick')){
							e.preventDefault();
							jitems.removeClass('open');
							$(this).addClass('open').parentsUntil('.nav').filter('li.parent').addClass('open');
						} else {
							var href = $(this).children('a').attr('href');
							if(href){
								window.location.href = href;
							}
						}
					};
				
				jitems.on('touchstart', onTouch).on('click', onClick).data('noclick', 0);
				
				$(document).on('touchstart', function(){
					jitems.data('noclick', 0);
					$(document.body).removeClass('hoverable');
				});
			});
		};
		
		$(document).ready(function(){
			$('ul.nav').has('.dropdown-menu').jatouchMenu();
		});
	}
	
	//$(window).resize(function(){
	//	var jbtncollapse = $('#ja-mainnav').find('.navbar .btn-navbar');
	//	$(document.body)[jbtncollapse.length && jbtncollapse.css('display') != 'none' ? 'removeClass' : 'addClass']('hoverable');
	//});
	
}(window.$ja || window.jQuery);
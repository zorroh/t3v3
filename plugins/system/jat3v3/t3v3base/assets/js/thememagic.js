
!function($){
	var T3V3Theme = window.T3V3Theme = window.T3V3Theme || {};

	$.extend(T3V3Theme, {
		initrequest: function(){
			if(window.parent != window){
				window.parent.postMessage('rqless', [window.location.protocol, '//', window.location.hostname, window.location.port].join(''));
			}
		},
		listener: function(e){
			if(e.origin == [window.location.protocol, '//', window.location.hostname, window.location.port].join('')){
				var json = $.parseJSON(e.data);
				switch (json.cmd){
					case 'less:refresh':
						T3V3Theme.vars = json.vars;
						T3V3Theme.others = json.others;
						T3V3Theme.theme = json.theme;
						
						if(less){
							less.refresh(true);
						}
					break;

					default:
					break;
				}
			}
		},
		handleLink: function(){
			var links = document.links,
				forms = document.forms,
				origin = [window.location.protocol, '//', window.location.hostname, window.location.port].join(''),
				iter, i, il;

			for(i = 0, il = links.length; i < il; i++) {
				iter = links[i];

				if(iter.href && iter.hostname == window.location.hostname && iter.href.indexOf('#') == -1){
					iter.href = iter.href + (iter.href.lastIndexOf('?') != -1 ? '&' : '?') + (iter.href.lastIndexOf('themer=') == -1 ? 'themer=Y' : ''); 
				}
			}

			
			for(i = 0, il = forms.length; i < il; i++) {
				iter = forms[i];

				if(iter.action.indexOf(origin) == 0){
					iter.action = iter.action + (iter.action.lastIndexOf('?') != -1 ? '&' : '?') + (iter.action.lastIndexOf('themer=') == -1 ? 'themer=Y' : ''); 
				}
			}
		}
	});

	if (window.addEventListener){
		window.addEventListener('message', T3V3Theme.listener, false)
	} else {
		window.attachEvent('onmessage', T3V3Theme.listener)
	}

	$(document).ready(function(){
		T3V3Theme.handleLink();
		T3V3Theme.initrequest();
	})
	
}(window.$ja || window.jQuery);

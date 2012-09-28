var T3V3Theme = window.T3V3Theme || {};

!function ($) {



	$.extend(T3V3Theme, {

		placeholder: 'placeholder' in document.createElement('input'),

		//cache the original link
		initialize: function(){
			this.initCPanel();
			this.initCacheSource();
			this.initThemeAction();
			this.onJITCompile();
			this.initModalDialog();
			this.initRadioGroup();
			//this.initChosen();
		},
		
		initCacheSource: function(){
			T3V3Theme.links = [];

			$('link[rel="stylesheet/less"]').each(function(){
				$(this).data('original', this.href.split('?')[0]);
			});

			$.each(T3V3Theme.data, function(key){
				var extended = $.extend({}, T3V3Theme.data.default, this);
				T3V3Theme.data[key] = extended;
			});
		},

		initCPanel: function(){
			
			$('#thememagic .themer-minimize').on('click', function(){
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					$('#thememagic').css('left', 0);
					$('#preview').css('left', $('#thememagic').outerWidth(true));
				} else {
					$(this).addClass('active');
					$('#thememagic').css('left', - $('#thememagic').outerWidth(true));
					$('#preview').css('left', 0);
				}
				
				return false;
			});
		},

		initRadioGroup: function(){
			//clone from J3.0 a2
			$('#thememagic .radio.btn-group label').addClass('btn')
			$('#thememagic').on('click', '.btn-group label', function(){
				var label = $(this),
					input = $('#' + label.attr('for'));

				if (!input.prop('checked')){
					label.closest('.btn-group')
						.find('label')
						.removeClass('active btn-success btn-danger btn-primary');

					label.addClass('active ' + (input.val() == '' ? 'btn-primary' : (input.val() == 0 ? 'btn-danger' : 'btn-success')));
					
					input.prop('checked', true).trigger('change.less');
				}
			});
			$('#thememagic .radio.btn-group input:checked').each(function(){
				$('label[for=' + $(this).attr('id') + ']').addClass('active ' + ($(this).val() == '' ? 'btn-primary' : ($(this).val() == 0 ? 'btn-danger' : 'btn-success')));
			});

			$('#thememagic').on('change.depend', 'input[type=radio]', function(){
				if(this.checked){
					$(this)
						.closest('.btn-group')
						.find('label').removeClass('active btn-primary')
						.filter('[for="' + this.id + '"]').addClass('active ' + ($(this).val() == '' ? 'btn-primary' : ($(this).val() == 0 ? 'btn-danger' : 'btn-success')));
				}
			});
		},
		
		initChosen: function(){
			$('#ja-variable-form').find('select').chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});
		},
		
		initThemeAction: function(){
			this.jel = document.getElementById('ja-theme-list');
			
			//change theme
			$('#ja-theme-list').on('change', function(){
				
				var val = this.value;

				if(T3V3Theme.admin && T3V3Theme.changed){

					if(T3V3Theme.active == 'default' || T3V3Theme.active == -1){
						T3V3Theme.confirm(T3V3Theme.langs.saveChange, function(option){
							if(option){
								T3V3Theme.nochange = 1;
								T3V3Theme.saveThemeAs(function(){
									T3V3Theme.changeTheme(val);
								});
							} else {
								T3V3Theme.changeTheme(val);
							}
						});
					}else {
						T3V3Theme.confirm(T3V3Theme.langs.saveChange.replace('%THEME%', T3V3Theme.theme), function(option){
							if(option){
								T3V3Theme.saveTheme();

								$('#thememagic-dlg').modal('hide');
							}

							T3V3Theme.changeTheme(val);
						});
					}
				} else {
					T3V3Theme.changeTheme(val);
				}
								
				return false;
			});
			
			if(T3V3Theme.admin){
				//save theme
				$('#ja-theme-submit').on('click', function(){
					if(!$(this).hasClass('disabled')){
						T3V3Theme.saveTheme();
					}
					return false;
				});
				//saveas theme
				$('#ja-theme-saveas').on('click', function(){
					if(!$(this).hasClass('disabled')){
						T3V3Theme.saveThemeAs();
					}
					return false;
				});
				
				//delete theme
				$('#ja-theme-delete').on('click', function(){
					if(!$(this).hasClass('disabled')){
						T3V3Theme.deleteTheme();
					}
					return false;
				});
			}

			if(T3V3Theme.active != -1){
				T3V3Theme.fillData();
			}

			$('#ja-theme-submit, #ja-theme-clone, #ja-theme-delete')[($('#ja-theme-list').val() == 'default' ? 'addClass' : 'removeClass')]('disabled');
		},

		initModalDialog: function(){
			$('#thememagic-dlg').on('click', '.modal-footer a', function(){
				T3V3Theme.addtime = 500; //add time for close popup

				if($.isFunction(T3V3Theme.modalCallback)){
					T3V3Theme.modalCallback($(this).hasClass('btn-primary'));
					return false;
				} else if($(this).hasClass('btn-primary')){
					$('#thememagic-dlg').modal('hide');
				}
			});

			$('#prompt-form').on('submit', function(){
				$('#thememagic-dlg .modal-footer a.btn-primary').trigger('click');

				return false;
			});
		},
		
		onJITCompile: function(){
			$('#thememagic').on('keypress.less', 'input', T3V3Theme.onKeypress)
							.on('change.less', 'select, input', T3V3Theme.onKeypress);
		},
		
		offJITCompile: function(){
			$('#thememagic').off('keypress.less', 'input', T3V3Theme.onKeypress)
							.off('change.less', 'select, input', T3V3Theme.onKeypress);
		},
		
		applyLess: function(force){
			
			var jprogress = $('#recss-progress');
			if(jprogress.hasClass('invisible')){
				jprogress.removeClass('invisible').addClass('in').find('.bar').width(0);
			}

			var nvars = T3V3Theme.rebuildData(true),
				jsonstr = JSON.stringify(nvars);

			if(!force && T3V3Theme.jsonstr === jsonstr){
				setTimeout(function(){
					jprogress.addClass('invisible').find('.bar').width(0);
				}, 1000);
			
				return false;
			}

			T3V3Theme.variables = nvars;
			T3V3Theme.jsonstr = jsonstr;

			setTimeout(function(){
				jprogress.find('.bar').width('10%');
				setTimeout(function(){
					jprogress.find('.bar').width('100%');

					setTimeout(function(){
						jprogress.addClass('invisible').find('.bar').width(0);
					}, 1000);
				}, 1000);
				
				setTimeout(function(){
					$('#ifr-preview')[0].contentWindow.postMessage(JSON.stringify({
							cmd: 'less:refresh',
							vars: T3V3Theme.variables,
							theme: T3V3Theme.active,
							others: T3V3Theme.themes[T3V3Theme.active]
						}), [window.location.protocol, '//', window.location.hostname, window.location.port].join(''));
				}, 10);
				
			}, 100 + T3V3Theme.addtime); //wait for popup hide or other animation complete

			//reset
			T3V3Theme.addtime = 0;

			return false;
		},
		
		changeTheme: function(theme, pass){
			if(pass && !$('#recss-progress').hasClass('invisible')){
				setTimeout(function(){
					$('#recss-progress').addClass('invisible').find('.bar').width(0);
				}, 1000);
			}

			if($.trim(theme) == ''){
				return false;
			}
			
			//enable or disable control buttons
			$('#ja-theme-submit, #ja-theme-clone, #ja-theme-delete')[(theme == 'default' ? 'addClass' : 'removeClass')]('disabled');
			
			T3V3Theme.active = theme;	//store the current theme
			T3V3Theme.changed = false;

			if(!pass){
				this.offJITCompile();		//turn off JIT Compile for save cpu cycles
				this.fillData();			//fill the data
				this.onJITCompile();		//turn on JIT Compile again
				this.applyLess();			//refresh   	
			}
			
            return true;
		},
		
		serializeArray: function(){
			var els = [],
				allelms = document.adminForm.elements,
				pname1 = 'jaform\\[jat3v3\\]\\[.*\\]',
				pname2 = 'jaform\\[jat3v3\\]\\[.*\\]\\[\\]';
				
			for (var i = 0, il = allelms.length; i < il; i++){
				var el = allelms[i];
				
				if (el.name && (el.name.match(pname1) || el.name.match(pname2))){
					els.push(el);
				}
			}
			
			return els;
		},

		fillData: function (){
			
			var els = this.serializeArray(),
				data = T3V3Theme.data[T3V3Theme.active];
				
			if(els.length == 0 || !data){
				return;
			}
			
			$.each(els, function(){
				var name = T3V3Theme.getName(this),
					values = (data[name] != undefined) ? data[name] : '';
				
				T3V3Theme.setValues(this, $.makeArray(values));
			});
			
			//reset form state when new data is filled
			$(document.adminForm).find('.changed').removeClass('changed');

			if(typeof JADepend != 'undefined'){
				JADepend.update();
			}
		},
		
		valuesFrom: function(els){
			var vals = [];
			
			$(els).each(function(){
				var type = this.type,
					val = $.makeArray(((type == 'radio' || type == 'checkbox') && !this.checked) ? null : $(this).val());

				if(type == 'text' && !val[0]){
					val[0] = $(this).attr('placeholder');
				}

				for (var i = 0, l = val.length; i < l; i++){
					if($.inArray(val[i], vals) == -1){
						vals.push(val[i]);
					}
				}
			});
			
			return vals;
		},
		
		setValues: function(el, vals){
			var jel = $(el);
			
			if(jel.prop('tagName').toUpperCase() == 'SELECT'){
				jel.val(vals);
				
				if($.makeArray(jel.val())[0] != vals[0]){
					jel.val('-1');
				}
			}else {
				if(jel.prop('type') == 'checkbox' || jel.prop('type') == 'radio'){
					jel.prop('checked', $.inArray(el.value, vals) != -1).trigger('change.depend');

				} else {
					jel.attr('placeholder', vals[0]);
					jel.val(vals[0]);

					if(T3V3Theme.placeholder && T3V3Theme.data.default[T3V3Theme.getName(el)] == vals[0]){
						jel.val('');
					}
				}
			}
		},
		
		rebuildData: function(optimize){
			var els = this.serializeArray(),
				json = {};
				
			$.each(els, function(){
				var values = T3V3Theme.valuesFrom(this);
				if(values.length && values[0] != '' && (!optimize || (optimize && !this._disabled))){
					json[T3V3Theme.getName(this)] = this.name.substr(-2) == '[]' ? values : values[0];
				}
			});

			for(var k in json){
				if(json.hasOwnProperty(k) && k.match(/_custom/)){
					json[k.replace('_custom', '')] = json[k];	
				}
			}
			
			return json;
		},
		
		getName: function(el){
			var matches = el.name.match('jaform\\[jat3v3\\]\\[([^\\]]*)\\]');
			if (matches){
				return matches[1];
			}
			
			return '';
		},
		
		deleteTheme: function(){

			T3V3Theme.confirm(T3V3Theme.langs.delTheme, function(option){
				if(option){
					T3V3Theme.submitForm({
						t3task: 'delete',
						theme: T3V3Theme.active
					});

					$('#thememagic-dlg').modal('hide');
				}
			});
		},
		
		cloneTheme: function(){
			T3V3Theme.prompt(T3V3Theme.langs.addTheme, function(option){
				if(option){
					var nname = $('#theme-name').val();
					if(nname){
						nname = nname.replace(/[^0-9a-zA-Z_-]/g, '').replace(/ /, '').toLowerCase();
						if(nname == ''){
							T3V3Theme.alert('warning', T3V3Theme.langs.correctName);
							return this.cloneTheme();
						}
						
						T3V3Theme.data[nname] = T3V3Theme.data[T3V3Theme.active];
						T3V3Theme.themes[nname] = $.extend({}, T3V3Theme.themes[T3V3Theme.active]);
						
						this.submitForm({
							t3task: 'duplicate',
							theme: nname,
							from: T3V3Theme.active
						});
					}

					$('#thememagic-dlg').modal('hide');
				}
				
			});
			
            return true;
		},
		
		saveTheme: function(){
			T3V3Theme.data[T3V3Theme.active] = this.rebuildData();
			this.submitForm({
				t3task: 'save',
				theme: T3V3Theme.active
			}, T3V3Theme.data[T3V3Theme.active])		
		},
		
		saveThemeAs: function(callback){
			T3V3Theme.prompt(T3V3Theme.langs.addTheme, function(option){
				if(option){
					var nname = $('#theme-name').val();
					if(nname){
						nname = nname.replace(/[^0-9a-zA-Z_-]/g, '').replace(/ /, '').toLowerCase();
						if(nname == ''){
							T3V3Theme.alert('warning', T3V3Theme.langs.correctName);
							return this.saveThemeAs(callback);
						} else if(T3V3Theme[nname]){
							T3V3Theme.alert('warning', T3V3Theme.langs.themeExist);
							return this.saveThemeAs(callback);
						}
						
						T3V3Theme.data[nname] = this.rebuildData();
						T3V3Theme.themes[nname] = $.extend({}, T3V3Theme.themes[T3V3Theme.active]);

						this.submitForm({
							t3task: 'save',
							theme: nname,
							from: T3V3Theme.active
						}, T3V3Theme.data[nname]);
					}

					$('#thememagic-dlg').modal('hide');
				}

				if($.isFunction(callback)){
					callback();
				}

				return true;
			});

			return true;
		},
		
		submitForm: function(params, data){
			if(T3V3Theme.run){
				T3V3Theme.ajax.abort();
			}

			$('#recss-progress').removeClass('invisible').addClass('in').find('.bar').width(0);
			clearTimeout(T3V3Theme.progressid);
			T3V3Theme.progressid = setTimeout(function(){
				$('#recss-progress').find('.bar').width('10%');
			}, 500);
			
			T3V3Theme.run = true;
			T3V3Theme.ajax = $.post(
				T3V3Theme.url + (T3V3Theme.url.indexOf('?') != -1 ? '' : '?') +
				$.param($.extend(params, {
					t3action: 'theme',
					t3template: T3V3Theme.template
				})) , data, function(result){
					
				T3V3Theme.run = false;

				clearTimeout(T3V3Theme.progressid);
				$('#recss-progress').find('.bar').width('100%');

				if(result == ''){
					return;
				}
				
				result = $.parseJSON(result);

				T3V3Theme.alert(result.error || result.success, result.error ? 'error' : (result.success ? 'success' : 'info'), result.theme);

				if(result.theme){
					
					var jel = T3V3Theme.jel;
					
					switch (result.type){	
						
						case 'new':
						case 'duplicate':			
							jel.options[jel.options.length] = new Option(result.theme, result.theme);							
							
							if(!T3V3Theme.nochange){
								jel.options[jel.options.length - 1].selected = true;
								T3V3Theme.changeTheme(result.theme, true);	
								T3V3Theme.nochange = 0;
							} else {

							}
						break;
						
						case 'delete':
							var opts = jel.options;
							
							for(var j = 0, jl = opts.length; j < jl; j++){
								if(opts[j].value == result.theme){
									jel.remove(j);
									break;
								}
							}
							
							jel.options[0].selected = true;					
							T3V3Theme.changeTheme(jel.options[0].value);
						break;
						
						default:
							setTimeout(function(){
								$('#recss-progress').addClass('invisible').find('.bar').width(0);
							}, 1000);
						break;
					}
				} else {
					setTimeout(function(){
						$('#recss-progress').addClass('invisible').find('.bar').width(0);
					}, 1000);
				}
			});
		},

		alert: function(msg, type, title){
			$('#thememagic .alert').remove();

			T3V3Theme.jalert = $('<div class="alert alert-' + (type || 'info') + '">' +
				'<button type="button" class="close" data-dismiss="alert">×</button>' +
				(title ? '<h4 class="alert-heading">' + title + '</h4>' : '') +
				'<p>' + msg + '</p>' +
			'</div>').prependTo($('#ja-variable-form')).on('closed', function(){
				clearTimeout(T3V3Theme.salert);
				T3V3Theme.jalert = null;
			}).alert();

			clearTimeout(T3V3Theme.salert);
			T3V3Theme.salert = setTimeout(function(){
				if(T3V3Theme.jalert){
					T3V3Theme.jalert.alert('close');
					T3V3Theme.jalert = null;
				}
			}, 5000);
		},

		confirm: function(msg, callback){
			T3V3Theme.modalCallback = callback;

			var jdialog = $('#thememagic-dlg');
			jdialog.find('.prompt-block').hide();
			jdialog.find('.message-block').show().find('p').html(msg);
			jdialog.find('.cancel').html(T3V3Theme.langs.lblNo);
			jdialog.find('.btn-primary').html(T3V3Theme.langs.lblYes);

			jdialog.removeClass('modal-prompt modal-alert')
				.addClass('modal-confirm')
				.modal('show');
		},

		prompt: function(msg, callback){
			T3V3Theme.modalCallback = callback;
			var jdialog = $('#thememagic-dlg');
			jdialog.find('.message-block').hide();
			jdialog.find('.prompt-block').show().find('span').html(msg);
			jdialog.find('.cancel').html(T3V3Theme.langs.lblCancel);
			jdialog.find('.btn-primary').html(T3V3Theme.langs.lblOk);

			jdialog.removeClass('modal-alert modal-confirm')
				.addClass('modal-prompt')
				.modal('show');
		},
		
		onKeypress: function(e){
			if(e && e.isTrigger && e.namespace != 'less'){
				return;
			}

			$(this).addClass('changed'); //mark the input as changed
			T3V3Theme.changed = true;
			clearTimeout(T3V3Theme.lid);
			T3V3Theme.lid = setTimeout(T3V3Theme.applyLess, 1000);
		},
		listener: function(e){
			if(typeof T3V3Theme.jsonstr != 'undefined' && e.origin == [window.location.protocol, '//', window.location.hostname, window.location.port].join('')){
				if(e.data == 'rqless'){
					T3V3Theme.applyLess(1);
				}
			}
		}
	});

	if (window.addEventListener){
		window.addEventListener('message', T3V3Theme.listener, false);
	} else {
		window.attachEvent('onmessage', T3V3Theme.listener);
	}
	
	$(document).ready(function(){
		T3V3Theme.initialize();
	});
	
}(window.$ja || window.jQuery);

!function ($) {
	
	$(document).ready(function(){
		if(typeof MooRainbow == 'undefined'){ //only initialize when there was no Joomla default color picker
			$('.input-colorpicker').colorpicker({
				colorFormat: ['#HEX'],
				buttonColorize: true,
				showOn: 'both',
				buttonImage: T3V3Theme.colorimgurl,
				open: function(e){
					$(e.target).data('ccolor', $(e.target).val());
				},
				close: function(e, info){
					if(info.iscancel){
						$(e.target).val($(e.target).data('ccolor'));
					} else {				
						T3V3Theme.onKeypress.apply(e.target);
					}

				}
			});
		}
	});
	
}(window.$ja || window.jQuery);

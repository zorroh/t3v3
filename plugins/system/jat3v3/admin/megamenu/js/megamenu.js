var T3V3AdminMegamenu = window.T3V3AdminMegamenu || {};

!function ($) {
	var currentSelected = null,
	megamenu, nav_items, nav_subs, nav_cols, nav_all;

	$.fn.megamenuAdmin = function (options) {
		var defaultOptions = {
		};

		var options = $.extend(defaultOptions, options);
		megamenu = $(this);
		nav_items = megamenu.find('ul[class*="level"]>li>a');
		nav_subs = megamenu.find('.nav-child');
		nav_cols = megamenu.find('[class*="span"]');
		
		nav_all = nav_items.add(nav_subs).add(nav_cols);
		// hide sub 
		nav_items.each (function () {
			var a = $(this),
				liitem = a.closest('li');
			if (liitem.data ('sub') == 'hide') {
				var sub = liitem.find('.nav-child:first');
				// check if have menu-items in sub
				sub.css('display','none');
				a.removeClass ('dropdown-toggle').data('toggle', '');
				liitem.removeClass('dropdown dropdown-submenu mega');
			}
		});
		// hide toolbox
		hide_toolbox(true);
		// bind events for all selectable elements
		bindEvents (nav_all);

		// unbind all events for toolbox actions & inputs
		$('.toolbox-action, .toolbox-toggle, .toolbox-input').unbind ("focus blur click change keydown");

		// stop popup event when click in toolbox area
		$('.t3-row-mega').click (function(event) {
			event.stopPropagation();
			return false;
		});
		// deselect when click outside menu
		$(document.body).click (function(event) {
			hide_toolbox (true);
			//event.stopPropagation();
		});

		// bind event for action
		$('.toolbox-action').click (function(event) {
			var action = $(this).data ('action');

			if (action) {
				actions.datas = $(this).data();
				actions[action] ();
			}
			event.stopPropagation();
			return false;
		});
		$('.toolbox-toggle').change (function(event) {
			var action = $(this).data ('action');
			if (action) {
				actions.datas = $(this).data();
				actions[action] ();
			}
			event.stopPropagation();
			return false;
		});
		// ignore events
		$('.toolbox-input').bind ('focus blur click', function(event) {
			event.stopPropagation();
			return false;
		});
		$('.toolbox-input').bind ('keydown', function(event) {
			if (event.keyCode == '13') {
				apply_toolbox (this);
				event.preventDefault();
			}
		});

		$('.toolbox-input').change (function(event) {
			apply_toolbox (this);
			event.stopPropagation();
			return false;
		});

		return this;
	};

	// Actions
	var actions = {};
	actions.data = {};

	actions.toggleSub = function () {
		if (!currentSelected) return ;
		var liitem = currentSelected.closest('li'),
		sub = liitem.find ('.nav-child:first');
		if (liitem.data('group')) return; // not allow do with group
		if (sub.length == 0 || sub.css('display') == 'none') {
			// add sub
			if (sub.length == 0) {
				sub = $('<div class="nav-child dropdown-menu mega-dropdown-menu"><div class="row-fluid"><div class="span12" data-width="12"><div class="mega-inner"></div></div></div></div>').appendTo(liitem);
				sub.css ('width', '400');
				sub.data('width', 400);
				bindEvents (sub.find ('[class*="span"]'));
				liitem.data('sub', 'extra');
				liitem.addClass ('mega');
			} else {
				// sub.attr('style', '');
				sub.css('display','');
				liitem.data('sub', '');
			}
			liitem.data('group', 0);
			currentSelected.addClass ('dropdown-toggle').data('toggle', 'dropdown');
			liitem.addClass(liitem.data('level') == 1 ? 'dropdown' : 'dropdown-submenu');
			bindEvents(sub);
		} else {
			unbindEvents(sub);
			// check if have menu-items in sub
			if (liitem.find('ul.level'+liitem.data('level')).length > 0) {
				sub.css('display','none');
				liitem.data('sub', 'hide');
			} else {
				// just remove it
				sub.remove();
			}
			liitem.data('group', 0);
			currentSelected.removeClass ('dropdown-toggle').data('toggle', '');
			liitem.removeClass('dropdown dropdown-submenu mega');
		}
		// update toolbox status
		update_toolbox ();
	}

	actions.toggleGroup = function () {
		if (!currentSelected) return ;
		var liitem = currentSelected.parent(),
			sub = liitem.find ('.nav-child:first');
		if (liitem.data('level') == 1) return; // ignore for top level
		if (liitem.data('group')) {
			liitem.data('group', 0);
			liitem.removeClass('mega-group').addClass('dropdown-submenu');
			currentSelected.addClass ('dropdown-toggle').data('toggle', 'dropdown');
			sub.removeClass ('mega-group-ct').addClass ('dropdown-menu mega-dropdown-menu');
			sub.css('width', sub.data('width'));
			rebindEvents(sub);
		} else {
			currentSelected.removeClass ('dropdown-toggle').data('toggle', '');
			liitem.data('group', 1);
			liitem.removeClass('dropdown-submenu').addClass('mega-group');
			sub.removeClass ('dropdown-menu mega-dropdown-menu').addClass ('mega-group-ct');
			sub.css('width', '');
			rebindEvents(sub);
		}
		// update toolbox status
		update_toolbox ();
	}

	actions.moveItemsLeft = function () {
		if (!currentSelected) return ;
		var $item = currentSelected.closest('li'),
		$liparent = $item.parent().closest('li'),
		level = $liparent.data('level'),
		$col = $item.closest ('[class*="span"]'),
		$items = $col.find ('ul:first > li'),
		itemidx = $items.index ($item),
		$moveitems = $items.slice (0, itemidx+1),
		itemleft = $items.length - $moveitems.length,
		$rows = $col.parent().parent().children ('[class*="row"]'),
		$cols = $rows.children('[class*="span"]').filter (function(){return !$(this).data('position')}),
		colidx = $cols.index ($col);
		if (!$liparent.length) return ; // need make this is mega first

		if (colidx == 0) {
			// add new col
			var oldSelected = currentSelected;
			currentSelected = $col;
			// add column to first
			actions.datas.addfirst = true;
			actions.addColumn ();
			$cols = $rows.children('[class*="span"]').filter (function(){return !$(this).data('position')});
			currentSelected = oldSelected;
			colidx++;
		}
		// move content to right col
		var $tocol = $($cols[colidx-1]);
		var $ul = $tocol.find('ul:first');
		if (!$ul.length) {
			$ul = $('<ul class="mega-nav level'+level+'">').appendTo ($tocol.children('.mega-inner'));
		}
		$moveitems.appendTo($ul);
		if (itemleft == 0) {
			$col.find('ul:first').remove();
		}
		// update toolbox status
		update_toolbox ();
	}

	actions.moveItemsRight = function () {
		if (!currentSelected) return ;
		var $item = currentSelected.closest('li'),
		$liparent = $item.parent().closest('li'),
		level = $liparent.data('level'),
		$col = $item.closest ('[class*="span"]'),
		$items = $col.find ('ul:first > li'),
		itemidx = $items.index ($item),
		$moveitems = $items.slice (itemidx),
		itemleft = $items.length - $moveitems.length,
		$rows = $col.parent().parent().children ('[class*="row"]'),
		$cols = $rows.children('[class*="span"]').filter (function(){return !$(this).data('position')}),
		colidx = $cols.index ($col);
		if (!$liparent.length) return ; // need make this is mega first

		if (colidx == $cols.length - 1) {
			// add new col
			var oldSelected = currentSelected;
			currentSelected = $col;
			actions.datas.addfirst = false;
			actions.addColumn ();
			$cols = $rows.children('[class*="span"]').filter (function(){return !$(this).data('position')});
			currentSelected = oldSelected;
		}
		// move content to right col
		var $tocol = $($cols[colidx+1]);
		var $ul = $tocol.find('ul:first');
		if (!$ul.length) {
			$ul = $('<ul class="mega-nav level'+level+'">').appendTo ($tocol.children('.mega-inner'));
		}
		$moveitems.prependTo($ul);
		if (itemleft == 0) {
			$col.find('ul:first').remove();
		}
		// update toolbox status
		show_toolbox (currentSelected);
	}

	actions.addRow = function () {
		if (!currentSelected) return ;
		var $row = $('<div class="row-fluid"><div class="span12"><div class="mega-inner"></div></div></div>').appendTo(currentSelected.find('[class*="row"]:first').parent()),
		$col = $row.children();
		// bind event
		bindEvents ($col);
		currentSelected = null;
		// switch selected to new column
		show_toolbox ($col);
	}

	actions.alignment = function () {
		var liitem = currentSelected.closest ('li');
		liitem.removeClass ('mega-align-left mega-align-center mega-align-right').addClass ('mega-align-'+actions.datas.align);
		liitem.data('alignsub', actions.datas.align);
		update_toolbox ();
	}

	actions.addColumn = function () {
		if (!currentSelected) return ;
		var $cols = currentSelected.parent().children('[class*="span"]'),
		colcount = $cols.length + 1,
		colwidths = defaultColumnsWidth (colcount);
		// add new column  
		var $col = $('<div><div class="mega-inner"></div></div>');
		if (actions.datas.addfirst) 
			$col.prependTo (currentSelected.parent());
		else {
			$col.insertAfter (currentSelected);
		}
		$cols = $cols.add ($col);
		// bind event
		bindEvents ($col);
		// update width
		$cols.each (function (i) {
			$(this).removeClass ('span'+$(this).data('width')).addClass('span'+colwidths[i]).data('width', colwidths[i]);
		});
		// switch selected to new column
		show_toolbox ($col);
	}

	actions.removeColumn = function () {
		if (!currentSelected) return ;
		var $col = currentSelected,
		$row = $col.parent(),
		$rows = $row.parent().children ('[class*="row"]'),
		$allcols = $rows.children('[class*="span"]'),
		$allmenucols = $allcols.filter (function(){return !$(this).data('position')}),
		$cols = $row.children('[class*="span"]'),
		colcount = $cols.length - 1,
		colwidths = defaultColumnsWidth (colcount),
		type_menu = $col.data ('position') ? false : true;

		if ((type_menu && $allmenucols.length == 1) || $allcols.length == 1) {
			// if this is the only one column left
			return ;
		}
		// remove column  
		// check and move content to other column        
		if (type_menu) {
			var colidx = $allmenucols.index($col),
			tocol = colidx == 0 ? $allmenucols[1] : $allmenucols[colidx-1];
			$col.find ('ul:first > li').appendTo ($(tocol).find('ul:first'));
		} 

		var colidx = $allcols.index($col),
		nextActiveCol = colidx == 0 ? $allcols[1] : $allcols[colidx-1];

		
		if (colcount < 1) {
			$row.remove();
		} else {            
			$cols = $cols.not ($col);
			// update width
			$cols.each (function (i) {
				$(this).removeClass ('span'+$(this).data('width')).addClass('span'+colwidths[i]).data('width', colwidths[i]);
			});
			// remove col
			$col.remove();
		}

		show_toolbox ($(nextActiveCol));
	}

	// toggle screen
	actions.toggleScreen = function () {
		if ($('.toolbox-togglescreen').hasClass('t3-fullscreen-full')) {
			$('.subhead-collapse').removeClass ('subhead-fixed');
			$('#megamenu-admin').closest('.controls').removeClass ('controlbox-fixed');			
			$('.toolbox-togglescreen').removeClass ('t3-fullscreen-full').find('i').removeClass().addClass(actions.datas.iconfull);
		} else {
			$('.subhead-collapse').addClass ('subhead-fixed');
			$('#megamenu-admin').closest('.controls').addClass ('controlbox-fixed');
			$('.toolbox-togglescreen').addClass ('t3-fullscreen-full').find('i').removeClass().addClass(actions.datas.iconsmall);
		}
	}

	actions.saveConfig = function (e) {
		var config = {},
		items = megamenu.find('ul[class*="level"] > li');
		items.each (function(){
			var $this = $(this),
			id = 'item-'+$this.data('id'),
			item = {};
			if ($this.hasClass ('mega')) {
				var $sub = $this.find ('.nav-child:first');
				item['sub'] = {};
				if ($sub.data('width')) {
					item['sub']['width'] = $sub.data('width');
				}
				if ($sub.data('class')) {
					item['sub']['class'] = $sub.data('class');
				}
				// build row
				var $rows = $sub.find('[class*="row"]:first').parent().children('[class*="row"]'),
				rows = [],
				i = 0;

				$rows.each (function () {
					var row = [],
					$cols = $(this).children('[class*="span"]'),
					j = 0;
					$cols.each (function(){
						var li = $(this).find('ul[class*="level"] > li:first'),
						col = {};
						if (li.length) {
							col['item'] = li.data('id');
						} else if ($(this).data('position')) {
							col['position'] = $(this).data('position');
						} else {
							col['item'] = 0;
						}
						if ($(this).data('width')) col['width'] = $(this).data('width');
						if ($(this).data('class')) col['class'] = $(this).data('class');
						row[j++] = col;
					});
					rows[i++] = row;
				});
				item['sub']['rows'] = rows;
			}
			if ($this.data('class')) {
				item['class'] = $this.data('class');
			}
			if ($this.data('group')) {
				item['group'] = $this.data('group');
			}
			if ($this.data('sub') == 'hide') {
				item['hidesub'] = 1;
			}
			if ($this.data('alignsub')) {
				item['alignsub'] = $this.data('alignsub');
			}
			if (Object.keys(item).length) config[id] = item;
		});

		var menutype = $('#jform_params_mm_type').val();
		$.ajax({
			url: T3V3Admin.adminurl,
			data:{'t3action':'megamenu', 't3task':'save', 'menutype': menutype, 'config': JSON.stringify(config)},
			type: 'POST',
			async: !e || e.isTrigger
		});
	}

	toolbox_type = function () {
		return currentSelected[0].tagName == 'A' ? 'item' : (currentSelected.hasClass ('nav-child') ? 'sub' : 'col');
	}

	hide_toolbox = function (show_intro) {
		$('#megamenu-toolbox .toolbox').hide();
		currentSelected = null;
		if (megamenu && megamenu.data('nav_all')) megamenu.data('nav_all').removeClass ('selected');
		megamenu.find ('li').removeClass ('open');
		if (show_intro) {
			$('#megamenu-intro').show();
		} else {
			$('#megamenu-intro').hide();
		}
	}

	show_toolbox = function (selected) {
		hide_toolbox (false);
		if (selected) currentSelected = selected;
		// remove class open for other
		megamenu.find ('ul[class*="level"] > li').each (function(){
			if (!$(this).has (currentSelected).length > 0) $(this).removeClass ('open');
			else $(this).addClass ('open');
		});            

		// set selected
		megamenu.data('nav_all').removeClass ('selected');
		currentSelected.addClass ('selected');		
		var type = toolbox_type ();
		$('#megamenu-tool' + type).show();
		update_toolbox (type);

		$('#megamenu-toolbox').show();
	}

	update_toolbox = function (type) {
		if (!type) type = toolbox_type ();
		// remove all disabled status
		$('#megamenu-toolbox .disabled').removeClass('disabled');
		$('#megamenu-toolbox .active').removeClass('active');
		switch (type) {
			case 'item':
				$('.toolitem-exclass').attr('value', currentSelected.data ('class') || '');
				// value for toggle
				var liitem = currentSelected.closest('li'),
					liparent = liitem.parent().closest('li'),
					sub = liitem.find ('.nav-child:first');
					
				// toggle Submenu
				var toggle = $('.toolitem-sub');
				toggle.find('label').removeClass('active btn-success btn-danger btn-primary');
				if (liitem.data('group')) {
					// disable the toggle
					$('.toolitem-sub').addClass ('disabled');
				} else if (sub.length == 0 || sub.css('display') == 'none') {
					// sub disabled
					update_toggle (toggle, 0);
				} else {
					// sub enabled
					update_toggle (toggle, 1);
				}				

				// toggle Group
				var toggle = $('.toolitem-group');
				toggle.find('label').removeClass('active btn-success btn-danger btn-primary');
				if (liitem.data('level') == 1 || sub.length == 0 || liitem.data('sub') == 'hide') {
					// disable the toggle
					$('.toolitem-group').addClass ('disabled');
				} else if (liitem.data('group')) {
					// Group off
					update_toggle (toggle, 1);
				} else {
					// Group on
					update_toggle (toggle, 0);				
				}

				// move left/right column action: disabled if this item is not in a mega submenu
				if (!liparent.length || !liparent.hasClass('mega')) {
					$('.toolitem-moveleft, .toolitem-moveright').addClass ('disabled');
				}

				break;

			case 'sub':
				var liitem = currentSelected.closest('li');
				$('.toolsub-exclass').attr('value', currentSelected.data ('class') || '');

				if (liitem.data('group')) {
					$('.toolsub-width').attr('value', '').addClass ('disabled');
					// disable alignment
					$('.toolitem-alignment').addClass ('disabled');
				} else {
					$('.toolsub-width').attr('value', currentSelected.data ('width') || '');
					// if not top level, allow align-left & right only
					if (liitem.data('level') > 1) {
						$('.toolsub-align-center').addClass ('disabled');
					}

					// active align button
					if (liitem.data('alignsub')) {
						$('.toolsub-align-'+liitem.data('alignsub')).addClass ('active');
					}					
				}

				break;

			case 'col':
				$('.toolcol-exclass').attr('value', currentSelected.data ('class') || '');
				//$('.toolcol-position').attr('value', currentSelected.data ('position') || '');
				//$('.toolcol-width').attr('value', currentSelected.data ('width') || '');
				$('.toolcol-position').val (currentSelected.data ('position') || '').trigger("liszt:updated");
				$('.toolcol-width').val (currentSelected.data ('width') || '').trigger("liszt:updated");
				/* enable/disable module chosen */
				if (currentSelected.find ('.mega-nav').length > 0) {
					$('.toolcol-position').parent().addClass('disabled');
				}
				// disable choose width if signle column
				if (currentSelected.parent().children().length == 1) {
					$('.toolcol-width').parent().addClass ('disabled');
				}
					
				break;
		}
	}

	update_toggle = function (toggle, val) {
		$input = toggle.find('input[value="'+val+'"]');
		$input.attr('checked', 'checked');
		$input.trigger ('update');
	}

	apply_toolbox = function (input) {
		var name = $(input).data ('name'), 
		value = input.value,
		type = toolbox_type ();
		switch (name) {
			case 'width':
				if (type == 'sub') {
					currentSelected.width(value);
				}
				if (type == 'col') {
					currentSelected.removeClass('span'+currentSelected.data(name)).addClass ('span'+value);
				}
				currentSelected.data (name, value);
				break;

			case 'class':
				if (type == 'item') {
					var item = currentSelected.closest('li');
				} else {
					var item = currentSelected;
				}
				item.removeClass(item.data(name) || '').addClass (value);
				item.data (name, value);
				break;

			case 'position':
				// replace content if this is not menu-items type
				if (currentSelected.find ('ul[class*="level"]').length == 0) {
					// get module content
					if (value) {
						$.ajax({
							url: T3V3Admin.rooturl,
							data:{'t3action':'module', 'mid': value}
						}).done(function ( data ) {
							currentSelected.find('.mega-inner').html(data).find(':input').removeAttr('name');
						});
					} else {
						currentSelected.find('.mega-inner').html('');
					}
					currentSelected.data (name, value);
				}
				break;
		}
	}

	defaultColumnsWidth = function (count) {
		if (count < 1) return null;
		var total = 12,
		min = Math.floor(total / count),
		widths = [];
		for(var i=0;i<count;i++) {
			widths[i] = min;
		}
		widths[count - 1] = total - min*(count-1);
		return widths;
	}

	bindEvents = function (els) {
		if (megamenu.data('nav_all')) 
			megamenu.data('nav_all', megamenu.data('nav_all').add(els));
		else
			megamenu.data('nav_all', els);

		els.mouseover(function(event) {
			megamenu.data('nav_all').removeClass ('hover');
			$this = $(this);
			clearTimeout (megamenu.data('hovertimeout'));
			megamenu.data('hovertimeout', setTimeout("$this.addClass('hover')", 100));
			event.stopPropagation();
		});
		els.mouseout(function(event) {
			clearTimeout (megamenu.data('hovertimeout'));
			$(this).removeClass('hover');
		});

		els.click (function(event){
			show_toolbox ($(this));
			event.stopPropagation();                
			return false;
		});
	}

	unbindEvents = function (els) {
		megamenu.data('nav_all', megamenu.data('nav_all').not(els));
		els.unbind('mouseover').unbind('mouseout').unbind('click');
	}

	rebindEvents = function (els) {
		unbindEvents(els);
		bindEvents(els);
	}
}(window.$ja || window.jQuery);

!function($){
	$.extend(T3V3AdminMegamenu, {
		// put megamenu admin panel into right place
		prepare: function(){
			// var panel = $('#jform_params_mm_panel-lbl').closest ('.control-group').find('.controls');
			var panel = $('#jform_params_mm_type').closest ('.controls');
			panel.append ($('#megamenu-admin').removeClass('hidden'));

			// first load
			if ($('#jform_params_navigation_type').val() == 'megamenu') {
				setTimeout(function(){ //wait for page ready
					$('#jform_params_mm_type').trigger('change.less');
				}, 500);
			} else {

				// handle event for enable megamenu
				$('#jform_params_navigation_type').on('change', function(e) {
					if ($(this).val() == 'megamenu'){
						$('#jform_params_mm_type').trigger('change.less');
					}
				});
			}
		},

		t3megamenu: function(form, ctrlelm, ctrl, rsp){
			$('#megamenu-container').html(rsp).megamenuAdmin().find(':input').removeAttr('name');
		},

		initPanel: function(){
			$('#jform_params_mm_panel').hide();
		},

		initPreSubmit: function(){

			var form = document.adminForm;
			if(!form){
				return false;
			}

			var onsubmit = form.onsubmit;

			form.onsubmit = function(e){
				$('.toolbox-saveConfig').trigger('click');
				if($.isFunction(onsubmit)){
					onsubmit();
				}
			};
		},
	});

	$(window).load(function(){
		T3V3AdminMegamenu.prepare();
	});

	$(document).ready(function(){
		T3V3AdminMegamenu.initPanel();
		T3V3AdminMegamenu.initPreSubmit();
	});

}(window.$ja || window.jQuery);
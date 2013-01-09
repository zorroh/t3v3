<?php
t3v3import ('menu/megamenu.tpl');

class T3V3MenuMegamenu {
	protected $children = array();
	protected $_items = array();
	protected $settings = null;
	protected $menu = '';

	function __construct ($menutype='mainmenu', $settings=array()) {
		$app = JFactory::getApplication();
		$menu = $app->getMenu('site');
		$items = $menu->getItems('menutype', $menutype);
		$this->settings = $settings;
		$this->editmode = isset ($settings['editmode']);
		foreach ($items as &$item) {
			$parent = isset($this->children[$item->parent_id]) ? $this->children[$item->parent_id] : array();
			$parent[] = $item;
			$this->children[$item->parent_id] = $parent;
			$this->_items[$item->id] = $item;
		}
		foreach ($items as &$item) {
			// bind setting for this item
			$key = 'item-'.$item->id;
			$setting = isset($this->settings[$key]) ? $this->settings[$key] : array();

			$item->mega = 0;
			$item->group = 0;
			$item->dropdown = 0;
			if (isset($setting['group'])) {
				$item->group = 1;
			} else {
				if ((isset($this->children[$item->id]) && ($this->editmode || !isset($setting['hidesub']))) || isset($setting['sub'])) {
					$item->dropdown = 1;
				}
			}
			$item->mega = $item->group || $item->dropdown;
			// set default sub if not exists
			if ($item->mega && !isset($setting['sub'])) {
				$c = $this->children[$item->id][0]->id;
				$setting['sub'] = array('rows'=>array(array(array('width'=>12, 'item'=>$c))));
			}
			$item->setting = $setting;
		}
	}

	function render () {
		$this->menu = '';

		$this->_('beginmenu');
		$keys = array_keys($this->_items);
		$this->nav(null, $keys[0]);
		$this->_('endmenu');

		echo $this->menu;
	}

	function nav ($pitem, $start = 0, $end = 0) {
		if ($start > 0) {		
			if (!isset ($this->_items[$start])) return ;
			$pid = $this->_items[$start]->parent_id;
			$items = array();
			$started = false;
			foreach ($this->children[$pid] as $item) {
				if ($started) {
					if ($item->id == $end) break;
					$items[] = $item;
				} else {
					if ($item->id == $start) {
						$started = true;
						$items[] = $item;
					}
				}
			}
			if (!count($items)) return;
		} else {
			$pid = $pitem->id;
			if (!isset($this->children[$pid])) return ;
			$items = $this->children[$pid];			
		}

		$this->_('beginnav', array ('item'=>$pitem));

		foreach ($items as $item) {
			$this->item ($item);
		}

		$this->_('endnav', array ('item'=>$pitem));
	}

	function item ($item) {
		// item content
		$setting = $item->setting;
		
		$this->_('beginitem', array ('item'=>$item, 'setting'=>$setting));

		$this->menu .= $this->_('item', array ('item'=>$item, 'setting'=>$setting));

		if ($item->mega) {
			$this->mega($item);
		}
		$this->_('enditem', array ('item'=>$item));
	}

	function mega ($item) {
		$key = 'item-'.$item->id;
		$setting = $item->setting;
		$sub = $setting['sub'];

		$this->_('beginmega', array ('item'=>$item));
		$endItems = array();
		$k = 0;
		foreach ($sub['rows'] as $row) {
			foreach ($row as $col) {
				if (!isset($col['position'])) {
					if ($k) $endItems[$k] = $col['item'];
					$k = $col['item'];
				}
			}
		}
		$endItems[$k] = 0;

		foreach ($sub['rows'] as $row) {
			$this->_('beginrow');
			foreach ($row as $col) {
				$this->_('begincol', array('setting'=>$col));
				if (isset($col['position'])) {
					$this->module ($col['position']);
				} else {
					$toitem = $endItems[$col['item']];
					$this->nav ($item, $col['item'], $toitem);
				}
				$this->_('endcol');
			}
			$this->_('endrow');
		}
		$this->_('endmega');
	}

	function module ($module) {
			// load module
		$id = intval($module);
		$db = JFactory::getDbo();
    $query = $db->getQuery(true);
		$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params');
		$query->from('#__modules AS m');
		$query->where('m.id = '.$id);
		$query->where('m.published = 1');
		$db->setQuery($query);
		$module = $db->loadObject ();

		$style = 'jaxhtml';
		$content = JModuleHelper::renderModule($module, array('style'=>$style));

		$this->menu .= $content."\n";
	}

	function _ ($tmpl, $vars = array()) {
		if (method_exists('T3V3MenuMegamenuTpl', $tmpl)) {			
			$this->menu .= T3V3MenuMegamenuTpl::$tmpl($vars)."\n";
		} else {
			$this->menu .= "$tmpl\n";			
		}
	}
}
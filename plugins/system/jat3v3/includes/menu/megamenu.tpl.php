<?php

class T3V3MenuMegamenuTpl {
	static function beginmenu ($vars) {
		return '<div id="ja-megamenu">';
	}
	static function endmenu ($vars) {
		return '</div>';
	}

	static function beginnav ($vars) {
		$item = $vars['item'];
		$cls = '';
		if (!$item) {
			// first nav
			$cls = 'nav level0';
		} else {
			$cls .= ' mega-nav';
			$cls .= ' level'.$item->level;
		}
		if ($cls) $cls = 'class="'.trim($cls).'"';

		return '<ul '.$cls.'>';
	}
	static function endnav ($vars) {
		return '</ul>';
	}

	static function beginmega ($vars) {
		$item = $vars['item'];
		$setting = $item->setting;
		$sub = $setting['sub'];
		$cls = 'nav-child '.($item->dropdown ? 'dropdown-menu mega-dropdown-menu' : 'mega-group-ct');
		$style = '';
		$data = '';
		if (isset($sub['width'])) {
			$style = " style=\"width:{$sub['width']}px\"";
			$data .= " data-width=\"{$sub['width']}\"";
		} 
		if (isset($setting['class'])) $data .= " data-class=\"{$setting['class']}\"";

		if ($cls) $cls = 'class="'.trim($cls).'"';

		return "<div $cls $style $data>";
	}
	static function endmega ($vars) {
		return '</div>';
	}

	static function beginrow ($vars) {
		return '<div class="row-fluid">';
	}
	static function endrow ($vars) {
		return '</div>';
	}

	static function begincol ($vars) {
		$setting = isset($vars['setting']) ? $vars['setting'] : array();
		$width = isset($setting['width']) ? $setting['width'] : '12';
		$data = "data-width=\"$width\"";
		if (isset($setting['class'])) $data .= " data-class=\"{$setting['class']}\"";
		if (isset($setting['position'])) $data .= " data-position=\"{$setting['position']}\"";

		return "<div class=\"span$width\" $data><div class=\"mega-inner\">";
	}
	static function endcol ($vars) {
		return '</div></div>';
	}

	static function beginitem ($vars) {
		$item = $vars['item'];
		$setting = $item->setting;
		$cls = '';
		if ($item->dropdown) {
			$cls .= $item->level == 1 ? ' dropdown' : ' dropdown-submenu';
		}
		if ($item->mega) $cls .= ' mega';
		if ($item->group) $cls .= ' mega-group';

		$data = "data-id=\"{$item->id}\" data-level=\"{$item->level}\"";
		if ($item->group) $data .= " data-group=\"1\"";
		if (isset($setting['class'])) {
			$data .= " data-class=\"{$setting['class']}\"";
			$cls .= " {$setting['class']}";
		}
		if (isset($setting['alignsub'])) {
			$data .= " data-alignsub=\"{$setting['alignsub']}\"";
			$cls .= " mega-align-{$setting['alignsub']}";
		}
		if (isset($setting['hidesub'])) $data .= " data-sub=\"hide\"";

		if ($cls) $cls = 'class="'.trim($cls).'"';

		return "<li $cls $data>";
	}
	static function enditem ($vars) {
		$item = $vars['item'];
		$setting = $item->setting;
		return '</li>';
	}
	function item ($vars) {
		$item = $vars['item'];
		$setting = $item->setting;
		$attr = '';
		$caret = '';
		if ($item->dropdown) {
			$attr = ' class="dropdown-toggle" data-toggle="dropdown"';
			$caret = '<b class="caret"></b>';
		}
		if($item->browserNav > 0){
			$attr .= ' target="blank"';
		}

		$flink = $item->link;
		switch ($item->type)
		{
			case 'separator':
			case 'heading':
				// No further action needed.
				break;

			case 'url':
				if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)){
					// If this is an internal Joomla link, ensure the Itemid is set.
					$flink = $item->link . '&Itemid=' . $item->id;
				}
				break;

			case 'alias':
				// If this is an alias use the item id stored in the parameters to make the link.
				$flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
				break;

			default:
				$router = JSite::getRouter();
				if ($router->getMode() == JROUTER_MODE_SEF){
					$flink = 'index.php?Itemid=' . $item->id;
				} else {
					$flink .= '&Itemid=' . $item->id;
				}
				break;
		}

		if (strcasecmp(substr($flink, 0, 4), 'http') && (strpos($flink, 'index.php?') !== false)){
			$flink = JRoute::_($flink, true, $item->params->get('secure'));
		}
		else {
			$flink = JRoute::_($flink);
		}

		return '<a href="'.$flink.'" '.$attr.'><span>'.$item->title.'</span>'.$caret.'</a>';
	}
}

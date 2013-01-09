<?php

class T3V3MenuMegamenuTpl {
	static function beginmenu ($vars) {
		return '<div id="ja-mega-menu">';
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
		$cls = 'nav-child '.($item->dropdown ? 'dropdown-menu mega-menu' : 'mega-group');
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
		if ($item->group) $cls .= ' group';
		if ($cls) $cls = 'class="'.trim($cls).'"';

		$data = "data-id=\"{$item->id}\" data-level=\"{$item->level}\"";
		if ($item->group) $data .= " data-group=\"1\"";
		if (isset($setting['class'])) $data .= " data-class=\"{$setting['class']}\"";

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

		return '<a href="#" '.$attr.'><span>'.$item->title.'</span>'.$caret.'</a>';
	}
}

<?php
/**
 * $JA#COPYRIGHT$
 */

// No direct access
defined('_JEXEC') or die();
/**
 * T3V3Less class compile less
 *
 * @package T3V3
 */
class T3V3Action extends JObject
{
	static public function run ($action) {
		if (method_exists('T3V3Action', $action)) {
			T3V3Action::$action();
		}
	}

	static public function lessc () {
		$path = JRequest::getString ('s');
		t3v3import ('core/less');
		$t3less = new T3V3Less;
		$css = $t3less->getCss($path);

		header("Content-Type: text/css");
		header("Content-length: ".strlen($css));
		echo $css;
		exit;
	}

	public static function lesscall(){
		JFactory::getLanguage()->load(T3V3_PLUGIN, JPATH_ADMINISTRATOR);

		t3v3import ('core/less');
		
		$result = array();
		try{
			T3V3Less::compileAll();
			$result['successful'] = JText::_('T3V3_THEME_COMPILE_SUCCESS');
		}catch(Exception $e){
			$result['error'] = sprintf(JText::_('T3V3_THEME_COMPILE_FAILED'), $e->getMessage());
		}
		
		die(json_encode($result));
	}

	public static function theme(){
		
		JFactory::getLanguage()->load(T3V3_PLUGIN, JPATH_ADMINISTRATOR);
		JFactory::getLanguage()->load('tpl_' . T3V3_TEMPLATE, JPATH_SITE);

		if(!defined('T3V3')) {
			die(json_encode(array(
				'error' => JText::_('T3V3_THEME_PLUGIN_NOT_READY')
			)));
		}

		$user = JFactory::getUser();
		$action = JRequest::getCmd('t3task', '');

		if ($action != 'thememagic' && !$user->authorise('core.manage', 'com_templates')) {
		    die(json_encode(array(
				'error' => JText::_('T3V3_THEME_NO_PERMISSION')
			)));
		}

		
		if(empty($action)){
			die(json_encode(array(
				'error' => JText::_('T3V3_THEME_UNKNOW_ACTION')
			)));
		}

		t3v3import('core/theme');
		
		if(method_exists('ThemeHelper', $action)){
			ThemeHelper::$action(T3V3_TEMPLATE_PATH . '/assets');	
		} else {
			die(json_encode(array(
				'error' => JText::_('T3V3_THEME_UNKNOW_ACTION')
			)));
		}
	}

	static public function unittest () {
		$app = JFactory::getApplication();
		$tpl = $app->getTemplate(true);
		$t3v3 = T3V3::getApp($tpl);
		$t3v3->posname ('sidebar-1 , position-2 , position-3 , position-4');
		echo "<br />";
		$t3v3->posname ('sidebar-1 or position-2 or position-3 or position-4');
		exit;
	}	
}
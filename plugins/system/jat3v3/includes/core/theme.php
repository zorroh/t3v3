<?php
/**
 * $JA#COPYRIGHT$
 */

/**
 *
 * Admin helper module class
 * @author JoomlArt
 *
 */
class ThemeHelper
{
	/**
	 *
	 * save Profile
	 */
	
	public static function response($data){
		die(json_encode($data));
	}
	
	public static function error($msg){
		return self::response(array('error' => $msg));
	}
	
	public static function save($path)
	{
		$result = array();
		
		if(empty($path)){
			return self::error(JText::_('T3V3_THEME_UNKNOWN_THEME'));
		}
		
		$theme = JRequest::getCmd('theme');
		$from = JRequest::getCmd('from');
		if (!$theme) {
		   return self::error(JText::_('T3V3_THEME_INVALID_DATA_TO_SAVE'));
		}

		$file = $path . '/less/themes/' . $theme . '/variables-custom.less';

		if(!class_exists('JRegistryFormatLESS')){
			include_once dirname(dirname(dirname(__FILE__))) . '/includes/format/less.php';
		}
		$variables = new JRegistry();
		$variables->loadObject($_POST);
		
		$data = $variables->toString('LESS');
		$type = 'new';
		if (JFile::exists($file)) {
			@chmod($file, 0777);
			$type = 'overwrite';
		} else {

			if(JFolder::exists($path . '/less/themes/' . $from)){
				if(@JFolder::copy($path . '/less/themes/' . $from, $path . '/less/themes/' . $theme) != true){
					return self::error(JText::_('T3V3_THEME_NOT_FOUND'));
				}
			} else if($from == 'default') {
				$dummydata = "";
				@JFile::write($path . '/less/themes/' . $theme . '/template.less', $dummydata);
				@JFile::write($path . '/less/themes/' . $theme . '/variables.less', $dummydata);
				@JFile::write($path . '/less/themes/' . $theme . '/template-responsive.less', $dummydata);
			}
		}
		
		$return = @JFile::write($file, $data);

		if (!$return) {
			return self::error(JText::_('T3V3_THEME_OPERATION_FAILED'));
		} else {
			$result['success'] = sprintf(JText::_('T3V3_THEME_SAVE_SUCCESSFULLY'), $theme);
			$result['theme'] = $theme;
			$result['type'] = $type;
		}

		//LessHelper::compileForTemplate(T3V3_TEMPLATE_PATH, $theme);
		t3v3import ('core/less');
		T3V3Less::compileAll($theme);
		return self::response($result);
	}

	/**
	 *
	 * Clone Profile
	 */
	public static function duplicate($path)
	{
		$theme = JRequest::getCmd('theme');
		$from = JRequest::getCmd('from');
		$result = array();
		
		if (empty($theme) || empty($from)) {
			return self::error(JText::_('T3V3_THEME_INVALID_DATA_TO_SAVE'));
		}

		$source = $path . '/less/themes/' . $from;
		if (!JFolder::exists($source)) {
			return self::error(JText::_(sprintf('T3V3_THEME_NOT_FOUND', $from)));
		}
		
		$dest = $path . '/less/themes/' . $theme;
		if (JFolder::exists($dest)) {
			return self::error(sprintf(JText::_('T3V3_THEME_EXISTED'), $theme));
		}

		$result = array();
		if (@JFolder::copy($source, $dest) == true) {
			$result['success'] = JText::_('T3V3_THEME_CLONE_SUCCESSFULLY');
			$result['theme'] = $theme;
			$result['reset'] = true;
			$result['type'] = 'duplicate';
		} else {
			return self::error(JText::_('T3V3_THEME_OPERATION_FAILED'));
		}
		
		//LessHelper::compileForTemplate(T3V3_TEMPLATE_PATH , $theme);
		t3v3import ('core/less');
		T3V3Less::compileAll($theme);
		return self::response($result);
	}

	/**
	 *
	 * Delete a profile
	 */
	public static function delete($path)
	{
		// Initialize some variables
		$theme = JRequest::getCmd('theme');
		$result = array();
		
		if (!$theme) {
			return ThemeHelper::error(JText::_('T3V3_THEME_UNKNOWN_THEME'));
		}

		$file = $path . '/less/themes/' . $theme;
		$return = false;
		if (!JFolder::exists($file)) {
			return ThemeHelper::error(JText::_(sprintf('T3V3_THEME_NOT_FOUND', $theme)));
		}
		
		$return = @JFolder::delete($file);
		
		if (!$return) {
			return ThemeHelper::error(sprintf(JText::_('T3V3_THEME_DELETE_FAIL'), $file));
		} else {
			
			$result['template'] = '0';
			$result['success'] = sprintf(JText::_('T3V3_THEME_DELETE_SUCCESSFULLY'), $theme);
			$result['theme'] = $theme;
			$result['type'] = 'delete';
		}

		JFolder::delete($path . '/css/themes/' . $theme);
		return self::response($result);
	}

	/**
	 *
	 * Show thememagic form
	 */
	public static function thememagic($path)
	{
		$app = JFactory::getApplication();
		$isadmin = $app->isAdmin();
		$url = $isadmin ? JUri::root(true).'/' : JUri::current();
		$url .= (preg_match('/\?/', $url) ? '&' : '?').'themer=1';
		// show thememagic form

		//todo: Need to optimize here
		$tplparams = JApplication::getInstance('site')->getTemplate(true)->params;

		$jassetspath = T3V3_TEMPLATE_PATH . '/assets';
		$jathemepath = $jassetspath . '/less/themes';
		if(!class_exists('JRegistryFormatLESS')){
			include_once T3V3_ADMIN_PATH . '/includes/format/less.php';
		}

		$themes = array();
		$jsondata = array();

		//push a default theme
		$tobj = new stdClass();
		$tobj->id = 'default';
		$tobj->title = JText::_('JDEFAULT');
		$themes['default'] = $tobj;
		$varfile = $jassetspath . '/less/variables.less';
		if(file_exists($varfile)){
			$params = new JRegistry;
			$params->loadString(JFile::read($varfile), 'LESS');
			$jsondata['default'] = $params->toArray();
		}

		if (JFolder::exists($jathemepath)) {
			$jathemes = JFolder::folders($jathemepath);
			if ($jathemes) {
				foreach ($jathemes as $theme) {
					$varsfile = $jathemepath . '/' . $theme . '/variables-custom.less';
					if(file_exists($varsfile)){

						$tobj = new stdClass();
						$tobj->id = $theme;
						$tobj->title = $theme;

						//check for all less file in theme folder
						$params = false;
						$others = JFolder::files($jathemepath . '/' . $theme, '.less');
						foreach($others as $other){
							//get those developer custom values
							if($other == 'variables.less'){
								$params = new JRegistry;
								$params->loadString(JFile::read($jathemepath . '/' . $theme . '/variables.less'), 'LESS');								
							}

							if($other != 'variables-custom.less'){
								$tobj->$other = true; //JFile::read($jathemepath . '/' . $theme . '/' . $other);
							}
						}

						$cparams = new JRegistry;
						$cparams->loadString(JFile::read($jathemepath . '/' . $theme . '/variables-custom.less'), 'LESS');
						if($params){
							foreach ($cparams->toArray() as $key => $value) {
								$params->set($key, $value);
							}	
						} else {
							$params = $cparams;
						}

						$themes[$theme] = $tobj;
						$jsondata[$theme] = $params->toArray();
					}
				}
			}
		}

		$langs = array (
			'addTheme' => JText::_('T3V3_THEME_ASK_ADD_THEME'),
			'delTheme' => JText::_('T3V3_THEME_ASK_DEL_THEME'),
			'correctName' => JText::_('T3V3_THEME_ASK_CORRECT_NAME'),
			'themeExist' => JText::_('T3V3_THEME_EXISTED'),
			'saveChange' => JText::_('T3V3_THEME_ASK_SAVE_CHANGED'),
			'lblCancel' => JText::_('JCANCEL'),
			'lblOk'	=> JText::_('T3V3_THEME_LABEL_OK'),
			'lblNo' => JText::_('JNO'),
			'lblYes' => JText::_('JYES')
		);

		$backurl = JFactory::getURI();
		$backurl->delVar('t3action');
		$backurl->delVar('t3task');

		$form = new JForm('thememagic.themer', array('control' => 'jaform'));
		$form->load(JFile::read(T3V3_PATH . DIRECTORY_SEPARATOR . 'params' . DIRECTORY_SEPARATOR . 'themer.xml'));
		$form->loadFile(T3V3_TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'templateDetails.xml', false, '//config');

		$fieldSets = $form->getFieldsets('thememagic');

		include T3V3_ADMIN_PATH.'/admin/tpls/thememagic.php';
		
		exit();
	}	
}
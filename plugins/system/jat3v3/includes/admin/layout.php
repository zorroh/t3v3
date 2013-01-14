<?php
/**
 * $JA#COPYRIGHT$
 */


/**
 *
 * Layout helper module class
 * @author JoomlArt
 *
 */
class T3v3AdminLayout
{
	public static function response($result = array()){
		die(json_encode($result));
	}

	public static function error($msg = ''){
		return self::response(array(
			'error' => $msg
			));
	}

	public static function display(){
		
		$japp = JFactory::getApplication();
		if(!$japp->isAdmin()){
			$tpl = $japp->getTemplate(true);
		} else {

			$tplid = JFactory::getApplication()->input->getCmd('view') == 'style' ? JFactory::getApplication()->input->getCmd('id', 0) : false;
			if(!$tplid){
				die(json_encode(array(
					'error' => JText::_('T3V3_MSG_UNKNOW_ACTION')
					)));
			}

			$cache = JFactory::getCache('com_templates', '');
			if (!$templates = $cache->get('jat3tpl')) {
				// Load styles
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('id, home, template, s.params');
				$query->from('#__template_styles as s');
				$query->where('s.client_id = 0');
				$query->where('e.enabled = 1');
				$query->leftJoin('#__extensions as e ON e.element=s.template AND e.type='.$db->quote('template').' AND e.client_id=s.client_id');

				$db->setQuery($query);
				$templates = $db->loadObjectList('id');
				foreach($templates as &$template) {
					$registry = new JRegistry;
					$registry->loadString($template->params);
					$template->params = $registry;
				}
				$cache->store($templates, 'jat3tpl');
			}

			if (isset($templates[$tplid])) {
				$tpl = $templates[$tplid];
			}
			else {
				$tpl = $templates[0];
			}
		}

		ob_clean();
		$t3v3 = T3v3::getSite($tpl);
		$layout = $t3v3->getLayout();
		$t3v3->loadLayout($layout);
		$lbuffer = ob_get_clean();
		die($lbuffer);
	}

	public static function save()
	{
		// Initialize some variables
		$input = JFactory::getApplication()->input;
		$template = $input->getCmd('template');
		$layout = $input->getCmd('layout');
		if (!$template || !$layout) {
			return self::error(JText::_('INVALID_DATA_TO_SAVE'));
		}
		
		$file = JPATH_ROOT . '/templates/' . $template . '/etc/layout/' . $layout . '.ini';
		if (JFile::exists($file)) {
			@chmod($file, 0777);
		}

		$params = new JRegistry();
		$params->loadObject($_POST);

		$data = $params->toString('INI');
		if (!@JFile::write($file, $data)) {
			return self::error(JText::_('OPERATION_FAILED'));
		}

		return self::response(array(
			'successful' => JText::sprintf('SAVE_PROFILE_SUCCESSFULLY', $layout),
			'layout' => $layout,
			'type' => 'new'
			));
	}

	public static function copy()
	{
		// Initialize some variables
		$input = JFactory::getApplication()->input;
		$template = $input->getCmd('template');
		$original = $input->getCmd('original');
		$clone = $input->getCmd('clone');

		//safe name
		$clone = JApplication::stringURLSafe($clone);

		if (!$template || !$original || !$clone) {
			return self::error(JText::_('T3V3_LAYOUT_INVALID_DATA_TO_SAVE'));
		}

		$path = JPATH_ROOT . '/templates/' . $template . '/tpls/';
		$source = $path . $original . '.php';
		$dest = $path . $clone . '.php';

		// Check if original file exists
		if (JFile::exists($source)) {
			// Check if the desired file already exists
			if (!JFile::exists($dest)) {
				if (!JFile::copy($source, $dest)) {
					return self::error(JText::_('T3V3_LAYOUT_OPERATION_FAILED'));
				} else {
					//clone configuration
					if(JFile::exists($path . $original . '.ini')){
						JFile::copy($path . $original . '.ini', $path . $clone . '.init');
					}
				}
			}
			else {
				return self::error(JText::_('T3V3_LAYOUT_EXISTED'));
			}
		}
		else {
			return self::error(JText::_('T3V3_LAYOUT_NOT_FOUND'));
		}

		return self::response(array(
			'successful' => JText::_('T3V3_LAYOUT_SAVE_SUCCESSFULLY'),
			'original' => $original,
			'clone' => $clone,
			'type' => 'clone'
			));
	}

	public static function delete(){
		// Initialize some variables
		$input = JFactory::getApplication()->input;
		$layout = $input->getCmd('layout');
		$template = $input->getCmd('template');

		if (!$layout) {
			return self::error(JText::_('T3V3_LAYOUT_UNKNOW_ACTION'));
		}

		$layoutfile = JPATH_ROOT . '/templates/' . $template . '/tpls/' . $layout . '.php';
		$initfile = JPATH_ROOT . '/templates/' . $template . '/etc/layout/' . $layout . '.ini';

		$return = false;
		if (!JFile::exists($layoutfile)) {
			return self::error(JText::sprintf('T3V3_LAYOUT_NOT_FOUND', $layout));
		}
		
		$return = @JFile::delete($layoutfile);
		
		if (!$return) {
			return self::error(JText::_('T3V3_LAYOUT_DELETE_FAIL'));
		} else {
			@JFile::delete($initfile);
			
			return self::response(array(
				'successful' => JText::_('T3V3_LAYOUT_DELETE_SUCCESSFULLY'),
				'layout' => $layout,
				'type' => 'delete'
			));
		}
	}
}
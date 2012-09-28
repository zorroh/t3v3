<?php
// Define constant

class T3v3Admin {
	
	/**
	 * function render
	 * render T3v3 administrator configuration form
	 *
	 * @return render success or not
	 */
	public function render(){
		$body = JResponse::getBody();
		
		$layout = T3V3_ADMIN_PATH . '/admin/tpls/default.php';
		if(file_exists($layout) && JRequest::getCmd('view') == 'style'){
			ob_start();
			$this->loadParams();
			$buffer = ob_get_clean();

			$body = preg_replace('@<form\s[^>]*name="adminForm"[^>]*>?.*?</form>@siu', $buffer, $body);
		}


		$body = $this->replaceToolbar($body);
		$body = $this->replaceDoctype($body);

		JResponse::setBody($body);
	}

	public function addScripts(){
		JFactory::getLanguage()->load(T3V3_PLUGIN, JPATH_ADMINISTRATOR);
		JFactory::getLanguage()->load(T3V3_TEMPLATE);

		$langs = array(
			'lblCompile' => JText::_('T3V3_LBL_RECOMPILE'),
			'lblThemer' => JText::_('T3V3_LBL_VIEWTHEMER'),
			'enableThemeMagic' => JText::_('T3V3_MSG_ENABLE_THEMEMAGIC')
		);

		$params = new JRegistry;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('params')
			->from('#__template_styles')
			->where('template='. $db->quote(T3V3_TEMPLATE));
		
		$db->setQuery($query);
		$params->loadString($db->loadResult());

		JFactory::getDocument()->addScriptDeclaration ( '
			var T3V3Admin = window.T3V3Admin || {};
			T3V3Admin.baseurl = \'' . JFactory::getURI()->toString() . '\';
			T3V3Admin.template = \'' . T3V3_TEMPLATE . '\';
			T3V3Admin.langs = ' . json_encode($langs) . ';
			T3V3Admin.devmode = ' . $params->get('devmode', 0) . ';
			T3V3Admin.themermode = ' . $params->get('themermode', 0) . ';
			T3V3Admin.themerUrl = \'' . JFactory::getURI()->toString() . '&t3action=theme&t3task=thememagic' . '\';' 
		);
	}
	
	/**
	 * function loadParam
	 * load and re-render parameters
	 *
	 * @return render success or not
	 */
	function loadParams(){
		$tplXml = T3V3_TEMPLATE_PATH . '/templateDetails.xml';
		$jtpl = T3V3_ADMIN_PATH . '/admin/tpls/default.php';
		
		if(file_exists($tplXml) && file_exists($jtpl)){
			
			//get the current joomla default instance
			$form = JForm::getInstance('com_templates.style', 'style', array('control' => 'jform', 'load_data' => true));
			
			//remove all fields from group 'params' and reload them again in right other base on template.xml
			$form->removeGroup('params');
			$form->loadFile(T3V3_PATH . '/params/' . 'template.xml');
			$form->loadFile(T3V3_TEMPLATE_PATH . DIRECTORY_SEPARATOR . 'templateDetails.xml', true, '//config');
			
			$xml = simplexml_load_file($tplXml);
			
			include $jtpl;
			
			//search for global parameters
			$japp = JFactory::getApplication();
			$pglobals = array();
			foreach($form->getGroup('params') as $param){
				if($form->getFieldAttribute($param->fieldname, 'global', 0, 'params')){
					$pglobals[] = array('name' => $param->fieldname, 'value' => $form->getValue($param->fieldname, 'params')); 
				}
			}
			$japp->setUserState('oparams', $pglobals);
			
			return true;
		}
		
		return false;
	}

	function replaceToolbar($body){
		$t3toolbar = T3V3_ADMIN_PATH . '/admin/tpls/toolbar.php';
		
		if(file_exists($t3toolbar) && class_exists('JToolBar')){
			//get the existing toolbar html
			jimport('joomla.language.help');
			$toolbar = JToolBar::getInstance('toolbar')->render('toolbar');
			$helpurl = JHelp::createURL(JRequest::getCmd('view') == 'template' ? 'JHELP_EXTENSIONS_TEMPLATE_MANAGER_TEMPLATES_EDIT' : 'JHELP_EXTENSIONS_TEMPLATE_MANAGER_STYLES_EDIT');
			$helpurl = htmlspecialchars($helpurl, ENT_QUOTES);
		
			//render our toolbar
			ob_start();
			include $t3toolbar;
			$t3toolbar = ob_get_clean();

			//replace it
			$body = str_replace($toolbar, $t3toolbar, $body);
		}

		return $body;
	}

	function replaceDoctype($body){
		return preg_replace('@<!DOCTYPE\s(.*?)>@', '<!DOCTYPE html>', $body);
	}
}

?>
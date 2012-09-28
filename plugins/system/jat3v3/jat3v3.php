<?php
/**
 * $JA#COPYRIGHT$
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Joomla! P3P Header Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.p3p
 */

class plgSystemJaT3v3 extends JPlugin
{
	//function onAfterInitialise(){
	function onAfterRoute(){
		include_once dirname(__FILE__) . '/includes/core/defines.php';
		$template = $this->detect();
		if($template){
			define ('T3V3_TEMPLATE', $template);
			define ('T3V3_TEMPLATE_URL', JURI::root(true).'/templates/'.T3V3_TEMPLATE);
			define ('T3V3_TEMPLATE_PATH', JPATH_ROOT . '/templates/' . T3V3_TEMPLATE);

			if(JRequest::getCmd('themer', 0)){
				define('T3V3_THEMER', 1);
			}
			
			include_once dirname(__FILE__) . '/includes/core/t3v3.php';
			
			if (!defined('T3V3')){
				throw new Exception(JText::_('T3V3_PLUGIN_PACKAGE_DAMAGED'));
			}
			
			// excute action by T3v3
			if ($action = JRequest::getCmd ('t3action')) {
				t3v3import ('core/action');
				T3V3Action::run ($action);
			}
		}
	}
	
	function onBeforeRender(){
		if($this->detect()){
			$japp = JFactory::getApplication();
			$jdoc = JFactory::getDocument();
			if($japp->isAdmin()){
				//plugins
				$jdoc->addStyleSheet(T3V3_ADMIN_URL . '/admin/assets/bootstrap/css/bootstrap.css');
				$jdoc->addStyleSheet(T3V3_ADMIN_URL . '/admin/assets/plugins/chosen/chosen.css');
				$jdoc->addStyleSheet(T3V3_ADMIN_URL . '/includes/depend/assets/css/jadepend.css');
				
				$jdoc->addStyleSheet(T3V3_ADMIN_URL . '/admin/assets/css/t3v3admin.css');

				$jdoc->addScript(T3V3_ADMIN_URL . '/admin/assets/js/jquery-1.8.0.min.js');
				$jdoc->addScript(T3V3_ADMIN_URL . '/admin/assets/bootstrap/js/bootstrap.js');
				$jdoc->addScriptDeclaration ( 'var $ja = jQuery.noConflict();' );

				$jdoc->addScript(T3V3_ADMIN_URL . '/includes/depend/assets/js/jadepend.js');

				$jdoc->addScript(T3V3_ADMIN_URL . '/admin/assets/plugins/chosen/chosen.jquery.min.js');
				$jdoc->addScript(T3V3_ADMIN_URL . '/admin/assets/js/t3v3admin.js');

				$t3v3app = T3v3::getApp();
				$t3v3app->addScripts();
			} else {
				$params = $japp->getTemplate(true)->params;
				if(defined('T3V3_THEMER') && $params->get('themermode', 0)){
					
					$jdoc->addScript(T3V3_URL.'/assets/js/thememagic.js');
					
					$theme = $params->get('theme');
					if($theme){
						$themepath = T3V3_TEMPLATE_PATH . '/assets/less/themes/' . $theme;

						if(file_exists($themepath . '/variables-custom.less')){
							if(!class_exists('JRegistryFormatLESS')){
								include_once T3V3_ADMIN_PATH . '/includes/format/less.php';
							}

							//default variables
							$varfile = T3V3_TEMPLATE_PATH . '/assets/less/variables.less';
							if(file_exists($varfile)){
								$themeinfo = new stdClass;
								$params = new JRegistry;
								$params->loadString(JFile::read($varfile), 'LESS');
								
								//get all less files in "theme" folder
								$others = JFolder::files($themepath, '.less');
								foreach($others as $other){
									//get those developer custom values
									if($other == 'variables.less'){
										$devparams = new JRegistry;
										$devparams->loadString(JFile::read($themepath . '/variables.less'), 'LESS');

										//overwrite the default variables
										foreach ($devparams->toArray() as $key => $value) {
											$params->set($key, $value);
										}								
									}

									//ok, we will import it later
									if($other != 'variables-custom.less' && $other != 'variables.less'){
										$themeinfo->$other = true;
									}
								}

								//load custom variables
								$cparams = new JRegistry;
								$cparams->loadString(JFile::read($themepath . '/variables-custom.less'), 'LESS');
								
								//and overwrite those defaults variables
								foreach ($cparams->toArray() as $key => $value) {
									$params->set($key, $value);
								}	
								
								$jdoc->addScriptDeclaration ( '
									var T3V3Theme = window.T3V3Theme || {};
									T3V3Theme.vars = ' . json_encode($params->toArray()) . ';
									T3V3Theme.others = ' . json_encode($themeinfo) . ';
									T3V3Theme.theme = \'' . $theme . '\';
									if(typeof less != \'undefined\'){
										less.refresh();
									}
								' );
							}
						}
					} else {
						$jdoc->addScriptDeclaration ( '
							var T3V3Theme = window.T3V3Theme || {};
							T3V3Theme.vars = [];
							T3V3Theme.others = [];
							T3V3Theme.theme = \'default\';
							if(typeof less != \'undefined\'){
								less.refresh();
							}
						' );
					}
				}
			}
		}
	}
	
	function onAfterRender ()
	{
		$japp = JFactory::getApplication();
		if($japp->isAdmin()){
			if($this->detect()){
				$t3v3app = T3v3::getApp();
				$t3v3app->render();
			}
		}
    }
	
	/**
     * Add JA Extended menu parameter in administrator
     *
     * @param   JForm   $form   The form to be altered.
     * @param   array   $data   The associated data for the form
     *
     * @return  null
     */
	function onContentPrepareForm($form, $data)
	{
		// extra option for menu item
		if ($form->getName() == 'com_menus.item') {
			$this->loadLanguage();
			JForm::addFormPath(T3V3_PATH . DIRECTORY_SEPARATOR . 'params');
			$form->loadFile('megaitem', false);
		} else if($this->detect() && $form->getName() == 'com_templates.style'){
			$this->loadLanguage();
			JForm::addFormPath(T3V3_PATH . DIRECTORY_SEPARATOR . 'params');
			$form->loadFile('template', false);
        }
	}
	
    function onExtensionAfterSave($option, $data){
        if($this->detect() && $option == 'com_templates.style' && !empty($data->id)){
			//get new params value
			$japp = JFactory::getApplication();
			$params = new JRegistry;
			$params->loadString($data->params);
			$oparams = $japp->getUserState('oparams');
						
			//check for changed params
			$pchanged = array();
			foreach($oparams as $oparam){
				if($params->get($oparam['name']) != $oparam['value']){
					$pchanged[] = $oparam;
				}
			}
			//if we have any changed, we will update to global
			if(count($pchanged)){
				$db = JFactory::getDBO();
				// update global change
				$sql = '`params`';
				foreach ($pchanged as $p) {
					$sql = 'replace('.$sql.','.$db->quote ('"'.$p['name'].'":"'.$p['value'].'"').','.$db->quote ('"'.$p['name'].'":"'.$params->get($p['name']).'"').')';
				}

				$query = $db->getQuery(true);
				$query
					->update('#__template_styles')
					->set('params =' . $sql)
					->where('`template`=' . $db->quote($data->template));
				$db->setQuery($query);
				$db->query();
			}
        }
    }
    
	function detect()
	{
		static $t3v3;

		if (!isset($t3v3)) {
			$t3v3 = false; // set false
			$app = JFactory::getApplication();
			// get template name
			$tplname = '';
			if ($app->isAdmin()) {
				if($tplname = JRequest::getCmd('t3template', '')){
		
				}
				else if(JRequest::getCmd('option') == 'com_templates' && 
					(preg_match('/style\./', JRequest::getCmd('task')) || JRequest::getCmd('view') == 'style' || JRequest::getCmd('view') == 'template')
				){
					$db       = JFactory::getDBO();
					$query    = $db->getQuery(true);
					$id  = JRequest::getInt('id');
					
					//when in POST the view parameter does not set
					
					if (JRequest::getCmd('view') == 'template') {						
						$query
							->select('element')
							->from('#__extensions')
							->where('extension_id='.(int)$id . ' AND type=' . $db->quote('template'));
					} else {
						$query
							->select('template')
							->from('#__template_styles')
							->where('id='.(int)$id);
					}
					
					$db->setQuery($query);
					$tplname = $db->loadResult();
				}

			} else {
				$tplname = $app->getTemplate(false);
			}

			if ($tplname) {				
				// parse xml
				$filePath = JPath::clean(JPATH_ROOT.'/templates/'.$tplname.'/templateDetails.xml');
				if (is_file ($filePath)) {
					$xml = JInstaller::parseXMLInstallFile($filePath);
					if (strtolower($xml['group']) == 'ja_t3v3') {
						$t3v3 = $tplname;
					}
				}
			}
		}
		return $t3v3;
	}
}

<?php
/**
 * $JA#COPYRIGHT$
 */

// no direct access
defined('_JEXEC') or die;



$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$ifstitle = $params->get('ifstitle');
$ifsbtntitle = $params->get('ifsbtntitle');
$ifsintro = $params->get('ifsintro');
$ifssuccess = $params->get('ifssuccess');

if(JRequest::getBool('jaifs_ajax')){
	ob_clean();  
    include "launch/submit.php";
    exit(); 
}
require (JModuleHelper::getLayoutPath('mod_jaifsform'));

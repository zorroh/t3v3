<?php
/**
 * $JA#COPYRIGHT$
 */

if (!defined('_JEXEC')) {
    // no direct access
 define('_JEXEC', 1);
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
    $path = dirname(dirname(dirname(dirname(__FILE__))));
    define('JPATH_BASE', $path);

    if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
        //Apache CGI
        $_SERVER['PHP_SELF'] = rtrim(dirname(dirname(dirname($_SERVER['PHP_SELF']))), '/\\');
    } else {
        //Others
        $_SERVER['SCRIPT_NAME'] = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/\\');
    }

	require_once (JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'defines.php');
    require_once (JPATH_BASE . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'framework.php');
    JDEBUG ? $_PROFILER->mark('afterLoad') : null;

    /**
     * CREATE THE APPLICATION
     *
     * NOTE :
     */
    $mainframe = JFactory::getApplication('administrator');

    /**
     * INITIALISE THE APPLICATION
     *
     * NOTE :
     */
    $mainframe->initialise(array('language' => $mainframe->getUserState("application.lang", 'lang')));

    JPluginHelper::importPlugin('system');

    // trigger the onAfterInitialise events
    JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
}

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

$user = JFactory::getUser();
$allowGroups = array('Super Users', 'Administrator', 'Manager');
$finded = false;

foreach ($user->groups as $group => $group_id) {
    if (in_array($group, $allowGroups) || in_array($group_id, $allowGroups)) {
        $finded = true;
        break;
    }
}

$task = isset($_REQUEST['japaramaction']) ? $_REQUEST['japaramaction'] : '';

if ($task != '') {
    JAAdminHelper::$task();
}

if (!$finded) {
    $result['error'] = JText::_('NO_PERMISSION');
    echo json_encode($result);
    exit();
}
/**
 *
 * Admin helper module class
 * @author JoomlArt
 *
 */
class JAAdminHelper
{
    /**
     *
     * save Profile
     */
    public static function saveProfile()
    {
        $mainframe = JFactory::getApplication();
        // Initialize some variables
		
        $client =  JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
        
        $post = $_POST;
        $profile = JRequest::getCmd('profile');

        $result = array();
        if (!$profile) {
            $result['error'] = JText::_('INVALID_DATA_TO_SAVE_PROFILE');
            echo json_encode($result);
            exit();
        }

        $errors = array();

        $file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $profile . '.ini';

        $params = new JRegistry();
        if (isset($post)) {
            foreach ($post as $k => $v) {
                $params->set($k, $v);
            }
        }
        $data = (string) $params;
        if (JFile::exists($file)) {
            @chmod($file, 0777);
        }
        $return = @JFile::write($file, $data);

        if (!$return) {
            $errors[] = JText::_('OPERATION_FAILED');
        }

        if ($errors) {
            $result['error'] = implode('<br/>', $errors);
        } else {
            $result['successful'] = sprintf(JText::_('SAVE_PROFILE_SUCCESSFULLY'), $profile);
            $result['profile'] = $profile;
            $result['type'] = 'new';
        }

        echo json_encode($result);
        exit();
    }

    /**
     *
     * Clone Profile
     */
    function cloneProfile()
    {
        $profile = JRequest::getCmd('profile');
        $fromprofile = JRequest::getCmd('fromprofile');
        $template = JRequest::getCmd('template');
        $result = array();
        if (!$profile || !$fromprofile) {
            $result['error'] = JText::_('INVALID_DATA_TO_SAVE_PROFILE');
            echo json_encode($result);
            exit();
        }

        $path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'profiles';
        $source = $path . DIRECTORY_SEPARATOR . $fromprofile . '.ini';
        if (!JFile::exists($source) && $template) {
            $source = JPATH_SITE . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'mod_janewspro' . DIRECTORY_SEPARATOR . $fromprofile . '.ini';
        }
        $dest = $path . DIRECTORY_SEPARATOR . $profile . '.ini';
        if (JFile::exists($dest)) {
            $result['error'] = sprintf(JText::_('PROFILE_EXIST'), $profile);
            echo json_encode($result);
            exit();
        }

        $result = array();
        if (JFile::exists($source)) {
            if ($error = @JFile::copy($source, $dest) == true) {
                $result['successful'] = JText::_('CLONE_PROFILE_SUCCESSFULLY');
                $result['profile'] = $profile;
                $result['reset'] = true;
                $result['type'] = 'clone';
            } else {
                $result['error'] = $error;
            }
        } else {
            $result['error'] = JText::_(sprintf('PROFILE_NOT_FOUND', $fromprofile));
        }
        echo json_encode($result);
        exit();
    }

    /**
     *
     * Delete a profile
     */
    function deleteProfile()
    {
        // Initialize some variables

        $client = JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
        $template = JRequest::getCmd('template');
        $profile = JRequest::getCmd('profile');
        $errors = array();
        $result = array();
        if (!$profile) {
            $result['error'] = JText::_('NO_PROFILE_SPECIFIED');
            echo json_encode($result);
            exit();
        }

        $file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR . $profile . '.ini';
        $return = false;
        if (JFile::exists($file)) {
            $return = @JFile::delete($file);
        }
        if (!$return) {
            $result['error'] = sprintf(JText::_('DELETE_FAIL'), $file);
        } else {
            $source = JPATH_SITE . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'mod_janewspro' . DIRECTORY_SEPARATOR . $profile . '.ini';

            if ($template && JFile::exists($source)) {
                $result['template'] = '1';
                $result['successful'] = sprintf(JText::_('DELETE_PROFILE_SUCCESSFULLY_FROM_MODULE'), $profile);
            } else {
                $result['template'] = '0';
                $result['successful'] = sprintf(JText::_('DELETE_PROFILE_SUCCESSFULLY'), $profile);
            }
            $result['profile'] = $profile;
            $result['type'] = 'delete';
        }

        echo json_encode($result);
        exit();
    }
}
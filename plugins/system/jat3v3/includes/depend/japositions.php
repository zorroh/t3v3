<?php
/**
 * $JA#COPYRIGHT$
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Radio List Element
 *
 * @package  JAT3.Core.Element
 */
class JFormFieldJaPositions extends JFormField
{
    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    protected $type = 'JaPositions';

	/**
	 * Check and load assets file if needed
	 */
	function loadAsset(){
		if (!defined ('_JA_DEPEND_ASSET_')) {
			define ('_JA_DEPEND_ASSET_', 1);
			$uri = str_replace(DS, '/', str_replace( JPATH_SITE, JURI::base(), dirname(__FILE__) ));
			$uri = str_replace('/administrator/', '', $uri);
			
			if(!defined('T3V3')){
                $jdoc = JFactory::getDocument();
                $jdoc->addStyleSheet($uri.'/assets/css/jadepend.css');
                $jdoc->addScript($uri.'/assets/js/jadepend.js');    
            }
		}
	}
	
    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     */
    function getInput()
    {
        $this->loadAsset();

        $db = JFactory::getDBO();
        $query = "SELECT DISTINCT position FROM #__modules ORDER BY position ASC";
        $db->setQuery($query);
        $groups = $db->loadObjectList();

        $groupHTML = array();
        if ($groups && count($groups)) {
            foreach ($groups as $v=>$t) {
                $groupHTML[] = JHTML::_('select.option', $t->position, $t->position);
            }
        }
        $lists = JHTML::_('select.genericlist', $groupHTML, $this->name . ($this->element['multiple'] == 1 ? '[]' : ''), ($this->element['multiple'] == 1 ? 'multiple="multiple" size="10" ' : ''), 'value', 'text', $this->value);
        
        return $lists;
    }
}
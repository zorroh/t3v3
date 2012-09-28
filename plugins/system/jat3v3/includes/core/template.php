<?php
/**
 * $JA#COPYRIGHT$
 */

// No direct access
defined('_JEXEC') or die();

/**
 * T3V3Template class provides extended template tools used for T3v3 framework
 *
 * @package T3V3
 */
class T3v3Template extends ObjectExtendable
{
	/**
	 * Current template instance
	 *
	 * @var    string
	 */
	public $_tpl = null;

	/**
	 * Current template instance
	 *
	 * @var    string
	 */
	protected $_scripts = null;
	/**
	 * Class constructor
	 *
	 * @param   object  $template Current template instance
	 */
	public function __construct($template = null)
	{
		if ($template) {
			$this->_tpl = $template;
			$this->_extend(array($template));
		}

		$this->_scripts = array();
	}
  
	/**
	* Load block content
	*
	* @param $block string
	*     Block name - the real block is tpls/blocks/[blockname].php
	*
	* @return string Block content
	*/
	function loadBlock($block, $data = '')
	{
		$bf = 'tpls/blocks/'.$block.'.php';
		$path = '';
		if (is_file (T3V3_TEMPLATE_PATH . '/' . $bf)) {
			$path = T3V3_TEMPLATE_PATH . '/' . $bf;
		} else if (is_file (T3V3_PATH . '/' . $bf)) {
			$path = T3V3_PATH . '/' . $bf;
		}

		if ($path) {
			include $path;
		} else {
			echo "<div class=\"error\">Block [$block] not found!</div>";
		}
	} 

	/**
	* Load block content
	*
	* @param $block string
	*     Block name - the real block is tpls/blocks/[blockname].php
	*
	* @return string Block content
	*/
	function loadLayout($layout)
	{
		$path = T3V3_TEMPLATE_PATH . '/tpls/'.$layout.'.php';
		if (!is_file ($path)) {
			$path = T3V3_TEMPLATE_PATH . '/tpls/default.php';
		}

		if (is_file ($path)) {
			include $path;
		} else {
			echo "<div class=\"error\">Layout [$layout] or [Default] not found!</div>";
		}
	} 

	/**
	* Get data property for layout - responsive layout
	*
	* @param $layout object
	*     Layout configuration
	* @param $col int
	*     Column number, start from 0
	*
	* @return string Block content
	*/
	function getData ($layout, $col) {
		$data = '';
		foreach ($layout as $device => $width) {
			if (!isset ($width[$col]) || !$width[$col]) continue;
			$data .= " data-$device=\"{$width[$col]}\"";
		}
		return $data;
	}

	/**
	* Get layout column class
	*
	* @param $layout object
	*     Layout configuration
	* @param $col int
	*     Column number, start from 0
	*
	* @return string Block content
	*/
	function getClass ($layout, $col) {
		$width = $layout->default;
		if (!isset ($width[$col]) || !$width[$col]) return "";
		return $width[$col];
	}

	/**
	* Get position name
	*
	* @param $poskey string
	*     the key used in block
	*/
	function getPosname ($condition) {
		$operators = '(,|\+|\-|\*|\/|==|\!=|\<\>|\<|\>|\<=|\>=|and|or|xor)';
		$words = preg_split('# ' . $operators . ' #', $condition, null, PREG_SPLIT_DELIM_CAPTURE);
		for ($i = 0, $n = count($words); $i < $n; $i += 2)
		{
			// odd parts (modules)
			$name = strtolower($words[$i]);
			$words[$i] = $this->params->get ('pos_'.$name, $name);;
		}

		$poss = implode(' ', $words);
		return $poss;
	}

	/**
	* echo position name
	*
	* @param $poskey string
	*     the key used in block
	*/
	function posname ($condition) {
		echo $this->getPosname ($condition);
	}

	/**
	* Add current template css base on template setting. 
	*
	* @param $name String
	*     file name, without .css
	*
	* @return string Block content
	*/
	function addStyleSheet ($name) {
		$devmode = $this->params->get('devmode', 0);
		$themermode = $this->params->get('themermode', 0);
		$theme = $this->params->get('theme', '');
		$doc = JFactory::getDocument();
		$href = '';
		
		if ($devmode || ($themermode && defined ('T3V3_THEMER')) && is_file (T3V3_TEMPLATE_PATH.'/assets/less/'.$name.'.less')) {
	    	t3v3import ('core/less');
	    	T3V3Less::addStylesheet ('templates/'.T3V3_TEMPLATE.'/assets/less/'.$name.'.less');
		} else {
			if ($theme && is_file (T3V3_TEMPLATE_PATH."/assets/css/themes/{$theme}/{$name}.css")) {
				$href = T3V3_TEMPLATE_URL."/assets/css/themes/{$theme}/{$name}.css";
			} else if(is_file(T3V3_TEMPLATE_PATH."/assets/css/{$name}.css")){
				$href = T3V3_TEMPLATE_URL."/assets/css/{$name}.css";
			} else {
				$href = T3V3_URL."/assets/css/{$name}.css";
			}
			// Add this css into template
			$doc->addStyleSheet($href);
		}
	}

  /**
   * Add T3v3 basic head 
   */
  function addHead () {
    $doc = JFactory::getDocument();
    $responsive = $this->params->get('responsive', 1);

    // BOOTSTRAP CSS
    $this->addStyleSheet ('bootstrap'); 

    if ($responsive) {
      // RESPONSIVE CSS
      $this->addStyleSheet ('bootstrap-responsive'); 
    }

    // TEMPLATE CSS -->
    $this->addStyleSheet ('template'); 

    if ($responsive) {
      // RESPONSIVE CSS
      $this->addStyleSheet ('template-responsive'); 
    }

    // Add scripts
    $doc->addScript (T3V3_URL.'/bootstrap/js/jquery.js');
    $doc->addScript (T3V3_URL.'/bootstrap/js/bootstrap.js');
    $doc->addScript (T3V3_URL.'/assets/js/script.js');
    
    if ($responsive) {
      $doc->addScript (T3V3_URL.'/assets/js/responsive.js');
    }
  }

  function paramToStyle($style, $paramname = '', $isurl = false){
  	if($paramname == ''){
  		$paramname = $style;
  	}
  	$param = $this->params->get($paramname);
  	
  	if (!$param) return '';

  	if ($isurl) {
  		return "$style:url($param);";
  	} else {
  		return "$style:$param".(is_numeric($param) ? 'px;':';');
  	}
  }

}
?>
<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div id="t3v3toolbar" class="btn-toolbar">

	<?php
	if(JRequest::getCmd('view') == 'style'):
	?>
  <div id="jatoolbar-save" class="btn-group">
    <button id="jatoolbar-style-save-save" class="btn btn-primary"><i class="icon-save"></i>Save</button>
    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
      <span class="caret"></span>&nbsp;
    </button>
    <ul class="dropdown-menu">
      <li id="jatoolbar-style-save-close"><a href="#"><?php echo JText::_('T3V3_BE_TOOLBAR_SAVECLOSE') ?></a></li>
      <li id="jatoolbar-style-save-clone"><a href="#"><?php echo JText::_('T3V3_BE_TOOLBAR_SAVE_AS_CLONE') ?></a></li>
    </ul>
  </div>
  <?php 
  endif;
  ?>

	<div id="jatoolbar-recompile" class="btn-group">
		<button class="btn"><i class="icon-check"></i><?php echo JText::_('T3V3_BE_TOOLBAR_COMPILE_LESS_CSS') ?></button>
	</div>

	<div id="jatoolbar-themer" class="btn-group">
		<button class="btn"><i class="icon-magic"></i><?php echo JText::_('T3V3_BE_TOOLBAR_THEMER') ?></button>
	</div>

	<div id="jatoolbar-close" class="btn-group <?php echo JRequest::getCmd('view') ?>">
		<button class="btn"><i class="icon-remove"></i><?php echo JText::_('T3V3_BE_TOOLBAR_CLOSE') ?></button>
	</div>
	<div id="jatoolbar-help" class="btn-group <?php echo JRequest::getCmd('view') ?>" onclick="Joomla.popupWindow('<?php echo $helpurl; ?>', 'Help', 700, 500, 1);">
		<button class="btn"><i class="icon-question-sign"></i><?php echo JText::_('T3V3_BE_TOOLBAR_HELP') ?></button>
	</div>

</div>
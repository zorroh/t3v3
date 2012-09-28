<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$user = JFactory::getUser();
$canDo = TemplatesHelper::getActions();
$iswritable = is_writable('jat3test.txt');

?>
<?php if($iswritable): ?>
<div id="writable-message" class="alert warning">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<strong><?php echo JText::_('T3V3_MSG_WARNING'); ?></strong> <?php echo JText::_('T3V3_MSG_FILE_NOT_WRITABLE'); ?>
</div>
<?php endif;?>
<div class="form-t3admin clearfix">
<form action="<?php echo JRoute::_('index.php?option=com_templates&layout=edit&id='.JRequest::getInt('id')); ?>" method="post" name="adminForm" id="style-form" class="form-validate form-horizontal">
	<div class="header clearfix">
		<div class="controls-row">
			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('title'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('title'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('template'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('template'); ?>
				</div>
			</div>
			<div class="control-group hide">
				<div class="control-label">
					<?php echo $form->getLabel('client_id'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('client_id'); ?>
					<input type="text" size="35" value="<?php echo $form->getValue('client_id') == 0 ? JText::_('JSITE') : JText::_('JADMINISTRATOR'); ?>	" class="inputbox readonly" readonly="readonly" />
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $form->getLabel('home'); ?>
				</div>
				<div class="controls">
					<?php echo $form->getInput('home'); ?>
				</div>
			</div>
		</div>
	</div>
	<fieldset>
    <div class="nav-t3admin clearfix">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('T3V3_PARAMS_OVERVIEW');?></a></li>
			<?php
			$fieldSets = $form->getFieldsets('params');
			foreach ($fieldSets as $name => $fieldSet) :
				$label = !empty($fieldSet->label) ? $fieldSet->label : 'COM_TEMPLATES_'.$name.'_FIELDSET_LABEL';
			?>
				<li><a href="#<?php echo preg_replace( '/\s+/', ' ', $name);?>" data-toggle="tab"><?php echo JText::_($label) ?></a></li>
			<?php
			endforeach;
			?>
			<?php if ($user->authorise('core.edit', 'com_menu') && ($form->getValue('client_id') == 0)):?>
				<?php if ($canDo->get('core.edit.state')) : ?>
						<li><a href="#assignment" data-toggle="tab"><?php echo JText::_('T3V3_PARAMS_MENUS_ASSIGNMENT');?></a></li>
				<?php endif; ?>
			<?php endif;?>
		</ul>

		<div class="tab-content clearfix">
			<div class="tab-pane tab-overview active clearfix" id="details">
				<?php include T3V3_ADMIN_PATH . '/admin/tpls/default_overview.php'; ?>
			</div>
			<?php
			foreach ($fieldSets as $name => $fieldSet) :
				if (isset($fieldSet->description) && trim($fieldSet->description)) :
					echo '<p class="tip">'.(JText::_($fieldSet->description)).'</p>';
				endif;
				?>
				<div class="tab-pane" id="<?php echo preg_replace( '/\s+/', ' ', $name); ?>">
					<?php foreach ($form->getFieldset($name) as $field) :
					$hide = ($field->type === 'JaDepend' && $form->getFieldAttribute($field->fieldname, 'function', '', $field->group) == '@group');
					if ($field->type == 'Text') {
						// add placeholder to Text input
						$textinput = str_replace ('/>', ' placeholder="'.$form->getFieldAttribute($field->fieldname, 'default', '', $field->group).'"/>', $field->input);
					}
					?>
					<?php if ($field->hidden || ($field->type == 'JaDepend' && !$field->label)) : ?>
						<?php echo $field->input; ?>
					<?php else : ?>
					<div class="control-group<?php echo $hide ? ' hide' : ''?>">
						<div class="control-label">
							<?php echo $field->label; ?>
						</div>
						<div class="controls">
							<?php echo $field->type=='Text'?$textinput:$field->input ?>
						</div>
					</div>
					<?php endif; ?>
				<?php endforeach; ?>
				</div>
			<?php endforeach;  ?>

			<?php if ($user->authorise('core.edit', 'com_menu') && $form->getValue('client_id') == 0):?>
				<?php if ($canDo->get('core.edit.state')) : ?>
					<div class="tab-pane clearfix" id="assignment">
						<?php include T3V3_ADMIN_PATH . '/admin/tpls/default_assignment.php'; ?>
					</div>
				<?php endif; ?>
			<?php endif;?>
		</div>
  </div>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>
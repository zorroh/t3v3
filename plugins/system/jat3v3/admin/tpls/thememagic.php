<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo JText::_('T3V3_THEMEMAGIC_TITLE'); ?></title>
		<link type="text/css" rel="stylesheet" href="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/bootstrap/css/bootstrap.css" />
		<link type="text/css" rel="stylesheet" href="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/colorpicker/jquery.colorpicker.css" />
		<!--link type="text/css" rel="stylesheet" href="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/chosen/chosen.css" /-->
		<link type="text/css" rel="stylesheet" href="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/css/thememagic.css" />

		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/js/jquery-1.8.0.js"></script>
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/jquery-ui/js/jquery-ui-1.8.23.custom.min.js"></script>
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/bootstrap/js/bootstrap.js"></script>
	</head>

	<body<?php echo $tplparams->get('themermode', 0) == 0 ? ' class="nomagic"' : ''?>>
		<div id="wrapper">
			<?php if($tplparams->get('themermode', 0)): ?>
			<div id="thememagic">
				<a href="<?php echo JURI::base(true); ?>" class="themer-minimize"><i class="icon-remove-sign"></i><i class="icon-magic"></i>  <span><?php echo JText::_('T3V3_THEMEMAGIC_MINIMIZE') ; ?></span></a>
				<a href="<?php echo $backurl; ?>" class="themer-close" title="<?php echo JText::_('T3V3_THEMEMAGIC_CLOSE'); ?>"><i class="icon-arrow-left"></i><?php echo JText::_($isadmin ? 'T3V3_THEMEMAGIC_BACK_TO_ADMIN' : 'T3V3_THEMEMAGIC_EXIT'); ?></a>

				<div class="header">
				  <h2><strong><?php echo JText::_('T3V3_THEMEMAGIC_CUSTOMIZING'); ?></strong> <span><?php echo $tplparams->get('sitename'); ?></span></h2>
				  <form id="ja-theme-form" name="ja-theme-form" class="form-validate form-inline">
					<div class="controls controls-row">
						<label for="ja-theme-list"><?php echo JText::_('T3V3_THEMEMAGIC_THEME_LABEL'); ?></label>
					  <?php
						echo JHTML::_('select.genericlist', $themes, 'ja-theme-list', 'autocomplete="off"', 'id', 'title', $tplparams->get('theme', -1));
					  ?>
					  <?php if( $isadmin) : ?>
					  <div class="btn-group">
						<button class="btn btn-primary" type="submit" id="ja-theme-submit" name="ja-theme-save"><?php echo JText::_('T3V3_THEMEMAGIC_SAVE') ?></button>
						<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
						<ul class="dropdown-menu">
						  <li><a id="ja-theme-saveas" href="#" title="Save As"><?php echo JText::_('T3V3_THEMEMAGIC_SAVEAS') ?></a></li>
						  <li><a id="ja-theme-delete" href="#" title="Delete"><?php echo JText::_('T3V3_THEMEMAGIC_DELETE') ?></a></li>
						</ul>
					  </div>
					  <?php endif; ?>
					</div>
				  </form>
				</div>
	
				<form id="ja-variable-form" name="adminForm" class="form-validate">
					<div id="recss-progress" class="progress progress-striped active fade invisible">
						<div class="bar"></div>
					</div>

					<div class="accordion" id="jaccord">
						<?php
						$i = 0;
						foreach ($fieldSets as $name => $fieldSet) :
							$label = !empty($fieldSet->label) ? $fieldSet->label : 'T3T3'.$name.'_FIELDSET_LABEL';
						?>
							
						<div class="accordion-group<?php echo $i == 0?' active':'' ?>">
							<div class="accordion-heading">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#jaccord" href="#<?php echo preg_replace( '/\s+/', ' ', $name);?>"><?php echo JText::_($label) ?></a>
							</div>
							<div id="<?php echo preg_replace( '/\s+/', ' ', $name);?>" class="accordion-body collapse<?php echo (($i == 0)? ' in' : ''); ?>">
								<div class="accordion-inner">
								
									<?php foreach ($form->getFieldset($name) as $field) :
										$hide = ($field->type === 'JaDepend' && $form->getFieldAttribute($field->fieldname, 'function', '', $field->group) == '@group');
										// add placeholder to Text input
										if ($field->type == 'Text') {
											$textinput = str_replace ('/>', ' placeholder="' . $form->getFieldAttribute($field->fieldname, 'default', '', $field->group).'"/>', $field->input);
										}
									?>
										<div class="control-group<?php echo $hide ? ' hide' : ''?>">
										<?php if (!$field->hidden) : ?>
											<div class="control-label">
												<?php echo preg_replace('/(\s*)for="(.*?)"(\s*)/i', ' ', $field->label); ?>
											</div>
										<?php endif; ?>
											<div class="controls">
												<?php echo $field->type == 'Text'? $textinput : str_replace('value="#000000"', 'value=""', $field->input); ?>
											</div>
										</div>
									<?php
									endforeach;
									?>
								</div>
							</div>
						</div>

					<?php
					$i++;
						endforeach;
					?>
				</div>
			</form>
			</div>
			<?php else :?>
			
			<div id="themer-warning" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h3><?php echo JText::_('T3V3_THEMEMAGIC_TITLE'); ?></h3>
				</div>
				<div class="modal-body">
					<p><?php echo JText::_('T3V3_MSG_ENABLE_THEMEMAGIC'); ?></p>
				</div>
				<div class="modal-footer">
					<a href="#" class="btn btn-primary" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('T3V3_LBL_OK') ?></a>
				</div>
			</div>

			<?php endif;?>
			<div id="preview">
				<iframe id="ifr-preview" frameborder="0" src="<?php echo $url . ($tplparams->get('theme', -1) != -1 ? ('&t3style=' . $tplparams->get('theme')) : '') ?>"></iframe>
			</div>

		</div>

		<?php if($tplparams->get('themermode', 0)): ?>
		<div id="thememagic-dlg" class="modal hide fade">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3><?php echo JText::_('T3V3_THEMEMAGIC_TITLE') ?></h3>
			</div>
			<div class="modal-body">
				<form id="prompt-form" name="prompt-form" class="form-horizontal prompt-block">
					<span class="help-block"><?php echo JText::_('T3V3_THEME_ASK_ADD_THEME') ?></span>
					<div class="control-group">
						<label class="control-label" for="theme-name">Name</label>
						<div class="controls">
							<input type="text" id="theme-name" placeholder="Theme name">
						</div>
					</div>
				</form>
				<div class="message-block">
					<p></p>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn cancel" data-dismiss="modal" aria-hidden="true"></a>
				<a href="#" class="btn btn-primary"></a>
			</div>
		</div>
		
		
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/js/json2.js"></script>
		<!--script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/chosen/chosen.jquery.js"></script-->
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/colorpicker/jquery.colorpicker.js"></script>
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/includes/depend/assets/js/jadepend.js"></script>
		<script type="text/javascript" src="<?php echo T3V3_ADMIN_URL; ?>/admin/assets/js/thememagic.js"></script>

		<script type="text/javascript">
			// add class active for open 
			$('#jaccord .accordion-group').on('hide', function () {
				$(this).removeClass('active');
			}).on('show', function() {
				$(this).addClass('active');
			});
			
			var T3V3Theme = window.T3V3Theme || {};
			T3V3Theme.admin = <?php echo intval($isadmin); ?>;
			T3V3Theme.data = <?php echo json_encode($jsondata); ?>;
			T3V3Theme.themes = <?php echo json_encode($themes); ?>;
			T3V3Theme.template = '<?php echo T3V3_TEMPLATE; ?>';
			T3V3Theme.url = '<?php echo JURI::root(true) . '/administrator/index.php'; ?>';
			T3V3Theme.langs = <?php echo json_encode($langs); ?>;
			T3V3Theme.active = '<?php echo $tplparams->get('theme', 'default')?>';
			T3V3Theme.variables = <?php echo ($tplparams->get('theme', -1) == -1 ? '{}' : 'T3V3Theme.data[T3V3Theme.active]') ?>;
			T3V3Theme.colorimgurl = '<?php echo T3V3_ADMIN_URL; ?>/admin/assets/plugins/colorpicker/images/ui-colorpicker.png';
		</script>
		<?php else :?>
			<script type="text/javascript">
				$(document).ready(function(){
					$('#themer-warning').modal('show')
				});
			</script>
		<?php endif;?>
	</body>
</html>

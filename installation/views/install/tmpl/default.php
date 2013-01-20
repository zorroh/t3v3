<?php
/**
 * @package    Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$tasks = array();
$tasks[] = ($this->options['db_old'] == 'remove') ? "database_remove" : "database_backup";
$tasks[] = "database";
if ($this->options['sample_file']) {
	$tasks[] = "sample";
}
$tasks[] = "config";
if ($this->options['summary_email']) {
	$tasks[] = "email";
}
?>
<form action="index.php" method="post" id="adminForm" class="form-validate form-horizontal x">
	<h3><?php echo JText::_('INSTL_INSTALLING'); ?></h3>
	<hr class="hr-condensed" />

	<div class="progress progress-striped active" id="install_progress">
		<div class="bar" style="width: 0%;"></div>
	</div>

	<table class="table">
		<tbody>
		<?php foreach($tasks as $task) : ?>
		<tr id="install_<?php echo $task; ?>">
			<td class="item" nowrap="nowrap" width="10%">
				<?php
					if ($task == 'email') {
						echo JText::sprintf('INSTL_INSTALLING_EMAIL', '<span class="label">'.$this->options['admin_email'].'</span>');
					} else {
						echo JText::_('INSTL_INSTALLING_'.strtoupper($task));
					}
				?>
			</td>
			<td>
				<div class="spinner spinner-img" style="visibility:hidden;"></div>
			</td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="2"></td>
		</tr>
		</tfoot>
	</table>
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">
	window.addEvent('domready', function() {
		doInstall();
	});
	function doInstall()
	{
		if(document.id('install_progress') != null) {
			Install.install(['<?php echo implode("','", $tasks); ?>']);
		} else {
			(function(){doInstall();}).delay(500);
		}
	}
</script>

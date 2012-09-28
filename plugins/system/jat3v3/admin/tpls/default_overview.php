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

<div class="t3-overview">

	<div class="row-fluid">

		<div class="span8">
			<?php if (is_file (T3V3_TEMPLATE_PATH.'/templateInfo.php')): ?>
			<div class="template-info row-fluid">
				<?php include T3V3_TEMPLATE_PATH.'/templateInfo.php' ?>
			</div>
			<?php endif ?>
		</div>


		<div class="span4">
			<div class="t3-resource">
				<h3>Resource</h3>
				<p>Tincidunt at dolor eu fringilla wisi tincidunt ante sit ut Quisque. Et id est turpis lorem ut malesuada gravida in In tempor. Molestie dui dolor Vivamus metus hendrerit Proin Vestibulum lacinia ligula interdum.</p>
				<ul>
					<li><a href="#" title="">T3 V3.0 Framework demo</a></li>
					<li><a href="#" title="">T3 V3.0 Framework demo forum</a></li>
					<li><a href="#" title="">T3 V3.0 Framework demo updates</a></li>
					<li><a href="#" title="">T3 V3.0 Framework demo JIRA</a></li>
				</ul>
			</div>

			<div class="t3-updater">
				Current version 1.0.0. Click here to download the <a href="#" title="Latest version">latest version</a> now.
			</div>
		</div>


	</div>
</div>
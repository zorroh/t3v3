<?php
	t3v3import('admin/layout');
?>

<!-- LAYOUT CONFIGURATION PANEL -->
<div id="t3-layout-admin" class="t3-layout-admin hide">
	<div class="t3-inline-nav clearfix">
		<div class="t3-row-mode clearfix">
			<ul class="nav nav-tabs t3-layout-modes">
				<li class="active mode-structure"><a href="#" title="Module Positions">Module Positions</a></li>
				<li class="mode-layout"><a href="#" title="Responsive Layout">Responsive Layout</a></li>
			</ul>
			<button class="btn t3-reset-all pull-right"><i class="icon-undo"></i>Reset All</button>
		</div>
		<div class="t3-row-device clearfix">
			<div class="btn-group t3-layout-devices hide">
				<button class="btn t3-dv-wide" data-device="wide" title="Wide"><i class="icon-desktop"></i>Wide</button>
				<button class="btn t3-dv-normal" data-device="normal" title="Normal"><i class="icon-laptop"></i>Normal</button>
				<button class="btn t3-dv-xtablet" data-device="xtablet" title="XTablet"><i class="icon-laptop"></i>XTablet</button>
				<button class="btn t3-dv-tablet" data-device="tablet" title="Tablet"><i class="icon-tablet"></i>Tablet</button>
				<button class="btn t3-dv-mobile" data-device="mobile" title="Mobile"><i class="icon-mobile-phone"></i>Mobile</button>
			</div>
			<button class="btn t3-reset-device pull-right hide">Reset layout for current device</button>
			<button class="btn t3-reset-position pull-right">Reset Positions</button>
      <button class="t3-tog-fullscreen" title="Toggle Fullscreen"><i class="icon-resize-full"></i></button>
		</div>
	</div>
	<div id="t3-layout-cont" class="t3-layout-cont layout-custom t3-layout-mode-m"></div>
</div>

<!-- POPOVER POSITIONS -->
<div id="t3-layout-tpl-positions" class="popover right hide" tabindex="-1">
	<div class="arrow"></div>
	<h3 class="popover-title">Select a position</h3>
	<div class="popover-content">
		<?php echo T3v3AdminLayout::getTplPositions() ?>

		<button class="btn btn-small t3-chzn-empty"><i class="icon-remove"></i>None</button>
		<button class="btn btn-small btn-success t3-chzn-default"><i class="icon-ok-circle"></i>Default</button>
	</div>
</div>

<!-- CLONE BUTTONS -->
<div id="t3-layout-clone-btns">
	<button id="t3-layout-clone-copy" class="btn btn-success"><i class="icon-save"></i>Save as Copy</button>
	<button id="t3-layout-clone-delete" class="btn"><i class="icon-remove"></i>Delete</button>
</div>

<!-- MODAL CLONE LAYOUT -->
<div id="t3-layout-clone-dlg" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3>Please enter new layout name</h3>
	</div>
	<div class="modal-body">
		<form class="form-horizontal prompt-block">
			<div class="control-group">
				<label class="control-label" for="t3-layout-cloned-name">Layout name</label>
				<div class="controls"><input type="text" id="t3-layout-cloned-name" /></div>
			</div>
		</form>
		<div class="message-block"><p></p></div>
	</div>
	<div class="modal-footer">
		<a href="" class="btn cancel" data-dismiss="modal">Cancel</a>
		<a href="" class="btn btn-primary" id="t3-layout-clone-btn">Ok</a>
	</div>
</div>
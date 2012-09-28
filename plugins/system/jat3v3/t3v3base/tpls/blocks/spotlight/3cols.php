<?php
/**
 * $JA#COPYRIGHT$
 */

// No direct access
defined('_JEXEC') or die;
?>
<?php

	// width configuration
	$layout = json_decode('{
      "default" : [ "span4"   , "span4"     , "span4"  ],
      "wide"  	: [],
      "xtablet" : [],
      "tablet"  : []
    }');

	$style = 'JAxhtml';
  
  $pos = preg_split('/\s*,\s*/', $data);
  // check if there's any modules
  if (!$this->countModules (implode (' or ', $pos))) return;
  // check if number of module positions less than the blocks
  if (count($pos) < 3) return;

	$col = 0;
?>

<!-- SPOTLIGHT -->
<div class="row">
  <div class="<?php echo $this->getClass($layout, $col) ?>" <?php echo $this->getData ($layout, $col++) ?>>
      <jdoc:include type="modules" name="<?php echo $pos[0] ?>" style="<?php echo $style ?>" />
  </div>
  <div class="<?php echo $this->getClass($layout, $col) ?>" <?php echo $this->getData ($layout, $col++) ?>>
      <jdoc:include type="modules" name="<?php echo $pos[1] ?>" style="<?php echo $style ?>" />
  </div>
  <div class="<?php echo $this->getClass($layout, $col) ?>" <?php echo $this->getData ($layout, $col++) ?>>
      <jdoc:include type="modules" name="<?php echo $pos[2] ?>" style="<?php echo $style ?>" />
  </div>
</div>
<!-- SPOTLIGHT -->
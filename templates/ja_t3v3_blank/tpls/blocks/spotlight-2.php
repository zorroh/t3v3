<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>


<?php if ($this->countModules($this->getPosname('position-5 or position-6 or position-7 or position-8'))) : ?>
<!-- SPOTLIGHT 2 -->
<section class="container ja-sl ja-sl-2">
  <?php 
    $this->loadBlock ('spotlight/4cols', $this->getPosname('position-5, position-6, position-7, position-8'))
  ?>
</section>
<!-- //SPOTLIGHT 2 -->
<?php endif ?>
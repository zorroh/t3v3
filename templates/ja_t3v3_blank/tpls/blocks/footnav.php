<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<?php if ($this->countModules($this->getPosname('footer-1 or footer-2 or footer-3 or footer-4 or footer-5 or footer-6'))) : ?>
<!-- FOOT NAVIGATION -->
<nav class="ja-footnav">
  <?php 
    $this->loadBlock ('spotlight/6cols', $this->getPosname('footer-1, footer-2, footer-3, footer-4, footer-5, footer-6'))
  ?>
</nav>
<!-- //FOOT NAVIGATION -->
<?php endif ?>
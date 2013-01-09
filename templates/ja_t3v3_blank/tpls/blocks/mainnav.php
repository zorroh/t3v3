<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- MAIN NAVIGATION -->
<nav id="ja-mainnav" class="wrap ja-mainnav">
  <div class="container navbar">
    <div class="navbar-inner">

      <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
    		<span class="icon-bar"></span>
    		<span class="icon-bar"></span>
    		<span class="icon-bar"></span>
  	  </button>

  	  <div class="nav-collapse collapse">
        <?php $this->mainnav() ?>
  		</div>
    </div>
  </div>
</nav>
<!-- //MAIN NAVIGATION -->
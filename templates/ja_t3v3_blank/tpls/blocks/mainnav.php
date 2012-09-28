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
  		<?php
    			$hasmenu = false;
    			if($this->params->get('mm_enable', 0)){
    				t3v3import('menu/mega');
    				
    				if(class_exists('JAMenuMega')){
    					$menu = new JAMenuMega($this->params);
    					$menu->loadMenu();
    					$menu->genMenu();
    					
    					$hasmenu = true;
    				}
    			}
    		?>
  		<?php if($hasmenu == false): ?>
  	    <jdoc:include type="modules" name="<?php $this->posname('mainnav') ?>" style="raw" />
  		<?php endif; ?>
  	  </div>

    </div>
  </div>
</nav>
<!-- //MAIN NAVIGATION -->
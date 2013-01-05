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
        if ($this->getParam ('mm_enable')) : 
          t3v3import('menu/megamenu');
          $menutype = $this->getParam ('mm_type', 'mainmenu');
          $file = T3V3_TEMPLATE_PATH.'/etc/megamenu.ini';
          $currentconfig = json_decode(@file_get_contents ($file), true);
          $mmconfig = ($currentconfig && isset($currentconfig[$menutype])) ? $currentconfig[$menutype] : array();

          $menu = new T3V3MenuMegamenu ($menutype, $mmconfig);
          $menu->render();          

          $this->addCss ('megamenu');
        ?>

        <?php else: ?>
        <jdoc:include type="modules" name="<?php $this->_p('mainnav') ?>" style="raw" />
        <?php endif ?>
  		</div>
      
    </div>
  </div>
</nav>
<!-- //MAIN NAVIGATION -->
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
    <jdoc:include type="head" />
    <?php $this->loadBlock ('head') ?>  
    <?php $this->addStyleSheet('home-1') ?>
  </head>

  <body>

    <?php $this->loadBlock ('header') ?>
    
    <?php $this->loadBlock ('mainnav') ?>
    
    <?php $this->loadBlock ('mainbody-home') ?>
    
    <?php $this->loadBlock ('footer') ?>

  </body>
</html>
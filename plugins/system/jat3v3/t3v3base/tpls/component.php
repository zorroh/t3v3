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
  </head>

  <body>
    <section id="ja-mainbody" class="container ja-mainbody">
      <div class="row">
        <div id="ja-content" class="ja-content span12">
          <jdoc:include type="component" />    
        </div>
      </div>
    </section>
  </body>

</html>
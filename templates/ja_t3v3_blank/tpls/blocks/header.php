<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$sitename = $this->params->get('sitename') ? $this->params->get('sitename') : JFactory::getConfig()->get('sitename');
$slogan = $this->params->get('slogan');

$logo_type = $this->params->get('logotype', 'text');
if ($logo_type == 'image') {
  $logo_img = $this->params->get('logoimage');
  if (!$logo_img) {
    $logo_img = 'templates/'.T3V3_TEMPLATE.'/images/logo.png';
  }
}
?>

<!-- HEADER -->
<header id="ja-header" class="container ja-header">
  <div class="row">
    <div class="span12">

      <?php if ($logo_type == 'text'): ?>
      <!-- LOGO TEXT -->
      <div class="logo logo-text">
        <h1>
          <a href="index.php" title="<?php echo strip_tags($sitename) ?>">
            <span><?php echo $sitename ?></span>
          </a>
          <small class="site-slogan hidden-phone"><?php echo $slogan ?></small>
        </h1>
      </div>
      <?php else: ?>
      <!-- LOGO IMAGE -->
      <div class="logo logo-image">
        <h1>
          <a href="index.php" title="<?php echo strip_tags($sitename) ?>">
              <img src="<?php echo $logo_img ?>" alt="<?php echo strip_tags($sitename) ?>" />
          </a>
        </h1>
      </div>
      <!-- //LOGO -->
       <?php endif; ?>

      <?php if ($this->countModules("head-search")) : ?>
      <!-- HEAD SEARCH -->
      <div class="head-search">
        <jdoc:include type="modules" name="head-search" style="raw" />
      </div>
      <!-- //HEAD SEARCH -->
      <?php endif ?>

    </div>
  </div>
</header>
<!-- //HEADER -->

<?php
/**
 * @package   T3 Blank
 * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<!-- FOOTER -->
<footer id="ja-footer" class="wrap ja-footer">

  <div class="container">
    <div class="row">
      <div class="span12">
        <?php $this->loadBlock ('footnav') ?>
      </div>
    </div>
  </div>

  <section class="ja-copyright">
    <div class="container">
      <div class="copyright">
          <jdoc:include type="modules" name="footer" />
      </div>
      <?php
      $t3_logo = $this->getParam ('setting_t3logo', 't3-logo-light', 't3-logo-dark');
      if ($t3_logo != 'none') : ?>
      <div class="poweredby">
          <small><a href="http://t3.joomlart.com" title="Powered By T3 Framework" target="_blank">Powered by <strong>T3 Framework</strong></a></small>
      </div>
      <?php endif; ?>
    </div>
  </section>

</footer>
<!-- //FOOTER -->
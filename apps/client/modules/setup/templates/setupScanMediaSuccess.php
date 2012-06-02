<?php
#
# Configure the Application app.yml
#
slot( 'title', __( 'Streeme: Media Scan' ) );
slot( 'description', __( 'Use this tool to scan your media' ) );
slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.4666, maximum-scale=1.0, user-scalable=no">' );
use_stylesheet( '/css/setup/stylesheet.css' );
?>
<script src="/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<div class="container">
  <div class="languagecontainer"><?php include_partial('player/languagechooser'); ?></div>
  <div class="formarea clearfix">
    <div class="infosection">
      <h1><?php echo __('Scan Your Media') ?></h1>
      <p>
        <?php echo __('Now that your settings are complete, you may begin scanning your media using the contols below.') ?>
        <?php echo __('If the scan fails to work, you may return to the settings start page to try configuring your app again or check the logs for setup errors. You may also run this process using the commandline by issuing the following commands') ?>
        <span class="code"><?php echo 'symfony schedule-scan' ?></span>
        <div class="scanstatus">
          <?php if ($scanSuccess): ?>
            <img src=""  alt="<?php echo __('Media scanned successfully') ?>" />
          <?php endif; ?>
          <?php if ($scanError): ?>
            <img src=""  alt="<?php echo __('Media could not be scanned') ?>" />
          <?php endif; ?>
        </div>
      </p>
    </div>
    <form method="post" action="">
      <div class="formsection"><ul class="form"><li>&nbsp;</li></ul></div>
      <div class="buttonsection">
        <ul class="buttons">
          <li class="submitbutton"><input type="submit" value="<?php echo __('Scan media now') ?> &raquo;"/></li>
          <li><a href="<?php echo url_for('@setup_home') ?>">Return to the setup start page</a></li>
        </ul>
      </div>
    </form>
  </div>
</div>
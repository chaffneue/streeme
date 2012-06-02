<?php
#
# Configure User accounts
#
slot( 'title', __( 'Streeme: User Management' ) );
slot( 'description', __( 'Please configure your user accounts' ) );
slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.4666, maximum-scale=1.0, user-scalable=no">' );
use_stylesheet( '/css/setup/stylesheet.css' );
?>
<script src="/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<div class="container">
  <div class="languagecontainer"><?php include_partial('player/languagechooser'); ?></div>
  <div class="formarea clearfix">
    <div class="infosection">
      <h1><?php echo __('Account Configuration') ?></h1>
      <p><?php echo __('Set up your users in the form below. Note that users do not have separate media libraries in Streeme. Any users you add here will be able to access all of your scanned music, playlists and album art, so use this tool wisely. If you want to install Streeme for separate libraries, consider running another streeme instance on a different set of ports.' ) ?></p>
    </div>
    <form method="post" action="<?php echo url_for('@setup_user') ?>" id="accountSetupForm">
      <div class="formsection">
        <ul class="form">
          <?php if($user_add_error): ?>
          <li class="delete">
            <div class="warningbox"><?php echo __('The User was not added due to an error. You can consult the log/client_dev.log file for more information') ?></div>
          </li>
          <?php endif?>
          <?php if($user_del_error): ?>
          <li class="delete">
            <div class="warningbox"><?php echo __('The User was not deleted due to an error. You can consult the log/client_dev.log file for more information') ?></div>
          </li>
          <?php endif?>
          <?php include_partial('setup/userSetupForm', array('form'=>$form)); ?>
        </ul>
      </div>
      <div class="buttonsection">
        <ul class="buttons">
          <li class="submitbutton"><input type="submit" value="<?php echo __('Save and Continue') ?>"/></li>
          <li><a href="<?php echo url_for('@setup_application') ?>"><?php echo __('Skip This Step') ?></a></li>
        </ul>
      </div>
    </form>
  </div>
</div>
<?php
#
# Configure User accounts
#
slot( 'title', __( 'Streeme: Welcome' ) );
slot( 'description', __( 'Use the following links to setup your application' ) );
slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.4666, maximum-scale=1.0, user-scalable=no">' );
use_stylesheet( '/css/setup/stylesheet.css' );
?>
<script src="/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<div class="container">
  <div class="languagecontainer"><?php include_partial('player/languagechooser'); ?></div>
  <div class="formarea clearfix" style="background: url(/images/setup/setup-header.jpg) #FFF no-repeat top left;">
    <div style="height: 114px; border-bottom: 1px solid #BBB; clear:both;"></div>
    <div class="infosection">
      <h1><?php echo __('Welcome to Streeme') ?></h1>
      <p>
        <?php echo __('Use the options below to configure your Streeme app on the web. Each of these tools also describe how to do similar operations on the commandline.') ?>
      </p>
      
      <ul class="introoptions">
        <li><a href="<?php echo url_for('@setup_db') ?>"><?php echo __('Setup Your Database') ?></a></li>
        <li><a href="<?php echo url_for('@setup_user') ?>"><?php echo __('Manage Your Users') ?></a></li>
        <li><a href="<?php echo url_for('@setup_application') ?>"><?php echo __('Configure Your Application') ?></a></li>
        <li><a href="<?php echo url_for('@setup_scan') ?>"><?php echo __('Run a Media Scan') ?></a></li>
      </ul>
    </div>
  </div>
</div>
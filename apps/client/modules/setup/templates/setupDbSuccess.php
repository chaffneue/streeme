<?php
#
# Configure the Database connection string
#
slot( 'title', __( 'Streeme: Database Configuration' ) );
slot( 'description', __( 'Please configure your database connections' ) );
slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.4666, maximum-scale=1.0, user-scalable=no">' );
use_stylesheet( '/css/setup/stylesheet.css' );
?>
<script src="/js/jquery-1.4.2.min.js" type="text/javascript"></script>
<div class="container">
  <div class="languagecontainer"><?php include_partial('player/languagechooser'); ?></div>
  <div class="formarea clearfix">
    <div class="infosection">
      <h1><?php echo __('Database Configuration') ?></h1>
      <p>
        <?php echo __('Set up your database by filling in the form below. We recommend using the SQLite with the default settings. <strong>If you have a large library of 10000 songs or more</strong>, you should download and install MySQL and fill in the appropriate fields. Streeme supports Sqlite, MySQL and PostgreSQL.' ) ?>
        <?php echo __('The following form will create or edit the following file, which you may edit at any time using a text editor or by re-running this setup script.') ?>
        <span class="code"><?php echo sprintf( '%s%sconfig%sdatabases.yml', sfConfig::get('sf_root_dir'), DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR) ?></span>
      </p>
    </div>
    <form method="post" action="<?php echo url_for('@setup_db') ?>" id="dbSetupForm" onsubmit="return confirm('<?php echo __('This action will completely overwrite any current datbase configurations and re-initialize the database. Proceed with setup?') ?>') ? true : false;">
      <div class="formsection">
        <ul class="form">
          <?php if($config_write_error): ?>
          <li class="delete">
            <div class="warningbox"><?php echo __('Could not write the config file, please verify that the web server has sufficient access rights to edit config/databases.yml') ?></div>
          </li>
          <?php endif?>
          <?php if($db_setup_error): ?>
          <li class="delete">
            <div class="warningbox"><?php echo __('The database failed to respond or is misconfigured. Please try your settings again. You can also consult the log/client_dev.log file for more information') ?></div>
          </li>
          <?php endif?>
                   
          <?php include_partial('setup/dbSetupForm', array('form'=>$form)); ?>
        </ul>
      </div>
      <div class="buttonsection">
        <ul class="buttons">
          <li class="submitbutton"><input type="submit" value="<?php echo __('Save and Continue') ?>"/></li>
          <li><a href="<?php echo url_for('@setup_user') ?>"><?php echo __('Skip This Step') ?></a></li>
        </ul>
      </div>
    </form>
  </div>
</div>
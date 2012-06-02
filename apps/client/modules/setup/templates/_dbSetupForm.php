<?php
#
# The Database configuration form
#
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#setup_database_type').bind('change',function(){
		  switch($('#setup_database_type').val())
		  {
			  case 'sqlite':
				  $('.mysql').hide();
				  $('.postgres').hide();
				  $('.sqlite').show();
				  break;

        case 'postgres':
            $('.mysql').hide();
            $('.sqlite').hide();
            $('.postgres').show();
            break;
				  
			  default:
		      $('.postgres').hide();
	        $('.sqlite').hide();
	        $('.mysql').show();
	        break;
		  }
	});
	$('#setup_database_type').trigger('change');
});
</script>
<li>
  <?php if($form['_csrf_token']->hasError()): ?>
    <span class="error_message"><?php echo __('Form Invalid. Please ensure cookies are turned on.') ?></span>
  <?php endif ?>
  <?php echo $form['_csrf_token']->render() ?>
  <?php if($sf_user->hasFlash('formStatus')): ?>
    <div class="success"></div><span class=""><?php echo $sf_user->getFlash('formStatus'); ?></span></div>
  <?php endif ?>
</li>

<li id="db_select" class="db_select">
  <label><?php echo __('Database Engine') ?></label>
  <?php if($form['database_type']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_type']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_type']->render(); ?>
</li>

<li class="postgres mysql">
  <label><?php echo __('Database Hostname/IP') ?></label>
  <?php if($form['database_host']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_host']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_host']->render(); ?>
</li>

<li class="mysql">
  <label><?php echo __('Database Port Number') ?></label>
  <?php if($form['database_port']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_port']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_port']->render(); ?>
</li>

<li class="postgres">
  <label><?php echo __('Database Port Number') ?></label>
  <?php if($form['database_pg_port']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_pg_port']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_pg_port']->render(); ?>
</li>

<li class="postgres mysql sqlite">
  <label><?php echo __('Database Name') ?></label>
  <?php if($form['database_name']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_name']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_name']->render(); ?>
</li>

<li class="postgres mysql">
  <label><?php echo __('Database Username') ?></label>
  <?php if($form['database_username']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_username']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_username']->render(); ?>
</li>

<li class="postgres mysql">
  <label><?php echo __('Database Password') ?></label>
  <?php if($form['database_password']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_password']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_password']->render(); ?>
</li>

<li class="sqlite">
  <label><?php echo __('Database File Path') ?></label>
  <?php if($form['database_path']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_path']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_path']->render(); ?>
</li>
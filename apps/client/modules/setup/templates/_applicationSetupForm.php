<?php
#
# The Database configuration form
#
?>
<script type="text/javascript">
$(document).ready(function(){
  $('#setup_library_type').bind('change',function(){
      switch($('#setup_library_type').val())
      {
        case '2':
          $('.itunes').hide();
          $('.filesystem').show();
          break;

        case '1':
            $('.filesystem').hide();
            $('.itunes').show();
            break;
      }
  });
  $('#setup_transcoding').bind('change',function(){
      switch($('#setup_transcoding').val())
      {
        case 'yes':
          $('.transcode').show();
          break;

        case 'no':
            $('.transcode').hide();
            break;
      }
  });
  
  $('#setup_library_type').trigger('change');
  $('#setup_transcoding').trigger('change');
  <?php
  if(!$isMysql)
  {
	  echo "$('.mysql').hide();\r\n";
  }
  ?>
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
  <label><?php echo __('Media Library Type') ?></label>
  <?php if($form['library_type']->hasError()): ?>
    <span class="error_message"><?php echo $form['library_type']->getError();?></span>
  <?php endif ?>
  <?php echo $form['library_type']->render(); ?>
</li>

<li class="itunes">
  <label><?php echo __('Itunes XML File Path') ?></label>
  <?php if($form['itunes_path']->hasError()): ?>
    <span class="error_message"><?php echo $form['itunes_path']->getError();?></span>
  <?php endif ?>
  <?php echo $form['itunes_path']->render(); ?>
</li>

<li class="filesystem">
  <label>
    <?php echo __('Add Media Folders') ?><br/>
    <span class="description"><?php echo __('Add one path to watch per line. If you are using iTunes, you can leave this area blank.') ?></span>
  </label>
  <?php if($form['library_paths']->hasError()): ?>
    <span class="error_message"><?php echo $form['library_paths']->getError();?></span>
  <?php endif ?>
  <?php echo $form['library_paths']->render(); ?>
</li>

<li>
  <label><?php echo __('Use Transcoding?') ?></label>
  <?php if($form['transcoding']->hasError()): ?>
    <span class="error_message"><?php echo $form['transcoding']->getError();?></span>
  <?php endif ?>
  <?php echo $form['transcoding']->render(); ?>
</li>

<li class="transcode">
  <label><?php echo __('FFMPEG executable') ?></label>
  <?php if($form['ffmpeg_path']->hasError()): ?>
    <span class="error_message"><?php echo $form['ffmpeg_path']->getError();?></span>
  <?php endif ?>
  <?php echo $form['ffmpeg_path']->render(); ?>
</li>

<li>
  <label><?php echo __('Items per Page') ?></label>
  <?php if($form['songs_per_page']->hasError()): ?>
    <span class="error_message"><?php echo $form['songs_per_page']->getError();?></span>
  <?php endif ?>
  <?php echo $form['songs_per_page']->render(); ?>
</li>

<li>
  <label><?php echo __('Enable Palm Pre Support?') ?></label>
  <?php if($form['send_cookies']->hasError()): ?>
    <span class="error_message"><?php echo $form['send_cookies']->getError();?></span>
  <?php endif ?>
  <?php echo $form['send_cookies']->render(); ?>
</li>

<li class="mysql">
  <?php if($form['database_indexing']->hasError()): ?>
    <span class="error_message"><?php echo $form['database_indexing']->getError();?></span>
  <?php endif ?>
  <?php echo $form['database_indexing']->render(); ?>
  <?php echo __('Enable indexing (for media libraries over 10k items)') ?>
</li>
<li>
  <label>
    <?php echo __('Add Setup IPs') ?><br/>
    <span class="description"><?php echo __('These setup screens may only be accessed by the following IP addresses - excluding 127.0.0.1') ?></span>
  </label>
  <?php if($form['allowed_ips']->hasError()): ?>
    <span class="error_message"><?php echo $form['library_paths']->getError();?></span>
  <?php endif ?>
  <?php echo $form['allowed_ips']->render(); ?>
</li>
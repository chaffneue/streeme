<script type="text/javascript">
$(document).ready(function(){
  $('#setup_user_action').bind('change',function(){
      switch($('#setup_user_action').val())
      {
        case 'add':
          $('.delete').hide();
          $('.add').show();
          break;

        case 'del':
            $('.add').hide();
            $('.delete').show();
            break;
      }
  });
  $('#setup_user_action').trigger('change');
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
<li class="delete">
  <div class="warningbox"><?php echo __('Warning! This action will permanently remove users from the Database') ?></div>
</li>
<li>
  <label><?php echo __('Action') ?></label>
  <?php if($form['user_action']->hasError()): ?>
    <span class="error_message"><?php echo $form['user_action']->getError();?></span>
  <?php endif ?>
  <?php echo $form['user_action']->render(); ?>
</li>

<li class="add delete">
  <label><?php echo __('Username') ?></label>
  <?php if($form['username']->hasError()): ?>
    <span class="error_message"><?php echo $form['username']->getError();?></span>
  <?php endif ?>
  <?php echo $form['username']->render(); ?>
</li>

<li class="add">
  <label><?php echo __('Password') ?></label>
  <?php if($form['password']->hasError()): ?>
    <span class="error_message"><?php echo $form['password']->getError();?></span>
  <?php endif ?>
  <?php echo $form['password']->render(); ?>
</li>

<li class="add">
  <label><?php echo __('Confirm Password') ?></label>
  <?php if($form['password_confirm']->hasError()): ?>
    <span class="error_message"><?php echo $form['password_confirm']->getError();?></span>
  <?php endif ?>
  <?php echo $form['password_confirm']->render(); ?>
</li>
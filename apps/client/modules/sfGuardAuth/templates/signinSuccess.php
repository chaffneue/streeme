<?php
  slot( 'title', __( 'Streeme: Please sign in' ) );
  slot( 'description', __( 'Please sign in to continue' ) );
  slot( 'metaHTML', '<meta name="viewport" content="width=device-width, initial-scale=0.6667, maximum-scale=1.0, user-scalable=no">' );
  use_stylesheet( '/css/auth/stylesheet.css' );
?>
<div class="languagecontainer"><?php include_partial('player/languagechooser'); ?></div>
<div class="logininstruction"><?php echo __( 'Please Sign In' ) ?></div>
<div class="loginerror">
<?php
if($form['username']->hasError())
{
  echo __('Invalid username or password.') . ' ';
}
?>
</div>
<div class="logincontainer">
  <form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
    <div class="formarea">
      <div><label for="signin_username"><?php echo __( 'Username' ) ?></label></div>
      <div><?php echo $form['username'] ?></div>
      <div><label for="signin_password"><?php echo __( 'Password' ) ?></label></div>
      <?php echo $form['password'] ?>
      <?php echo $form['_csrf_token'] ?>
      <div><?php echo $form['remember'] ?>&nbsp;<label for="signin_remember"><?php echo __( 'Remember me' ) ?></label></div>
      <div class="signin"><input type="submit" id="submitauth" value="<?php echo __( 'Sign in' ) ?>" /></div>
    </div>
  </form>
</div>
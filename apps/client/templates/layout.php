<!doctype html>
<html lang="en-us">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <?php echo get_slot( 'metaHTML' ); ?>
  <title><?php echo get_slot( 'title' ); ?></title>
  <meta name="description" content="<?php echo get_slot( 'description' ); ?>" />
  <link rel="shortcut icon" href="<?php echo public_path( 'favicon.ico', true ); ?>" />
  <link rel="apple-touch-icon" href="<?php echo public_path( 'apple-touch-icon.png', true ); ?>" />
  <?php include_stylesheets() ?>
  <?php include_javascripts() ?>
</head>
<body>
  <?php echo $sf_content ?>
</body>
</html>

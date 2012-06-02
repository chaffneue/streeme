<!doctype html>
<html lang="en-us">
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <?php echo get_slot( 'metaHTML' ); ?>
  <title><?php echo get_slot( 'title' ); ?></title>
  <meta name="description" content="<?php echo get_slot( 'description' ); ?>" />
  <link rel="shortcut icon" href="<?php echo public_path( 'favicon.ico', true ); ?>" />
  <link rel="apple-touch-icon" href="<?php echo public_path( 'apple-touch-icon.png', true ); ?>" />
  <?php
    $namespace = $this->getModuleName() . $this->getActionName();
    if( strtolower( $namespace ) != 'sfguardauthsignin'
        &&
        substr( $namespace, 0, 5 ) != 'setup'
    )
    {
      $combiner = new combineFiles();
      echo sprintf( '<link rel="stylesheet" type="text/css" href="%s" />',
                    $combiner->combine( 'css', $namespace, sfContext::getInstance()->getResponse()
                   ) );
      unset( $combiner );
    }
    else
    {
      include_stylesheets();
    }
  ?>
</head>
<body>
  <?php echo $sf_content ?>
</body>
</html>

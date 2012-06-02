<?php
#
# Find an appropriate combined js/css file in cache
#
class combineAction extends sfAction
{
  public function execute($request)
  {
    $file = null;
    foreach( $this->getContext()->getRouting()->getRoutes() as $route )
    {
      $route_info = $route->getDefaults();
      if( $request->getParameter('namespace') == strtolower($route_info['module'] . $route_info['action']) )
      {
        $combiner = new combineFiles();
        $file = $combiner->getFileName($request->getParameter('type'), strtolower($route_info['module'] . $route_info['action']));
      }
    }
    $this->content = null;
    if ( is_readable( $file ) )
    {
      $this->content = file_get_contents( $file );
    }
    else
    {
      $this->content = sprintf('Couldn\'t read file %s', $file);
    }
    
    sfConfig::set('sf_web_debug', false);
    $this->getResponse()->setHttpHeader('Content-Type', ( $request->getParameter('type') === 'css' ) ? 'text/css' : 'text/javascript' );
    $this->getResponse()->setHttpHeader('Cache-Control','max-age=86040000, public, must-revalidate');
    $this->setTemplate('output');
    $this->setLayout(false);
  }
}
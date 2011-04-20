<?php
class sfWebResponseMock extends sfWebResponse
{
  public function getStylesheets()
  {
    return array( 
                  '/test/test.css' => array(),
                  '/test/test.min.css' => array(),
                 );
  }
  
  public function getJavascripts()
  {
    return array( 
                  '/test/test.js' => array(),
                  '/test/test.min.js' => array(),
                 );
  }
}
<?php
#
# Culture management system for Streeme.
# Cheers to Symfonians for i18nSubdomainFilter which showed me what to do :) I owe someone a beer 
#
class i18nAutodetectFilter extends sfFilter
{
  /**
   * Log filter activity
   *
   * @param string  $message
   * @param int     $level
   */
  public function log($message, $level = sfLogger::DEBUG)
  {
    sfContext::getInstance()->getLogger()->log('{i18nAutodetectFilter} '.$message, $level);
  }

  /**
   * Executes filter chain
   *
   * @param sfFilterChain $filterChain
   */
  public function execute($filterChain)
  {
    if ($this->isFirstCall())
    {
      $context = $this->getContext();
      $request = $context->getRequest();
      $response = $context->getResponse();
      $user = $context->getUser();
      $cookieName = sfConfig::get( 'app_translation_cookie', 'myCulture' );
      
      //get the user's preferred browser language
      $culture =  $request->getPreferredCulture( sfConfig::get('sf_translations_available', array() ) );
      $this->log(sprintf( 'Browser language is %s', $culture ) );
      
      //override with a user cookie language if it exists
      if( $request->getCookie($cookieName) )
      {
        $culture = $request->getCookie($cookieName);
        $this->log(sprintf( 'Overriding culture %s from culture cookie', $culture ) );
      }
      
      //has the user overridden the language manually?
      if( $request->getParameter('sf_culture') && StreemeUtil::in_array_ci( $request->getParameter('sf_culture'),  sfConfig::get('sf_translations_available', array() ) ) )
      {
        //set the language for this request, so you don't have to refresh
        $culture = $request->getParameter('sf_culture');
        
        //update the cookie with the new language for future requests
        $response->setCookie( $cookieName, $culture, time()+60*60*24*30*12*30, '/' );
        
        $this->log(sprintf( 'Culture manually overridden to %s', $culture ) );
      }
      
      $this->log(sprintf('Applying culture: %s for this session', $culture));
      $user->setCulture( $culture );
      sfConfig::set('sf_current_culture', $culture );
      $context->getResponse()->addMeta( 'language', $culture, true );
    }
    $filterChain->execute();
  }
}

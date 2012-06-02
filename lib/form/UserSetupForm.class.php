<?php
/**
 * This is the form used by the setup page to expose user setup
 *
 * @author Richard Hoar
 * @package streeme
 */
class UserSetupForm extends BaseForm
{
  private function getUserActionChoices()
  {
    return array(
      'add'=>sfContext::getInstance()->getI18N()->__('Add User'),
      'del'=>sfContext::getInstance()->getI18N()->__('Delete User'),
    );
  }
  
  public function configure()
  {
    $this->setWidgets(array(
      'user_action'       => new sfWidgetFormSelect(array('choices'=>$this->getUserActionChoices())),
      'username'          => new sfWidgetFormInput(),
      'password'          => new sfWidgetFormInputPassword(),
      'password_confirm'  => new sfWidgetFormInputPassword(),
    ));
    
    $this->setValidators(array(
      'user_action'       => new sfValidatorChoice(array('required' => true, 'choices'=>array_keys($this->getUserActionChoices()))),
      'username'          => new sfValidatorPass(),
      'password'          => new sfValidatorPass(),
      'password_confirm'  => new sfValidatorPass(),
    ));
    
    $this->widgetSchema->setNameFormat('setup[%s]');
    $this->widgetSchema->setFormFormatterName('list');
  }
}
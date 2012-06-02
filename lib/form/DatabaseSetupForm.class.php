<?php
/**
 * This is the form used by the setup page to expose databases.yml setup
 *
 * @author Richard Hoar
 * @package streeme
 */
class DatabaseSetupForm extends BaseForm
{
  private function getDatabaseTypeChoices()
  {
    return array(
      'sqlite'=>sfContext::getInstance()->getI18N()->__('SQLite'),
      'mysql'=>sfContext::getInstance()->getI18N()->__('MySQL'),
      'postgres'=>sfContext::getInstance()->getI18N()->__('PostgreSQL')
    );
  }
    
  public function configure()
  {
    $this->setWidgets(array(
      'database_type'     => new sfWidgetFormSelect(array('choices'=>$this->getDatabaseTypeChoices())),
      'database_host'     => new sfWidgetFormInput(),
      'database_port'     => new sfWidgetFormInput(),
      'database_pg_port'  => new sfWidgetFormInput(),
      'database_name'     => new sfWidgetFormInput(),
      'database_username' => new sfWidgetFormInput(),
      'database_password' => new sfWidgetFormInputPassword(),
      'database_path'     => new sfWidgetFormInput(),
    ));
    
    $this->setValidators(array(
      'database_type'     => new sfValidatorChoice(array('required' => true, 'choices'=>array_keys($this->getDatabaseTypeChoices()))),
      'database_host'     => new sfValidatorPass(),
      'database_port'     => new sfValidatorPass(),
      'database_pg_port'  => new sfValidatorPass(),
      'database_name'     => new sfValidatorPass(),
      'database_username' => new sfValidatorPass(),
      'database_password' => new sfValidatorPass(),
      'database_path'     => new sfValidatorPass(),
    ));
    
    $this->setDefaults(array(
      'database_host'     => 'localhost',
      'database_port'     => '3306',
      'database_pg_port'  => '5432',
      'database_name'     => 'streeme',
      'database_path'     => sfConfig::get('sf_root_dir') .'/data'
    ));
    $this->widgetSchema->setNameFormat('setup[%s]');
    $this->widgetSchema->setFormFormatterName('list');
  }
}
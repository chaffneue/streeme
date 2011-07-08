<?php

/**
 * EchonestProperties filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseEchonestPropertiesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'song_id' => new sfWidgetFormFilterInput(),
      'name'    => new sfWidgetFormFilterInput(),
      'value'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'song_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'    => new sfValidatorPass(array('required' => false)),
      'value'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('echonest_properties_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EchonestProperties';
  }

  public function getFields()
  {
    return array(
      'id'      => 'Number',
      'song_id' => 'Number',
      'name'    => 'Text',
      'value'   => 'Text',
    );
  }
}

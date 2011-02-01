<?php

/**
 * Album form base class.
 *
 * @method Album getObject() Returns the current form's model object
 *
 * @package    streeme
 * @subpackage form
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAlbumForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'scan_id'         => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'amazon_flagged'  => new sfWidgetFormInputText(),
      'meta_flagged'    => new sfWidgetFormInputText(),
      'folders_flagged' => new sfWidgetFormInputText(),
      'service_flagged' => new sfWidgetFormInputText(),
      'has_art'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scan_id'         => new sfValidatorInteger(array('required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'amazon_flagged'  => new sfValidatorInteger(array('required' => false)),
      'meta_flagged'    => new sfValidatorInteger(array('required' => false)),
      'folders_flagged' => new sfValidatorInteger(array('required' => false)),
      'service_flagged' => new sfValidatorInteger(array('required' => false)),
      'has_art'         => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Album', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('album[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Album';
  }

}

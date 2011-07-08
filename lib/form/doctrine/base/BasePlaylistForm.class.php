<?php

/**
 * Playlist form base class.
 *
 * @method Playlist getObject() Returns the current form's model object
 *
 * @package    streeme
 * @subpackage form
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlaylistForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'scan_id'           => new sfWidgetFormInputText(),
      'service_name'      => new sfWidgetFormInputText(),
      'service_unique_id' => new sfWidgetFormInputText(),
      'name'              => new sfWidgetFormInputText(),
      'mtime'             => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'scan_id'           => new sfValidatorInteger(array('required' => false)),
      'service_name'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'service_unique_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'name'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mtime'             => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('playlist[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Playlist';
  }

}

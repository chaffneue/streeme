<?php

/**
 * PlaylistFiles form base class.
 *
 * @method PlaylistFiles getObject() Returns the current form's model object
 *
 * @package    streeme
 * @subpackage form
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePlaylistFilesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'playlist_id' => new sfWidgetFormInputText(),
      'filename'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'playlist_id' => new sfValidatorInteger(array('required' => false)),
      'filename'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('playlist_files[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlaylistFiles';
  }

}

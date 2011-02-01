<?php

/**
 * Song form base class.
 *
 * @method Song getObject() Returns the current form's model object
 *
 * @package    streeme
 * @subpackage form
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSongForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'unique_id'       => new sfWidgetFormInputText(),
      'artist_id'       => new sfWidgetFormInputText(),
      'album_id'        => new sfWidgetFormInputText(),
      'genre_id'        => new sfWidgetFormInputText(),
      'last_scan_id'    => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'length'          => new sfWidgetFormInputText(),
      'accurate_length' => new sfWidgetFormInputText(),
      'filesize'        => new sfWidgetFormInputText(),
      'bitrate'         => new sfWidgetFormInputText(),
      'yearpublished'   => new sfWidgetFormInputText(),
      'tracknumber'     => new sfWidgetFormInputText(),
      'label'           => new sfWidgetFormInputText(),
      'mtime'           => new sfWidgetFormInputText(),
      'atime'           => new sfWidgetFormInputText(),
      'filename'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'unique_id'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'artist_id'       => new sfValidatorInteger(array('required' => false)),
      'album_id'        => new sfValidatorInteger(array('required' => false)),
      'genre_id'        => new sfValidatorInteger(array('required' => false)),
      'last_scan_id'    => new sfValidatorInteger(array('required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'length'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'accurate_length' => new sfValidatorInteger(array('required' => false)),
      'filesize'        => new sfValidatorInteger(array('required' => false)),
      'bitrate'         => new sfValidatorInteger(array('required' => false)),
      'yearpublished'   => new sfValidatorInteger(array('required' => false)),
      'tracknumber'     => new sfValidatorInteger(array('required' => false)),
      'label'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mtime'           => new sfValidatorInteger(array('required' => false)),
      'atime'           => new sfValidatorInteger(array('required' => false)),
      'filename'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('song[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Song';
  }

}

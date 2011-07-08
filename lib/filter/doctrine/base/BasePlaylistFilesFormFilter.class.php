<?php

/**
 * PlaylistFiles filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlaylistFilesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'playlist_id' => new sfWidgetFormFilterInput(),
      'filename'    => new sfWidgetFormFilterInput(),
      'orderfield'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'playlist_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'filename'    => new sfValidatorPass(array('required' => false)),
      'orderfield'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('playlist_files_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PlaylistFiles';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'playlist_id' => 'Number',
      'filename'    => 'Text',
      'orderfield'  => 'Number',
    );
  }
}

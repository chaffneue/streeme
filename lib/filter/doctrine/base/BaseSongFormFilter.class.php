<?php

/**
 * Song filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSongFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scan_id'         => new sfWidgetFormFilterInput(),
      'unique_id'       => new sfWidgetFormFilterInput(),
      'artist_id'       => new sfWidgetFormFilterInput(),
      'album_id'        => new sfWidgetFormFilterInput(),
      'name'            => new sfWidgetFormFilterInput(),
      'length'          => new sfWidgetFormFilterInput(),
      'accurate_length' => new sfWidgetFormFilterInput(),
      'filesize'        => new sfWidgetFormFilterInput(),
      'bitrate'         => new sfWidgetFormFilterInput(),
      'yearpublished'   => new sfWidgetFormFilterInput(),
      'tracknumber'     => new sfWidgetFormFilterInput(),
      'label'           => new sfWidgetFormFilterInput(),
      'isremix'         => new sfWidgetFormFilterInput(),
      'mtime'           => new sfWidgetFormFilterInput(),
      'atime'           => new sfWidgetFormFilterInput(),
      'filename'        => new sfWidgetFormFilterInput(),
      'comments'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'scan_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unique_id'       => new sfValidatorPass(array('required' => false)),
      'artist_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'album_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'            => new sfValidatorPass(array('required' => false)),
      'length'          => new sfValidatorPass(array('required' => false)),
      'accurate_length' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'filesize'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bitrate'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'yearpublished'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tracknumber'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'label'           => new sfValidatorPass(array('required' => false)),
      'isremix'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'mtime'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'atime'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'filename'        => new sfValidatorPass(array('required' => false)),
      'comments'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('song_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Song';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'scan_id'         => 'Number',
      'unique_id'       => 'Text',
      'artist_id'       => 'Number',
      'album_id'        => 'Number',
      'name'            => 'Text',
      'length'          => 'Text',
      'accurate_length' => 'Number',
      'filesize'        => 'Number',
      'bitrate'         => 'Number',
      'yearpublished'   => 'Number',
      'tracknumber'     => 'Number',
      'label'           => 'Text',
      'isremix'         => 'Number',
      'mtime'           => 'Number',
      'atime'           => 'Number',
      'filename'        => 'Text',
      'comments'        => 'Text',
    );
  }
}

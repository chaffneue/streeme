<?php

/**
 * Playlist filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePlaylistFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scan_id'           => new sfWidgetFormFilterInput(),
      'service_name'      => new sfWidgetFormFilterInput(),
      'service_unique_id' => new sfWidgetFormFilterInput(),
      'name'              => new sfWidgetFormFilterInput(),
      'mtime'             => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'scan_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'service_name'      => new sfValidatorPass(array('required' => false)),
      'service_unique_id' => new sfValidatorPass(array('required' => false)),
      'name'              => new sfValidatorPass(array('required' => false)),
      'mtime'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('playlist_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Playlist';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'scan_id'           => 'Number',
      'service_name'      => 'Text',
      'service_unique_id' => 'Text',
      'name'              => 'Text',
      'mtime'             => 'Number',
    );
  }
}

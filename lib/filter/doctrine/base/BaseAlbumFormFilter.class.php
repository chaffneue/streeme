<?php

/**
 * Album filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAlbumFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scan_id'         => new sfWidgetFormFilterInput(),
      'name'            => new sfWidgetFormFilterInput(),
      'amazon_flagged'  => new sfWidgetFormFilterInput(),
      'meta_flagged'    => new sfWidgetFormFilterInput(),
      'folders_flagged' => new sfWidgetFormFilterInput(),
      'service_flagged' => new sfWidgetFormFilterInput(),
      'has_art'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'scan_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'            => new sfValidatorPass(array('required' => false)),
      'amazon_flagged'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'meta_flagged'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'folders_flagged' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'service_flagged' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'has_art'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('album_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Album';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'scan_id'         => 'Number',
      'name'            => 'Text',
      'amazon_flagged'  => 'Number',
      'meta_flagged'    => 'Number',
      'folders_flagged' => 'Number',
      'service_flagged' => 'Number',
      'has_art'         => 'Number',
    );
  }
}

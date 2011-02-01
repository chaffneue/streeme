<?php

/**
 * Scan filter form base class.
 *
 * @package    streeme
 * @subpackage filter
 * @author     Richard Hoar
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseScanFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'scan_time' => new sfWidgetFormFilterInput(),
      'scan_type' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'scan_time' => new sfValidatorPass(array('required' => false)),
      'scan_type' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('scan_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Scan';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'scan_time' => 'Text',
      'scan_type' => 'Text',
    );
  }
}

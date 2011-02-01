<?php

/**
 * User group reference model.
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage model
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: PluginsfGuardUserGroup.class.php 23793 2009-11-11 17:42:50Z Kris.Wallsmith $
 */
abstract class PluginsfGuardUserGroup extends BasesfGuardUserGroup
{
  public function postSave($event)
  {
    parent::postSave($event);
    $this->getsfGuardUser()->reloadGroupsAndPermissions();
  }
}

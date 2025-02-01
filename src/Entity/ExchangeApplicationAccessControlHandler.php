<?php

namespace Drupal\studenciaki\Entity;

use Drupal;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

class ExchangeApplicationAccessControlHandler extends EntityAccessControlHandler {

  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
//    switch ($operation) {
//      case 'view':
//      case 'update':
//        return AccessResult::allowedIfHasPermissions($account, ['manage exchange_application', 'manage own exchange_application']);
//      case 'delete':
//        return AccessResult::forbidden();
//
//      default:
//        return parent::checkAccess($entity, $operation, $account);
//    }

    return AccessResult::allowed();
  }


  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
//    return parent::checkCreateAccess($account, $context, $entity_bundle);
    return AccessResult::allowed();
  }

}

<?php

namespace Drupal\studenciaki\Entity;

use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;

class ExchangeApplicationRepresentativeListBuilder extends EntityListBuilder {

  protected $currentUser;

  protected $page;

  protected $limit;

  protected $sortField;

  protected $sortOrder;

  protected $search;

  /**
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, AccountInterface $current_user, $page = 0, $limit = 1, $sortField = 'created', $sortOrder = 'DESC', $search = '') {
    parent::__construct($entity_type, $storage);
    $this->currentUser = $current_user;
    $this->page = (int) $page;
    $this->limit = (int) $limit;
    $this->sortField = $sortField;
    $this->sortOrder = $sortOrder;
    $this->search = $search;
  }

  /**
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function load(): array {
    $exchange_offer_ids = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getQuery()
      ->condition('type', 'exchange_offer')
      ->accessCheck(TRUE)
      ->execute();

    if (empty($exchange_offer_ids)) {
      return [];
    }

    $count_query = $this->getEntitiesByOfferIds($exchange_offer_ids);

    $total = (int) $count_query->count()->execute();
    \Drupal::service('pager.manager')->createPager($total, $this->limit);

    $query = $this->getEntitiesByOfferIds($exchange_offer_ids);

    $query->sort($this->sortField, $this->sortOrder);
    $query->range($this->page * $this->limit, $this->limit);
    $entity_ids = $query->execute();
    return $this->getStorage()->loadMultiple($entity_ids);
  }

  /**
   * {@inheritdoc}
   *
   */
  public function render(): array {
    $entities = $this->load();
    $items = [];

    $isSuperAdmin = $this->currentUser->hasPermission('administer exchange_application');
    foreach ($entities as $entity) {
      if (!$isSuperAdmin) {
        $isOfferOwner = $entity->get('exchange_offer')->entity->getOwnerId() == $this->currentUser->id();
        if (!$isOfferOwner) {
          continue;
        }
      }
      $referenced_entity = $entity->get('exchange_offer')->entity;

      $items[] = [
        'entity_id' => $entity->id(),
        'referenced_entity' => $referenced_entity,
        'exchange_application' => $entity,
      ];
    }

    return [
      '#theme' => 'exchange_application_representative_list',
      '#items' => $items,
      '#pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

  private function getEntitiesByOfferIds($exchange_offer_ids): \Drupal\Core\Entity\Query\QueryInterface {
    $query = $this->getStorage()->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('exchange_offer', $exchange_offer_ids, 'IN');

    if (!empty($this->search)) {
      $orGroup = $query->orConditionGroup()
        ->condition('firstname', '%' . $this->search . '%', 'LIKE')
        ->condition('lastname', '%' . $this->search . '%', 'LIKE');
      $query->condition($orGroup);
    }
    return $query;
  }

}

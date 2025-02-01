<?php

namespace Drupal\studenciaki\Entity;

use Drupal\Core\Entity\EntityListBuilder;

class ExchangeApplicationListBuilder extends EntityListBuilder {

  public function load(): array {
    $request = \Drupal::requestStack()->getCurrentRequest();
    $page = (int) $request->query->get('page', 0);
    $sort = $request->query->get('sort', 'created');
    $order = strtoupper($request->query->get('order', 'DESC'));
    $search = $request->query->get('search');

    $allowed_sort_fields = ['created', 'status'];
    if (!in_array($sort, $allowed_sort_fields)) {
      $sort = 'created';
    }
    if (!in_array($order, ['ASC', 'DESC'])) {
      $order = 'DESC';
    }

    $limit = 5;
    $query = $this->getStorage()->getQuery();
    $query->accessCheck(TRUE);
    $current_user_id = \Drupal::currentUser()->id();
    $query->condition('user_id', $current_user_id);

    if (!empty($search)) {
      $offer_ids = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->getQuery()
        ->accessCheck(TRUE)
        ->condition('type', 'exchange_offer')
        ->condition('title', '%' . $search . '%', 'LIKE')
        ->execute();
      if (!empty($offer_ids)) {
        $query->condition('exchange_offer', $offer_ids, 'IN');
      }
      else {
        return [];
      }
    }

    $count_query = clone $query;
    $total = (int) $count_query->count()->execute();

    \Drupal::service('pager.manager')->createPager($total, $limit);
    $query->sort($sort, $order);
    $query->range($page * $limit, $limit);

    $entity_ids = $query->execute();
    return $this->getStorage()->loadMultiple($entity_ids);
  }

  public function render(): array {
    $entities = $this->load();
    $items = [];

    foreach ($entities as $entity) {
      $referenced_entity = $entity->get('exchange_offer')->entity;
      $items[] = [
        'entity_id' => $entity->id(),
        'referenced_entity' => $referenced_entity,
        'exchange_application' => $entity,
      ];
    }

    return [
      '#theme' => 'exchange_application_list',
      '#items' => $items,
      '#pager' => [
        '#type' => 'pager',
      ],
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}

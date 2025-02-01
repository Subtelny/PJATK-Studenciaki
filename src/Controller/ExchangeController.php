<?php

namespace Drupal\studenciaki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class ExchangeController extends ControllerBase {

  public function listOffers(): array {
    $request = \Drupal::requestStack()->getCurrentRequest();
    $page = (int) $request->query->get('page', 0);
    $sort = $request->query->get('sort', 'exchange_start_date');
    $order = strtoupper($request->query->get('order', 'ASC'));
    $search = $request->query->get('search', '');

    $allowed_sort_fields = ['exchange_start_date', 'exchange_ranking'];
    if (!in_array($sort, $allowed_sort_fields)) {
      $sort = 'exchange_start_date';
    }

    if (!in_array($order, ['ASC', 'DESC'])) {
      $order = 'ASC';
    }

    $limit = 5;

    $query = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('type', 'exchange_offer');

    if (!empty($search)) {
      $or = $query->orConditionGroup()
        ->condition('exchange_location', '%' . $search . '%', 'LIKE')
        ->condition('title', '%' . $search . '%', 'LIKE');
      $query->condition($or);
    }

    $count_query = clone $query;
    $total = (int) $count_query->count()->execute();

    \Drupal::service('pager.manager')->createPager($total, $limit);

    $query->sort($sort, $order);
    $query->range($page * $limit, $limit);

    $exchange_offer_ids = $query->execute();
    $nodes = Node::loadMultiple($exchange_offer_ids);

    $items = [];
    foreach ($nodes as $node) {
      $items[] = [
        'node' => $node,
      ];
    }

    return [
      '#theme' => 'exchange_offer_list',
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

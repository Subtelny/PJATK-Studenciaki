<?php

namespace Drupal\studenciaki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\studenciaki\Entity\ExchangeApplicationRepresentativeListBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class ExchangeApplicationRepresentativeController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   *
   * @return array
   *   A render array.
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function render(Request $request) {
    $page = (int) $request->query->get('page', 0);
    $allowed_sort_fields = ['created', 'status'];
    $sort = $request->query->get('sort', 'created');
    if (!in_array($sort, $allowed_sort_fields)) {
      $sort = 'created';
    }
    $order = strtoupper($request->query->get('order', 'DESC'));
    if (!in_array($order, ['ASC', 'DESC'])) {
      $order = 'DESC';
    }

    $search = $request->query->get('search', '');

    $list_builder = new ExchangeApplicationRepresentativeListBuilder(
      $this->entityTypeManager->getDefinition('exchange_application'),
      $this->entityTypeManager->getStorage('exchange_application'),
      $this->currentUser(),
      $page,
      7,
      $sort,
      $order,
      $search
    );

    return $list_builder->render();
  }

}

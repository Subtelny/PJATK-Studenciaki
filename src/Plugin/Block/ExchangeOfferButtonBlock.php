<?php

namespace Drupal\studenciaki\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 *
 * @Block(
 *   id = "exchange_offer_button_block",
 *   admin_label = @Translation("Exchange Offer Button"),
 * )
 */
class ExchangeOfferButtonBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof \Drupal\node\NodeInterface && $node->bundle() === 'exchange_offer') {
      $url = Url::fromRoute('entity.exchange_application.add_form', ['offer_id' => $node->id()]);
      $link = Link::fromTextAndUrl($this->t('Złóż Aplikację'), $url)->toRenderable();
      $link['#attributes']['class'][] = 'button';
      $link['#attributes']['class'][] = 'button--primary';
      $link['#attributes']['style'][] = 'margin-top: 20px;';

      return $link;
    }

    return [];
  }

}

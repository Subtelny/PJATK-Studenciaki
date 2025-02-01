<?php

namespace Drupal\studenciaki\Entity;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Theme\Registry;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExchangeApplicationViewBuilder extends EntityViewBuilder {

  protected $currentUser;

  public function __construct(EntityTypeInterface $entity_type, EntityRepositoryInterface $entity_repository, LanguageManagerInterface $language_manager, AccountInterface $current_user, Registry $theme_registry, EntityDisplayRepositoryInterface $entity_display_repository) {
    parent::__construct($entity_type, $entity_repository, $language_manager, $theme_registry, $entity_display_repository);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.repository'),
      $container->get('language_manager'),
      $container->get('current_user'),
      $container->get('theme.registry'),
      $container->get('entity_display.repository'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build): array {
    $build = parent::build($build);
    $entity = $build['#exchange_application'] ?? NULL;
    if (!$entity) {
      return $build;
    }

    $offer_id = $entity->get('exchange_offer')->target_id;
    $offer_node = Node::load($offer_id);

    if (!$this->currentUser->hasPermission('administer exchange_application')) {
      $isOwner = $entity->getOwnerId() == $this->currentUser->id();
      $isOfferOwner = $offer_node != NULL && $entity->getOwnerId() == $offer_node->getOwnerId();
      if ((
          $isOwner && !$this->currentUser->hasPermission('manage own exchange_application')
        ) || (
          $isOfferOwner && !$this->currentUser->hasPermission('manage exchange_application'))
      ) {
        $build['#theme'] = 'exchange_application_forbidden';
        $build['#cache'] = [
          'max-age' => 0,
        ];
        return $build;
      }
    }

    if ($entity->hasField('exchange_application_comment')) {
      $comment_build = $entity->get('exchange_application_comment')->view([
        'label' => 'hidden',
        'type' => 'comment_default',
      ]);
      $build['#comments_section'] = $comment_build;
    }
    $build['#files'] = $entity->get('files')->view([
      'label' => 'hidden',
      'type' => 'file_default',
    ]);

    $build['#exchange_offer'] = $entity->get('exchange_offer')->view([
      'label' => 'hidden',
      'type' => 'entity_reference_entity_view',
    ]);

    $build['#can_edit_exchange_offer'] = FALSE;
    $referenced_entity = $entity->get('exchange_offer')->entity;
    if ($referenced_entity != NULL) {
      $build['#referenced_entity'] = $referenced_entity;
      $url = $referenced_entity->toUrl('canonical');
      $build['#referenced_entity_link'] = Link::fromTextAndUrl($referenced_entity->label(), $url)
        ->toRenderable();
      if ($referenced_entity->access('update')) {
        $build['#can_edit_exchange_offer'] = TRUE;
      }
    }

    $build['#entity_id'] = $entity->id();
    $build['#theme'] = 'exchange_application_canonical';
    $build['#cache'] = [
      'max-age' => 0,
    ];
    return $build;
  }

}

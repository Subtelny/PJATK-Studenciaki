<?php

namespace Drupal\studenciaki\Entity;

use Drupal\comment\Plugin\Field\FieldType\CommentItemInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 *
 * @ingroup exchange_application
 *
 * @ContentEntityType(
 *   id = "exchange_application",
 *   label = @Translation("Aplikacja na wymianę"),
 *   handlers = {
 *     "access" =
 *   "Drupal\studenciaki\Entity\ExchangeApplicationAccessControlHandler",
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "view_builder" =
 *   "Drupal\studenciaki\Entity\ExchangeApplicationViewBuilder",
 *     "list_builder" =
 *   "Drupal\studenciaki\Entity\ExchangeApplicationListBuilder",
 *     "views_data" = "Drupal\studenciaki\Entity\ExchangeApplicationViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\studenciaki\Form\ExchangeApplicationForm",
 *       "add" = "Drupal\studenciaki\Form\ExchangeApplicationForm",
 *       "edit" = "Drupal\studenciaki\Form\ExchangeApplicationForm",
 *       "delete" = "Drupal\studenciaki\Form\ExchangeApplicationDeleteForm"
 *     },
 *   },
 *   base_table = "exchange_application",
 *   revision_table = "exchange_application_revision",
 *   admin_permission = "administer exchange_application",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "revision" = "revision_id",
 *   },
 *   links = {
 *     "canonical" = "/exchange-application/{exchange-application}",
 *     "add-form" = "/exchange-application/add/{offer_id}",
 *     "edit-form" = "/exchange-application/{exchange-application}/edit",
 *     "collection" = "/exchange-application"
 *   },
 *   field_ui_base_route = "exchange-application.settings"
 * )
 */
class ExchangeApplication extends ContentEntityBase implements EntityChangedInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type): array {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The primary identifier for the ExchangeApplication entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the ExchangeApplication entity.'))
      ->setReadOnly(TRUE);

    $fields['revision_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Revision ID'))
      ->setDescription(t('The revision identifier.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nazwa'))
      ->setDescription(t('Nazwa aplikacji na wymianę.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Autor'))
      ->setDescription(t('The user ID of author of the ExchangeApplication.'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getCurrentUserId')
      ->setReadOnly(TRUE);

    $fields['firstname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Imię'))
      ->setDescription(t('Imię studenta.'))
      ->setDefaultValueCallback(static::class . '::getCurrentUserFirstName')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['lastname'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Nazwisko'))
      ->setDescription(t('Nazwisko studenta.'))
      ->setDefaultValueCallback(static::class . '::getCurrentUserLastName')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['current_university'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Obecna uczelnia'))
      ->setDescription(t('Obecna uczelnia studenta.'))
      ->setDefaultValueCallback(static::class . '::getCurrentUserUniversityName')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['field_of_study'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Kierunek studiów'))
      ->setDescription(t('Kierunek studiów studenta.'))
      ->setDefaultValueCallback(static::class . '::getCurrentUserFieldOfStudyName')
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['exchange_application_comment'] = BaseFieldDefinition::create('comment')
      ->setLabel(t('Comments'))
      ->setDescription(t('Comments about this entity.'))
      ->setDefaultValue([
        'status' => CommentItemInterface::OPEN,
      ])
      ->setSetting('comment_type', 'exchange_application_comment');

    $fields['files'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Dokumenty'))
      ->setDescription(t('Dokumenty aplikacji na wymianę.'))
      ->setSettings([
        'file_extensions' => 'pdf doc docx xls xlsx txt png jpg jpeg',
        'title_field' => FALSE,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'file_default',
        'weight' => 15,
      ])
      ->setDisplayOptions('form', [
        'type' => 'file_generic',
        'weight' => 15,
      ])
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setDescription(t('Status aplikacji na wymianę.'))
      ->setSettings([
        'allowed_values' => [
          'approved' => 'Zaakceptowana',
          'declined' => 'Odrzucona',
          'waiting' => 'Oczekująca',
        ],
      ])
      ->setDefaultValue('waiting');

    $fields['exchange_offer'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Oferta wymiany'))
      ->setDescription(t('Oferta wymiany, na którą aplikuje student.'))
      ->setSetting('target_type', 'node')
      ->setSetting('handler', 'default')
      ->setSetting('handler_settings', [
        'target_bundles' => [
          'exchange_offer' => 'exchange_offer',
        ],
      ])
      ->setDefaultValueCallback(static::class . '::getDefaultExchangeOffer')
      ->setRequired(TRUE);

    return $fields;
  }

  /**
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function getDefaultExchangeOffer(): ?array {
    $route_match = \Drupal::routeMatch();
    $offer_id = $route_match->getParameter('offer_id');

    if ($offer_id && \Drupal::entityTypeManager()
        ->getStorage('node')
        ->load($offer_id)) {
      return ['target_id' => $offer_id];
    }

    return NULL;
  }

  /**
   * @return int|null
   *   The user ID of the owner, or NULL if not set.
   */
  public function getOwnerId(): ?int {
    return $this->get('user_id')->target_id;
  }

  public static function getCurrentUserId(): array {
    return [\Drupal::currentUser()->id()];
  }

  public static function getCurrentUserFirstName(): array {
    return self::getCurrentUserFieldValue('field_name');
  }

  public static function getCurrentUserLastName(): array {
    return self::getCurrentUserFieldValue('field_surname');
  }

  public static function getCurrentUserUniversityName(): array {
    $referencedNodeName = self::getReferencedNodeName('field_university_reference');
    if ($referencedNodeName[0] !== '') {
      return $referencedNodeName;
    }
    return self::getCurrentUserFieldValue('field_university_name');
  }

  public static function getCurrentUserFieldOfStudyName(): array {
    return self::getCurrentUserFieldValue('field_study_field');
  }

  public static function getReferencedNodeName(string $fieldname): array {
    $route_match = \Drupal::routeMatch();
    $node = $route_match->getParameter('node');
    if ($node && $node->hasField($fieldname) && !$node->get($fieldname)
        ->isEmpty()) {
      $referenced_nodes = $node->get($fieldname)->referencedEntities();
      $referenced_node = reset($referenced_nodes);
      if ($referenced_node) {
        return [$referenced_node->label()];
      }
    }
    return [''];
  }

  public static function getCurrentUserFieldValue($fieldname): array {
    $current_user = \Drupal::currentUser();
    $user_storage = \Drupal::entityTypeManager()->getStorage('user');
    $user = $user_storage->load($current_user->id());
    if ($user && $user->hasField($fieldname) && !$user->get($fieldname)
        ->isEmpty()) {
      $first_name = $user->get($fieldname)->value;
      return [$first_name];
    }
    return [''];
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime(): ?int {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp): ExchangeApplication|EntityChangedInterface|static {
    $this->set('changed', $timestamp);
    return $this;
  }

  public function getChangedTimeAcrossTranslations(): int {
    return $this->get('changed')->value;
  }

}

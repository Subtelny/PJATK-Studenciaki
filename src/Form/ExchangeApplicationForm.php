<?php

namespace Drupal\studenciaki\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class ExchangeApplicationForm extends ContentEntityForm {

  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if ($this->entity->isNew()) {
      $current_user_id = \Drupal::currentUser()->id();
      $offer_id = $this->entity->get('exchange_offer')->target_id;

      if (empty($offer_id)) {
        $form_state->setErrorByName('exchange_offer', $this->t('Exchange offer is required.'));
        return;
      }

      $storage = \Drupal::entityTypeManager()
        ->getStorage('exchange_application');
      $count = $storage->getQuery()
        ->condition('user_id', $current_user_id)
        ->condition('exchange_offer', $offer_id)
        ->count()
        ->accessCheck(FALSE)
        ->execute();

      if ($count > 0) {
        $form_state->setErrorByName('', $this->t('Już złożyłeś aplikację na tę ofertę wcześniej.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $offer_id = \Drupal::routeMatch()->getParameter('offer_id');

    if ($offer_id && $this->entity->hasField('exchange_offer') && $this->entity->get('exchange_offer')
        ->isEmpty()) {
      $offer_node = Node::load($offer_id);
      if ($offer_node && $offer_node->bundle() === 'exchange_offer') {
        $this->entity->set('exchange_offer', $offer_id);

        $uni_name = $offer_node->get('exchange_uni_name')->value;
        $this->entity->set('firstname', $uni_name);
      }
      else {
        \Drupal::messenger()->addError(t('Nie znaleziono oferty wymiany.'));
      }
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        \Drupal::messenger()
          ->addStatus('Twoja aplikacja została złożona pomyślnie.');
        break;

      default:
        \Drupal::messenger()
          ->addStatus('Twoja aplikacja została zapisana pomyślnie.');
    }

    $form_state->setRedirect('entity.exchange_application.canonical', ['exchange_application' => $entity->id()]);
  }

}

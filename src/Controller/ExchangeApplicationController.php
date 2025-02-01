<?php

namespace Drupal\studenciaki\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\studenciaki\Entity\ExchangeApplication;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\studenciaki\Service\ExchangeApplicationService;


class ExchangeApplicationController extends ControllerBase {

  /**
   *
   * @param \Drupal\studenciaki\Entity\ExchangeApplication $exchange_application
   *   The MyEntity entity to approve.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirects to the canonical page with a status message.
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function approve(ExchangeApplication $exchange_application): RedirectResponse {
    if (!$this->currentUser()
      ->hasPermission('change exchange_application status')) {
      $this->messenger()
        ->addError($this->t('Nie masz uprawnień do zmiany statusu tej aplikacji.'));
      return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
    }

    $exchange_application->set('status', 'approved');
    $exchange_application->set('changed', \Drupal::time()
      ->getRequestTime());
    $exchange_application->save();

    $this->messenger()
      ->addStatus($this->t('Aplikacja wymiany została zaakceptowana.'));

    $this->sendEmail($exchange_application, 'Gratulacje! Twoja aplikacja wymiany została zaakceptowana.');
    return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
  }

  /**
   *
   * @param \Drupal\studenciaki\Entity\ExchangeApplication $exchange_application
   *   The MyEntity entity to decline.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirects to the canonical page with a status message.
   */
  public function decline(ExchangeApplication $exchange_application): RedirectResponse {
    if (!$this->currentUser()
      ->hasPermission('change exchange_application status')) {
      $this->messenger()
        ->addError($this->t('Nie masz uprawnień do zmiany statusu tej aplikacji.'));
      return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
    }

    $exchange_application->set('status', 'declined');
    $exchange_application->set('changed', \Drupal::time()
      ->getRequestTime());
    $exchange_application->save();

    $this->messenger()
      ->addStatus($this->t('Aplikacja wymiany została odrzucona.'));

    $this->sendEmail($exchange_application, 'Przykro nam, Twoja aplikacja wymiany została odrzucona.');
    return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
  }

  /**
   *
   * @param \Drupal\studenciaki\Entity\ExchangeApplication $exchange_application
   *   The MyEntity entity to set to waiting.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirects to the canonical page with a status message.
   */
  public function waiting(ExchangeApplication $exchange_application): RedirectResponse {
    // Check if the user has permission to change status.
    if (!$this->currentUser()
      ->hasPermission('change exchange_application status')) {
      $this->messenger()
        ->addError($this->t('Nie masz uprawnień do zmiany statusu tej aplikacji.'));
      return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
    }

    $exchange_application->set('status', 'waiting');
    $exchange_application->set('changed', \Drupal::time()
      ->getRequestTime());
    $exchange_application->save();

    $this->messenger()
      ->addStatus($this->t('Aplikacja wymiany została ustawiona jako oczekująca.'));

    $this->sendEmail($exchange_application, 'Twoja aplikacja wymiany została ustawiona jako oczekująca.');
    return $this->redirect('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()]);
  }

  /**
   * @param \Drupal\studenciaki\Entity\ExchangeApplication $exchange_application
   *
   * @return void
   */
  private function sendEmail(ExchangeApplication $exchange_application, $message): void {
    $user = User::load($exchange_application->getOwnerId());
    $email = $user->getEmail();
    $display_name = $user->getDisplayName();
    $url = Url::fromRoute('entity.exchange_application.canonical', ['exchange_application' => $exchange_application->id()], ['absolute' => TRUE])
      ->toString();
    $exchange_application_service = \Drupal::service('studenciaki.exchange_application_service');
    $exchange_application_service->sendExchangeApplicationChangedEmail($email, $display_name, $url, $message);
  }

}

<?php

namespace Drupal\studenciaki\Service;

use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Service for handling Exchange Applications.
 */
class ExchangeApplicationService {

  /**
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   */
  public function __construct(MailManagerInterface $mail_manager, LanguageManagerInterface $language_manager) {
    $this->mailManager = $mail_manager;
    $this->languageManager = $language_manager;
  }

  public function sendExchangeApplicationChangedEmail($to, $username, $url, $message): void {
    $module = 'studenciaki';
    $key = 'exchange_application_changed';
    $langcode = $this->languageManager->getDefaultLanguage()->getId();
    $params['username'] = $username;
    $params['message'] = t($message);
    $params['application_link'] = $url;
    $this->mailManager->mail($module, $key, $to, $langcode, $params, NULL, TRUE);
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager')
    );
  }

}

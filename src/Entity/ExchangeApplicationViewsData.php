<?php

namespace Drupal\studenciaki\Entity;

use Drupal\views\EntityViewsData;


class ExchangeApplicationViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Example of how you might add a custom join or relationship:
    // $data['my_entity']['table']['group'] = $this->t('My entity');
    // $data['my_entity']['table']['provider'] = 'my_module';

    return $data;
  }

}

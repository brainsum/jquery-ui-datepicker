<?php

namespace Drupal\jquery_ui_datepicker\Callback;

use Drupal\Core\Datetime\Element\Datetime;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Class DateTimeDatepicker.
 *
 * @package Drupal\jquery_ui_datepicker\Callback
 */
class DateTimeDatepicker implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRender'];
  }

  /**
   * Pre-render callback for datetime elements.
   *
   * @param array $element
   *   The element.
   *
   * @return array
   *   The element.
   *
   * @see: jquery_ui_datepicker_element_info_alter / datetime.
   */
  public static function preRender(array $element) {
    if (!empty($element['#jqdp'])) {
      $element['timezone'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => Datetime::formatExample('T'),
        '#attributes' => [
          'title' => new TranslatableMarkup('The date in the timezone of your account.'),
        ],
      ];
    }

    return $element;
  }

}

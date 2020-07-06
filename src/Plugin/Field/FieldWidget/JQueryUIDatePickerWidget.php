<?php

namespace Drupal\jquery_ui_datepicker\Plugin\Field\FieldWidget;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\datetime\Plugin\Field\FieldWidget\DateTimeDefaultWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'datetime_default' widget.
 *
 * @FieldWidget(
 *   id = "jquery_ui_datepicker",
 *   label = @Translation("jQuery UI datepicker"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class JQueryUIDatePickerWidget extends DateTimeDefaultWidget {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')->getStorage('date_format'),
      $container->get('current_user')
    );
  }

  /**
   * JQueryUIDatePickerTimestampWidget constructor.
   *
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    EntityStorageInterface $dateStorage,
    AccountProxyInterface $currentUser
  ) {
    parent::__construct(
      $plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $third_party_settings,
      $dateStorage
    );

    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    // TODO: Add timepicker settings.
    return [
      'date_format' => 'd M y',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $options = [];

    /** @var \Drupal\Core\Datetime\DateFormatInterface $format */
    foreach ($this->dateStorage->loadMultiple() as $format) {
      $options[$format->getPattern()] = $format->label();
    }

    $element['date_format'] = [
      '#type' => 'select',
      '#title' => $this->t('Date Format'),
      '#options' => $options,
      '#default_value' => $this->settings['date_format'],
    ];

    // TODO: Add timepicker settings form.
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $form['#attached']['library'][] = 'jquery_ui_datepicker/timepicker';
    $element['value']['#jqdp'] = TRUE;

    $dateName = $this->fieldDefinition->getName() . '[' . $delta . ']' . '[value][date]';
    $timeName = $this->fieldDefinition->getName() . '[' . $delta . ']' . '[value][time]';

    $settings = $this->getSettings();

    $dateFormat = _date_format_parse($settings['date_format']);

    $element['value']['#date_date_element'] = 'textfield';
    $element['value']['#date_time_element'] = 'textfield';
    $element['value']['#date_date_format'] = $dateFormat['date'];
    $element['value']['#date_time_format'] = $dateFormat['time'];

    $element['value']['#value_callback'] = 'jquery_ui_datepicker_value_callback';

    $form['#attached']['drupalSettings']['jquery_ui_datepicker'][$dateName] = [
      'dateFormat' => _date_format_to_jquery_format($dateFormat['date']),
    ];

    $dateTimeObject = $element['value']['#default_value'] instanceof DrupalDateTime
      ? $element['value']['#default_value']
      : new DrupalDateTime();

    // TODO: Update this to use configured settings.
    $form['#attached']['drupalSettings']['jquery_timepicker'][$timeName] = [
      'timeFormat' => _date_format_to_jquery_format($dateFormat['time']),
      'interval' => 60,
      'minTime' => '00:00',
      'maxTime' => '23:59',
      'startTime' => $dateTimeObject->format('G'),
      'dynamic' => FALSE,
      'dropdown' => TRUE,
      'scrollbar' => FALSE,
    ];

    $profileLink = Link::createFromRoute(
      $this->t('profile edit page'),
      'entity.user.edit_form',
      ['user' => $this->currentUser->id()]
    );
    $element['value']['#description'] = $this->t(
      'Change timezone preference on your @profile.',
      ['@profile' => $profileLink->toString()]
    );

    return $element;
  }

}

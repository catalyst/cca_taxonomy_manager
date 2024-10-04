<?php

namespace Drupal\cca_taxonomy_manager\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'cca_taxonomy_manager.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cca_taxonomy_manager.settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $vocabularies = \Drupal::entityTypeManager()->getStorage('taxonomy_vocabulary')->loadMultiple();
    $options = [];
    foreach ($vocabularies as $machine_name => $vocabulary) {
      $options[$machine_name] = $vocabulary->label();
    }

    $form['taxonomies'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Taxonomies'),
      '#options' => $options,
      '#default_value' => $config->get('taxonomies') ?? [],
      '#description' => t('Specify taxonomies that should have their listing provided by a CCA Taxonomy Manager view display. Set this <em>after</em> creating the view display and display menu tab.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('taxonomies', $form_state->getValue('taxonomies'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}

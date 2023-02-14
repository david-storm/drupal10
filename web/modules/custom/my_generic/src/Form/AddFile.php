<?php

namespace Drupal\my_generic\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AddFile extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csv_importer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['csv'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Select CSV file'),
      '#required' => TRUE,
      '#autoupload' => TRUE,
      '#upload_validators' => ['file_validate_extensions' => ['csv']],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\my_generic\BatchService $batchService */
    $batchService = \Drupal::getContainer()->get('my_generic.parser');
    $batchService->process(current($form_state->getValue('csv')));
  }

}

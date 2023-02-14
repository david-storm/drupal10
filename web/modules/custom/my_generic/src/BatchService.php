<?php

namespace Drupal\my_generic;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Queue\Batch;
use Drupal\Core\Queue\Memory;

class BatchService {

  use DependencySerializationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function process($csvFile) {
    $data = $this->parseFile($csvFile);
    $batch_builder = (new BatchBuilder())
      ->setQueue('memory_queue', Batch::class)
      ->setTitle(t('Parsing csv file'))
      ->addOperation([$this, 'main_logic'], [$data]);

    batch_set($batch_builder->toArray());
  }


  public function main_logic($contents, array &$context) {

    if (!isset($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = count($contents);
    }

    //todo istead sleep will be something logic
    sleep(2);

    $context['sandbox']['progress']++;

    if ($context['sandbox']['progress'] !== $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
  }

  private function parseFile($csvFile) {
    $entity = $this->entityTypeManager->getStorage('file')->load($csvFile);

    $return = [];

    if (($csv = fopen($entity->uri->getString(), 'r')) !== FALSE) {
      while (($row = fgetcsv($csv, 0, ',')) !== FALSE) {
        $return[] = $row;
      }

      fclose($csv);
    }

    return $return;
  }

}

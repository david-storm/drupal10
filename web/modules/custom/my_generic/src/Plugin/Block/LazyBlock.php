<?php

namespace Drupal\my_generic\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Provides a block to show lazy building in action.
 *
 * @Block(
 *   id = "lazyblock",
 *   admin_label = @Translation("Lazy block"),
 *   category = @Translation("Custom")
 * )
 */
class LazyBlock extends BlockBase implements TrustedCallbackInterface {

  public function build() {
    $build = [];

    $build['time'] = [
      '#lazy_builder' => [
        static::class . '::lazyBuilder',
        []
      ],
    ];

    return $build;
  }

  public static function lazyBuilder() {
    $build = [];

    sleep(5);
    $build['lazy_builder_time'] = [
      '#markup' => date('r'),
      '#cache' => [
        'max-age' => 0,
      ],
    ];

    return $build;
  }

  public static function trustedCallbacks() {
    return [
      'lazyBuilder'
    ];
  }
}

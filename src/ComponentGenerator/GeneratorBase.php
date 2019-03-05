<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\entity_summary\GeneratedSummary;
use Drupal\entity_summary\GeneratorAwareInterface;
use Drupal\entity_summary\GeneratorAwareTrait;
use Drupal\entity_summary\GeneratorInterface;

abstract class GeneratorBase implements GeneratorInterface, GeneratorAwareInterface {
  use GeneratorAwareTrait;

  /**
   * @inheritdoc
   */
  public function generate($object, &$context = NULL) {
    if (!isset($context)) {
      $context = [];
    }

    return $this->generateWithContext($object, $context);
  }

  /**
   * @param mixed $object
   * @param array $context
   * @return GeneratedSummary|string|null
   */
  abstract protected function generateWithContext($object, &$context);
}

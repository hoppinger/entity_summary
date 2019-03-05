<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_summary\GeneratedSummary;

class FieldItemListGenerator extends GeneratorBase {
  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    return $object instanceof FieldItemListInterface;
  }

  /**
   * @inheritdoc
   */
  public function generateWithContext($object, &$context) {
    $empty_result = new GeneratedSummary();

    foreach ($object as $item) {
      $item_result = GeneratedSummary::createFromObject($this->generator->generate($item, $context));

      if ($item_result->isEmpty()) {
        $empty_result->addCacheableDependency($item_result);
      } else {
        $item_result->addCacheableDependency($empty_result);
        return $item_result;
      }
    }

    return $empty_result;
  }
}

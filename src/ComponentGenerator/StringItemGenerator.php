<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Field\Plugin\Field\FieldType\StringItemBase;
use Drupal\entity_summary\GeneratedSummary;

class StringItemGenerator extends GeneratorBase {
  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    return $object instanceof StringItemBase;
  }

  /**
   * @inheritdoc
   */
  public function generateWithContext($object, &$context) {
    if ($object->value && trim($object->value)) {
      return new GeneratedSummary(Unicode::truncate(trim($object->value), 200, TRUE));
    }

    return new GeneratedSummary();
  }
}

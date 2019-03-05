<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\entity_summary\GeneratedSummary;

class EntityReferenceItemGenerator extends GeneratorBase {
  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    return $object instanceof EntityReferenceItem;
  }

  /**
   * @inheritdoc
   */
  public function generateWithContext($object, &$context) {
    $entity = $object->entity;

    if ($entity) {
      return $this->generator->generate($entity, $context);
    }

    return new GeneratedSummary();
  }
}

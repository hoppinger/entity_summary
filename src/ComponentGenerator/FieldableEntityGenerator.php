<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Component\Utility\SortArray;
use Drupal\entity_summary\GeneratedSummary;

class FieldableEntityGenerator extends GeneratorBase {
  /**
   * @var EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * @param EntityFieldManagerInterface $entityFieldManager
   */
  public function __construct(EntityFieldManagerInterface $entityFieldManager) {
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    return $object instanceof FieldableEntityInterface;
  }

  /**
   * Check if we are not stuck in an endless loop.
   *
   * Return FALSE if we are not in a loop, TRUE if we are in a loop.
   *
   * @param FieldableEntityInterface $object
   * @param array $context
   * @return bool
   */
  protected function detectLoop(FieldableEntityInterface $object, &$context) {
    if (!isset($context['entities'])) {
      $context['entities'] = [];
    }

    if (!isset($context['entities'][$object->getEntityTypeId()])) {
      $context['entities'][$object->getEntityTypeId()] = [];
    }

    if (in_array($object->id(), $context['entities'][$object->getEntityTypeId()])) {
      return TRUE;
    }

    $context['entities'][$object->getEntityTypeId()][] = $object->id();

    return FALSE;
  }

  /**
   * @inheritdoc
   */
  public function generateWithContext($object, &$context) {
    /** @var FieldableEntityInterface $object */

    $empty_result = new GeneratedSummary();
    $empty_result->addCacheableDependency($object);

    if ($this->detectLoop($object, $context)) {
      return $empty_result;
    }

    $display = EntityViewDisplay::collectRenderDisplay($object, 'entity_summary');
    $components = $display->getComponents();
    uasort($components, [SortArray::class, 'sortByWeightElement']);

    $baseFields = $this->entityFieldManager->getBaseFieldDefinitions($object->getEntityTypeId());
    foreach ($baseFields as $key => $baseField) {
      if (!isset($components[$key])) {
        continue;
      }

      if ($baseField->getType() == 'text_long' || $baseField->getType() == 'string_long') {
        continue;
      }

      unset($components[$key]);
    }

    $empty_result->addCacheTags(['entity_view_display_list', 'entity_types', 'entity_field_info']);

    foreach ($components as $component_name => $component) {
      if ($formatter = $display->getRenderer($component_name)) {
        $items = $object->get($component_name);
        $items->filterEmptyItems();

        $items_result = GeneratedSummary::createFromObject($this->generator->generate($items, $context));
        if ($items_result->isEmpty()) {
          $empty_result->addCacheableDependency($items_result);
        } else {
          $items_result->addCacheableDependency($empty_result);
          return $items_result;
        }
      }
    }

    return $empty_result;
  }
}

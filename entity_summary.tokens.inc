<?php

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\entity_summary\GeneratedSummary;

function entity_summary_token_info_alter(&$data) {
  foreach ($data['types'] as $type_key => $info) {

    if (empty($info['needs-data']) || $info['needs-data'] != $type_key) {
      continue;
    }

    if (empty($data['tokens'][$type_key])) {
      continue;
    }

    $entity_definition = \Drupal::entityTypeManager()->getDefinition($type_key, FALSE);
    if (!$entity_definition) {
      continue;
    }

    if (!($entity_definition instanceof ContentEntityType)) {
      continue;
    }

    $data['tokens'][$type_key]['entity_summary'] = [
      'name' => t("@entity_type summary", ['@entity_type' => $entity_definition->getLabel()]),
      'description' => t("An automatically generated summary of the @entity_type.", ['@entity_type' => $entity_definition->getLabel()]),
    ];
  }
}

function entity_summary_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  foreach ($tokens as $name => $original) {
    if ($name != 'entity_summary') {
      continue;
    }

    if (empty($data[$type])) {;
      continue;
    }

    $summary = \Drupal::service('entity_summary.caching_generator')->generate($data[$type]);
    $summary_object = GeneratedSummary::createFromObject($summary);

    if (!$summary_object->isEmpty()) {
      $replacements[$original] = $summary_object->getSummary();
    }

    $bubbleable_metadata->addCacheableDependency($summary_object);
  }

  return $replacements;
}
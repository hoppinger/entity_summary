<?php

namespace Drupal\entity_summary;

interface GeneratorInterface {
  /**
   * @param mixed $object
   * @param array|null $context
   * @return GeneratedSummary|string|null
   */
  public function generate($object, &$context = NULL);

  /**
   * @param mixed $object
   * @return bool
   */
  public function supportsGeneration($object);
}
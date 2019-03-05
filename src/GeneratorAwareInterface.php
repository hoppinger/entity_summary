<?php

namespace Drupal\entity_summary;

interface GeneratorAwareInterface {
  /**
   * @param GeneratorInterface $generator
   */
  public function setGenerator(GeneratorInterface $generator);
}
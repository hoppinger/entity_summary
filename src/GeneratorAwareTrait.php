<?php

namespace Drupal\entity_summary;

trait GeneratorAwareTrait {
  /**
   * @var GeneratorInterface
   */
  protected $generator;

  /**
   * @param GeneratorInterface $generator
   */
  public function setGenerator(GeneratorInterface $generator) {
    $this->generator = $generator;
  }
}
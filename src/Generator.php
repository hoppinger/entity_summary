<?php

namespace Drupal\entity_summary;

class Generator implements GeneratorInterface {
  /**
   * @var GeneratorInterface[]
   */
  protected $component_generators;

  /**
   * @param GeneratorInterface[] $component_generators
   */
  public function __construct($component_generators) {
    foreach ($component_generators as $component_generator) {
      if ($component_generator instanceof GeneratorAwareInterface) {
        $component_generator->setGenerator($this);
      }
    }
    $this->component_generators = $component_generators;
  }

  /**
   * @inheritdoc
   */
  public function generate($object, &$context = NULL) {
    if (!isset($context)) {
      $context = [];
    }

    if ($generator = $this->getGenerator($object)) {
      return $generator->generate($object, $context);
    }

    return new GeneratedSummary();
  }

  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    $generator = $this->getGenerator($object);
    return isset($generator);
  }

  /**
   * @param mixed $object
   * @return GeneratorInterface|null
   */
  protected function getGenerator($object) {
    foreach ($this->component_generators as $generator) {
      if ($generator instanceof GeneratorInterface && $generator->supportsGeneration($object)) {
        return $generator;
      }
    }
  }
}
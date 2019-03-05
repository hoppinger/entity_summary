<?php

namespace Drupal\entity_summary;

use Drupal\Core\Render\BubbleableMetadata;

class GeneratedSummary extends BubbleableMetadata {
  /**
   * @var string|null
   */
  protected $summary;

  /**
   * @param string|null $summary
   */
  public function __construct($summary = NULL) {
    $this->summary = $summary;
  }

  /**
   * @return bool
   */
  public function isEmpty() {
    return !isset($this->summary);
  }

  /**
   * @param string|null $summary
   */
  public function setSummary($summary = NULL): void {
    $this->summary = $summary;
  }

  /**
   * @return string|null
   */
  public function getSummary() {
    return $this->summary;
  }

  public static function createFromObject($object) {
    if (!isset($object) || is_string($object)) {
      return new static($object);
    }

    if ($object instanceof GeneratedSummary) {
      $result = new static($object->getSummary());
      $result->addCacheableDependency($object);
      return $result;
    }

    return parent::createFromObject($object);
  }
}

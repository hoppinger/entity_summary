<?php

namespace Drupal\entity_summary;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Cache\UseCacheBackendTrait;
use Drupal\Core\Entity\EntityInterface;

class CachingGenerator implements GeneratorInterface {
  use UseCacheBackendTrait;

  /**
   * @var GeneratorInterface
   */
  protected $generator;

  public function __construct(GeneratorInterface $generator, CacheBackendInterface $cacheBackend) {
    $this->generator = $generator;
    $this->cacheBackend = $cacheBackend;
  }

  public function supportsGeneration($object) {
    return $this->generator->supportsGeneration($object);
  }

  public function generate($object, &$context = NULL) {
    $cid = NULL;

    if ($object instanceof EntityInterface) {
      $cid = 'entity_summary:' . $object->getEntityTypeId() . ':' . $object->id();
    }

    if ($cid) {
      if ($cache = $this->cacheGet($cid)) {
        return $cache->data;
      }
    }

    $result = GeneratedSummary::createFromObject($this->generator->generate($object, $context));

    if (!$cid) {
      return $result;
    }

    $this->cacheSet($cid, $result, $result->getCacheMaxAge(), $result->getCacheTags());
    return $result;
  }
}

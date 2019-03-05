<?php

namespace Drupal\entity_summary\ComponentGenerator;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\entity_summary\GeneratedSummary;
use Drupal\text\Plugin\Field\FieldType\TextItemBase;

class TextItemGenerator extends GeneratorBase {
  /**
   * @var RendererInterface
   */
  protected $renderer;

  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * @inheritdoc
   */
  public function supportsGeneration($object) {
    return $object instanceof TextItemBase;
  }

  /**
   * @inheritdoc
   */
  public function generateWithContext($object, &$context) {
    $empty_result = new GeneratedSummary();

    if (!empty($object->summary) && trim($object->summary)) {
      $summary_result = $this->summarizeText($object, 'summary');
      if ($summary_result->isEmpty()) {
        $empty_result->addCacheableDependency($summary_result);
      } else {
        $summary_result->addCacheableDependency($empty_result);
        return $summary_result;
      }
    }

    if (!empty($object->value) && trim($object->value)) {
      $value_result = $this->summarizeText($object, 'value');
      if ($value_result->isEmpty()) {
        $empty_result->addCacheableDependency($value_result);
      } else {
        $value_result->addCacheableDependency($empty_result);
        return $value_result;
      }
    }

    return $empty_result;
  }

  /**
   * Summarize HTML text by reducing it to plain text and then truncating it.
   *
   * @param TextItemBase $object
   * @param string $property
   * @return GeneratedSummary
   */
  protected function summarizeText(TextItemBase $object, $property) {
    // We deliberately don't use text_summary here, because that function is
    // designed to return a HTML string with the specified length. Since we
    // strip the HTML off, that function is not a great fit here.
    $build = [
      '#type' => 'processed_text',
      '#text' => $object->{$property},
      '#format' => $object->format,
      '#filter_types_to_skip' => [],
      '#langcode' => $object->getLangcode(),
    ];

    $render_context = new RenderContext();
    $markup = $this->renderer->executeInRenderContext($render_context, function() use($build) {
      return $this->renderer->render($build);
    });

    $result = new GeneratedSummary();
    if (!$render_context->isEmpty()) {
      $result->addCacheableDependency($render_context->pop());
    }

    $plain_text = Html::decodeEntities(strip_tags($markup));
    if (trim($plain_text)) {
      $result->setSummary(Unicode::truncate($plain_text, 200, TRUE));
    }

    return $result;
  }
}

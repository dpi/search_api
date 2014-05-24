<?php

/**
 * @file
 * Contains \Drupal\search_api\Item\Field.
 */

namespace Drupal\search_api\Item;

/**
 * Represents a field on a search item that can be indexed.
 */
class Field implements FieldInterface {

  use FieldTrait;

  /**
   * The field's values.
   *
   * @var array
   */
  protected $values = array();

  /**
   * The Search API data type of this field.
   *
   * @var string
   */
  protected $type;

  /**
   * The original data type of this field.
   *
   * @var string
   */
  protected $originalType;

  /**
   * The state of this field in the index, whether indexed or not.
   *
   * @var bool
   */
  protected $indexed;

  /**
   * The boost assigned to this field, if any.
   *
   * @var float
   */
  protected $boost;

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function setType($type) {
    $this->type = $type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function setValues(array $values) {
    $this->values = $values;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addValue($value) {
    $this->values[] = $value;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOriginalType() {
    return $this->originalType;
  }

  /**
   * {@inheritdoc}
   */
  public function setOriginalType($original_type) {
    $this->originalType = $original_type;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isIndexed() {
    if (!isset($this->indexed)) {
      $fields = $this->index->getOption('fields', array());
      $this->indexed = isset($fields[$this->fieldIdentifier]);
    }
    return $this->indexed;
  }

  /**
   * {@inheritdoc}
   */
  public function setIndexed($indexed, $notify = FALSE) {
    $this->indexed = (bool) $indexed;
    if ($notify) {
      $fields = $this->index->getOption('fields', array());
      if ($indexed) {
        $fields[$this->fieldIdentifier] = array('type' => $this->getType());
        if (($boost = $this->getBoost()) != 1.0) {
          $fields[$this->fieldIdentifier]['boost'] = $boost;
        }
      }
      else {
        unset($fields[$this->fieldIdentifier]);
      }
      $this->index->setOption('fields', $fields);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getBoost() {
    if (!isset($this->boost)) {
      $fields = $this->index->getOption('fields', array());
      $this->boost = isset($fields[$this->fieldIdentifier]['boost']) ? (float) $fields[$this->fieldIdentifier]['boost'] : 1.0;
    }
    return $this->boost;
  }

  /**
   * {@inheritdoc}
   */
  public function setBoost($boost, $notify = FALSE) {
    $boost = (float) $boost;
    $this->boost = $boost;
    if ($notify) {
      $fields = $this->index->getOption('fields', array());
      if (isset($fields[$this->fieldIdentifier])) {
        if ($boost != 1.0) {
          $fields[$this->fieldIdentifier]['boost'] = $boost;
        }
        else {
          unset($fields[$this->fieldIdentifier]['boost']);
        }
      }
      $this->index->setOption('fields', $fields);
    }
    return $this;
  }

}

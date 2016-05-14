<?php

class Chart implements \JsonSerializable
{

  private $type;
  private $displayed;
  private $data;
  private $options;
  private $formatters;

  public function __construct($type)
  {
    $this->type = $type;
    $this->displayed = true;
    $this->data = array();
    $this->options = new ColumnChartOptions();
    $this->formatters = array();
    $this->options = new ColumnChartOptions();
    $this->options->addColor("#1AA329");
    $this->options->addColor("#1C64FF");
    $this->options->addColor("#CC0000");
    $this->options->addColor("#ffff00");
    $this->options->addColor("#f94a32");
    $this->options->addColor("#ffa500");
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function setOptions($options)
  {
    $this->options = $options;
  }

  public function getOptions()
  {
    return $this->options;
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
} 
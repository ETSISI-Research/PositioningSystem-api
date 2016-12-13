<?php

class RadarChartDataset implements \JsonSerializable
{

  private $label;
  private $fillColor;
  private $strokeColor;
  private $pointColor;
  private $pointStrokeColor;
  private $pointHighlightStroke;
  private $data;

  public function __construct()
  {
    $this->label = "";
    $this->fillColor = "rgba(255,0,0,0.2)";
    $this->strokeColor = "rgba(220,220,220,1)";
    $this->pointColor = "rgba(220,220,220,1)";
    $this->pointStrokeColor = "#fff";
    $this->pointHighlightStroke = "rgba(220,220,220,1)";
    $this->data = array();
  }

  public function setData($data)
  {
    $this->data = $data;
  }

  public function addData($data)
  {
    array_push($this->data, $data);
  }

  public function setFillColor($fillColor)
  {
    $this->fillColor = $fillColor;
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
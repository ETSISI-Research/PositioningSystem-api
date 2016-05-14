<?php

class ColumnChartOptions implements \JsonSerializable
{
  private $title;
  private $isStacked;
  private $minorTicks;
  private $displayExactValues;
  private $colors;
  private $vAxis;
  private $fill;
  private $hAxis;

  public function __construct()
  {
    $this->isStacked = true;
    $this->colors = array();
  }

  public function setMinorTicks($minorTicks)
  {
    $this->minorTicks = $minorTicks;
  }

  public function setDisplayExactValues($displayExactValues)
  {
    $this->displayExactValues = $displayExactValues;
  }

  public function addColor($color)
  {
    array_push($this->colors, $color);
  }

  public function setFill($fill)
  {
    $this->fill = $fill;
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
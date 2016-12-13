<?php

class ChartDataRows implements \JsonSerializable
{
  private $c;

  public function __construct()
  {
    $this->c = array();
  }

  public function addRow($row)
  {
    array_push($this->c, $row);
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
<?php

class ChartData implements \JsonSerializable
{
  private $cols;
  private $rows;

  public function __construct()
  {
    $this->cols = array();
    $this->rows = array();
  }

  public function addCol($col)
  {
    array_push($this->cols, $col);
  }

  public function addRow($row)
  {
    array_push($this->rows, $row);
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
<?php

class ChartDataRow implements \JsonSerializable
{
  private $v;
  private $f;

  public function __construct()
  {
    $this->v = "";
    $this->f = "";
  }

  public function setV($v)
  {
    $this->v = $v;
  }

  public function setF($f)
  {
    $this->f = $f;
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
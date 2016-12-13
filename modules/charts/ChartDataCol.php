<?php

class ChartDataCol implements \JsonSerializable
{
  private $id;
  private $label;
  private $type;
  private $p;

  public function __construct()
  {
    $this->p = array();
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function setLabel($label)
  {
    $this->label = $label;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function setP($p)
  {
    $this->p = $p;
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
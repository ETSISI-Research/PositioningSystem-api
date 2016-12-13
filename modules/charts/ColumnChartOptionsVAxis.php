<?php

class ColumnChartOptionsVAxis implements \JsonSerializable
{
  private $title;
  

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}

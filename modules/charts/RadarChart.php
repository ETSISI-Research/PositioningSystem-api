<?php

class RadarChart implements \JsonSerializable
{
  private $labels;
  private $datasets;

  public function __construct()
  {
    $this->labels = array();
    $this->datasets = array();
  }

  public function addLabel($label)
  {
    array_push($this->labels, $label);
  }

  public function setLabels($labels)
  {
    $this->labels = $labels;
  }

  public function setDatasets($datasets)
  {
    $this->datasets = $datasets;
  }

  public function getLabels()
  {
    return $this->labels;
  }

  public function getDataSets()
  {
    return $this->datasets;
  }

  public function addDataset($dataset)
  {
    array_push($this->datasets, $dataset);
  }

  public function JsonSerialize()
  {
    return get_object_vars($this);
  }
}
<?php
	
require_once "ChartDataRow.php";
require_once "ChartDataRows.php";
require_once "ChartDataCol.php";
require_once "ChartData.php";
require_once "Chart.php";
require_once "RadarChartDataset.php";
require_once "RadarChart.php";
require_once "ColumnChartOptions.php";
require_once "ColumnChartOptionsVAxis.php";

$app->get('/charts/columns/:projectId', function ($projectId) use ($entityManager){
    getColumnsChartData($entityManager, $projectId);
});

$app->get('/charts/gauge', function () use ($entityManager){
        echo getGaugeChartData($entityManager);
});
$app->get('/charts/pie/:projectId', function ($projectId) use ($entityManager){
        getPieChartData($entityManager, $projectId);
});
$app->get('/charts/pie/partners/:projectId', function ($projectId) use ($entityManager){
        getPartnersPieChartData($entityManager, $projectId);
});
$app->get('/charts/pie/countries/:projectId', function ($projectId) use ($entityManager){
        getCountriesPieChartData($entityManager, $projectId);
});
$app->get('/charts/spider/:projectId', function ($projectId) use ($entityManager){
        getSpiderChartData($entityManager, $projectId);
});

$app->get('/charts/timeline/:projectId', function ($projectId) use ($entityManager){
        getTimelineChartData($entityManager, $projectId);
});

$app->get('/charts/family/timeline/:familyId', function ($familyId) use ($entityManager){
        getFamilyTimelineChartData($entityManager, $familyId);
});

$app->get('/charts/subfamily/timeline/:subfamilyId', function ($subfamilyId) use ($entityManager){
        getSubfamilyTimelineChartData($entityManager, $subfamilyId);
});


$app->get('/charts/geochart/:projectId', function ($projectId) use ($entityManager){
        getGeoChartData($entityManager, $projectId);
});

$app->get('/charts/partnersEfficiency/:projectId', function ($projectId) use ($entityManager){
        getPartnersEfficiencyChart($entityManager, $projectId);
});

function getPartnersEfficiency()
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $db = $entityManager->getRepository('Partners')->findBy(array('users' => $user[0]));

  $partners = array();
  foreach ($db as $partner) {
    $productsDb = $entityManager->getRepository('Products')->findBy(array('partner' => $partner));
    $partners = array("name" => $partner->getName());
  }

}

function getGaugeChartData($entityManager)
{
  $integrated = count(getAllProducts($entityManager, 2));
  $inProgress = count(getAllProducts($entityManager, 1));
  $notStarted = count(getAllProducts($entityManager, 0));
  $total = $integrated + $inProgress + $notStarted;
  if ($total == 0)
  {
    $integratedPercent = 0;
  }
  else
  {
    $integratedPercent = ($integrated/$total) * 100;
  }
  

  $options = new ColumnChartOptions();
  $options->setMinorTicks(5);
  $options->setDisplayExactValues(true);

  $col1 = new ChartDataCol();
  $col1->setId("integration");
  $col1->setLabel("Integration");
  $col1->setType("number");
  $row1 = new ChartDataRow();
  $row1->setV(round($integratedPercent));
  $row1->setF(round($integratedPercent) . "%");
  $rows = new ChartDataRows();
  $rows->addRow($row1);
  $gaugeChartData = new ChartData();
  $gaugeChartData->addRow($rows);
  $gaugeChartData->addCol($col1);
  $gaugeChart = new Chart("Gauge");
  $gaugeChart->setData($gaugeChartData);
  $gaugeChart->setOptions($options);
  return json_encode($gaugeChart);
}

function getAllProductsByProject($entityManager, $projectId) {

  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));

  $projects = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0]));
  $project = $projects[0];

  $products = array();
  
    $db1 = $entityManager->getRepository('Families')->findBy(array('project' => $project));
    foreach ($db1 as $family) {
      $db2 = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
      foreach ($db2 as $subfamily) {
        $db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily));
        if(isset($db3[0])){
          foreach ($db3 as $product) {
            array_push($products, $product);
          }
          
        } 
      }
    }
  return $products;
}

function getAllProductsByFamily($entityManager, $familyId)
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $familyDb = $entityManager->getRepository('Families')->findBy(array('id' => $familyId));

  $products = array();
  

  $db2 = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $familyDb[0]));
  foreach ($db2 as $subfamily) {
    $db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily));
    if(isset($db3[0])){
      foreach ($db3 as $product) {
        array_push($products, $product);
      }
    } 
  }
  return $products;
}

function getAllProductsBySubfamily($entityManager, $subfamilyId)
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $subfamilyDb = $entityManager->getRepository('Subfamilies')->findBy(array('id' => $subfamilyId));
  $products = array();
  $db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamilyDb[0]));
  if(isset($db3[0])){
    foreach ($db3 as $product) {
      array_push($products, $product);
    }
  } 
  return $products;
}

function getTimelineChartData($entityManager, $projectId)
{
  $products = getAllProductsByProject($entityManager, $projectId);
  $col1 = new ChartDataCol();
  $col1->setLabel("start");
  $col1->setType("datetime");
  $col2 = new ChartDataCol();
  $col2->setLabel("end");
  $col2->setType("datetime");
  $col3 = new ChartDataCol();
  $col3->setLabel("content");
  $col3->setType("string");
  
  $timelineChart = new ChartData();

  foreach ($products as $product)
  {
    $startYear = $product->getStartDate()->format('Y');
    $startMonth = $product->getStartDate()->format('m');
    $startDay = $product->getStartDate()->format('d');

    $endYear = $product->getEndDate()->format('Y');
    $endMonth = $product->getEndDate()->format('m');
    $endDay = $product->getEndDate()->format('d');

    $rows = new ChartDataRows();
    $dataRow1 = new ChartDataRow();
    $dataRow1->setV("Date(".$startYear.", ".$startMonth.", ".$startDay.")");
    $dataRow2 = new ChartDataRow();
    $dataRow2->setV("Date(".$endYear.", ".$endMonth.", ".$endDay.")");
    $dataRow3 = new ChartDataRow();
    $dataRow3->setV($product->getName());
    $rows->addRow($dataRow1);
    $rows->addRow($dataRow2);
    $rows->addRow($dataRow3);
   $timelineChart->addRow($rows);
  }


  
  $timelineChart->addCol($col1);
  $timelineChart->addCol($col2);
  $timelineChart->addCol($col3);
  
  echo json_encode($timelineChart);
}

function getFamilyTimelineChartData($entityManager, $familyId)
{
  $products = getAllProductsByFamily($entityManager, $familyId);
  $col1 = new ChartDataCol();
  $col1->setLabel("start");
  $col1->setType("datetime");
  $col2 = new ChartDataCol();
  $col2->setLabel("end");
  $col2->setType("datetime");
  $col3 = new ChartDataCol();
  $col3->setLabel("content");
  $col3->setType("string");
  
  $timelineChart = new ChartData();

  foreach ($products as $product)
  {
    $startYear = $product->getStartDate()->format('Y');
    $startMonth = $product->getStartDate()->format('m');
    $startDay = $product->getStartDate()->format('d');

    $endYear = $product->getEndDate()->format('Y');
    $endMonth = $product->getEndDate()->format('m');
    $endDay = $product->getEndDate()->format('d');

    $rows = new ChartDataRows();
    $dataRow1 = new ChartDataRow();
    $dataRow1->setV("Date(".$startYear.", ".$startMonth.", ".$startDay.")");
    $dataRow2 = new ChartDataRow();
    $dataRow2->setV("Date(".$endYear.", ".$endMonth.", ".$endDay.")");
    $dataRow3 = new ChartDataRow();
    $dataRow3->setV($product->getName());
    $rows->addRow($dataRow1);
    $rows->addRow($dataRow2);
    $rows->addRow($dataRow3);
   $timelineChart->addRow($rows);
  }


  
  $timelineChart->addCol($col1);
  $timelineChart->addCol($col2);
  $timelineChart->addCol($col3);
  
  echo json_encode($timelineChart);
}

function getSubfamilyTimelineChartData($entityManager, $subfamilyId)
{
  $products = getAllProductsBySubfamily($entityManager, $subfamilyId);
  $col1 = new ChartDataCol();
  $col1->setLabel("start");
  $col1->setType("datetime");
  $col2 = new ChartDataCol();
  $col2->setLabel("end");
  $col2->setType("datetime");
  $col3 = new ChartDataCol();
  $col3->setLabel("content");
  $col3->setType("string");
  
  $timelineChart = new ChartData();

  foreach ($products as $product)
  {
    $startYear = $product->getStartDate()->format('Y');
    $startMonth = $product->getStartDate()->format('m');
    $startDay = $product->getStartDate()->format('d');

    $endYear = $product->getEndDate()->format('Y');
    $endMonth = $product->getEndDate()->format('m');
    $endDay = $product->getEndDate()->format('d');

    $rows = new ChartDataRows();
    $dataRow1 = new ChartDataRow();
    $dataRow1->setV("Date(".$startYear.", ".$startMonth.", ".$startDay.")");
    $dataRow2 = new ChartDataRow();
    $dataRow2->setV("Date(".$endYear.", ".$endMonth.", ".$endDay.")");
    $dataRow3 = new ChartDataRow();
    $dataRow3->setV($product->getName());
    $rows->addRow($dataRow1);
    $rows->addRow($dataRow2);
    $rows->addRow($dataRow3);
   $timelineChart->addRow($rows);
  }

  $timelineChart->addCol($col1);
  $timelineChart->addCol($col2);
  $timelineChart->addCol($col3);
  
  echo json_encode($timelineChart);
}

function getPartnersEfficiencyChart($entityManager, $projectId)
{

  $columnChartDataCol1 = new ChartDataCol();
  $columnChartDataCol1->setType("string");

  $columnChartDataCol2 = new ChartDataCol();
  $columnChartDataCol2->setLabel("Integrated");
  $columnChartDataCol2->setType("number");

  $columnChartDataCol3 = new ChartDataCol();
  $columnChartDataCol3->setLabel("Started");
  $columnChartDataCol3->setType("number");

  $columnChartDataCol4 = new ChartDataCol();
  $columnChartDataCol4->setLabel("Not started");
  $columnChartDataCol4->setType("number");

  $columnChartData = new ChartData();
  $columnChartData->addCol($columnChartDataCol1);
  $columnChartData->addCol($columnChartDataCol2);
  $columnChartData->addCol($columnChartDataCol3);
  $columnChartData->addCol($columnChartDataCol4);

  $partnersDb = $entityManager->getRepository('Partners')->findBy(array('users' => getId()));
  foreach($partnersDb as $partner)
  {
    $integrated = count($entityManager->getRepository('Products')->findBy(array('partner' => $partner, 'status' => 2)));
    $inProgress = count($entityManager->getRepository('Products')->findBy(array('partner' => $partner, 'status' => 1)));
    $notStarted = count($entityManager->getRepository('Products')->findBy(array('partner' => $partner, 'status' => 0)));

    $total = $integrated + $inProgress + $notStarted;
    if($integrated == 0)
    {
      $integratedPercent = 0;
    }
    else
    {
      $integratedPercent = ($integrated/$total) * 100;
    }

    if($inProgress == 0)
    {
      $inProgressPercent = 0;
    }
    else
    {
      $inProgressPercent = ($inProgress/$total) * 100;
    }
            
    if($notStarted == 0)
    {
      $notStartedPercent = 0;
    }
    else
    {
      $notStartedPercent = ($notStarted/$total) * 100;
    }

    $columnChartDataRows = new ChartDataRows();
    $columnChartDataRow1 = new ChartDataRow();
    $columnChartDataRow1->setF($partner->getName());

    $columnChartDataRow2 = new ChartDataRow();
    $columnChartDataRow2->setV($integratedPercent);
    $columnChartDataRow2->setF("" . $integrated);

    $columnChartDataRow3 = new ChartDataRow();
    $columnChartDataRow3->setV($inProgressPercent);
    $columnChartDataRow3->setF("" . $inProgress);

    $columnChartDataRow4 = new ChartDataRow();
    $columnChartDataRow4->setV($notStartedPercent);
    $columnChartDataRow4->setF("" . $notStarted);

    $columnChartDataRows->addRow($columnChartDataRow1);
    $columnChartDataRows->addRow($columnChartDataRow2);
    $columnChartDataRows->addRow($columnChartDataRow3);
    $columnChartDataRows->addRow($columnChartDataRow4);
    $columnChartData->addRow($columnChartDataRows);
  }

  $columnChart = new Chart("ColumnChart");
  $columnChart->getOptions()->addColor("#1AA329");
  $columnChart->getOptions()->addColor("#1C64FF");
  $columnChart->getOptions()->addColor("#CC0000");
  $columnChart->setData($columnChartData);

  echo json_encode($columnChart);
}

function getColumnsChartData($entityManager, $projectId)
{
  $familiesObject = getFamiliesAsObject($projectId, $entityManager);
  
  $columnChartDataCol1 = new ChartDataCol();
  $columnChartDataCol1->setType("string");

  $columnChartDataCol2 = new ChartDataCol();
  $columnChartDataCol2->setLabel("Integrated");
  $columnChartDataCol2->setType("number");

  $columnChartDataCol3 = new ChartDataCol();
  $columnChartDataCol3->setLabel("In progress");
  $columnChartDataCol3->setType("number");

  $columnChartDataCol4 = new ChartDataCol();
  $columnChartDataCol4->setLabel("Not Started");
  $columnChartDataCol4->setType("number");
  
 
  $columnChartData = new ChartData();
  $columnChartData->addCol($columnChartDataCol1);
  $columnChartData->addCol($columnChartDataCol2);
  $columnChartData->addCol($columnChartDataCol3);
  $columnChartData->addCol($columnChartDataCol4);

  foreach ($familiesObject as $family)
  {
    $integrated = getFamilyProductsCount($family->id, 2, $entityManager);
    $inProgress = getFamilyProductsCount($family->id, 1, $entityManager);
    $notStarted = getFamilyProductsCount($family->id, 0, $entityManager);
    $total = $integrated + $inProgress + $notStarted;
    if($integrated == 0)
    {
      $integratedPercent = 0;
    }
    else
    {
      $integratedPercent = ($integrated/$total) * 100;
    }

    if($inProgress == 0)
    {
      $inProgressPercent = 0;
    }
    else
    {
      $inProgressPercent = ($inProgress/$total) * 100;
    }
            
    if($notStarted == 0)
    {
      $notStartedPercent = 0;
    }
    else
    {
      $notStartedPercent = ($notStarted/$total) * 100;
    }

    $columnChartDataRows = new ChartDataRows();
    $columnChartDataRow1 = new ChartDataRow();
    $columnChartDataRow1->setF($family->name);

    $columnChartDataRow2 = new ChartDataRow();
    $columnChartDataRow2->setV($integratedPercent);
    $columnChartDataRow2->setF($integrated . " products integrated");

    $columnChartDataRow3 = new ChartDataRow();
    $columnChartDataRow3->setV($inProgressPercent);
    $columnChartDataRow3->setF($inProgressPercent . " products in progress");

    $columnChartDataRow4 = new ChartDataRow();
    $columnChartDataRow4->setV($notStartedPercent);
    $columnChartDataRow4->setF($notStartedPercent . " products not started");

    $columnChartDataRows->addRow($columnChartDataRow1);
    $columnChartDataRows->addRow($columnChartDataRow2);
    $columnChartDataRows->addRow($columnChartDataRow3);
    $columnChartDataRows->addRow($columnChartDataRow4);
    $columnChartData->addRow($columnChartDataRows);

  }

  

  $columnChart = new Chart("ColumnChart");
  $columnChart->getOptions()->addColor("#1AA329");
  $columnChart->getOptions()->addColor("#1C64FF");
  $columnChart->getOptions()->addColor("#CC0000");
  $columnChart->setData($columnChartData);

  echo json_encode($columnChart);
}

function getSpiderChartData($entityManager, $projectId)
{
  $radarChart = new RadarChart();

  $families = json_decode(getFamilies($entityManager, $projectId));
  $newDataset = new RadarChartDataset();
  $newDataset->setFillColor("rgba(0,10,100,0.8)");
  foreach($families as $family)
  {

    $integrated = getProductsInFamilyWithStatus($entityManager, $family->id, 2);
    $inProgress = getProductsInFamilyWithStatus($entityManager, $family->id, 1);
    $notStarted = getProductsInFamilyWithStatus($entityManager, $family->id, 0);
    $total = $integrated + $inProgress + $notStarted;
    if($integrated == 0)
    {
      $integratedPercent = 0;
    }
    else
    {
      $integratedPercent = round(($integrated/$total) * 100);
    }

    $radarChart->addLabel($family->name);
    
    $newDataset->addData($integratedPercent);
    
  }
  $radarChart->addDataset($newDataset);
  echo json_encode($radarChart);
}

function getPieChartData($entityManager, $projectId)
{
  $integratedProducts = getProductsWithStatus($entityManager, $projectId, 2);
  $inProgressProducts = getProductsWithStatus($entityManager, $projectId, 1);
  $notStartedProducts = getProductsWithStatus($entityManager, $projectId, 0);
	$familiesObject = getFamiliesAsObject($projectId, $entityManager);

	echo "{\"type\": \"PieChart\",
        \"displayed\": true,
        \"data\": {
            \"cols\": [
              {
                  \"id\": \"legend\",
                  \"label\": \"legend\",
                  \"type\": \"string\",
                  \"p\": []
              },
              {
                  \"id\": \"productsCount\",
                  \"label\": \"productsCount\",
                  \"type\": \"number\",
                  \"p\": []
              }
            ],
      \"rows\": [
      {
        \"c\": [
          {
            \"v\": \"Integrated\"
          },
          {
            \"v\": ". $integratedProducts .",
            \"f\": \"".$integratedProducts." products\"
          }
        ]
      },
      {
        \"c\": [
          {
            \"v\": \"In progress\"
          },
          {
            \"v\": ". $inProgressProducts .",
            \"f\": \"".$inProgressProducts." products\"
          }
        ]
      },
      {
        \"c\": [
          {
            \"v\": \"Not started\"
          },
          {
            \"v\": ". $notStartedProducts .",
            \"f\": \"".$notStartedProducts." products\"
          }
        ]
      }
    ]
  },
        \"options\": {
            \"title\": \"\",
            \"isStacked\": \"true\",
            \"fill\": 20,
            \"colors\": [\"#1AA329\", \"#1C64FF\", \"#CC0000\"],
            \"displayExactValues\": true,
            \"vAxis\": {
                \"title\": \"Sales unit\",
                \"gridlines\": {
                    \"count\": 10
                }
            },
            \"hAxis\": {
                \"title\": \"Date\"
            }
        },
        \"formatters\": {}
    }";
}

function countProductsByPartnerAndProject($entityManager, $partnerId, $projectId)
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $partnerDb = $entityManager->getRepository('Partners')->findBy(array('id' => $partnerId));
  $projectDb = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId));
  $familiesDb = $entityManager->getRepository('Families')->findBy(array('project' => $projectDb[0]));

  $totalCount = 0;

  foreach ($familiesDb as $family)
  {
    $subfamiliesDb = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
    foreach ($subfamiliesDb as $subfamily)
    {
      $productsDb = $entityManager->getRepository('Products')->findBy(array('partner' => $partnerDb[0], 'subfamily' => $subfamily));
      $totalCount = $totalCount + count($productsDb);
    }
  }
  return $totalCount;
}

function countProductsByCountryAndProject($entityManager, $countryName, $projectId)
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $countryDb = $entityManager->getRepository('Countries')->findBy(array('name' => $countryName));
  $projectDb = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId));
  $familiesDb = $entityManager->getRepository('Families')->findBy(array('project' => $projectDb[0]));


  $totalCount = 0;

  foreach ($familiesDb as $family)
  {
    $subfamiliesDb = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
    foreach ($subfamiliesDb as $subfamily)
    {
      $partnersDb = $entityManager->getRepository('Partners')->findBy(array('country' => $countryDb[0]));
      foreach ($partnersDb as $partner)
      {
        $productsDb = $entityManager->getRepository('Products')->findBy(array('partner' => $partner, 'subfamily' => $subfamily));
        $totalCount = $totalCount + count($productsDb);
      }
    }
  }
  return $totalCount;
}

function getPartnersPieChartData($entityManager, $projectId)
{
  $integratedProducts = getProductsWithStatus($entityManager, $projectId, 2);
  $inProgressProducts = getProductsWithStatus($entityManager, $projectId, 1);
  $notStartedProducts = getProductsWithStatus($entityManager, $projectId, 0);
  $familiesObject = getFamiliesAsObject($projectId, $entityManager);

  $pieChart = new Chart("PieChart");
  $pieChartData = new ChartData();

  $pieChartDataCol1 = new ChartDataCol();
  $pieChartDataCol1->setId("legend");
  $pieChartDataCol1->setLabel("legend");
  $pieChartDataCol1->setType("string");
  $pieChartDataCol2 = new ChartDataCol();
  $pieChartDataCol2->setId("productsCount");
  $pieChartDataCol2->setLabel("productsCount");
  $pieChartDataCol2->setType("number");

  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $partnersDb = $entityManager->getRepository('Partners')->findBy(array('users' => $user[0]));

  foreach ($partnersDb as $partner) {
    $pieChartDataRows1 = new ChartDataRows();
    $pieChartDataRow1 = new ChartDataRow();
    $pieChartDataRow1->setV($partner->getName());
    $pieChartDataRow1->setF($partner->getName());
    $pieChartDataRow2 = new ChartDataRow();
    $count = countProductsByPartnerAndProject($entityManager,$partner->getId(),$projectId);
    $pieChartDataRow2->setV($count);
    $pieChartDataRow2->setF($count . " products");
    $pieChartDataRows1->addRow($pieChartDataRow1);
    $pieChartDataRows1->addRow($pieChartDataRow2);
    $pieChartData->addRow($pieChartDataRows1);
  }

  $pieChartData->addCol($pieChartDataCol1);
  $pieChartData->addCol($pieChartDataCol2);

  $pieChart->setData($pieChartData);
  echo json_encode($pieChart);
}

function getCountriesPieChartData($entityManager, $projectId)
{
  $integratedProducts = getProductsWithStatus($entityManager, $projectId, 2);
  $inProgressProducts = getProductsWithStatus($entityManager, $projectId, 1);
  $notStartedProducts = getProductsWithStatus($entityManager, $projectId, 0);
  $familiesObject = getFamiliesAsObject($projectId, $entityManager);

  $pieChart = new Chart("PieChart");
  $pieChartData = new ChartData();

  $pieChartDataCol1 = new ChartDataCol();
  $pieChartDataCol1->setId("legend");
  $pieChartDataCol1->setLabel("legend");
  $pieChartDataCol1->setType("string");
  $pieChartDataCol2 = new ChartDataCol();
  $pieChartDataCol2->setId("productsCount");
  $pieChartDataCol2->setLabel("productsCount");
  $pieChartDataCol2->setType("number");

  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $countriesDb = $entityManager->getRepository('Countries')->findAll();
  foreach ($countriesDb as $country) {
    $pieChartDataRows1 = new ChartDataRows();
    $pieChartDataRow1 = new ChartDataRow();
    $pieChartDataRow1->setV($country->getName());
    $pieChartDataRow1->setF($country->getName());
    $pieChartDataRow2 = new ChartDataRow();
    $count = countProductsByCountryAndProject($entityManager,$country->getName(),$projectId);
    $pieChartDataRow2->setV($count);
    $pieChartDataRow2->setF($count . " products");
    $pieChartDataRows1->addRow($pieChartDataRow1);
    $pieChartDataRows1->addRow($pieChartDataRow2);
    $pieChartData->addRow($pieChartDataRows1);
  }

  $pieChartData->addCol($pieChartDataCol1);
  $pieChartData->addCol($pieChartDataCol2);

  $pieChart->setData($pieChartData);





  echo json_encode($pieChart);
}

function getProductsInFamilyWithStatus($entityManager, $familyId, $status)
{
  $totalProducts = 0;
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));

  $familyDb = $entityManager->getRepository('Families')->findBy(array('id' => $familyId));
  $projectDb = $entityManager->getRepository('Projects')->findBy(array('id' => $familyDb[0]->getId(), 'user' => $user[0]));
  if(count($projectDb>0))
  {
    $family = $familyDb[0];
    $subFamilies = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
    foreach ($subFamilies as $subFamily)
    {
      $products = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subFamily, 'status' => $status));
       $totalProducts = $totalProducts + count($products);
    }
    
  }

  return $totalProducts;
}

function getProductsWithStatus($entityManager, $projectId, $status)
{
  $totalProducts = 0;
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $projects = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0]));

  foreach ($projects as $project)
  {
    $families = $entityManager->getRepository('Families')->findBy(array('project' => $project));
    foreach ($families as $family)
    {
      $subFamilies = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
      foreach ($subFamilies as $subFamily)
      {
        $products = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subFamily, 'status' => $status));
        $totalProducts = $totalProducts + count($products);
      }
    }
  }
  return $totalProducts;
}

function getFamilyProductsCount($familyId, $status, $entityManager)
{
	$sql = "SELECT COUNT(*) as total FROM products WHERE Subfamily_id IN 
				(SELECT id FROM subfamilies WHERE Family_id = :familyId) AND status = :status";
  $params['familyId'] = $familyId;
  $params['status'] = $status;
  $stmt = $entityManager->getConnection()->prepare($sql);
  $stmt->execute($params);
  $productsCount = $stmt->fetchAll(PDO::FETCH_OBJ);
  return $productsCount[0]->total;
}

function getFamiliesAsObject($projectId, $entityManager)
{
  $sql = "SELECT f.id, f.name, f.description, f.creationDate, p.name AS partner FROM families AS f 
          JOIN partners AS p ON (f.Partner_id = p.id) WHERE Project_Id=:projectId";
  $params['projectId'] = $projectId;
  $stmt = $entityManager->getConnection()->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll(PDO::FETCH_OBJ);
}




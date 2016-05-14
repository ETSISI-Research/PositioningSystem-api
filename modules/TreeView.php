<?php

$app->get('/treeview/:projectId', function ($projectId) use ($entityManager) {
    echo json_encode(getTreeViewFamilies($entityManager, $projectId));
});

function getTreeViewFamilies($entityManager, $projectId)
{
	$sql = "SELECT id, name, description FROM families WHERE Project_Id = :projectId";
	$params['projectId'] = $projectId;
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute($params);
	$families = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$arrayFamilies = array();
	foreach ($families as $family)
	{
        $arrayDescription = array(
            'description' => $family['description']
        );
            
		$arrayFamilies[] = array(
            "parentId" => $projectId,
            "id" => $family['id'],
			"label" => $family['name'],
            "data" => $arrayDescription,
            "type" => "family",
			"children" => getTreeViewSubFamilies($entityManager, $family['id'])
		);
				
	}
		
	return $arrayFamilies;

}

function getTreeViewSubFamilies($entityManager, $familyId)
{
	$sql = "SELECT id, name, description FROM subfamilies WHERE Family_Id = :familyId";
	$params['familyId'] = $familyId;
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute($params);
	$subfamilies = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$arraySubfamilies = array ();

	foreach ($subfamilies as $subfamily)
	{
        $arrayDescription = array (
            'description' => $subfamily['description']
        );
            
		$arraySubfamily = array(
            'parentId' => $familyId,
            'id' => $subfamily['id'],
			'label' => $subfamily['name'],
            'data' => $arrayDescription,
            'type' => "subfamily",
			'children' => getTreeViewProducts($entityManager, $subfamily['id'])
		);
		
		array_push($arraySubfamilies, $arraySubfamily);			
	}

	return $arraySubfamilies;
}

function getTreeViewProducts($entityManager, $subfamilyId)
{
	$sql = "SELECT id, name, description FROM products WHERE SubFamily_Id = :subfamilyId";
	$params['subfamilyId'] = $subfamilyId;
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute($params);
	$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$arrayProducts = array ();

	foreach ($products as $product)
	{

		$pe = $product['description'][0];
		$arrayProduct = array(
            'parentId' => $subfamilyId,
            'id' => $product['id'],
			'label' => $product['name'],
            'data' => array('description' => $pe),
            'type' => "product"
		);
		
		array_push($arrayProducts, $arrayProduct);			
	}
	
	return $arrayProducts;
}

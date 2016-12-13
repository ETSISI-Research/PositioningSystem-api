<?php

$app->get('/subfamilies/:familyId', function ($familyId) use ($entityManager) {
    echo json_encode(getSubfamilies($entityManager, $familyId));
});
$app->post('/subfamilies/:familyId', 'addSubFamily');
$app->delete('/subfamilies/:subfamilyId', function ($subfamilyId) use ($entityManager) {
    deleteSubfamily($entityManager, $subfamilyId);
});

$app->put('/subfamilies/:subfamilyId', 'updateSubFamily');
$app->get('/subfamily/:subfamilyId', function ($subfamilyId) use ($entityManager) {
    echo getSubfamily($entityManager, $subfamilyId);
});

function addSubFamily($familyId) {
	$request = Slim::getInstance()->request();
	$subFamily = json_decode($request->getBody());
	$sql = "INSERT INTO subfamilies (Family_Id, name, description, Partner_Id, creationDate) VALUES (:familyId, :name, :description, :Partner_id, CURRENT_TIMESTAMP())";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("familyId", $familyId);
		$stmt->bindParam("name", $subFamily->name);
		$stmt->bindParam("description", $subFamily->description);
        $stmt->bindParam("Partner_id", $subFamily->Partner_id);
		$stmt->execute();
		$subFamily->id = $db->lastInsertId();
		$db = null;
		echo json_encode($sql); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getSubFamilies($entityManager, $familyId) {
    $sql = "SELECT s.id, s.name, s.description, s.creationDate, p.name AS partner 
            FROM subfamilies AS s 
                JOIN partners AS p ON (s.Partner_id = p.id)
            WHERE Family_Id=:familyId";
	
	$params['familyId'] = $familyId;
  	$stmt = $entityManager->getConnection()->prepare($sql);
  	$stmt->execute($params);
  	return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function updateSubFamily($subFamilyId) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$subFamily = json_decode($body);
	$sql = "UPDATE subfamilies SET name=:name, description=:description, Partner_Id=:Partner_id WHERE id=:subFamilyId";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $subFamily->name);
		$stmt->bindParam("description", $subFamily->description);
        $stmt->bindParam("Partner_id", $subFamily->Partner_id);
		$stmt->bindParam("subFamilyId", $subFamilyId);
		$stmt->execute();
		$db = null;
		echo json_encode($subFamily); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getSubfamily($entityManager, $subfamilyId) {
    $sql = "SELECT s.id, s.name, s.description, s.creationDate, p.name AS partner, s.Family_id, s.Partner_id, f.Project_id,
            f.name AS Family, pr.name AS Proyecto
            FROM subfamilies AS s 
                JOIN partners AS p ON (s.Partner_id = p.id)
                JOIN families AS f ON (s.Family_id = f.id)
                JOIN projects AS pr ON (f.Project_id = pr.id)
            WHERE s.id=:subfamilyId";


	$params['subfamilyId'] = $subfamilyId;
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute($params);
	$subfamily = $stmt->fetchAll(PDO::FETCH_OBJ);
	
	echo json_encode($subfamily[0]); 
	
}

function deleteSubfamily($entityManager, $subfamilyId) {
		$db2 = $entityManager->getRepository('Subfamilies')->findBy(array('id' => $subfamilyId));
		foreach ($db2 as $subfamily) {
			$db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily));
			foreach ($db3 as $product) {
				$entityManager->remove($product);
				$entityManager->flush();
			}
			$entityManager->remove($subfamily);
			$entityManager->flush();	
		}
}
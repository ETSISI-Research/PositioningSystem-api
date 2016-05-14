<?php

$app->get('/family/:id', function ($id) use ($entityManager){
    getFamily($id, $entityManager);
});

$app->get('/families/:projectId', function ($projectId) use ($entityManager){
        echo getFamilies($entityManager, $projectId);
});

$app->post('/families/:projectId', 'addFamily');
$app->put('/families/:id', 'updateFamily');

$app->delete('/families/:familyId', function ($familyId) use ($entityManager){
        deleteFamily($entityManager, $familyId);
});

function getFamilies($entityManager, $projectId)
{
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projects = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId, 'user' => $user[0]));

	if(count($projects)>0)
	{
		$familiesDb = $entityManager->getRepository('Families')->findBy(array('project' => $projects[0]));
		$families = array();
		foreach ($familiesDb as $family) {
			$families[] = array(
				"id" => $family->getId(),
				"name" => $family->getName(),
				"description" => $family->getDescription(),
				"partnerName" => $family->getPartner()->getName(),
				"partnerId" =>$family->getPartner()->getId()
			);
		}

	}
	$project = $entityManager->find('Projects', $projectId);
	$privileges = $entityManager->getRepository('Privileges')->findBy(array('privilegesUsers' => $user[0], 'projects' => $project));
	if(count($privileges)>0)
	{
		$familiesDb = $entityManager->getRepository('Families')->findBy(array('project' => $project));
		foreach ($familiesDb as $family) {
		$families[] = array(
			"id" => $family->getId(),
			"name" => $family->getName(),
			"description" => $family->getDescription()
		);
	}
	}

	return json_encode($families);
}



function addFamily($projectId) {
	$request = Slim::getInstance()->request();
	$family = json_decode($request->getBody());
	$sql = "INSERT INTO families (Project_Id, name, description, Partner_Id, creationDate) VALUES (:projectId, :name, :description, :Partner_id, CURRENT_TIMESTAMP())";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("projectId", $projectId);
		$stmt->bindParam("name", $family->name);
		$stmt->bindParam("description", $family->description);
		$stmt->bindParam("Partner_id", $family->Partner_id);
		$stmt->execute();
		$family->id = $db->lastInsertId();
		$db = null;
		echo json_encode($sql);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}


/*	$sql = "INSERT INTO families (project_id, name, description, Partner_Id, creationDate) VALUES (:projectId, :name, :description, :Partner_id, CURRENT_TIMESTAMP())";
	$params['project_id'] = $projectId;
	$params['name'] = $name;
	$params['to'] = $to;
	$params['date'] = $date;
	$stmt = $entityManager->getConnection()->prepare($sql);
	echo json_encode(array("result"=>$stmt->execute($params)));*/
}

function getFamily($id, $entityManager) {
	$sql = "SELECT f.id, f.name, f.description, f.creationDate, p.name AS partner, f.Project_id, f.Partner_id, f.Project_id,
                pr.name AS Proyecto FROM families AS f JOIN partners AS p ON (f.Partner_id = p.id)
                JOIN projects AS pr ON (f.Project_id = pr.id) WHERE f.id=:id";
	$params['id'] = $id;
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute($params);
	echo json_encode($stmt->fetchAll()[0]);
}

function updateFamily($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$family = json_decode($body);
	$sql = "UPDATE families SET name=:name, description=:description, Partner_Id=:Partner_id, active=:active WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $family->name);
		$stmt->bindParam("description", $family->description);
		$stmt->bindParam("Partner_id", $family->Partner_id);
		$stmt->bindParam("active", $family->active);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($family);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deleteFamily($entityManager, $familyId) {
	$db1 = $entityManager->getRepository('Families')->findBy(array('id' => $familyId));
	foreach ($db1 as $family) {
		$db2 = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
		foreach ($db2 as $subfamily) {
			$db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily));
			foreach ($db3 as $product) {
				$entityManager->remove($product);
				$entityManager->flush();
			}
			$entityManager->remove($subfamily);
			$entityManager->flush();
		}
		$entityManager->remove($family);
		$entityManager->flush();
	}
}

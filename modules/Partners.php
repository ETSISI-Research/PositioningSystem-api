<?php

$app->get('/partners', function () use ($entityManager){
        getPartners($entityManager);
});
$app->post('/partners', function () use ($entityManager){
        addPartner($entityManager);
});
$app->get('/partners/:partnerId',	function ($partnerId) use ($entityManager){
	getPartner($entityManager, $partnerId);
});
$app->put('/partners/:id', 'updatePartner');
$app->delete('/partners/:id', 'deletePartner');

function getPartners($entityManager) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Partners')->findBy(array('users' => $user[0]));

	$partners = array();
	foreach ($db as $partner) {
	    $partners[] = array(
		"id" => $partner->getId(),
		"name" => $partner->getName(),
		"description" => $partner->getDescription(),
		"creationDate" => $partner->getCreationDate(),
		"countryName" => $partner->getCountry()->getName(),
		"countryCode" => $partner->getCountry()->getCode()
		);
	}

	echo json_encode($partners);
}

function getPartner($entityManager, $partnerId) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$partnerDb = $entityManager->getRepository('Partners')->findBy(array('id' => $partnerId, 'users' => $user[0]));
	$partner = $partnerDb[0];
	$partner = array(
		"id" => $partner->getId(),
		"name" => $partner->getName(),
		"image" => $partner->getImage(),
		"description" => $partner->getDescription(),
		"creationDate" => $partner->getCreationDate(),
		"countryName" => $partner->getCountry()->getName(),
		"countryCode" => $partner->getCountry()->getCode()
		);



	echo json_encode($partner);
}

function addPartner($entityManager) {

	try{
		$request = Slim::getInstance()->request();
		$input = json_decode($request->getBody());

		$partner = new Partners();
		$partner->setName($input->name);
		$partner->setDescription($input->description);
		$partner->setCreationDate(new DateTime());

		$country = $entityManager->getRepository('Countries')->findBy(array('id' => $input->country));
		$users = $entityManager->getRepository('Users')->findBy(array('id' => getId()));

		$partner->setCountry($country[0]);
		$partner->setUsers($users[0]);

		$entityManager->persist($partner);
		$entityManager->flush();
	}
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function updatePartner($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$partner = json_decode($body);
	$sql = "UPDATE partners SET name=:name, description=:description, Country_id=:Country_id WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $partner->name);
		$stmt->bindParam("description", $partner->description);
		$stmt->bindParam("Country_id", $partner->Country_id);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($partner);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deletePartner($id) {
	$sql = "DELETE FROM partners WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

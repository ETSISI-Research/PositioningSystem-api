<?php
$app->get('/countries', function () use ($entityManager){
	getCountries($entityManager);
});

$app->delete('/countries/:id', 'deleteCountry');

function getCountries($entityManager) {
	


	$sql = "SELECT * FROM countries ORDER BY id";
	$stmt = $entityManager->getConnection()->prepare($sql);
	$stmt->execute();
	echo json_encode($stmt->fetchAll());
}

function getCountry($id) {
	$sql = "SELECT * FROM countries WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$country = $stmt->fetchObject();  
		$db = null;
		echo json_encode($country); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addCountry() {
	$request = Slim::getInstance()->request();
	$country = json_decode($request->getBody());
	$sql = "INSERT INTO countries (name, creationDate) VALUES (:name, CURRENT_TIMESTAMP())";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $country->name);
		$stmt->execute();
		$country->id = $db->lastInsertId();
		$db = null;
		echo json_encode($sql); 
	} catch(PDOException $e) {
		error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updateCountry($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$country = json_decode($body);
	$sql = "UPDATE countries SET name=:name, active=:active WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $country->name);
		$stmt->bindParam("active", $country->active);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($country); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}     

function deleteCountry($id) {
	$sql = "UPDATE countries SET active = 0 WHERE id=:id";
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

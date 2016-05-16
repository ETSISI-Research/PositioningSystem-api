<?php

$app->get('/products/:subfamilyId', function ($subfamilyId) use ($entityManager) {
        getProducts($entityManager, $subfamilyId);
    });
$app->post('/products/:subFamilyId', 'addProduct');
$app->delete('/products/:productId', function ($productId) use ($entityManager) {
    deleteProduct($entityManager, $productId);
});

$app->put('/products/:productId', function ($productId) use ($entityManager){
    updateProduct($entityManager, $productId);
 });

$app->get('/product/:productId', function ($productId) use ($entityManager){
    getProduct($entityManager, $productId);
 });
$app->get('/products/status/:status', function ($status) use ($entityManager) {
        echo json_encode(getAllProducts($entityManager, $status));
    });

function getProducts($entityManager, $subfamilyId) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$subfamily = $entityManager->getRepository('Subfamilies')->findBy(array('id' => $subfamilyId));
  	$productsDb = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily[0]));
	$products = array();
  	foreach ($productsDb as $product)
  	{
  		$products[] = array(
  			"id" => $product->getId(),
			"name" => $product->getName(),
			"description" => $product->getDescription(),
			"creationDate" => $product->getCreationDate(),
			"status" => $product->getStatus(),
			"partner" => $product->getPartner()->getName()
		);
  	}
  	echo json_encode($products);
}

function getAllProducts($entityManager, $status) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));

	$db = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0]));
	$products = array();
	foreach ($db as $project) {
		$db1 = $entityManager->getRepository('Families')->findBy(array('project' => $project));
		foreach ($db1 as $family) {
			$db2 = $entityManager->getRepository('Subfamilies')->findBy(array('family' => $family));
			foreach ($db2 as $subfamily) {
				$db3 = $entityManager->getRepository('Products')->findBy(array('subfamily' => $subfamily, "status" => $status));
				foreach ($db3 as $product) {
					$productx = array(
		                "id" => $product->getId(),
		                'name' => $product->getName(),
		                'description' => $product->getDescription(),
		                'partnerName' => $product->getPartner()->getName(),
		               	'partnerId' => $product->getPartner()->getId(),
		               	'subfamilyId' => $product->getSubfamily()->getId(),
		               	'creationDate' => $product->getCreationDate()
					);
					array_push($products, $productx);
				}
			}
		}
	}

	return $products;
}

function getProduct($entityManager, $productId) {
	$productDb = $entityManager->getRepository('Products')->findBy(array('id' => $productId));
	$product[] = array(
  		"id" => $productDb[0]->getId(),
		"name" => $productDb[0]->getName(),
		"description" => $productDb[0]->getDescription(),
		"creationDate" => $productDb[0]->getCreationDate(),
		"status" => $productDb[0]->getStatus(),
		"partner" => $productDb[0]->getPartner()->getName(),
		"subfamilyId" => $productDb[0]->getSubfamily()->getId(),
		"subfamilyName" => $productDb[0]->getSubfamily()->getName(),
		"familyId" => $productDb[0]->getSubfamily()->getFamily()->getId(),
		"familyName" => $productDb[0]->getSubfamily()->getFamily()->getName(),
		"projectId" => $productDb[0]->getSubfamily()->getFamily()->getProject()->getId(),
		"projectName" => $productDb[0]->getSubfamily()->getFamily()->getProject()->getName(),
		"startDate" => $productDb[0]->getStartDate(),
		"endDate" => $productDb[0]->getEndDate()
	);
	echo json_encode($product[0]);
}

function addProduct($subFamilyId) {
	$request = Slim::getInstance()->request();
	$product = json_decode($request->getBody());
	$sql = "INSERT INTO products (SubFamily_Id, name, description, Partner_Id, creationDate) VALUES (:subFamilyId, :name, :description, :Partner_id, CURRENT_TIMESTAMP())";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("subFamilyId", $subFamilyId);
		$stmt->bindParam("name", $product->name);
		$stmt->bindParam("description", $product->description);
        $stmt->bindParam("Partner_id", $product->Partner_id);
		$stmt->execute();
		$product->id = $db->lastInsertId();
		$db = null;
		echo json_encode($sql);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function updateProduct($entityManager, $productId) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$product = json_decode($body);
	$sql = "UPDATE products SET name=:name, description=:description, Partner_Id=:Partner_id, status=:status, startDate=:startDate, endDate=:endDate WHERE id=:productId";
	$stmt = $entityManager->getConnection()->prepare($sql);
	$params['name'] = $product->name;
	$params['description'] = $product->description;
	$params['Partner_id'] = $product->Partner_id;
	$params['status'] = $product->status;
	$params['productId'] = $productId;
	$params['startDate'] = $product->startDate->date;
	$params['endDate'] = $product->endDate->date;
	$stmt->execute($params);
	echo '{"success":{"message":"Product updated successfully"}}';
}

function deleteProduct($entityManager, $productId) {
	$product = $entityManager->getRepository('Products')->findBy(array('id' => $productId));
	$entityManager->remove($product[0]);
	$entityManager->flush();
}

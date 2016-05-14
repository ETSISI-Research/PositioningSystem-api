<?php

$app->get('/users/:id',	function() use ($entityManager){
	// TODO
	// WTF am I using this session
	$userDb = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$user = array(
		"id" => $userDb[0]->getId(),
		"name" => $userDb[0]->getName(),
		"lastName" => $userDb[0]->getLastName(),
		"email" => $userDb[0]->getEmail()
	);
	echo json_encode($user);
});

$app->post('/users', function(){

});

$app->put('/users/:id', function() use ($entityManager){
	// TODO
	// WTF am I using this session
	$userDb = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$name = $body->name;
	$lastName = $body->lastName;
	$userDb[0]->setName($name);
	$userDb[0]->setLastName($lastName);
	$entityManager->flush();
	echo '{"success":{"message":"User updated successfully"}}';
});

$app->delete('/users/:id', function(){

});

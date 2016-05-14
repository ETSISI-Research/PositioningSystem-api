<?php

$app->get('/contacts', function () use ($entityManager){
        getContacts($entityManager);
});

$app->get('/contacts/requests', function () use ($entityManager){
        getContactREquests($entityManager);
});

$app->post('/contacts/add/:email',	function () use ($entityManager){
        contactInvite($entityManager);
});

$app->post('/contacts/accept/:id',	function ($id) use ($entityManager){
        contactAccept($entityManager, $id);
});

function getContacts($entityManager) {

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Contacts')->findBy(array('users' => $user[0], 'status' => 1));
	$db2 = $entityManager->getRepository('Contacts')->findBy(array('users1' => $user[0], 'status' => 1));
	$contacts = array();
	foreach ($db as $contact) {
		$contacts[] = array(
			"id" => $contact->getUsers1()->getId(),
			"name" => $contact->getUsers1()->getName(),
			"lastName" => $contact->getUsers1()->getLastName(),
			"email" => $contact->getUsers1()->getEmail(),
		);
	}
	foreach ($db2 as $contact) {
		$contacts[] = array(
			"id" => $contact->getUsers()->getId(),
			"name" => $contact->getUsers()->getName(),
			"lastName" => $contact->getUsers()->getLastName(),
			"email" => $contact->getUsers()->getEmail(),
		);
	}
	echo json_encode($contacts);
}

function getContactRequests($entityManager) {

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Contacts')->findBy(array('users1' => $user[0], 'status' => 0));
	
	$contacts = array();
	foreach ($db as $contact) {
		$contacts[] = array(
			"id" => $contact->getUsers()->getId(),
			"name" => $contact->getUsers()->getName(),
			"lastName" => $contact->getUsers()->getLastName(),
			"email" => $contact->getUsers()->getEmail(),
		);
	}
	
	echo json_encode($contacts);
}

function contactInvite($entityManager) {
	$request = Slim::getInstance()->request();
	$contact = json_decode($request->getBody());
	$email = $contact->email;
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$invited = $entityManager->getRepository('Users')->findBy(array('email' => $email));
	$contacts = $entityManager->getRepository('Contacts')->findBy(array('users' => $user[0], 'users1' => $invited[0]));

	try{
		if(count($contacts) == 0)
		{
			$contact = new Contacts();
			$contact->setUsers($user[0]);
			$contact->setUsers1($invited[0]);
			$contact->setStatus(0);
			$contact->setCreationDate(new DateTime());

			$entityManager->persist($contact);
			$entityManager->flush();
			echo '{"success":{"message":"Invitation sent"}}';
		}
		else
		{
			echo '{"error":{"message":"Invitation has already been sent"}}';
		}

	}
	catch(PDOException $e) {
		echo '{"error":{"message":"Error"}}'; 
	}
	catch(Exception $e)
	{
		echo '{"error":{"message":"Error"}}'; 
	}
}

function contactAccept($entityManager, $id) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$invited = $entityManager->getRepository('Users')->findBy(array('id' => $id));

	$contacts = $entityManager->getRepository('Contacts')->findBy(array('users1' => $user[0], 'users' => $invited[0]));

	try{
		echo getId() . " - " . $id;
		if(count($contacts) > 0)
		{
			$contacts[0]->setStatus(1);
			$entityManager->persist($contacts[0]);
			$entityManager->flush();
		}
	}
	catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
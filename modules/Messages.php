<?php

$app->get('/messages', function () use ($entityManager){
        getMessages($entityManager);
});

$app->get('/messages/unread', function () use ($entityManager){
        getUnreadMessages($entityManager);
});

$app->get('/messages/markAsReaded/:messageId', function ($messageId) use ($entityManager){
        markAsReaded($entityManager, $messageId);
});

$app->get('/messages/:messageId', function ($messageId) use ($entityManager){
        getMessage($entityManager, $messageId);
});


$app->post('/messages/send/:contactId', function ($contactId) use ($entityManager){
        sendMessage($entityManager, $contactId);
});


function markAsReaded($entityManager, $messageId)
{
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$messageDb = $entityManager->getRepository('Messages')->findBy(array('usersReceiverid' => $user[0], 'id' => $messageId));
	$messageDb[0]->setReaded(1);
	$entityManager->flush();
}

function getMessage($entityManager, $messageId) {

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$messageDb = $entityManager->getRepository('Messages')->findBy(array('usersReceiverid' => $user[0], 'id' => $messageId));
	$message = array(
		"id" => $messageDb[0]->getId(),
		"creationDate" => $messageDb[0]->getCreationDate(),
		"subject" => $messageDb[0]->getSubject(),
		"message" => $messageDb[0]->getMessage(),
		"contactName" => $messageDb[0]->getSender()->getName(),
		"contactEmail" => $messageDb[0]->getSender()->getEmail(),
		"date" => $messageDb[0]->getCreationDate(),
		"senderId" => $messageDb[0]->getSender()->getId()
	);
	echo json_encode($message);
}

function getMessages($entityManager) {

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Messages')->findBy(array('usersReceiverid' => $user[0]));
	$messages = array();
	foreach ($db as $message) {
		$messages[] = array(
			"id" => $message->getId(),
			"subject" => $message->getSubject(),
			"contactName" => $message->getSender()->getName(),
			"message" => $message->getMessage(),
			"contactEmail" => $message->getSender()->getEmail(),
			"date" => $message->getCreationDate(),
			"senderId" => $message->getSender()->getId()
		);
	}
	echo json_encode($messages);
}

function getUnreadMessages($entityManager) {

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Messages')->findBy(array('usersReceiverid' => $user[0], 'readed' => 0));
	$messages = array();
	foreach ($db as $message) {
		$messages[] = array(
			"id" => $message->getId(),
			"subject" => $message->getSubject(),
			"message" => $message->getMessage(),
			"contactName" => $message->getSender()->getName(),
			"contactEmail" => $message->getSender()->getEmail(),
			"date" => $message->getCreationDate(),
			"senderId" => $message->getSender()->getId()
		);
	}
	echo json_encode($messages);
}

function sendMessage($entityManager, $contactId)
{
	$request = Slim::getInstance()->request();
	$messageContent = json_decode($request->getBody());
	$subject = $messageContent->subject;
	$messageBody = $messageContent->message;
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$recipient = $entityManager->getRepository('Users')->findBy(array('id' => $contactId));

	$message = new Messages();
	$message->setMessage($messageBody);
	$message->setSubject($subject);
	$message->setSender($user[0]);
	$message->setReceiver($recipient[0]);
	$message->setCreationDate(new DateTime());

	$entityManager->persist($message);
	$entityManager->flush();
}
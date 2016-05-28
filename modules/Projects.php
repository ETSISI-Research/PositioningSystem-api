<?php

$app->get('/projects/associates/:projectId', function ($projectId) use ($entityManager){
        getAssociates($entityManager, $projectId);
});

$app->get('/projects/others', function () use ($entityManager){
        getOthersProjects($entityManager);
});

$app->get('/projects/others/:id', function ($id) use ($entityManager){
        getOthersProject($entityManager, $id);
});

$app->get('/projects/:id',	function ($id) use ($entityManager){
        getProject($entityManager, $id);
});

$app->post('/projects/snapshot',	function () use ($entityManager){
        takeSnapshot($entityManager);
});

$app->get('/projects/spider/:projectId/:snapshotId', function ($projectId, $snapshotId) use ($entityManager){
        echo json_encode(getSnapshotData($entityManager, $projectId, $snapshotId));
});

$app->get('/projects/spider/:projectId/:snapshotId/:compareId', function ($projectId, $snapshotId, $compareId) use ($entityManager){
	echo json_encode(getCompareSnapshotData($entityManager, $projectId, $snapshotId, $compareId));
});

$app->post('/projects/invite/:projectId',	function ($projectId) use ($entityManager){
        projectInvite($entityManager, $projectId);
});

$app->get('/projects', function () use ($entityManager){
    echo json_encode(getProjects($entityManager));
});
$app->post('/projects', function () use ($entityManager){
        addProject($entityManager);
});

$app->put('/projects/:id', 'updateProject');


$app->delete('/projects/:id', function ($id) use ($entityManager){
        deleteProject($entityManager, $id);
});

$app->get('/projectsCount', function () use ($entityManager){
        getProjectsCount($entityManager);
});

function getAssociates($entityManager, $projectId)
{
  	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  	$projectDb = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId, 'user' => $user[0]->getId()));
	$privileges = $entityManager->getRepository('Privileges')->findBy(array('projects' => $projectDb[0]));
	if (count($privileges) > 0)
	{
		foreach ($privileges as $privilege)
		{
			$user = $entityManager->find('Users', $privilege->getPrivilegesUsers()->getId());
			$users[] = array(
				"id" => $user->getId(),
				"name" => $user->getName(),
				"lastName" => $user->getLastName(),
				"email" => $user->getEmail()
			);
		}
		echo json_encode($users);
	}
}

function getSnapshotData($entityManager, $projectId, $snapshotId)
{
  $user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
  $projects = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId, 'user' => $user[0]->getId()));
  $snapshot = $entityManager->getRepository('Snapshots')->findBy(array('id' => $snapshotId, 'projects' => $projects[0]));

  $radarChart = new RadarChart();
  $data = json_decode($snapshot[0]->getData());
  $radarChart->setLabels($data->labels);
  $radarChart->setDatasets($data->datasets);
  return $radarChart;
}

function getCompareSnapshotData($entityManager, $projectId, $snapshotId, $compareId)
{
	$radar =  getSnapshotData($entityManager, $projectId, $snapshotId);
	$compareRadar = getSnapshotData($entityManager, $projectId, $compareId);

	//$radar->addLabel($compareRadar->getLabels());
	//$radar->addDataset($compareRadar->getDataSets());
	return $compareRadar;
}

function takeSnapshot($entityManager)
{
	$request = Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$id = $body->id;
	$name = $body->name;
	$description = $body->description;

	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projects = $entityManager->getRepository('Projects')->findBy(array('id' => $id, 'user' => $user[0]));
	$project = $projects[0];

	$radarChart = new RadarChart();

	  $families = json_decode(getFamilies($entityManager, $id));
	  $newDataset = new RadarChartDataset();
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
	      $integratedPercent = ($integrated/$total) * 100;
	    }

	    $radarChart->addLabel($family->name);
	    $newDataset->addData($integratedPercent);
	  }
	  $radarChart->addDataset($newDataset);
	  $dataset1 = new RadarChartDataset();
	  $dataset2 = new RadarChartDataset();
	  $dataset2->setFillColor("rgba(0,0,200,0.4)");
	$snapshot = new Snapshots();
	$snapshot->setName($name);
	$snapshot->setDescription($description);
	$snapshot->setCreationdate(new DateTime());
	$snapshot->setData(json_encode($radarChart));
	$snapshot->setProjects($project);

	$entityManager->persist($snapshot);
	$entityManager->flush();

	echo json_encode($radarChart);
}

function getProjects($entityManager) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projectsDb = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0]));
	$projects = array();
	foreach ($projectsDb as $project)
	{
		$projects[] = array(
			"id" => $project->getId(),
			"name" => $project->getName(),
			"description" => $project->getDescription(),
			"image" => $project->getImage(),
			"partnerId" => $project->getPartner()->getId(),
			"partner" => $project->getPartner()->getName(),
			"creationDate" => $project->getCreationDate()
		);
	}
	return $projects;
}

function getOthersProjects($entityManager) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$privileges = $entityManager->getRepository('Privileges')->findBy(array('privilegesUsers' => $user[0]));

	if (count($privileges) > 0)
	{
		foreach ($privileges as $privilege)
		{
			$project = $entityManager->find('Projects', $privilege->getProjects()->getId());
			$projects[] = array(
				"id" => $project->getId(),
				"name" => $project->getName(),
				"description" => $project->getDescription(),
				"image" => $project->getImage(),
				"partnerId" => $project->getPartner()->getId(),
				"partner" => $project->getPartner()->getName(),
				"creationDate" => $project->getCreationDate()
			);
		}
		echo json_encode($projects);
	}
}

function projectInvite($entityManager, $projectId) {
	$request = Slim::getInstance()->request();
	$contact = json_decode($request->getBody());
	$email = $contact->email;
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$invited = $entityManager->getRepository('Users')->findBy(array('email' => $email));
	$project = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId));


	if( count($user) > 0 && count($invited) && count($project) )
	{
		$privileges = $entityManager->getRepository('Privileges')->findBy(array('privilegesUsers' => $invited[0], 'projects' => $project[0]));
		if(count($privileges)==0)
		{
			try{
				$privileges = new Privileges();
				$privileges->setAccessLevel(1);
				$privileges->setProjects($project[0]);
				$privileges->setPrivilegesUsers($invited[0]);

				$entityManager->persist($privileges);
				$entityManager->flush();
				echo '{"success":{"message":"Invitation sent"}}';
			}
			catch(PDOException $e) {
				echo '{"error":{"message":'. $e->getMessage() .'}}';
			}
		}
		else
		{
			echo '{"error":{"message":"This user has already been invited"}}';
		}
	}
	else
	{
		echo '{"error":{"message":"Invitation failed"}}';
	}
}

function getProjectsCount($entityManager)
{
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$db = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0]));
	echo json_encode(count($db));
}

function getProject($entityManager, $id) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projects = $entityManager->getRepository('Projects')->findBy(array('user' => $user[0], 'id' => $id));

	if (count($projects) > 0)
	{
		$project = array(
			"id" => $projects[0]->getId(),
			"name" => $projects[0]->getName(),
			"description" => $projects[0]->getDescription(),
			"image" => $projects[0]->getImage(),
			"partnerId" => $projects[0]->getPartner()->getId(),
			"partner" => $projects[0]->getPartner()->getName(),
			"creationDate" => $projects[0]->getCreationDate()
		);
		echo json_encode($project);
	}
	else
	{
		$project = $entityManager->find('Projects', $id);
		$privileges = $entityManager->getRepository('Privileges')->findBy(array('privilegesUsers' => $user[0], 'projects' => $project));
		if(count($privileges)>0)
		{
			$project = array(
				"id" => $privileges[0]->getProjects()->getId(),
				"name" => $privileges[0]->getProjects()->getName(),
				"description" => $privileges[0]->getProjects()->getDescription(),
				"image" => $privileges[0]->getProjects()->getImage(),
				"partnerId" => $privileges[0]->getProjects()->getPartner()->getId(),
				"partner" => $privileges[0]->getProjects()->getPartner()->getName(),
				"creationDate" => $privileges[0]->getProjects()->getCreationDate()
			);
			echo json_encode($project);
		}

	}


}

function getOthersProject($entityManager, $id) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projects = $entityManager->getRepository('Projects')->findBy(array('id' => $id));

	$privileges = $entityManager->getRepository('Privileges')->findBy(array('privilegesUsers' => $user[0], 'projects' => $projects[0]));

	if (count($privileges) > 0)
	{
		$project = array(
			"id" => $projects[0]->getId(),
			"name" => $projects[0]->getName(),
			"description" => $projects[0]->getDescription(),
			"image" => $projects[0]->getImage(),
			"partnerId" => $projects[0]->getPartner()->getId(),
			"partner" => $projects[0]->getPartner()->getName(),
			"creationDate" => $projects[0]->getCreationDate()
		);
		echo json_encode($project);
	}
}

function addProject($entityManager) {
	$request = \Slim\Slim::getInstance()->request();
	$input = $request->post();

	$partner = $entityManager->getRepository('Partners')->findBy(array('id' => $input['partnerId']));
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));

	$project = new Projects();
	$project->setName($input['name']);
	$project->setDescription($input['description']);
	$project->setCreationDate(new DateTime());
	$project->setPartner($partner[0]);
	$project->setUser($user[0]);

	$entityManager->persist($project);
	$entityManager->flush();
}

function updateProject($id) {
	$request = Slim::getInstance()->request();
	$body = $request->getBody();
	$project = json_decode($body);
	$sql = "UPDATE projects SET name=:name, description=:description, Partner_Id=:Partner_id WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("name", $project->name);
		$stmt->bindParam("description", $project->description);
        $stmt->bindParam("Partner_id", $project->Partner_id);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($project);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}
}

function deleteProject($entityManager, $id) {
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$project = $entityManager->getRepository('Projects')->findBy(array('id' => $id));

	$db1 = $entityManager->getRepository('Families')->findBy(array('project' => $project[0]));
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
	$entityManager->remove($project[0]);
	$entityManager->flush();
}

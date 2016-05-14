<?php

$app->get('/snapshots/:projectId', function ($projectId) use ($entityManager){
        getSnapshots($entityManager, $projectId);
});


function getSnapshots($entityManager, $projectId)
{
	$user = $entityManager->getRepository('Users')->findBy(array('id' => getId()));
	$projects = $entityManager->getRepository('Projects')->findBy(array('id' => $projectId));
	if(count($projects)>0)
	{
		$snapshots = $entityManager->getRepository('Snapshots')->findBy(array('projects' => $projects[0]));

	}
	echo json_encode($snapshots);
}
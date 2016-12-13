<?php
require 'vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Slim\Middleware\HttpBasicAuthentication\PdoAuthenticator;

$isDevMode = true;
$srcPaths = array(__DIR__."/src");
$config = Setup::createAnnotationMetadataConfiguration($srcPaths, $isDevMode, "data/DoctrineORMModule/Proxy", null, false);

$conn = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'dbname'   => 'ps',
    'user'     => 'root',
    'password' => ''
);

$entityManager = EntityManager::create($conn, $config);

$app = new Slim\Slim();

require 'modules/Countries.php';
require 'modules/Products.php';
require 'modules/SubFamilies.php';
require 'modules/TreeView.php';
require 'modules/Users.php';
require 'modules/Messages.php';
require 'modules/Partners.php';
require 'modules/charts/Charts.php';
require 'modules/Projects.php';
require 'modules/Families.php';
require 'modules/Contacts.php';
require 'modules/Snapshots.php';

$pdo = getPDO();
/*$app->add(
	new \Slim\Middleware\HttpBasicAuthentication([
	    "secure" => false,
	    "relaxed" => ["localhost"],
	    "authenticator" => new PdoAuthenticator([
	        "pdo" => $pdo
	    ])
	]));*/

function getPDO()
{
  //return new \PDO("mysql:host=localhost;port=3306;dbname=ps",'root','');
}

function getId()
{

    if(isset($_SESSION['id'])) {
        return $_SESSION['id'];
    }
    else {
        return 1;
    }
	/*$email = 'imponet@test.com';
  $pdo = getPDO();
  $statement = $pdo->prepare("SELECT id FROM users WHERE email = :email");
  //$statement->bindParam('email', $_SERVER['PHP_AUTH_USER']);
	$statement->bindParam('email', $email);
  $statement->execute();
  return $statement->fetch(PDO::FETCH_OBJ)->id;*/
}

function loginUser($entityManager, $email, $password)
{
	$repository = $entityManager->getRepository('Users');
	$dbUser = $repository->findBy(array('email' => $email, 'password' => md5($password)));
	if (empty($dbUser)) {
		echo json_encode(array("response" => "error"));
	}
	else
	{
		$_SESSION['id'] = $dbUser[0]->getId();
		echo json_encode(array("response" => "success"));
	}
}

$app->get('/test',	function (){
	echo "test";
});

$app->get('/authenticate',	function () use ($entityManager) {
    echo "success";
});

$app->post('/authenticate',	function () use ($entityManager) {
    $request = \Slim\Slim::getInstance()->request();
    $input = $request->post();
    loginUser($entityManager, $input['email'], $input['password']);
});

$app->get('/signup/:firstName/:lastName/:email/:password',	function ($firstName, $lastName, $email, $password) use ($entityManager) {
        signUp($entityManager, $firstName, $lastName, $email, $password);
});




$app->post('/upload/:folder', function ($folder) use ($app) {
        uploadImage($app, $folder);
    });

$app->run();

function uploadImage($app, $folder)
{

	$flowIdentifier = $app->request()->post('flowIdentifier');
	$flowChunkNumber = $app->request()->post('flowChunkNumber');
	$flowFilename = $app->request()->post('flowFilename');
	$flowRelativePath = $app->request()->post('flowRelativePath');
	$flowIdentifier = $app->request()->post('flowIdentifier');
	$flowFolder = $app->request()->post('flowFolder');
	$file_ext = "";

	$fname = $flowFilename;
	if(isset($_FILES['file'])){
		//The error validation could be done on the javascript client side.
		$errors= array();
		$file_name = $_FILES['file']['name'];
		$file_size =$_FILES['file']['size'];
		$file_tmp =$_FILES['file']['tmp_name'];
		$file_type=$_FILES['file']['type'];
		$file_ext = strtolower(pathinfo($flowRelativePath, PATHINFO_EXTENSION));
		$extensions = array("jpeg","jpg","png");
		if(in_array($file_ext,$extensions )=== false){
			$errors[]="image extension not allowed, please choose a JPEG or PNG file.";
		}
		if($file_size > 2097152){
			$errors[]='File size cannot exceed 2 MB';
		}
		if(empty($errors)==true){

			move_uploaded_file($file_tmp,"../images/".$folder."/".md5($flowIdentifier).".".$file_ext);
		}else{
			print_r($errors);
		}
	}

	$sql = "UPDATE $folder SET image = :image WHERE id = :id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$image = md5($flowIdentifier).".".$file_ext;
		$stmt->bindParam("id", $flowIdentifier);
		$stmt->bindParam("image", $image);
		$stmt->execute();
		echo json_encode($image);
	}
    catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}';
	}

}

function signUp($entityManager, $firstName, $lastName, $email, $password)
{
	$user = new Users();
	$user->setName($firstName);
	$user->setlastName($lastName);
	$user->setEmail($email);
	$user->setPassword(md5($password));
	$user->setCreationDate(new DateTime());

	$repository = $entityManager->getRepository('Users');

	$dbUser = $repository->findBy(array('email' => $email));

	if (empty($dbUser)) {
	    $entityManager->persist($user);
		$entityManager->flush();
		echo json_encode(array("response" => "success"));
	}
	else
	{
		echo json_encode(array("response" => "error"));
	}
}

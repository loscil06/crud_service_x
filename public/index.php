<?php
require __DIR__ . '/../lib/common.php';
require __DIR__ . '/../vendor/autoload.php';

//Ruoting.
$page = 'login';
if (isset($_GET['p'])) {
	$page = $_GET['p'];
}
//Rendering
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../templates');
$twig = new Twig_Environment($loader);
switch ($page) {
	case 'login':
		$values = array(
			'title' => 'Service X - Log In');
		echo $twig->render('login.twig', $values);
		break;
	case 'signup':
		$values = array(
			'title' => 'Service X - Sign Up');
		echo $twig->render('signup.twig', $values);
		break;
	//TODO: Implement home		
}
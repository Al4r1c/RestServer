<?php
	error_reporting(E_ALL);

	define('BASE_PATH', __DIR__ . '/../');

	define('SERVER_NAMESPACE', 'Serveur');

	include_once(BASE_PATH . 'functions/functions.php');

	include_once(BASE_PATH . 'packages/autoload.php');

	include_once(BASE_PATH . 'src/classloader/ClassLoader.class.php');


	$classLoader = new \ClassLoader\ClassLoader();
	$classLoader->ajouterNamespace('Serveur', BASE_PATH . 'src');
	$classLoader->ajouterNamespace('Conteneur', BASE_PATH . 'src');
	$classLoader->ajouterNamespace('Tests', BASE_PATH . 'tests', '.php');
	$classLoader->register();
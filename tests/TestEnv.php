<?php
	error_reporting(E_ALL^E_NOTICE);

	if(!defined ('BASE_PATH')) {
		define('BASE_PATH', __DIR__ . '/../');
	}

	if(!defined ('SERVER_NAMESPACE')) {
		define('SERVER_NAMESPACE', 'Serveur');
	}

	include_once(BASE_PATH . 'functions/functions.php');

	include_once(BASE_PATH . 'packages/autoload.php');

	include_once(BASE_PATH . 'src/classloader/ClassLoader.class.php');


	$classLoader = new \ClassLoader\ClassLoader();
	$classLoader->ajouterNamespace('Serveur', __DIR__ . '/../src');
	$classLoader->ajouterNamespace('Conteneur', __DIR__ . '/../src');
	$classLoader->ajouterNamespace('Tests', __DIR__, '.php');
	$classLoader->register();


	$GLOBALS['global_function_appli_error'] = 'throwExceptionEnvTest';
	function throwExceptionEnvTest() {
		return;
	}
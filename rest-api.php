<?php
	include_once(__DIR__ . '/config/bootstrap.php');

	include_once(BASE_PATH . 'functions/functions.php');

	include_once './packages/autoload.php';

	include_once(BASE_PATH . 'src/classloader/ClassLoader.class.php');


	$classLoader = new \ClassLoader\ClassLoader();
	$classLoader->ajouterNamespace('Serveur', BASE_PATH . 'src');
	$classLoader->ajouterNamespace('Conteneur', BASE_PATH . 'src');
	$classLoader->register();

	$main = new \Serveur\MainApplication(new \Conteneur\MonConteneur());
	$main->run();

	echo $main->recupererResultat();
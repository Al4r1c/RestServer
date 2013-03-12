<?php
    error_reporting(E_ALL ^ E_NOTICE);

    if (!defined('BASE_PATH')) {
        define('BASE_PATH', realpath(__DIR__ . '/../'));
    }

    if (!defined('SERVER_NAMESPACE')) {
        define('SERVER_NAMESPACE', 'Serveur');
    }

    include_once(BASE_PATH . '/functions/functions.php');

    include_once(BASE_PATH . '/libraries/autoload.php');

    include_once(BASE_PATH . '/src/classloader/ClassLoader.class.php');


    $GLOBALS['global_function_ajouterErreur'] = 'throwExceptionEnvTest';
    function throwExceptionEnvTest($erreurNumber, $codeErreur)
    {
        // Ajouter en global et tester ?
    }


    $classLoader = new \ClassLoader\ClassLoader();
    $classLoader->ajouterNamespace('Serveur', BASE_PATH . '/src/serveur');
    $classLoader->ajouterNamespace('Conteneur', BASE_PATH . '/src/conteneur');
    $classLoader->ajouterNamespace('Logging', BASE_PATH . '/src/logging');
    $classLoader->ajouterNamespace('Tests', realpath(__DIR__ . '/Modules') . DIRECTORY_SEPARATOR, '.php');
    $classLoader->register();
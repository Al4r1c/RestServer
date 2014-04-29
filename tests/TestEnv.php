<?php
error_reporting(E_ALL ^ E_NOTICE);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', realpath(__DIR__ . '/../'));
}

if (!defined('SERVER_NAMESPACE')) {
    define('SERVER_NAMESPACE', 'Serveur');
}

include_once(BASE_PATH . '/functions/functions.php');

include_once(BASE_PATH . '/vendor/autoload.php');


$GLOBALS['global_function_ajouterErreur'] = 'throwExceptionEnvTest';
function throwExceptionEnvTest($erreurNumber, $codeErreur)
{
}


$classLoader = new ClassLoader();
$classLoader->ajouterNamespace('AlaroxRestServeur', BASE_PATH . '/src/AlaroxRestServeur');
$classLoader->ajouterNamespace('Tests', realpath(__DIR__ . '/Modules') . DIRECTORY_SEPARATOR, '.php');
$classLoader->register();
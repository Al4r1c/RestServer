<?php
    error_reporting(E_ALL);

    define('BASE_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));

    define('SERVER_NAMESPACE', 'AlaroxRestServeur');

    $_SERVER['PHP_INPUT'] = file_get_contents('php://input');
<?php
include_once(__DIR__ . '/config/bootstrap.php');

include_once(BASE_PATH . '/functions/functions.php');

include_once(BASE_PATH . '/libraries/autoload.php');


$classLoader = new ClassLoader();
$classLoader->ajouterNamespace('Serveur', BASE_PATH . '/src/serveur');
$classLoader->ajouterNamespace('Conteneur', BASE_PATH . '/src/conteneur');
$classLoader->ajouterNamespace('Logging', BASE_PATH . '/src/logging');
$classLoader->ajouterNamespace('Ressource', BASE_PATH . '/ressource', 'php');
$classLoader->register();


$main = new \Serveur\MainApplication(new \Conteneur\Conteneur());
$main->ajouterObserveur(\Logging\LoggingFactory::getLogger('logger'));
$main->setHandlers();

echo $main->run();
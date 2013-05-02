<?php
include_once(__DIR__ . '/config/bootstrap.php');

include_once(BASE_PATH . '/functions/functions.php');

include_once(BASE_PATH . '/libraries/autoload.php');



$main = new \Serveur\MainApplication(new \Conteneur\Conteneur());
$main->ajouterObserveur(\Logging\LoggingFactory::getLogger('logger'));
$main->setHandlers();

echo $main->run();
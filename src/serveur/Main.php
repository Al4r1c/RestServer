<?php
namespace Serveur;

use ClassLoader;

class Main
{
    public function __construct()
    {
        $classLoader = new ClassLoader('.class.php');
        $classLoader->ajouterNamespace('Serveur', BASE_PATH . '/src/serveur');
        $classLoader->ajouterNamespace('Conteneur', BASE_PATH . '/src/conteneur');
        $classLoader->ajouterNamespace('Logging', BASE_PATH . '/src/logging');
        $classLoader->ajouterNamespace('Model', BASE_PATH . '/application/model', '.php');
        $classLoader->ajouterNamespace('Ressource', BASE_PATH . '/application/ressource', '.php');
        $classLoader->register();
    }
}
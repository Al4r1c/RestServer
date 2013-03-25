<?php
namespace Serveur\Utils;

use AlaroxFileManager\AlaroxFile;

class Constante
{
    private static $_extension = 'php';

    /**
     * @param string $nomConfig
     * @return mixed
     */
    public static function chargerConfig($nomConfig)
    {
        $alaroxFileManager = new AlaroxFile();
        $fichier =
            $alaroxFileManager->getFile(BASE_PATH . '/public/constantes/' . $nomConfig . '.' . self::$_extension);

        return $fichier->loadFile();
    }
}
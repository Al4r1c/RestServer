<?php
	namespace Serveur\Utils;

	use Serveur\Lib\Fichier;

	class Constante {

		private static $extension = 'php';

		public static function chargerConfig($nomConfig) {
			$fichier = \Serveur\Utils\FileManager::getFichier();
			$fichier->setFichierParametres($nomConfig . '.' .  self::$extension, '/public/constantes');

			return $fichier->chargerFichier();
		}
	}
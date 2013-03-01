<?php
	namespace Serveur\Utils;

	class Constante {

		private static $extension = 'php';

		/**
		 * @param string $nomConfig
		 * @return mixed
		 */
		public static function chargerConfig($nomConfig) {
			$fichier = \Serveur\Utils\FileManager::getFichier();
			$fichier->setFichierParametres($nomConfig . '.' . self::$extension, '/public/constantes');

			return $fichier->chargerFichier();
		}
	}
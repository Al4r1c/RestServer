<?php
	namespace Serveur\Utils;

	use Serveur\Lib\Fichier;

	class Constante {

		private static $extension = 'php';

		public static function chargerConfig($nomConfig) {
			$fichier = new Fichier();
			$fichier->setBasePath(BASE_PATH);
			$fichier->setFichierConfig($nomConfig . '.' .  self::$extension, '/public/constantes');

			return $fichier->chargerFichier();
		}
	}
<?php
	namespace Serveur\Utils;

	class Tools {
		public static function isValideHttpCode($codeHttp) {
			return array_key_exists($codeHttp, Constante::chargerConfig('httpcode'));
		}

		public static function isValideHeader($header) {
			return in_array(strtolower($header), array_map('strtolower', Constante::chargerConfig('headers')));
		}
	}
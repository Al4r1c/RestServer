<?php
	namespace Serveur\Utils;

	class Tools {
		public static function isValideHttpCode($codeHttp) {
			return array_key_exists($codeHttp, Constante::chargerConfig('httpcode'));
		}
	}
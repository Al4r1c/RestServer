<?php
	namespace Serveur\Exceptions\Exceptions;

	class ArgumentTypeException extends MainException {
		/**
		 * @param string $code
		 * @param int $codeStatus
		 * @param string $methode
		 * @param string $attendu
		 * @param string $obtenu
		 */
		public function __construct($code, $codeStatus, $methode, $attendu, $obtenu) {
			parent::__construct($code, $codeStatus, $methode, $attendu, $obtenu);
		}
	}
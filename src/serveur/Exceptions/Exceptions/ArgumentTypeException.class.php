<?php
	namespace Serveur\Exceptions\Exceptions;

	class ArgumentTypeException extends MainException {
		public function __construct($code, $codeStatus, $methode, $attendu, $obtenu) {
			parent::__construct($code, $codeStatus, $methode, $attendu, $obtenu);
		}
	}
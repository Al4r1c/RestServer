<?php
	namespace Serveur\Exceptions\Exceptions;

	use Serveur\Exceptions\Exceptions\MainException;

	class RestRequeteException extends MainException {
		public function __construct($code, $codeStatus) {
			parent::__construct($code, $codeStatus, '{trad.restReqError}: ', array_slice(func_get_args(), 2));
		}
	}
<?php
	namespace Serveur\Exceptions\Exceptions;

	use Serveur\Utils\Tools;

	class MainException extends \Exception {

		private $codeRetourHttp = 500;

		public function __construct($code, $codeStatus) {
			parent::__construct('', $code);
			$this->setStatus($codeStatus);
			trigger_error_app(E_USER_ERROR, $code, array_slice(func_get_args(), 2));
		}

		public function getStatus() {
			return $this->codeRetourHttp;
		}

		public function setStatus($codeHttp) {
			if(Tools::isValideHttpCode($codeHttp)) {
				$this->codeRetourHttp = $codeHttp;
			}
		}
	}
<?php
	namespace Serveur\Exceptions\Exceptions;

	use Exception;
	use Serveur\Utils\Tools;

	class MainException extends \Exception {

		private $codeRetourHttp = 500;

		public function __construct($code, $codeStatus) {
			parent::__construct('', $code);
			$this->setStatus($codeStatus);
			trigger_error_app(E_USER_ERROR, $code);
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
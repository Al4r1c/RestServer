<?php
	namespace Serveur\Exceptions\Exceptions;

	use Exception;
	use Serveur\Utils\Tools;

	class MainException extends \Exception {

		private $codeRetourHttp = 500;

		public function __construct($code, $codeStatus, $message, $arguments = array()) {
			parent::__construct($message . $this->recupererMessage($code), $code);
			$this->setStatus($codeStatus);
			new \Serveur\Exceptions\Types\Error($this->getCode(), $this->getMessage(), $arguments);
		}

		protected function recupererMessage($code) {
			return '{errorMessage.' . $code . '}';
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
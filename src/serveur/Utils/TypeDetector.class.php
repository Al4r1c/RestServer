<?php
	namespace Serveur\Utils;

	use Serveur\Utils\Constante;

	class TypeDetector {

		private $constanteMimes;

		public function __construct(array $mimesTypes) {
			$this->constanteMimes = $mimesTypes;
		}

		public function getMimeType($mimeExtension) {
			if(!isNull($this->constanteMimes[$mimeExtension])) {
				return $this->constanteMimes[$mimeExtension];
			} else {
				return '*/*';
			}
		}

		public function extraireMimesTypeHeader($header) {
			$allType = explode(',', $header);
			$tabTypesTrouves = array();

			foreach($allType as $unType) {
				if(strpos($unType = strtolower($unType), ';') !== false) {
					$unType = substr($unType, 0, strpos($unType, ';'));
				}

				foreach($this->constanteMimes as $uneExtension => $unFormatMime) {
					if(strcmp($unFormatMime, $unType) === 0) {
						$tabTypesTrouves[] = $uneExtension;
					}
				}
			}

			return $tabTypesTrouves;
		}
	}
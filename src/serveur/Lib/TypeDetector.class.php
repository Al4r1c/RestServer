<?php
	namespace Serveur\Lib;

	use Serveur\Utils\Constante;

	class TypeDetector {
		/**
		 * @var array
		 */
		private $constanteMimes;

		/**
		 * @param array $mimesTypes
		 */
		public function __construct(array $mimesTypes) {
			$this->constanteMimes = $mimesTypes;
		}

		/**
		 * @param string $clefMimeExtension
		 * @return string
		 */
		public function getMimeType($clefMimeExtension) {
			if(!isNull($this->constanteMimes[$clefMimeExtension])) {
				return $this->constanteMimes[$clefMimeExtension];
			} else {
				return '*/*';
			}
		}

		/**
		 * @param string $header
		 * @return array
		 */
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
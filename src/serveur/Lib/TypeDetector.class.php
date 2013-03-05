<?php
    namespace Serveur\Lib;

    use Serveur\Utils\Constante;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class TypeDetector {
        /**
         * @var array
         */
        private $constanteMimes;

        /**
         * @param array $mimesTypes
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function __construct($mimesTypes) {
            if(!is_array($mimesTypes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $mimesTypes);
            }

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
         * @param string $enteteHttpAccept
         * @return array
         */
        public function extraireMimesTypeHeader($enteteHttpAccept) {
            $allType = explode(',', $enteteHttpAccept);
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
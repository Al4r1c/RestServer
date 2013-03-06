<?php
    namespace Modules;

    class FactoryMock extends \PHPUnit_Framework_TestCase {
        /** @return \PHPUnit_Framework_MockObject_MockObject */
        public function createMock($type, $methodes = array()) {
            $mock = null;

            switch (strtolower($type)) {
                case 'abstractchargeurfichier':
                    $mock = self::getMockAbstractChargeur();
                    break;
                case 'abstractrenderer':
                    $mock = self::getMockAbstractRenderer();
                    break;
                case 'config':
                    $mock = self::getMockConfig($methodes);
                    break;
                case 'constante':
                    $mock = self::getMockConstante($methodes);
                    break;
                case 'erreur':
                    $mock = self::getMockErreur($methodes);
                    break;
                case 'errorhandler':
                    $mock = self::getErrorHandler($methodes);
                    break;
                case 'fichier':
                    $mock = self::getMockFichier($methodes);
                    break;
                case 'filesystem':
                    $mock = self::getMockFileSystem($methodes);
                    break;
                case 'headermanager':
                    $mock = self::getMockHeadersManager($methodes);
                    break;
                case 'i18nmanager':
                    $mock = self::getMockI18nManager($methodes);
                    break;
                case 'notice':
                    $mock = self::getMockNotice($methodes);
                    break;
                case 'restrequete':
                    $mock = self::getMockRestRequete($methodes);
                    break;
                case 'restreponse':
                    $mock = self::getMockRestReponse($methodes);
                    break;
                case 'server':
                    $mock = self::getMockServer($methodes);
                    break;
                case 'tradmanager':
                    $mock = self::getMockTradManager($methodes);
                    break;
                case 'xmlelement':
                    $mock = self::getMockXmlElement($methodes);
                    break;
                case 'xmlparser':
                    $mock = self::getMockXmlParser($methodes);
                    break;
                default:
                    new \Exception('Mock type not found.');
                    break;
            }

            return $mock;
        }

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FichierChargement\AbstractChargeurFichier */
        private function getMockAbstractChargeur() {
            return $this->getMockForAbstractClass('Serveur\Lib\FichierChargement\AbstractChargeurFichier');
        }

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Renderers\AbstractRenderer */
        protected function getMockAbstractRenderer() {
            return $this->getMockForAbstractClass('Serveur\Renderers\AbstractRenderer');
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Config\Config
         */
        protected function getMockConfig($methodes = array()) {
            return $this->getMock('Serveur\Config\Config', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Utils\Constante
         */
        protected function getMockConstante($methodes = array()) {
            return $this->getMock('Serveur\Utils\Constante', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Error
         */
        private function getMockErreur($methodes = array()) {
            return $this->getMock('Serveur\Exceptions\Types\Error', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Handler\ErrorHandler
         */
        private function getErrorHandler($methodes) {
            return $this->getMock('Serveur\Exceptions\Handler\ErrorHandler', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\Fichier
         */
        protected function getMockFichier($methodes = array()) {
            return $this->getMock('Serveur\Lib\Fichier', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FileSystem
         */
        protected function getMockFileSystem($methodes = array()) {
            return $this->getMock('Serveur\Lib\FileSystem', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\HeaderManager
         */
        protected function getMockHeadersManager($methodes = array()) {
            return $this->getMock('Serveur\Rest\HeaderManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\I18nManager
         */
        protected function getMockI18nManager($methodes = array()) {
            return $this->getMock('Logging\I18n\I18nManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Notice
         */
        private function getMockNotice($methodes = array()) {
            return $this->getMock('Serveur\Exceptions\Types\Notice', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestRequete
         */
        protected function getMockRestRequete($methodes = array()) {
            return $this->getMock('Serveur\Rest\RestRequete', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestReponse
         */
        protected function getMockRestReponse($methodes = array()) {
            return $this->getMock('Serveur\Rest\RestReponse', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\Server
         */
        protected function getMockServer($methodes = array()) {
            return $this->getMock('Serveur\Rest\Server', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\TradManager
         */
        private function getMockTradManager($methodes = array()) {
            return $this->getMock('Logging\I18n\TradManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLElement
         */
        protected function getMockXmlElement($methodes = array()) {
            return $this->getMock('Serveur\Lib\XMLParser\XMLElement', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLParser
         */
        protected function getMockXmlParser($methodes = array()) {
            return $this->getMock('Serveur\Lib\XMLParser\XMLParser', $methodes);
        }
    }
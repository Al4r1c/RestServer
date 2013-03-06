<?php
    namespace Modules;

    class FactoryMock extends \PHPUnit_Framework_TestCase {
        /** @return \PHPUnit_Framework_MockObject_MockObject */
        public function createMock($type, $methodes = array(), $arguments = array()) {
            switch (strtolower($type)) {
                case 'abstractchargeurfichier':
                    $mock = self::getMockAbstractChargeur();
                    break;
                case 'abstractrenderer':
                    $mock = self::getMockAbstractRenderer();
                    break;
                case 'config':
                    $mock = self::getMockConfig($methodes, $arguments);
                    break;
                case 'constante':
                    $mock = self::getMockConstante($methodes, $arguments);
                    break;
                case 'erreur':
                    $mock = self::getMockErreur($methodes, $arguments);
                    break;
                case 'fichier':
                    $mock = self::getMockFichier($methodes, $arguments);
                    break;
                case 'filesystem':
                    $mock = self::getMockFileSystem($methodes, $arguments);
                    break;
                case 'headermanager':
                    $mock = self::getMockHeadersManager($methodes, $arguments);
                    break;
                case 'i18nmanager':
                    $mock = self::getMockI18nManager($methodes, $arguments);
                    break;
                case 'notice':
                    $mock = self::getMockNotice($methodes, $arguments);
                    break;
                case 'restrequete':
                    $mock = self::getMockRestRequete($methodes, $arguments);
                    break;
                case 'restreponse':
                    $mock = self::getMockRestReponse($methodes, $arguments);
                    break;
                case 'server':
                    $mock = self::getMockServer($methodes, $arguments);
                    break;
                case 'tradmanager':
                    $mock = self::getMockTradManager($methodes, $arguments);
                    break;
                case 'xmlelement':
                    $mock = self::getMockXmlElement($methodes, $arguments);
                    break;
                case 'xmlparser':
                    $mock = self::getMockXmlParser($methodes, $arguments);
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
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Config\Config
         */
        protected function getMockConfig($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Config\Config', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Utils\Constante
         */
        protected function getMockConstante($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Utils\Constante', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Error
         */
        private function getMockErreur($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Exceptions\Types\Error', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\Fichier
         */
        protected function getMockFichier($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Lib\Fichier', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FileSystem
         */
        protected function getMockFileSystem($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Lib\FileSystem', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\HeaderManager
         */
        protected function getMockHeadersManager($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Rest\HeaderManager', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\I18nManager
         */
        protected function getMockI18nManager($methodes = array(), $arg = array()) {
            return $this->getMock('Logging\I18n\I18nManager', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Notice
         */
        private function getMockNotice($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Exceptions\Types\Notice', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestRequete
         */
        protected function getMockRestRequete($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Rest\RestRequete', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestReponse
         */
        protected function getMockRestReponse($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Rest\RestReponse', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\Server
         */
        protected function getMockServer($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Rest\Server', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\TradManager
         */
        private function getMockTradManager($methodes = array(), $arg = array()) {
            return $this->getMock('Logging\I18n\TradManager', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLElement
         */
        protected function getMockXmlElement($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Lib\XMLParser\XMLElement', $methodes, $arg);
        }

        /**
         * @param array $methodes
         * @param array $arg
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLParser
         */
        protected function getMockXmlParser($methodes = array(), $arg = array()) {
            return $this->getMock('Serveur\Lib\XMLParser\XMLParser', $methodes, $arg);
        }
    }
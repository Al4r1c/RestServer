<?php
    namespace Modules\ServeurTests\Exceptions;

    include_once(__DIR__ . '/../../../TestEnv.php');

    use Modules\TestCase;

    class ErreurHandlerTest extends TestCase {
        /** @var \Serveur\Exceptions\Handler\ErreurHandler */
        private $_errorHandler;

        public function setUp() {
            $this->_errorHandler = new \Serveur\Exceptions\Handler\ErreurHandler();
        }

        public function testAjouterErreurGlobal() {
            $this->_errorHandler->global_ajouterErreur(E_USER_ERROR, 10000, array('var1'));
            $this->assertContainsOnlyInstancesOf('\Serveur\Exceptions\Types\Error', $this->_errorHandler->getErreurs());
            $this->assertCount(1, $this->_errorHandler->getErreurs());
        }

        public function testAjouterNoticeGlobal() {
            $this->_errorHandler->global_ajouterErreur(E_USER_NOTICE, 10000, array('var1'));
            $this->assertContainsOnlyInstancesOf('\Serveur\Exceptions\Types\Notice', $this->_errorHandler->getErreurs());
            $this->assertCount(1, $this->_errorHandler->getErreurs());
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testAjouterErreurNonReconnue() {
            $this->_errorHandler->global_ajouterErreur(E_WARNING, 10000, array('var1'));
        }

        public function testExceptionHandler() {
            $this->_errorHandler->exceptionHandler(new \Exception('Message', 10000));
            $this->assertContainsOnlyInstancesOf('\Serveur\Exceptions\Types\Error', $this->_errorHandler->getErreurs());
            $this->assertCount(1, $this->_errorHandler->getErreurs());
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerGraveDoncException() {
            $this->_errorHandler->errorHandler(E_ERROR, 'Message', 'script.php', 15);
        }

        public function testErrorHandlerNotice() {
            $this->_errorHandler->errorHandler(E_DEPRECATED, 'Message', 'script.php', 15);
            $this->assertContainsOnlyInstancesOf('\Serveur\Exceptions\Types\Notice', $this->_errorHandler->getErreurs());
            $this->assertCount(1, $this->_errorHandler->getErreurs());
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerTypeInconnu() {
            $this->_errorHandler->errorHandler(9999, 'Message', 'script.php', 15);
        }
    }
<?php
    namespace Modules\ServeurTests\Exceptions;

    use Modules\TestCase;
    use Serveur\GestionErreurs\Exceptions\MainException;

    class MainExceptionTest extends TestCase
    {
        /** @var MainException */
        private $_mainException;

        private function setMainException($code, $codeStatus)
        {
            $this->_mainException = new MainException($code, $codeStatus);
        }

        public function testGetCode()
        {
            $this->setMainException(10000, 500);
            $this->assertEquals(10000, $this->_mainException->getCode());
        }

        public function testGetCodeStatus()
        {
            $this->setMainException(10000, 500);
            $this->assertEquals(500, $this->_mainException->getStatus());
        }

        /**
         * @expectedException \Exception
         */
        public function testSetCodeErrone()
        {
            $this->setMainException(10000, 'fake');
        }
    }
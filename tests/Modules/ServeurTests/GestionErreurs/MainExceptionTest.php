<?php
    namespace Tests\ServeurTests\GestionErreurs;

    use Serveur\GestionErreurs\Exceptions\MainException;
    use Tests\TestCase;

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
            $this->assertInstanceOf('Serveur\Lib\ObjetReponse', $this->_mainException->getObjetReponseErreur());
        }
    }
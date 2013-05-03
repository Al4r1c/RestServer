<?php
namespace Tests\ServeurTests\GestionErreurs;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Tests\TestCase;

class ArgumentTypeExceptionTest extends TestCase
{
    /** @var ArgumentTypeException */
    private $_argumentException;

    private function setMainException($variable)
    {
        $this->_argumentException = new ArgumentTypeException(10000, 500, 'Methode()', 'attendu', $variable);
    }

    public function testGetObtenuInt()
    {
        $this->setMainException(10000);
        $this->assertAttributeEquals('integer', '_obtenu', $this->_argumentException);
    }

    public function testGetObtenuString()
    {
        $this->setMainException('pala');
        $this->assertAttributeEquals('string', '_obtenu', $this->_argumentException);
    }

    public function testGetObtenuObjet()
    {
        $this->setMainException(new \StdClass());
        $this->assertAttributeEquals('stdClass', '_obtenu', $this->_argumentException);
    }
}
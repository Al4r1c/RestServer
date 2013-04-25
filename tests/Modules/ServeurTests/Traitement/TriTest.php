<?php
namespace Tests\ServeurTests\Traitement;

use Serveur\Traitement\DonneeRequete\Tri;
use Tests\TestCase;

class TriTest extends TestCase
{
    /**
     * @var Tri
     */
    private $_tri;

    public function setUp()
    {
        $this->_tri = new Tri();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\Serveur\\Traitement\\DonneeRequete\\Tri', $this->_tri);
    }

    public function testTypeTri()
    {
        $this->_tri->setTypeTri('orderBy');

        $this->assertEquals('orderBy', $this->_tri->getTypeTri());
    }

    public function testValeur()
    {
        $this->_tri->setValeur('testField');

        $this->assertEquals('testField', $this->_tri->getValeur());
    }
}

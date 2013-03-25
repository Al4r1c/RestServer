<?php
namespace Tests\LoggingTests\Factory;

use Logging\LoggingFactory;
use Tests\TestCase;

class LoggingFactoryTest extends TestCase
{
    public function testRecupererLogger()
    {
        $factory = LoggingFactory::getLogger('logger');
        $this->assertInstanceOf("Logging\\Displayer\\AbstractDisplayer", $factory);
    }

    /**
     * @expectedException \Exception
     */
    public function testRecupererInexistant()
    {
        LoggingFactory::getLogger('WRONG_ONE');
    }
}
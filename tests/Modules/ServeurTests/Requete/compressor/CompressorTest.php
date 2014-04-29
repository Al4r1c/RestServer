<?php
namespace Tests\lib\compressor;

use AlaroxRestServeur\Serveur\Requete\compressor\Compressor;

class CompressorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Compressor
     */
    private $_compressor;

    public function setUp()
    {
        $this->_compressor = new Compressor();
    }

    public function testSetCompressorFactory()
    {
        $unCompressorFactory = $this->getMock('\\AlaroxRestServeur\\Serveur\\Requete\\compressor\\CompressorFactory');

        $this->_compressor->setCompressorFactory($unCompressorFactory);

        $this->assertAttributeSame($unCompressorFactory, '_compressorFactory', $this->_compressor);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCompressorFactoryTypeErrone()
    {
        $this->_compressor->setCompressorFactory(array());
    }

    public function testCompress()
    {
        $compressorFactory = $this->getMock('\\AlaroxRestServeur\\Serveur\\Requete\\compressor\\CompressorFactory', array('getCompressor'));
        $abstractCompressor = $this->getMockForAbstractClass('\\AlaroxRestServeur\\Serveur\\Requete\\compressor\\AbstractCompressor');

        $compressorFactory->expects($this->once())
            ->method('getCompressor')
            ->with('gzip')
            ->will($this->returnValue($abstractCompressor));

        $abstractCompressor->expects($this->once())
            ->method('uncompress')
            ->with('compressedData:{parameter:var}')
            ->will($this->returnValue('{parameter:var}'));

        $this->_compressor->setCompressorFactory($compressorFactory);

        $this->assertEquals('{parameter:var}', $this->_compressor->uncompress('compressedData:{parameter:var}', 'gzip'));
    }

    /**
     * @expectedException \Exception
     */
    public function testUncompressorFactoryNotSet()
    {
        $this->_compressor->uncompress('data', 'gzip');
    }
}

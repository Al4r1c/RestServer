<?php
    namespace Modules\ServeurTests\XMLParser;

    include_once(__DIR__ . '/../../../TestEnv.php');

    use Modules\TestCase;

    class XMLElementTest extends TestCase {
        /** @var \Serveur\Lib\XMLParser\XMLElement */
        private $_xmlElement;

        public function setUp() {
            $this->_xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
        }

        public function testSetNom() {
            $this->_xmlElement->setNom('nom');

            $this->assertEquals('nom', $this->_xmlElement->getNom());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetNomErrone() {
            $this->_xmlElement->setNom(array());
        }

        public function testSetChildren() {
            $this->_xmlElement->setChildren(array(new \Serveur\Lib\XMLParser\XMLElement()));

            $this->assertEquals(array(new \Serveur\Lib\XMLParser\XMLElement()), $this->_xmlElement->getChildren());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetChildrenErrone() {
            $this->_xmlElement->setChildren('fils');
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetChildrenErrone2() {
            $this->_xmlElement->setChildren(array(5));
        }

        public function testGetChildrenMaisAucunChildren() {
            $this->_xmlElement->setChildren(false);

            $this->assertEquals(array(), $this->_xmlElement->getChildren());
        }

        public function testSetAttributs() {
            $this->_xmlElement->setAttributs(array('param1' => 'val1'));

            $this->assertEquals(array('param1' => 'val1'), $this->_xmlElement->getAttributs());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetAttributsErrone() {
            $this->_xmlElement->setAttributs('stringgggggg');
        }

        public function testGetAttribut() {
            $this->_xmlElement->setAttributs(array('param1' => 'val1', 'param2' => 'val2'));

            $this->assertEquals('val2', $this->_xmlElement->getAttribut('param2'));
        }

        public function testGetAttributNonExistant() {
            $this->_xmlElement->setAttributs(array('param1' => 'val1'));

            $this->assertNull($this->_xmlElement->getAttribut('param2'));
        }

        public function testSetValeur() {
            $this->_xmlElement->setValeur('valeur');

            $this->assertEquals('valeur', $this->_xmlElement->getValeur());
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetValeurErrone() {
            $this->_xmlElement->setValeur(null);
        }

        public function testSetDonnees() {
            $this->_xmlElement->setDonnees(array(
                'element' => 'nom',
                'attr' => array('attr1' => 'val1'),
                'children' => array(new \Serveur\Lib\XMLParser\XMLElement()),
                'data' => 'value')
            );

            $this->assertEquals('nom', $this->_xmlElement->getNom());
            $this->assertEquals(array('attr1' => 'val1'), $this->_xmlElement->getAttributs());
            $this->assertEquals(array(new \Serveur\Lib\XMLParser\XMLElement()), $this->_xmlElement->getChildren());
            $this->assertEquals('value', $this->_xmlElement->getValeur());
        }
    }
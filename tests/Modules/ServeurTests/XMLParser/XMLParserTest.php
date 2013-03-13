<?php
    namespace Tests\ServeurTests\XMLParser;

    use Serveur\Lib\XMLParser\XMLParser;
    use Tests\TestCase;

    class XMLParserTest extends TestCase
    {
        /** @var XMLParser */
        private $_xmlParser;

        public function setUp()
        {
            $this->_xmlParser = new XMLParser();
        }

        public function testSetContenu()
        {
            $this->_xmlParser->setContenuInitial("<root></root>");

            $this->assertEquals("<root></root>", $this->_xmlParser->getContenuInitial());
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testSetContenuErrone()
        {
            $this->_xmlParser->setContenuInitial(array());
        }

        public function testGetErreurVide()
        {
            $this->assertNull($this->_xmlParser->getErreurMessage());
        }

        public function testParse()
        {
            $this->_xmlParser->setContenuInitial("<root></root>");
            $this->_xmlParser->parse();

            $xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
            $xmlElement->setNom('root');

            $this->assertEquals($xmlElement, $this->_xmlParser->getDonneesParsees());
        }

        public function testParseErreur()
        {
            $this->_xmlParser->setContenuInitial("<root></toor>");
            $this->_xmlParser->parse();

            $this->assertNull($this->_xmlParser->getDonneesParsees());
            $this->assertFalse($this->_xmlParser->isValide());
            $this->assertInternalType('string', $this->_xmlParser->getErreurMessage());
        }

        public function testGetValeur()
        {
            $this->_xmlParser->setContenuInitial(" <root> \n \n <elem>value</elem></root>");
            $this->_xmlParser->parse();

            $xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
            $xmlElement->setNom('elem');
            $xmlElement->setChildren(false);
            $xmlElement->setValeur('value');

            $this->assertEquals(array($xmlElement), $this->_xmlParser->getConfigValeur('elem'));
        }

        public function testGetValeurLointaine()
        {
            $this->_xmlParser->setContenuInitial("<root><elem><deeper><yes>valeur</yes></deeper></elem></root>");
            $this->_xmlParser->parse();

            $xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
            $xmlElement->setNom('yes');
            $xmlElement->setChildren(false);
            $xmlElement->setValeur('valeur');

            $this->assertEquals(array($xmlElement), $this->_xmlParser->getConfigValeur('elem.deeper.yes'));
        }

        public function testGetValeurAttribut()
        {
            $this->_xmlParser->setContenuInitial("<root><elem attr=\"y\">ok</elem><elem attr=\"n\">nok</elem></root>");
            $this->_xmlParser->parse();

            $xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
            $xmlElement->setNom('elem');
            $xmlElement->setAttributs(array('attr' => 'y'));
            $xmlElement->setChildren(false);
            $xmlElement->setValeur('ok');

            $this->assertEquals(array($xmlElement), $this->_xmlParser->getConfigValeur('elem[attr=y]'));
        }

        public function testGetValeurSupportAccent()
        {
            $this->_xmlParser->setContenuInitial("<root><elem>AccentuéHey</elem></root>");
            $this->_xmlParser->parse();

            $xmlElement = new \Serveur\Lib\XMLParser\XMLElement();
            $xmlElement->setNom('elem');
            $xmlElement->setChildren(false);
            $xmlElement->setValeur('AccentuéHey');

            $this->assertEquals(array($xmlElement), $this->_xmlParser->getConfigValeur('elem'));
        }

        public function testGetValeurExistePasNull()
        {
            $this->_xmlParser->setContenuInitial("<root><elem></elem></root>");
            $this->_xmlParser->parse();

            $this->assertNull($this->_xmlParser->getConfigValeur('fake.elem'));
        }
    }
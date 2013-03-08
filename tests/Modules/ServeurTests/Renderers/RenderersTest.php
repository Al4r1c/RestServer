<?php
    namespace Modules\ServeurTests\Renderers;

    use Modules\TestCase;

    class RenderersTest extends TestCase
    {

        private static $donnee = array('param1' => 1, 'param2' => array('one' => 'onevar2'), array('yosh', 'yosh2'));

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testRenderNonArrayDonnees()
        {
            $renderer = new \Serveur\Renderers\Html();

            $renderer->render('string');
        }

        public function testRenderHtml()
        {
            $renderer = new \Serveur\Renderers\Html();

            $sortie = sprintf(
                $renderer::$templateHtml,
                "<ul>\n\t<li><strong>param1:</strong> 1</li>\n\t<li><strong>param2:</strong> <ul>\n\t<li><strong>one:</strong> onevar2</li>\n</ul>\n</li>\n\t<li><strong>0:</strong> <ul>\n\t<li><strong>0:</strong> yosh</li>\n\t<li><strong>1:</strong> yosh2</li>\n</ul>\n</li>\n</ul>\n"
            );

            $this->assertEquals($sortie, $renderer->render(self::$donnee));
        }

        public function testJson()
        {
            $renderer = new \Serveur\Renderers\Json();

            $this->assertEquals(
                '{"param1":1,"param2":{"one":"onevar2"},"0":["yosh","yosh2"]}',
                $renderer->render(self::$donnee)
            );
        }

        public function testPlain()
        {
            $renderer = new \Serveur\Renderers\Plain();

            $this->assertEquals(
                "param1 => 1\nparam2 => \n\tone => onevar2\n0 => \n\t0 => yosh\n\t1 => yosh2\n",
                $renderer->render(self::$donnee)
            );
        }

        public function testXml()
        {
            $renderer = new \Serveur\Renderers\Xml();

            $this->assertEquals(
                '<?xml version="1.0"?>' . "\n" .
                '<root><param1>1</param1><param2><one>onevar2</one></param2><0>yosh</0><1>yosh2</1></root>' . "\n",
                $renderer->render(self::$donnee)
            );
        }
    }
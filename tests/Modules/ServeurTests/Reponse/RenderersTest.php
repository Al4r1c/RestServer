<?php
namespace Tests\ServeurTests\Reponse;

use AlaroxRestServeur\Serveur\Reponse\Renderers\Html;
use AlaroxRestServeur\Serveur\Reponse\Renderers\Json;
use AlaroxRestServeur\Serveur\Reponse\Renderers\Plain;
use AlaroxRestServeur\Serveur\Reponse\Renderers\Xml;
use Tests\TestCase;

class RenderersTest extends TestCase
{
    private static $donnee = array(
        'param1' => 1,
        'param2' => array(
            'one' => 'onevar2',
            'two' => array(
                'yosh',
                array(
                    'key1' => 'val1',
                    'key2' => 'val2'
                )
            )
        )
    );

    /**
     * @expectedException     \AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     * @expectedExceptionCode 1000
     */
    public function testRenderNonArrayDonnees()
    {
        $renderer = new Html();

        $renderer->render('string');
    }

    public function testRenderHtml()
    {
        $renderer = new Html();

        $sortie = sprintf(
            $renderer->getTemplateHtml(),
            "<ul>
<li><strong>param1:</strong>&nbsp;1</li>
<li><strong>param2:</strong>&nbsp;
<ul>
<li><strong>one:</strong>&nbsp;onevar2</li>
<li><strong>two:</strong>&nbsp;
<ul>
<li><strong>0:</strong>&nbsp;yosh</li>
<li><strong>1:</strong>&nbsp;
<ul>
<li><strong>key1:</strong>&nbsp;val1</li>
<li><strong>key2:</strong>&nbsp;val2</li>
</ul>
</li>
</ul>
</li>
</ul>
</li>
</ul>
"
        );

        $this->assertEquals($sortie, $renderer->render(self::$donnee));
    }

    public function testJson()
    {
        $renderer = new Json();

        $this->assertEquals(
            '{"param1":1,"param2":{"one":"onevar2","two":["yosh",{"key1":"val1","key2":"val2"}]}}',
            $renderer->render(self::$donnee)
        );
    }

    public function testPlain()
    {
        $renderer = new Plain();

        $this->assertEquals(
            "param1 => 1\nparam2 => \n\tone => onevar2\n\ttwo => \n\t\t0 => yosh\n\t\t1 => \n\t\t\tkey1 => val1\n\t\t\tkey2 => val2\n",
            $renderer->render(self::$donnee)
        );
    }

    public function testXml()
    {
        $renderer = new Xml();

        $this->assertEquals(
            "<?xml version=\"1.0\"?>\n" .
                '<root><param1>1</param1><param2><one>onevar2</one><two>yosh</two><two><key1>val1</key1><key2>val2</key2></two></param2></root>' .
                "\n",
            $renderer->render(self::$donnee)
        );
    }
}
<?php
namespace AlaroxRestServeur\Serveur\Reponse\Renderers;

class Html extends AbstractRenderer
{

    public static $templateHtml = "<!DOCTYPE html>
<html>
<head>
	<title>Data</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html\" />
	<style>
		body {
			font-family: Helvetica, Arial, sans-serif;
			font-size: 14px;
			color: #000;
			padding: 5px;
	    }
	    ul {
			padding-bottom: 15px;
			padding-left: 20px;
	    }
    </style>
</head>
<body>
%s
</body>
</html>";

    /**
     * @param array $donnees
     * @return string
     */
    protected function genererRendu(array $donnees)
    {
        return $this->templateHtml($this->convertTableauToListeHtml($donnees));
    }

    /**
     * @param array $array
     * @return string
     */
    private function convertTableauToListeHtml(array $array)
    {
        $list = "<ul>\n";

        foreach ($array as $clef => $valeur) {
            $list .= "<li><strong>" . $clef . ":</strong>&nbsp;";
            if (is_array($valeur)) {
                $list .= "\n" . $this->convertTableauToListeHtml($valeur);
            } else {
                $list .= $valeur;
            }
            $list .= "</li>\n";
        }

        return $list . "</ul>\n";
    }

    /**
     * @param string $donneesFormatListeHtml
     * @return string
     */
    private function templateHtml($donneesFormatListeHtml)
    {
        $retour = sprintf(self::$templateHtml, $donneesFormatListeHtml);

        return <<<EOT
$retour
EOT;
    }

    public function getTemplateHtml()
    {
        return self::$templateHtml;
    }
}
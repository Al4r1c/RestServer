<?php
	namespace Serveur\Renderers;

	class Html extends \Serveur\Renderers\AbstractRenderer {
		/**
		 * @param array $donnees
		 * @return string
		 */
		public function render(array $donnees) {
			return $this->templateHtml($this->convertTableauToListeHtml($donnees));
		}

		/**
		 * @param array $array
		 * @return string
		 */
		private function convertTableauToListeHtml(array $array) {
			$list = "<ul>\n";

			foreach($array as $clef => $valeur) {
				$list .= "\t<li><strong>" . $clef . ":</strong> ";
				if(is_array($valeur)) {
					$list .= $this->convertTableauToListeHtml($valeur);
				} else {
					$list .= $valeur;
				}
				$list .= "</li>\n";
			}

			return $list . "</ul>\n";
		}

		/**
		 * @param string $DonneesFormatListeHtml
		 * @return string
		 */
		private function templateHtml($DonneesFormatListeHtml) {
			return <<<EOT
<!DOCTYPE html>
<html>
<head>
	<title>Data</title>
	<meta http-equiv="Content-Type" content="text/html" />
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
$DonneesFormatListeHtml
</body>
</html>
EOT;
		}
	}
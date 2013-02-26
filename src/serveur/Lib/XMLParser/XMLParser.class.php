<?php
	namespace Serveur\Lib\XMLParser;

	use Serveur\Lib\XMLParser\XMLElement;

	class XMLParser {

		private $donneesSourcedata;
		/** @var XMLElement */
		private $donneesParsees;
		private $erreur;

		public function setContenu($contenuXml) {
			$this->donneesSourcedata = $contenuXml;
			$this->parse($contenuXml);
		}

		public function isValide() {
			return empty($this->erreur);
		}

		public function getErreurMessage() {
			return sprintf('XML error at line %d column %d: %s', $this->erreur['line'], $this->erreur['column'], $this->erreur['message']);
		}

		private function parse($contenuXml) {
			$parser = xml_parser_create();

			xml_set_object($parser, $this);
			xml_set_element_handler($parser, 'tagDebutXML', 'tagFinXML');
			xml_set_character_data_handler($parser, 'valeurXML');
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

			$lignes = explode("\n", $contenuXml);
			foreach($lignes as $uneLigne) {
				if(trim($uneLigne) == '') {
					continue;
				}

				$donnee = $uneLigne . "\n";

				if(!xml_parse($parser, $donnee)) {
					$this->donneesParsees = null;
					$this->erreur = array(
						'line' => xml_get_current_line_number($parser),
						'column' => xml_get_current_column_number($parser),
						'message' => xml_error_string(xml_get_error_code($parser))
					);
				}
			}
			unset($GLOBALS['temporaire']);
		}

		private function tagDebutXML($parser, $nom, $attributs) {
			$GLOBALS['temporaire'][] = $nom;

			$this->donneesParsees[$nom]['element'] = strtolower($nom);
			$this->donneesParsees[$nom]['attr'] = array_map('strtolower', $attributs);
			$this->donneesParsees[$nom]['children'] = array();
		}

		private function tagFinXML($parser, $nom) {
			global $temporaire;

			if(end($temporaire) == $nom) {
				$tempName = $nom;

				array_pop($temporaire);

				$nouveauLast = end($temporaire);

				$nouvelElement = new XMLElement();
				$nouvelElement->setDonnees($this->donneesParsees[$tempName]);

				if(count($temporaire) > 0) {
					$this->donneesParsees[$nouveauLast]['children'][] = $nouvelElement;
					unset($this->donneesParsees[$tempName]);
				} else {
					$this->donneesParsees = $nouvelElement;
				}
			}
		}

		private function valeurXML($parser, $valeur) {
			if(trim($valeur) != '') {
				end($this->donneesParsees);
				$this->donneesParsees[key($this->donneesParsees)]['data'] = trim(str_replace("\n", '', $valeur));
				$this->donneesParsees[key($this->donneesParsees)]['children'] = false;
			}
		}

		/** @return XMLElement[] */
		public function getConfigValeur($clefConfig) {
			if($valeur = $this->rechercheValeurTableauMultidim(explode('.', strtolower($clefConfig)), $this->donneesParsees->getChildren())) {
				return $valeur;
			} else {
				return null;
			}
		}

		/** @param $arrayValues XMLElement[] */
		public function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues) {
			if (count($tabKey) == 1) {
				$tabResult = array();

				if(preg_match_all('#^[a-z]+(\[[a-z]+=[a-z0-9]+\]){1}$#', $tabKey[0])) {
					$tabClef = explode('[', $tabKey[0]);
					$clef = $tabClef[0];
					$filtres = explode('=', $tabClef[1]);
					$filtres[1] = substr($filtres[1], 0, -1);
				} else {
					$clef = $tabKey[0];
				}

				foreach($arrayValues as $unElement) {
					if($unElement->getNom() === $clef) {
						if(isset($filtres) && $unElement->getAttribut($filtres[0]) !== strtolower($filtres[1])) {
							continue;
						}

						$tabResult[] = $unElement;
					}
				}

				return $tabResult;
			} else {
				foreach($arrayValues as $unElement) {
					if($unElement->getNom() === $tabKey[0]) {
						array_shift($tabKey);
						return $this->rechercheValeurTableauMultidim($tabKey, $unElement->getChildren());
					}
				}

				return false;
			}
		}
	}
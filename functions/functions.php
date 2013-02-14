<?php
	function trigger_notice_apps($code) {
		new \Serveur\Exceptions\Types\Notice($code, null, array_slice(func_get_args(), 1));
	}

	function isNull(&$donnee) {
		return (!isset($donnee) || is_null($donnee) || (!is_array($donnee) && trim($donnee) == '') || (is_array($donnee) && empty($donnee)));
	}

	function array_search_recursif($needle, $haystack) {
		foreach($haystack as $key => $value) {
			$current_key = $key;
			if($needle === $value OR (is_array($value) && array_search_recursif($needle, $value) !== false)) {
				return $current_key;
			}
		}
		return false;
	}

	function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues) {
		if (count($tabKey) == 1) {
			return $arrayValues[$tabKey[0]];
		} else {
			if(array_key_exists($tabKey[0], $arrayValues)) {
				$arrayValues = $arrayValues[$tabKey[0]];
				array_shift($tabKey);
				return rechercheValeurTableauMultidim($tabKey, $arrayValues);
			} else {
				return false;
			}
		}
	}

	function getFichierExtension($unFichier) {
		$positionPoint = strrpos($unFichier, '.');
		if (!$positionPoint) {
			return false;
		}
		return strtolower(substr($unFichier, $positionPoint+1));
	}
<?php
/**
 * @param int $erreurNumber
 * @param int $codeErreur
 * @param string $argumentsMessage
 */
function trigger_error_app($erreurNumber, $codeErreur, $argumentsMessage)
{
    call_user_func_array($GLOBALS['global_function_ajouterErreur'], func_get_args());
}

/**
 * @param mixed $donnee
 * @return bool
 */
function isNull(&$donnee)
{
    return (!isset($donnee) || is_null($donnee) || ((is_string($donnee)) && trim($donnee) == '') ||
        ((is_array($donnee) || is_object($donnee)) && empty($donnee)));
}

/**
 * @param string $string
 * @param string $stringRecherche
 * @return bool
 */
function startsWith($string, $stringRecherche)
{
    return !strncmp($string, $stringRecherche, strlen($stringRecherche));
}

/**
 * @param array $keys
 * @param array $array
 * @return bool
 */
function array_keys_exist(array $keys, array $array)
{
    if (count(array_intersect($keys, array_keys($array))) == count($keys)) {
        return true;
    }

    return false;
}

/**
 * @param array $array
 * @param int $case
 * @param bool $flag_rec
 */
function array_change_key_case_recursive(&$array, $case = CASE_LOWER)
{
    $array = array_change_key_case($array, $case);

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            array_change_key_case_recursive($array[$key], $case, true);
        }
    }
}

/**
 * @param string $needle
 * @param array $haystack
 * @return bool|int|string
 */
function array_search_recursif($needle, array $haystack)
{
    foreach ($haystack as $key => $value) {
        $current_key = $key;
        if ($needle === $value OR (is_array($value) && array_search_recursif($needle, $value) !== false)) {
            return $current_key;
        }
    }

    return false;
}

/**
 * @param array $tabKey
 * @param array $arrayValues
 * @return bool
 */
function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues)
{
    if (count($tabKey) == 1) {
        if (array_key_exists($tabKey[0], $arrayValues)) {
            return $arrayValues[$tabKey[0]];
        } else {
            return null;
        }
    } else {
        if (array_key_exists($tabKey[0], $arrayValues)) {
            $arrayValues = $arrayValues[$tabKey[0]];
            array_shift($tabKey);

            return rechercheValeurTableauMultidim($tabKey, $arrayValues);
        } else {
            return null;
        }
    }
}

/**
 * @param string $key
 * @param array $arrayValues
 * @param bool $insensitive
 * @return bool
 */
function array_key_multi_exist($key, array $arrayValues, $insensitive = false)
{
    if ($insensitive === true) {
        array_change_key_case_recursive($arrayValues, CASE_LOWER);
        $key = strtolower($key);
    }

    return !is_null(rechercheValeurTableauMultidim(explode('.', $key), $arrayValues));
}

/**
 * @param string $key
 * @param array $arrayValues
 * @param bool $insensitive
 * @return mixed
 */
function array_key_multi_get($key, array $arrayValues, $insensitive = false)
{
    if ($insensitive === true) {
        array_change_key_case_recursive($arrayValues, CASE_LOWER);
        $key = strtolower($key);
    }

    return rechercheValeurTableauMultidim(explode('.', $key), $arrayValues);
}

/**
 * @param callable $fonction
 * @param array $array
 * @return array
 */
function array_map_recursive($fonction, array $array)
{
    $rarr = array();
    foreach ($array as $k => $v) {
        $rarr[$k] = is_array($v) ? array_map_recursive($fonction, $v) : $fonction($v);
    }

    return $rarr;
}

/**
 * @param array $donnees
 * @param int $level
 * @return string
 */
function arrayToString(array $donnees, $level = 0)
{
    $valeurs = '';

    foreach ($donnees as $clef => $valeur) {
        for ($i = 0; $i < $level; $i++) {
            $valeurs .= "\t";
        }

        if (is_array($valeur)) {
            $valeurs .= $clef . " => \n" . arrayToString($valeur, ($level + 1));
        } else {
            $valeurs .= $clef . " => " . $valeur . "\n";
        }
    }

    return $valeurs;
}
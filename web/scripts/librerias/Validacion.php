<?php

use DateTime;

/**
 * Función que valida con filter, si el número que le llega está dentro del rango de las opciones
 * filter devuelve el número, si no devuelve un booleano falso
 *
 * @param integer $var
 * @param integer $min
 * @param integer $max
 * @param integer $default
 * @return boolean
 */
function validaEntero(int $var, int $min, int $max, int $default): bool
{
    $resultado = filter_var($var, FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => $min,
            'max_range' => $max,
        ],
    ]);
    if ($resultado !== false) {
        $var = $resultado;
        return true;
    } else {
        $var = $default;
        return false;
    }
}

/**
 * Undocumented function
 * Función que valida con filter, si el número que le llega está dentro del rango de las opciones
 * filter devuelve el número, si no devuelve un booleano falso
 * @param integer $var
 * @param integer $min
 * @param integer $max
 * @param integer $default
 * @return void
 */
function validaReal(float $var, float $min, float $max, float $default): bool
{
    $resultado = filter_var($var, FILTER_VALIDATE_FLOAT, [
        'options' => [
            'min_range' => $min,
            'max_range' => $max,
        ],
    ]);
    if ($resultado !== false) {
        $var = $resultado;
        return true;
    } else {
        $var = $default;
        return false;
    }
}


/**
 * Valida una fecha en formato dd/mm/aaaa. Comprueba que tiene 3 partes separadas por "/",
 * que son números, que el año tiene 4 cifras y que la fecha existe. Si se indica una fecha
 * mínima, comprueba además que la fecha no es anterior a ella (la misma fecha sí vale).
 * Si todo va bien deja la fecha con formato correcto (dd/mm/aaaa) y devuelve true;
 * si falla le asigna la fecha por defecto y devuelve false.
 *
 * @param string $fecha
 * @param string $defecto
 * @param string|null $minima fecha mínima permitida (dd/mm/aaaa), o null para no comparar
 * @return bool
 */
function validaFecha(string &$fecha, string $defecto, ?string $minima = null): bool
{
    $partes = explode("/", trim($fecha));
    if (count($partes) != 3) {
        $fecha = $defecto;
        return false;
    }

    [$dia, $mes, $anio] = $partes;

    // Comprobar que son números y que el año tiene 4 cifras
    if (!ctype_digit($dia) || !ctype_digit($mes) || !ctype_digit($anio) || strlen($anio) != 4) {
        $fecha = $defecto;
        return false;
    }

    // Comprobar que la fecha existe (31/02 no vale)
    if (!checkdate((int)$mes, (int)$dia, (int)$anio)) {
        $fecha = $defecto;
        return false;
    }

    // String saneado: 2 cifras para día y mes, 4 para el año
    $fechaSaneada = sprintf("%02d/%02d/%04d", $dia, $mes, $anio);

    // Comparar con la fecha mínima, si se ha indicado
    if ($minima !== null) {
        $fechaComprobar = DateTime::createFromFormat("!d/m/Y", $fechaSaneada);
        $fechaMinima    = DateTime::createFromFormat("!d/m/Y", $minima);

        if ($fechaMinima !== false && $fechaComprobar < $fechaMinima) {
            $fecha = $defecto;
            return false;
        }
    }

    $fecha = $fechaSaneada;
    return true;
}

/**
 * Funcion que valida la hora, esta lo splitea en un array,
 * comprobamos que sea el formato correcto, que los números no se pasan de un límite
 * si esto está mal se devuelve la hora por defecto con false, si esta bien se crea mi formato de hora y se devuelve true
 *
 * @param string $hora
 * @param string $defecto
 * @return boolean
 */
function validaHora(string &$hora, string $defecto): bool
{
    $arrayHora = explode(":", trim($hora));
    if (count($arrayHora) != 3) {
        $hora = $defecto;
        return false;
    }

    // Cada parte tiene que ser un número de 1 o 2 cifras
    foreach ($arrayHora as $parte) {
        if (!ctype_digit($parte) || strlen($parte) > 2) {
            $hora = $defecto;
            return false;
        }
    }

    // Comprobamos que los valores están dentro de los límites
    if ($arrayHora[0] >= 24 || $arrayHora[1] >= 60 || $arrayHora[2] >= 60) {
        $hora = $defecto;
        return false;
    }

    // Devuelvo el string saneado: 2 cifras para hora, minutos y segundos
    $hora = sprintf("%02d:%02d:%02d", $arrayHora[0], $arrayHora[1], $arrayHora[2]);
    return true;
}

/**
 * Función en la cual utilizo el filtro para sanear el email, y después comparo si este email es válido o no
 * si no lo es se introduce en la variable de email el por defecto
 *
 * @param string $var
 * @param string $defecto
 * @return boolean
 */
function validaEmail(string &$var, string $defecto): bool {
    $saneado = filter_var($var, FILTER_SANITIZE_EMAIL);
    if (filter_var($saneado, FILTER_VALIDATE_EMAIL) !== false) {
        $var = $saneado;
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Función que comprueba que una cadena no se pasa de largo de mi límite establecido, si esta se pasa se le mete el valor
 * por defecto.
 *
 * @param string $cadena
 * @param string $longitud
 * @param string $defecto
 * @return bool
 */
function validaCadena(string &$cadena, int $longitud, ?string $defecto = null):bool{
    if (mb_strlen($cadena) > $longitud) {
        $cadena = $defecto;
        return false;
    }
    return true;
}

/**
 * Función para validar con mis expresiones regulares, le tenemos que meter en options nuestra nueva forma de filtro con
 * el regexp, si es verdadero devuelvo true, si no meto la variable por defecto y devuelvo false
 *
 * @param string $var
 * @param string $expresion
 * @param string $defecto
 * @return boolean
 */
function validaExpresion(string &$var, string $expresion, string $defecto): bool {
    $opciones = ['options' => ['regexp' => $expresion]];
    if (filter_var($var, FILTER_VALIDATE_REGEXP, $opciones) !== false) {
        return true;
    }
    $var = $defecto;
    return false;
}

/**
 * Función que valida si un valor está dentro del array, ya sea como valor ($tipo = 1) o como clave ($tipo = 2)
 *
 * @param mixed $var
 * @param array $posibles
 * @param integer $tipo
 * @return boolean
 */
function validaRango(mixed $var, array $posibles, int $tipo = 1): bool
{
    if ($tipo == 2) {
        return (is_int($var) || is_string($var)) && array_key_exists($var, $posibles);
    }
    return in_array($var, $posibles);
}
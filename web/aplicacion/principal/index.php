<?php


include_once(dirname(__FILE__) . "/../../cabecera.php");
require_once (__DIR__ . '/../datos/Muebles.php');

//Incluyo mis dos librerias (apis)
//controlador

$ubicacion = [
    "pagina principal" => "../../index.php",
    "relacion 5" => "./index.php",
    "Ejercicio 2" => "Ejercicio2.php"
];

//Mi array de valores del formulario
$valores = [
    "nombre" => "",
    "contraseña" => "",
    "fecha_nacimiento" => "",
    "fecha_carnet" => [
        "dia" => "",
        "mes" => "",
        "año" => ""
    ],
    "hora_levantarse" => "",
    "estado" => null,
    "estudios" => [],
    "hermanos" => 0,
    "sueldo" => 1100
];

$errores = [];

/**
 * Mi variable de mi clase muebles donde tengo mi array indexado
 */
$arrayMuebles = $muebles;

$tablaMueblesString = tablaMuebles($arrayMuebles);
/**
 * Comprobamos que se ha enviado el formulario
 */
if (isset($_POST["enviar"])) {

    
}

//Solo muestro el resumen si se ha enviado el formulario y no hay ningún error
$mostrarResumen = isset($_POST["enviar"]) && empty($errores);

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");


cabecera();
finCabecera();
inicioCuerpo("2DAW APLICACION", $ubicacion);
cuerpo($valores, $errores, $tablaMueblesString); //llamo a la vista
finCuerpo();
// **********************************************************
////vista cabecera donde podemos ver otros enlaces 

function cabecera() {}

//vista
function cuerpo(array $valores, array $errores, string $tablaMueblesString)
{

    echo $tablaMueblesString;
    // //Si todo es correcto no se muestra el formulario, solo el resumen
    // if ($mostrarResumen) {
    //     echo resumen($valores);
    //     return;
    // }

    // //LOS ECHO SE PONEN EN TODA LA PRÁCTICA PARA QUE SI LE DA ERROR AL MADNA EL FORMULARIO,
    // // QUE EL FORMULARIO SE QUEDE RELLENO

    // if ($errores) {
    //     echo "<div class = 'error'>";
    //     foreach ($errores as $clave => $error) {
    //         echo "$clave => $error <br>";
    //     }
    //     echo "</div>";
    // }

    // //llamo a la función que dibuja el formulario
    // echo formulario($valores, $errores);
}

/**
 * Función que dibuja el formulario. Está separada de la vista, que es la que la llama.
 *
 * @param array $valores valores a mostrar en cada campo
 * @param array $errores errores de validación
 * @return string HTML del formulario
 */
function formulario(array $valores, array $errores): string
{
    ob_start();
?>
    
<?php
    return ob_get_clean();
}

function tablaMuebles(array $muebles): string
{
    $html = "<table>";
    $html .= "<thead><tr><th>Índice</th><th>Tipo</th><th>Nombre</th><th>Fabricante</th><th>Precio</th></tr></thead>";
    $html .= "<tbody>";

    foreach ($muebles as $indice => $mueble) {
        $html .= "<tr>";
        $html .= "<td>" . $indice . "</td>";
        $html .= "<td>" . get_class($mueble) . "</td>";
        $html .= "<td>" . $mueble->getNombre() . "</td>";
        $html .= "<td>" . $mueble->getFabricante() . "</td>";
        $html .= "<td>" . $mueble->getPrecio() . " €</td>";
        $html .= "</tr>";
    }

    $html .= "</tbody></table>";
    return $html;
}

/**
 * Función que dibuja el resumen con los datos introducidos.
 * Muestra el texto de estado y estudios en lugar de su código.
 *
 * @param array $valores datos ya validados
 * @return string HTML del resumen
 */
function resumen(array $valores)
{
    
}
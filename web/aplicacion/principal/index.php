<?php

include_once(dirname(__FILE__) . "/../../cabecera.php");
require_once(__DIR__ . '/../datos/Muebles.php');

mb_internal_encoding("UTF-8");

//controlador

$ubicacion = [
    "pagina principal" => "../../index.php",
    "tienda" => "./index.php"
];

/**
 * Array indexado de muebles (definido en datos/Muebles.php)
 */
$arrayMuebles = $muebles;

// Índice que llega del select del formulario (POST)
$indicePost = is_string($_POST["muebles"] ?? null) ? $_POST["muebles"] : "";

// Botón Modificar Mueble: redirige a la página de modificación pasando el índice por la URL
if (isset($_POST["modificar"]) && isset($arrayMuebles[$indicePost])) {
    header("Location: ./mueblemodificar.php?mueble=" . urlencode($indicePost));
    exit;
}

// Botón Mostrar Mueble: redirige a esta misma página pasando el índice por la URL
if (isset($_POST["mostrar"]) && isset($arrayMuebles[$indicePost])) {
    header("Location: ./index.php?mostrar=" . urlencode($indicePost));
    exit;
}

// Si venimos de la redirección de "Mostrar", el índice llega por GET
$indiceSel = is_string($_GET["mostrar"] ?? null) ? $_GET["mostrar"] : "";
$muebleMostrar = $arrayMuebles[$indiceSel] ?? null;

$tablaMueblesString = tablaMuebles($arrayMuebles);

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");

cabecera();
finCabecera();
inicioCuerpo("TIENDA", $ubicacion);
cuerpo($tablaMueblesString, $arrayMuebles, $indiceSel, $muebleMostrar); //llamo a la vista
finCuerpo();
// **********************************************************

////vista cabecera donde podemos ver otros enlaces
function cabecera() {}

//vista
function cuerpo(string $tablaMueblesString, array $arrayMuebles, string $indiceSel, ?MuebleBase $muebleMostrar)
{
    echo $tablaMueblesString;
    echo formulario($arrayMuebles, $indiceSel);

    if ($muebleMostrar !== null) {
        echo mostrarMueble($muebleMostrar);
    }
}


/**
 * Dibuja el formulario con el select de muebles y los dos botones de envío.
 *
 * @param array $muebles array indexado de muebles
 * @param string $indiceSel índice seleccionado (para que el select se quede marcado)
 * @return string HTML del formulario
 */
function formulario(array $muebles, string $indiceSel = ""): string
{
    ob_start();
?>
    <form method="post" action="./index.php">
        <p>
            <label>Mueble:
                <select name="muebles">
                    <?php foreach ($muebles as $indice => $mueble) { ?>
                        <option value="<?php echo $indice; ?>"
                            <?php if ((string) $indice === $indiceSel) { echo "selected"; } ?>>
                            <?php echo $indice . " - " . get_class($mueble) . " - " . $mueble->getNombre() . " - " . $mueble->getPrecio() . " €"; ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </p>
        <p>
            <input type="submit" name="mostrar" value="Mostrar Mueble">
            <input type="submit" name="modificar" value="Modificar Mueble" formtarget="_blank">
        </p>
    </form>
<?php
    return ob_get_clean();
}

/**
 * Dibuja la tabla con todos los muebles.
 *
 * @param array $muebles array indexado de muebles
 * @return string HTML de la tabla
 */
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
 * Muestra todas las propiedades del mueble, una por línea, sin usar la conversión a cadena.
 *
 * @param MuebleBase $mueble mueble seleccionado
 * @return string HTML con las propiedades y características
 */
function mostrarMueble(MuebleBase $mueble): string
{
    $html = "<p>Clase: " . get_class($mueble) . "</p>";
    $html .= "<p>Nombre: " . $mueble->getNombre() . "</p>";
    $html .= "<p>Fabricante: " . $mueble->getFabricante() . "</p>";
    $html .= "<p>País: " . $mueble->getPais() . "</p>";
    $html .= "<p>Año: " . $mueble->getAnio() . "</p>";
    $html .= "<p>Fecha inicio venta: " . $mueble->getFechaIniVenta() . "</p>";
    $html .= "<p>Fecha fin venta: " . $mueble->getFechaFinVenta() . "</p>";
    $html .= "<p>Precio: " . $mueble->getPrecio() . " €</p>";

    // Propiedades adicionales, según la clase hija
    foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
        $getter = "get" . mb_ucfirst($propiedad);
        $html .= "<p>" . $propiedad . ": " . $mueble->$getter() . "</p>";
    }

    // Características (el foreach las recorre en orden inverso)
    $html .= "<p>Características:</p><ul>";
    foreach ($mueble->getCaracteristicas() as $nombre => $valor) {
        if (is_bool($valor)) {
            $valor = $valor ? "true" : "false";
        }
        $html .= "<li>" . $nombre . ": " . $valor . "</li>";
    }
    $html .= "</ul>";

    return $html;
}
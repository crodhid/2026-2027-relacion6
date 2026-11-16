<?php


include_once(dirname(__FILE__) . "/../../cabecera.php");
require_once(__DIR__ . '/../datos/Muebles.php');


mb_internal_encoding("UTF-8");


//controlador


$ubicacion = [
    "pagina principal" => "../../index.php",
    "tienda" => "./index.php",
    "modificar mueble" => "./mueblemodificar.php"
];


// Primera carga: el índice llega por GET (redirección). Al enviar el formulario: POST (hidden "mueble")
$origen = isset($_POST["enviar"]) ? $_POST : $_GET;
$indice = is_string($origen["mueble"] ?? null) ? $origen["mueble"] : "";
$mueble = $muebles[$indice] ?? null;


$valores = [];
$errores = [];
$mostrarResumen = false;
$todosMateriales = Materiales::casos();


if ($mueble !== null) {


    // Valores iniciales: los datos actuales del mueble según su tipo
    $valores = [
        "nombre" => $mueble->getNombre(),
        "fabricante" => $mueble->getFabricante(),
        "pais" => $mueble->getPais(),
        "anio" => $mueble->getAnio(),
        "fechaIniVenta" => $mueble->getFechaIniVenta(),
        "fechaFinVenta" => $mueble->getFechaFinVenta(),
        "precio" => $mueble->getPrecio(),
        "material" => 0
    ];


    if ($mueble instanceof MuebleReciclado) {
        $valores["porcentajeReciclado"] = $mueble->getPorcentajeReciclado();
    } else {
        $valores["peso"] = $mueble->getPeso();
        $valores["serie"] = $mueble->getSerie();
    }


    // Si se ha enviado el formulario, los valores pasan a ser los enviados
    if (isset($_POST["enviar"])) {
        foreach (array_keys($valores) as $campo) {
            $valores[$campo] = recoge($campo);
        }


        $errores = validar($mueble, $valores, $todosMateriales);


        if (empty($errores)) {
            actualizar($mueble, $valores);
            $mostrarResumen = true;
        }
    }
}


//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");


cabecera();
finCabecera();
inicioCuerpo("TIENDA - MODIFICAR MUEBLE", $ubicacion);
cuerpo($mueble, $indice, $valores, $errores, $mostrarResumen, $todosMateriales); //llamo a la vista
finCuerpo();
// **********************************************************


function cabecera() {}


//vista
function cuerpo(?MuebleBase $mueble, string $indice, array $valores, array $errores, bool $mostrarResumen, array $todosMateriales)
{
    if ($mueble === null) {
        echo "<p>No se ha indicado ningún mueble válido.</p>";
        echo "<p><a href='./index.php'>Ir a Index.php</a></p>";
        return;
    }


    // Si todo es correcto no se muestra el formulario, solo el resumen
    if ($mostrarResumen) {
        echo resumen($mueble);
        return;
    }


    if ($errores) {
        echo "<div class = 'error'>";
        foreach ($errores as $clave => $error) {
            echo "$clave => $error <br>";
        }
        echo "</div>";
    }


    echo formulario($mueble, $indice, $valores, $todosMateriales);
}




/**
 * Recoge un campo del POST como cadena, quitando los espacios del principio y del final
 * (versión multibyte de trim). Si no es una cadena devuelve "".
 *
 * @param string $campo nombre del campo en $_POST
 * @return string valor recogido
 */
function recoge(string $campo): string
{
    $valor = is_string($_POST[$campo] ?? null) ? $_POST[$campo] : "";
    return preg_replace('/^\s+|\s+$/u', '', $valor) ?? "";
}


/**
 * Dibuja el formulario de modificación según el tipo de mueble.
 *
 * @param MuebleBase $mueble mueble que se modifica
 * @param string $indice índice del mueble en el array
 * @param array $valores valores a mostrar en cada campo
 * @return string HTML del formulario
 */
function formulario(MuebleBase $mueble, string $indice, array $valores, array $todosMateriales): string
{
    ob_start();
?>
    <h2>Modificar mueble <?php echo $indice . " (" . get_class($mueble) . ")"; ?></h2>


    <form method="post" action="./mueblemodificar.php">
        <input type="hidden" name="mueble" value="<?php echo $indice; ?>">


        <p>
            <label>Nombre:
                <input type="text" name="nombre" maxlength="40" value="<?php echo $valores["nombre"]; ?>">
            </label>
        </p>


        <p>
            <label>Fabricante:
                <input type="text" name="fabricante" value="<?php echo $valores["fabricante"]; ?>">
            </label>
        </p>


        <p>
            <label>País:
                <input type="text" name="pais" value="<?php echo $valores["pais"]; ?>">
            </label>
        </p>


        <p>
            <label>Año de fabricación:
                <input type="text" name="anio" size="4" maxlength="4" value="<?php echo $valores["anio"]; ?>">
            </label>
        </p>


        <p>
            <label>Fecha inicio venta (dd/mm/aaaa):
                <input type="text" name="fechaIniVenta" value="<?php echo $valores["fechaIniVenta"]; ?>">
            </label>
        </p>


        <p>
            <label>Fecha fin venta (dd/mm/aaaa):
                <input type="text" name="fechaFinVenta" value="<?php echo $valores["fechaFinVenta"]; ?>">
            </label>
        </p>


        <p>
            <label>Precio (€):
                <input type="text" name="precio" value="<?php echo $valores["precio"]; ?>">
            </label>
        </p>

        <p>
            <label>Material:
                <select name="materiales">
                    <?php for($i = 0; $i < count($todosMateriales); $i++) { ?>
                        <option value="<?php echo $todosMateriales[$i]->descripcion(); ?>"
                            >
                            <?php echo $todosMateriales[$i]->descripcion() ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
        </p>


        <?php if ($mueble instanceof MuebleReciclado) { ?>
            <p>
                <label>Porcentaje reciclado (0-100):
                    <input type="text" name="porcentajeReciclado" value="<?php echo $valores["porcentajeReciclado"]; ?>">
                </label>
            </p>
        <?php } else { ?>
            <p>
                <label>Peso (15-300):
                    <input type="text" name="peso" value="<?php echo $valores["peso"]; ?>">
                </label>
            </p>
            <p>
                <label>Serie:
                    <input type="text" name="serie" value="<?php echo $valores["serie"]; ?>">
                </label>
            </p>
        <?php } ?>


        <p><input type="submit" name="enviar" value="Modificar"></p>
    </form>


    <p><a href="./index.php">Ir a Index.php</a></p>
<?php
    return ob_get_clean();
}


/**
 * Valida los datos enviados. La mitad con una instancia auxiliar (métodos set)
 * y la otra mitad directamente con condiciones.
 *
 * @param MuebleBase $mueble mueble original (solo para saber su tipo)
 * @param array $valores datos enviados
 * @return array errores encontrados (vacío si todo es correcto)
 */
function validar(MuebleBase $mueble, array $valores, array $todosMateriales): array
{
    $errores = [];


    // ---- MITAD 1: instancia auxiliar del mismo tipo, llamando a los set ----
    $tipo = get_class($mueble);
    $aux = new $tipo("AUX");


    if (!$aux->setNombre($valores["nombre"])) {
        $errores["nombre"] = "El nombre no puede estar vacío ni superar 40 caracteres";
    }


    if (!ctype_digit($valores["anio"]) || !$aux->setAnio((int) $valores["anio"])) {
        $errores["anio"] = "El año debe estar entre 2020 y el año actual";
    }


    // Las fechas se validan en este orden porque dependen del año y de la fecha de inicio
    if (!$aux->setFechaIniVenta($valores["fechaIniVenta"])) {
        $errores["fechaIniVenta"] = "Fecha de inicio de venta no válida (no puede ser anterior al 01/01 del año de fabricación)";
    }


    if (!$aux->setFechaFinVenta($valores["fechaFinVenta"])) {
        $errores["fechaFinVenta"] = "Fecha de fin de venta no válida (no puede ser anterior a la de inicio)";
    }


    // ---- MITAD 2: directamente con condiciones ----
    if ($valores["pais"] === "" || mb_strlen($valores["pais"]) > 20) {
        $errores["pais"] = "El país no puede estar vacío ni superar 20 caracteres";
    }


    if (!is_numeric($valores["precio"]) || (float) $valores["precio"] < 30) {
        $errores["precio"] = "El precio debe ser un número no menor de 30";
    }


    if ($mueble instanceof MuebleReciclado) {
        if (
            !ctype_digit($valores["porcentajeReciclado"]) ||
            (int) $valores["porcentajeReciclado"] > 100
        ) {
            $errores["porcentajeReciclado"] = "El porcentaje debe ser un entero entre 0 y 100";
        }
    } else {
        if (
            !is_numeric($valores["peso"]) ||
            (float) $valores["peso"] < 15 || (float) $valores["peso"] > 300
        ) {
            $errores["peso"] = "El peso debe estar entre 15 y 300";
        }


        if ($valores["serie"] === "") {
            $errores["serie"] = "La serie no puede estar vacía";
        }
    }

    if (isset($_POST["materiales"])) {
        for ($i=0; $i < count($todosMateriales) ; $i++) { 
            if ($todosMateriales[$i] == $_POST["materiales"]) {
                $valores["material"] = $todosMateriales[$i]->valor();
            }
        }
    }

    return $errores;
}


/**
 * Actualiza el mueble con los datos ya validados.
 */
function actualizar(MuebleBase $mueble, array $valores): void
{
    $mueble->setNombre($valores["nombre"]);
    $mueble->setFabricante($valores["fabricante"]);
    $mueble->setPais($valores["pais"]);
    $mueble->setAnio((int) $valores["anio"]);
    $mueble->setFechaIniVenta($valores["fechaIniVenta"]);
    $mueble->setFechaFinVenta($valores["fechaFinVenta"]);
    $mueble->setPrecio((float) $valores["precio"]);
    $mueble->setMaterial((int)$valores["material"]);
    



    if ($mueble instanceof MuebleReciclado) {
        $mueble->setPorcentajeReciclado((int) $valores["porcentajeReciclado"]);
    } elseif ($mueble instanceof MuebleTradicional) {
        $mueble->setPeso((float) $valores["peso"]);
        $mueble->setSerie($valores["serie"]);
    }
}


/**
 * Dibuja el resumen con los datos del mueble ya modificado.
 *
 * @param MuebleBase $mueble mueble actualizado
 * @return string HTML del resumen
 */
function resumen(MuebleBase $mueble): string
{
    $html = "<h2>Mueble modificado correctamente</h2>";
    $html .= "<p>Clase: " . get_class($mueble) . "</p>";
    $html .= "<p>Nombre: " . $mueble->getNombre() . "</p>";
    $html .= "<p>Fabricante: " . $mueble->getFabricante() . "</p>";
    $html .= "<p>País: " . $mueble->getPais() . "</p>";
    $html .= "<p>Año: " . $mueble->getAnio() . "</p>";
    $html .= "<p>Fecha inicio venta: " . $mueble->getFechaIniVenta() . "</p>";
    $html .= "<p>Fecha fin venta: " . $mueble->getFechaFinVenta() . "</p>";
    $html .= "<p>Precio: " . $mueble->getPrecio() . " €</p>";
    $html .= "<p>Material: " . $mueble->getMaterialDescripcion() . "</p>";


    foreach ($mueble->damePropiedadesAdicionales() as $propiedad) {
        $getter = "get" . mb_ucfirst($propiedad);
        $html .= "<p>" . $propiedad . ": " . $mueble->$getter() . "</p>";
    }


    $html .= "<p><a href='./index.php'>Ir a Index.php</a></p>";
    return $html;
}
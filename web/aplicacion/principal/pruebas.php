<?php

include_once(dirname(__FILE__) . "/../../cabecera.php");

mb_internal_encoding("UTF-8");

//controlador

$ubicacion = [
    "pagina principal" => "../../index.php",
    "tienda" => "./index.php",
    "pruebas" => "./pruebas.php"
];

/**
 * Resultados de las pruebas, agrupados por clase.
 * El orden importa: la última sección agota el límite de instancias.
 */
$resultados = [
    "Caracteristicas" => pruebasCaracteristicas(),
    "MuebleBase (clase abstracta, probada con una instancia de MuebleReciclado)" => pruebasMuebleBase(),
    "MuebleReciclado" => pruebasMuebleReciclado(),
    "MuebleTradicional" => pruebasMuebleTradicional(),
    "Límite de instancias (MAXIMO_MUEBLES)" => pruebasLimite()
];

//dibuja la plantilla de la vista
inicioCabecera("APLICACION PRIMER TRIMESTRE");

cabecera();
finCabecera();
inicioCuerpo("TIENDA - PRUEBAS", $ubicacion);
cuerpo($resultados); //llamo a la vista
finCuerpo();
// **********************************************************

////vista cabecera donde podemos ver otros enlaces
function cabecera() {}

//vista
function cuerpo(array $resultados)
{
    echo vistaResultados($resultados);
}

/**
 * Dibuja una tabla por cada grupo de pruebas.
 *
 * @param array $resultados array titulo => lista de [descripcion, resultado]
 * @return string HTML de las tablas
 */
function vistaResultados(array $resultados): string
{
    $html = "";

    foreach ($resultados as $titulo => $pruebas) {
        $html .= "<h2>" . $titulo . "</h2>";
        $html .= "<table>";
        $html .= "<thead><tr><th>Prueba</th><th>Resultado</th></tr></thead>";
        $html .= "<tbody>";

        foreach ($pruebas as [$descripcion, $resultado]) {
            $html .= "<tr><td>" . $descripcion . "</td><td>" . $resultado . "</td></tr>";
        }

        $html .= "</tbody></table>";
    }

    $html .= "<p><a href='./index.php'>Ir a Index.php</a></p>";
    return $html;
}

// **********************************************************
// Funciones auxiliares del controlador

/**
 * Ejecuta una prueba y devuelve [descripcion, resultado].
 * Si la prueba lanza una excepción o un error, el resultado es el mensaje.
 *
 * @param string $descripcion qué se prueba
 * @param callable $prueba función que ejecuta la prueba y devuelve un valor
 * @return array [descripcion, resultado en texto]
 */
function probar(string $descripcion, callable $prueba): array
{
    try {
        $resultado = formatear($prueba());
    } catch (Throwable $e) {
        $resultado = "EXCEPCIÓN (" . get_class($e) . "): " . $e->getMessage();
    }

    return [$descripcion, $resultado];
}

/**
 * Convierte cualquier valor en texto legible (true/false, arrays, objetos...).
 *
 * @param mixed $valor valor a convertir
 * @return string texto legible
 */
function formatear(mixed $valor): string
{
    if (is_object($valor)) {
        return method_exists($valor, "__toString") ? (string) $valor : get_class($valor);
    }

    if (is_array($valor)) {
        return json_encode($valor, JSON_UNESCAPED_UNICODE);
    }

    return var_export($valor, true);
}

// **********************************************************
// Pruebas de cada clase

/**
 * Pruebas de la clase Caracteristicas (propiedades dinámicas, foreach, ULTIMA, cadena).
 */
function pruebasCaracteristicas(): array
{
    $c = new Caracteristicas();
    $pruebas = [];

    $pruebas[] = probar("Añadir dinámicamente color, material y precio", function () use ($c) {
        $c->color = "rojo";
        $c->material = "madera";
        $c->precio = 300;
        return "añadidas";
    });

    $pruebas[] = probar("Leer la característica color", fn() => $c->color);

    $pruebas[] = probar("Leer una característica que no existe (debe dar error)", fn() => $c->inexistente);

    $pruebas[] = probar("isset sobre una característica existente (color)", fn() => isset($c->color));

    $pruebas[] = probar("isset sobre una característica que no existe", fn() => isset($c->inexistente));

    $pruebas[] = probar("Recorrer con foreach (debe salir en orden inverso)", function () use ($c) {
        $salida = [];
        foreach ($c as $nombre => $valor) {
            $salida[] = $nombre . "=" . var_export($valor, true);
        }
        return implode(", ", $salida);
    });

    $pruebas[] = probar("Convertir a cadena (car1:valor1;car2:valor2;...)", fn() => (string) $c);

    $pruebas[] = probar("Modificar una característica existente (color = azul)", function () use ($c) {
        $c->color = "azul";
        return $c->color;
    });

    $pruebas[] = probar("Añadir la característica ULTIMA", function () use ($c) {
        $c->ULTIMA = true;
        return "añadida";
    });

    $pruebas[] = probar("Añadir una característica nueva después de ULTIMA (debe dar error)", function () use ($c) {
        $c->nueva = 1;
        return "NO dio error";
    });

    $pruebas[] = probar("Modificar una existente después de ULTIMA (precio = 350, debe permitirse)", function () use ($c) {
        $c->precio = 350;
        return $c->precio;
    });

    $pruebas[] = probar("Cadena final", fn() => (string) $c);

    return $pruebas;
}

/**
 * Pruebas de MuebleBase. Al ser abstracta se prueba con una instancia de MuebleReciclado.
 */
function pruebasMuebleBase(): array
{
    $pruebas = [];

    $pruebas[] = probar("MuebleBase es abstracta", fn() => (new ReflectionClass("MuebleBase"))->isAbstract());

    // Año 2022, venta desde 01/03/2022 hasta 31/12/2030
    $m = new MuebleReciclado("sofá", "Roble", "ESPAÑA", 2022, "01/03/2022", "31/12/2030", 120.5, 40);

    $pruebas[] = probar("Estado inicial (__toString)", fn() => (string) $m);
    $pruebas[] = probar("getNombre (se guarda en mayúsculas)", fn() => $m->getNombre());
    $pruebas[] = probar("getFabricante (se añade FaMu: al principio)", fn() => $m->getFabricante());

    // Nombre
    $pruebas[] = probar("setNombre con cadena vacía (false)", fn() => $m->setNombre(""));
    $pruebas[] = probar("setNombre con 41 caracteres (false)", fn() => $m->setNombre(str_repeat("a", 41)));
    $pruebas[] = probar("getNombre tras los fallos (no se ha asignado nada)", fn() => $m->getNombre());
    $pruebas[] = probar("setNombre correcto (mesa baja)", function () use ($m) {
        return $m->setNombre("mesa baja") . " -> " . $m->getNombre();
    });

    // Fabricante
    $pruebas[] = probar("setFabricante con 50 caracteres (se recorta a 30)", function () use ($m) {
        $m->setFabricante(str_repeat("x", 50));
        return mb_strlen($m->getFabricante());
    });
    $pruebas[] = probar("setFabricante que ya empieza por FaMu: (no se duplica)", function () use ($m) {
        $m->setFabricante("FaMu:Nogal");
        return $m->getFabricante();
    });

    // País
    $pruebas[] = probar("setPais con 21 caracteres (false)", fn() => $m->setPais(str_repeat("P", 21)));
    $pruebas[] = probar("setPais correcto (PORTUGAL)", function () use ($m) {
        return $m->setPais("PORTUGAL") . " -> " . $m->getPais();
    });

    // Año
    $pruebas[] = probar("setAnio 2019 (false, anterior a 2020)", fn() => $m->setAnio(2019));
    $pruebas[] = probar("setAnio del año que viene (false, posterior al actual)", fn() => $m->setAnio((int) date("Y") + 1));
    $pruebas[] = probar("setAnio 2022 (true)", function () use ($m) {
        return $m->setAnio(2022) . " -> " . $m->getAnio();
    });

    // Fechas
    $pruebas[] = probar("setFechaIniVenta 31/12/2021 (false, anterior al 01/01/2022)", fn() => $m->setFechaIniVenta("31/12/2021"));
    $pruebas[] = probar("setFechaIniVenta 15/03/2022 (true)", function () use ($m) {
        return $m->setFechaIniVenta("15/03/2022") . " -> " . $m->getFechaIniVenta();
    });
    $pruebas[] = probar("setFechaFinVenta 01/01/2022 (false, anterior a la de inicio)", fn() => $m->setFechaFinVenta("01/01/2022"));
    $pruebas[] = probar("setFechaFinVenta 31/12/2035 (true)", function () use ($m) {
        return $m->setFechaFinVenta("31/12/2035") . " -> " . $m->getFechaFinVenta();
    });

    // Precio
    $pruebas[] = probar("setPrecio 10 (false, menor de 30)", fn() => $m->setPrecio(10));
    $pruebas[] = probar("setPrecio 99.5 (true)", function () use ($m) {
        return $m->setPrecio(99.5) . " -> " . $m->getPrecio();
    });

    // Sobrecarga dinámica
    $pruebas[] = probar("Crear una propiedad dinámica (debe dar error)", function () use ($m) {
        $m->inventada = 1;
        return "NO dio error";
    });
    $pruebas[] = probar("Asignar directamente a una propiedad privada (debe dar error)", function () use ($m) {
        $m->_nombre = "X";
        return "NO dio error";
    });

    // Método de clase
    $pruebas[] = probar("puedeCrear (devuelve si se pueden crear más y cuántas quedan)", function () {
        $quedan = 0;
        $puede = MuebleBase::puedeCrear($quedan);
        return "puede: " . var_export($puede, true) . ", quedan: " . $quedan;
    });

    // Características del mueble
    $pruebas[] = probar("anadirCaracteristicas('saliente', true, 'precio', 300)", function () use ($m) {
        $m->anadirCaracteristicas("saliente", true, "precio", 300);
        return (string) $m->getCaracteristicas();
    });
    $pruebas[] = probar("anadirCaracteristicas con número impar de argumentos (se desecha el último)", function () use ($m) {
        $m->anadirCaracteristicas("color", "rojo", "huerfana");
        return (string) $m->getCaracteristicas();
    });
    $pruebas[] = probar("getCaracteristicas devuelve un objeto de la clase", fn() => get_class($m->getCaracteristicas()));

    $pruebas[] = probar("damePropiedadesAdicionales (redefinida en la clase hija)", fn() => $m->damePropiedadesAdicionales());

    $pruebas[] = probar("__toString final (con las características)", fn() => (string) $m);

    return $pruebas;
}

/**
 * Pruebas de MuebleReciclado.
 */
function pruebasMuebleReciclado(): array
{
    $pruebas = [];

    $pruebas[] = probar("MuebleReciclado es final (no admite subclases)", fn() => (new ReflectionClass("MuebleReciclado"))->isFinal());

    $r = new MuebleReciclado("silla", "Eco", "ESPAÑA", 2023, "01/06/2023", "31/12/2035", 45, 40);

    $pruebas[] = probar("Instancia con valores válidos (__toString)", fn() => (string) $r);
    $pruebas[] = probar("getPorcentajeReciclado", fn() => $r->getPorcentajeReciclado());
    $pruebas[] = probar("damePropiedadesAdicionales", fn() => $r->damePropiedadesAdicionales());
    $pruebas[] = probar("setPorcentajeReciclado -1 (false)", fn() => $r->setPorcentajeReciclado(-1));
    $pruebas[] = probar("setPorcentajeReciclado 101 (false)", fn() => $r->setPorcentajeReciclado(101));
    $pruebas[] = probar("setPorcentajeReciclado 75 (true)", function () use ($r) {
        return $r->setPorcentajeReciclado(75) . " -> " . $r->getPorcentajeReciclado();
    });

    $pruebas[] = probar("Constructor con nombre vacío (debe lanzar excepción)", function () {
        return new MuebleReciclado("");
    });

    // Todos los valores inválidos salvo el nombre: deben caer a los valores por defecto
    $pruebas[] = probar("Constructor con valores inválidos (se asignan los valores por defecto)", function () {
        $d = new MuebleReciclado("banco", "", "PAISDEMASIADOLARGOPARAELLIMITE", 1999, "01/01/1990", "01/01/1900", 5, 500);
        return (string) $d;
    });

    return $pruebas;
}

/**
 * Pruebas de MuebleTradicional.
 */
function pruebasMuebleTradicional(): array
{
    $pruebas = [];

    $pruebas[] = probar("MuebleTradicional es final (no admite subclases)", fn() => (new ReflectionClass("MuebleTradicional"))->isFinal());

    $t = new MuebleTradicional("armario", "Nogal", "ESPAÑA", 2023, "01/06/2023", "31/12/2040", 300, 120.5, "S05");

    $pruebas[] = probar("Instancia con valores válidos (__toString)", fn() => (string) $t);
    $pruebas[] = probar("getPeso", fn() => $t->getPeso());
    $pruebas[] = probar("getSerie", fn() => $t->getSerie());
    $pruebas[] = probar("damePropiedadesAdicionales", fn() => $t->damePropiedadesAdicionales());
    $pruebas[] = probar("setPeso 14 (false, menor de 15)", fn() => $t->setPeso(14));
    $pruebas[] = probar("setPeso 301 (false, mayor de 300)", fn() => $t->setPeso(301));
    $pruebas[] = probar("setPeso 200 (true)", function () use ($t) {
        return $t->setPeso(200) . " -> " . $t->getPeso();
    });
    $pruebas[] = probar("setSerie S09 (true)", function () use ($t) {
        return $t->setSerie("S09") . " -> " . $t->getSerie();
    });
    $pruebas[] = probar("setSerie sin argumentos (true, vuelve al valor por defecto S01)", function () use ($t) {
        return $t->setSerie() . " -> " . $t->getSerie();
    });
    $pruebas[] = probar("setSerie con cadena vacía (true: la serie no tiene restricciones)", fn() => $t->setSerie(""));

    $pruebas[] = probar("Constructor con nombre vacío (debe lanzar excepción)", function () {
        return new MuebleTradicional("");
    });

    $pruebas[] = probar("Constructor con valores inválidos (todo a su valor por defecto; la serie siempre se acepta)", function () {
        $d = new MuebleTradicional("estante", "", "PAISDEMASIADOLARGOPARAELLIMITE", 1999, "01/01/1990", "01/01/1900", 5, 5, "S02");
        return (string) $d;
    });

    return $pruebas;
}

/**
 * Pruebas del límite de instancias. Va la última porque agota el máximo.
 */
function pruebasLimite(): array
{
    $pruebas = [];

    $pruebas[] = probar("Instancias que aún se pueden crear", function () {
        $quedan = 0;
        MuebleBase::puedeCrear($quedan);
        return $quedan;
    });

    $pruebas[] = probar("Crear muebles hasta superar el máximo (debe lanzar excepción)", function () {
        $creados = [];
        for ($i = 0; $i <= MuebleBase::MAXIMO_MUEBLES; $i++) {
            $creados[] = new MuebleTradicional("limite " . $i);
        }
        return "NO dio error";
    });

    $pruebas[] = probar("puedeCrear después de alcanzar el límite", function () {
        $quedan = 0;
        $puede = MuebleBase::puedeCrear($quedan);
        return "puede: " . var_export($puede, true) . ", quedan: " . $quedan;
    });

    return $pruebas;
}
<?php 
error_reporting(0);
define("RUTABASE", dirname(__FILE__));
//define("MODO_TRABAJO","produccion"); //en "produccion o en desarrollo
define("MODO_TRABAJO","desarrollo"); //en "produccion o en desarrollo

if (MODO_TRABAJO=="produccion")
    error_reporting(0);
    else 
        error_reporting(E_ALL);  

// Autocarga original (sin cambios)
spl_autoload_register(function ($clase){
    $ruta=RUTABASE."/scripts/clases/";
    $fichero=$ruta."$clase.php";
    
    if (file_exists($fichero))
        {
            require_once($fichero);
        }
      else
        {
            throw new Exception("La clase $clase no se ha encontrado.");
        }
});

// Autocarga de las clases de la práctica (curso2026).
// El tercer parámetro (true) hace que se consulte ANTES que la anterior;
// si el fichero no existe aquí, la autocarga original sigue su camino.
spl_autoload_register(function ($clase){
    $fichero = RUTABASE . "/aplicacion/clases/curso2026/$clase.php";

    if (file_exists($fichero))
        {
            require_once($fichero);
        }
}, true, true);

include(RUTABASE."/aplicacion/plantilla/plantilla.php");
include(RUTABASE. "/scripts/librerias/Validacion.php");
// Borrada la línea include(RUTABASE. "/aplicacion/clases/curso2026"); (era un include de una carpeta)

//creo todos los objetos que necesita mi aplicación
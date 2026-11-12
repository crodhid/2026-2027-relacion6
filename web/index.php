<?php
include_once(dirname(__FILE__) . "/cabecera.php");
//controlador


$usuario=getenv("MYSQL_USER");

//dibuja la plantilla de la vista
inicioCabecera("Tienda");
cabecera();
finCabecera();
inicioCuerpo("Tienda");
cuerpo();  //llamo a la vista
finCuerpo();
// **********************************************************

//vista
function cabecera() 
{}

//vista
function cuerpo()
{
?>
    <br><br>
    <a href="./aplicacion/principal/index.php">Relacion 6</a>
    

<?php
}

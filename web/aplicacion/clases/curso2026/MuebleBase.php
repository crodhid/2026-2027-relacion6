<?php

abstract class MuebleBase
{
    /**
     * Número máximo de muebles que se pueden crear
     */
    const MAXIMO_MUEBLES = 10;

    /**
     * Número de muebles (instancias) creados hasta el momento
     */
    private static int $_mueblesCreados = 0;

    /**
     * Propiedades de instancia
     */
    private string $_nombre;
    private string $_fabricante = 'FaMu:';
    private string $_pais = 'ESPAÑA';
    private int $_anio = 2020;
    private string $_fechaIniVenta = '01/01/2020';
    private string $_fechaFinVenta = '31/12/2040';
    private float $_precio = 30;

    /**
     * No se permite la sobrecarga dinámica de propiedades
     */
    public function __set(string $name, mixed $value): void
    {
        throw new Exception("No se permite crear propiedades dinámicas");
    }

    /**
     * NOMBRE
     */
    public function getNombre(): string
    {
        return $this->_nombre;
    }

    /**
     * Comprueba que el nombre no esté vacío ni supere 40 caracteres.
     * Lo guarda en mayúsculas. Si falla, no asigna nada.
     *
     * @param string $nombre
     * @return bool
     */
    public function setNombre(string $nombre): bool
    {
        $nombre = mb_strtoupper(trim($nombre));
        if ($nombre === '' || mb_strlen($nombre) > 40) {
            return false;
        }
        $this->_nombre = $nombre;
        return true;
    }

    /**
     * FABRICANTE
     */
    public function getFabricante(): string
    {
        return $this->_fabricante;
    }

    /**
     * Añade "FaMu:" al principio si no lo tiene y recorta a 30 caracteres.
     *
     * @param string $fabricante
     * @return bool
     */
    public function setFabricante(string $fabricante): bool
    {
        if (!str_starts_with($fabricante, "FaMu:")) {
            $fabricante = 'FaMu:' . $fabricante;
        }
        $this->_fabricante = mb_substr($fabricante, 0, 30);
        return true;
    }

    /**
     * PAÍS
     */
    public function getPais(): string
    {
        return $this->_pais;
    }

    /**
     * Comprueba que el país no supere 20 caracteres. Si falla devuelve false
     *
     * @param string $pais
     * @return bool
     */
    public function setPais(string $pais): bool
    {
        if (!validaCadena($pais, 30)) {
            return false;
        }

        $this->_pais = $pais;
        return true;
    }


    /**
     * Función que devuelve el año
     *
     * @return int
     */
    public function getAnio(): int
    {
        return $this->_anio;
    }

    /**
     * Función que valida si el año es correcto o no antes de hacerle el set
     *
     * @param int $anio
     * @return boolean
     */
    public function setAnio(int $anio): bool
    {
        if (!validaEntero($anio, 2020, 2026, 2020)) {
            return false;
        }

        $this->_anio = $anio;
        return true;
    }


    /**
     * Función que devuelve la fecha de inicio de venta
     *
     * @return string
     */
    public function getFechaIniVenta(): string
    {
        return $this->_fechaIniVenta;
    }

    /**
     * Función para verificar si la fecha introducida es correcta, si no lo es devuelve false
     * @param string $fecha
     * @return boolean
     */
    public function setFechaIniVenta(string $fecha): bool
    {
        //Creo mi fecha minima
        $fechaMinima = "01/01/" . $this->_anio;
        if (!validaFecha($fecha, "01/01/2020", $fechaMinima)) {
            return false;
        }
        $this->_fechaIniVenta = $fecha;
        return true;
    }

    /**
     * Función que devuelve la fecha final de venta
     *
     * @return string
     */
    public function getFechaFinVenta(): string
    {
        return $this->_fechaFinVenta;
    }

    /**
     * Función que valida la fecha final de venta
     *
     * @param string $fecha
     * @return boolean
     */
    public function setFechaFinVenta(string $fecha): bool {
        if (!validaFecha($fecha, "31/12/2040", $this->getFechaIniVenta())) {
            return false;
        }
        $this->_fechaFinVenta = $fecha;
        return true;
    }

    /**
     * Función que me devuelve el precio
     *
     * @return float
     */
    public function getPrecio():float{
        return $this->_precio;
    }

    /**
     * Función que me establece el precio, si todo sale bien devuelve true, si sale mal devuelve false
     *
     * @return boolean
     */
    public function setPrecio():bool{
        
    }
}

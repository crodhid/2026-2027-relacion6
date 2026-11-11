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
    private string $_fabricante;
    private string $_pais;
    private int $_anio;
    private string $_fechaIniVenta;
    private string $_fechaFinVenta;
    private float $_precio;

    //Propiedad privada de tipo caracteristica
    private Caracteristicas $_caracteristica;


    /**
     * Constructor en el cual todo vamos a ir metiendolo con set, validando que no me devuelve falso.
     * Si me devuelve falso tengo que meterle mis datos por defecto 
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIniVenta = '01/01/2020',
        string $fechaFinVenta = '31/12/2040',
        float $precio = 30
    ) {
        if (!self::puedeCrear()) {
            throw new Exception("No se pueden crear más muebles, has llegado al límite de instancias");
        }

        if (!$this->setNombre($nombre)) {
            throw new Exception("No se puede asignar este nombre");
        }

        $this->setFabricante($fabricante);

        if (!$this->setPais($pais)) {
            $this->_pais = 'ESPAÑA';
        }

        if (!$this->setAnio($anio)) {
            $this->_anio = 2020;
        }

        // setFechaIniVenta depende del año, por eso va después de setAnio
        if (!$this->setFechaIniVenta($fechaIniVenta)) {
            $this->_fechaIniVenta = "01/01/" . $this->_anio;
        }

        // setFechaFinVenta depende de la fecha de inicio
        if (!$this->setFechaFinVenta($fechaFinVenta)) {
            $this->_fechaFinVenta = '31/12/2040';
        }

        if (!$this->setPrecio($precio)) {
            $this->_precio = 30;
        }

        self::$_mueblesCreados++;
    }




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
    public function setFechaFinVenta(string $fecha): bool
    {
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
    public function getPrecio(): float
    {
        return $this->_precio;
    }

    /**
     * Función que me establece el precio, si todo sale bien devuelve true, si sale mal devuelve false
     *
     * @return boolean
     */
    public function setPrecio(float $precio): bool
    {
        if ($precio < 30) {
            return false;
        }
        $this->_precio = $precio;
        return true;
    }

    /**
     * Función que heredarán las clases hijas, esta devuelve un array de las propiedades que se le va a añadir
     *
     * @return array
     */
    abstract function damePropiedadesAdicionales(): array;

    /**
     * Función que me indica si puedo crear mas instancias de esta clase o no
     *
     * @param integer|null $num
     * @return boolean
     */
    public static function puedeCrear(?int &$num = null): bool
    {
        $num = self::MAXIMO_MUEBLES - self::$_mueblesCreados;
        return $num > 0;
    }


    /**
     * Función la cual añade todos los valores que le pases, si los valores son impares el 
     * último se desecha
     */
    public function añadirCaracteristicas(mixed ...$args)
    {
        //Desechamos el último elemento si es impar
        if ((count($args) % 2) == 1) {
            array_pop($args);
        }
        //Voy guardando clave y valor, voy recorriendo el array de dos en dos
        for ($i = 0; $i < count($args); $i += 2) {
            $this->_caracteristica->{$args[$i]} = $args[$i + 1];
        }
    }

    /**
     * Función la cual devuelve un objeto con las características
     *
     * @return object
     */
    public function getCaracteristicas(): object {
        return $this->_caracteristica;
    }


    /**
     * Método toString
     *
     * @return string
     */
    public function __toString()
    {
        return "MUEBLE de clase" . get_class() . " con nombre " . $this->getNombre() . ", fabricante" . $this->getFabricante() .
            ", fabricado en " . $this->getPais() . " a partir del año " . $this->getAnio() . ", vendido desde " . $this->getFechaIniVenta() .
            " hasta " . $this->getFechaFinVenta() . ", precio" . $this->getPrecio();
    }
}

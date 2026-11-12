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

    // Propiedad privada de tipo Caracteristicas
    private Caracteristicas $_caracteristica;

    /**
     * Constructor: todo se asigna con los set. Si el nombre falla se lanza excepción;
     * en cualquier otro campo, si falla el set se asigna el valor por defecto.
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

        // Inicializamos el objeto de características
        $this->_caracteristica = new Caracteristicas();

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
     */
    public function setPais(string $pais): bool
    {
        if (!validaCadena($pais, 20)) {
            return false;
        }

        $this->_pais = $pais;
        return true;
    }

    /**
     * AÑO
     */
    public function getAnio(): int
    {
        return $this->_anio;
    }

    /**
     * Valida que el año esté entre 2020 y el año actual
     */
    public function setAnio(int $anio): bool
    {
        if (!validaEntero($anio, 2020, (int) date('Y'), 2020)) {
            return false;
        }

        $this->_anio = $anio;
        return true;
    }

    /**
     * FECHA INICIO VENTA
     */
    public function getFechaIniVenta(): string
    {
        return $this->_fechaIniVenta;
    }

    /**
     * Valida que la fecha no sea anterior al 1 de enero del año de fabricación
     */
    public function setFechaIniVenta(string $fecha): bool
    {
        // Fecha mínima: 1 de enero del año de inicio de fabricación
        $fechaMinima = "01/01/" . $this->_anio;
        if (!validaFecha($fecha, "01/01/2020", $fechaMinima)) {
            return false;
        }
        $this->_fechaIniVenta = $fecha;
        return true;
    }

    /**
     * FECHA FIN VENTA
     */
    public function getFechaFinVenta(): string
    {
        return $this->_fechaFinVenta;
    }

    /**
     * Valida que la fecha no sea anterior a la fecha de inicio de venta
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
     * PRECIO
     */
    public function getPrecio(): float
    {
        return $this->_precio;
    }

    /**
     * El precio no puede ser menor de 30
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
     * Será redefinida en las clases hijas: devuelve un array con el nombre
     * de las propiedades adicionales a la clase base
     */
    abstract public function damePropiedadesAdicionales(): array;

    /**
     * Indica si se pueden crear más instancias y, por referencia,
     * cuántas quedan por crear
     */
    public static function puedeCrear(?int &$num = null): bool
    {
        $num = self::MAXIMO_MUEBLES - self::$_mueblesCreados;
        return $num > 0;
    }

    /**
     * Añade las características recibidas en pares (nombre, valor).
     * Si el número de argumentos es impar, se desecha el último.
     */
    public function anadirCaracteristicas(mixed ...$args): void
    {
        // Desechamos el último elemento si hay un número impar
        if (count($args) % 2 == 1) {
            array_pop($args);
        }

        // Recorremos el array de dos en dos: clave y valor
        $total = count($args);
        for ($i = 0; $i < $total; $i += 2) {
            $this->_caracteristica->{$args[$i]} = $args[$i + 1];
        }
    }

    /**
     * Devuelve las características asignadas al mueble (como objeto)
     */
    public function getCaracteristicas(): Caracteristicas
    {
        return $this->_caracteristica;
    }

    /**
     * Convierte la instancia en cadena, incluyendo las características
     */
    public function __toString(): string
    {
        return "MUEBLE de clase " . static::class . " con nombre " . $this->getNombre() .
            ", fabricante " . $this->getFabricante() .
            ", fabricado en " . $this->getPais() . " a partir del año " . $this->getAnio() .
            ", vendido desde " . $this->getFechaIniVenta() . " hasta " . $this->getFechaFinVenta() .
            ", precio " . $this->getPrecio() .
            ", características: " . $this->_caracteristica;
    }
}
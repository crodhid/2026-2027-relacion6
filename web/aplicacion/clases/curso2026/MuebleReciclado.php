<?php

final class MuebleReciclado extends MuebleBase
{
    private int $_porcentajeReciclado = 10;

    /**
     * Constructor: llama primero al de la clase base (nombre, máximo de instancias, etc.)
     * y después asigna el porcentaje. Si el set falla, se queda el valor por defecto (10).
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIniVenta = '01/01/2020',
        string $fechaFinVenta = '31/12/2040',
        float $precio = 30,
        int $porcentajeReciclado = 10
    ) {
        parent::__construct($nombre, $fabricante, $pais, $anio, $fechaIniVenta, $fechaFinVenta, $precio);

        if (!$this->setPorcentajeReciclado($porcentajeReciclado)) {
            $this->_porcentajeReciclado = 10;
        }
    }

    /**
     * Función que me devuelve mi variable de instancia pero sin acceder a ella de forma directa
     *
     * @return integer
     */
    public function getPorcentajeReciclado(): int
    {
        return $this->_porcentajeReciclado;
    }

    /**
     * Función para modificar mi variable de instancia, aquí estamos controlando que esté entre 0 y 100.
     * Si no lo está devuelve false y no asigna nada (en el constructor se asignará el valor por defecto).
     *
     * @param integer $porcentaje
     * @return boolean
     */
    public function setPorcentajeReciclado(int $porcentaje): bool
    {
        if (!validaEntero($porcentaje, 0, 100, 10)) {
            return false;
        }

        $this->_porcentajeReciclado = $porcentaje;
        return true;
    }

    /**
     * Devuelve un array con el nombre de las propiedades adicionales a las de la clase base
     *
     * @return array
     */
    #[Override]
    public function damePropiedadesAdicionales(): array
    {
        return ["porcentajeReciclado"];
    }

    /**
     * Añade el porcentaje reciclado a la cadena de la clase base
     *
     * @return string
     */
    #[Override]
    public function __toString(): string
    {
        return parent::__toString() . ", porcentaje de reciclado " . $this->getPorcentajeReciclado();
    }
}
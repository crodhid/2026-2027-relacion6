<?php

final class MuebleTradicional extends MuebleBase
{
    private float $_peso = 15;
    private string $_serie = "S01";

    /**
     * Constructor: llama primero al de la clase base (nombre, máximo de instancias, etc.)
     * y después asigna peso y serie. Si el set del peso falla, se asigna el valor por defecto.
     */
    public function __construct(
        string $nombre,
        string $fabricante = 'FaMu:',
        string $pais = 'ESPAÑA',
        int $anio = 2020,
        string $fechaIniVenta = '01/01/2020',
        string $fechaFinVenta = '31/12/2040',
        float $precio = 30,
        float $peso = 15,
        string $serie = "S01"
    ) {
        parent::__construct($nombre, $fabricante, $pais, $anio, $fechaIniVenta, $fechaFinVenta, $precio);

        if (!$this->setPeso($peso)) {
            $this->_peso = 15;
        }

        if (!$this->setSerie($serie)) {
            $this->_serie = "S01";
        }
    }

    /**
     * Getter and setter
     */

    public function getPeso(): float
    {
        return $this->_peso;
    }

    /**
     * Función que valida si mi peso está entre 15 y 300. Si no lo está devuelve false y no asigna nada
     * (en el constructor se asignará el valor por defecto)
     *
     * @param float $peso
     * @return boolean
     */
    public function setPeso(float $peso): bool
    {
        if (!validaReal($peso, 15, 300, 15)) {
            return false;
        }
        $this->_peso = $peso;
        return true;
    }

    /**
     * Función que me devuelve mi serie
     *
     * @return string
     */
    public function getSerie(): string
    {
        return $this->_serie;
    }

    /**
     * Función que asigna la serie
     *
     * @param string $serie
     * @return boolean
     */
    public function setSerie(string $serie = "S01"): bool
    {
        $this->_serie = $serie;
        return true;
    }

    /**
     * Función que me devuelve en un array el nombre de las propiedades extra de esta clase
     *
     * @return array
     */
    #[Override]
    public function damePropiedadesAdicionales(): array
    {
        return ["Peso", "Serie"];
    }

    /**
     * Método toString que le añado las propiedades nuevas de esta clase
     *
     * @return string
     */
    #[Override]
    public function __toString(): string
    {
        return parent::__toString() . ", peso " . $this->getPeso() . ", serie " . $this->getSerie();
    }
}
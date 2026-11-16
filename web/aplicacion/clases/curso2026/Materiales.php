<?php

enum Materiales : int {
    case madera = 5;
    case hierro = 10;
    case metal = 15;
    case poliester = 20;

    /**
     * Función que me devuelve el valor string de mis cases del enum
     */
    function descripcion():string{
        return $this->name;
    }

    /**
     * Función que me devuelve el valor tipo int asociado a mi string
     *
     * @return integer
     */
    function valor():int{
        return $this->value; 
    }

    /**
     * Función que me devuelve un array con todos los valores del enum
     * cases es una funcion de php propia para los enums
     *
     * @return array
     */
    static function casos():array{
        return self::cases();
    }
}
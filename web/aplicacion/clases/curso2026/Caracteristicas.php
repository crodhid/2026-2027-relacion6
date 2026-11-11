<?php

class Caracteristicas implements IteratorAggregate{
    //Array donde vamos a ir guardando mis datos de las carácteristicas
    private array $_datos = [];
    //La inicializamos a false hasta que se introduzca ULTIMA
    private bool $_cerrada = false;


    /**
     * Función en la cual estamos viendo antes de añadir si cerrada esta en true o falso, si el valor de la key ya existe y
     * comprobando si es ULTIMA el la key para poder cerrar el array
     *
     * @param string $nombre
     * @param mixed $valor
     */
    public function __set(string $nombre,mixed $valor)
    {
        //Primero tenemos que comprobar si la clave existe para ver si la está modificando y no insertando
        $existe = array_key_exists($nombre, $this->_datos);
        if (!$existe && $this->_cerrada) {
            throw new Exception("No puedes añadir más características una vez añadido ULTIMA");
            
        }

        $this->_datos[$nombre] = $valor;

        //toca comprobar si lo que hemos añadido es ultima
        if (mb_strtoupper($nombre) == "ULTIMA") {
            $this->_cerrada = true;
        }
    }

    /**
     * Función en la cual comprobamos si existe la key que me está pasando dentro de mi array,
     * si no existe devuelvo una excepción, si existe devuelvo el valor.
     *
     * @param string $nombre
     * @return mixed
     */
    public function __get(string $nombre):mixed
    {
        if (!array_key_exists($nombre, $this->_datos)) {
            throw new Exception("La clave no existe");
        }
        return $this->_datos[$nombre];
    }

    /**
     * Función que me dice si eso que le he pasado es distinto de nulo o no
     *
     * @param string $nombre
     * @return boolean
     */
    public function __isset(string $nombre):bool
    {
        return isset($this->_datos[$nombre]);
    }

    /**
     * Función que hace que cuando hacen un for each los valores se lean al revés
     * el true está puesto para especificarle que me mantenga los valores de las keys
     *
     * @return Traversable
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator(array_reverse($this->_datos),true);
    }

    /**
     * Función toString la cual devuelve una cadena
     *
     * @return string
     */
    public function __toString():string
    {
        $cadena = "";
        foreach ($this->_datos as $key => $value) {
            $cadena .= $key . ":" . $value .";";
        } 

        return $cadena;
    }

}
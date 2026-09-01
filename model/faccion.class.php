<?php

class Faccion {

    private $idFaccion;
    private $idJuego;
    private $descFaccion;
    private $indActivo;

    public function __construct($idFaccion, $idJuego, $descFaccion, $indActivo = 1) {
        $this->idFaccion = $idFaccion;
        $this->idJuego = $idJuego;
        $this->descFaccion = $descFaccion;
        $this->indActivo = $indActivo;
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
        return $this;
    }
}

?>

<?php

class Juego {

    private $idJuego;
    private $descJuego;
    private $audAlta;

    public function __construct($idJuego, $descJuego, $audAlta = null) {
        $this->idJuego = $idJuego;
        $this->descJuego = $descJuego;
        $this->audAlta = $audAlta;
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

<?php

/* Descripcion: POJO de Liga
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 29-09-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */
class Liga {

  private $idLiga;
  private $nombre;
  private $numFases;
  private $numRondas;
  private $indActivo;
  private $fecIni;
  private $fecFin;
  private $logo;
  private $idJuego;

  public function __construct($fIdLiga, $fNombre, $fNumFases, $fNumRondas, $fIndActivo, $fFecIni,  $fFecFin, $fLogo, $fIdJuego ) 
  { 

      $this->idLiga = $fIdLiga;
      $this->nombre = $fNombre;
      $this->numFases = $fNumFases;
      $this->numRondas = $fNumRondas;
      $this->indActivo = $fIndActivo;
      $this->fecIni = $fFecIni;
      $this->fecFin = $fFecFin;
      $this->logo = $fLogo;
      $this->idJuego = $fIdJuego;
  } 
  
  /* GETTERS Y SETTERS */
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
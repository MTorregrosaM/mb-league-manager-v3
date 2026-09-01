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
class Fase {

	private $idLiga;
	private $numFase;
	//private $numRonda;
	private $fecIni;
	private $fecFin;
	private $claveCifrada;


	public function __construct($fIdLiga, $fNumFase, $fClaveCifrada, $fFecIni, $fFecFin ) 
	{ 

	    $this->idLiga = $fIdLiga;
	    $this->numFase = $fNumFase;
	  //  $this->numRonda = $fNumRonda;
	    $this->claveCifrada = $fClaveCifrada;
	    $this->fecIni = $fFecIni;
	    $this->fecFin = $fFecFin;
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
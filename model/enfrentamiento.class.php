<?php

/* Descripcion: POJO de Enfrentamiento
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 29-09-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */
class Enfrentamiento {

	private $idLiga;
	private $idEnfrentamiento;
	private $idFase;
	private $numRonda;
	private $idJugador1;
	private $idJugador2;
	private $resultadoJugador1;
	private $resultadoJugador2;
	private $valPinturaJug1;
	private $valPinturaJug2;
	private $idJugVictoriaConcedida; 
	private $indValidado;
	private $fechaBatalla;
	private $valDeportividadJug1;
	private $valDeportividadJug2;

	public function __construct($fIdEnfrentamiento, $fIdLiga, $fIdFase, $fNumRonda, $fIdJugador1,  $fIdJugador2, $fResultadoJugador1, $fResultadoJugador2, $fValPinturaJug1, $fValPinturaJug2, $fIdJugVictoriaConcedida, $fFechaBatalla, $fIndValidado, $valDeportividadJug1, $valDeportividadJug2 ) 
	{ 

	    $this->idEnfrentamiento = $fIdEnfrentamiento;
	    $this->idLiga = $fIdLiga;
	    $this->idFase = $fIdFase;
	    $this->numRonda = $fNumRonda;
	    $this->idJugador1 = $fIdJugador1;
	    $this->idJugador2 = $fIdJugador2;
	    $this->resultadoJugador1 = $fResultadoJugador1;
	    $this->resultadoJugador2 = $fResultadoJugador2;
	    $this->valPinturaJug1 = $fValPinturaJug1;
	    $this->valPinturaJug2 = $fValPinturaJug2;
	    $this->idJugVictoriaConcedida = $fIdJugVictoriaConcedida;
	    $this->fechaBatalla = $fFechaBatalla;
	    $this->indValidado = $fIndValidado;
	    $this->valDeportividadJug1 = $valDeportividadJug1;
	    $this->valDeportividadJug2 = $valDeportividadJug2;

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
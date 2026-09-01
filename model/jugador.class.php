<?php

/* Descripcion: POJO de Jugador
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 29-09-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */
class Jugador {

	private $idLiga;
	private $idJugador;
	private $nick;
	private $nombre;
	private $apellido1;
	private $apellido2;
	private $foto;
	private $telefono;
	private $email;
	private $bando;
	private $puntosPintura;

	public function __construct($fIdLiga, $fIdJugador, $fNick, $fNombre,  $fApellido1, $fApellido2, $fFoto, $fTelefono, $fEmail, $fBando, $fPuntosPintura ) 
	{ 

		$this->idLiga = $fIdLiga;
	    $this->idJugador = $fIdJugador;
	    $this->nick = $fNick;
	    $this->nombre = $fNombre;
	    $this->apellido1 = $fApellido1;
	    $this->apellido2 = $fApellido2;
	    $this->foto = $fFoto;
	    $this->telefono = $fTelefono;
	    $this->email = $fEmail;
	    $this->bando = $fBando;
	    $this->puntosPintura = $fPuntosPintura;
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
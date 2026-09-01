<?php

/* Descripcion: POJO de Liga
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 15-11-2017
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */
class Usuario {

	private $idUsuario;
	private $nick;
	private $pass;
	private $rol;


	public function __construct($idUsuario, $nick, $pass, $rol  ) 
	{ 

	    $this->idUsuario = $idUsuario;
	    $this->nick = $nick;
	    $this->pass = $pass;
	    $this->rol = $rol;
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
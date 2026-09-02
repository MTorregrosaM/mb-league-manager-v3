<?php

/* Descripcion: Liga
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 15-11-2017
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */

class controllerUsuario {

  private $oConexBD;
  private $oUsuario;

  public function __construct( ) 
  { 
    $this->oConexBD = new ConexBD();

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



    /* MÉTODOS PÚBLICOS */

    /* datos de usuario */
    public function recuperarDatosUsuario( $fIdUsuario ){
    try {
            $queryDB = "SELECT nick, pass, rol
              FROM mb_usuarios
              WHERE idUsuario = ?";
         $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'i', array((int) $fIdUsuario));

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $this->oUsuario = new Usuario ( $fIdUsuario, $fila[0], $fila[1], $fila[2]  ) ;
          return  $this->oUsuario; 
        }
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarDatosUsuario - usuario.controller.php"); 
      return null;   
    }

    }
  
    public function loginUsuario( $fNick, $fPass ){
    try {
     $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada(
       "SELECT idUsuario, pass FROM mb_usuarios WHERE nick = ?", "s", array((string) $fNick)
     );

      if ($resultadoBD != null){
      foreach ($resultadoBD as $fila) {
    $hashValido = password_verify((string) $fPass, (string) $fila[1]);
    $hashLegacy = strlen((string) $fila[1]) === 32 && hash_equals((string) $fila[1], md5((string) $fPass));
    if ($hashValido || $hashLegacy) {
      if ($hashLegacy) {
        $nuevoHash = password_hash((string) $fPass, PASSWORD_DEFAULT);
        $this->oConexBD->ejecutarConsultaPreparada(
          "UPDATE mb_usuarios SET pass = ? WHERE idUsuario = ?", "si", array($nuevoHash, (int) $fila[0]), 1
        );
      }
      return $fila[0];
    }
      }
      }else{
      return 0;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "loginUsuario - usuario.controller.php"); 
      return null;   
    }

    }

    /* método para recuperar el listado de registros. */
    public function recuperarListadoUsuarios (  $fNick = null, $fRol = null, $numPag = 0 ) {
  
    try {
      // calculamos el num de pag

      $numPag =  ($numPag > 0)? ($numPag * 10) :  $numPag ;
  
        $queryDB = "SELECT  idUsuario, nick, pass, rol, DATE_FORMAT(ult_acceso, '%d-%m-%Y %H:%i:%s' )
              FROM mb_usuarios
              WHERE 1=1 ";
      $tipos = '';
      $parametros = array();
            
      // GESTIONAMOS FILTROS
      if ( $fNick != null) { $queryDB .= " AND UPPER(nick) LIKE UPPER(?) "; $tipos .= 's'; $parametros[] = '%' . $fNick . '%'; }
      if ( $fRol != null) { $queryDB .= " AND UPPER(rol) LIKE UPPER(?) "; $tipos .= 's'; $parametros[] = '%' . $fRol . '%'; }

      // ORDENAMOS
        $queryDB .= " ORDER BY idUsuario ASC ";

      // PAGINAMOS
      $queryDB .= " LIMIT " . $numPag . ", 10 ";

      $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, $tipos, $parametros);

      $arrResultados = array ();

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1], $fila[2] , $fila[3], $fila[4]  ) ;

          array_push($arrResultados, $arrObjeto);
        }
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarListadoUsuarios - usuario.controller.php");  
      return null;   
    }
    }




    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function modificarDatosUsuario( $fIdUsuario, $fNick, $fPass, $fRol ){
      
	    try {


	      // comprobamos los campos que han cambiado
	      $this->oUsuario = $this->recuperarDatosUsuario ( $fIdUsuario );
	        
	      $aux = 0; 
	      $auxLogo = 0; 
	        $queryDB = "UPDATE mb_usuarios SET ";

	        if ($fNick != $this->oUsuario->nick){

	          $queryDB .= ($aux > 0)? " , " : "";
	          $queryDB .= " nick = '". $fNick ."' ";
	          $aux = 1;
	        } 
          if ($fPass !== '' && !password_verify($fPass, $this->oUsuario->pass)){
	          $queryDB .= ($aux > 0)? " , " : "";
            $queryDB .= " pass = '". $this->oConexBD->escaparCadena(password_hash($fPass, PASSWORD_DEFAULT)) ."' ";
	          $aux = 1;
	        }
	        if ($fRol != $this->oUsuario->rol){

	          $queryDB .= ($aux > 0)? " , " : "";
	          $queryDB .= " rol = '". $fRol ."' ";
	          $aux = 1;
	        } 
	        $queryDB .= " WHERE idUsuario = " . $fIdUsuario;

	  
	      if ($aux == 1){ 
	        $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

	        if ($resultadoBD >= 1){
	          return 1; 
	        }else{
	          return 2;
	        }
	      }else{
	        return 3;
	      }

	    }catch(Exception $e){
	      $oLog = Log::getInstance();
	      $oLog->trazaLog ($e, "modificarDatosUsuario - usuario.controller.php"); 
	      return null;   
	    }
	 
    }

    

    /* método para MODIFICAR PERMISOS */
    public function modificarPermisosUsuario( $fIdUsuario, $idsLigasUsuario ){
      
	    try {

      	$fIdUsuario = (int) $fIdUsuario;
      	if($fIdUsuario <= 0) {
	    		return 2;
	    	}
      	$idsLigasUsuario = is_array($idsLigasUsuario) ? $idsLigasUsuario : array();

          $queryDB = "DELETE FROM mb_ligas_usuarios WHERE idUsuario = " . $fIdUsuario;
	        $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


			foreach($idsLigasUsuario as $idLiga) {
        $idLiga = (int) $idLiga;
        if ($idLiga <= 0) {
          continue;
        }
            	$queryDB = "INSERT INTO mb_ligas_usuarios (idUsuario, idLiga) VALUES(" . $fIdUsuario . "," . $idLiga . ")" ;	    	 
	        	$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
         	}
	       

          return 1;

	    }catch(Exception $e){
	      $oLog = Log::getInstance();
	      $oLog->trazaLog ($e, "modificarPermisosUsuario - usuario.controller.php"); 
	      return null;   
	    }
 
    }





    /* método para ALTA NUEVA */
    public function altaNuevoUsuario( $fNick, $fPass, $fRol ){

    try {
      if ( $fNick == null || $fPass == null || $fRol == null ) {
        return 2;
      }

      // validamos que no existe un usuario con ese nick ya registrado      
      $queryDB = "SELECT nick FROM mb_usuarios WHERE nick = '" . $fNick . "'";
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $existeUsuario = true;
      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
             $existeUsuario = true;
        }
      }else{
         $existeUsuario = false;
      }

      if (!$existeUsuario) {
        // insertamos el nuevo registro
          $queryDB = "INSERT INTO mb_usuarios ( nick, pass, rol, ult_acceso )
            VALUES ('" . $this->oConexBD->escaparCadena($fNick) . "', '" . $this->oConexBD->escaparCadena(password_hash($fPass, PASSWORD_DEFAULT)) . "','" .$this->oConexBD->escaparCadena($fRol)."' , '". Date('Y-m-d H:i:s') . "' )";
        
        $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

         if ($resultadoBD > 0 ){

            return 1; 
          }
      }else{
        return 2;
      }
        

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "altaNuevoUsuario - usuario.controller.php");  
      return null;   
    }
    }




    /* método para BORRAR un elemento */
    public function borrarUsuario ( $fIdUsuario ) {
    
    try {

      $this->oUsuario = $this->recuperarDatosUsuario ( $fIdUsuario );

        $queryDB = "DELETE FROM mb_usuarios
            WHERE idUsuario = ".$fIdUsuario ;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
            

        $queryDB = "DELETE FROM mb_ligas_usuarios
            WHERE idUsuario = ".$fIdUsuario ;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

    }catch(Exception $e){
      $oLog = Log::getInstance();

      $oLog->trazaLog ($e, "borrarUsuario - usuario.controller.php"); 
      return null;   
    }
    }




    /* método para ALTA NUEVA */
    public function registroUltimoAcceso( $fIdUsuario ){
  
    try {
      
      // insertamos el nuevo registro
      // fecha de y hora actual
      $ahora = Date('Y-m-d H:i:s', strtotime('+1 hours') );
      $queryDB = "UPDATE mb_usuarios SET ult_acceso = '". $ahora . "' 
             WHERE idUsuario = " . $fIdUsuario;
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

      

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "registroUltimoAcceso - usuario.controller.php");  
      return null;   
    }
    }


    /* método para recuperar el las ligas de un usuario. */
    public function recuperarLigasUsuario (  $fIdUsuario ) {
  
		try {

		  $queryDB = "SELECT  idLiga
				  FROM mb_ligas_usuarios
				  WHERE idUsuario = " . $fIdUsuario . " ORDER BY 1 DESC";            

		  $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

		  $ligasUsuario = "";
		  if ($resultadoBD != null){
			foreach ($resultadoBD as $fila) {

		   		$ligasUsuario .= ", " .   $fila[0];

			}
			return substr($ligasUsuario, 1,strlen($ligasUsuario)-1);  
		  }else{
			return null;
		  }

		}catch(Exception $e){
		  $oLog = Log::getInstance();
		  $oLog->trazaLog ($e, "recuperarLigasUsuario - usuario.controller.php");  
		  return null;   
		}
    }


    /* Listado de empresas para los formularios de búsqueda */
    public function recuperarSelectUsuarios(  ){
    try {
        $queryDB = "SELECT idUsuario, nick FROM mb_usuarios ";    
        $queryDB .="  ORDER BY nombre";

      $arrResultados = array ();
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        // primero metemos el <option 0> en caso de que sea para el formulario de alta
      

        foreach ($resultadoBD as $fila) {
          $arr = array();
          array_push($arr, $fila[0], $fila[1] ) ;
          array_push($arrResultados, $arr);
        }
          
        return $arrResultados;  
      }else{
        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectUsuarios - usuario.controller.php"); 
      return null;   
    }
    }



    /* Listado de empresas para los formularios de búsqueda */
    public function recuperarSelectLigasUsuario( $idUsuario ){
    try {
      $idUsuario = (int) $idUsuario;
      $queryDB = "SELECT T1.idLiga,
        CONCAT(COALESCE(YEAR(COALESCE(T1.fecIni, (SELECT MIN(fecIni) FROM mb_fases WHERE mb_fases.idLiga = T1.idLiga))), ''), '_', T1.nombre),
        T2.idUsuario
          FROM mb_ligas T1
          LEFT JOIN mb_ligas_usuarios T2 ON T1.idLiga = T2.idLiga AND T2.idUsuario = " . $idUsuario . "
          ORDER BY T1.fecIni DESC, T1.nombre";

      $arrResultados = array ();
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
          foreach ($resultadoBD as $fila) {
			  $arr = array();
			  array_push($arr, $fila[0], $fila[1],  $fila[2]  ) ;
			  array_push($arrResultados, $arr);
        }
          
        return $arrResultados;  
      }else{
        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectLigasUsuario - usuario.controller.php"); 
      return null;   
    }
    }


    
    /* método para cambiar el formato de fecha de la BD al UTC español */
    public function formatoFecha ( $utcUsa = true, $fFecha = null ){

      if ($utcUsa){
      $user_tz = 'America/Los_Angeles';
      $format = "Y-m-d";
    }else{
      $user_tz = 'Europe/Madrid';
      $format = "d-m-Y";
    }

    $schedule_date = new DateTime($fFecha, new DateTimeZone($user_tz) );
    $schedule_date->setTimeZone(new DateTimeZone('UTC'));
    return $schedule_date->format($format);
  }


  /* método para PAGINADOR de empresas */
    public function paginadorUsuarios (  $fNick = null ) {
    
    try {

        $queryDB = "SELECT count(1)
              FROM mb_usuarios
              WHERE 1=1 ";
            
      // GESTIONAMOS FILTROS
      if ( $fNick != null) { $queryDB .= " AND UPPER(nick) LIKE UPPER('%" . $fNick  . "%') ";  }        


      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $resultado = $fila[0];
        }
        return $resultado;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "paginadorUsuarios - usuario.controller.php"); 
      return null;   
    }
    }

}



?>
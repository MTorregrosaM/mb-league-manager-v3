<?php

/* Descripcion: Liga
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 13-05-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */

class controllerJugador {

  private $oConexBD;
  private $oJugador;

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

    /* datos de liga */
    public function recuperarDatosJugador( $idJugador ){
    try {

        $queryDB = "SELECT idLiga, nick, nombre, apellido1, apellido2, foto, telefono, email, bando, puntosPintura
              FROM mb_jugadores
              WHERE 1=1 ";
        $queryDB .= "AND idJugador = '". $idJugador . "' ";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $this->oJugador = new Jugador ( $idJugador, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8], $fila[9] ) ;
          return  $this->oJugador;  
        }
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarDatosJugador - jugador.controller.php"); 
      return null;   
    }

    }




    /* método para recuperar el listado de registros. */
    public function recuperarListadoJugadores ( $fIdLiga = null, $fNick = null,  $fEmail = null, $fTelefono = null, $numPag = 0, $numLim = 10, $validarEnfrentamientos = false, $fNumFase = null, $fNumRonda = null ) {
  
    try {

      // calculamos el num de pag

      $numPag =  ($numPag > 0)? ($numPag * 10) :  $numPag ;
  
        $queryDB = "SELECT  idJugador, idLiga, nick, nombre, apellido1, apellido2, foto, telefono, email, bando, puntosPintura
              FROM mb_jugadores 
              WHERE 1=1 ";
            
      // GESTIONAMOS FILTROS
        if ( $fIdLiga != null ) { $queryDB .= " AND idLiga = " . $fIdLiga ;  }
      if ( $fNick != null ) { $queryDB .= " AND UPPER(nick) LIKE UPPER('%" . $fNick  . "%') ";  }
      if ( $fEmail != null ) { $queryDB .= " AND UPPER(email) LIKE '%" . $fEmail. "%'";  }
      if ( $fTelefono != null ) { $queryDB .= " AND telefono LIKE '%" . fTelefono . "%'";  }  
      if ( $validarEnfrentamientos ) { $queryDB .= " AND (idJugador not in (select distinct idJugador1 from mb_enfrentamientos WHERE numFase = " . $fNumFase . " AND numRonda = " . $fNumRonda  ."  )
                               AND idJugador not in (select distinct idJugador2 from mb_enfrentamientos WHERE numFase = " . $fNumFase . " AND numRonda = " . $fNumRonda   ."))";  }   

      // ORDENAMOS
        $queryDB .= " ORDER BY nick DESC ";

      // PAGINAMOS
      $queryDB .= " LIMIT " . $numPag . ", ".  $numLim . "  ";

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array ();

      if ($resultadoBD != null){

        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8], $fila[9], $fila[10]  ) ;

          array_push($arrResultados, $arrObjeto);
        }
        return $arrResultados;  
      }else{
      	
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarListadoJugadores - jugador.controller.php"); 
      return null;   
    }
    }



    /* método para recuperar el listado de registros. */
    public function recuperarSelectJugadores ( $fIdLiga = null, $fNumFase = null, $fNumRonda = null, $validarGrabEnfrentamiento = false, $validarDobleLista = false ) {

    try {
      // VALIDAMOS NUMFASE
      $fNumFase = ( $fNumFase == null )? 1 : $fNumFase;
        $queryDB = "SELECT distinct  idJugador, nick
              FROM mb_jugadores t1
              join mb_fases t2 
              on t1.idLiga = t2.idLiga";
      
      if ($validarGrabEnfrentamiento) {
        $queryDB .= " left join mb_enfrentamientos t3
              on t1.idJugador = t3.idJugador1 AND t1.idLiga = t3.idLiga
              left join mb_enfrentamientos t4
              on t1.idJugador = t4.idJugador2 AND t1.idLiga = t4.idLiga";
      }     
  

      $queryDB .= " WHERE 1=1 ";        
            
      // GESTIONAMOS FILTROS
        if ( $fIdLiga != null ) { $queryDB .= " AND t1.idLiga = " . $fIdLiga ;  }
        //if ( $fNumFase != null ) { $queryDB .= " AND numFase = " . $fNumFase ;  }
        //if ( $fNumRonda != null ) { $queryDB .= " AND t3.numRonda = " . $fNumRonda ;  }
        if ( $validarGrabEnfrentamiento ) { $queryDB .= " AND (( t3.numFase = " . $fNumFase . " AND t1.idJugador = t3.idJugador1  AND t3.valPinturaJug2 IS NULL) 
                                  OR ( t4.numFase = " . $fNumFase . " AND t1.idJugador = t4.idJugador2   AND t4.valPinturaJug1 IS NULL)) ";  }

      // ORDENAMOS
          $queryDB .= " ORDER BY nick ASC ";


      if ($validarDobleLista) {
        $queryDB = "SELECT DISTINCT idJugador, nick FROM
              (
                select distinct B.idJugador, B.nick from mb_enfrentamientos A
                left join mb_jugadores B on A.idJugador1 = B.idJugador and A.idLiga = B.idLiga
                left join mb_jugadores C on A.idJugador2 = C.idJugador and A.idLiga = C.idLiga
                where A.idLiga = " .  $fIdLiga . "
                and (B.bando IN(3,6) AND C.bando IN(3,6))
                AND A.valPinturaJug1 IS NULL
              ) t1
              union
              SELECT DISTINCT idJugador, nick FROM
              (
                select distinct C.idJugador, C.nick from mb_enfrentamientos A
                left join mb_jugadores B on A.idJugador1 = B.idJugador and A.idLiga = B.idLiga
                left join mb_jugadores C on A.idJugador2 = C.idJugador and A.idLiga = C.idLiga
                where A.idLiga = " .  $fIdLiga . "
                and (B.bando IN(3,6) AND C.bando IN(3,6))
                AND A.valPinturaJug2 IS NULL
              ) t1";
      } 

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array ();

      if ($resultadoBD != null){

        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1] ) ;

          array_push($arrResultados, $arrObjeto);

        }     
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectJugadores - jugador.controller.php");  
      return null;   
    }
    }


    /* método para recuperar el listado de registros. */
    public function recuperarJugadorContrincante ( $fIdLiga, $fNumRonda, $fIdJugador1, $fNumFase ) {
  
    try {
            /* $queryDB = "SELECT distinct idEnfrentamiento, idjugador1, idJugador2, nick, resultadoJugador1, resultadoJugador2, fechaBatalla
              FROM mb_enfrentamientos t1
              join mb_jugadores t2 
              on t1.idJugador2 = t2.idJugador
              WHERE t1.idLiga = " . $fIdLiga ."
              AND numRonda = " . $fNumRonda ."
              and t1.idJugador1 = " .  $fIdJugador1 . "
              and t1.numFase = " .$fNumFase . "
              UNION 
              SELECT distinct idEnfrentamiento,idJugador1, idJugador2,nick, resultadoJugador1, resultadoJugador2, fechaBatalla
              FROM mb_enfrentamientos t1
              join mb_jugadores t2 
              on t1.idJugador1 = t2.idJugador
              WHERE t1.idLiga = " . $fIdLiga ."
              AND numRonda = " . $fNumRonda ."
              and t1.numFase = " .$fNumFase . "
              and t1.idJugador2 = " .  $fIdJugador1;  */

              $queryDB = "SELECT idEnfrentamiento, 
              CASE WHEN idJugador1 = " .  $fIdJugador1 . " then idJugador1 ELSE idJugador2 END  AS idJugador1, 
               CASE WHEN idJugador1 = " .  $fIdJugador1 . " then idJugador2 ELSE idJugador1 END  AS idJugador2, 
               nick, 
              CASE WHEN idJugador1 = " .  $fIdJugador1 . " then resultadoJugador1 ELSE resultadoJugador2 END  AS resultadoJugador1, 
               CASE WHEN idJugador1 = " .  $fIdJugador1 . " then resultadoJugador2 ELSE resultadoJugador1 END  AS resultadoJugador2, 
               fechaBatalla 
              FROM
              (
              SELECT distinct idEnfrentamiento, idjugador1, idJugador2, nick, resultadoJugador1, resultadoJugador2, fechaBatalla 
              FROM mb_enfrentamientos t1 join mb_jugadores t2 on t1.idJugador2 = t2.idJugador 
              WHERE t1.idLiga = " . $fIdLiga ." AND numRonda = " . $fNumRonda ." and t1.idJugador1 = " .  $fIdJugador1 . " and t1.numFase = " .$fNumFase . "
              UNION SELECT distinct idEnfrentamiento,idJugador1, idJugador2,nick, resultadoJugador1, resultadoJugador2, fechaBatalla 
              FROM mb_enfrentamientos t1 join mb_jugadores t2 on t1.idJugador1 = t2.idJugador 
              WHERE t1.idLiga = " . $fIdLiga ." AND numRonda = " . $fNumRonda ." and t1.numFase = " .$fNumFase . " and t1.idJugador2 = " .  $fIdJugador1 . "
              ) A";

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array ();

      if ($resultadoBD != null){

        foreach ($resultadoBD as $fila) {
          array_push($arrResultados, $fila[0], $fila[1] , $fila[2], $fila[3] , $fila[4],$fila[5],$this->formatoFecha(false,$fila[6])  ) ;


        }     
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarJugadorContrincante - jugador.controller.php");  
      return null;   
    }
    }


    /* método para recuperar el listado de registros. SOLO FLAMES OF WAR */
    public function recuperarJugadoresContrincante ( $fIdLiga, $fIdJugador1, $fNumFase ) {
  
    try {
        $queryDB = "SELECT distinct idjugador1, idJugador2, nick, numRonda
              FROM mb_enfrentamientos t1
              join mb_jugadores t2 
              on t1.idJugador2 = t2.idJugador
              WHERE t1.idLiga = " . $fIdLiga ."
              and t1.idJugador1 = " .  $fIdJugador1 . "
              and t1.numFase = " .$fNumFase . "
            and t2.bando in (3,6)
              UNION 
              SELECT distinct idjugador1, idJugador2, nick, numRonda
              FROM mb_enfrentamientos t1
              join mb_jugadores t2 
              on t1.idJugador1 = t2.idJugador
              WHERE t1.idLiga = " . $fIdLiga ."
              and t1.numFase = " .$fNumFase . "
              and t1.idJugador2 = " .  $fIdJugador1 . "
            and t2.bando in (3,6)";            

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array();

      if ($resultadoBD != null){

        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1] , $fila[2], $fila[3]  ) ;

          array_push($arrResultados, $arrObjeto);

        }     
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarJugadoresContrincante - jugador.controller.php");  
      return null;   
    }
    }



    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function modificarDatosJugador( $fIdJugador, $fNick, $fNombre, $fApellido1, $fApellido2, $fFoto, $fTelefono, $fEmail, $fBando, $fPuntosPintura ){
      
    try {

    
      // comprobamos los campos que han cambiado
      $this->oJugador = $this->recuperarDatosJugador ( $fIdJugador );
  
      $aux = 0; 
      $auxfoto = 0; 
        $queryDB = "UPDATE mb_jugadores SET ";

        if ($fNombre != $this->oJugador->nombre){

          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " nombre = '". $fNombre ."' ";
          $aux = 1;
        }
        if ($fNick != $this->oJugador->nick){

          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " nick = '". $fNick ."' ";
          $aux = 1;
        } 
        if ($fApellido1 != $this->oJugador->apellido1){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " apellido1 = '". $fApellido1 ."' ";
          $aux = 1;
        }
        if ($fApellido2 != $this->oJugador->apellido2){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " apellido2 = '". $fApellido2 ."' ";
          $aux = 1;
        }
        if ($fTelefono != $this->oJugador->telefono){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " telefono = '". $fTelefono ."' ";
          $aux = 1;
        }  
        if ($fEmail != $this->oJugador->email){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " email = '". $fEmail ."' ";
          $aux = 1;
        }    
        if ($fBando != $this->oJugador->bando){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " bando = '". $fBando ."' ";
          $aux = 1;
        }    
        if ($fPuntosPintura != $this->oJugador->puntosPintura){
          $fPuntosPintura = ($fPuntosPintura > 0) ? 2 : 0;
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " puntosPintura = '". $fPuntosPintura ."' ";
          $aux = 1;
        }   
      
        if ($fFoto != $this->oJugador->foto){
        // borramos el foto previo 
        if (file_exists("./recursos/img/jugadores/". $this->oJugador->foto))
          unlink("./recursos/img/jugadores/". $this->oJugador->foto);
        
          $fFotoAux = "foto-".$fIdJugador.substr($fFoto,strpos($fFoto, "."));
        $queryDB = "UPDATE mb_jugadores SET foto = '". $fFotoAux . "'
                WHERE idJugador  = '". $fIdJugador . "'";
        $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
          $auxfoto = 1;
        }
        $queryDB .= " WHERE idJugador = " . $fIdJugador;

    
        if ($auxfoto == 1){
          return 1;
        }elseif ($aux == 1){ 
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
      $oLog->trazaLog ($e, "modificarDatosJugador - jugador.controller.php"); 
      return null;   
    }
 
    }




    /* método para ALTA NUEVA */
    public function altaNuevoJugador( $fIdLiga, $fNick, $fNombre, $fApellido1, $fApellido2, $fFoto, $fTelefono, $fEmail, $fBando, $fPuntosPintura ){

    try {
      if ( trim((string) $fIdLiga) === '' || trim((string) $fNick) === '' || trim((string) $fEmail) === '' ) {
        return 2;
      }

      $fIdLiga = (int) $fIdLiga;
      $fTelefono = trim((string) $fTelefono) === '' ? 0 : (int) $fTelefono;
      $fPuntosPintura = (int) $fPuntosPintura;
      $fNick = addslashes(trim((string) $fNick));
      $fNombre = addslashes(trim((string) $fNombre));
      $fApellido1 = addslashes(trim((string) $fApellido1));
      $fApellido2 = addslashes(trim((string) $fApellido2));
      $fFoto = addslashes(trim((string) $fFoto));
      $fEmail = addslashes(trim((string) $fEmail));
      $fBando = addslashes(trim((string) $fBando));

      $queryDB = "INSERT INTO mb_jugadores( idLiga, nick, nombre, apellido1, apellido2, foto, telefono, email, bando, puntosPintura, audAlta )
            VALUES (" . $fIdLiga . ",'". $fNick . "', '" . $fNombre . "', '" . $fApellido1 . "', '" . $fApellido2 . "','" . $fFoto . "' , ". $fTelefono . ", '" .  $fEmail . "' , '" . 
              $fBando . "', " . $fPuntosPintura . ", '" . Date('Y-m-d H:i:s') . "' )";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


      if ($resultadoBD >= 1){

        //calculamos el ID para asignar la foto
        $queryDB = "SELECT MAX(idJugador)  as idJugador FROM mb_jugadores";

        $resultadoBDaux = $this->oConexBD->ejecutarConsulta($queryDB);

        if ($resultadoBDaux != null){

          foreach ($resultadoBDaux as $fila) {

            $fFoto = ($fFoto != "")? "foto-".$fila[0].substr($fFoto,strpos($fFoto, ".")) : "foto-".$fila[0].".png";
            $queryDB = "UPDATE mb_jugadores SET foto = '". $fFoto . "'
                  WHERE idJugador  = '". $fila[0] . "'";
            $resultadoBDaux2 = $this->oConexBD->ejecutarConsulta($queryDB, 1);

            if ($resultadoBDaux2 >= 1){

              return 1; 
            }
          }
        }
      }else{
        return 2;
      } 

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "altaNuevoJugador - jugador.controller.php");  
      return null;   
    }
    }




    /* método para BORRAR un elemento */
    public function borrarJugador ( $fIdJugador ) {
    
    try {

      $this->oJugador = $this->recuperarDatosJugador ( $fIdJugador );
    
        $queryDB = "DELETE FROM mb_jugadores
            WHERE idJugador = ".$fIdJugador ;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
            
      
      if ($resultadoBD >= 1){

        // borramos cualquier imagen con las 3 extensiones permitidas
        if (file_exists("./recursos/img/jugadores/foto-".  $fIdJugador .".png"))
          unlink("./recursos/img/jugadores/foto-".  $fIdJugador .".png");
        if (file_exists("./recursos/img/jugadores/foto-".  $fIdJugador .".jpg"))
          unlink("./recursos/img/jugadores/foto-".  $fIdJugador .".jpg");
        if (file_exists("./recursos/img/jugadores/foto-".  $fIdJugador  .".gif"))
          unlink("./recursos/img/jugadores/foto-".  $fIdJugador .".gif");
        return true;  
      }else{
        return false;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();

      $oLog->trazaLog ($e, "borrarJugador - jugador.controller.php"); 
      return null;   
    }
    }




  /* método para PAGINADOR de empresas */
    public function paginadorJugadores (  $fIdLiga = null, $fNick = null,  $fEmail = null, $fTelefono = null ) {
    
    try {

         $queryDB = "SELECT count(1)
              FROM mb_jugadores
              WHERE 1=1 ";
            
      // GESTIONAMOS FILTROS
        if ( $fIdLiga != null) { $queryDB .= " AND idLiga = " . $fIdLiga ;  }
      if ( $fNick != null) { $queryDB .= " AND UPPER(nick) LIKE UPPER('%" . $fNick  . "%') ";  }
      if ( $fEmail != null) { $queryDB .= " AND UPPER(email) LIKE '%" . $fEmail. "%'";  }
      if ( $fTelefono != null) { $queryDB .= " AND telefono LIKE '%" . fTelefono . "%'";  }       

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
      $oLog->trazaLog ($e, "paginadorJugadores - jugador.controller.php");  
      return null;   
    }
    }


    /* método para recuperar el último Jugador */
    public function recuperarUltimoJugador ( $fNick,  $fEmail ) {

    try {

        $queryDB = "SELECT max(idJugador)
              FROM mb_jugadores
            WHERE 1=1
            ";
      // GESTIONAMOS FILTROS
      if ( $fNick != null) { $queryDB .= " AND nick = '" . $fNick  . "' ";  }
      if ( $fEmail != null) { $queryDB .= " AND email = '" . $fEmail  . "' ";  }
    
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
      $oLog->trazaLog ($e, "recuperarUltimoJugador - jugador.controller.php");  
      return null;   
    }
    }




    /* LISTAS */


    /* método para recuperar el listado de registros. */
    public function recuperarListadoListas (  $fIdJugador, $fIdLiga, $fNumFase = null, $fBando = null ) {
  
    try {

    $queryDB = "SELECT  idJugador, numFase, urlDocumento, bando, fechaDocumento
              FROM mb_listas 
              WHERE 1=1 
              AND idLiga = " . $fIdLiga . " 
              AND idJugador = " . $fIdJugador . " " ;
        if ( $fNumFase != null && (int) $fNumFase > 0) { $queryDB .= " AND numFase = " . (int) $fNumFase ;  }
        if ( $fBando != null) { $queryDB .= " AND bando = '" . $fBando . "'" ;  }

      // ORDENAMOS
        $queryDB .= " ORDER BY numFase ASC ";


      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array ();

      if ($resultadoBD != null){

        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4] ) ;

          array_push($arrResultados, $arrObjeto);
        }
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarListadoListas - jugador.controller.php");  
      return null;   
    }
    }   




    /* método para ALTA NUEVA */
    public function altaNuevaLista( $fIdJugador, $fIdLiga, $fNumFase, $fUrlDocumento, $fBando  ){

    try {
      if ( $fIdLiga == null ||  $fNumFase == null || $fIdJugador == null || $fUrlDocumento == null || $fBando == null ) {
        return 2;
      }

      // comprobamos si ya existe la lista grabada
      $queryDB  ="SELECT COUNT(1) AS CONT FROM mb_listas 
            WHERE idJugador = ".$fIdJugador . "
            AND idLiga = " . $fIdLiga . "
            AND numFase = ". $fNumFase . "
            AND bando = '" .  $fBando . "'";
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);


      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          if ($fila[0] > 0) {
            return 3; // devolvemos error por duplicado
          }
        }
      }

      $queryDB = "INSERT INTO mb_listas ( idLiga, idJugador, numFase, urlDocumento, bando, fechaDocumento )
            VALUES (" . $fIdLiga . ",". $fIdJugador . ", " . $fNumFase . ", '" . $fUrlDocumento . "', '". $fBando . "','" . Date('Y-m-d H:i:s') . "' )";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


      if ($resultadoBD >= 1){
        return 1; 

      }else{
        return 2;
      } 

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "altaNuevaLista - jugador.controller.php");  
      return null;   
    }
    }




    /* método para BORRAR un elemento */
    public function borrarLista ( $fIdLiga, $fIdJugador, $fNumFase, $fBando, $fRutaDoc ) {
    
    try {

      // $fRutaDoc = str_replace("/", "\\", $fRutaDoc);
        $queryDB = "DELETE FROM mb_listas
            WHERE idJugador = ".$fIdJugador . "
            AND idLiga = " . $fIdLiga . "
            AND numFase = ". $fNumFase . "
            AND bando = '". $fBando . "'";

      $resultadoBD  = $this->oConexBD->ejecutarConsulta($queryDB, 1);
            
      $resultadoBD = 1;
      if ($resultadoBD >= 1){

        if (file_exists($fRutaDoc)){
          unlink($fRutaDoc);
        }

        return true;  
      }else{
        return false;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();

      $oLog->trazaLog ($e, "borrarLista - jugador.controller.php"); 
      return null;   
    }
    }



    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function modificarLista( $fIdJugador, $fIdLiga, $fNumFase, $fBando, $fUrlDocumento, $fRuta ){
      
    try {

    
      // comprobamos si ya existe la lista grabada
      $queryDB  ="SELECT urlDocumento  FROM mb_listas 
            WHERE idJugador = ".$fIdJugador . "
            AND idLiga = " . $fIdLiga . "
            AND numFase = ". $fNumFase . "
            AND bando = '" .  $fBando . "'";
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);


      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
            $queryDB = "UPDATE mb_listas 
                  SET urlDocumento = '" . $fUrlDocumento . "'
                  WHERE idJugador = ".$fIdJugador . "
                  AND idLiga = " . $fIdLiga . "
                  AND numFase = ". $fNumFase . "
                  AND bando = '" .  $fBando . "'";
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
          
            if ($resultadoBD >= 1){

              if (file_exists($fRuta."/". $fila[0]))
                unlink($fRuta."/". $fila[0]);

              return 1; 
            }else{
              return 2;
            }

          
        }
      }

  
    
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "modificarDatosJugador - jugador.controller.php"); 
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



}



?>
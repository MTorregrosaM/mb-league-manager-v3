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

class controllerLiga {

  private $oConexBD;
  private $oLiga;

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

    public function existeLigaConNombre($fNombre, $fIdLigaExcluir = null) {
      $queryDB = 'SELECT idLiga FROM mb_ligas WHERE UPPER(TRIM(nombre)) = UPPER(TRIM(?))';
      $tipos = 's';
      $parametros = array($fNombre);

      if ($fIdLigaExcluir !== null) {
        $queryDB .= ' AND idLiga <> ?';
        $tipos .= 'i';
        $parametros[] = (int) $fIdLigaExcluir;
      }

      $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, $tipos, $parametros);
      return is_array($resultadoBD) && count($resultadoBD) > 0;
    }

    /* datos de liga */
    public function recuperarDatosLiga( $idLiga ){
    try {
        $queryDB = "SELECT nombre, numFases, numRondas, indActivo, DATE_FORMAT(fecIni, '%d-%m-%Y' ), DATE_FORMAT(fecFin, '%d-%m-%Y' ), logo, idJuego
              FROM mb_ligas 
              WHERE 1=1 ";
        $queryDB .= "AND idLiga = '". $idLiga . "' ";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $this->oLiga = new Liga ( $idLiga, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7]  ) ;
          return  $this->oLiga; 
        }
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarDatosLiga - liga.controller.php"); 
      return null;   
    }

    }


    /* datos de liga y fases para cruces de doble lista */
    public function recuperarDatosLigaDobleLista( ){
    try {
        $queryDB = "SELECT distinct t1.idLiga, nombre as nombreLiga, numFase
            FROM mb_ligas t1 join mb_fases t2 
            on t1.idLiga = t2.idLiga
            where indActivo =1 
            and t1.idJuego in(1,2)
            and t2.fecIni <= date(NOW())
            and (t2.fecFin IS NULL OR t2.fecFin >= date(NOW()))";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto,  $fila[0], $fila[1], $fila[2] ) ;
          return  $arrObjeto; 
        }
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarDatosLigaDobleLista - liga.controller.php"); 
      return null;   
    }

    }



    /* método para recuperar el listado de registros. */
    public function recuperarListadoLigas (  $fNombre = null, $fIndActivo = null, $fFecIni = null, $numPag = 0, $idLigasUsuario = null ) {
  
    try {

      // si es admin no se filtra por inactivos
      if ($_SESSION["rol"] == "ADMIN"){
        $fIndActivo = null;
        $idLigasUsuario = null;
      } elseif ($idLigasUsuario === null && function_exists('ligasPermitidasUsuario') && isset($_SESSION['rol'])) {
        $idLigasUsuario = ligasPermitidasUsuario();
      }
      

      // calculamos el num de pag
      $numPag =  ($numPag > 0)? ($numPag * 10) :  $numPag ;
  
        $queryDB = "SELECT  idLiga, nombre, numFases, numRondas, indActivo, DATE_FORMAT(fecIni, '%d-%m-%Y' ), DATE_FORMAT(fecFin, '%d-%m-%Y' ), logo, idJuego
              FROM mb_ligas
              WHERE 1=1 ";
            
      // GESTIONAMOS FILTROS
      if ( $fNombre != null) { $queryDB .= " AND UPPER(nombre) LIKE UPPER('%" . $fNombre  . "%') ";  }
      if ( $fIndActivo != null) { $queryDB .= " AND indActivo = " . $fIndActivo;  }
      if ( $fFecIni != null) { $queryDB .= " AND fecIni >= '" . $this->formatoFecha($fFecIni, true) . "'";  }     
      if ( $idLigasUsuario !== null) { $queryDB .= " AND idLiga in (" . $idLigasUsuario . ")";  }

      // ORDENAMOS
      $queryDB .= " ORDER BY fecIni DESC ";

      // PAGINAMOS
      $queryDB .= " LIMIT " . $numPag . ", 10 ";

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      $arrResultados = array ();

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          $arrObjeto = array();

          array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8]  ) ;

          array_push($arrResultados, $arrObjeto);
        }
        return $arrResultados;  
      }else{
        return null;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarListadoLigas - liga.controller.php");  
      return null;   
    }
    }




    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function modificarDatosLiga( $fIdLiga, $fNombre, $fNumFases, $fNumRondas, $fFecIni, $fFecFin, $fIndActivo, $fIdJuego ){
      
    try {


      // comprobamos los campos que han cambiado
      $this->oLiga = $this->recuperarDatosLiga ( $fIdLiga );
      if ($fNombre != $this->oLiga->nombre && $this->existeLigaConNombre($fNombre, $fIdLiga)) {
        return 4;
      }
        
      $aux = 0; 
      $auxLogo = 0; 
        $queryDB = "UPDATE mb_ligas SET ";

        if ($fNombre != $this->oLiga->nombre){

          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " nombre = '". $fNombre ."' ";
          $aux = 1;
        } 
        if ($fNumFases != $this->oLiga->numFases){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " numFases = '". $fNumFases ."' ";
          $aux = 1;
        }
        if ($fNumRondas != $this->oLiga->numRondas){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " numRondas = '". $fNumRondas ."' ";
          $aux = 1;
        } 

        if ($fIndActivo != $this->oLiga->indActivo){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " indActivo = '". $fIndActivo ."' ";
          $aux = 1;
        }
      
        if ($fIdJuego != $this->oLiga->idJuego){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " idJuego = '". $fIdJuego ."' ";
          $aux = 1;
        } 

      // actualizamos fases y rondas
        if ($fNumFases != $this->oLiga->numFases || $fNumRondas != $this->oLiga->numRondas
           ||  $fFecIni  != $this->oLiga->fecIni ||  $fFecFin != $this->oLiga->fecFin){
        
        $queryDBFases = "DELETE FROM mb_fases 
          WHERE idLiga = ".$fIdLiga ;


        $resultadoBD2 = $this->oConexBD->ejecutarConsulta($queryDBFases, 1);


        // generamos la tabla de fases y rondas
        for ($i=1; $i<=$fNumFases; $i++){
          for($j=1; $j<=$fNumRondas; $j++){
            $fecIniAux = "";
            $fecFinAux = "";
            if ($i== 1) { 
              $fecIniAux = $this->formatoFecha( $fFecIni, true);
            }else if($i == $fNumFases) { 
              $fecFinAux = $this->formatoFecha(  $fFecFin, true);
            }


            $queryDBFases = "INSERT INTO mb_fases( idLiga, numFase, numRonda, fecIni, fecFin )
                  VALUES (" . $fIdLiga . ", " . $i . ", " . $j . ",'" . $fecIniAux . "', '" . $fecFinAux . "')";
                  
            $resultadoBDaux3 = $this->oConexBD->ejecutarConsulta($queryDBFases, 1);
            
          }
        }   

      }

        if ( $fFecIni  != $this->oLiga->fecIni){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " fecIni = '". $this->formatoFecha( $fFecIni, true) ."' ";
          $aux = 1;
        }
        if ( $fFecFin != $this->oLiga->fecFin){
          $queryDB .= ($aux > 0)? " , " : "";
          $queryDB .= " fecFin = '". $this->formatoFecha( $fFecFin, true) ."' ";
          $aux = 1;
        }
        $queryDB .= " WHERE idLiga = " . $fIdLiga;

       
        /*
        if ($auxLogo == 1){
          return 1;
        }else*/
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
      $oLog->trazaLog ($e, "modificarDatosLiga - liga.controller.php"); 
      return null;   
    }
 
    }




    /* método para ALTA NUEVA */
    public function altaNuevaLiga( $fNombre, $fNumFases, $fNumRondas, $fFecIni, $fFecFin, $fIndActivo, $fIdJuego ){
  
    try {
      /*if ( $fFecIni >= $fFecFin){
        echo  $fFecIni . '' . $fFecFin; 
        return 3;
      }*/
      if ( $fNombre == null || $fNumFases == null || $fNumRondas == null ) {
        return 2;
      }
      if (filter_var($fNumFases, FILTER_VALIDATE_INT) === false || filter_var($fNumRondas, FILTER_VALIDATE_INT) === false || (int) $fNumFases < 1 || (int) $fNumRondas < 1) {
        return 5;
      }
      if ($this->existeLigaConNombre($fNombre)) {
        return 4;
      }

      $fechaLigaInicio = $this->formatoFecha($fFecIni, true);
      $fechaLigaFin = $this->formatoFecha($fFecFin, true);
      if ($fechaLigaInicio > $fechaLigaFin) {
        return 3;
      }

      // insertamos el nuevo registro
        $queryDB = "INSERT INTO mb_ligas ( nombre, numFases, numRondas, fecIni, fecFin, indActivo, idJuego, audAlta )
              VALUES ('" . $fNombre . "', " . $fNumFases . ", " . $fNumRondas . ", '" . $fechaLigaInicio . "' , '" . $fechaLigaFin . "' , 
                '" . $fIndActivo . "', ". $fIdJuego . ",'" . Date('Y-m-d H:i:s') . "' )";
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

      
      if ($resultadoBD >= 1){

        //calculamos el ID para asignar la logo
        $queryDB = "SELECT MAX(idLiga)  as idLiga FROM mb_ligas";

        $resultadoBDaux = $this->oConexBD->ejecutarConsulta($queryDB);

        if ($resultadoBDaux != null){

          foreach ($resultadoBDaux as $fila) {

            // actualizamos el nombre del fichero de la imagen
            // asignamos el logo predeterminado de la liga
            $fLogo = "logo-".$fila[0].".png";
            $queryDB = "UPDATE mb_ligas SET logo = '". $fLogo . "'
                  WHERE idLiga  = '". $fila[0] . "'";
            $resultadoBDaux2 = $this->oConexBD->ejecutarConsulta($queryDB, 1);

            $rutaDocumentosLiga = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'recursos' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'ligas' . DIRECTORY_SEPARATOR . (int) $fila[0];
            if (!is_dir($rutaDocumentosLiga) && !mkdir($rutaDocumentosLiga, 0775, true)) {
              throw new RuntimeException('No se pudo crear el directorio de documentos de la liga.');
            }
            for ($fase = 1; $fase <= (int) $fNumFases; $fase++) {
              $rutaFase = $rutaDocumentosLiga . DIRECTORY_SEPARATOR . $fase;
              if (!is_dir($rutaFase) && !mkdir($rutaFase, 0775, true)) {
                throw new RuntimeException('No se pudo crear el directorio de documentos de la fase.');
              }
            }

            
            $fechaInicio = new DateTime($fechaLigaInicio);
            $fechaFin = new DateTime($fechaLigaFin);
            $diasCalendario = (int) $fechaInicio->diff($fechaFin)->format('%a') + 1;

            // generamos las fases y repartimos proporcionalmente los días de la liga
            for ($i=1; $i<=$fNumFases; $i++){           
              $offsetInicio = (int) floor(($diasCalendario * ($i - 1)) / $fNumFases);
              $offsetFin = (int) floor(($diasCalendario * $i) / $fNumFases) - 1;
              $fechaFaseInicio = clone $fechaInicio;
              $fechaFaseFin = clone $fechaInicio;
              $fechaFaseInicio->modify("+" . $offsetInicio . " days");
              $fechaFaseFin->modify("+" . $offsetFin . " days");
              $fecIniAux = $fechaFaseInicio->format('Y-m-d');
              $fecFinAux = $fechaFaseFin->format('Y-m-d');

              for($j=1; $j<=$fNumRondas; $j++){
                $queryDBFases = "INSERT INTO mb_fases( idLiga, numFase, numRonda, fecIni, fecFin )
                      VALUES (" . $fila[0] . ", " . $i . ", " . $j . ",'" . $fecIniAux . "', '" . $fecFinAux . "')";
                      
                $resultadoBDaux3 = $this->oConexBD->ejecutarConsulta($queryDBFases, 1);
                
              }
            }   


            return 1;
          }
        }
      }else{
        return 2;
      } 

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "altaNuevaLiga - liga.controller.php");  
      return null;   
    }
    }




    /* método para BORRAR un elemento */
    public function borrarLiga ( $fIdLiga ) {
    
    try {

      $fIdLiga = (int) $fIdLiga;
      $this->oLiga = $this->recuperarDatosLiga ( $fIdLiga );

        $queryDB = "DELETE FROM mb_enfren_misiones_sec
            WHERE idEnfrentamiento IN (SELECT idEnfrentamiento FROM mb_enfrentamientos WHERE idLiga = ".$fIdLiga.")";

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
            

        $queryDB = "DELETE FROM mb_listas
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


        $queryDB = "DELETE FROM mb_enfrentamientos
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


        $queryDB = "DELETE FROM mb_jugadores
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


        $queryDB = "DELETE FROM mb_fases 
            WHERE idLiga = ".$fIdLiga ;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


        $queryDB = "DELETE FROM mb_misiones_secundarias
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);


        $queryDB = "DELETE FROM mb_ligas_usuarios
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

        $queryDB = "DELETE FROM mb_ligas
            WHERE idLiga = ".$fIdLiga;

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

      if ($resultadoBD >= 1){

        // borramos cualquier imagen con las 3 extensiones permitidas
        if (file_exists("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".png"))
          unlink("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".png");
        if (file_exists("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".jpg"))
          unlink("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".jpg");
        if (file_exists("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".gif"))
          unlink("./recursos/img/ligas/logo-". $this->oLiga->idLiga.".gif");
        return true;  
      }else{
        return false;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();

      $oLog->trazaLog ($e, "borrarLiga - liga.controller.php"); 
      return null;   
    }
    }




  /* método para PAGINADOR de empresas */
    public function paginadorLigas (  $fNombre = null, $fIndActivo = null, $fFecIni = null, $idLigasUsuario = null ) {
    
    try {

        $queryDB = "SELECT count(1)
              FROM mb_ligas
              WHERE 1=1 ";
            
      // GESTIONAMOS FILTROS
      if (isset($_SESSION['rol']) && $_SESSION["rol"] != "ADMIN" && $idLigasUsuario === null && function_exists('ligasPermitidasUsuario')) { $idLigasUsuario = ligasPermitidasUsuario(); }
      if ( $fNombre != null) { $queryDB .= " AND UPPER(nombre) LIKE UPPER('%" . $fNombre  . "%') ";  }
      if ( $fIndActivo != null) { $queryDB .= " AND indActivo = " . $fIndActivo;  }
      if ( $fFecIni != null) { $queryDB .= " AND fecIni = '" . $this->formatoFecha( $fFecIni, true);  }
      if ( $idLigasUsuario !== null) { $queryDB .= " AND idLiga in (" . $idLigasUsuario . ")"; }


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
      $oLog->trazaLog ($e, "paginadorLigas - liga.controller.php"); 
      return null;   
    }
    }

    /* método para recuperar el último liga */
    public function recuperarUltimaLiga ( $fNombre,  $fFecIni ) {

    try {

        $queryDB = "SELECT max(idLiga)
              FROM mb_ligas 
            WHERE 1=1
            ";
      // GESTIONAMOS FILTROS
      if ( $fNombre != null) { $queryDB .= " AND nombre = '" . $fNombre  . "' ";  }
      if ( $fFecIni != null) { $queryDB .= " AND fecIni = '" . $this->formatoFecha( $fFecIni, true)  . "' ";  }
    
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
      $oLog->trazaLog ($e, "recuperarUltimaLiga - liga.controller.php");  
      return null;   
    }
    }



    /* Listado de empresas para los formularios de búsqueda */
    public function recuperarSelectLigas( $formAlta = null, $mostrarInactivos = false, $idLigas = null, $selectorPublico = false, $soloActivas = false ){
    try {


      // si es admin no se filtra por inactivos
      if (!$selectorPublico && isset($_SESSION["rol"]) && $_SESSION["rol"] == "ADMIN"){
        $mostrarInactivos = true;
        $idLigas = null;
      } elseif (!$selectorPublico && $idLigas === null && function_exists('ligasPermitidasUsuario') && isset($_SESSION['rol'])) {
        $idLigas = ligasPermitidasUsuario();
      }
      if (!$selectorPublico && isset($_SESSION["rol"]) && $_SESSION["rol"] != "ADMIN" && $idLigas !== null) {
        $mostrarInactivos = true;
      }
      if ($soloActivas) {
        $mostrarInactivos = false;
      }
      

        $queryDB = "SELECT idLiga,
                CONCAT(COALESCE(YEAR(COALESCE(fecIni, (SELECT MIN(fecIni) FROM mb_fases WHERE mb_fases.idLiga = mb_ligas.idLiga))), ''), '_', nombre),
                indActivo
            FROM mb_ligas WHERE 1 = 1 ";
        $queryDB .= (!$mostrarInactivos)? " AND indActivo = 1 " : ""; 
        $queryDB .= ($idLigas !== null)? " AND idLiga in (" . $idLigas . ")" : "";
      if ($selectorPublico && (!isset($_SESSION["rol"]) || strtoupper(trim((string) $_SESSION["rol"])) !== "ADMIN")) {
        $queryDB .= " AND nombre NOT LIKE 'Test - %' ";
      }
      $queryDB .="  ORDER BY fecIni DESC, nombre";

      $arrResultados = array ();
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        // primero metemos el <option 0> en caso de que sea para el formulario de alta
      

        foreach ($resultadoBD as $fila) {
          $arr = array();
          // nombre (para ADMIN añadimos el estado)
          if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "ADMIN"){
            $marcaActivo = ($fila[2] == 0)? " (Desactivada)" : "";
            $nombreLiga = $fila[1]  . $marcaActivo  ;
        }else{
           $nombreLiga = $fila[1] ;
        }
          array_push($arr, $fila[0], $nombreLiga, $fila[2] ) ;
          array_push($arrResultados, $arr);
        }
          
        return $arrResultados;  
      }else{
        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectLigas - liga.controller.php"); 
      return null;   
    }
    }



    /* Listado de empresas para los formularios de búsqueda */
    public function recuperarSelectFases( $fIdLiga, $faseActiva = null ){
      try{        
        $queryDB = "SELECT distinct numFase FROM mb_fases 
              WHERE idLiga = " . $fIdLiga ;

      // GESTIONAMOS FILTROS
    if ((int) $faseActiva === 1) { $queryDB .= " AND (fecIni IS NULL OR fecIni <= '" . Date('Y-m-d') . "') AND (fecFin IS NULL OR fecFin >= '" . Date('Y-m-d') . "')"; }

      $queryDB .= " ORDER BY 1";

      $arrResultados = array ();
      
      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        
        foreach ($resultadoBD as $fila) {
          array_push($arrResultados, $fila[0]);
        }
          
        return $arrResultados;  
      }else{
        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectFases - liga.controller.php"); 
      return null;   
    }

    }


    /* Listado de rondas para los formularios de búsqueda */
    public function recuperarSelectRondas( $fIdLiga, $fNumFase = null ){
      try{
         $queryDB = "SELECT distinct numRonda FROM mb_fases 
              WHERE idLiga = " . $fIdLiga;

        // GESTIONAMOS FILTROS
      if ( $fNumFase != null) { $queryDB .= " AND numFase = " .  $fNumFase; }

      $queryDB .= " ORDER BY 1";

      $arrResultados = array ();
      
      $resultadoBDRonda = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBDRonda != null){
      
        foreach ($resultadoBDRonda as $fila) {
          array_push($arrResultados, $fila[0]);
        }
          
        return $arrResultados;  
      }else{

        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectRondas - liga.controller.php");  
      return null;   
    }

    }



    /* Listado de facciones para los formularios de búsqueda */
    public function recuperarSelectFacciones ( $fIdJuego ){
      try{
         $queryDB = "SELECT idFaccion, descFaccion FROM `mb_facciones` 
                  WHERE idJuego = ". (int) $fIdJuego . " AND indActivo = 1
                  ORDER BY 2 ASC";


      $arrResultados = array ();
      
      $resultadoBDFacciones = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBDFacciones != null){
      
        foreach ($resultadoBDFacciones as $fila) {
          array_push($arrResultados, $fila);
        }
          
        return $arrResultados;  
      }else{

        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectRondas - liga.controller.php");  
      return null;   
    }

    }


    /* TIPO DE JUEGO */
    public function recuperarDescJuego ( $fIdJuego ){
      try{
         $queryDB = "SELECT descJuego FROM `mb_juegos` 
                  WHERE idJuego = ". $fIdJuego;


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
      $oLog->trazaLog ($e, "recuperarDescJuego - liga.controller.php");  
      return null;   
    }

    }



    /* Listado de facciones para los formularios de búsqueda */
    public function recuperarSelectJuegos ( $soloActivos = true ){
      try{
         $queryDB = "SELECT idJuego, descJuego FROM `mb_juegos` 
                  WHERE " . ($soloActivos ? "indActivo = 1" : "1 = 1") . " ORDER BY 2 ASC";


      $arrResultados = array ();
      
      $resultadoBDFjuegos = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBDFjuegos != null){
      
        foreach ($resultadoBDFjuegos as $fila) {
          array_push($arrResultados, $fila);
        }
          
        return $arrResultados;  
      }else{

        return null;
      }
    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarSelectJuegos - liga.controller.php");  
      return null;   
    }

    }

    
    /* método para cambiar el formato de fecha de la BD al UTC español */
    public function formatoFecha($fFecha, $utcUsa = true) {
      if ($utcUsa) {
          $user_tz = 'America/Los_Angeles';
          $format = "Y-m-d";
      } else {
          $user_tz = 'Europe/Madrid';
          $format = "d-m-Y";
      }
  
      $schedule_date = new DateTime($fFecha, new DateTimeZone($user_tz));
      $schedule_date->setTimeZone(new DateTimeZone('UTC'));
      return $schedule_date->format($format);
  }
  

    /* recuperamos maximo id de liga para seleccion por defecto en toda la app */
    public function recuperarMaxIdLiga(  ){
    try {
        $queryDB = "SELECT max(idLiga) as maxLiga
              FROM mb_ligas";

      $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

      if ($resultadoBD != null){
        foreach ($resultadoBD as $fila) {
          return  $fila[0];
        }
      }else{
        return 1;
      }

    }catch(Exception $e){
      $oLog = Log::getInstance();
      $oLog->trazaLog ($e, "recuperarMaxIdLiga - liga.controller.php"); 
      return null;   
    }

    }

}



?>
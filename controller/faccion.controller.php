<?php

class controllerFaccion {

    private $oConexBD;

    public function __construct() {
        $this->oConexBD = new ConexBD();
    }

    public function recuperarDatosFaccion($idFaccion) {
        try {
            $queryDB = "SELECT idFaccion, idJuego, descFaccion, indActivo FROM mb_facciones WHERE idFaccion = " . (int) $idFaccion;
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);
            if ($resultadoBD != null) {
                foreach ($resultadoBD as $fila) {
                    return new Faccion($fila[0], $fila[1], $fila[2], $fila[3]);
                }
            }
        } catch (Exception $e) {
            return null;
        }
        return null;
    }

    public function recuperarListadoFacciones($idJuego, $descFaccion = null) {
        try {
            $queryDB = "SELECT idFaccion, idJuego, descFaccion, indActivo FROM mb_facciones WHERE idJuego = " . (int) $idJuego;
            if ($descFaccion != null && $descFaccion !== '') {
                $queryDB .= " AND UPPER(descFaccion) LIKE UPPER('%" . addslashes($descFaccion) . "%')";
            }
            $queryDB .= " ORDER BY descFaccion ASC";
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);
            $arrResultados = array();
            if ($resultadoBD != null) {
                foreach ($resultadoBD as $fila) {
                    $arrResultados[] = array($fila[0], $fila[1], $fila[2], $fila[3]);
                }
            }
            return $arrResultados;
        } catch (Exception $e) {
            return array();
        }
    }

    public function altaNuevaFaccion($idJuego, $descFaccion) {
        $descFaccion = trim((string) $descFaccion);
        if ((int) $idJuego <= 0 || $descFaccion === '') {
            return 2;
        }
        try {
            $valor = addslashes($descFaccion);
            $existe = $this->oConexBD->ejecutarConsulta("SELECT idFaccion FROM mb_facciones WHERE idJuego = " . (int) $idJuego . " AND UPPER(TRIM(descFaccion)) = UPPER('" . $valor . "')");
            if ($existe != null && count($existe) > 0) {
                return 2;
            }
            return $this->oConexBD->ejecutarConsulta("INSERT INTO mb_facciones (idJuego, descFaccion, indActivo) VALUES (" . (int) $idJuego . ", '" . $valor . "', 1)", 1) > 0 ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function modificarFaccion($idFaccion, $descFaccion, $indActivo) {
        $descFaccion = trim((string) $descFaccion);
        if ((int) $idFaccion <= 0 || $descFaccion === '') {
            return 2;
        }
        try {
            $queryDB = "UPDATE mb_facciones SET descFaccion = '" . addslashes($descFaccion) . "', indActivo = " . ((int) $indActivo === 1 ? 1 : 0) . " WHERE idFaccion = " . (int) $idFaccion;
            return $this->oConexBD->ejecutarConsulta($queryDB, 1) > 0 ? 1 : 3;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function borrarFaccion($idFaccion) {
        if ((int) $idFaccion <= 0) {
            return false;
        }
        try {
            $enUso = $this->oConexBD->ejecutarConsulta("SELECT idJugador FROM mb_jugadores WHERE bando = " . (int) $idFaccion . " LIMIT 1");
            if ($enUso != null && count($enUso) > 0) {
                return false;
            }
            return $this->oConexBD->ejecutarConsulta("DELETE FROM mb_facciones WHERE idFaccion = " . (int) $idFaccion, 1) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>

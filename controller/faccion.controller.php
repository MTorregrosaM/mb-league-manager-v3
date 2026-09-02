<?php

class controllerFaccion {

    private $oConexBD;

    public function __construct() {
        $this->oConexBD = new ConexBD();
    }

    public function recuperarDatosFaccion($idFaccion) {
        try {
            $queryDB = "SELECT idFaccion, idJuego, descFaccion, indActivo FROM mb_facciones WHERE idFaccion = ?";
            $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'i', array((int) $idFaccion));
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
            $queryDB = "SELECT idFaccion, idJuego, descFaccion, indActivo FROM mb_facciones WHERE idJuego = ?";
            $tipos = 'i';
            $parametros = array((int) $idJuego);
            if ($descFaccion != null && $descFaccion !== '') {
                $queryDB .= " AND UPPER(descFaccion) LIKE UPPER(?)";
                $tipos .= 's';
                $parametros[] = '%' . $descFaccion . '%';
            }
            $queryDB .= " ORDER BY descFaccion ASC";
            $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, $tipos, $parametros);
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
            $existe = $this->oConexBD->ejecutarConsultaPreparada("SELECT idFaccion FROM mb_facciones WHERE idJuego = ? AND UPPER(TRIM(descFaccion)) = UPPER(?)", 'is', array((int) $idJuego, $descFaccion));
            if ($existe != null && count($existe) > 0) {
                return 2;
            }
            return $this->oConexBD->ejecutarConsultaPreparada("INSERT INTO mb_facciones (idJuego, descFaccion, indActivo) VALUES (?, ?, 1)", 'is', array((int) $idJuego, $descFaccion), 1) > 0 ? 1 : 0;
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
            $queryDB = "UPDATE mb_facciones SET descFaccion = ?, indActivo = ? WHERE idFaccion = ?";
            return $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'sii', array($descFaccion, (int) $indActivo === 1 ? 1 : 0, (int) $idFaccion), 1) > 0 ? 1 : 3;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function borrarFaccion($idFaccion) {
        if ((int) $idFaccion <= 0) {
            return false;
        }
        try {
            $enUso = $this->oConexBD->ejecutarConsultaPreparada("SELECT idJugador FROM mb_jugadores WHERE bando = ? LIMIT 1", 'i', array((int) $idFaccion));
            if ($enUso != null && count($enUso) > 0) {
                return false;
            }
            return $this->oConexBD->ejecutarConsultaPreparada("DELETE FROM mb_facciones WHERE idFaccion = ?", 'i', array((int) $idFaccion), 1) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>

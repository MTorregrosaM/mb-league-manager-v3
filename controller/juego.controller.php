<?php

class controllerJuego {

    private $oConexBD;
    private $oJuego;

    public function __construct() {
        $this->oConexBD = new ConexBD();
    }

    public function recuperarDatosJuego($idJuego) {
        try {
            $queryDB = "SELECT idJuego, descJuego, indActivo, audAlta FROM mb_juegos WHERE idJuego = " . (int) $idJuego;
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);
            if ($resultadoBD != null) {
                foreach ($resultadoBD as $fila) {
                    return new Juego($fila[0], $fila[1], $fila[3]);
                }
            }
        } catch (Exception $e) {
            return null;
        }
        return null;
    }

    public function recuperarListadoJuegos($descJuego = null, $numPag = 0, $indActivo = null, $fFecIni = null) {
        try {
            $queryDB = "SELECT idJuego, descJuego, indActivo, DATE_FORMAT(audAlta, '%d-%m-%Y %H:%i:%s') FROM mb_juegos WHERE 1 = 1";
            if ($descJuego != null && $descJuego !== '') {
                $descJuego = addslashes($descJuego);
                $queryDB .= " AND UPPER(descJuego) LIKE UPPER('%" . $descJuego . "%')";
            }
            if ($indActivo !== null && $indActivo !== '') {
                $queryDB .= " AND indActivo = " . ((int) $indActivo === 1 ? 1 : 0);
            }
            $rangoFecha = $this->rangoFechaAlta($fFecIni);
            if ($rangoFecha !== null) {
                $queryDB .= " AND audAlta >= '" . $rangoFecha[0] . "' AND audAlta < '" . $rangoFecha[1] . "'";
            }
            $queryDB .= " ORDER BY descJuego ASC LIMIT " . max(0, (int) $numPag) . ", 10";
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

    public function paginadorJuegos($descJuego = null, $indActivo = null, $fFecIni = null) {
        try {
            $queryDB = "SELECT COUNT(1) FROM mb_juegos WHERE 1 = 1";
            if ($descJuego != null && $descJuego !== '') {
                $queryDB .= " AND UPPER(descJuego) LIKE UPPER('%" . addslashes($descJuego) . "%')";
            }
            if ($indActivo !== null && $indActivo !== '') {
                $queryDB .= " AND indActivo = " . ((int) $indActivo === 1 ? 1 : 0);
            }
            $rangoFecha = $this->rangoFechaAlta($fFecIni);
            if ($rangoFecha !== null) {
                $queryDB .= " AND audAlta >= '" . $rangoFecha[0] . "' AND audAlta < '" . $rangoFecha[1] . "'";
            }
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);
            return ($resultadoBD != null) ? (int) $resultadoBD[0][0] : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function rangoFechaAlta($fecha) {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return null;
        }
        $fechaObjeto = DateTime::createFromFormat('d-m-Y', $fecha);
        if ($fechaObjeto === false || DateTime::getLastErrors() !== false && (DateTime::getLastErrors()['warning_count'] > 0 || DateTime::getLastErrors()['error_count'] > 0)) {
            return null;
        }
        return array($fechaObjeto->format('Y-m-d') . ' 00:00:00', $fechaObjeto->modify('+1 day')->format('Y-m-d') . ' 00:00:00');
    }

    public function altaNuevoJuego($descJuego, $indActivo = 1) {
        $descJuego = trim((string) $descJuego);
        if ($descJuego === '') {
            return 2;
        }
        try {
            $valor = addslashes($descJuego);
            $existe = $this->oConexBD->ejecutarConsulta("SELECT idJuego FROM mb_juegos WHERE descJuego = '" . $valor . "'");
            if ($existe != null && count($existe) > 0) {
                return 2;
            }
            return $this->oConexBD->ejecutarConsulta("INSERT INTO mb_juegos (descJuego, indActivo, audAlta) VALUES ('" . $valor . "', " . ((int) $indActivo === 1 ? 1 : 0) . ", NOW())", 1) > 0 ? 1 : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function modificarJuego($idJuego, $descJuego, $indActivo = 1) {
        $descJuego = trim((string) $descJuego);
        if ((int) $idJuego <= 0 || $descJuego === '') {
            return 2;
        }
        try {
            $valor = addslashes($descJuego);
            $queryDB = "UPDATE mb_juegos SET descJuego = '" . $valor . "', indActivo = " . ((int) $indActivo === 1 ? 1 : 0) . " WHERE idJuego = " . (int) $idJuego;
            return $this->oConexBD->ejecutarConsulta($queryDB, 1) > 0 ? 1 : 3;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function borrarJuego($idJuego) {
        if ((int) $idJuego <= 0) {
            return false;
        }
        try {
            $enUso = $this->oConexBD->ejecutarConsulta("SELECT idLiga FROM mb_ligas WHERE idJuego = " . (int) $idJuego);
            if ($enUso != null && count($enUso) > 0) {
                return false;
            }
            return $this->oConexBD->ejecutarConsulta("DELETE FROM mb_juegos WHERE idJuego = " . (int) $idJuego, 1) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>

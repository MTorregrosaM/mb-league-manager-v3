<?php

class controllerJuego {

    private $oConexBD;
    private $oJuego;

    public function __construct() {
        $this->oConexBD = new ConexBD();
    }

    public function recuperarDatosJuego($idJuego) {
        try {
            $queryDB = "SELECT idJuego, descJuego, indActivo, audAlta FROM mb_juegos WHERE idJuego = ?";
            $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'i', array((int) $idJuego));
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
            $tipos = '';
            $parametros = array();
            if ($descJuego != null && $descJuego !== '') {
                $queryDB .= " AND UPPER(descJuego) LIKE UPPER(?)";
                $tipos .= 's';
                $parametros[] = '%' . $descJuego . '%';
            }
            if ($indActivo !== null && $indActivo !== '') {
                $queryDB .= " AND indActivo = ?";
                $tipos .= 'i';
                $parametros[] = (int) $indActivo === 1 ? 1 : 0;
            }
            $rangoFecha = $this->rangoFechaAlta($fFecIni);
            if ($rangoFecha !== null) {
                $queryDB .= " AND audAlta >= ? AND audAlta < ?";
                $tipos .= 'ss';
                $parametros[] = $rangoFecha[0];
                $parametros[] = $rangoFecha[1];
            }
            $queryDB .= " ORDER BY descJuego ASC LIMIT " . max(0, (int) $numPag) . ", 10";
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

    public function paginadorJuegos($descJuego = null, $indActivo = null, $fFecIni = null) {
        try {
            $queryDB = "SELECT COUNT(1) FROM mb_juegos WHERE 1 = 1";
            $tipos = '';
            $parametros = array();
            if ($descJuego != null && $descJuego !== '') {
                $queryDB .= " AND UPPER(descJuego) LIKE UPPER(?)";
                $tipos .= 's';
                $parametros[] = '%' . $descJuego . '%';
            }
            if ($indActivo !== null && $indActivo !== '') {
                $queryDB .= " AND indActivo = ?";
                $tipos .= 'i';
                $parametros[] = (int) $indActivo === 1 ? 1 : 0;
            }
            $rangoFecha = $this->rangoFechaAlta($fFecIni);
            if ($rangoFecha !== null) {
                $queryDB .= " AND audAlta >= ? AND audAlta < ?";
                $tipos .= 'ss';
                $parametros[] = $rangoFecha[0];
                $parametros[] = $rangoFecha[1];
            }
            $resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, $tipos, $parametros);
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
            $existe = $this->oConexBD->ejecutarConsultaPreparada("SELECT idJuego FROM mb_juegos WHERE descJuego = ?", 's', array($descJuego));
            if ($existe != null && count($existe) > 0) {
                return 2;
            }
            return $this->oConexBD->ejecutarConsultaPreparada("INSERT INTO mb_juegos (descJuego, indActivo, audAlta) VALUES (?, ?, NOW())", 'si', array($descJuego, (int) $indActivo === 1 ? 1 : 0), 1) > 0 ? 1 : 0;
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
            $queryDB = "UPDATE mb_juegos SET descJuego = ?, indActivo = ? WHERE idJuego = ?";
            return $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'sii', array($descJuego, (int) $indActivo === 1 ? 1 : 0, (int) $idJuego), 1) > 0 ? 1 : 3;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function borrarJuego($idJuego) {
        if ((int) $idJuego <= 0) {
            return false;
        }
        try {
            $enUso = $this->oConexBD->ejecutarConsultaPreparada("SELECT idLiga FROM mb_ligas WHERE idJuego = ?", 'i', array((int) $idJuego));
            if ($enUso != null && count($enUso) > 0) {
                return false;
            }
            return $this->oConexBD->ejecutarConsultaPreparada("DELETE FROM mb_juegos WHERE idJuego = ?", 'i', array((int) $idJuego), 1) > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>

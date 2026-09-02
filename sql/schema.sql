-- League Manager database schema.
-- This script targets MySQL 5.5 or later.

CREATE DATABASE IF NOT EXISTS mb_league
  CHARACTER SET utf8
  COLLATE utf8_general_ci;

USE mb_league;

CREATE TABLE IF NOT EXISTS mb_juegos (
  idJuego INT NOT NULL AUTO_INCREMENT,
  descJuego VARCHAR(100) NOT NULL,
  indActivo TINYINT(1) NOT NULL DEFAULT 1,
  audAlta DATETIME NOT NULL,
  PRIMARY KEY (idJuego),
  UNIQUE KEY uq_mb_juegos_desc (descJuego)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_facciones (
  idFaccion INT NOT NULL AUTO_INCREMENT,
  idJuego INT NOT NULL,
  descFaccion VARCHAR(100) NOT NULL,
  indActivo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (idFaccion),
  UNIQUE KEY uq_mb_facciones_juego_desc (idJuego, descFaccion),
  KEY idx_mb_facciones_juego (idJuego),
  CONSTRAINT fk_mb_facciones_juego FOREIGN KEY (idJuego) REFERENCES mb_juegos (idJuego)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_ligas (
  idLiga INT NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(250) DEFAULT NULL,
  numFases INT DEFAULT NULL,
  numRondas INT DEFAULT NULL,
  indActivo INT DEFAULT NULL,
  fecIni DATE DEFAULT NULL,
  fecFin DATE DEFAULT NULL,
  logo VARCHAR(250) DEFAULT NULL,
  idJuego INT NOT NULL DEFAULT 1,
  audAlta DATETIME DEFAULT NULL,
  PRIMARY KEY (idLiga),
  KEY idx_mb_ligas_juego (idJuego),
  CONSTRAINT fk_mb_ligas_juego FOREIGN KEY (idJuego) REFERENCES mb_juegos (idJuego)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_fases (
  idLiga INT NOT NULL,
  numFase VARCHAR(45) NOT NULL,
  numRonda VARCHAR(45) NOT NULL,
  claveCifrada VARCHAR(250) DEFAULT NULL,
  fecIni DATETIME DEFAULT NULL,
  fecFin DATETIME DEFAULT NULL,
  PRIMARY KEY (idLiga, numFase, numRonda),
  CONSTRAINT fk_mb_fases_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_usuarios (
  idUsuario INT NOT NULL AUTO_INCREMENT,
  nick VARCHAR(250) NOT NULL,
  pass VARCHAR(255) NOT NULL,
  rol VARCHAR(45) NOT NULL,
  ult_acceso DATETIME DEFAULT NULL,
  PRIMARY KEY (idUsuario),
  UNIQUE KEY uq_mb_usuarios_nick (nick)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_ligas_usuarios (
  idUsuario INT NOT NULL,
  idLiga INT NOT NULL,
  PRIMARY KEY (idUsuario, idLiga),
  CONSTRAINT fk_mb_ligas_usuarios_usuario FOREIGN KEY (idUsuario) REFERENCES mb_usuarios (idUsuario),
  CONSTRAINT fk_mb_ligas_usuarios_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_jugadores (
  idJugador INT NOT NULL AUTO_INCREMENT,
  idLiga INT NOT NULL,
  nick VARCHAR(250) NOT NULL DEFAULT '',
  nombre VARCHAR(250) DEFAULT NULL,
  apellido1 VARCHAR(250) DEFAULT NULL,
  apellido2 VARCHAR(250) DEFAULT NULL,
  foto VARCHAR(250) DEFAULT NULL,
  telefono BIGINT DEFAULT NULL,
  email VARCHAR(45) DEFAULT NULL,
  bando VARCHAR(20) DEFAULT NULL,
  puntosPintura INT NOT NULL DEFAULT 0,
  audAlta DATETIME DEFAULT NULL,
  PRIMARY KEY (idJugador),
  KEY idx_mb_jugadores_liga (idLiga),
  CONSTRAINT fk_mb_jugadores_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_listas (
  idLiga INT NOT NULL,
  idJugador INT NOT NULL,
  numFase INT NOT NULL,
  bando VARCHAR(20) NOT NULL,
  urlDocumento VARCHAR(45) DEFAULT NULL,
  fechaDocumento DATETIME DEFAULT NULL,
  PRIMARY KEY (idLiga, idJugador, numFase, bando),
  CONSTRAINT fk_mb_listas_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga),
  CONSTRAINT fk_mb_listas_jugador FOREIGN KEY (idJugador) REFERENCES mb_jugadores (idJugador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_misiones_secundarias (
  idMisionSecundaria INT NOT NULL,
  idLiga INT NOT NULL,
  tituloMisionSecundaria VARCHAR(250) NOT NULL,
  txtMisionSecundaria VARCHAR(250) NOT NULL,
  numMedallas INT NOT NULL DEFAULT 0,
  KEY idx_mb_misiones_id (idMisionSecundaria),
  KEY idx_mb_misiones_liga (idLiga),
  CONSTRAINT fk_mb_misiones_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_enfrentamientos (
  idEnfrentamiento INT NOT NULL AUTO_INCREMENT,
  idLiga INT NOT NULL,
  numFase INT NOT NULL,
  numRonda INT NOT NULL,
  idJugador1 INT DEFAULT NULL,
  idJugador2 INT DEFAULT NULL,
  bandoJugador1 VARCHAR(20) DEFAULT NULL,
  bandoJugador2 VARCHAR(20) DEFAULT NULL,
  resultadoJugador1 INT DEFAULT NULL,
  resultadoJugador2 INT DEFAULT NULL,
  valPintura INT DEFAULT NULL,
  valPinturaJug1 INT DEFAULT NULL,
  valPinturaJug2 INT DEFAULT NULL,
  valDeportividadJug1 INT DEFAULT NULL,
  valDeportividadJug2 INT DEFAULT NULL,
  indMisionSecundaria INT DEFAULT NULL,
  txtMisionSecundaria INT DEFAULT NULL,
  resultadoBatalla INT DEFAULT NULL,
  fechaBatalla DATE DEFAULT NULL,
  indValidado INT DEFAULT NULL,
  idJugVictoriaConcedida INT DEFAULT NULL,
  bandoJugadorGanador INT DEFAULT NULL,
  victoriaSector INT DEFAULT NULL,
  audAlta DATETIME DEFAULT NULL,
  PRIMARY KEY (idEnfrentamiento),
  KEY idx_mb_enfrentamientos_liga_fase (idLiga, numFase, numRonda),
  KEY idx_mb_enfrentamientos_jugador1 (idJugador1),
  KEY idx_mb_enfrentamientos_jugador2 (idJugador2),
  CONSTRAINT fk_mb_enfrentamientos_liga FOREIGN KEY (idLiga) REFERENCES mb_ligas (idLiga),
  CONSTRAINT fk_mb_enfrentamientos_jugador1 FOREIGN KEY (idJugador1) REFERENCES mb_jugadores (idJugador),
  CONSTRAINT fk_mb_enfrentamientos_jugador2 FOREIGN KEY (idJugador2) REFERENCES mb_jugadores (idJugador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS mb_enfren_misiones_sec (
  idEnfrentamiento INT NOT NULL,
  idJugador1 INT NOT NULL,
  idMisionSecundaria INT NOT NULL,
  PRIMARY KEY (idEnfrentamiento, idJugador1, idMisionSecundaria),
  CONSTRAINT fk_mb_enfren_misiones_enfrentamiento FOREIGN KEY (idEnfrentamiento) REFERENCES mb_enfrentamientos (idEnfrentamiento),
  CONSTRAINT fk_mb_enfren_misiones_jugador FOREIGN KEY (idJugador1) REFERENCES mb_jugadores (idJugador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
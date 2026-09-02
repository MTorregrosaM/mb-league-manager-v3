-- Dummy data for local development and manual testing.
-- Run schema.sql before running this script.

USE mb_league;

INSERT INTO mb_juegos (idJuego, descJuego, indActivo, audAlta) VALUES
  (1, 'Flames of War', 1, '2026-09-01 10:00:00'),
  (2, 'Warhammer 40,000', 1, '2026-09-01 10:01:00'),
  (3, 'Bolt Action', 1, '2026-09-01 10:02:00');

INSERT INTO mb_facciones (idFaccion, idJuego, descFaccion, indActivo) VALUES
  (1, 1, 'United States', 1),
  (2, 1, 'Germany', 1),
  (3, 1, 'United Kingdom', 1),
  (4, 2, 'Space Marines', 1),
  (5, 2, 'Orks', 1),
  (6, 3, 'British Army', 1);

INSERT INTO mb_usuarios (idUsuario, nick, pass, rol, ult_acceso) VALUES
  (1, 'admin', '$2y$12$rhPLOhVXfkol.tTh5uKGfuyM6AVctz3Eoqqj63uQgWGuL5f31bQey', 'ADMIN', NULL),
  (2, 'league.manager', '$2y$12$e19Y30EV.ljr8q/1s0k5EOqJ36zpMakq8P3cPFo9fUhFX1CZOSuJK', 'USER', NULL),
  (3, 'scorekeeper', '$2y$12$rfuq7Riye1kXINWJedM6iuIYWtW7FO2lhENFatNEwCJiztkXh/UuK', 'USER', NULL);

INSERT INTO mb_ligas (idLiga, nombre, numFases, numRondas, indActivo, fecIni, fecFin, logo, idJuego, audAlta) VALUES
  (1, 'Autumn Test League', 2, 2, 1, '2026-09-01', NULL, 'logo-1.png', 1, '2026-09-01 10:10:00'),
  (2, 'Winter 40K Test League', 1, 2, 1, '2026-10-01', NULL, 'logo-2.png', 2, '2026-09-01 10:11:00');

INSERT INTO mb_fases (idLiga, numFase, numRonda, claveCifrada, fecIni, fecFin) VALUES
  (1, '1', '1', 'autumn-phase-1', '2026-09-01 00:00:00', NULL),
  (1, '1', '2', 'autumn-phase-1', '2026-09-01 00:00:00', NULL),
  (1, '2', '1', 'autumn-phase-2', '2026-10-01 00:00:00', NULL),
  (1, '2', '2', 'autumn-phase-2', '2026-10-01 00:00:00', NULL),
  (2, '1', '1', 'winter-phase-1', '2026-10-01 00:00:00', NULL),
  (2, '1', '2', 'winter-phase-1', '2026-10-01 00:00:00', NULL);

INSERT INTO mb_ligas_usuarios (idUsuario, idLiga) VALUES
  (2, 1),
  (2, 2),
  (3, 1);

INSERT INTO mb_jugadores (idJugador, idLiga, nick, nombre, apellido1, apellido2, foto, telefono, email, bando, puntosPintura, audAlta) VALUES
  (1, 1, 'Alex', 'Alex', 'Morgan', NULL, NULL, 600000001, 'alex@example.test', '1', 8, '2026-09-01 10:20:00'),
  (2, 1, 'Blake', 'Blake', 'Taylor', NULL, NULL, 600000002, 'blake@example.test', '2', 7, '2026-09-01 10:21:00'),
  (3, 1, 'Casey', 'Casey', 'Jordan', NULL, NULL, 600000003, 'casey@example.test', '3', 9, '2026-09-01 10:22:00'),
  (4, 1, 'Drew', 'Drew', 'Parker', NULL, NULL, 600000004, 'drew@example.test', '1', 6, '2026-09-01 10:23:00'),
  (5, 2, 'Evan', 'Evan', 'Reed', NULL, NULL, 600000005, 'evan@example.test', '4', 8, '2026-09-01 10:24:00'),
  (6, 2, 'Finley', 'Finley', 'Stone', NULL, NULL, 600000006, 'finley@example.test', '5', 7, '2026-09-01 10:25:00');

INSERT INTO mb_listas (idLiga, idJugador, numFase, bando, urlDocumento, fechaDocumento) VALUES
  (1, 1, 1, '1', 'docs/alex-main.pdf', '2026-09-01 11:00:00'),
  (1, 1, 1, '2', 'docs/alex-reserve.pdf', '2026-09-01 11:01:00'),
  (1, 2, 1, '2', 'docs/blake-main.pdf', '2026-09-01 11:02:00'),
  (1, 2, 1, '1', 'docs/blake-reserve.pdf', '2026-09-01 11:03:00'),
  (1, 3, 1, '3', 'docs/casey-main.pdf', '2026-09-01 11:04:00'),
  (1, 4, 1, '1', 'docs/drew-main.pdf', '2026-09-01 11:05:00'),
  (2, 5, 1, '4', 'docs/evan-main.pdf', '2026-09-01 11:06:00'),
  (2, 6, 1, '5', 'docs/finley-main.pdf', '2026-09-01 11:07:00');

INSERT INTO mb_misiones_secundarias (idMisionSecundaria, idLiga, tituloMisionSecundaria, txtMisionSecundaria, numMedallas) VALUES
  (1, 1, 'Hold the Line', 'Keep one objective under control at the end of the game.', 2),
  (2, 1, 'First Strike', 'Destroy an enemy unit during the first two turns.', 1),
  (3, 2, 'Secure the Relic', 'Control the central relic when the game ends.', 2);

INSERT INTO mb_enfrentamientos (idEnfrentamiento, idLiga, numFase, numRonda, idJugador1, idJugador2, bandoJugador1, bandoJugador2, resultadoJugador1, resultadoJugador2, valPintura, valPinturaJug1, valPinturaJug2, valDeportividadJug1, valDeportividadJug2, indMisionSecundaria, txtMisionSecundaria, resultadoBatalla, fechaBatalla, indValidado, idJugVictoriaConcedida, bandoJugadorGanador, victoriaSector, audAlta) VALUES
  (1, 1, 1, 1, 1, 2, '1', '2', 4, 2, 8, 4, 5, 5, 4, 1, 1, 1, '2026-09-05', 1, 0, 1, 0, '2026-09-05 20:00:00'),
  (2, 1, 1, 1, 3, 4, '3', '1', 1, 3, 7, 3, 4, 4, 5, 1, 2, 2, '2026-09-06', 1, 0, 1, 0, '2026-09-06 20:00:00'),
  (3, 2, 1, 1, 5, 6, '4', '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);

INSERT INTO mb_enfren_misiones_sec (idEnfrentamiento, idJugador1, idMisionSecundaria) VALUES
  (1, 1, 1),
  (1, 2, 2),
  (2, 3, 2);
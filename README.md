# MB League Manager

MB League Manager is a PHP application for managing tabletop wargaming leagues. It covers the complete competition workflow: leagues, games, factions, phases, rounds, players, army lists, match pairings and results.

## Current status

The project is a working server-rendered application with a legacy MVC-style structure and a shared responsive interface. The main authenticated workflows are available and use the common navigation, dark theme and responsive layouts.

Current application capabilities include:

- League creation, configuration and activation.
- Game and faction management.
- Phase and round management, including default calendar generation.
- Player registration and league assignment.
- Army list management by player and phase.
- Match pairing and double-list workflows.
- Public result entry by players using the phase secret key.
- Result editing, duplicate-submission protection and pending-result handling.
- Administrator validation of results after checking both submissions.
- Painting, sportsmanship, secondary mission and sector data for supported games.
- League progress, rankings, player details and match history.
- User accounts, roles and league permissions.
- A role-protected help page available from the `?` button in the menu.
- Reusable PHP components for displaying league data on external pages.

## Screenshots

<p>
	<a href="docs/screenshots/double-list.png"><img src="docs/screenshots/double-list.png" alt="League overview" width="280"></a>
	<a href="docs/screenshots/leagues.png"><img src="docs/screenshots/leagues.png" alt="League administration" width="280"></a>
	<a href="docs/screenshots/matches.png"><img src="docs/screenshots/matches.png" alt="Match results" width="280"></a>
</p>

## Main workflows

### League administration

An administrator creates a league by selecting the game, number of phases, number of rounds and active dates. The application generates the corresponding phase and round calendar by default. The administrator can then review the generated structure, assign users and manage the league content.

### Players and lists

Players are created under a league and can be associated with their army lists for each phase. Players must belong to the selected league before they can be used in a pairing or result.

### Match results

The result-entry workflow requires the league, phase, round, player identity, opponent, battle date and score. Depending on the game, it can also record painting, sportsmanship, secondary missions and sector information.

For supported Flames of War formats, the selected result determines the valid score range. An empate requires matching scores. The form also checks required fields and the phase secret key before saving.

After a player submits a result, the other player can submit the same match. If both submissions disagree, the result is reported for review instead of being treated as confirmed. A submission token prevents the same form from being sent repeatedly.

### Result validation

Results can remain pending until they have been reviewed. An administrator uses **Resultados** to find pending entries, checks the players, phase, round, date and score, edits incorrect data when necessary, and validates the result only after the information is consistent.

## Roles and access

- `USER`: works with the leagues assigned to the account, manages the available player and result workflows, and can access the help page.
- `ADMIN`: manages users, leagues, games, factions and permissions, can access every league, and validates or corrects results.

League access is checked for requests that contain a league, player or related entity. Unauthenticated requests and unauthorized league requests are rejected.

## Application structure

- Root PHP files: authenticated screens and competition workflows.
- `model/`: domain classes for leagues, games, factions, phases, players, matches and users.
- `controller/`: database operations and application rules.
- `config/`: authentication, security, database access, logging and configuration.
- `ajax/`: asynchronous endpoints used by selectors, rounds, pairings and result validation.
- `inc/`: reusable templates and helpers for external league displays.
	- `ranking.php`: league ranking template, expects `$idLiga`.
	- `fase-ronda.php`: round template, exposes `printFaseRonda($idLiga, $idFase, $idRonda)`.
	- `listas.php`: player army-list template, expects `$idLiga`.
	- `progreso_liga.php`: league progress template, expects `$idLiga`.
	- `funciones_mb_league.php` and `puntuacion-fow.php`: shared helper functions.
	- The former hardcoded `faseN-rondaM.php` copies and the obsolete `ranking2.php` have been removed.
- `assets/css/`: shared responsive styles and the application theme.
- `assets/js/`: jQuery, jQuery UI, calendars, form validation and supporting frontend libraries.
- `assets/img/`: existing application, league and interface assets.
- `sql/`: database schema and seed scripts.

## Main pages

- `index.php`: competition overview, progress and rankings.
- `gestion-ligas.php`: league management.
- `gestion-fases.php`: phases and rounds.
- `gestion-jugadores.php`: player management.
- `gestion-listas.php`: army lists.
- `gestion-resultados.php`: match and pairing management.
- `alta-resultados.php`: player result entry.
- `editar-resultados.php`: result review and administration.
- `detalle-jugador.php`: player history and lists.
- `cruce-doble-lista.php`: double-list pairing workflow.
- `ayuda.php`: authenticated user guide.

## Security and known technical debt

- Authentication, session expiration, CSRF tokens and league-level authorization are implemented in `config/auth.php`.
- Database access uses `mysqli` and prepared statements in the shared database class.
- Database credentials must remain outside version control.
- The legacy application still contains MD5-based password handling. This should be migrated to `password_hash()` and `password_verify()` before treating the authentication system as modernized.
- Existing controllers and database identifiers are retained for compatibility with the current schema and external integrations.

## External integrations

The PHP files in `inc/` can be included by other pages to render league rounds, player lists, progress information and rankings. Use the parameterized templates instead of creating a file for each phase and round. `pintar_tablas.php` provides another reusable reporting entry point. Integrations must provide the variables expected by the selected component and use the same database access configuration.

## Database scripts

- `sql/schema.sql` defines the application database structure.
- `sql/seed.sql` contains sample records for the supported workflows.
- `sql/seed_pro.sql` contains the project data set intended for the production-style deployment.

Review database scripts before applying them to an existing database, especially when preserving league, player or result data.

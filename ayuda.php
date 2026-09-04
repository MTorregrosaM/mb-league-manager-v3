<?php
require_once __DIR__ . "/config/auth.php";
require_once "cabecera.php";
?>

<html lang="es" data-bs-theme="dark">
<head>
  <title>Ayuda - MB League</title>
  <style>
    #contenedor-principal .pagina-ayuda .ranking-card {
      display: flow-root;
    }
    #contenedor-principal .pagina-ayuda .ranking-card h3 {
      float: none !important;
      display: block !important;
      width: 100% !important;
      clear: both !important;
    }
    #contenedor-principal .pagina-ayuda .ranking-card > p,
    #contenedor-principal .pagina-ayuda .ranking-card > ol,
    #contenedor-principal .pagina-ayuda .ranking-card > ul {
      clear: both !important;
    }
  </style>
</head>
<body>
<div id="contenedor-principal">
  <?php require_once "menu.php"; ?>

  <h2 class="h2"><span>Ayuda</span></h2>


  <div class="rankings-content pagina-ayuda">
    <section class="ranking-card">
      <h3>Como empezar</h3>
      <p>El portal organiza las competiciones por ligas. Desde el menu puedes consultar las competiciones, gestionar jugadores y registrar o revisar resultados.</p>
      <p>Los usuarios solo pueden trabajar con las ligas que tienen asignadas. El administrador puede gestionar todas las ligas y usuarios.</p>
    </section>

    <section class="ranking-card">
      <h3>Crear una liga</h3>
      <ol>
        <li>Entra en <strong>Ligas</strong> y selecciona la opcion para crear una liga.</li>
        <li>Introduce el nombre, el juego, el numero de fases, el numero de rondas y las fechas de inicio y fin.</li>
        <li>Marca la liga como activa cuando quieras que este disponible para trabajar con ella.</li>
        <li>Guarda el formulario. La liga quedara disponible en los selectores del portal.</li>
      </ol>
      <p>El administrador puede asignar la liga a cada usuario desde la gestion de ligas y usuarios.</p>
    </section>

    <section class="ranking-card">
      <h3>Fases y rondas</h3>
      <p>Una liga se divide en fases y cada fase contiene sus rondas. Entra en la gestion de fases de la liga para revisar o completar su configuracion.</p>
      <p>Al crear la liga se generan por defecto los calendarios correspondientes al numero de fases y rondas indicado. Comprueba la estructura antes de comenzar a registrar resultados.</p>
    </section>

    <section class="ranking-card">
      <h3>Usuarios y contrasena</h3>
      <p>El administrador crea los usuarios desde <strong>Usuarios</strong>. En el formulario se asignan el nombre de acceso, los datos del usuario, el rol y la contrasena.</p>
      <p>La contrasena debe asignarse al crear la cuenta y comunicarse al usuario por un canal seguro. El usuario debe cambiarla o solicitar una nueva si deja de ser confidencial.</p>
    </section>

    <section class="ranking-card">
      <h3>Anadir jugadores</h3>
      <ol>
        <li>Selecciona la liga correspondiente y abre <strong>Jugadores</strong>.</li>
        <li>Crea cada jugador indicando su nick y los datos solicitados.</li>
        <li>Si la competicion utiliza listas, entra en la gestion de listas para asociar la lista del jugador a la liga.</li>
      </ol>
      <p>Los jugadores deben estar dados de alta en la liga antes de poder incluirlos en un enfrentamiento.</p>
    </section>

    <section class="ranking-card">
      <h3>Gestion de enfrentamientos</h3>
      <p>En <strong>Cruces</strong> puedes consultar los emparejamientos de cada fase y ronda. Selecciona la liga, la fase y la ronda para trabajar con el enfrentamiento correspondiente.</p>
      <p>Usa <strong>Registrar resultado</strong> para indicar los puntos de ambos jugadores, la fecha y los datos adicionales disponibles para el juego. El resultado puede quedar pendiente de validacion.</p>
    </section>

    <section class="ranking-card">
      <h3>Alta de resultados</h3>
      <ol>
        <li>Selecciona la liga, la fase y la ronda del enfrentamiento.</li>
        <li>Introduce la clave de la fase cuando sea necesaria para acceder al formulario.</li>
        <li>Indica quien eres, comprueba tu contrincante y selecciona la fecha de batalla.</li>
        <li>Elige el resultado e introduce los puntos, ademas de los datos adicionales disponibles para el juego.</li>
        <li>Revisa todos los datos y envia el formulario una sola vez.</li>
      </ol>
      <p>El sistema puede dejar el resultado pendiente hasta que el otro jugador registre el mismo enfrentamiento. Cuando ambos resultados coinciden, queda preparado para su validacion.</p>
    </section>

    <section class="ranking-card">
      <h3>Por que puede fallar el alta de resultados</h3>
      <ul>
        <li>La clave secreta de la fase es incorrecta o no se han completado los campos obligatorios.</li>
        <li>Los puntos no corresponden con el resultado elegido. En Flames of War se comprueban los limites y la igualdad de puntos en un empate.</li>
        <li>El resultado ya se ha enviado desde ese formulario y se bloquea el reenvio para evitar duplicados.</li>
        <li>Los resultados enviados por los dos jugadores no coinciden. En ese caso hay que revisar los datos y contactar con los rangers si es necesario.</li>
        <li>Puede producirse un error interno al guardar los datos o al preparar la confirmacion del resultado.</li>
      </ul>
      <p>Si el alta falla, comprueba primero la liga, la fase, la ronda, la clave, los jugadores, la fecha y los puntos antes de volver a intentarlo.</p>
    </section>

    <section class="ranking-card">
      <h3>Validacion de resultados</h3>
      <p>Cuando un usuario registra un resultado, este puede quedar marcado como <strong>pendiente de validacion</strong>. El resultado debe revisarse antes de considerarlo definitivo.</p>
      <ol>
        <li>El usuario comprueba los jugadores, la fase, la ronda, la fecha y los puntos introducidos.</li>
        <li>El administrador entra en <strong>Resultados</strong> y filtra la liga o el jugador para localizar los resultados pendientes.</li>
        <li>Revisa los datos del enfrentamiento y pulsa el icono de validacion cuando sean correctos.</li>
        <li>Si hay un error, el administrador puede editar el resultado, guardar la correccion y validarlo despues.</li>
      </ol>
      <p>Solo debe validarse un resultado cuando los dos jugadores y el administrador hayan confirmado que la informacion es correcta.</p>
    </section>

    <section class="ranking-card">
      <h3>Consejos</h3>
      <ul>
        <li>Mant&eacute;n activa una liga solo durante el periodo en el que deba estar disponible.</li>
        <li>Revisa las fases, rondas y calendarios antes de introducir resultados.</li>
        <li>Valida los resultados cuando hayas comprobado que los datos son correctos.</li>
        <li>No compartas las contrasenas ni reutilices la misma contrasena en otros servicios.</li>
      </ul>
    </section>
  </div>
</div>
<?php require_once "footer.php"; ?>
</body>
</html>

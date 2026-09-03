(function ($) {
  "use strict";

  var labelsPorClase = {
    "btn-borrar": "Eliminar",
    "btn-editar-reg": "Editar",
    "btn-listas": "Ver listas",
    "btn-fases": "Ver fases",
    "btn-ligas-usuario": "Ver ligas",
    "btn-validar-resultado": "Validar resultado"
  };

  function obtenerLabel($imagen) {
    var title = $imagen.attr("title");
    if (title) {
      return title;
    }

    for (var clase in labelsPorClase) {
      if ($imagen.hasClass(clase)) {
        return labelsPorClase[clase];
      }
    }

    return "Acción";
  }

  function crearMenu($celda) {
    if ($celda.children(".acciones-menu").length) {
      return;
    }

    var $imagenes = $celda.find("form.form-btn-acciones > img").add($celda.children("img.btn-validar-resultado"));
    if (!$imagenes.length) {
      return;
    }

    var $menu = $("<div>", {
      "class": "acciones-menu"
    });
    var $trigger = $("<button>", {
      "type": "button",
      "class": "acciones-menu-trigger",
      "aria-label": "Mostrar acciones",
      "aria-expanded": "false",
      "text": "⋮"
    });
    var $lista = $("<div>", {
      "class": "acciones-menu-list",
      "role": "menu"
    });

    $imagenes.each(function () {
      var $imagen = $(this);
      var $item = $("<button>", {
        "type": "button",
        "class": "acciones-menu-item",
        "role": "menuitem"
      });
      var $icono = $imagen.clone().removeAttr("onclick").removeAttr("title");

      $item.append($icono).append($("<span>", {
        "text": obtenerLabel($imagen)
      }));
      $item.on("click", function (evento) {
        evento.preventDefault();
        evento.stopPropagation();
        cerrarMenus();
        $imagen.trigger("click");
      });
      $lista.append($item);
    });

    $trigger.on("click", function (evento) {
      evento.preventDefault();
      evento.stopPropagation();
      var abierto = $menu.hasClass("is-open");
      cerrarMenus();
      if (!abierto) {
        $menu.addClass("is-open");
        $trigger.attr("aria-expanded", "true");
        var $listaAbierta = $menu.children(".acciones-menu-list");
        $listaAbierta.css({ display: "flex", visibility: "hidden" });
        posicionarMenu($trigger, $listaAbierta);
      }
    });

    $menu.append($trigger).append($lista);
    $menu.data("acciones-lista", $lista);
    $celda.prepend($menu);
  }

  function crearMenus() {
    $("#contenedor-principal td.td-acciones").each(function () {
      crearMenu($(this));
    });
  }

  function cerrarMenus() {
    $(".acciones-menu.is-open").each(function () {
      var $menu = $(this);
      var $lista = $menu.data("acciones-lista");
      $menu.removeClass("is-open");
      $menu.children(".acciones-menu-trigger").attr("aria-expanded", "false");
      if ($lista && $lista.parent()[0] !== $menu[0]) {
        $menu.append($lista);
      }
      if ($lista) {
        $lista.css({ display: "", visibility: "", top: "", left: "" });
      }
    });
  }

  function posicionarMenu($trigger, $lista) {
    var rect = $trigger[0].getBoundingClientRect();
    var margen = 8;
    $lista.css({
      position: "fixed",
      zIndex: 2147483647,
      display: "flex",
      visibility: "hidden"
    });
    var ancho = $lista.outerWidth();
    var alto = $lista.outerHeight();
    var izquierda = Math.max(margen, Math.min(rect.right - ancho, window.innerWidth - ancho - margen));
    var arriba = rect.bottom + 6;

    if (arriba + alto > window.innerHeight - margen) {
      arriba = rect.top - alto - 6;
    }

    $lista.css({
      top: Math.max(margen, arriba),
      left: izquierda,
      visibility: "visible"
    });
  }

  $(function () {
    crearMenus();

    $(document).on("click", function (evento) {
      if (!$(evento.target).closest(".acciones-menu").length) {
        cerrarMenus();
      }
    });

    $(document).on("keydown", function (evento) {
      if (evento.key === "Escape") {
        cerrarMenus();
      }
    });

    $(window).on("resize scroll", cerrarMenus);

    if (window.MutationObserver) {
      new MutationObserver(crearMenus).observe(document.body, {
        childList: true,
        subtree: true
      });
    }
  });
})(jQuery);

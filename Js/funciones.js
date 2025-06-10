function login() {
  var UserName = $("#UserName").val();
  //debugger;
  let sonido_success = document.getElementById("success");
  let sonido_info = document.getElementById("info");
  let sonido_error = document.getElementById("error");
  let permisos = "";
  if (UserName == "" || UserName == null) {
    sonido_error.play();
    Command: toastr["error"]("No cuentas con permisos!!", "#FlexiTransfer");
    document.getElementById("UserName").value = "";
  } else {
    $.ajax({
      url: `http://192.168.10.3:7293/api/v1/permission/Userlogin?nombreUsuario=${UserName}&sistema=samp`,
      //url: `http://192.168.10.139:8085/api/v1/permission/Userlogin?nombreUsuario=${UserName}&sistema=flexidocker`,
      method: "GET",
      timeout: 0,
      success: function (respuesta) {
        //debugger
        let permiso = respuesta[0];
        if (permiso == "4092") {
          sonido_success.play();
          document.getElementById("overlay").style.display = "flex";
          Command: toastr["success"]("Bienvenido!", "#FlexiTransfer");
          sessionStorage.setItem("usuario", UserName);
          setTimeout(function () {
            window.location.href = "./modulos/pages/menus.html";
          }, 1000);
          document.getElementById("UserName").value = "";
        } else {
          sonido_error.play();
          Command: toastr["error"](
            "No cuentas con permisos!!",
            "#FlexiTransfer"
          );
          document.getElementById("UserName").value = "";
        }
      },
    });
  }
}

function cerrarSesion() {
  if (confirm("¿Estás seguro de que deseas cerrar la sesión?")) {
    sessionStorage.clear();

    document.getElementById("overlay").style.display = "flex";
    toastr["warning"]("Sesión finalizada.", "#FlexiTransfer");
    setTimeout(function () {
      window.location.href = "../../index.html";
    }, 1000);
  }
}

//Modulo Transferencia ENS-PT
function ordenes_asignadas() {
  // Muestra el spinner/overlay antes de la petición
  document.getElementById("overlay").style.display = "flex";

  $.ajax({
    url: "../../modulos/ordenes_surtido/index.php",
    type: "post",
    data: {titulo:'Ordenes Asignadas'},
    success: function (response) {
      // Inyecta la respuesta en tu contenedor
      $("#contenido").html(response);
    },
    error: function () {
      console.error("Ocurrió un error al cargar el contenido.");
    },
    complete: function () {
      // Se ejecuta tanto si es success como si es error
      // Oculta el spinner
      document.getElementById("overlay").style.display = "none";
    },
  });
}

function mod_multiples() {
  // Muestra el spinner/overlay antes de la petición
  document.getElementById("overlay").style.display = "flex";

  $.ajax({
    url: "../../modulos/etiqueta_multiple/index.php",
    type: "post",
    data: {titulo:'Crear Etiqueta Multiple'},
    success: function (response) {
      // Inyecta la respuesta en tu contenedor
      $("#contenido").html(response);
    },
    error: function () {
      console.error("Ocurrió un error al cargar el contenido.");
    },
    complete: function () {
      // Se ejecuta tanto si es success como si es error
      // Oculta el spinner
      document.getElementById("overlay").style.display = "none";
    },
  });
}

function atras() {
  document.getElementById("overlay").style.display = "flex";
  $.ajax({
    url: "../../modulos/pages/menu.html",
    type: "post",
    data: {},
    success: function (response) {
      $("#all_page").html(response);
      Permisos_menu();
    },
    error: function () {
      console.error("Ocurrió un error al cargar el contenido.");
    },
    complete: function () {
      // Se ejecuta tanto si es success como si es error
      // Oculta el spinner
      document.getElementById("overlay").style.display = "none";
    },
  });
}


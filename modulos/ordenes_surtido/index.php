<?php
$titulo = $_POST['titulo'] ?? '';
?>

  
  <div class="container mt-4 text-center" id="deley">
    <h5>
      <a href="" onclick="atras()">
      <i class="fa-solid fa-bars"></i> Menu
      </a>
    </h5>
  <div class="container text-center">
    <h4 id="titulo"><?php echo $titulo ?></h4>
  </div>
    <div class="container mt-4">
    <div class="" id ='os'>
    </div>
    <div class="table-responsive">
    </div>
    </div>
  </div>

  <!-- Spinner de carga oculto por defecto -->
  <div id="overlay" class="overlay text-center" style="display: none">
    <div class="spinner"></div>
    <br />
  </div>
  </div><!-- Modal -->
<div class="modal " id="Modal-solicitud-traslado" tabindex="-1" role="dialog" aria-labelledby="rechazoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="idtiq"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="det_modal">
              
            </div>  
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
            <!-- <button type='button' id ="Btn_insert" class="btn btn-success">Aceptar</button> -->
            </div>
        </div>
    </div>
</div>
  <script>
let sonido_success = document.getElementById("success");
let sonido_info = document.getElementById("info");
let sonido_error = document.getElementById("error"); 
  CargaOrden();
  
  function CargaOrden() {
    let almacenista =
    sessionStorage.getItem("usuario") || localStorage.getItem("usuario");
    //debugger
    var datos = {almacenista: almacenista};
    var Json = JSON.stringify(datos);
    console.log(Json);
    
    //debugger
    document.getElementById("overlay").style.display = "flex";
    $.ajax({
     url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=busca_orden'),
    // url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=busca_orden',
     // url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=busca_orden',
        type: 'POST',
        contentType: 'application/json',
        data: Json,
        beforeSend:function(){
          $('#os').html(`<center><i class='fa fa-spinner fa-spin fa-5x' style='color: #0370b0;'></i><br><br>Cargando...</center>`)
        },
        success: function(response) {
          console.log(response);
//debugger
          document.getElementById('os').innerHTML = '';

          response.forEach(item => {
            if (item.mensaje === 'OK') {
              let os = item.os;
              let turno = item.turno;
              let estatus = item.estatus;
              let idmaster = item.idmaster;
              let docentry = item.docEntry;

              // Crear card contenedora
              let card = document.createElement('div');
              card.className = 'card mb-3 p-0 border-0';
      
              // Crear botón como contenedor personalizado
              let boton = document.createElement('div');
              boton.style.height = '90px';
              boton.style.color = 'white';
              boton.style.fontSize = '15px';
              boton.style.padding = '5px';
              boton.style.borderRadius = '10px';
              boton.style.cursor = 'pointer';
              boton.style.textAlign = 'center';
              boton.style.display = 'flex';
              boton.style.flexDirection = 'column';
              boton.style.justifyContent = 'center';

              let titulo = document.createElement('strong');
              titulo.style.display = 'block';
              titulo.style.marginBottom = '4px'; 
              titulo.textContent = `OS: ${os} - [TURNO ${turno}]`;

              let titulo2 = document.createElement('strong');
              if(docentry !=''){
                
                titulo2.style.display = 'block';
                titulo2.style.marginBottom = '4px'; 
                titulo2.textContent = `Folio SAP: [${docentry}]`;
              }
              let descripcion = document.createElement('span');
              descripcion.style.display = 'block';
              descripcion.style.marginBottom = '0px';

              if (estatus === 'S') {
                
                boton.style.backgroundColor = '#FFB066'; 
                descripcion.textContent = 'Generar Solicitud de traslado';


                boton.onclick = function() {
                  $('#Modal-solicitud-traslado').modal('show');
                  $('#idtiq').html(`Crear solicitud de traslado`)
                  $('#det_modal').html(`<br>
                    <h5 id="os-x"></h5>
                    <br><br>
                    <button type="button" 
                  id="Btn_crea_solicitud" onclick ='sol_traslado(\"${os}\")' class="btn btn-info w-50 rounded shadow">
                    Crear
                    </button>
                    <br><br><br>`)
                    document.getElementById("os-x").textContent = `OS: ${os}` ;
                    
                };

              } else if (estatus === 'P') {
                boton.style.backgroundColor = '#72BDE6'; // azul
                descripcion.textContent = 'Pendiente de surtir';

                boton.onclick = function() {
                  
                  document.getElementById("overlay").style.display = "flex";
                  $.ajax({
                      url: "../../modulos/ordenes_surtido/lista_surtir.php",
                      type: "post",
                      data: {titulo:'Lista de Materiales',os:os},
                      success: function (response) {
                        //debugger
                        setTimeout(function () {
                          $("#deley").html(response);
                        }, 1000);
                        
                       
                        //Permisos_menu();
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
                };

              }else if (estatus === 'F') {
                boton.style.backgroundColor = '#76D7A8'; 
                descripcion.textContent = 'Surtida para entregar';

                boton.onclick = function () {
                  $("#Modal-solicitud-traslado").modal("show");
                  $("#idtiq").html("Surtida para entregar");

                  // Mostrar spinner de carga con Font Awesome
                  $("#det_modal").html(`
                      <div id="qr_img_container" style="text-align: center;">
                          <i class="fas fa-spinner fa-spin fa-7x" style="color: #3498db;"></i>
                          <p style="margin-top: 10px;">Generando QR...</p>
                          <p>${idmaster}</p>
                      </div>
                  `);

                  // Esperar un poco antes de generar el QR (simula el "proceso")
                  setTimeout(() => {
                      let master = `|${idmaster}|`;
                      let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(master)}&size=150x150`;

                      // Reemplazar el spinner con el QR
                      let container = document.getElementById("qr_img_container");
                      if (container) {
                          container.innerHTML = `
                              <img id="qr_img" src="${qrUrl}" alt="QR generado" style="width:150px; height:150px;" />
                              <p>${idmaster}</p>
                          `;
                      }
                  }, 300); // puedes subir a 500ms si quieres que se vea más el spinner
              };
              } else if (estatus==='N') {
                boton.style.backgroundColor = '#FF7C7C'; 
                boton.style.backgroundColor = 'gray';
                descripcion.textContent = 'NO TIENES ORDENES ASIGNADAS';
              } 

                boton.appendChild(titulo);
                boton.appendChild(titulo2);
                boton.appendChild(descripcion);
                card.appendChild(boton);
                document.getElementById('os').appendChild(card);
              } else {
                toastr.error('Ocurrió un error en algún registro', 'Mi Fleximatic');
              }
            });
      },
      error: function(data) {
        alert('ocurrió un error');
      }
    });

}

function sol_traslado(os) { 

  //let os = this.getAttribute("data-os");
  //debugger
  var datos = {os:os};
  var Json = JSON.stringify(datos);
  
  $.ajax({
    url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
    //url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
    //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
    type: 'POST',
    contentType: 'application/json',
    data: Json,
    beforeSend:function(){
          $('#Btn_crea_solicitud').prop("disabled",true);
        },
    success: function(response) { 
      //debugger
        console.log(response);

        let mensaje = response[0].mensaje;

        if (!mensaje.includes("|")) {
            // Si no contiene "|", mostrar el mensaje completo como error
            $('#Modal-solicitud-traslado').modal('hide');
            toastr.error(mensaje, 'App Surtido');
           
            return;
        }
        //debugger
        let partes = mensaje.split("|");
        let estado = partes[0];  // "OK"
        let id = partes[1];

        if (estado === 'OK') { 
            sonido_info.play();
            Command: toastr["success"]("Se creó la solicitud de traslado " + id + " !!", "#App Surtido");
        } else {
            //toastr.error(estado || 'Respuesta no válida', 'Mi Fleximatic');
           // $('#Modal-solicitud-traslado').modal('hide');
            Command: toastr["warning"](estado || 'Respuesta no válida', '#Surtido Surtido');
           
            
        }
    },
    error: function () {
        console.error("Ocurrió un error al cargar el contenido.");
    },
    complete: function () {
        document.getElementById("overlay").style.display = "none";
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('#Modal-solicitud-traslado').modal('hide'); 
        $('#Btn_crea_solicitud').prop("disabled",true);
        ordenes_asignadas()
    },
});


  }

   
  </script>
  
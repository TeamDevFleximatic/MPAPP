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
        <h4 id="titulo"></h4>
    </div>
    <div class="container mt-4 text-center">
        <button type="button" class="btn btn-info" id="btnCrear"><i class="fa-solid fa-qrcode"></i> Crear Etiqueta Multiple</button>
    </div>
    <div id="contenedorFolio" class="mt-3"></div>
    <div id="table-detalle" class="mt-3"></div>

</div>
<div class="modal " id="piezas" tabindex="-1"  aria-labelledby="rechazoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="codigo"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
              <ul style="list-style: none; padding-left: 0;">
                <li id="li_os" data-os=""></li>
                <li id="li_codigo" data-codigo=""></li>
                <li id="li_lote"></li>
              </ul>
            <span><strong>Ingresa la Cantidad de piezas:</strong></span>
            <input type="number" step="0.0001" id="input_modal" class="form-control mt-3 text-center" style="width: 25%; margin: 0 auto; display: block;"/>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button> -->
            <button type='button' id ="Btn_insert" class="btn btn-success">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script>


    let sonido_success = document.getElementById("success");
    let sonido_info = document.getElementById("info");
    let sonido_error = document.getElementById("error"); 
    document.getElementById('btnCrear').addEventListener('click', function () {
        let almacenista = sessionStorage.getItem("usuario") || localStorage.getItem("usuario");
        const contenedor = document.getElementById('contenedorFolio');
        const boton = this;
        var datos = {alm:almacenista};
        var Json = JSON.stringify(datos);
        $.ajax({
            //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
            url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=Crea_Folio_multiple',
            //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
            type: 'POST',
            contentType: 'application/json',
            data: Json,
            beforeSend:function(){
            $('#btnCrear').prop("disabled",true);
            },
            success: function(response) {
                //debugger 
                if (response[0].mensaje == 'OK') {
                    let etiqueta = response[0].id_etiqueta;

                    // Evitar crear múltiples elementos
                    if (document.getElementById('folioGenerado')) return;

                    // Crear <p> con estilo
                    const p = document.createElement('p');
                    p.id = 'folioGenerado';
                    p.className = 'text-center fw-bold mt-2';
                    p.textContent = etiqueta;

                    // Crear <input> deshabilitado
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control text-center fw-bold mt-2';
                    input.id = 'inputFolio';
                    input.placeholder = 'Escanea un contenedor';
                    input.disabled = false;

                    // Agregar al contenedor
                    contenedor.appendChild(p);
                    contenedor.appendChild(input);
                    input.focus();
                    

                    activarEnterSolo();
                    get_tabla(etiqueta)
                }
            },
            error: function () {
                console.error("Ocurrió un error al cargar el contenido.");
            },
            complete: function () {
                document.getElementById("overlay").style.display = "none"; 
                $('#btnCrear').prop("disabled",true);
                
            },
        });
    });
function activarEnterSolo() {
    const input = document.getElementById('inputFolio');
    if (!input) return;

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const id_etiqueta = input.value;
            let Etiq_multiple = document.getElementById("folioGenerado").textContent;
            var datos = {Etiq_multiple:Etiq_multiple,id_etiqueta:id_etiqueta};
            var Json = JSON.stringify(datos);
            //debugger
            $.ajax({
                //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
                url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=Valida_etiqueta',
                //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
                type: 'POST',
                contentType: 'application/json',
                data: Json,
                beforeSend:function(){
                document.getElementById("overlay").style.display = "none"; 
                },
                success: function(response) {
                    //debugger 
                    if (response[0].mensaje == 'OK') {
                        let id_etiqueta = response[0].id_etiqueta;
                        let cod_articulo = response[0].cod_articulo;
                        let pzs_restantes = response[0].pzs_restantes-1;
                        document.getElementById("codigo").textContent = "Codigo: "+cod_articulo; 
                        document.getElementById("li_os").textContent = id_etiqueta; 
                        document.getElementById("input_modal").value = pzs_restantes ; 
                            $('#piezas').modal('show');
                        //debugger 
                        
                    }else{
                        sonido_error.play();
                        Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
                    }
                },
                error: function () {
                    console.error("Ocurrió un error al cargar el contenido.");
                },
                    complete: function () {
                    document.getElementById('inputFolio').value = '';
                },
            });
                       
        }
    });
}
 
 document.getElementById("Btn_insert").onclick = function() {
    let cod = document.getElementById("codigo").textContent;
    let partes = cod.split(":"); // ["Codigo", " 22-001-119"]
    let codigo = partes[1].trim();
    let Etiq_multiple = document.getElementById("folioGenerado").textContent;
    let id_etiqueta = document.getElementById("li_os").textContent;
    let cantidad = document.getElementById("input_modal").value;
    var datos = {codigo:codigo,Etiq_multiple:Etiq_multiple,id_etiqueta:id_etiqueta,cantidad:cantidad};
    var Json = JSON.stringify(datos);
    //debugger
    $.ajax({
        //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
        url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=inserta_etiqueta',
        //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
        type: 'POST',
        contentType: 'application/json',
        data: Json,
        beforeSend:function(){
            document.getElementById("overlay").style.display = "flex";
        },
        success: function(response) {
            //debugger 
            if (response[0].mensaje == 'OK') {
                sonido_success.play();
                Command: toastr["success"]("Etiqueta Guardada!", "#App Surtido");
                get_tabla(Etiq_multiple)
            }else{
                sonido_error.play();
                Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
            }
        },
        error: function () {
            console.error("Ocurrió un error al cargar el contenido.");
        },
            complete: function () {
             $('#piezas').modal('hide');
             get_tabla(Etiq_multiple)
             document.getElementById("overlay").style.display = "none";
        },
    });
};
function get_tabla(Etiq_multiple){
    var datos = {Etiq_multiple:Etiq_multiple};
    var Json = JSON.stringify(datos);
    $.ajax({
        //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
        url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=tabla_detalle',
        //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
        type: 'POST',
        contentType: 'application/json',
        data: Json,
       
        success: function(response) {
            //debugger 
            let div = document.getElementById("table-detalle");

            // Limpiar contenido previo
            div.innerHTML = "";
            if (response[0].mensaje == 'OK') {
                let id_etiqueta1 = response[0].id_etiqueta;
                let id_etiqueta = id_etiqueta1.slice(-6);
                let cod_articulo = response[0].cod_articulo;
                let cantidad = response[0].cantidad;
              
            

            // Crear contenedor responsivo
            let contenedor = document.createElement("div");
            contenedor.className = "table-responsive w-100"; // ajusta el ancho según necesites

            // Crear tabla
            let tabla = document.createElement("table");
            tabla.className = "table table-sm table-bordered table-striped text-center";

            // Crear encabezado
            let thead = document.createElement("thead");
            thead.innerHTML = `
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Cantidad</th>
                
            </tr>
            `;
            tabla.appendChild(thead);
            
            let sumaCantidad = 0;
            // Crear cuerpo de la tabla
            let tbody = document.createElement("tbody");

            response.forEach(item => {
            let fila = document.createElement("tr");
            let cantidad = parseFloat(item.cantidad) || 0;
             sumaCantidad += cantidad;
            // Extraer últimos 6 caracteres
            let ultimos6 = item.id_etiqueta.slice(-6);

            fila.innerHTML = `
                <td onclick="eliminar('${item.recno}','${item.cantidad}','${item.id_etiqueta}')" style="cursor:pointer; color:blue; font-weight:bold; text-decoration:underline;">${ultimos6}</td>
                <td>${item.cod_articulo}</td>
                <td>${item.cantidad}</td>
            `;
                //debugger
            tbody.appendChild(fila);
            });

            tabla.appendChild(tbody);
            contenedor.appendChild(tabla);
            div.appendChild(contenedor);
            // Crear botón
            let boton = document.createElement("button");
            boton.innerHTML = `<i class="fa-solid fa-box-open"></i> Cerrar Caja`;
            boton.className = "btn btn-success mt-3"; // Estilo Bootstrap
            boton.onclick = function() {
                let Etiq_multiple = document.getElementById("folioGenerado").textContent;
                let flag = false;
                var datos = {Etiq_multiple:Etiq_multiple};
                var Json = JSON.stringify(datos);
                let confirmar = confirm("¿Estas seguro de cerrar esta caja?");
                $.ajax({
                    //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
                    url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=Cerrar_caja',
                    //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
                    type: 'POST',
                    contentType: 'application/json',
                    data: Json,
                    success: function(response) {
                        //debugger 
                        if (response[0].mensaje == 'OK') {
                            sonido_success.play();
                            Command: toastr["success"]("Dirigete a la termina a imprimir la etiqueta!", "#App Surtido");
                            flag = true
                           
                        }else{
                            sonido_error.play();
                            Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
                        }
                    },
                    error: function () {
                        console.error("Ocurrió un error al cargar el contenido.");
                    },
                    complete: function () {
                        if (flag){
                           //
                            $.ajax({
                                url: "/MPAPP/modulos/etiqueta_multiple/index.php",
                                type: "post",
                                data: {},
                                 beforeSend:function(){
                                document.getElementById("overlay").style.display = "flex";
                                },
                                success: function (response) {
                                    $("#contenido").html(response);
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
                            
                        }
                    },
                });
                
            };
           let contador = document.createElement("p");
            contador.className = "mt-2 fw-bold text-end";
            contador.textContent = `Total: ${sumaCantidad}`;
            div.appendChild(contador);

            // Agregar el botón al div
            div.appendChild(boton);
   
            }
        },
        error: function () {
            console.error("Ocurrió un error al cargar el contenido.");
        },
            complete: function () {
          
        },
    })
}
function eliminar(recno,cantidad,id_etiqueta) {
    let confirmar = confirm("¿Estás seguro de que quieres eliminar el registro de la etiqueta " + id_etiqueta + "?");
     if (confirmar) {
        let Etiq_multiple = document.getElementById("folioGenerado").textContent;
        var datos = {Etiq_multiple:Etiq_multiple,cantidad:cantidad,recno:recno};
        var Json = JSON.stringify(datos);
        $.ajax({
            //url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado'),
            url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/PICKING_MP?Accion=Borrar_detalle',
            //url: 'http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/CrearSolicitudTraslado?Accion=crear_sol_traslado',
            type: 'POST',
            contentType: 'application/json',
            data: Json,
            beforeSend:function(){
                document.getElementById("overlay").style.display = "flex";
            },
            success: function(response) {
                //debugger 
                if (response[0].mensaje == 'OK') {
                    sonido_success.play();
                    Command: toastr["success"]("Etiqueta Eliminada!", "#App Surtido");
                }else{
                    sonido_error.play();
                    Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
                }
            },
            error: function () {
                console.error("Ocurrió un error al cargar el contenido.");
            },
                complete: function () {
                    document.getElementById("overlay").style.display = "none";
                    get_tabla(Etiq_multiple)
            },
        });
    } else {
        Command: toastr["warning"]('Cancelaste la Opreación!!');
    }
  
}


</script>



<?php
$titulo = $_POST['titulo'] ?? '';
$os = $_POST['os'] ?? '';
$codigo = $_POST['codigo'] ?? '';
$sumcantmatpedida = $_POST['sumcantmatpedida'] ?? '';
$sumcantsurt = $_POST['sumcantsurt'] ?? '';
$um = $_POST['um'] ?? '';
$ubicacion = $_POST['ubicacion'] ?? '';
$ubicaciones = explode(',', $ubicacion);
$nombreRecortado = $_POST['nombreRecortado'] ?? '';
$Json = $_POST['Json'] ?? '';
$total = count($ubicaciones);
$mitad = ceil($total / 2);
$resta = $sumcantmatpedida - $sumcantsurt;
//supervisorproduccion 
?>
<div class="container mt-4 text-center" id="deley">
<h5>
<?php
    echo"<button type='button' class='btn btn-info' onclick='listaMat(\"$os\")'><i class='fa-solid fa-bars'></i> Lista de materiales</button>
   ";
?>
  </a>
</h5>
  <div class="container text-center"> 
    <h3 ><?php echo $codigo ?></h3>
    <h5 id="titulo"><?php echo $nombreRecortado ?></h5>
    <hr>
    <span><strong>Ubicacion actual:</strong></span>
    <?php

        echo "<div style='display: flex; justify-content: center; gap: 20px; text-align: center;'>"; 
        echo "<p style='font-size: 12px; font-weight: bold; margin: 2px 0;'>" . $ubicacion . "</p>";
        echo "</div>";
      
    ?>
  <hr>
  <div class="container mt-4 text-center" style="display: flex; flex-direction: column;  align-items: center; height: 100vh;">
    <img src="../../images/escanear.png" style="max-width: 40%; height: auto; max-height: px;" alt="scan Image">
    <input type="text" id ="etiqueta"class="form-control mt-3" placeholder="Escanear etiqueta..." style="width: 100%;"/>
     <span><strong>cantidad a surtir:</strong></span>
     <hr>
     <div class="card bg-info text-white">
      <h3 id="total"></h3>
     </div>
  </div>
</div>

  <!-- Spinner de carga oculto por defecto -->
<div id="overlay" class="overlay text-center" style="display: none">
    <div class="spinner"></div>
    <br />
</div>
</div><!-- Modal -->
<div class="modal " id="cantidad" tabindex="-1"  aria-labelledby="rechazoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="idtiq"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
              <ul style="list-style: none; padding-left: 0;">
                <li id="li_os" data-os="<?php  echo $os ?>"><strong><?php  echo $os ?></strong></li>
                <li id="li_codigo" data-codigo="<?php  echo $codigo ?>"><strong>Codigo: </strong> <?php  echo $codigo ?></li>
                <li id="li_lote"></li>
              </ul>
            <span><strong>Ingresa la Cantidad de Material:</strong></span>
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
let os = "<?php echo $os; ?>";
let codigo = "<?php echo $codigo; ?>";
let resta = "<?php echo $resta; ?>";
let ubicacion = "<?php echo $ubicacion; ?>";
var CodQr = '';

$(() => {
  const input = document.getElementById("input_modal");

  input.addEventListener("input", function () {
    const valor = this.value;
    if (valor.includes(".")) {
      const decimales = valor.split(".")[1];
      if (decimales.length > 4) {
        this.value = parseFloat(valor).toFixed(4);
      }
    }
  });
  $('#etiqueta').focus();
  // Obtener el array desde el localStorage
  const productos = JSON.parse(localStorage.getItem('productos_surtir')) || [];

  // Buscar el producto por su código
  const producto = productos.find(p => p.codigo === codigo);

  // Verificamos que lo encontró y actualizamos el h3
  if (producto) {
    document.getElementById('total').innerText = `${Number(resta).toFixed(4)} ${producto.um}`;
  } else {
    document.getElementById('total').innerText = '0'; // o algún valor por defecto
  }
  escanear();
  var datos = {codigo:codigo,ubicacion:ubicacion};
  var Json = JSON.stringify(datos);
  console.log(Json);

});

  function escanear() {
  document.getElementById("etiqueta").addEventListener("keypress", function (e) {
     var Cod = $('#etiqueta').val();
     let Cod2 = Cod.split('|');
     let CodQr = Cod2[1];


    //CodQr = $("#etiqueta").val();
    var datos = {etiqueta: CodQr,os:os, codigo:codigo};
    var Json = JSON.stringify(datos);
    console.log(Json);
    //debugger
    if (e.key === "Enter") {
      $.ajax({
        url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=valida_etiqueta'),
        //url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=valida_etiqueta',
        type: 'POST',
        contentType: 'application/json',
        data: Json,
        success: function(response) { 
          //debugger
          if (response.length > 0 && response[0].mensaje == 'OK') {
            let peso = response[0].peso;
            let loteValue = response[0].lote;
            let liLote = document.getElementById("li_lote");
            
            document.getElementById("input_modal").value = peso ; 
            document.getElementById("idtiq").textContent = response[0].etiqueta; 
            liLote.innerHTML = "<strong>Lote:</strong> " + loteValue;
            liLote.setAttribute("data-lote", loteValue);
            $('#cantidad').modal('show');
            document.getElementById('etiqueta').value = '';
          } else {
            sonido_error.play();
            Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
            document.getElementById('etiqueta').value = '';
          } 
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
      //$('#cantidad').modal('show');
    }
  });
}
function actualizarCantidadProducto(codigo, nuevaCantidad,nuevoEstatus) {
  // 1. Obtener el array desde el localStorage
  const productos = JSON.parse(localStorage.getItem('productos_surtir')) || [];

  // 2. Buscar el producto actual para obtener su 'um'
  const productoActual = productos.find(p => p.codigo === codigo);
  const unidad = productoActual?.um || ''; // por si no tiene 'um'

  
  // 3. Actualizar la cantidad
  const actualizados = productos.map(p => {
  if (p.codigo === codigo) {
    return {
      ...p,
      sumcantmatpedida: nuevaCantidad,
      estatus: nuevoEstatus
    };
  }
  return p;
});


  // 4. Guardar el array actualizado
  localStorage.setItem('productos_surtir', JSON.stringify(actualizados));

  // 5. Actualizar el <h3> con cantidad y unidad
  if (productoActual) {
    document.getElementById('total').innerText = `${nuevaCantidad.toFixed(4)} ${unidad}`;
  }
}
$('#Btn_insert').click(function() { 
    let os = $('#li_os').attr('data-os');
    let lote = $('#li_lote').attr('data-lote');
    let codigo = $('#li_codigo').attr('data-codigo');
    let idetiq = $('#idtiq').text();
    let cantidad = $('#input_modal').val();
    var datos = {os:os, etiqueta:idetiq,codigo:codigo,lote:lote,cantidad:cantidad,ubicacion:ubicacion};
    var Json = JSON.stringify(datos);
    //debugger
    $.ajax({
        url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=inserta_etiqueta'),
      // url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=inserta_etiqueta',
        type: 'POST',
        contentType: 'application/json',
        data: Json,
        beforeSend:function(){
          $('#Btn_insert').prop("disabled",true);
        },
        success: function(response) { 
        console.log(response);
          //debugger
          if (response.length > 0 && response[0].mensaje == 'OK') {
              sonido_success.play();
              Command: toastr["success"]("Etiqueta Guardada!", "#App Surtido");
              
              actualizarCantidadProducto(codigo,response[0].sumcantsurt,response[0].estatus);
              document.getElementById('etiqueta').value = '';
              //debugger
              if(response[0].sumcantsurt == 0 && response[0].fin == 0){
                listaMat(os)
                sonido_success.play();
                Command: toastr["success"]("Codigo "+codigo+" surtido totalmente!!", "#App Surtido");
              }else if(response[0].fin == 1 ){
                ordenes_asignadas() 
                sonido_info.play();
                Command: toastr["warning"]("orden completada!!", "#App Surtido");
                
              }
          } else {
            sonido_error.play();
            Command: toastr["warning"](response[0]?.mensaje || 'Respuesta no válida', 'Mi Fleximatic');
            document.getElementById('etiqueta').value = '';
          }
        },
        error: function () {
        console.error("Ocurrió un error al cargar el contenido.");
        },
        complete: function () {
          $('body').removeClass('modal-open');
          $('.modal-backdrop').remove();
          $('#cantidad').modal('hide');
          $('#Btn_insert').prop("disabled",false);
          $('#etiqueta').focus();
        document.getElementById("overlay").style.display = "none";
        },
      });
    console.log(Json);
});
function listaMat(os) {

  //alert ('aqui estoy')
//  debugger
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
}
function atras2() {
  document.getElementById("overlay").style.display = "flex";
  $.ajax({
    url: "../../modulos/pages/menu.html",
    type: "post",
    data: {},
    success: function (response) {
      $("#contenido").html(response);
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
</script>



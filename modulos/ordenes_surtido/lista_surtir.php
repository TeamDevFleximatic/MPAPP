<?php
$titulo = $_POST['titulo'] ?? '';
$os = $_POST['os'] ?? '';
?>
<div class="container mt-4 text-center" id="deley">
  <h5>
    <button type='button' class='btn btn-info' onclick='ordenes_asignadas()'><i class='fa-solid fa-bars'></i> Ordenes asignadas</button>
  </h5>
  <h3 class="fw-bold text-dark"><?php echo $os ?></h3>
  <h4 id="titulo" class="text-secondary"><?php echo $titulo ?></h4>

  <div class="container mt-4">
    <div id="os" class="row row-cols-1 g-3 justify-content-center">
      <!-- Aquí se insertarán las tarjetas -->
    </div>
  </div>
</div>

<!-- Spinner de carga oculto por defecto -->
<div id="overlay" class="overlay text-center" style="display: none">
  <div class="spinner-border text-primary" role="status"></div>
  <br />
</div>

<script>
let os = "<?php echo $os; ?>";
const datos = { os: os };
const Json = JSON.stringify(datos);
console.log(Json);

$(document).ready(() => {
  $.ajax({
    url: '/MPAPP/proxy.php?url=' + encodeURIComponent('http://192.168.10.139:8086/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=lista_materiales'),
    //url: 'https://localhost:7191/api/SurtidoMP/SurtidoMP/v1/App_MP?Accion=lista_materiales',
    type: 'POST',
    contentType: 'application/json',
    data: Json,
    beforeSend:function(){
      $('#os').html(`<center><i class='fa fa-spinner fa-spin fa-5x' style='color: #0370b0;'></i><br><br>Cargando...</center>`)
    },
    success: function (response) {
      console.log(response);
//debugger
      const contenedor = document.getElementById('os');
      contenedor.innerHTML = '';

      const productos = [];

      response.forEach(item => {
        const codmat = item.codmat?.trim() || '';
        const ubicacion = item.ubicacion?.trim() || '';
        const nommat = item.nommat?.trim() || '';
        const um = item.um?.trim() || '';
        const nombreRecortado = nommat.length > 23 ? nommat.substring(0, 23) + '...' : nommat;
        const sumcantmatpedida = parseFloat(item.sumcantmatpedida);
        const sumcantsurt = parseFloat(item.sumcantsurt);
        const status = item.estatus;
      
        productos.push({
          codigo: codmat,
          sumcantmatpedida: sumcantmatpedida,
          sumcantsurt:sumcantsurt,
          um: um,
          nommat:nommat,
          status:status
        });

        // Crear la estructura tipo tarjeta
        const col = document.createElement('div');
        col.className = 'col';

        const card = document.createElement('div');
        card.className = 'card p-3 shadow-sm';
        if(status==0){
          card.style.cursor = 'pointer';
          card.onclick = () => verDetalle(codmat, nommat, nombreRecortado, sumcantmatpedida,sumcantsurt,um, ubicacion);
        }else{
          card.style.cursor = 'not-allowed';
          card.style.backgroundColor = '#e3e3e3'; // Color de fondo para el estado surtido
        } 
        card.innerHTML = `
          <div class="fw-bold text-primary">${codmat}</div>
          <div class="text-muted">${nombreRecortado}</div>
          
        `;

        col.appendChild(card);
        contenedor.appendChild(col);
      });

      localStorage.setItem('productos_surtir', JSON.stringify(productos));
    },
    error: function () {
      console.error("Ocurrió un error al cargar el contenido.");
    },
    complete: function () {
      document.getElementById("overlay").style.display = "none";
    }
  });

  function verDetalle(codigo, nommat, nombreRecortado, sumcantmatpedida,sumcantsurt, um, ubicacion) {
    document.getElementById("overlay").style.display = "flex";
//debugger
    $.ajax({
      url: "../../modulos/ordenes_surtido/ubicacion.php",
      type: "post",
      data: {
        titulo: 'Surtir codigo',
        os: os,
        codigo: codigo,
        sumcantmatpedida: sumcantmatpedida,
        sumcantsurt: sumcantsurt,
        nombreRecortado: nombreRecortado,
        um: um,
        ubicacion: ubicacion
      },
      success: function (response) {
        setTimeout(function () {
          $("#deley").html(response);
        }, 1000);
      },
      error: function () {
        console.error("Ocurrió un error al cargar el contenido.");
      },
      complete: function () {
        document.getElementById("overlay").style.display = "none";
      }
    });
  }
});

function atras2() {
  document.getElementById("overlay").style.display = "flex";
  $.ajax({
    url: "../../../../modulos/pages/menu.html",
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
</script>

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
        <button type="button" class="btn btn-info" id="btnCrear"><i class="fa-solid fa-qrcode"></i> <?php echo $titulo ?></button>
    </div>
    <div id="contenedorFolio" class="mt-3"></div>
</div>

<script>
  document.getElementById('btnCrear').addEventListener('click', function () {
    const contenedor = document.getElementById('contenedorFolio');
    const boton = this;

    // Evitar crear múltiples elementos
    if (document.getElementById('folioGenerado')) return;

    // Crear <p> con estilo
    const p = document.createElement('p');
    p.id = 'folioGenerado';
    p.className = 'text-center fw-bold mt-2';
    p.textContent = generarFolio();

    // Agregar al contenedor
    contenedor.appendChild(p);

     boton.disabled = true;
  });

  // Función para generar el folio
  function generarFolio() {
    const fecha = new Date();
    return 'MUL-' + fecha.getFullYear() + 
           (fecha.getMonth() + 1).toString().padStart(2, '0') + 
           fecha.getDate().toString().padStart(2, '0') + 
           '-' + Math.floor(Math.random() * 1000);
  }
</script>

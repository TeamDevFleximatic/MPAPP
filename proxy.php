<?php
// Este proxy simplemente reenvía la petición y la respuesta sin alterarla
$apiUrl = filter_input(INPUT_GET, 'url', FILTER_VALIDATE_URL);
if (!$apiUrl) {
    http_response_code(400);
    echo 'URL no válida';
    exit;
}

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // ⚠️ solo para pruebas
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);

// Enviar body en POST, PUT, etc.
$input = file_get_contents('php://input');
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    curl_setopt($curl, CURLOPT_POSTFIELDS, $input);
}

// Pasar headers originales (opcional, puedes comentar si no necesitas)
$headers = [];
foreach (getallheaders() as $key => $value) {
    $headers[] = "$key: $value";
}
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

// Ejecutar
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

// Devolver código y tipo de contenido original
http_response_code($httpCode);
if ($contentType) {
    header('Content-Type: ' . $contentType);
}

echo $response;
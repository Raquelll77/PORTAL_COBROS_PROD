<?php
require_once __DIR__ . '/../includes/UltraMsg.php';

use Services\UltraMsg;

// ⚙️ Configura tus credenciales UltraMsg
$token = "nph5qqc84jt1cvge";
$instance_id = "instance41959";

// 📲 Número de WhatsApp destino (con código de país)
$numero = "50432054544"; // <-- cámbialo por tu número real (sin +)
$mensaje = "✅ Prueba desde Portal Movesa. Todo funcionando correctamente.";

// 🚀 Enviar mensaje
$client = new UltraMsg($token, $instance_id);
$response = $client->sendTextMessage($numero, $mensaje);

echo "<pre>";
print_r($response);
echo "</pre>";

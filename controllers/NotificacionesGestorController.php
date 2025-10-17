<?php

namespace Controllers;

use Model\NotificacionesGestor;
use MVC\Router;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Services\UltraMsg;

require_once __DIR__ . '/../includes/UltraMsg.php';

class NotificacionesGestorController
{
    // 🔹 Envía notificaciones a gestores con promesas de pago para hoy
    public static function enviarPromesasHoy(Router $router)
    {
        isAuth();
        NotificacionesGestor::useSQLSrv();

        $gestores = NotificacionesGestor::obtenerGestoresPromesasHoy();

        if (empty($gestores)) {
            echo json_encode(['status' => true, 'mensaje' => '✅ No hay promesas de pago para hoy.']);
            return;
        }

        foreach ($gestores as $gestor) {

            // 🔹 Obtener los préstamos específicos del gestor
            $detalleClientes = NotificacionesGestor::obtenerDetallePromesasHoyPorGestor($gestor->nombre);

            $listaClientes = "";
            foreach ($detalleClientes as $detalle) {
                $monto = number_format($detalle->montoPromesa ?? 0, 2);
                $listaClientes .= "• {$detalle->prenumero} - {$detalle->nombre_cliente} (L {$monto})\n";
            }

            if (empty($listaClientes)) {
                $listaClientes = "(Sin detalles disponibles)";
            }

            // 🔹 Mensaje completo
            $mensaje = "Tienes {$gestor->total_clientes} clientes con promesas de pago para hoy:\n\n{$listaClientes}\nIngresa al portal para revisarlos.";

            // Enviar WhatsApp
            self::enviarWhatsApp(
                $gestor->telefono,
                $gestor->nombre,
                $mensaje,
                "https://web.grupomovesa.com/portal/login.php"
            );

            // Enviar correo
            self::enviarEmail(
                $gestor->correo,
                $gestor->nombre,
                "Promesas de pago para hoy",
                nl2br($mensaje)  // convierte \n en <br> para HTML
            );
        }

        echo json_encode(['status' => true, 'mensaje' => '✅ Notificaciones enviadas a gestores.']);
    }


    // 🔹 Envía recordatorios al mediodía si no han gestionado
    public static function enviarRecordatorios(Router $router)
    {
        isAuth();
        NotificacionesGestor::useSQLSrv();

        $gestores = NotificacionesGestor::obtenerGestoresRecordatorios();

        if (empty($gestores)) {
            echo json_encode(['status' => true, 'mensaje' => '✅ No hay recordatorios pendientes.']);
            return;
        }

        foreach ($gestores as $gestor) {

            // 🔹 Obtener los préstamos específicos del gestor
            $detalleClientes = NotificacionesGestor::obtenerDetallePromesasHoyPorGestor($gestor->nombre);

            $listaClientes = "";
            foreach ($detalleClientes as $detalle) {
                $listaClientes .= "• {$detalle->prenumero} - {$detalle->nombre_cliente}\n";
            }

            if (empty($listaClientes)) {
                $listaClientes = "(Sin detalles disponibles)";
            }

            // 🔹 Mensaje completo
            $mensaje = "Tienes {$gestor->total_clientes} clientes con promesas de pago para hoy:\n\n{$listaClientes}\nIngresa al portal para revisarlos.";

            // Enviar WhatsApp
            self::enviarWhatsApp(
                $gestor->telefono,
                $gestor->nombre,
                $mensaje,
                "https://web.grupomovesa.com/PORTAL-COBROS.php"
            );

            // Enviar correo
            self::enviarEmail(
                $gestor->correo,
                $gestor->nombre,
                "Promesas de pago para hoy",
                nl2br($mensaje)  // nl2br para respetar saltos de línea en el correo
            );
        }


        echo json_encode(['status' => true, 'mensaje' => '✅ Recordatorios enviados.']);
    }

    // Función interna para enviar WhatsApp
    private static function enviarWhatsApp($telefono, $nombre, $mensaje, $link = "")
    {
        if (empty($telefono)) {
            error_log("⚠️ Teléfono vacío para {$nombre}");
            return;
        }

        $token = ULTRAMSG_TOKEN;       // Token UltraMsg
        $instance_id = ULTRAMSG_INSTANCE;    // ID de instancia UltraMsg
        $client = new UltraMsg($token, $instance_id);

        $to = "504" . preg_replace('/[^0-9]/', '', $telefono);
        $mensajeFinal = "Hola {$nombre} 👋\n{$mensaje}\n{$link}";

        $response = $client->sendTextMessage($to, $mensajeFinal);

        if (!isset($response['success']) || !$response['success']) {
            error_log("❌ Error enviando WhatsApp a {$telefono}: " . json_encode($response));
        }
    }

    // Función interna para enviar correo
    private static function enviarEmail($correo, $nombre, $asunto, $mensaje)
    {
        if (empty($correo))
            return;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'mail.grupomovesa.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'notificaciones@grupomovesa.com';
            $mail->Password = 'VYgd}T!XcVy}';
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom("notificaciones@grupomovesa.com", "Notificaciones Movesa");
            $mail->addAddress($correo, $nombre);
            $mail->Subject = $asunto;
            $mail->isHTML(true);
            $mail->Body = nl2br($mensaje); // convierte \n en <br> automáticamente
            $mail->AltBody = strip_tags($mensaje); // versión de texto plano por compatibilidad

            $mail->send();
        } catch (Exception $e) {
            error_log("❌ Error enviando correo a {$correo}: " . $mail->ErrorInfo);
        }
    }

    //Versión automática sin login, protegida por token
    public static function enviarPromesasHoyAuto()
    {

        $expectedToken = '5K9@2025';

        // Obtener token de la URL
        $token = $_GET['token'] ?? '';

        // Si no hay token o no coincide → rechazar
        if (!$token || !hash_equals($expectedToken, $token)) {
            http_response_code(401);
            echo json_encode([
                'status' => false,
                'mensaje' => '❌ Token inválido o no autorizado.'
            ]);
            return;
        }


        NotificacionesGestor::useSQLSrv();

        $gestores = NotificacionesGestor::obtenerGestoresPromesasHoy();

        if (empty($gestores)) {
            echo json_encode([
                'status' => true,
                'mensaje' => '✅ No hay promesas de pago para hoy.'
            ]);
            return;
        }

        foreach ($gestores as $gestor) {
            $detalleClientes = NotificacionesGestor::obtenerDetallePromesasHoyPorGestor($gestor->nombre);

            $listaClientes = "";
            foreach ($detalleClientes as $detalle) {
                $monto = number_format($detalle->montoPromesa ?? 0, 2);
                $listaClientes .= "• {$detalle->prenumero} - {$detalle->nombre_cliente} (L {$monto})\n";
            }

            if (empty($listaClientes)) {
                $listaClientes = "(Sin detalles disponibles)";
            }

            $mensaje = "Tienes {$gestor->total_clientes} clientes con promesas de pago para hoy:\n\n{$listaClientes}\nIngresa al portal para revisarlos.";

            self::enviarWhatsApp(
                $gestor->telefono,
                $gestor->nombre,
                $mensaje,
                "https://web.grupomovesa.com/portal/login.php"
            );

            self::enviarEmail(
                $gestor->correo,
                $gestor->nombre,
                "Promesas de pago para hoy",
                nl2br($mensaje)
            );
        }

        echo json_encode([
            'status' => true,
            'mensaje' => '✅ Notificaciones automáticas enviadas correctamente.'
        ]);
    }

}

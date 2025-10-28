<?php
namespace Controllers;

use MVC\Router;
use Model\Finiquito;
use Dompdf\Dompdf;
use Dompdf\Options;

class FiniquitoController
{
    public static function generar(Router $router)
    {
        $serie = $_GET['serie'] ?? '';

        if (!$serie) {
            $router->render('errores/falta_serie', [
                'mensaje' => 'Debe proporcionar una serie en la URL'
            ]);
            return;
        }

        $finiquito = Finiquito::obtenerPorSerie($serie);

        if (!$finiquito) {
            $router->render('errores/no_encontrado', [
                'mensaje' => "No se encontró información para la serie: $serie"
            ]);
            return;
        }

        // 📄 Renderizamos la plantilla HTML en memoria (sin mostrarla)
        $backgroundPath = 'C:/xampp/htdocs/PORTAL-COBROS/public/build/img/membrete.jpg';

        // ✅ conviertes la imagen a base64 (100% compatible con Dompdf)
        if (file_exists($backgroundPath)) {
            $imagenBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($backgroundPath));
        } else {
            $imagenBase64 = '';
        }

        // Dispones las variables que quieres usar en la plantilla
        ob_start();
        $fondo = $imagenBase64;
        include __DIR__ . "/../views/plantillas/finiquito.php";
        $html = ob_get_clean();

        // ⚙️ Configurar Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Para permitir imágenes, CSS remoto
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait'); // orientación vertical
        $dompdf->render();

        // 📦 Descarga o muestra el PDF
        $dompdf->stream("FINIQUITO_$serie.pdf", [
            "Attachment" => true // true = descarga automática, false = abrir en navegador
        ]);
    }
}

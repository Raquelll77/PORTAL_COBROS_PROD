<?php
namespace Controllers;

use Model\ClientesPrestamos;
use MVC\Router;
use Model\Finiquito;
use Dompdf\Dompdf;
use Dompdf\Options;

class FiniquitoController
{
    // 📄 Generar finiquito
    public static function generarFiniquito(Router $router)
    {
        $serie = $_GET['serie'] ?? '';

        if (!$serie) {
            $router->render('errores/falta_serie', [
                'mensaje' => 'Debe proporcionar una serie en la URL'
            ]);
            return;
        }

        try {
            self::generarPDF($serie, 'finiquito', "FINIQUITO_$serie.pdf");
        } catch (\Exception $e) {
            $router->render('errores/no_encontrado', [
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    // 📄 Generar carta de decomiso
    public static function generarCartaDecomiso(Router $router)
    {
        $serie = $_GET['serie'] ?? '';
        $prenumero = $_GET['prenumero'] ?? '';
        $gestor = $_GET['gestor'] ?? '';

        if (!$serie) {
            $router->render('errores/falta_serie', [
                'mensaje' => 'Debe proporcionar una serie en la URL'
            ]);
            return;
        }

        try {
            // Pasa los datos extra al método base
            self::generarPDF($serie, 'carta_decomiso', "CARTA_DECOMISO_$serie.pdf", [
                'prenumero' => $prenumero,
                'gestor' => $gestor
            ]);
        } catch (\Exception $e) {
            $router->render('errores/no_encontrado', [
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function generarCartaDevolucion(Router $router)
    {
        $serie = $_GET['serie'] ?? '';

        if (!$serie) {
            $router->render('errores/falta_serie', [
                'mensaje' => 'Debe proporcionar una serie en la URL'
            ]);
            return;
        }

        try {
            // Pasa los datos extra al método base
            self::generarPDF($serie, 'carta_devolucion', "CARTA_DEVOLUCION_$serie.pdf");
        } catch (\Exception $e) {
            $router->render('errores/no_encontrado', [
                'mensaje' => $e->getMessage()
            ]);
        }
    }

    public static function generarConstancia(Router $router)
    {
        $serie = $_GET['serie'] ?? '';
        $prenumero = $_GET['prenumero'] ?? '';
        $saldoPrestamo = ClientesPrestamos::getSaldoClientes($prenumero);

        if (!$serie) {
            $router->render('errores/falta_serie', [
                'mensaje' => 'Debe proporcionar una serie en la URL'
            ]);
            return;
        }

        try {
            // Pasa los datos extra al método base
            self::generarPDF($serie, 'constancia_consolidacion', "CONSTANCIA_CONSOLIDADA_$serie.pdf", [
                'saldo' => $saldoPrestamo
            ]);
        } catch (\Exception $e) {
            $router->render('errores/no_encontrado', [
                'mensaje' => $e->getMessage()
            ]);
        }
    }


    // 🧩 Función base que genera y descarga el PDF
    private static function generarPDF(string $serie, string $vista, string $nombreArchivo, array $extras = [])
    {
        $finiquito = Finiquito::obtenerPorSerie($serie);

        if (!$finiquito) {
            throw new \Exception("No se encontró información para la serie: $serie");
        }

        // 📄 Imagen de fondo
        $backgroundPath = 'C:/xampp/htdocs/PORTAL-COBROS/public/build/img/membrete.jpg';
        $fondo = file_exists($backgroundPath)
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($backgroundPath))
            : '';

        // ✅ Variables que estarán disponibles dentro de la vista
        $variables = [
            'finiquito' => $finiquito,
            'fondo' => $fondo,
            'prenumero' => $extras['prenumero'] ?? '',
            'gestor' => $extras['gestor'] ?? '',
            'saldo' => $extras['saldo'] ?? [],
        ];

        // ✅ Esto crea variables locales a partir de las claves del array
        extract($variables);

        // 🧠 Renderizar la vista
        ob_start();
        include __DIR__ . "/../views/plantillas/{$vista}.php";
        $html = ob_get_clean();

        // ⚙️ Configurar Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('Letter', 'portrait');
        $dompdf->render();

        // 📦 Descargar directamente
        $dompdf->stream($nombreArchivo, ["Attachment" => true]);
    }

}

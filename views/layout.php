<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Cobros | <?= $titulo ?? '' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Open+Sans&display=swap"
        rel="stylesheet">

    <!-- Incluir DataTables y el script -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <!-- CSS del proyecto -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/build/css/app.css">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/build/img/logoskg.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/build/img/logoskg.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/build/img/logoskg.jpg">
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">



    <!-- Librerías externas -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
</head>

<body>
    <?php $hideChrome = $hideChrome ?? false;
    $hideLoader = isset($_POST['skip_loader']) && $_POST['skip_loader'] == "1";

    if (!$hideChrome) { ?>
        <div id="loftloader-wrapper" style="
            position:fixed;
            top:0;left:0;
            width:100%;height:100%;
            background:rgba(0,0,0,0.4);
            z-index:9999;
            display:flex;
            justify-content:center;
            align-items:center;
            backdrop-filter:blur(4px);
        ">
            <div class="loader-content" style="display:flex;flex-direction:column;align-items:center;">
                <img src="<?= BASE_URL ?>/build/img/logoskg-transparente.png" alt="Logo SKG"
                    style="width:200px;height:auto;display:block;opacity:0.7; animation: pulse 1.5s infinite; " />
                <div class="spinner" style="
            border:4px solid rgba(0,0,0,0.1);
            border-left-color:#333;
            border-radius:50%;
            width:40px;height:40px;
            margin-top:15px;
            animation:spin 1s linear infinite;">
                </div>
            </div>
        </div>

    <?php } ?>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_name("PORTAL-COBROS");
        session_start();
    }
    // viene del render del controlador
    ?>

    <?php if (!$hideChrome): ?>
        <?php include __DIR__ . '/principal/header-dashboard.php'; ?>
    <?php endif; ?>

    <?= $contenido ?? '' ?>
    <?= $script ?? '' ?>

    <!-- JS externos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- 1. jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- 2. DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <!-- 3. DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

    <!-- 4. Dependencias de exportación -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- Loader personalizado con logo -->

    <style>
        body {
            overflow: hidden;
            /* evita scroll mientras carga */
        }

        /* Estado inicial visible */
        #loftloader-wrapper {
            opacity: 1;
            transition: opacity .4s ease;
        }

        /* Estado oculto */
        #loftloader-wrapper.hidden {
            opacity: 0;
            pointer-events: none;
        }


        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        window.skipLoader = false;

        // Detectar cuando se envía un form POST
        document.addEventListener("submit", function (e) {
            const form = e.target;
            if (form.method && form.method.toLowerCase() === "post") {
                // 🚩 marcar que es POST => no mostrar loader
                window.skipLoader = true;
            }
        }, true);

        // Cuando la página carga o se vuelve a mostrar
        $(window).on('load pageshow', function (e) {
            $("#loftloader-wrapper").hide();
            $("body").css("overflow", "auto");

            if (e.persisted) {
                location.reload();
            }
        });

        // Antes de salir/navegar
        $(window).on('beforeunload pagehide', function (e) {
            if (window.skipLoader) {
                window.skipLoader = false; // resetear para la próxima
                return; // 🚫 no mostrar loader en POST
            }

            $("#loftloader-wrapper").show();
            $("body").css("overflow", "hidden");
        });

        // Mostrar loader global
        function mostrarLoader(show = true) {
            if (show) {
                $("#loftloader-wrapper").show();
                $("body").css("overflow", "hidden");
            } else {
                $("#loftloader-wrapper").hide();
                $("body").css("overflow", "auto");
            }
        }

    </script>






</body>

</html>
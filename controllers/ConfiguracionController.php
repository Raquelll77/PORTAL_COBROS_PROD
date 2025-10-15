<?php

namespace Controllers;

use Model\ClientesPrestamos;
use Model\PrestamosXGestor;
use Model\Usuario;
use MVC\Router;
use Model\ActiveRecord;
use Clases\Upload;
use Model\NotificacionesGestor;


class ConfiguracionController
{
    public static function subir_creditos(Router $router)
    {
        isAuth();

        // Configurar tiempo de ejecución ilimitado
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $message = null;
        $status = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Verificar archivo cargado
                if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('No se seleccionó ningún archivo válido.');
                }

                // Configurar conexión a la base de datos
                ActiveRecord::useSQLSrv();
                $dbConnection = ActiveRecord::getActiveDB();

                // Procesar el archivo
                $uploader = new Upload($_FILES['file'], $dbConnection);
                $result = $uploader->processUpload();

                // Mensaje de éxito
                $message = "Archivo procesado exitosamente. Filas afectadas: $result.";
                $status = 'success';
            } catch (\Exception $e) {
                // Mensaje de error
                $message = htmlspecialchars($e->getMessage());
                $status = 'error';
            }
        }

        // Renderizar vista
        $router->render('configuracion/subir_creditos_xgestor', [
            'titulo' => 'Subir Creditos',
            'message' => $message,
            'status' => $status,
        ]);
    }

    public static function listarCreditos()
    {
        isAuth();
        $prestamos = PrestamosXGestor::all();

        header('Content-Type: application/json');
        echo json_encode($prestamos);
        exit;
    }
    public static function obtenerOpciones()
    {
        isAuth();

        $usuarios = PrestamosXGestor::obtenerUsuarios();
        $segmentos = PrestamosXGestor::obtenerSegmentos();

        echo json_encode([
            'success' => true,
            'usuarios' => $usuarios,
            'segmentos' => $segmentos
        ]);
    }

    public static function eliminarCredito()
    {
        isAuth();

        $id = $_POST['id'] ?? null;

        if ($id) {
            $credito = PrestamosXGestor::find($id);
            if ($credito) {
                $credito->eliminar();
                echo json_encode(['success' => true, 'message' => 'Crédito eliminado']);
                return;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar']);
    }

    public static function eliminarTodos()
    {
        isAuth();

        $deleted = PrestamosXGestor::eliminarTodos(); // debes crear este método en el modelo
        if ($deleted) {
            echo json_encode(['success' => true, 'message' => 'Todos los créditos fueron eliminados']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo eliminar la cartera']);
        }
    }

    public static function eliminarPorUsuario()
    {
        isAuth();

        $usuario = $_POST['usuarioCobros'] ?? null;

        if ($usuario) {
            // como ya sabes que la columna se llama usuarioCobros, se pasa directo
            $deleted = PrestamosXGestor::eliminarPorUsuario('usuariocobros', $usuario);

            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => "Créditos del usuario $usuario eliminados"
                ]);
                return;
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'No se pudo eliminar créditos del usuario'
        ]);
    }



    public static function actualizarCredito()
    {
        isAuth();

        $id = $_POST['id'] ?? null;

        if ($id) {
            $credito = PrestamosXGestor::find($id);

            if ($credito) {
                // Actualizar solo si vienen en POST
                $credito->prenumero = $_POST['prenumero'] ?? $credito->prenumero;
                $credito->usuarioCobros = $_POST['usuarioCobros'] ?? $credito->usuarioCobros;
                $credito->nombregestor = $_POST['nombregestor'] ?? $credito->nombregestor;
                $credito->meta = $_POST['meta'] ?? $credito->meta;
                $credito->segmento = $_POST['segmento'] ?? $credito->segmento;

                if ($credito->guardar()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Crédito actualizado correctamente'
                    ]);
                    return;
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al guardar en la base de datos'
                    ]);
                    return;
                }
            }
        }

        echo json_encode([
            'success' => false,
            'message' => 'No se encontró el crédito'
        ]);
    }




    public static function index(Router $router)
    {
        isAuth();
        $router->render('configuracion/index', [
            'titulo' => 'Menu Configuracion'

        ]);
    }

    public static function usuarios(Router $router)
    {
        $usuarios = Usuario::all();

        isAuth();
        $router->render('configuracion/usuarios', [
            'titulo' => 'Usuarios',
            'usuarios' => $usuarios
        ]);
    }
    public static function usuariosGuardar()
    {
        isAuth();
        header('Content-Type: application/json; charset=utf-8');

        // Usuario::useSQLSrv(); // Descomenta si esta tabla vive en SQL Server y no lo fijaste antes.

        $id = trim($_POST['id'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $rol = trim($_POST['rol'] ?? '');
        $estado = (int) ($_POST['estado'] ?? 0);
        $estado = $estado ? 1 : 0;

        // Validaciones básicas
        if ($usuario === '' || $nombre === '' || $rol === '') {
            echo json_encode(['ok' => false, 'message' => 'Faltan campos requeridos.']);
            return;
        }

        try {
            if ($id === '') {
                // CREAR
                $yaExiste = Usuario::where('usuario', $usuario);
                if ($yaExiste) {
                    echo json_encode(['ok' => false, 'message' => 'El usuario ya existe.']);
                    return;
                }

                if ($password === '') {
                    echo json_encode(['ok' => false, 'message' => 'La contraseña es requerida para crear el usuario.']);
                    return;
                }

                $u = new Usuario([
                    'nombre' => $nombre,
                    'usuario' => $usuario,
                    'password' => $password,   // SIN hash por decisión actual
                    'rol' => $rol,
                    'estado' => $estado,
                ]);

                if (!$u->guardar()) {
                    echo json_encode(['ok' => false, 'message' => 'No se pudo crear el usuario.']);
                    return;
                }

                echo json_encode([
                    'ok' => true,
                    'message' => 'Usuario creado.',
                    'data' => [
                        'id' => (int) $u->id,
                        'usuario' => $u->usuario,
                        'nombre' => $u->nombre,
                        'rol' => $u->rol,
                        'estado' => (int) $u->estado,
                    ]
                ]);
                return;
            }

            // ACTUALIZAR
            $u = Usuario::find((int) $id);
            if (!$u) {
                echo json_encode(['ok' => false, 'message' => 'Usuario no encontrado.']);
                return;
            }

            // Si el username cambia, validar duplicado
            if (strcasecmp($u->usuario, $usuario) !== 0) {
                $dup = Usuario::where('usuario', $usuario);
                if ($dup) {
                    echo json_encode(['ok' => false, 'message' => 'El usuario ya existe.']);
                    return;
                }
            }

            $u->nombre = $nombre;
            $u->usuario = $usuario;
            // NO tocar $u->password en actualización (se conserva)
            $u->rol = $rol;
            $u->estado = $estado;

            if (!$u->guardar()) {
                echo json_encode(['ok' => false, 'message' => 'No se pudo actualizar el usuario.']);
                return;
            }

            echo json_encode([
                'ok' => true,
                'message' => 'Usuario actualizado.',
                'data' => [
                    'id' => (int) $u->id,
                    'usuario' => $u->usuario,
                    'nombre' => $u->nombre,
                    'rol' => $u->rol,
                    'estado' => (int) $u->estado,
                ]
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'message' => 'Error en servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /configuracion/habilitar_usuario?id=123
     * Cambia estado a 1 y redirige al listado.
     */
    public static function usuariosHabilitar()
    {
        // isAuth(); y verificación de rol si aplica
        $id = (int) ($_REQUEST['id'] ?? 0);
        $isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // Usuario::useSQLSrv(); // si aplica
        $u = $id ? Usuario::find($id) : null;
        if ($u) {
            $u->estado = 1;
            $u->guardar();
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'estado' => 1, 'message' => 'Usuario habilitado']);
            exit;
        }
        header('Location: ' . BASE_URL . '/configuracion/usuarios');
        exit;
    }

    public static function usuariosInhabilitar()
    {
        $id = (int) ($_REQUEST['id'] ?? 0);
        $isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        $u = $id ? Usuario::find($id) : null;
        if ($u) {
            $u->estado = 0;
            $u->guardar();
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'estado' => 0, 'message' => 'Usuario inhabilitado']);
            exit;
        }
        header('Location: ' . BASE_URL . '/configuracion/usuarios');
        exit;
    }
}


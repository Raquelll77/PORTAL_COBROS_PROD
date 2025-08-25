<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        Usuario::useSQLSrv(); // si estás en SQL Server
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Buscar usuario
                $usuario = Usuario::where('usuario', $auth->usuario);

                if (!$usuario) {
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else {
                    // Verificar si está activo
                    if ((int) $usuario->estado !== 1) {
                        Usuario::setAlerta('error', 'Usuario inactivo. Contacte a un administrador.');
                    } else {
                        // Comparar contraseña (plain por ahora)
                        if (($_POST['password'] ?? '') === (string) $usuario->password) {
                            session_start();
                            $_SESSION['id'] = $usuario->id;
                            $_SESSION['nombre'] = $usuario->nombre;
                            $_SESSION['usuario'] = $usuario->usuario;
                            $_SESSION['rol'] = $usuario->rol;
                            $_SESSION['login'] = true;

                            if ($usuario->rol === 'TELECOBRO') {
                                header('Location: ' . BASE_URL . '/cobros');
                                exit;
                            }

                            header('Location: ' . BASE_URL . '/principal');
                            exit;
                        } else {
                            Usuario::setAlerta('error', 'Password incorrecto');
                        }
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas,
            'hideChrome' => true
        ]);
    }


    public static function logout()
    {
        // Cerrar la sesión
        session_start();
        $_SESSION = [];
        header('Location: ' . BASE_URL . '/');
    }
}

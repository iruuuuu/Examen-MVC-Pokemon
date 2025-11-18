<?php 

// 1. Enrutamiento de acciones
// Se determina la acción solicitada (registro, login, etc.).
// La acción por defecto es mostrar la vista de login.
$action = $_GET['action'] ?? 'login_view';

switch ($action) {
    case 'register':
        // Gestiona el registro de un nuevo usuario.
        // Si se reciben datos por POST, se procesa el formulario.
        if (isset($_POST['password2']) && isset($_POST['password']) && isset($_POST['nombre'])) {
            if ($_POST['password'] == $_POST['password2']) {
                UsuariosRepository::create($_POST['nombre'], $_POST['password'], 'maestro');
            }
            // Tras el intento de registro, se redirige al formulario de login.
            header('Location: index.php?c=usuarios');
            die();
        }
        // Si no hay datos POST, se muestra la vista de registro.
        require_once('views/registerView.phtml');
        break;

    case 'login':
        // Procesa el intento de inicio de sesión.
        if (isset($_POST['nombre']) && isset($_POST['password'])) {
            $usuario = UsuariosRepository::login($_POST['nombre'], $_POST['password']);
            if ($usuario) {
                $_SESSION['user'] = $usuario;
                header('Location: index.php');
                die();
            }
        }
        // Si el login falla (datos incorrectos o no enviados), se redirige de nuevo al formulario.
        header('Location: index.php?c=usuarios');
        die();
        break;

    case 'logout':
        // Cierra la sesión del usuario y redirige a la página de inicio.
        session_destroy();
        header('Location: index.php');
        die();
        break;

    case 'perfil':
        // Gestiona la visualización y actualización del perfil de usuario.
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?c=usuarios');
            die();
        }

        // Si se recibe un archivo, se procesa la actualización del avatar.
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $dir_subida = __DIR__ . '/../public/img/avatars/';
            $nombre_fichero = $_FILES['avatar']['name'];
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir_subida . $nombre_fichero)) {
                // Actualizar la base de datos
                UsuariosRepository::setAvatar($_SESSION['user']->getId(), $nombre_fichero);
                // Actualizar el objeto en la sesión
                $_SESSION['user']->setAvatar($nombre_fichero);
            }

            header('Location: index.php?c=usuarios&action=perfil');
            die();
        }

        // Si no hay datos POST, se muestra la vista del perfil.
        require_once 'views/perfilView.phtml';
        break;

    case 'admin_panel':
        // Muestra el panel de administración. Solo accesible para usuarios con rol 'admin'.
        if (!isset($_SESSION['user']) || $_SESSION['user']->getRol() != 'admin') {
            header('Location: index.php');
            die();
        }

        // Obtener los datos para el panel
        $maestro_mas_pokemon = UsuariosRepository::maestroConMasPokemon();
        $maestro_mas_equipos = UsuariosRepository::maestroConMasEquipos();

        // Cargar la vista del panel de admin
        require_once __DIR__ . '/../views/adminPanelView.phtml';
        break;

    case 'login_view':
    default:
        // Acción por defecto: Muestra el formulario de inicio de sesión.
        require_once 'views/loginView.phtml';
        break;
}
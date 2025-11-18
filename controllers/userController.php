<?php 

// Determinar la acción a realizar
$action = $_GET['action'] ?? 'login_view'; // Acción por defecto es mostrar el login

switch ($action) {
    case 'register':
        // Si se envía el formulario de registro
        if (isset($_POST['password2']) && isset($_POST['password']) && isset($_POST['nombre'])) {
            if ($_POST['password'] == $_POST['password2']) {
                UsuariosRepository::create($_POST['nombre'], $_POST['password'], 'maestro');
            }
            // Tras el registro, redirigir al formulario de login
            header('Location: index.php?c=usuarios');
            die();
        }
        // Si no, mostrar la vista de registro
        require_once('views/registerView.phtml');
        break;

    case 'login':
        // Si se envía el formulario de login
        if (isset($_POST['nombre']) && isset($_POST['password'])) {
            $usuario = UsuariosRepository::login($_POST['nombre'], $_POST['password']);
            if ($usuario) {
                $_SESSION['user'] = $usuario;
                header('Location: index.php');
                die();
            }
        }
        // Si el login falla, redirigir de nuevo al formulario de login
        header('Location: index.php?c=usuarios');
        die();
        break;

    case 'logout':
        session_destroy();
        header('Location: index.php');
        die();
        break;

    case 'perfil':
        // Asegurarse de que el usuario está logueado para ver el perfil
        if (isset($_SESSION['user'])) {
            // Si el usuario ha iniciado sesión, mostrar su perfil
            $usuario_id = $_SESSION['user']->getId();
            // Obtener los equipos y la pokedex del maestro
            $mis_equipos = EquiposRepository::getByMaestro($usuario_id);
            // Obtener los Pokémon capturados
            $mi_pokedex = PokedexRepository::getByMaestro($usuario_id);
            require_once 'views/perfilView.phtml';
        }
        break;

    case 'login_view':
    default:
        // Por defecto, o si se pide explícitamente, mostrar la vista de login
        require_once 'views/loginView.phtml';
        break;
}
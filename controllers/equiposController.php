<?php

// 1. Verificación de sesión
// Se comprueba si el usuario ha iniciado sesión. Si no, se le redirige a la página de inicio.
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// 2. Enrutamiento de acciones
// Se determina la acción solicitada por el usuario (listar, crear, ver, etc.).
// La acción por defecto es 'listar'. Se obtiene el ID del usuario de la sesión.
$action = $_GET['action'] ?? 'listar';
$usuario_id = $_SESSION['user']->getId();

switch ($action) {
    case 'crear':
        // Procesa la creación de un nuevo equipo.
        // Si se recibe un nombre por POST, se crea el equipo para el usuario actual.
        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
            $nombre_equipo = $_POST['nombre'];
            EquiposRepository::create($usuario_id, $nombre_equipo);
        }
        // Se redirige a la lista de equipos para mostrar el resultado.
        header('Location: index.php?c=equipos&action=listar');
        die();
        break;

    case 'eliminar':
        // Procesa la eliminación de un equipo.
        // Se requiere el ID del equipo por GET.
        if (isset($_GET['id'])) {
            $equipo_id = $_GET['id'];
            EquiposRepository::delete($equipo_id);
        }
        // Se redirige a la lista de equipos.
        header('Location: index.php?c=equipos&action=listar');
        die();
        break;

    case 'ver':
        if (isset($_GET['id'])) {
            $equipo_id = $_GET['id'];
            $equipo = EquiposRepository::getById($equipo_id);

            // Medida de seguridad: se comprueba que el equipo exista y pertenezca al usuario en sesión.
            // Si no, se redirige a la lista de equipos.
            if (!$equipo || $equipo->getUsuarioId() != $usuario_id) {
                header('Location: index.php?c=equipos&action=listar');
                die();
            }

            $pokemon_en_equipo = EquiposRepository::getPokemonInEquipo($equipo_id);
            $pokemon_disponibles = EquiposRepository::getPokemonDisponiblesParaEquipo($usuario_id, $equipo_id);

            require_once __DIR__ . '/../views/equipos/verView.phtml';
        } else {
            header('Location: index.php?c=equipos&action=listar');
            die();
        }
        break;

    case 'addPokemon':
        // Procesa la adición de un Pokémon a un equipo.
        // Se requiere el ID del equipo y del Pokémon.
        if (isset($_GET['equipo_id']) && isset($_GET['pokemon_id'])) {
            $equipo_id = $_GET['equipo_id'];
            $pokemon_id = $_GET['pokemon_id'];
            // Se valida que el equipo no supere el límite de 5 Pokémon.
            if (EquiposRepository::countPokemonInEquipo($equipo_id) < 5) {
                EquiposRepository::addPokemonToEquipo($equipo_id, $pokemon_id);
            }
        }
        // Se redirige a la vista del equipo para reflejar los cambios.
        header('Location: index.php?c=equipos&action=ver&id=' . $equipo_id);
        die();
        break;

    case 'removePokemon':
        // Procesa la eliminación de un Pokémon de un equipo.
        // Se requiere el ID del equipo y del Pokémon.
        if (isset($_GET['equipo_id']) && isset($_GET['pokemon_id'])) {
            EquiposRepository::removePokemonFromEquipo($_GET['equipo_id'], $_GET['pokemon_id']);
        }
        // Se redirige a la vista del equipo para reflejar los cambios.
        header('Location: index.php?c=equipos&action=ver&id=' . $_GET['equipo_id']);
        die();
        break;

    case 'listar':
    default:
        // Acción por defecto: Muestra la lista de equipos del usuario.
        $mis_equipos = EquiposRepository::getByMaestro($usuario_id);
        require_once __DIR__ . '/../views/equipos/listarView.phtml';
        break;
}
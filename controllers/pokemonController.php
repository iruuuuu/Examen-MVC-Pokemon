<?php

require_once __DIR__ . '/../helpers/fileHelper.php';

// 2. Enrutamiento de acciones
// Se determina la acción solicitada (crear, eliminar, ver, etc.).
$action = $_GET['action'] ?? 'listar';

// Lista de tipos de Pokémon permitidos
$allowed_types = ['electrico', 'fuego', 'tierra', 'lucha', 'agua'];

switch ($action) {
    case 'crear':
        // 1. Verificación de sesión: Solo los usuarios autenticados pueden crear Pokémon.
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?c=usuarios');
            die();
        }

        // Gestiona la creación de un nuevo Pokémon.
        // Si se reciben datos por POST, se procesa el formulario.
        if (isset($_POST['nombre']) && isset($_POST['tipo'])) {
            $nombre = $_POST['nombre'];
            $tipo = $_POST['tipo'];
            $creador_id = $_SESSION['user']->getId();
            $foto = 'default.png'; // Foto por defecto

            // Validar que el tipo esté en la lista de permitidos
            if (!in_array($tipo, $allowed_types)) {
                // Si el tipo no es válido, se interrumpe la operación.
                header('Location: index.php');
                die();
            }

            // Manejo de la subida de la imagen
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                // Ruta absoluta al directorio de destino
                $dir_subida = __DIR__ . '/../public/img/pokemon/';
                $nombre_fichero = $_FILES['foto']['name'];
                // Se mueve el fichero subido al directorio de destino.
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir_subida . $nombre_fichero)) {
                    $foto = $nombre_fichero; // Si se sube correctamente, se actualiza el nombre de la foto.
                }
            }

            // 1. Crear el Pokémon y obtener su nuevo ID
            $new_pokemon_id = PokemonRepository::create($nombre, $tipo, $foto, $creador_id);

            // [LÓGICA DESHABILITADA]
            // PokedexRepository::addPokemon($creador_id, $new_pokemon_id);

            header('Location: index.php');
            die();
        }
        // Si no hay datos POST, se muestra el formulario de creación.
        require_once __DIR__ . '/../views/createView.phtml';
        break;

    case 'eliminar':
        // Procesa la eliminación de un Pokémon. Solo accesible para administradores.
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            die();
        }

        if (isset($_GET['id']) && $_SESSION['user']->getRol() == 'admin') {
            PokemonRepository::delete($_GET['id']);
        }
        header('Location: index.php');
        die();
        break;

    case 'ver':
        // Muestra la vista detallada de un Pokémon específico.
        if (isset($_GET['id'])) {
            $pokemon = PokemonRepository::getById($_GET['id']);
            if ($pokemon) {
                // Se comprueba si el usuario ha iniciado sesión y si ha capturado este Pokémon.
                $capturado = false;
                if (isset($_SESSION['user'])) {
                    $capturado = PokedexRepository::exists($_SESSION['user']->getId(), $pokemon->getId());
                }
                require_once __DIR__ . '/../views/showView.phtml';
            } else {
                // Si el Pokémon no existe, redirigir a la lista principal
                header('Location: index.php');
                die();
            }
        }
        break;

    case 'editar':
        // Gestiona la edición de un Pokémon. Solo accesible para administradores.
        if (!isset($_SESSION['user'])) {
            header('Location: index.php');
            die();
        }

        if (!isset($_GET['id']) || $_SESSION['user']->getRol() != 'admin') {
            header('Location: index.php');
            die();
        }

        // Si se reciben datos por POST, se procesa la actualización.
        if (isset($_POST['nombre']) && isset($_POST['tipo'])) {
            $id = $_GET['id'];
            $nombre = $_POST['nombre'];
            $tipo = $_POST['tipo'];
            $foto = $_POST['foto_actual']; // Mantenemos la foto actual por defecto

            // Validar que el tipo esté en la lista de permitidos
            if (!in_array($tipo, $allowed_types)) {
                header('Location: index.php?c=pokemon&action=ver&id=' . $id);
                die();
            }

            // Si se sube una nueva foto, la procesamos
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $dir_subida = __DIR__ . '/../public/img/pokemon/';

                // Si el directorio no existe, lo creamos
                if (!is_dir($dir_subida)) {
                    mkdir($dir_subida, 0777, true);
                }

                // Generamos un nombre de fichero único
                $nombre_fichero_unico = uniqid('pokemon_', true) . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

                if (move_uploaded_file($_FILES['foto']['tmp_name'], $dir_subida . $nombre_fichero_unico)) {
                    $foto = $nombre_fichero_unico;
                }
            }

            PokemonRepository::update($id, $nombre, $tipo, $foto);
            header('Location: index.php?c=pokemon&action=ver&id=' . $id);
            die();
        }

        // Si no hay datos POST, se muestra el formulario de edición con los datos actuales.
        $pokemon = PokemonRepository::getById($_GET['id']);
        require_once __DIR__ . '/../views/pokemon/editView.phtml';
        break;

    default: // 'listar' y cualquier otro caso no reconocido.
        // Redirige a la página principal.
        header('Location: index.php');
        die();
        break;
}
<?php

// 1. Verificación de sesión
// Se comprueba si el usuario ha iniciado sesión. Si no, se le redirige a la página de inicio.
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    die();
}

// 2. Enrutamiento de acciones
// Se determina la acción solicitada (capturar, quitar, ver).
// La acción por defecto es 'ver'.
$action = $_GET['action'] ?? 'ver';
$usuario_id = $_SESSION['user']->getId();

switch ($action) {
    case 'capturar':
        // Procesa la captura de un Pokémon, añadiéndolo a la Pokédex del usuario.
        if (isset($_GET['id'])) {
            $pokemon_id = $_GET['id'];
            // Evitar duplicados
            if (!PokedexRepository::exists($usuario_id, $pokemon_id)) {
                PokedexRepository::addPokemon($usuario_id, $pokemon_id);
            }
            // Redirigir de vuelta a la página de detalles del Pokémon
            // para que el usuario vea el cambio de estado (botón "Capturar" a "Quitar").
            header('Location: index.php?c=pokemon&action=ver&id=' . $pokemon_id);
            die();
        }
        break;
    case 'quitar':
        // Procesa la liberación de un Pokémon, eliminándolo de la Pokédex del usuario.
        if (isset($_GET['id'])) {
            $pokemon_id = $_GET['id'];
            PokedexRepository::removePokemon($usuario_id, $pokemon_id);
            // Redirigir de vuelta a la página de detalles del Pokémon.
            header('Location: index.php?c=pokemon&action=ver&id=' . $pokemon_id);
            die();
        }
        break;

    case 'ver':
    default:
        // Acción por defecto: Muestra la Pokédex personal del usuario.
        // Se cargan tanto los Pokémon capturados como los que aún están disponibles.
        $mis_pokemon = PokedexRepository::getByMaestro($usuario_id);
        $disponibles = PokedexRepository::getDisponiblesParaMaestro($usuario_id);

        // Mostrar la vista de la Pokédex personal
        require_once __DIR__ . '/../views/pokedex/verView.phtml';
        break;
}
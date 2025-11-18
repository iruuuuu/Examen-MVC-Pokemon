<?php 

require_once __DIR__ . '/../models/Usuarios.php';
require_once __DIR__ . '/../models/UsuariosRepository.php';
require_once __DIR__ . '/../models/Equipos.php';
require_once __DIR__ . '/../models/EquiposRepository.php';
require_once __DIR__ . '/../models/Pokedex.php';
require_once __DIR__ . '/../models/PokedexRepository.php';
require_once __DIR__ . '/../models/Pokemon.php';
require_once __DIR__ . '/../models/PokemonRepository.php';

// Iniciar la sesión es lo primero que debemos hacer para que esté disponible en todos los controladores.
session_start();

// 1. Enrutamiento (Routing): Comprueba si se ha pasado un parámetro 'c' (controlador)
//    en la URL. Si es así, delega la petición al controlador correspondiente
//    (ej: userController.php, threadController.php) y ese controlador se encargará
//    de la petición.
if (isset($_GET['c'])) {
    // Usamos __DIR__ para asegurar que la ruta es relativa a la carpeta de controladores
    require_once(__DIR__ . '/' . $_GET['c'] . 'Controller.php');
}

// 2. Carga de datos y renderizado de la vista principal.
//    Esta parte se ejecuta si no se ha llamado a ningún otro controlador.
//    Siempre se obtienen todos los Pokémon para mostrarlos tanto a usuarios
//    logueados como a visitantes.
$pokemons = PokemonRepository::getAll();
require_once 'views/mainView.phtml';
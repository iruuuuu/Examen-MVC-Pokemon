<?php 

require_once 'Pokemon.php';
require_once 'Pokedex.php';

class PokedexRepository{

    /**
     * Obtiene los Pokémon que un maestro ha capturado.
     * @param int $usuario_id
     * @return Pokemon[]
     */
    public static function getByMaestro($usuario_id)
    {
        $db = Connection::connect();
        $q = "SELECT p.* FROM pokemons p JOIN pokedex pd ON p.id = pd.pokemon_id WHERE pd.usuario_id = " . $usuario_id;
        $result = $db->query($q);
        $pokemons = [];
        while ($row = $result->fetch_assoc()) {
            $pokemons[] = new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return $pokemons;
    }

    /**
     * Obtiene los Pokémon que un maestro todavía no ha capturado.
     * @param int $usuario_id
     * @return Pokemon[]
     */
    public static function getDisponiblesParaMaestro($usuario_id)
    {
        $db = Connection::connect();
        $q = "SELECT * FROM pokemons WHERE id NOT IN (SELECT pokemon_id FROM pokedex WHERE usuario_id = " . $usuario_id . ")";
        $result = $db->query($q);
        $pokemons = [];
        while ($row = $result->fetch_assoc()) {
            $pokemons[] = new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return $pokemons;
    }

    /**
     * Añade un Pokémon a la Pokédex de un maestro.
     * @param int $usuario_id
     * @param int $pokemon_id
     */
    public static function addPokemon($usuario_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "INSERT INTO pokedex (usuario_id, pokemon_id) VALUES (" . $usuario_id . ", " . $pokemon_id . ")";
        $db->query($q);
    }

    /**
     * Elimina un Pokémon de la Pokédex de un maestro.
     * @param int $usuario_id
     * @param int $pokemon_id
     */
    public static function removePokemon($usuario_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "DELETE FROM pokedex WHERE usuario_id = " . $usuario_id . " AND pokemon_id = " . $pokemon_id;
        $db->query($q);
    }

    public static function exists($usuario_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "SELECT COUNT(*) as total FROM pokedex WHERE usuario_id = " . $usuario_id . " AND pokemon_id = " . $pokemon_id;
        $result = $db->query($q);
        $row = $result->fetch_assoc();
        return (int)$row['total'] > 0;
    }
}
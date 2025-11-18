<?php

require_once 'Pokemon.php';

class PokemonRepository
{
    /**
     * Obtiene todos los Pokémon de la base de datos.
     * @return Pokemon[]
     */
    public static function getAll()
    {
        $db = Connection::connect();
        $q = "SELECT * FROM pokemons ORDER BY id ASC";
        $result = $db->query($q);
        $pokemons = [];
        while ($row = $result->fetch_assoc()) {
            $pokemons[] = new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return $pokemons;
    }

    /**
     * Obtiene un Pokémon por su ID.
     * @param int $id
     * @return Pokemon|null
     */
    public static function getById($id)
    {
        $db = Connection::connect();
        $q = "SELECT * FROM pokemons WHERE id=" . $id;
        $result = $db->query($q);
        if ($row = $result->fetch_assoc()) {
            return new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return null;
    }

    /**
     * Crea un nuevo Pokémon.
     * @param string $nombre
     * @param string $tipo
     * @param string $foto
     * @param int $creador_id
     * @return int El ID del nuevo Pokémon.
     */
    public static function create($nombre, $tipo, $foto, $creador_id) {
        $db = Connection::connect();
        $q = "INSERT INTO pokemons (nombre, tipo, foto, creador_id) VALUES ('" . $nombre . "', '" . $tipo . "', '" . $foto . "', " . $creador_id . ")";
        $db->query($q);
        return $db->insert_id;
    }

    /**
     * Elimina un Pokémon por su ID.
     * @param int $id
     */
    public static function delete($id) {
        $db = Connection::connect();
        $q = "DELETE FROM pokemons WHERE id = " . $id;
        $db->query($q);
    }

    /**
     * Actualiza un Pokémon existente.
     * @param int $id
     * @param string $nombre
     * @param string $tipo
     * @param string $foto
     */
    public static function update($id, $nombre, $tipo, $foto) {
        $db = Connection::connect();
        $q = "UPDATE pokemons SET nombre = '" . $nombre . "', tipo = '" . $tipo . "', foto = '" . $foto . "' WHERE id = " . $id;
        $db->query($q);
    }

    /**
     * Obtiene los Pokémon que un maestro todavía no ha capturado.
     * Asume una tabla 'pokedex' con 'usuario_id' y 'pokemon_id'.
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
     * Añade un Pokémon a la Pokédex de un maestro.
     * @param int $usuario_id
     * @param int $pokemon_id
     */
    public static function addToPokedex($usuario_id, $pokemon_id)
    {
        $db = Connection::connect();
        $q = "INSERT INTO pokedex (usuario_id, pokemon_id) VALUES (" . $usuario_id . ", " . $pokemon_id . ")";
        $db->query($q);
    }

    /**
     * Elimina un Pokémon de la Pokédex de un maestro.
     * @param int $usuario_id
     * @param int $pokemon_id
     */
    public static function removeFromPokedex($usuario_id, $pokemon_id)
    {
        $db = Connection::connect();
        $q = "DELETE FROM pokedex WHERE usuario_id = " . $usuario_id . " AND pokemon_id = " . $pokemon_id;
        $db->query($q);
    }
}
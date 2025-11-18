<?php 

require_once 'Equipos.php';
require_once 'Pokemon.php';

class EquiposRepository
{
    public static function getAll()
    {
        $db = Connection::connect();
        $q = "SELECT * FROM equipos";
        $result = $db->query($q);
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = new Equipos($row['id'], $row['usuario_id'], $row['nombre']);
        }
        return $equipos;
    }


    public static function getByMaestro($usuario_id)
    {
        $db = Connection::connect();
        $q = "SELECT * FROM equipos WHERE usuario_id = " . $usuario_id;
        $result = $db->query($q);
        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = new Equipos($row['id'], $row['usuario_id'], $row['nombre']);
        }
        return $equipos;
    }


    public static function getById($id) {
        $db = Connection::connect();
        $q = "SELECT * FROM equipos WHERE id = " . $id;
        $result = $db->query($q);
        if ($row = $result->fetch_assoc()) {
            return new Equipos($row['id'], $row['usuario_id'], $row['nombre']);
        }
        return null;
    }

    /**
     * Crea un nuevo equipo.
     * @param int $usuario_id
     * @param string $nombre
     * @return int El ID del nuevo equipo.
     */
    public static function create($usuario_id, $nombre) {
        $db = Connection::connect();
        $q = "INSERT INTO equipos (usuario_id, nombre) VALUES (" . $usuario_id . ", '" . $nombre . "')";
        $db->query($q);
        return $db->insert_id;
    }

    /**
     * Elimina un equipo por su ID.
     * @param int $id
     */
    public static function delete($id) {
        $db = Connection::connect();
        $q = "DELETE FROM equipos WHERE id = " . $id;
        $db->query($q);
    }

    /**
     * Obtiene los Pokémon que pertenecen a un equipo.
     * @param int $equipo_id
     * @return Pokemon[]
     */
    public static function getPokemonInEquipo($equipo_id) {
        $db = Connection::connect();
        // Obtiene los Pokémon del sistema que no están en un equipo específico.
        $q = "SELECT p.* FROM pokemons p JOIN equipo_pokemon ep ON p.id = ep.pokemon_id WHERE ep.equipo_id = " . $equipo_id;
        $result = $db->query($q);
        $pokemons = [];
        while ($row = $result->fetch_assoc()) {
            $pokemons[] = new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return $pokemons;
    }

    /**
     * Añade un Pokémon a un equipo.
     * @param int $equipo_id
     * @param int $pokemon_id
     */
    public static function addPokemonToEquipo($equipo_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "INSERT INTO equipo_pokemon (equipo_id, pokemon_id) VALUES (" . $equipo_id . ", " . $pokemon_id . ")";
        $db->query($q);
    }

    /**
     * Elimina un Pokémon de un equipo.
     * @param int $equipo_id
     * @param int $pokemon_id
     */
    public static function removePokemonFromEquipo($equipo_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "DELETE FROM equipo_pokemon WHERE equipo_id = " . $equipo_id . " AND pokemon_id = " . $pokemon_id;
        $db->query($q);
    }

    /**
     * Cuenta cuántos Pokémon hay en un equipo.
     * @param int $equipo_id
     * @return int
     */
    public static function countPokemonInEquipo($equipo_id) {
        $db = Connection::connect();
        $q = "SELECT COUNT(*) as total FROM equipo_pokemon WHERE equipo_id = " . $equipo_id;
        $result = $db->query($q);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /**
     * Verifica si un Pokémon ya existe en un equipo.
     * @param int $equipo_id
     * @param int $pokemon_id
     * @return bool
     */
    public static function existsInEquipo($equipo_id, $pokemon_id) {
        $db = Connection::connect();
        $q = "SELECT COUNT(*) as total FROM equipo_pokemon WHERE equipo_id = " . $equipo_id . " AND pokemon_id = " . $pokemon_id;
        $result = $db->query($q);
        $row = $result->fetch_assoc();
        return (int)$row['total'] > 0;
    }

    /**
     * Obtiene los Pokémon del sistema que no están en un equipo específico.
     * @param int $usuario_id
     * @param int $equipo_id
     * @return Pokemon[]
     */
    public static function getPokemonDisponiblesParaEquipo($usuario_id, $equipo_id) {
        $db = Connection::connect();
        //esta consulta saca los pokemon de la pokedex del usuario que no esten en el equipo
        $q = "SELECT p.* FROM pokemons p JOIN pokedex pd ON p.id = pd.pokemon_id WHERE pd.usuario_id = " . $usuario_id . " AND p.id NOT IN (SELECT pokemon_id FROM equipo_pokemon WHERE equipo_id = " . $equipo_id . ")";
        $result = $db->query($q);
        $pokemons = [];
        while ($row = $result->fetch_assoc()) {
            $pokemons[] = new Pokemon($row['id'], $row['nombre'], $row['tipo'], $row['foto'], $row['creador_id']);
        }
        return $pokemons;
    }
}
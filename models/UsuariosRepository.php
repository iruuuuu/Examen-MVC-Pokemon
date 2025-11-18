<?php 

require_once 'Usuarios.php';
class UsuariosRepository
{
    /**
     * Obtiene todos los usuarios de la base de datos.
     * @return Usuarios[]
     */
    public static function getAll()
    {
        $db = Connection::connect();
        $q = "SELECT * FROM usuarios";
        $result = $db->query($q);
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuarios($row['id'], $row['nombre'], $row['password'], $row['rol'], $row['avatar']);
        }
        return $usuarios;
    }

    /**
     * Obtiene un usuario por su ID.
     * @param int $id
     * @return Usuarios|null
     */
    public static function getById($id)
    {
        $db = Connection::connect();
        $q = "SELECT * FROM usuarios WHERE id=" . $id;
        $result = $db->query($q);
        if ($row = $result->fetch_assoc()) {
            return new Usuarios($row['id'], $row['nombre'], $row['password'], $row['rol'], $row['avatar']);
        }
        return null;
    }

    /**
     * Obtiene un usuario por su nombre.
     * @param string $nombre
     * @return Usuarios|null
     */
    public static function getByNombre($nombre)
    {
        $db = Connection::connect();
        $q = "SELECT * FROM usuarios WHERE nombre='" . $nombre . "'";
        $result = $db->query($q);
        if ($row = $result->fetch_assoc()) {
            return new Usuarios($row['id'], $row['nombre'], $row['password'], $row['rol'], $row['avatar']);
        }
        return null;
    }
    
    /**
     * Crea un nuevo usuario.
     * @param string $nombre
     * @param string $password
     * @param string $rol
     * @return int El ID del nuevo usuario.
     */
    public static function create($nombre, $password, $rol) {
        $db = Connection::connect();
        $hashed_password = md5($password);
        $q = "INSERT INTO usuarios (nombre, password, rol, avatar) VALUES ('" . $nombre . "', '" . $hashed_password . "', '" . $rol . "', 'avatar.jpg')";
        $db->query($q);
        return $db->insert_id;
    }

    /**
     * Elimina un usuario por su ID.
     * @param int $id
     */
    public static function delete($id) {
        $db = Connection::connect();
        $q = "DELETE FROM usuarios WHERE id = " . $id;
        $db->query($q);
    }

    /**
     * Encuentra al maestro con más Pokémon.
     * Asume que existe una tabla 'pokemons' con una columna 'id_usuario'.
     * @return array|null Un array asociativo con el nombre y el total, o null.
     */
    public static function maestroConMasPokemon(){
        $db = Connection::connect();
        $q = "SELECT u.nombre, COUNT(p.id) AS total_pokemon FROM usuarios u JOIN pokemons p ON u.id = p.creador_id GROUP BY u.nombre ORDER BY total_pokemon DESC LIMIT 1";
        $result = $db->query($q);
        return $result->fetch_assoc();
    }

    /**
     * Encuentra al maestro con más equipos.
     * Asume que existe una tabla 'equipos' con una columna 'id_usuario'.
     * @return array|null Un array asociativo con el nombre y el total, o null.
     */
    public static function maestroConMasEquipos(){
        $db = Connection::connect();
        $q = "SELECT u.nombre, COUNT(e.id) AS total_equipos FROM usuarios u JOIN equipos e ON u.id = e.usuario_id GROUP BY u.nombre ORDER BY total_equipos DESC LIMIT 1";
        $result = $db->query($q);
        return $result->fetch_assoc();
    }

    /**
     * Valida las credenciales de un usuario y devuelve el objeto si son correctas.
     * @param string $nombre
     * @param string $password
     * @return Usuarios|null
     */
    public static function login($nombre, $password)
    {
        $db = Connection::connect();
        $password_md5 = md5($password);
        $q = "SELECT * FROM usuarios WHERE nombre = '" . $nombre . "' AND password = '" . $password_md5 . "'";
        $result = $db->query($q);
        
        if ($userRow = $result->fetch_assoc()) {
            return new Usuarios($userRow['id'], $userRow['nombre'], $userRow['password'], $userRow['rol'], $userRow['avatar']);
        }
        return null;
    }

    /**
     * Actualiza la contraseña de un usuario.
     * @param int $id
     * @param string $new_password
     */
    public static function updatePassword($id, $new_password)
    {
        $db = Connection::connect();
        $hashed_password = md5($new_password);
        $q = "UPDATE usuarios SET password = '" . $hashed_password . "' WHERE id = " . $id;
        $db->query($q);
    }

    /**
     * Actualiza el avatar de un usuario.
     * @param int $id
     * @param string $avatar_filename
     */
    public static function setAvatar($id, $avatar_filename) {
        $db = Connection::connect();
        $q = "UPDATE usuarios SET avatar = '" . $avatar_filename . "' WHERE id = " . $id;
        $db->query($q);
    }
}
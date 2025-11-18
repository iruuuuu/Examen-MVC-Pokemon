<?php
class Usuarios
{
    private $id;
    private $nombre;
    private $password;
    private $rol;
    private $avatar;

    public function __construct($id, $nombre, $password, $rol, $avatar)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->password = $password;
        $this->rol = $rol;
        $this->avatar = $avatar;
    }


    public function getId()
    {
        return $this->id;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getRol()
    {
        return $this->rol;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function __toString()
    {
        return $this->nombre;
    }
}

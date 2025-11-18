<?php 

class Pokemon
{
    private $id;
    private $nombre;
    private $tipo;
    private $foto;
    private $creador_id;

    public function __construct($id, $nombre, $tipo, $foto, $creador_id)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->foto = $foto;
        $this->creador_id = $creador_id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getCreadorId()
    {
        return $this->creador_id;
    }
}
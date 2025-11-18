<?php 

class Equipos {
    private  $id;
    private  $usuario_id;
    private  $nombre;


    public function __construct( $id,  $usuario_id,  $nombre) {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->nombre = $nombre;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getUsuarioId() {
        return $this->usuario_id;
    }
    
    public function getNombre() {
        return $this->nombre;
    }





    public function __serialize(): array
    {
        // Serializa todas las propiedades del objeto automáticamente
        return get_object_vars($this);
    }
}

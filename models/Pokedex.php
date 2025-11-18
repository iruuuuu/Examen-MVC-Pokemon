<?php

class Pokedex {
    private  $usuario_id;
    private  $pokemon_id;


    public function __construct( $usuario_id,  $pokemon_id) {
        $this->usuario_id = $usuario_id;
        $this->pokemon_id = $pokemon_id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function getPokemonId() {
        return $this->pokemon_id;
    }

}
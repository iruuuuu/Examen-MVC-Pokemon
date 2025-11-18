<?php
class Equipo_Pokemon
{
    private $equipo_id;
    private $pokemon_id;


    public function __construct($equipo_id, $pokemon_id)
    {
        $this->equipo_id = $equipo_id;
        $this->pokemon_id = $pokemon_id;
    }


    public function getEquipoId()
    {
        return $this->equipo_id;
    }

    public function getPokemonId()
    {
        return $this->pokemon_id;
    }
}


<?php

class Info{

    /**
     * Información general de la ejecución.
     */
    private $general_info = array();

    /**
     * Información general de Airtable.
     */
    private $airtable_info = array();

    /**
     * Información general de Gw2.
     */
    private $gw2_info = array();

    /**
     * Información general de discord.
     */
    private $discord_info = array();
    
    /**
     * Construct
     */
    function __construct($info = null) {
        if(!is_null($info)){
            if(isset($info['general_info'])){
                $this->general_info = $info['general_info'];
            }
            if(isset($info['airtable_info'])){
                $this->airtable_info = $info['airtable_info']; 
            }
            if(isset($info['gw2_info'])){
                $this->gw2_info = $info['gw2_info'];
            }
            if(isset($info['discord_info'])){
                $this->discord_info = $info['discord_info'];
            }
        }
        $this->general_info['time'] = date('d-m-Y H:i:s');
    }

    /**
     * Crea una tabla con toda la información guardada.
     */
    public function print()
    {
        echo "<table style='margin-top:15px' class='table table-dark'>";
        echo "<tbody>";
        echo "<tr><td>TIME: " . $this->general_info['time'] . "</td><td></td></tr>";
        echo "<tr><td>DEBUG MODE: " . ($this->general_info['debug'] ? 'TRUE' : 'FALSE') . "</td><td></td></tr>";
        echo "<tr><td colspan='2' style='text-align:center; vertical-align:middle'><h3>AIRTABLE</h3></td></tr>";
        echo "<tr><td>Número de jugadores: " . $this->airtable_info['n_players'] . "<br/>Número de exjugadores: " . $this->airtable_info['n_explayers'] . "</td><td style='text-align:right; vertical-align:middle'>Jugadores creados:<br/>";
        foreach($this->airtable_info['insert'] AS $user){
            echo "$user<br/>";
        }
        echo "<br/>Jugadores eliminados:<br/>";
        foreach($this->airtable_info['deleted'] AS $user){
            echo $user['gw2'] . ' - ' . $user['discord'] . '<br/>';
        }
        echo "<br/>Jugadores modificados:<br/>";
        foreach($this->airtable_info['update'] AS $user){
            echo 'Modificados el usuario ' . $user['gw2'] . ' => El campo "' . $user['field'] . '" se modifica a "' . $user['value'] . '"<br/>';
        }
        echo "<br/>Jugadores que vuelven al clan:<br/>";
        foreach($this->airtable_info['return'] AS $user){
            echo "$user<br/>";
        }
        echo "</td></tr>";
        echo "<tr><td colspan='2' style='text-align:center; vertical-align:middle'><h3>GUILD WARS 2</h3></td></tr>";
        echo "<tr><td colspan='2'>Número de jugadores: " . $this->gw2_info['n_players'] . "</td></tr>";
        echo "<tr><td colspan='2' style='text-align:center; vertical-align:middle'><h3>DISCORD</h3></td></tr>";
        echo "<tr><td>Número de jugadores online: " . $this->discord_info['n_players'] . "</td><td style='text-align:right; vertical-align:middle'>Jugadores con el nombre mal:<br/>";
        foreach($this->discord_info['users_wrong_name'] AS $user){
            echo "$user<br/>";
        }
        echo "</td></tr>";
        echo "</tbody>";
        echo "</table>";
    }

    /**
     * __get
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property){
        if(property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
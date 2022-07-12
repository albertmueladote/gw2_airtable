<?php

class Gw2 extends main{
    
    /**
     * Clave del clan de Guild Wars 2
     */
    CONST GW2_GUILD_API_KEY = "D9BC6C8A-3288-E611-80D3-441EA14F1E40";
    
    /**
     * Clave del usuario de Guild Wars 2
     */
    CONST GW2_USER_API_KEY = "688DFABB-B97B-3E42-B07E-640FF87CDF2F9CF33BBE-1E58-4AD7-A54C-CA093F80667C";
    
    /**
     * Primera parte de la url de la api de Guild Wars 2
     */
    CONST GW2_API_URL_1 = "https://api.guildwars2.com/v2/guild/";
    
    /**
     * Segunda parte de la url de la api de Guild Wars 2
     */
    CONST GW2_API_URL_2 = "/members?access_token=";
    
    /**
     * Construct
     */
    function __construct() {
       
    }

    /**
     * Lista de los miembros del clan en el juego en tiempo real.
     * Devuelve un array de strings con los nombres de cuenta.
    **/
    public function curl()
    {        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this::GW2_API_URL_1 . $this::GW2_GUILD_API_KEY . $this::GW2_API_URL_2 . $this::GW2_USER_API_KEY);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $records = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $result = array();
        foreach($records AS $record){
           array_push($result, $record['name']);
        }
        return $result;
    }
}
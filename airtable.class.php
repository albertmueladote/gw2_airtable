<?php

class Airtable extends main{

    /**
     * Clave de Airtable
     */
    CONST AIRTABLE_API_KEY = "keybfRdy1SYVcEHsL";
    
    /**
     * Url de la api de Airtable
     */
    CONST AIRTABLE_API_URL = "https://api.airtable.com/v0/appeLZuDNy3nY0rzs/Asur";
    
    /**
     * Construct
     */
    function __construct() {
        
    }

    /**
     * Lista los miembros del clan en Airtable en tiempo real.
     * Devuelve el array:
     * [players]
     *      [0]
     *          [id]
     *          [gw2]
     *          [discord]
     *      [1]
     *          [id]
     *          [gw2]
     *          [discord]
     *      ...
     * [explayers]
     *      [0]
     *          [id]
     *          [gw2]
     *          [discord]
     *      [1]
     *          [id]
     *          [gw2]
     *          [discord]
     *      ...
     */
    public function curl($offset = null)
    {
        if(!is_null($offset)){
            $url = $this::AIRTABLE_API_URL . "?offset=$offset";
        }else{
            $url = $this::AIRTABLE_API_URL;
        }
        $headers = [
          'Content-Type: application/json',
          'Authorization: Bearer ' . $this::AIRTABLE_API_KEY,    
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $records = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $result = array();
        $result['players'] = array();
        $result['explayers'] = array();
        foreach($records['records'] AS $record){
            $aux = array();
            if(isset($record['fields']['Guild Wars 2'])){
                $aux['gw2'] = str_replace(array("\r", "\n"), '', $record['fields']['Guild Wars 2']);
            }else{
                $aux['gw2'] = '';
            }
            if(isset($record['fields']['Discord'])){
                $aux['discord'] = str_replace(array("\r", "\n"), '', $record['fields']['Discord']);
            }else{
                $aux['discord'] = '';
            }
            $aux['id'] = $record['id'];
            if(isset($record['fields']['Activado'])){
                array_push($result['players'], $aux);
            }else{
                array_push($result['explayers'], $aux);
            }
        }
        if(isset($records['offset'])){   
            $next_array = $this->curl($records['offset']);
            $result['players'] = array_merge($result['players'], $next_array['players']);
            $result['explayers'] = array_merge($result['explayers'], $next_array['explayers']);
        }

        return $result;
    }

    /**
     * Crea un registro con el campo "Guild Wars 2" envíado por parámetro y el campo "Activado" en true.
     */
    public function createRecord($gw2_name){
        if(strcmp($gw2_name, '') !== 0){
            $headers = [
              'Content-Type: application/json',
              'Authorization: Bearer ' . $this::AIRTABLE_API_KEY,    
            ];
            $params = json_encode(array(
                        "fields" => array(
                            "Guild Wars 2" => $gw2_name,
                            "Activado" => true,
                        ),
                        "typecast" => true
                    ));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this::AIRTABLE_API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            $records = json_decode(curl_exec($ch), true);
            curl_close($ch);
        }
    }

    /**
     * Actualiza un campo de un registro en Airtable con el id mandado por parámetro
     */
    public function updateRecord($record, $field, $value){
        if(strcmp($record['gw2'], '') !== 0 && strcmp($record['id'], '') !== 0){
           $headers = [
              'Content-Type: application/json',
              'Authorization: Bearer ' . $this::AIRTABLE_API_KEY,    
            ];
            if(is_bool($value)){
                if($value){
                    $value = 'true';
                }else{
                    $value = 'false';
                }
            }else{
                $value = '"' . $value . '"';
            }
            $params = "{\"records\":[{\"id\":\"" . $record['id'] . "\",\"fields\":{\"" . $field . "\": " . $value . "}}]}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , "PATCH");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $this::AIRTABLE_API_URL);
            $records = json_decode(curl_exec($ch), true);
            curl_close($ch);
        }
    }

    /**
     * Elimina un registro por su id
     */
    public function deleteRecord($record){
        if(strcmp($record['id'], '') !== 0 && strcmp($record['gw2'], '') !== 0){
            $headers = [
              'Content-Type: application/json',
              'Authorization: Bearer ' . $this::AIRTABLE_API_KEY,    
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this::AIRTABLE_API_URL . "?records%5B%5D=" . $record['id']);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST  , "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_exec($ch);
        }
    }
}
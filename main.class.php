<?php

class Main{

    /**
     * Determina si estamos en debug o en ejecución
     */
    CONST DEBUG = false;
    
    /**
     * Construct
     */
    function __construct() {
        $this->check();
    }

    /**
     * Compara la lista de jugadores del juego con la de Airtable.
     * Si encuentra jugadores en Airtable que no están en el clan, se eliminan de Airtable (desmarcándose la casilla "Activado").
     * Si encuentra jugadores en el clan pero están en la lista de exjugadores, los reincorpora (marcándose la casilla "Activado").
     * Si encuentra jugadores en el clan pero no están en Airtable, los crea.
     * Busca jugadores que estén en Discord y en Airtable comparando el campo "Guild Wars 2" de Airtable con el identificador entre paréntessis de Discord. 
     *  - Si en Airtable el jugador no tiene el campo "Discord", lo crea.
     *  - Si en Airtable el jugador tiene el campo "Discord" con un valor distinto al del nick en Discord, lo modifica.
     */
    public function check($airtable = null, $gw2 = null)
    {   
        $airtable = new Airtable();
        $airtable_list = $airtable->curl();
        $gw2 = new Gw2();
        $gw2_list = $gw2->curl();
        $discord = new Discord();
        $discord_list = $discord->curl();
        
        //A veces el servidor del juego falla y considera que el clan está vacío.
        if(sizeof($gw2_list) > 0)
        {
            //Si encuentra jugadores en Airtable pero no en el clan...
            $users_deleted = array();
            foreach($airtable_list['players'] AS $player){
                if(!in_array($player['gw2'], $gw2_list)){
                    if(!$this::DEBUG){
                        //... los manda a la lista de exjugadores.
                        $airtable->updateRecord($player, 'Activado', false);
                    }
                    array_push($users_deleted, $player);
                }
            }

            $airtable_list = $airtable->curl();

            //Si encuentra jugadores en el clan pero no en Airtable...
            $users_inserts = array();
            $users_return = array();
            foreach($gw2_list AS $user){
                $find_player = false;
                foreach($airtable_list['players'] AS $player){
                    if(strcmp($user, $player['gw2']) === 0){
                        $find_player = true;
                    }
                }
                //... lo busca en la lista de exjugadores...
                if(!$find_player){
                    $find_explayer = false;
                    foreach($airtable_list['explayers'] AS $player){
                        if(strcmp($user, $player['gw2']) === 0){
                            $find_explayer = true;
                            $player_to_update_or_create = $player;
                        }
                    }
                    if($find_explayer){
                        //... si lo encuentra en la lista de exjugadores, lo reincorpora...
                        if(!$this::DEBUG){
                            $airtable->updateRecord($player_to_update_or_create, 'Activado', true);
                        }
                        array_push($users_return, $user);
                    }else{
                        //... si no, lo crea.
                        if(!$this::DEBUG){
                            $airtable->createRecord($user);
                        }
                        array_push($users_inserts, $user);
                    }
                }
            }

            $airtable_list = $airtable->curl();

            //Compara los jugadores de Airtable con los de discord, buscando igualdades entre el campo "Guild Wars 2" de Airtable con el identificador entre paréntesis de discord. Si encuentra al usuario en ambos lados...
            $users_updated = array();
            foreach($airtable_list['players'] AS $player){    
                $find = false;
                foreach($discord_list['users'] AS $user){
                    if(strcmp($user['gw2'], explode('.', $player['gw2'])[0]) === 0){
                        if(strcmp($player['discord'], '') === 0 || strcmp($player['discord'], $user['discord']) !== 0){
                            //... si el campo "Discord" en Airtable está vacío o si el campo "Discord" en Airtable no está vacío pero es distinto actualiza el campo.
                            if(!$this::DEBUG){
                                $airtable->updateRecord($player, 'Discord', $user['discord']);
                            }
                            $player['field'] = 'Discord';
                            $player['value'] = $user['discord'];
                            array_push($users_updated, $player);
                        }
                    }
                }
            }
        }
        //Inicializamos la clase que crea la tabla con toda la información requerida.
        $info = new Info(array(
                        'general_info' => array('debug' => $this::DEBUG),
                        'airtable_info' => array('n_players' => sizeof($airtable_list['players']), 'n_explayers' => sizeof($airtable_list['explayers']), 'insert' => $users_inserts, 'deleted' => $users_deleted, 'update' => $users_updated, 'return' => $users_return), 
                        'gw2_info' => array('n_players' => sizeof($gw2_list)),
                        'discord_info' => array('n_players' => sizeof($discord_list['users']), 'users_wrong_name' => $discord_list['users_wrong_name'])
                    ));

        $info->print();
    }
}
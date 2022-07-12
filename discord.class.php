<?php

class Discord extends main{
    
    /**
     * Construct
     */
    function __construct() {

    }

    /**
     * Lista de los miembros del discord conectados. Filtra por aquellos que ya tienen el nombre con la nomenclatura "nombre (identificador de Gw2)".
     * Devuelve dos arrays, uno con los usuarios con el nick bien puesto y otro con los usuarios con el nick mal puesto.
    **/
    public function curl()
    {
        $url = 'https://discord.com/api/guilds/434762463498731521/widget.json';
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array('Authorization: Bearer Mjk2MDEzNzQ4NDc5NTkwNDAw.GoGG2Z.syfDT-ls-VP6Op_EqR5JPJK6STRT1ixAiZCFMc'),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $result = array();
        $result['users'] = array();
        $result['users_wrong_name'] = array();
        foreach($response['members'] AS $member){
            $aux = array();
            if(strpos($member['username'], ' (') !== false && strpos($member['username'], ')') !== false){
                $array = explode(' (', $member['username']);
                $aux['discord'] = $array[0];
                $array = explode(')', $array[1]);
                $aux['gw2'] = $array[0];
                array_push($result['users'], $aux);
            }else{
                if(!in_array($member['username'], $this->getGests())){
                    array_push($result['users_wrong_name'], $member['username']);
                }
            }
        }
        return $result;
    }

    /**
     * Estos usuarios son ignorados a la hora de listar a los jugadores con el nick bien puesto o mal puesto. Aquí debemos tener todos los usuarios que no tienen el rango "Miembro".
     */
    public function getGests()
    {
    return array('!     💎☯۝𝗚𝗢𝗗⚔𝘿.𝙎💎', 'Bronky', 'Dark Carnage', 'Jose', 'peepo', 'Thorquios', 'Taimi', 'Glenna', 'Ogden', 'AitorMC', '𝓛𝓾-𝓵𝓲𝓷', 'Gilitis.5621 | Valderoft', 'Pablomt', 'Arkael', 'Garrapato', 'thor', 'Taquito', 'Jumiyah', 'Cloud Kenshiro', 'Ismene', 'danytt', 'Mike', 'Arslan', 'Surah', 'Nekotina');
    }
}
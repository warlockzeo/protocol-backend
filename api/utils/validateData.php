<?php
    function cleaning ($data){
        $result = str_replace(';','',strip_tags(trim($data)));
        return $result;
    }

    function cleaningString ($data){
        $charset = mb_detect_encoding($data, 'auto');
        $charsetToUse = $charset === "UTF-8" ? "" : "ISO-8859-1";
        $htmlentitiesconverted = htmlentities($data, ENT_NOQUOTES, $charsetToUse, false);
        $htmlentitiesconverted = html_entity_decode($htmlentitiesconverted, ENT_DISALLOWED, 'UTF-8');
        
        $result = strip_tags(trim($htmlentitiesconverted));

        return $result;
    }

    function validateData ($data) {
        if(is_array($data) || is_object($data)) {
            $ret = [];
            foreach($data as $k => $v){
                $ret[$k] = validateData($v);
            }
            return $ret;

        } elseif(is_numeric($data)) {
            return $data;
        } else {
            //echo cleaningString($data);
            return cleaningString($data);
        }
    }

    function validade($data){
        $data2 = explode('-',$data);
        $ano = $data2[0];
        $mes = $data2[1];
        $dia = $data2[2];
        #setando a primeira data  10/01/2008 
        $dia1= mktime(0,0,0,$mes,$dia,$ano);
        #setando segunda data 10/02/2008
        $dia2 = mktime();
        #armazenando o valor da subtracao das datas
        $d3 = ($dia1-$dia2);
        #usando o roud para arrendondar os valores
        #converter o tempo em dias
        $dias = round($d3/60/60/24);
        #exibindo  dias | repudizira na tela 31
        return $dias; 
   }
?>
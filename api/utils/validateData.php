<?php
    function cleaning ($data){
        $result = str_replace(';','',strip_tags(trim($data)));
        return $result;
    }

    function validateData ($data) {
        if(is_array($data) || is_object($data)) {
            foreach($data as $d){
                validateData($d);
            }
        } elseif(is_numeric($data)) {
            return $data;
        } else {
            return cleaning($data);
        }
    }
?>
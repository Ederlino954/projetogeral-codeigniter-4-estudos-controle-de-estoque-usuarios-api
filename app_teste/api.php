<?php
    function api($endpoint, $post_vars){ // pegando as variáveis do index 

        $curl = curl_init();

        // tratar o array post_vars = array para json
        $post = json_encode($post_vars, 128); // transformando em JSON

        curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $post, // JSON usado aqui
        CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
            "Content-Type: application/json",                        
            "cache-control: no-cache"
        ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $erro = array(
                'STATUS' => 'ERROR',
                'MESSAGE' => $err
            );
            return $erro;
        } else {
            return json_decode($response, true); // Transformando em array assossiativo 
        }
    }

?>
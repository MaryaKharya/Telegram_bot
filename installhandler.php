<?php
    function regHandler($cert, $token, $murl) 
    {
        $url = "https://api.telegram.org/bot" . $token . "/setWebhook";
        $ch = curl_init();
        $optArray = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_SAFE_UPLOAD => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => array('url' => $murl, 'certificate' => '@' . realpath($cert))
        );
        curl_setopt_array($ch, $optArray);
        
        $result = curl_exec($ch);
        echo "<pre>";
            print_r($result);
        echo "</pre>";
        curl_close($ch);
    }

    $token = '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE';
    $path = '/ssl/YOURPUBLIC.pem';
    $handlerurl = 'https://blooming-oasis-19797.herokuapp.com/index.php'; // ИЗМЕНИТЕ ССЫЛКУ
    
    regHandler($path, $token, $handlerurl);
?>
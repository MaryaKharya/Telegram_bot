<?php

use Imgix\UrlBuilder;

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$builder = new UrlBuilder("demos.imgix.net");
$params = array("w" => 100, "h" => 100);

if (empty($data['message']['chat']['id'])) {
    exit();
}
 
define('TOKEN', '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE');

// Функция вызова методов API.
function sendTelegram($method, $response)
{
    $ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);  
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $res = curl_exec($ch);
    curl_close($ch);
 
    return $res;
}
 
// Прислали фото.
if (!empty($data['message']['photo'])) {
    $photo = array_pop($data['message']['photo']);
    $res = sendTelegram(
        'getFile', 
        array(
            'file_id' => $photo['file_id']
        )
    );
    
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        $dest = __DIR__ . '/' . basename($src);
		$builder = new UrlBuilder("blooming-oasis-19797.imgix.net");
        $params = array("sepia" = 70);
        $la = $builder->createURL($src, $params);
            sendTelegram(
                'sendMessage', 
                array(
                    'chat_id' => $data['message']['chat']['id'],
                    'text' => $la . "&s=a7831233dc1c265108c05628dd1d4144"
                )
            );
    }
    
    exit(); 
}
 
// Ответ на текстовые сообщения.
if (!empty($data['message']['text'])) {
    $text = $data['message']['text'];
 
    if ($text == 'привет') {
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => 'Хай!'
            )
        );
 
        exit(); 
    } 
} 
    // Отправка фото.
    if ($text == 'фото') {
        sendTelegram(
            'sendPhoto', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'photo' => 'https://blooming-oasis-19797.imgix.net/https%3A%2F%2Fsun9-3.userapi.com%2Fc9706%2Fu81896685%2F-6%2Fy_5ac9e6f4.jpg?sepia=70&s=e8fcc1c3d86901580fc0db57717664da'
            )
        );
        
        exit(); 
    }

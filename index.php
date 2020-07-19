<?php
header('Content-Type: text/html;charset=utf-8');

require 'vendor/autoload.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

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
$inline_button1 = array("text"=>"Справочник бота 📚","callback_data"=>'/help');
$inline_button2 = array("text"=>"Официальный сайт","url"=>"http://m-e-c.ru");
$inline_keyboard = [[$inline_button1, $inline_button2]];
$keyboard=array("inline_keyboard"=>$inline_keyboard);
$replyMarkup = json_encode($keyboard);
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
        $key = 'e592f995c2f3ae18d817f61aff1764b2';
        $url = 'http://api.convertio.co/convert';
        $da = ["apikey" => "e592f995c2f3ae18d817f61aff1764b2", "input" => "url", "file" => $src, "outputformat" => "png",];
        $fields_string = json_encode($da);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        $u = json_decode($result, true);

        $s = 'https://api.convertio.co/convert/' . $u['data']['id'] . '/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $s);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);

        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => $s
            )
        );
    }
    exit(); 
}
 

if (!empty($data['message']['document'])) {
	$res = sendTelegram(
		'getFile', 
		array(
			'file_id' => $data['message']['document']['file_id']
		)
	);
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        $key = 'e592f995c2f3ae18d817f61aff1764b2';
        $url = 'http://api.convertio.co/convert';
        $da = ["apikey" => "e592f995c2f3ae18d817f61aff1764b2", "input" => "url", "file" => $src, "outputformat" => "pdf",];
        $fields_string = json_encode($da);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        $u = json_decode($result, true);

        $s = 'https://api.convertio.co/convert/' . $u['data']['id'] . '/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $s);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);

        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => $s
            )
        );
    }
    exit(); 
}

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

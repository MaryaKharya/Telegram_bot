<?php


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
		$ff = $src;
		$out = 'png';
		$ku = array(
		   'apikey' => 'e592f995c2f3ae18d817f61aff1764b2',
		   'input' => 'url',
		   'file' => $ff,
		   'outputformat' => $out
		   );
		   

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'http://api.convertio.co/convert');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $ku);

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);

$result = json_decode($result, true);

foreach($result as $item) {
    $ka = $item['id'];
}
$lo = 'https://api.convertio.co/convert/' . $ka . '/status';
$lo = json_decode($lo, true);
foreach($lo as $item) {
    $sa = $item['url'];
}
            sendTelegram(
                'sendMessage', 
                array(
                    'chat_id' => $data['message']['chat']['id'],
                    'text' => $ka
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

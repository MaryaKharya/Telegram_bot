<?php
header('Content-Type: text/html;charset=utf-8');

const DB_DSN = "mysql:host=us-cdbr-east-02.cleardb.com;dbname=heroku_e564b85ef073325";
const DB_USER = "b18cf3a57611ff";
const DB_PASSWORD = "db9c4d56";

require 'vendor/autoload.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

function databaseConnection(): PDO
{
    static $connection = null;
    if ($connection === null)
    {
        $connection = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
        $connection->query('set names utf8');
    }
    return $connection;
}

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

$text = $data['message']['text'];
if ($text == '/start')
{
	    $connection = databaseConnection();
		$sql = "INSERT INTO users (name, chat_id) VALUES ('{$data['message']['from']['first_name']}', '{$data['message']['chat']['id']}')";
        $connection->query($sql);
        sendTelegram('sendMessage', array('chat_id' => $data['message']['chat']['id'],
                                          'text' => 'Добро пожаловать, ' $data['message']['from']['first_name'] '. Я сконверирую все, что захочешь. 
				                                     Для этого выбири формат, который хочешь получить в результате конвертирования.
                                                     Для фото:<br> jpg<br> jpeg<br> png<br> psd<br> gif<br> bmp<br>
						                             Для документов:<br> doc<br> docx<br> pdf<br> epub<br> fb2<br> mobi<br>'
                                         )
                    );
}

if ($text == 'jpg' or 'jpeg' or 'png' or 'psd' or 'gif' or 'bmp' or 'doc' or 'docx' or 'pdf' or 'epub' or 'fb2' or 'mobi')
{
	    $connection = databaseConnection();
        $id = "SELECT id FROM users WHERE chat_id = {$data['message']['chat']['id']}";
        $result = $connection->query($id)->fetch();
		$sql = "INSERT INTO formats (format, user_id) VALUES ('{$text}', '{$result['id']}')";
        $connection->query($sql);
        sendTelegram('sendMessage', array('chat_id' => $data['message']['chat']['id'],
                                          'text' => $data['message']['from']['first_name'] ', скинь фотографию или документ, который хотите конвертировать'
                                         )
                    );
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
        // отправка post запроса для получения id
		$connection = databaseConnection();
        $id = "SELECT id FROM users WHERE chat_id = {$data['message']['chat']['id']}";
        $result = $connection->query($id)->fetch();
        $format = "SELECT format FROM formats WHERE user_id = {$result['id']} ORDER BY id DESC LIMIT 1";
        $forma = $connection->query($format)->fetch();
        $key = 'e592f995c2f3ae18d817f61aff1764b2';
        $url = 'http://api.convertio.co/convert';
        $da = ["apikey" => "e592f995c2f3ae18d817f61aff1764b2", "input" => "url", "file" => $src, "outputformat" => $forma['format']];
        $fields_string = json_encode($da);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);
        $u = json_decode($result, true);

        //Добавление id в базу данных.

        $insert_id = $connection->lastInsertId();
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$u['data']['id']}', '{$insert_id}')";
        if ($connection->query($sql)) { 
        //клавиатура
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => 'результат придет в виде файла, ок?'
            )
        );
    }
    }
    exit(); 
}
 
//отправление файла
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

        //отправка post запроса
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

        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => 'выбири'
            )
        );
    }
}

    if ($text == 'ок') {
        //получение id из базы данных
		$connection = databaseConnection();
        $id = "SELECT id FROM users WHERE chat_id = {$data['message']['chat']['id']}";
        $result = $connection->query($id)->fetch();
        $convert = "SELECT con_id FROM conid WHERE user_chat_id = {$result['id']} ORDER BY id DESC LIMIT 1";
        $con = $connection->query($convert)->fetch();

        //get запрос на ссылку с конвертированным файлом
        $s = 'https://api.convertio.co/convert/' . $con['con_id'] . '/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $s);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);
        $ugu = json_decode($out, true);
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => $out
            )
        );
        exit(); 
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

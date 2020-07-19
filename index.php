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

        //Добавление id в базу данных.
        $connection = databaseConnection();
        $sql = "INSERT INTO users (name, chat_id) VALUES ('{$data['message']['from']['first_name']}', '{$data['message']['chat']['id']}')";
        $connection->query($sql);
        $insert_id = $connection->lastInsertId();
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$u['data']['id']}', '{$insert_id}')";
        if ($connection->query($sql)) {

        //клавиатура
        $button1 = array("text"=>"файл","callback_data"=>'file');
        $button2 = array("text"=>"фото","callback_data"=>'photo');
        $inline_keyboard = [[$button1,$button2]];
        $keyboard=array("inline_keyboard"=>$inline_keyboard);
        $replyMarkup = json_encode($keyboard); 
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $data['message']['chat']['id'],
                'text' => 'в каком виде мне отправить фото?',
                'reply_markup' => $replyMarkup
            )
        );
    }
    }
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
        //Получение результата (пока ссылку)
$callback_query = $output['callback_query'];
$datata = $callback_query['data'];
$chat_id_in = $callback_query['from']['id'];
    if ($datata == 'file')
	{
		//получение id из базы данных
        $connection = databaseConnection();
        $id = "SELECT con_id FROM conid ORDER BY id DESC LIMIT 1";
        $result = $connection->query($id)->fetch();

        //get запрос на ссылку с конвертированным файлом
        $s = 'https://api.convertio.co/convert/' . $result . '/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $s);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);
        $out = file_get_contents($s);
        $ugu = json_decode($out, true);
        $umu = rawurldecode($ugu['data']['output']['url']);
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $chat_id_in,
                'text' => $umu
            )
        );
        exit(); 
	}


    if ($datata == 'photo') 
    {
        //получение id из базы данных
        $connection = databaseConnection();
        $id = "SELECT con_id FROM conid ORDER BY id DESC LIMIT 1";
        $result = $connection->query($id)->fetch();

        //get запрос на ссылку с конвертированным файлом
        $s = 'https://api.convertio.co/convert/' . $result . '/status';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $s);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
        $out = curl_exec($curl);
        curl_close($curl);
        $out = file_get_contents($s);
        $ugu = json_decode($out, true);
        $umu = rawurldecode($ugu['data']['output']['url']);
        sendTelegram(
            'sendMessage', 
            array(
                'chat_id' => $chat_id_in,
                'text' => $umu
            )
        );
        exit(); 
    } 
    }
}



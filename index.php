<?php
header('Content-Type: image/png');

require 'vendor/autoload.php';

const DB_DSN = "mysql:host=us-cdbr-east-02.cleardb.com;dbname=heroku_e564b85ef073325";
const DB_USER = "b18cf3a57611ff";
const DB_PASSWORD = "db9c4d56";

$data = file_get_contents('php://input');
$data = json_decode($data, true);
$text = $data['message']['text'];
$chat_id = $data['message']['chat']['id'];

define('TOKEN', '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE');

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

  $callback_query = $data['callback_query'];
  $manga_id = $callback_query['data'];
  $chat_id_in = $callback_query['message']['chat']['id'];
 

if ($text == '/start')
{
    $connection = databaseConnection();
    $sql = "INSERT INTO users (name, chat_id) VALUES ('{$data['message']['from']['first_name']}', '{$chat_id}')";
    $connection->query($sql);
		$inline_button1 = array("text"=>"jpg","callback_data" => "/jpg");
		$inline_button2 = array("text"=>"jpeg","callback_data" => "/jpeg");
		$inline_button3 = array("text"=>"png","callback_data" => "/png");
		$inline_button4 = array("text"=>"psd","callback_data" => "/psd");
		$inline_button5 = array("text"=>"gif","callback_data" => "/gif");
		$inline_button6 = array("text"=>"bmp","callback_data" => "/bmp");
		$inline_button7 = array("text"=>"doc","callback_data" => "/doc");
		$inline_button8 = array("text"=>"docx","callback_data" => "/docx");
		$inline_button9 = array("text"=>"pdf","callback_data" => "/pdf");
		$inline_button10 = array("text"=>"epub","callback_data" => "/epub");
		$inline_button11 = array("text"=>"fb2","callback_data" => "/fb2");
		$inline_button12 = array("text"=>"mobi","callback_data" => "/mobi");
        $inline_keyboard = [[$inline_button1, $inline_button2, $inline_button3, $inline_button4, $inline_button5, $inline_button6, $inline_button7, $inline_button8, $inline_button9, $inline_button10, $inline_button11, $inline_button12]];
        $keyboard=array("inline_keyboard"=>$inline_keyboard);
        $replyMarkup = json_encode($keyboard);
		    sendTelegram('sendMessage', array('chat_id' => $chat_id, 'text' => 'Добро пожаловать! Я сконверирую все, что захочешь. Для этого выбири формат, который хочешь получить в результате конвертирования.
Для фото:
jpg                      jpeg
png                      psd
gif                      bmp
Для документов:
doc                      docx
pdf                      epub
fb2                      mobi', 'reply_markup' => $replyMarkup));
    exit();
}


if ($manga_id == 'jpg' || $manga_id == 'jpeg' || $manga_id == 'png' || $manga_id == 'psd' || $manga_id == 'gif' || $manga_id == 'bmp' || $manga_id == 'doc' || $manga_id == 'docx' || $manga_id == 'pdf' || $manga_id == 'epub' || $manga_id == 'fb2' || $manga_id == 'mobi')
{
    $connection = databaseConnection();
    $id = "SELECT id FROM users WHERE chat_id = {$chat_id}";
    $result = $connection->query($id)->fetch();
    $sql = "INSERT INTO formats (format, user_id) VALUES ('{$manga_id}', '{$result['id']}')";
    $connection->query($sql);
    sendTelegram('sendMessage', array('chat_id' => $chat_id_in, 'text' => 'Cкинь фотографию или документ, который хочешь конвертировать'));
    exit();
}

// Прислали фото.
if (isset($data['message']['photo']))
{
    $photo = array_pop($data['message']['photo']);
    $res = sendTelegram('getFile', array('file_id' => $photo['file_id']));
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        // отправка post запроса для получения id
        $key = 'e592f995c2f3ae18d817f61aff1764b2';
        $url = 'http://api.convertio.co/convert';
        $connection = databaseConnection();
        $id = "SELECT id FROM users WHERE chat_id = {$chat_id}";
        $result = $connection->query($id)->fetch();
        $format = "SELECT format FROM formats WHERE user_id = {$result['id']} ORDER BY id DESC LIMIT 1";
        $forma = $connection->query($format)->fetch();
        $da = ["apikey" => "e592f995c2f3ae18d817f61aff1764b2", "input" => "url", "file" => $src, "outputformat" => $forma['format']];
        $fields_string = json_encode($da);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result_id = curl_exec($ch);
        $value = json_decode($result_id, true);

        //Добавление id в базу данных.
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$value['data']['id']}', '{$result['id']}')";
        if ($connection->query($sql)) { 
            //клавиатура
            sendTelegram('sendMessage', array('chat_id' => $chat_id, 'text' => 'результат придет в виде ссылки, ок?'));
        }
    }
    exit(); 
}

//отправление файла
if (!empty($data['message']['document'])) {
    $res = sendTelegram('getFile', array('file_id' => $data['message']['document']['file_id']));
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        // отправка post запроса для получения id
        $key = 'e592f995c2f3ae18d817f61aff1764b2';
        $url = 'http://api.convertio.co/convert';
        $connection = databaseConnection();
        $id = "SELECT id FROM users WHERE chat_id = {$chat_id}";
        $result = $connection->query($id)->fetch();
        $format = "SELECT format FROM formats WHERE user_id = {$result['id']} ORDER BY id DESC LIMIT 1";
        $forma = $connection->query($format)->fetch();
        $da = ["apikey" => "e592f995c2f3ae18d817f61aff1764b2", "input" => "url", "file" => $src, "outputformat" => $forma['format']];
        $fields_string = json_encode($da);

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        $result_id = curl_exec($ch);
        $value = json_decode($result_id, true);

        //Добавление id в базу данных.
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$value['data']['id']}', '{$result['id']}')";
        $connection->query($sql);
    //get запрос на ссылку с конвертированным файлом
	$url = 'https://api.convertio.co/convert/' . $value['data']['id'] . '/dl';
    $out = file_get_contents($url);
    $con_json = json_decode($out, true);
		sendTelegram('sendDocument', array('chat_id' => $chat_id, 'document' => 'https://sun9-15.userapi.com/vy0zsJaIsMMTh7nwTkkDBA1VpRzfL7ehwPRm_A/mBXzn2D0j5Q.jpg'));
    }
    exit(); 
}


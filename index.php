<?php
header('Content-Type: image/png');

include 'database.php';

require 'vendor/autoload.php';

$data = file_get_contents('php://input');
$data = json_decode($data, true);

define('TOKEN', '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE');

function sendMessageWithInline($chat_id, $message, $replyMarkup) {
    file_get_contents('https://api.telegram.org/bot' . TOKEN . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message) . '&reply_markup=' . $replyMarkup);
  }


if (empty($data['message']['chat']['id'])) {
    exit();
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

if (!empty($data['message']['text'])) {
    $text = $data['message']['text'];
    if ($text == '/start')
    {
        saveData($data);
        sendTelegram('sendMessage', array('chat_id' => $data['message']['chat']['id'],
                                          'text' => 'Добро пожаловать! Я сконверирую все, что захочешь. Для этого выбири формат, который хочешь получить в результате конвертирования.
Для фото:
jpg                    jpeg
png                    psd
gif                    bmp
Для документов:
doc                    docx
pdf                    epub
fb2                     mobi'
                                         )
                    );
        exit();
    }
}

if (!empty($data['message']['text'])) {
    $text = $data['message']['text'];
    if ($text == 'jpg' || $text == 'jpeg' || $text == 'png' || $text == 'psd' || $text == 'gif' || $text == 'bmp' || $text == 'doc' || $text == 'docx' || $text == 'pdf' || $text == 'epub' || $text == 'fb2' || $text == 'mobi')
    {
        getUser($data);
        $sql = "INSERT INTO formats (format, user_id) VALUES ('{$text}', '{$result['id']}')";
        $connection->query($sql);
        sendTelegram('sendMessage', array('chat_id' => $data['message']['chat']['id'],
                                          'text' => 'Cкинь фотографию или документ, который хочешь конвертировать'
                                         )
                    );
        exit();
    }
}

// Прислали фото.
if (!empty($data['message']['photo'])) {
    $photo = array_pop($data['message']['photo']);
    $res = sendTelegram('getFile', array('file_id' => $photo['file_id']
                                   )
           );
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        // отправка post запроса для получения id
        getUser($data);
        $format = "SELECT format FROM formats WHERE user_id = {$resul['id']} ORDER BY id DESC LIMIT 1";
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
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$u['data']['id']}', '{$resul['id']}')";
        if ($connection->query($sql)) { 
            //клавиатура
            sendTelegram('sendMessage', array('chat_id' => $data['message']['chat']['id'],
                                              'text' => 'результат придет в виде ссылки, ок?'
                                        )
                        );
        }
    }
    exit(); 
}
 
//отправление файла
if (!empty($data['message']['document'])) {
    $res = sendTelegram('getFile', array('file_id' => $data['message']['document']['file_id']
                                   )
                       );
    $res = json_decode($res, true);
    if ($res['ok']) {
        $src = 'https://api.telegram.org/file/bot' . TOKEN . '/' . $res['result']['file_path'];
        // отправка post запроса для получения id
        getUser($data);
        $format = "SELECT format FROM formats WHERE user_id = {$resul['id']} ORDER BY id DESC LIMIT 1";
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
		
		$inline_button1 = array("text"=>"файл","callback_data" => "/ok");
		$inline_button2 = array("text"=>"отмена","callback_data" => "/no");
        $inline_keyboard = [[$inline_button1, $inline_button2]];
        $keyboard=array("inline_keyboard"=>$inline_keyboard);
        $replyMarkup = json_encode($keyboard);

        //Добавление id в базу данных.
        $sql = "INSERT INTO conid (con_id, user_chat_id) VALUES ('{$u['data']['id']}', '{$resul['id']}')";
        if ($connection->query($sql)) { 
            //клавиатура

            sendMessageWithInline($data['message']['chat']['id'], 'результат придет в виде ссылки, ок?', $replyMarkup
                        );
        }
    }
    exit(); 
}

    if ($data['callback_query']['data'] == '/ok') {
        //получение id из базы данных
        getUser($data);
        $convert = "SELECT con_id FROM conid WHERE user_chat_id = {$result['id']} ORDER BY id DESC LIMIT 1";
        $con = $connection->query($convert)->fetch();
        //get запрос на ссылку с конвертированным файлом
			$s = 'https://api.convertio.co/convert/' . $con['con_id'] . '/dl';
        $out = file_get_contents($s);
        $ugu = json_decode($out, true);
		if (isset($ugu['data']['content']))
		{
		    sendTelegram('sendMessage', array('chat_id' => $data['callback_query']['message']['chat']['id'],
                                            'text' => 'воть'
                                    )
                    );
		}
		else
		{
			sleep(10);
		}

		
        
        exit(); 
    }

<?php
// определяем кодировку
header('Content-type: text/html; charset=utf-8');
// Создаем объект бота
$bot = new Bot();
// Обрабатываем пришедшие данные
$bot->init('php://input');

/**
 * Class Bot
 */
class Bot
{
    // <bot_token> - созданный токен для нашего бота от @BotFather
    private $botToken = "794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE";
    // адрес для запросов к API Telegram
    private $apiUrl = "https://api.telegram.org/bot";

    public function init($data_php)
    {
        // создаем массив из пришедших данных от API Telegram
        $data = $this->getData($data_php);
        // id чата отправителя
        $chat_id = $data['message']['chat']['id'];
        // включаем логирование будет лежать рядом с этим файлом
        // $this->setFileLog($data, "log.txt");

        // проверяем если пришло сообщение
        if (array_key_exists('message', $data)) {
        	//tckb пришла команда /start
            if ($data['message']['text'] == "/start") {
                $this->sendMessage($chat_id, "Приветствую! Загрузите картинку.");
            } elseif (array_key_exists('photo', $data['message'])) {
            	// если пришла картинка то сохраняем ее у себя
                $text = $this->getPhoto($data['message']['photo'])
                    ? "Спасибо! Можете еще загрузить мне понравилось их сохранять."
                    : "Что-то пошло не так, попробуйте еще раз";
                // отправляем сообщение о результате   
                $this->sendMessage($chat_id, $text);
            } else {
            	// если пришло что-то другое
                $this->sendMessage($chat_id, "Не понимаю команду! Просто загрузите картинку.");
            }
        }
    }

    // функция отправки текстового сообщения
    private function sendMessage($chat_id, $text)
    {
        $this->requestToTelegram([
            'chat_id' => $chat_id,
            'text' => $text,
        ], "sendMessage");
    }

	public function getPhoto($data){
		$out = $this->request('getFile', $data);        
        return $out;
	}  
	
	public function savePhoto($url,$puth){
		$ch = curl_init('https://api.telegram.org/file/bot' . $this->token .  '/' . $url);
		$fp = fopen($puth, 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
	
    public  function request($method, $data = array()) {
        $curl = curl_init(); 
          
        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token .  '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
          
        $out = json_decode(curl_exec($curl), true); 
          
        curl_close($curl); 
          
        return $out; 
    }
}
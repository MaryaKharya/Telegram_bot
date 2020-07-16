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

    // общая функция загрузки картинки
    private function getPhoto($data)
    {
    	// берем последнюю картинку в массиве
        $file_id = $data[count($data) - 1]['file_id'];
        // получаем file_path
        $file_path = $this->getPhotoPath($file_id);
        // возвращаем результат загрузки фото
        return $this->copyPhoto($file_path);
    }

    // функция получения метонахождения файла
    private function getPhotoPath($file_id) {
    	// получаем объект File
        $array = json_decode($this->requestToTelegram(['file_id' => $file_id], "getFile"), TRUE);
        // возвращаем file_path
        return  $array['result']['file_path'];
    }

    // копируем фото к себе
    private function copyPhoto($file_path) {
    	// ссылка на файл в телеграме
        $file_from_tgrm = "https://api.telegram.org/file/bot".$this->botToken."/".$file_path;
        // достаем расширение файла
        $ext =  end(explode(".", $file_path));
        // назначаем свое имя здесь время_в_секундах.расширение_файла
        $name_our_new_file = time().".".$ext;
        return copy($file_from_tgrm, "img/".$name_our_new_file);
    }

    // функция логирования в файл
    private function setFileLog($data, $file)
    {
        $fh = fopen($file, 'a') or die('can\'t open file');
        ((is_array($data)) || (is_object($data))) ? fwrite($fh, print_r($data, TRUE) . "\n") : fwrite($fh, $data . "\n");
        fclose($fh);
    }

    /**
     * Парсим что приходит преобразуем в массив
     * @param $data
     * @return mixed
     */
    private function getData($data)
    {
        return json_decode(file_get_contents($data), TRUE);
    }

    /** Отправляем запрос в Телеграмм
     * @param $data
     * @param string $type
     * @return mixed
     */
    private function requestToTelegram($data, $type)
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $this->botToken . '/' . $type);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }
        return $result;
    }
}
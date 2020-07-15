<?php
header('Content-Type: text/html; charset=utf-8');
 
require_once("vendor/autoload.php");

$access_token = '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE';
$bot = new \TelegramBot\Api\Client($token);
 
// обязательное. Запуск бота
$bot->command('start', function ($message) use ($bot) {
    $answer = 'Добро пожаловать!';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// помощ
$bot->command('help', function ($message) use ($bot) {
    $answer = 'Команды:
/help - помощ';
    $bot->sendMessage($message->getChat()->getId(), $answer);
});

// запускаем обработку
$bot->run();
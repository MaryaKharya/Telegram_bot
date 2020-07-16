 <?php

header('Content-Type: text/html; charset=utf-8');
// подключаемся к API
require_once("vendor/autoload.php");
// создаем переменную бота
$token = "794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE";
$bot = new \TelegramBot\Api\Client($token);
// если Телеграм-бот не зарегистрирован - регистрируем
// обязательное. Запуск бота
$bot->on(function($Update) use ($bot) {
    $message = $Update->getMessage();
    $mtext = $message->getText();
    $cid = $message->getChat()->getId();
    $updateId = $Update->getUpdateId();$bot->sendMessage($message->getChat()->getId(), "text");

  if(mb_stripos($mtext,"Маря") {
  	$bot->sendMessage($message->getChat()->getId(), 'Привет создатель!');
  } else {
    $bot->sendMessage($message->getChat()->getId(),.$mtext);
  }
}
$bot->command('start', function ($message) use ($bot) {
$answer = 'Добро пожаловать!';
$bot->sendMessage($message->getChat()->getId(), $answer);
});
// помощь
$bot->command('help', function ($message) use ($bot) {
$answer = 'Команды:
/help - помощь';
$bot->sendMessage($message->getChat()->getId(), $answer);
});

    $bot->command('ping', function ($message) use ($bot) {
        $bot->sendMessage($message->getChat()->getId(), 'pong!');
    });

// передаем картинку
$bot->command('getpic', function ($message) use ($bot) {
$pic = "https://icopydoc.ru/wp-content/uploads/fortelegrambot.jpg";
$bot->sendPhoto($message->getChat()->getId(), $pic);
});
// запускаем обработку
$bot->run();
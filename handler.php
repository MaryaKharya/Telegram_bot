 <?php

header('Content-Type: text/html; charset=utf-8');
// подключаемся к API
require_once("vendor/autoload.php");
// создаем переменную бота
$token = "794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE";
$bot = new \TelegramBot\Api\Client($token);
// если Телеграм-бот не зарегистрирован - регистрируем
// обязательное. Запуск бота

if ($message == "Гифка") {
            $url = "https://68.media.tumblr.com/bd08f2aa85a6eb8b7a9f4b07c0807d71/tumblr_ofrc94sG1e1sjmm5ao1_400.gif";
            $bot->sendDocument($message->getChat()->getId(), $url);
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

if ($message == )

// передаем картинку
$bot->command('getpic', function ($message) use ($bot) {
$pic = "https://icopydoc.ru/wp-content/uploads/fortelegrambot.jpg";
$bot->sendPhoto($message->getChat()->getId(), $pic);
});
// запускаем обработку
$bot->run();
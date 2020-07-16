 <?php

require "../vendor/autoload.php";
use \TelegramBot\Api;

$bot = new \TelegramBot\Api\BotApi('794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE');

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

$bot->run();
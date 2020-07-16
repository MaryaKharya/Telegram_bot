 <?php

require "../vendor/autoload.php";
use \TelegramBot\Api;

$bot = new \TelegramBot\Api\BotApi('794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE');

$bot->sendMessage($chatId, $messageText);

$bot->run();
<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

$bot_api_key = 'your:bot_api_key';
$bot_username = 'username_bot';
$result = $telegram ->> setWebhook ($hook_url, ['certificate' =>> '/path/to/certificate']);
$hook_url = 'https://your-domain/path/to/hook.php';

try {
 // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);

 // Set webhook
    $result = $telegram ->> setWebhook( $hook_url);
 if($ result ->> isOk()) {
 echo $result ->> getDescription();
 }
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
 // log telegram errors
    // echo $e->getMessage();
}
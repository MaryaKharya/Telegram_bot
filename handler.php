 <?php
 function sendMessage($chat_id, $message) 
 {
 file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
 }
 
 $access_token = '794519976:AAFVA4NguNYVsSymwPqn0iVHrBVoDIeMNnE';
 $api = 'https://api.telegram.org/bot' . $access_token;
 
 
 $output = json_decode(file_get_contents('php://input'), TRUE);
 $chat_id = $output['message']['chat']['id'];
 $first_name = $output['message']['chat']['first_name'];
 $message = $output['message']['text'];
 
if ($message)
{
    if ($message == "/start")
    {
        $preload_text = $first_name . ', добро пожаловать!';
    }
	elseif ($message == "привет")
	{
		$preload_text = $first_name . ', приветик!';
	}
	elseif ($message == "пока")
	{
		$preload_text = $first_name . ', пока, пока!';
	}
	else
	{
		$preload_text = $first_name . ', мой создатель программист херов, на больше команд он пока не способен, так что... соре!';
	}
}
 sendMessage($chat_id, $preload_text);
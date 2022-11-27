<?php
include('vendor/autoload.php');
use Telegram\Bot\Api;
include_once('env.php');
use env\Env as env;


$telegram = new Api(env::$TELEGRAM_BOT_TOKEN);
$chat_id = env::$group_test_stud_bot_v2;
$reply = "Hello world!";
$telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply]);

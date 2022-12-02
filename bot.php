<?php

include('vendor/autoload.php');
use Telegram\Bot\Api;
include_once('env.php');
include_once('db.php');
use env\Env as env;
use mydb\myDB;


$iteration_count = 0;

$telegram = new Api(env::$TELEGRAM_BOT_TOKEN2);
$tgDbase = new myDB(env::class);


class TGBot{
    public $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env::$TELEGRAM_BOT_TOKEN2);
    }

    function get_result(){return $this->telegram->getWebhookUpdates();}
    function sendMessage($chat_id, $message){
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML']);
    }
    function sendMessage_mark($chat_id, $message){
        $url = "https://t.me/mr_anders0n_bot";
        $inline[] = ['text'=>'Перейти до реєстрації', 'url'=>$url];
        $inline = array_chunk($inline, 2);
        $reply_markup = ['inline_keyboard'=>$inline];
        $keyboard = json_encode($reply_markup);
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_markup' => $keyboard,
            'parse_mode' => 'HTML']);
    }
    function sendMessage_mark_ConfirmForm($chat_id, $message){
        $url = "https://t.me/mr_anders0n_bot";
        $inline[] = ['text'=>'Так', 'url'=>$url];
        $inline = array_chunk($inline, 2);
        $reply_markup = ['inline_keyboard'=>$inline];
        $keyboard = json_encode($reply_markup);
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_markup' => $keyboard,
            'parse_mode' => 'HTML']);
    }
}


//if($text == 'start'){
//    $reply = "Hello world!";
//    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply]);
//}

// composer require irazasyed/telegram-bot-sdk ^2.0
//$ composer require vlucas/phpdotenv
//https://api.telegram.org/botTOKEN/setWebHook?url=HTTPSLINK

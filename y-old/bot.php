<?php

include('vendor/autoload.php');
use Telegram\Bot\Api;
include_once('env.php');
include_once('db.php');
use env\Env as env;
use mydb\myDB;


$iteration_count = 0;

$telegram = new Api(env::$TELEGRAM_BOT_TOKEN);
$tgDbase = new myDB(env::class);


class TGBot{
    public $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env::$TELEGRAM_BOT_TOKEN);
    }

    function get_result(){return $this->telegram->getWebhookUpdates();}
    function sendMessage($chat_id, $message){
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'parse_mode' => 'HTML']);
    }
    function replyMessage($chat_id, $message, $message_id){
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_to_message_id' => $message_id, 'parse_mode' => 'HTML']);
    }
    function sendMessage_mark($chat_id, $message, $keyboard){
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_markup' => $keyboard,
            'parse_mode' => 'HTML']);
    }
    function sendMessage_mark_start_register($chat_id, $message){
        $url = "https://t.me/helper_stud_bot";
        $inline[] = ['text'=>'Заповнити форму', 'url'=>$url];
        $inline = array_chunk($inline, 2);
        $reply_markup = ['inline_keyboard'=>$inline];
        $keyboard = json_encode($reply_markup);
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_markup' => $keyboard,
            'parse_mode' => 'HTML']);
    }
    function replyMessage_mark_start_register($chat_id, $message, $message_id){
        $url = "https://t.me/helper_stud_bot";
        $inline[] = ['text'=>'Заповнити форму', 'url'=>$url];
        $inline = array_chunk($inline, 2);
        $reply_markup = ['inline_keyboard'=>$inline];
        $keyboard = json_encode($reply_markup);
        $this->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $message, 'reply_to_message_id' => $message_id, 'reply_markup' => $keyboard,
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
//https://api.telegram.org/bot5480236027:AAFVSKP_ujUosykr0YlRCkmT1Hj4-HGSNmA/setWebHook?url=https://bot.help-study2021.online/index.php

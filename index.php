<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body{min-height: 200vh;}
        .elem{
            width: 100%;
            height: 100px;
            background-color: #69403a;
            box-shadow: inset 0 0 40px black;
            transition: ease-in-out all .5s;
            transform: translate(0, var(--scrollTop));
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url("starring.png");
        }
        .text{
            position: relative;
            -webkit-background-clip: text;
            color: transparent;
            background-image: radial-gradient( circle 759px at -6.7% 50%,  rgba(80,131,73,1) 0%, rgba(140,209,131,1) 26.2%, rgba(178,231,170,1) 50.6%, rgba(144,213,135,1) 74.1%, rgba(75,118,69,1) 100.3% );
            font-weight: bold;
            font-size: 2.5em;
        }
    </style>
</head>
<body>
    <div class="elem"><div class="text"></div></div>
</body>
<script>
    window.addEventListener('scroll', e => {
        document.body.style.cssText = `--scrollTop: ${this.scrollY}px`;
        document.querySelector('.text').innerHTML = `${Math.round(this.scrollY)}px`;
    });
</script>
</html>
<?php
include_once('env.php');
use env\Env as env;
include_once "bot.php";
include_once('db.php');
use mydb\myDB as DB;


$tgbot = new TGBot(env::$TELEGRAM_BOT_TOKEN2);
$db = new DB(env::class);
$chat_id = env::$group_test_stud_bot_v2;


/*  get data from message   */
$result     =                      $tgbot->get_result();
$chat_id    =          $result['message']['chat']['id'];
$from_id    =          $result['message']['from']['id'];
$type       =        $result['message']['chat']['type'];
$name       =    $result['message']['from']['username'];
$first_name =  $result['message']['from']['first_name'];
$last_name  =   $result['message']['from']['last_name'];
if($result['message']['text'])         {$text = $result['message']['text'];} // check if message is text \ get text
if($result['message']['caption']){$caption = $result['message']['caption'];} // check if message is image with caption \ get caption



// todo:                                                       . . : : first bot : : . .
// 1 - respond to keywords match in basic chat ore in each when match ?

$key_words_1 = ["математик", "дослідження операцій", "статистик", "економік", "фізик", "физик", "ймовірності", "тімс",
    "матаналіз", "курсов", "дипломн", "реферат", "презентаці", "статистиці", "філософі", "алгебр",
    "економетри", "економіці", "курсач", "численні методи", "численним методам"];
$key_words_2 = ["допомога", "допоможе", "зробити", "виконати", "допомогти", "помогти", "помощь", "потрібен"];

$answer = ["Звертайтесь до @kakadesa", "Увага ! Дуже багато шахраїв ! Перевіряйте виконавців, які відгукнуться ( відгуки, гарантії, бот @ugodabot, робота наперед )"];


/*  search keywords and message text match  */
function old_keywords_search($keywords, $haystack){
    $result = null;
    foreach($keywords as $needle){
        if(is_numeric(strripos($haystack, $needle))){$result = true;}
    }
    return $result;
}


function old_bot_check_string_match($text, $keywords_1, $keywords_2, $chat_id, $answer){
    global $tgbot;
    /*  if chat ID belong basic group  */
    if(intval($chat_id) === intval(env::$group_test_stud_bot_v2)){
        /*  if text from message match with keywords - send message from message array  */
        if(old_keywords_search($keywords_1, $text)){$tgbot->sendMessage(env::$group_test_stud_bot_v2, $answer[0]);}
        else if(old_keywords_search($keywords_2, $text)){$tgbot->sendMessage(env::$group_test_stud_bot_v2, $answer[1]);}
    }
}


// start function if message contain only text
if($text){old_bot_check_string_match($text, $key_words_1, $key_words_2, $chat_id, $answer);}

// start function if message contain photo with caption
if($caption){old_bot_check_string_match($caption, $key_words_1, $key_words_2, $chat_id, $answer);}



// todo:                                                       . . : : second bot : : . .
// 1 - check all match include picture, ore only text message ?

$our_chats = [env::$group_test_stud_bot_v2, env::$group_test_stud_bot_v2, env::$group_test_stud_bot_v2];
$key_words_second_bot = ["Реєстрація"];


function check_string_match($text, $keywords, $chat_id){
    global $tgbot;
    /*  if chat ID belong basic group  */
    if(intval($chat_id) === intval(env::$group_test_stud_bot_v2)) {
        $message = "Щоб створити і заповнити форму, перейдіть в чат з нашим ботом.";
        /*  if text from message match with keywords - send message from message array  */
        if(old_keywords_search($keywords, $text)){$tgbot->sendMessage_mark_start_register(env::$group_test_stud_bot_v2, $message);}
    }
}

/*  exclude our chats to define private chat with bot   */
function exclude_chats($chat_id){
    global $our_chats;
    return is_numeric(array_search($chat_id, $our_chats)) ? false : true;
}

/*  ------------------------   */
function form_fill_start($from_id){
    global $db, $tgbot, $chat_id;
    try {
        $db->create_task_table($from_id);
    } catch(Exception $e) {
    }
    $db->set_task_table($from_id, 'cur_item', 1);
    $db->set_task_table($from_id, 'start', true);
    $tgbot->sendMessage($chat_id, "Заповніть пункт 1");
}
function form_fill($from_id){
    global $db, $tgbot, $chat_id, $text, $result, $update;
    $task_table = $db->get_task_table($from_id);
    
    if($task_table[5] == 1){
        $db->set_task_table($from_id, 'cur_item', 2);
        $db->set_task_table($from_id, 'item1', $text);
        $tgbot->sendMessage($chat_id, "Заповніть пункт 2");
    }else if($task_table[5] == 2){
        $db->set_task_table($from_id, 'cur_item', 3);
        $db->set_task_table($from_id, 'item2', $text);
        $tgbot->sendMessage($chat_id, "Заповніть пункт 3");
    }else if($task_table[5] == 3){
        $db->set_task_table($from_id, 'cur_item', 4);
        $db->set_task_table($from_id, 'item3', $text);
        sleep(1);
        $task_table = $db->get_task_table($from_id);
        $reply = "Ваша форма:\n {$task_table[2]} \n {$task_table[3]} \n {$task_table[4]} \n\n Надіслати адміністратору?";
        $inline[] = [['text'=>'Так', 'callback_data' => 'send_yes'], ['text'=>'Ні', 'callback_data' => 'send_no']];
        $reply_markup = $tgbot->telegram->replyKeyboardMarkup(['keyboard' => $inline, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
        $tgbot->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup, 'parse_mode' => 'HTML']);
    }

}


// start function if message contain only text
if($text){check_string_match($text, $key_words_second_bot, $chat_id);}

// private chat - /start
if($text === "/start" && $type === "private"){form_fill_start($from_id);}
// private chat - form fill
else if($text && $type === "private"){form_fill($from_id);}




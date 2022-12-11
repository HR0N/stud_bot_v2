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
$env_chat_id = env::$group_test_stud_bot_v2;


$update = json_decode(file_get_contents('php://input'), TRUE);
$callback_query = $update['callback_query'];
$callback_query_data = $callback_query['data'];
$callback_chat_id = $callback_query["message"]["chat"]["id"];
$callback_user = $update['callback_query']['from']['username'];
/*  get data from message   */
$result     =                      $tgbot->get_result();
$chat_id    =          $result['message']['chat']['id'];
$from_id    =          $result['message']['from']['id'];
$type       =        $result['message']['chat']['type'];
$username       =    $result['message']['from']['username'];
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
        if(is_numeric(strripos(mb_strtolower($haystack), mb_strtolower($needle)))){$result = true;}
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
$key_words_second_bot = ["Реєстрація", "Регистрация"];


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
    global $db, $tgbot, $chat_id, $username;
    try {
        $db->create_task_table($from_id);
    } catch(Exception $e) {
    }
    $db->set_task_table($from_id, 'cur_item', 1);
    $db->set_task_table($from_id, 'start', true);
    $db->set_task_table($from_id, 'username', $username);
    $tgbot->sendMessage($chat_id, "Напишіть тип роботи.");
}
function form_fill($from_id){
    global $db, $tgbot, $chat_id, $text;
    $task_table = $db->get_task_table($from_id);
    if($task_table[5] == 1){
        $db->set_task_table_by_id($task_table[0], 'cur_item', 2);
        $db->set_task_table_by_id($task_table[0], 'item1', $text);
        $tgbot->sendMessage($chat_id, "Напишіть назву предмета.");
    }elseif($task_table[5] == 2){
        $db->set_task_table_by_id($task_table[0], 'cur_item', 3);
        $db->set_task_table_by_id($task_table[0], 'item2', $text);
        $tgbot->sendMessage($chat_id, "Напишіть термін виконання.");
    }elseif($task_table[5] == 3){
        $db->set_task_table_by_id($task_table[0], 'cur_item', 4);
        $db->set_task_table_by_id($task_table[0], 'item3', $text);
        sleep(1);
        $task_table = $db->get_task_table($from_id);
        $reply = "Ваша форма:\n{$task_table[2]} \n{$task_table[3]} \n{$task_table[4]} \n\nНадіслати адміністратору?";
        $inline[] = [['text'=>'Так', 'callback_data'=>'yes send'], ['text'=>'Ні', 'callback_data'=>'no send']];
        // don't remove this
//        $reply_markup = $tgbot->telegram->replyKeyboardMarkup(['keyboard' => $inline, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
        $reply_markup = ['inline_keyboard'=>$inline];
        $keyboard = json_encode($reply_markup);
        $tgbot->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML']);
    }

}
/*  check update data before send   */
function form_send_check($callback_chat_id, $callback_data){
    global $db, $tgbot, $result;
    $task_table = $db->get_task_table($callback_chat_id);
    $db->set_task_table_by_id($task_table[0], 'start', false);
    $task_table = $db->get_task_table($callback_chat_id);
    if($callback_data == 'yes send'){
        $tgbot->sendMessage($callback_chat_id, "Вашу заявку надіслано на розгляд адміністратору.");
        create_form_message_to_admin_confirm($task_table, $callback_chat_id);
    }elseif($callback_data == 'no send'){
        $tgbot->sendMessage($callback_chat_id, "Форму скасовано");
    }
}

/*  create message for admin group  */
function create_form_message_to_admin_confirm($task){
    global $tgbot;
    $message  = "№ {$task[0]}\nАвтор @{$task[7]}\n\n{$task[2]}\n{$task[3]}\n{$task[4]}\n";
    $inline[] = [['text'=>'Опублікувати', 'callback_data'=>"confirm publish {$task[0]}"], ['text'=>'Видалити', 'callback_data'=>"confirm delete {$task[0]}"]];
    // don't remove this
//        $reply_markup = $tgbot->telegram->replyKeyboardMarkup(['keyboard' => $inline, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
    $reply_markup = ['inline_keyboard'=>$inline];
    $keyboard = json_encode($reply_markup);
    $tgbot->telegram->sendMessage(['chat_id' => env::$group_stud_bot_v2_admin, 'text' => $message, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML']);
}

/*  confirm\delete buttons  */
function admin_form_confirm(){
    global $db, $tgbot, $callback_chat_id, $callback_query_data;
    $explosive = explode(' ', $callback_query_data);
    $task_id = $explosive[2];
    $confirm = $explosive[1] == 'publish' ? true : false;
    if($confirm){
        $task = $db->get_task_table_by_id($task_id);
        if(strlen(strval($task[0])) > 0){
            $message  = "№ {$task[0]}\n\n{$task[2]}\n{$task[3]}\n{$task[4]}\n";
            $inline[] = [['text'=>'Запросити замовлення', 'callback_data'=>"accept order {$task[0]}"]];
            // don't remove this
//        $reply_markup = $tgbot->telegram->replyKeyboardMarkup(['keyboard' => $inline, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
            $reply_markup = ['inline_keyboard'=>$inline];
            $keyboard = json_encode($reply_markup);
            $tgbot->telegram->sendMessage(['chat_id' => env::$group_stud_bot_v2_work, 'text' => $message, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML']);
        }else{$tgbot->sendMessage(env::$group_stud_bot_v2_admin, 'Замовлення вже видалено.');}
    }elseif(!$confirm){
        $tgbot->sendMessage(env::$group_stud_bot_v2_admin, "Замовлення видалено!");
        $db->delete_task($task_id);
    }
}

/*  accept order button  */
function accept_order(){
    global $db, $tgbot, $callback_chat_id, $callback_query_data, $callback_user;
    $explosive = explode(' ', $callback_query_data);
    $task_id = $explosive[2];
    $task = $db->get_task_table_by_id($task_id);
    $message = "Запит на замовлення!\n\nЗамовник @{$task[7]}\nХоче виконати @{$callback_user}\n\nОпис замовлення: \n$task[2]\n$task[3]\n$task[4]";
    $tgbot->sendMessage(env::$group_stud_bot_v2_admin, $message);
}


echo '<pre>';
echo var_dump(($db->get_task_table(441246772)));
echo '</pre>';

// start function if message contain only text
if($text){check_string_match($text, $key_words_second_bot, $chat_id);}

// private chat - /start
if($text === "/start" && $type === "private"){form_fill_start($from_id);}
// private chat - form fill
else if($type === "private"){form_fill($from_id);}

// update - check is form send to admin
if($callback_query_data == 'yes send' || $callback_query_data == 'no send'){form_send_check($callback_chat_id, $callback_query_data);}
// update - admin confirm\dismiss
if(is_numeric(strripos(mb_strtolower($callback_query_data), mb_strtolower('confirm')))){admin_form_confirm();}
// update - accept order =>
if(is_numeric(strripos(mb_strtolower($callback_query_data), mb_strtolower('accept order')))){accept_order();}


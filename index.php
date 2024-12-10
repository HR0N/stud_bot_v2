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
include 'messages.php';
use mydb\myDB as DB;
use Telegram\Bot\Exceptions\TelegramResponseException;


$tgbot = new TGBot(env::$TELEGRAM_BOT_TOKEN);
$db = new DB(env::class);
$env_chat_id = env::$stud_group;


$update = json_decode(file_get_contents('php://input'), TRUE);
$callback_query     =                        $update['callback_query'];
$callback_query_data =                         $callback_query['data'];
$callback_chat_id    =        $callback_query["message"]["chat"]["id"];
$callback_user       =   $update['callback_query']['from']['username'];
$callback_first_name = $update['callback_query']['from']['first_name'];
/*  get data from message   */
$result     =                      $tgbot->get_result();
$chat_id    =          $result['message']['chat']['id'];
$from_id    =          $result['message']['from']['id'];
$message_id =          $result['message']['message_id'];
$type       =        $result['message']['chat']['type'];
$username   =    $result['message']['from']['username'];
$first_name =  $result['message']['from']['first_name'];
$last_name  =   $result['message']['from']['last_name'];
if($result['message']['text'])         {$text = $result['message']['text'];} // check if message is text \ get text
if($result['message']['caption']){$caption = $result['message']['caption'];} // check if message is image with caption \ get caption


// todo:                                                       . . : : first bot : : . .

$answer = [$answer_01, $answer_02];



/*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - DEBUGGING - - - - - -   */
function DEBUGGING(){
    global $db, $tgbot, $chat_id, $text;
    if(intval($chat_id) === intval(env::$dev_group)){
        $tgbot->sendMessage(env::$dev_group, "test: " .$text);
    }

}
//DEBUGGING();
/*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - end of DEBUGGING - - - - - -   */

/*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - Deleting Messages - - - - - -   */
function save_msg_id($result){
    global $db;
    try {
        $result_message_id = $result->getMessageId();
        $json = [ 'messages' => [ 'last_bot_message_id' => $result_message_id, ], ];
        $old_message_id = json_decode($db->get_deleting_messages()[1])->messages->last_bot_message_id; // | returns int
        $db->set_deleting_messages($json);
        check_bot_msg_id_difference($result_message_id, $old_message_id);
    } catch (TelegramResponseException $exception) {    // TelegramResponseException must be imported
        //continue;
    }
}
function send_msg_and_save_id($chat_id, $message){
    global $db, $tgbot;
    $result = $tgbot->sendMessage($chat_id, $message);
    try {
        $result_message_id = $result->getMessageId();
        $json = [ 'messages' => [ 'last_bot_message_id' => $result_message_id, ], ];
        $old_message_id = json_decode($db->get_deleting_messages()[1])->messages->last_bot_message_id; // | returns int
        $db->set_deleting_messages($json);
        check_bot_msg_id_difference($result_message_id, $old_message_id);
    } catch (TelegramResponseException $exception) {    // TelegramResponseException must be imported
        //continue;
    }
}
function reply_msg_and_save_id($chat_id, $message, $message_id){
    global $db, $tgbot;
    $result = $tgbot->replyMessage($chat_id, $message, $message_id);
    try {
        $result_message_id = $result->getMessageId();
        $json = [ 'messages' => [ 'last_bot_message_id' => $result_message_id, ], ];

        $old_message_id = json_decode($db->get_deleting_messages()[1])->messages->last_bot_message_id; // | returns int
        $db->set_deleting_messages($json);
        check_bot_msg_id_difference($result_message_id, $old_message_id);
    } catch (TelegramResponseException $exception) {    // TelegramResponseException must be imported
        //continue;
    }
}

function check_bot_msg_id_difference($bot_message_id, $old_message_id){  //  only bot message
    global $db, $tgbot, $chat_id;
//    $tgbot->sendMessage($chat_id, "$bot_message_id - $old_message_id = ".$bot_message_id - $old_message_id);

    if($bot_message_id - $old_message_id <= 2){
        $tgbot->deleteMessage($chat_id, $old_message_id);
    }
}
/*  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - and Deleting Messages - - - -   */



/*  search keywords and message text match  */
function old_keywords_search($keywords, $haystack){
    $result = false;
    foreach($keywords as $needle){
        if(mb_strripos(mb_strtolower($haystack), mb_strtolower($needle)) !== false){
            $result = true;
            break;
        }
    }
    return $result;
}


function old_bot_check_string_match($text, $keywords_1, $keywords_2, $chat_id, $answer){
    global $tgbot, $message_id;
    /*  if chat ID belong basic group  */
    if(intval($chat_id) === intval(env::$stud_group) || intval($chat_id) === intval(env::$dev_group)){
        /*  if text from message match with keywords - send message from message array  */
        if(old_keywords_search($keywords_1, $text)){
//            reply_msg_and_save_id(env::$stud_group, $answer[0], $message_id);
            reply_msg_and_save_id($chat_id, $answer[0], $message_id);

        }
        else if(old_keywords_search($keywords_2, $text)){
//            send_msg_and_save_id(env::$stud_group, $answer[1]);
            send_msg_and_save_id($chat_id, $answer[1]);

        }
    }
}

function message_to_delete_search($keywords, $haystack){
    $result = null;
    foreach($keywords as $needle){
        if(mb_strtolower($needle) == mb_strtolower($haystack)){$result = true;}
    }
    return $result;
}




// todo:                                                       . . : : second bot : : . .
// 1 - check all match include picture, ore only text message ?

$key_words_second_bot = ["опір матеріалів", "опору", "опором" , "опору матеріалів", "ТММ", "теорії машин та механізмів", "теорія машин та механізмів",
"хімія", "хімією", "хімії", "інформати", "програмуванн", "електроні", "база даних", "базами даних", "інвустування", "інвестиці",
"функціональний аналіз", "функціональному аналізу", "функан", "управління інноваційною діяльністю",
"управлінням інноваційною діяльністю", "теорії інформації та кодування", "теорія інформації та кодування",
"інженерія програмного забезпечення", "будівельних конструкцій", "будівельні конструкції", "тзео", "фінансовий аналіз",
"фінансового аналізу", "фінансовим аналізом", "електротехні", "тое", "матеріалознавств", "логісти", "моделюванн",
"архітектурні конструкції та креслення", "механі", "технологія будівельного виробництва",
"технологією будівельного виробництва", "інженерна графіка", "інженерною графікою", "інженерній графіці", "java", "pascal",
"python", "c++", "c#", "менеджмент", "маркетинг", "біофізи", "біологі", "ТОЕ", "електротехні","метролог", "креслен", "будівництв",
"англійськ", "філософі", "історі", "психологі", "французськ", "педагогі", "векторний аналіз", "тензорний аналіз",
"векторного аналізу", "тензорного аналізу"];


function check_string_match($text, $keywords, $chat_id){
    global $tgbot, $message_id;
    /*  if chat ID belong basic group  */
    if(intval($chat_id) === intval(env::$stud_group) || intval($chat_id) === intval(env::$dev_group)){
        $message = "Щоб сформувати замовлення перейдіть в чат з нашим ботом";
        /*  if text from message match with keywords - send message from message array  */
        if(old_keywords_search($keywords, $text)){$result = $tgbot->replyMessage_mark_start_register(env::$stud_group, $message, $message_id); save_msg_id($result);}
    }
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
function form_fill($from_id, $username){
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
    }elseif($task_table[5] == 3 && strlen($username) >= 1){
        $db->set_task_table_by_id($task_table[0], 'cur_item', 4);
        $db->set_task_table_by_id($task_table[0], 'item3', $text);
        confirm_user_form_and_send_to_admin_group($from_id);
    }elseif($task_table[5] == 3 && strlen($username) < 1){
        $tgbot->sendMessage($chat_id, "Напишіть номер телефону або інший контакт для зв'язку.");
        $db->set_task_table_by_id($task_table[0], 'cur_item', 4);
        $db->set_task_table_by_id($task_table[0], 'item3', $text);
    }elseif($task_table[5] == 4){
        if(strlen($username) < 1){
            $db->set_task_table_by_id($task_table[0], 'cur_item', 5);
            $db->set_task_table_by_id($task_table[0], 'contact', $text);
        }
        confirm_user_form_and_send_to_admin_group($from_id);
    }

}
function  confirm_user_form_and_send_to_admin_group($from_id){
    global $db, $tgbot, $chat_id, $username;
    sleep(1);
    $task_table = $db->get_task_table($from_id);
    $contact = strlen($username) > 0 ? "" : "\n{$task_table[8]}";
    $reply = "Ваша форма:\n{$task_table[2]} \n{$task_table[3]} \n{$task_table[4]}".$contact." \n\nНадіслати адміністратору?";
    $inline[] = [['text'=>'Так, надіслати адміністратору', 'callback_data'=>'yes send'], ['text'=>'Ні', 'callback_data'=>'no send']];
    // don't remove this
//        $reply_markup = $tgbot->telegram->replyKeyboardMarkup(['keyboard' => $inline, 'resize_keyboard' => true, 'one_time_keyboard' => true]);
    $reply_markup = ['inline_keyboard'=>$inline];
    $keyboard = json_encode($reply_markup);
    $tgbot->telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $keyboard, 'parse_mode' => 'HTML']);
}
/*  check update data before send   */
function form_send_check($callback_chat_id, $callback_data){
    global $db, $tgbot, $result;
    $task_table = $db->get_task_table($callback_chat_id);
    $db->set_task_table_by_id($task_table[0], 'start', false);
    $task_table = $db->get_task_table($callback_chat_id);
    if($callback_data == 'yes send'){
        $tgbot->sendMessage($callback_chat_id, "Ваше замовлення прийнято. Ми зв'яжемось з вами максимально швидко!");
        create_form_message_to_admin_confirm($task_table, $callback_chat_id);
    }elseif($callback_data == 'no send'){
        $tgbot->sendMessage($callback_chat_id, "Форму скасовано");
    }
}

/*  create message for admin group  */
function create_form_message_to_admin_confirm($task){
    global $tgbot;
    $author = strlen($task[7]) > 0 ? "Автор @{$task[7]}" : "Контакт {$task[8]}";
    $message  = "№ {$task[0]}\n".$author."\n\nТип роботи: {$task[2]}\nПредмет: {$task[3]}\nТерміни: {$task[4]}\n";
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
            $url = "https://t.me/kakadesa";
            $message  = "№ {$task[0]}\n\nТип роботи: {$task[2]}\nПредмет: {$task[3]}\nТерміни: {$task[4]}\n";
//            $inline[] = [['text'=>'Взяти замовлення', 'callback_data'=>"accept order {$task[0]}"]];
            $inline[] = [['text'=>'Взяти замовлення', 'url'=>$url]];
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
    global $db, $tgbot, $callback_chat_id, $callback_query_data, $callback_user, $first_name, $callback_first_name;
//    $tgbot->sendMessage('-645978616', $callback_query_data);
    $explosive = explode(' ', $callback_query_data);
    $task_id = $explosive[2];
    $task = $db->get_task_table_by_id($task_id);
    $message2 = "Замовлення взято, {$callback_first_name} ми вам напишемо";
    $tgbot->sendMessage(env::$group_stud_bot_v2_work, $message2);
    $message = "Запит на замовлення!\n\n№ {$task[0]}\nЗамовник @{$task[7]}\nХоче виконати @{$callback_user}\n\nОпис замовлення: \n$task[2]\n$task[3]\n$task[4]";
    $tgbot->sendMessage(env::$group_stud_bot_v2_admin, $message);
}

// start function if message contain only text
if($text && message_to_delete_search($strings_to_remove, $text)){$tgbot->deleteMessage($chat_id, $message_id);}
else if($text && old_keywords_search($key_words_second_bot, $text)){check_string_match($text, $key_words_second_bot, $chat_id);}
else{
// start function if message contain only text
    if($text){old_bot_check_string_match($text, $key_words_1, $key_words_2, $chat_id, $answer);}

// start function if message contain photo with caption
    if($caption){old_bot_check_string_match($caption, $key_words_1, $key_words_2, $chat_id, $answer);}
}


// private chat - /start
if($text === "/start" && $type === "private"){form_fill_start($from_id);}
// private chat - form fill
else if($type === "private"){form_fill($from_id, $username);}

// update - check is form send to admin
if($callback_query_data == 'yes send' || $callback_query_data == 'no send'){form_send_check($callback_chat_id, $callback_query_data);}
// update - admin confirm\dismiss
if(is_numeric(strripos(mb_strtolower($callback_query_data), mb_strtolower('confirm')))){admin_form_confirm();}
// update - accept order =>
if(is_numeric(mb_strripos(mb_strtolower($callback_query_data), mb_strtolower('accept order')))){accept_order();}




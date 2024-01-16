<?php


$input = file_get_contents("php://input");
$update = json_decode($input, true);
////////////////////////// Functions /////////////////////////
function bot($data)
{
    return json_decode(file_get_contents("https://api.telegram.org/bot" . BOT_TOKEN . "/" . $data), true);
}

//////////////////////////   <database function>   /////////////////////////////////////////

message($user_id,'dsfdsfsdf');
function message($chat_id, $msg, $markup = null)
{
    if ($markup != null) {
        return bot("sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($msg) . "&reply_markup=" . $markup);
    } else {
        return bot("sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($msg));
    }
}
 //$replyMarkup = array(
//   'force_reply' => true,
//   'selective' => true
// );

function replyMessage($chat_id, $msg, $reply_to)
{
    bot("sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($msg) . "&reply_to_message_id=" . $reply_to );
}



function forwardMessage($user_id, $message_id, $from_chat_id)
{
    bot("forwardMessage?chat_id=" . $user_id . "&from_chat_id=" . $from_chat_id . "&message_id=" . $message_id);
}
function answerInlineQuery($inline_query_id, $arrayOfResult)
{
    return bot("answerInlineQuery?inline_query_id=" . $inline_query_id . "&results=" . $arrayOfResult);
    
}

function editMessage($chat_id, $message_id, $msg)
{
    bot("editMessageText?chat_id=" . $chat_id . "&message_id=" . $message_id . "&text=" . $msg);
}

function photo($chat_id, $photo_link, $caption = null)
{
    if ($caption != null) {
        bot("sendPhoto?chat_id=" . $chat_id . "&photo=" . $photo_link . "&caption=" . $caption);
    } else {
        bot("sendPhoto?chat_id=" . $chat_id . "&photo=" . $photo_link);

    }
}
function photoMarkup($chat_id, $photo_link,$markup, $caption = null)
{
    if ($caption != null) {
        bot("sendPhoto?chat_id=" . $chat_id . "&photo=" . $photo_link . "&caption=" . $caption."&reply_markup=".$markup);
    } else {
        bot("sendPhoto?chat_id=" . $chat_id . "&photo=" . $photo_link."&reply_markup=".$markup);

    }
}

function video($chat_id, $video_link, $caption = null)
{
    bot("sendVideo?chat_id=" . $chat_id . "&video=" . $video_link . "&caption=" . $caption);
}
function Voice($chat_id, $voice, $caption = null)
{
    if ($caption != null) {
        bot("sendVoice?chat_id=" . $chat_id . "&voice=" . $voice . "&caption=" . $caption);
    } else {
        bot("sendVoice?chat_id=" . $chat_id . "&voice=" . $voice);
    }
}
function voiceWithMurkub($chat_id, $voice, $markup = null)
{
    if ($markup != null) {
        bot("sendVoice?chat_id=" . $chat_id . "&voice=" . $voice . "&reply_markup=" . $markup);
    } else {
        bot("sendVoice?chat_id=" . $chat_id . "&voice=" . $voice);
    }
}
////////////////////////////
function sticker($chat_id, $sticker, $murkub = null)
{
    if ($murkub != null) {
        bot("sendSticker?chat_id=" . $chat_id . "&sticker=" . $sticker. "&reply_markup=" . $murkub);
    } else {
        bot("sendSticker?chat_id=" . $chat_id . "&sticker=" . $sticker);
    }
}

////////////////////////////
function send_file($chat_id, $file_id, $caption = null)
{
    bot("sendDocument?chat_id=" . $chat_id . "&document=" . $file_id . "&caption=" . $caption);
}

function action($chat_id, $action)
{
    bot("sendChatAction?chat_id=" . $chat_id . "&action=" . $action);
}

function answer_query($query_id, $text, $show_alert = false)
{
    bot("answerCallbackQuery?callback_query_id=" . $query_id . "&text=" . $text . "&show_alert=" . $show_alert);
}

function edit_markap($chat_id, $message_id, $text, $markub = null)
{
    if ($markub == null) {
        bot("editMessageText?chat_id=" . $chat_id . "&message_id=" . $message_id . "&text=" . $text);

    } else {
        bot("editMessageText?chat_id=" . $chat_id . "&message_id=" . $message_id . "&text=" . $text . "&reply_markup=" . $markub);
    }

}
function editMarkab($chat_id,$message_id,$markub){
    bot("editMessageReplyMarkup?chat_id=" . $chat_id . "&message_id=" . $message_id  . "&reply_markup=" . $markub);
}


function inline_btn($i)
{
    $ar = array();
    $button = array();
    for ($c = 0; $c < count($i); $c = $c + 2) {
        $button[$c / 2 % 2] = array("text" => urlencode($i[$c]), "callback_data" => $i[$c + 1]);
        if ($c / 2 % 2) {
            array_push($ar, array($button[0], $button[1]));
            $button = array();
        } elseif (count($i) - $c <= 2) {
            array_push($ar, array($button[0]));
            $button = array();
        }
    }
    return  json_encode(array("inline_keyboard" => $ar));
}

function getFileLink($file_id)
{
    $array=bot("getFile?file_id=".$file_id);
    $link="https://api.telegram.org/file/bot".BOT_TOKEN."/".$array['result']['file_path'];
    return $link;
}

function isMember($user_id, $chat_id)
{
    $status = bot("getChatMember?chat_id=" . $chat_id . "&user_id=" . $user_id);
    if($status != 'creator' or $status != 'administrator' or $status != 'member'){
        return false;
    }else{
        return true;
    }
    return $status['result']['status'];
}

function can_open_admin_setting($adminTable,$user_id){
    $getAccess = $adminTable->select($user_id);
    if ($getAccess->user_id == $user_id){
        return true;
    }else{
        return false;
    }
}

function remove_keyboard()
{
    bot("ReplyKeyboardRemove?remove_keyboard");
}
function back_to_main_menu($userTable,$user_id,$keyboard){
    $userTable->update($user_id,"position","start");
    message($user_id,"شما به منوی اصلی برگشتید ! \n لطفا انتخاب کنید",$keyboard);
}
function back_to_admin_menu($userTable,$user_id,$keyboard){
    $userTable->update($user_id,"position","start/admin");
    message($user_id,"شما به منوی اصلی ادمین برگشتید ! \n لطفا انتخاب کنید",$keyboard);
}
function keyboard($buttons)
{

    $markab = json_encode(array('keyboard'=>$buttons,'resize_keyboard' => true));
    return $markab;
}
function delMessage($chat_id,$message_id){
    $res=bot("deleteMessage?chat_id=".$chat_id."&message_id=".$message_id);
    return $res;
}
////clean code////

function editAdminKeyboard($adminTable,$user_id){
    $admin_keyboard =[
        ["لیست همه کاربران", "فضولی"],
        ['پیام ناشناس به همه'],
        ['🆘بن کاربر']
    ];
    $adminAccess = $adminTable->select($user_id);
    if (!$adminAccess->list_of_users){
        unset($admin_keyboard[0][0]);
    }
    if (!$adminAccess->overheard) {
        unset($admin_keyboard[0][1]);
    }
    if (!$adminAccess->sendMessage){
        unset($admin_keyboard[1][0]);
    }
    if (!$adminAccess->banuser){
        unset($admin_keyboard[2][0]);
    }
    $admin_keyboard[0]= array_values($admin_keyboard[0]);

    return $admin_keyboard;

}
function backToAdmin($userTable,$user_id){
    
}
function productKeyboard($product_id,$number){
   return json_encode(
        array('inline_keyboard'=>array(array(
            [
                "text"=>"➖","callback_data"=>"change_"+$number-1
            ],
            [
                "text"=>$number,"callback_data"=>"nothing"
            ],
            [
                "text"=>"➕","callback_data"=>"change_"+$number+1
            ]
        ),array(
            [
                "text"=>"افزودن به سبد خرید✅","callback_data"=>"addToCart_"+$product_id
            ],
            [
                "text"=>"باز کردن در  فروشگاه","callback_data"=>"openInShop_"+$product_id
            ]
        )),'resize_keyboard'=>true));
}
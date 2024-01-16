<?php
namespace App\helpers;

use App\Telegram\telegramBot;

class telegramHelpers {

    protected $tg;
    public function __construct($bot_token)
    {
        $this->tg = new telegramBot($bot_token);
    }

    public  function isMember($chat_id,$user_id)
    {
        $status = $this->tg->getChatMember($chat_id,$user_id)['result']['status'];
        //file_put_contents('resddult.txt',$status['result']['status'].PHP_EOL.PHP_EOL,FILE_APPEND);
        if($status == 'creator' or $status == 'administrator' or $status == 'member'){
            return true;
        }else{
            return false;
        }
        
    }
}
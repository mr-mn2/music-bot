<?php
namespace App\helpers;

use App\Model\admins;
use App\Model\users;
use App\Telegram\telegramBot;


class helpers {
    protected $dbuser;
    protected $dbAdmin;
    protected $telegram;
    public function __construct($bot_token)
    {
        $this->dbuser = new users();        
        $this->telegram = new telegramBot($bot_token);
        $this->dbAdmin = new admins();
    }
    public function backToMainMenu($user_id,$keyboard)
    {
        $reply_markup = $this->telegram->replyKeyboardMarkup($keyboard, true, true);
        $this->telegram -> sendMessage($user_id,'شما به منوی اصلی بازگشتید'.PHP_EOL.PHP_EOL.'با استفاده از منوی زیر میتونی به امکانات ربات ما دسترسی داشته باشی👌👌', $reply_markup);
        $this->dbuser -> update($user_id,'position','start');
    }

    public function backToAdminMenu($user_id,$keyboard)
    {
        $reply_markup = $this->telegram->replyKeyboardMarkup($keyboard, true, true);
        $this->telegram -> sendMessage($user_id,'شما به منوی ادمین بازگشتید'.PHP_EOL.PHP_EOL.'با استفاده از منوی زیر میتونی به امکانات ادمین دسترسی داشته باشی👌👌', $reply_markup);
        $this->dbuser -> update($user_id,'position','admin');
        die();
    }

    public function editAdminKeyboard($user_id){
        $admin_keyboard =[
            ['اضافه کردن موزیک🎸'],
            ['بن کردن کاربر🚫', 'آزاد کردن✅'],
            ['نعداد کاربران ربات🏹'],
            ['افزودن ادمین👩‍💼'],
            ['بازگشت🔙'],
    
        ];;
        $adminAccess = $this->dbAdmin->select($user_id);
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
    public function murkubForAccessing($someOneWhoWantToBeAdmin)
    {
        $adminInfo = $this->dbAdmin -> select($someOneWhoWantToBeAdmin);
        
        $buttons = [
            [
                ['text' => 'اضافه کردن موزیک🎸', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->addMusic ? '✅':'❌', 'callback_data' => 'accsessing_music_'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'بن کردن', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->banuser ? '✅':'❌', 'callback_data' => 'accsessing_banuser_'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'آنبن کردن', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->unbanuser ? '✅':'❌', 'callback_data' => 'accsessing_unbanuser_'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'انجام شد✅', 'callback_data' => 'done'],
            ]
            
        ];
        return $buttons;
    }
}
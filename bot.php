<?php

include "bootstrap/config.php";
include "vendor/autoload.php";
file_put_contents('result.txt', file_get_contents('php://input') . PHP_EOL . PHP_EOL, FILE_APPEND);

$tg = new App\Telegram\telegramBot(BOT_TOKEN);
$t_helpers = new App\helpers\telegramHelpers(BOT_TOKEN);
$users = new App\Model\users();
$musics = new App\Model\musics();
$dbAdmins = new App\Model\admins();
$updates = $tg->getWebhookUpdates();
$helpers = new App\helpers\helpers(BOT_TOKEN);
$page_limit = 10;
if (isset($updates->callback_query)) {
    $user_id = $updates->callback_query->from->id ?? null;
    $text = $updates->callback_query->data ?? null;
    $callback_query_id = $updates->callback_query->id;
    $message_id = $updates->callback_query->message->message_id;

} elseif (isset($updates->message)) {
    $text = $updates->message->text ?? null;
    $message_id = $updates->message->message_id;
    if (!empty($updates->message->from)) {
        $user_id = $updates->message->from->id ?? null;
        $first_name = $updates->message->from->first_name ?? null;
        $username = $updates->message->from->username ?? null;
    }
    if (!empty($updates->message->chat)) {
        $chat_id = $updates->message->chat->id ?? null;
    }
    if (!empty($updates->message->audio)) {
        $Music_duration = $updates->message->audio->duration;
        $musicName = isset($updates->message->audio->title) ? $updates->message->audio->title : $updates->message->audio->file_name;
        $performer = $updates->message->audio->performer ?? null;
        $music_file_id = $updates->message->audio->file_id;
    }
}

//force user to join the channel
if (($t_helpers->isMember($chanells[0], $user_id))) {
    $tg->sendMessage($user_id, 'برای استفاده از ربات باید در کانال ما عضو شوید😍👇🏻'.PHP_EOL.PHP_EOL.$channeUsername.' ⚡️');
    die();
}


$startKeyboard = [
    ['جستجوی موزیک🔍'],
    ['جدید ترین ها🆕', 'بهترین ها⭐️'],
    ['ارتباط با پشتیبانی👨‍🦰'],

];
$backKeyboard = [
    ['بازگشت🔙'],
];

@$admin_is_register = $dbAdmins->is_register($user_id);
if ($user_id == $main_admin or $user_id == $owner) {
    $adminKeyboard = [
        ['اضافه کردن موزیک🎸'],
        ['بن کردن کاربر🚫', 'آزاد کردن✅'],
        ['نعداد کاربران ربات🏹'],
        ['افزودن ادمین👩‍💼'],
        ['بازگشت🔙'],

    ];
} elseif ($admin_is_register) {
    $adminKeyboard = $helpers->editAdminKeyboard($user_id);

}

//codes


if ($text == '/start') {
    if (!$users->is_register($user_id)) {
        $users->insert_new_user($user_id, $first_name, $username, 'start');
    } else {
        if ($users->select($user_id)->isBanned) {
            $tg->sendMessage($user_id, 'تو بن شدی!');
            die();
        }
        $users->update($user_id, 'position', 'start');
    }

    $reply_markup = $tg->replyKeyboardMarkup($startKeyboard, true, true);
    $tg->sendMessage($user_id, "سلام به ربات موزیکام 404 خیلی خوش اومدید😍😍" . PHP_EOL . PHP_EOL . 'با استفاده از منوی زیر میتونی به امکانات ربات ما دسترسی داشته باشی👌👌', $reply_markup);
    die();
}

$position = $users->select($user_id)->position;
if ($position == 'start') {
    if ($text == 'ارتباط با پشتیبانی👨‍🦰') {

        $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, true);
        $tg->sendMessage($user_id, "در این بخش شما میتونید هر گونه انتقاد, پیشنهادویا هر حرفی که با ادمین داری رو بزنی" . PHP_EOL . PHP_EOL . 'پیام خودتو بصورت مختصر و مفید بنویس و ارسال کن!', $reply_markup);
        $users->update($user_id, 'position', 'messageToAdmin');
        die();
    } else
    if ($text == 'جستجوی موزیک🔍') {
        //InlineKeyboardMarkup($helpers->murkubForAccessing($admin_user_id));
        $tg->sendMessage($user_id, 'لطفا نام موزیک درخاستی یا نام خاننده آنرا برای من بفرست تا ببینم توی لیست موزیکام دارمش یا نه😍👇🏻');
        die();
    } else
    if ($text == '/admin') {
        if ($user_id == $main_admin) {
            $reply_markup = $tg->replyKeyboardMarkup($adminKeyboard, true, true);
            $tg->sendMessage($user_id, 'به منوی ادمین خوش آمدید! ' . PHP_EOL . PHP_EOL . 'با استفاده از منوی زیر میتونی به امکانات ادمین دسترسی داشته باشی👌👌', $reply_markup);
            $users->update($user_id, 'position', 'admin');
            die();
        }
    }else
    if ($text == 'جدید ترین ها🆕') {
        $newMusics = $musics ->selectLastInserted();
        $msg = 'لیست آهنگ های پیدا شده 👇🏻🎵🎶' . PHP_EOL . PHP_EOL;
        foreach ($newMusics as $newMusic) {
            $music_id = $newMusic->id;
            $music_name = $newMusic->name;
            $music_file_id = $newMusic->file_id;
            $singer = $newMusic->singer;
            $musicLikes = $newMusic->hearts;
            $duration = gmdate("i:s", $newMusic->duration);
            $msg .= $numbering . '.' . $music_name . ' - ' . $singer . PHP_EOL .'مدت زمان:' .'⏰' . '. ' . $duration . PHP_EOL.'تعداد لایک : '.$musicLikes.'❤️🔥'.PHP_EOL . 'لینک دانلود: ' . '/dn_' . $music_id . PHP_EOL . PHP_EOL . '〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️' . PHP_EOL . PHP_EOL;
            $numbering++;
        }
        $tg->sendMessage($user_id, $msg);
        die();
    }else
    if ($text == 'بهترین ها⭐️') {
        $newMusics = $musics ->selectBests();
        $msg = 'لیست آهنگ های محبوب 👇🏻🎵🎶' . PHP_EOL . PHP_EOL;
        foreach ($newMusics as $newMusic) {
            $music_id = $newMusic->id;
            $music_name = $newMusic->name;
            $music_file_id = $newMusic->file_id;
            $singer = $newMusic->singer;
            $musicLikes = $newMusic->hearts;
            $duration = gmdate("i:s", $newMusic->duration);
            $msg .= $numbering . '.' . $music_name . ' - ' . $singer . PHP_EOL.'مدت زمان:' . '⏰' . '. ' . $duration . PHP_EOL .'تعداد لایک : '.$musicLikes.'❤️🔥'.PHP_EOL . 'لینک دانلود: ' . '/dn_' . $music_id . PHP_EOL . PHP_EOL . '〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️' . PHP_EOL . PHP_EOL;
            $numbering++;
        }
        $tg->sendMessage($user_id, $msg);
        die();
    } elseif (strpos($text, 'next_') !== false) {
        $last_came_id = explode('_', $text)[1];
        $message_text = 'لیست آهنگ های پیدا شده 👇🏻🎵🎶' . PHP_EOL . PHP_EOL;
        $counter = $last_came_id;
        $last_search = $users->select($user_id)->last_search;
        $num = $musics->CountMusic($last_search);
        if ($last_came_id + $page_limit < $num) {
            $endponit = $last_came_id + $page_limit;
        } else {
            $endponit = $num;
        }
        $AllMusics = $musics->searchMusic($last_search);
        $numbering =$last_came_id+1;
        for ($counter; $counter < $endponit; $counter++) {
            $music_id = $AllMusics[$counter]['id'];
            $music_name = $AllMusics[$counter]['name'];
            $music_file_id = $AllMusics[$counter]['file_id'];
            $singer = $AllMusics[$counter]['singer'];
            $musicLikes = $AllMusics[$counter]['hearts'];
            $duration = gmdate("i:s", $AllMusics[$counter]['duration']);
            $message_text .= $numbering . '.' . $music_name . ' - ' . $singer . PHP_EOL.'مدت زمان:' . '⏰' . '. ' . $duration.PHP_EOL .'تعداد لایک : '.$musicLikes.'❤️🔥'. PHP_EOL . 'لینک دانلود: ' . '/dn_' . $music_id . PHP_EOL . PHP_EOL . '〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️' . PHP_EOL . PHP_EOL;
            $numbering++;
        }
        if ($num > $last_came_id + $page_limit) {
            $buttons = [
                [
                    ['text' => 'قبلی👈', 'callback_data' => 'prev_' . $endponit],
                    ['text' => '👉بعدی', 'callback_data' => 'next_' . $endponit],
                ],
            ];
        } else {
            $buttons = [
                [
                    ['text' => 'قبلی👈', 'callback_data' => 'prev_' . $endponit],
                ],
            ];
        }

        $murkup = $tg->InlineKeyboardMarkup($buttons);
        $tg->edit_markap($user_id, $message_id, $message_text, $murkup);
        die();
    } elseif (strpos($text, 'prev_') !== false) {
        $data = explode('_', $text); //11
        $last_id = $data[1];
        $last_search = $users->select($user_id)->last_search;
        $num = $musics->CountMusic($last_search);
        
        if ($last_id % $page_limit == 0) {
            $endponit = $last_id - $page_limit; //4
        } else {
            $endponit = $last_id - ($last_id % $page_limit); //4
        }
        $message_text = 'لیست آهنگ های پیدا شده 👇🏻🎵🎶' . PHP_EOL . PHP_EOL;
        $AllMusics = $musics->searchMusic($last_search);
        $cnt = $endponit - $page_limit;
        $numbering == $cnt+1;
        for ($i = $cnt; $i < $endponit; $i++) {
            $music_id = $AllMusics[$i]['id'];
            $music_name = $AllMusics[$i]['name'];
            $music_file_id = $AllMusics[$i]['file_id'];
            $singer = $AllMusics[$i]['singer'];
            $musicLikes = $AllMusics[$i]['hearts'];
            $duration = gmdate("i:s", $AllMusics[$i]['duration']);
            $message_text .= $numbering . '.' . $music_name . ' - ' . $singer . PHP_EOL.'مدت زمان:' . '⏰' . '. ' . $duration.PHP_EOL .'تعداد لایک : '.$musicLikes.'❤️🔥'. PHP_EOL . 'لینک دانلود: ' . '/dn_' . $music_id . PHP_EOL . PHP_EOL . '〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️' . PHP_EOL . PHP_EOL;
            $numbering++;
        }

        if ($endponit - $page_limit > 0) {
            $buttons = [
                [
                    ['text' => 'قبلی👈', 'callback_data' => 'prev_' . $endponit],
                    ['text' => '👉بعدی', 'callback_data' => 'next_' . $endponit],
                ]
            ];
        } else {
            $buttons = [
                [
                    ['text' => '👉بعدی', 'callback_data' => 'next_' . $endponit]
                ]
            ];
        } 
        $murkup = $tg->InlineKeyboardMarkup($buttons);
        $tg->edit_markap($user_id, $message_id, $message_text, $murkup);
        die();

    }elseif (strpos($text,'dn_') !== false) {
        $music_id = explode('_',$text)[1];
        $musicFromdb = $musics ->select($music_id);
        $buttons = [
            [
                ['text' => '❤️'.'('.$musicFromdb->hearts.')', 'callback_data' => 'heart_' . $music_id],
            ]
        ];
        $murkup = $tg->InlineKeyboardMarkup($buttons);
        $titleForMusic =$musicFromdb->name.'❤️🎸🏹'.PHP_EOL.PHP_EOL.'@musichome404 ⚡️';
        $tg->sendAudio($user_id,$musicFromdb->file_id,$titleForMusic,$musicFromdb->duration,$musicFromdb->singer,$musicFromdb->name,$message_id,$murkup);
        die();
    } elseif (strpos($text,'heart_')!==false) {
        $musicId = explode('_',$text)[1];
        $musicFromdb = $musics ->select($musicId);
        $newValue = $musicFromdb->hearts + 1;
        $musics ->update($musicId,'hearts',$newValue);
        $buttons = [
            [
                ['text' => '❤️'.'('.$newValue.')', 'callback_data' => 'dislike_' . $musicId],
            ]
        ];
        $murkup2 = $tg->InlineKeyboardMarkup($buttons);
        $murkup = $tg->editMessageReplyMarkup($user_id,$message_id,$murkup2);
        die();
    } elseif (strpos($text,'dislike')!==false) {
        $musicId = explode('_',$text)[1];
        $musicFromdb = $musics ->select($musicId);
        $newValue = $musicFromdb->hearts - 1;
        $musics ->update($musicId,'hearts',$newValue);
        $buttons = [
            [
                ['text' => '❤️'.'('.$newValue.')', 'callback_data' => 'heart_' . $musicId],
            ]
        ];
        $murkup2 = $tg->InlineKeyboardMarkup($buttons);
        $murkup = $tg->editMessageReplyMarkup($user_id,$message_id,$murkup2);
        die();
    }else{
        $AllMusics = $musics->searchMusic($text);
        if (empty($AllMusics)) {
            $tg->sendMessage($user_id, 'متاسفم ! 😔🥀' . PHP_EOL . PHP_EOL . 'متاسفانه همچین آهنگیرو ما توی آهنگامون نداریم🚶‍♂️' . PHP_EOL . PHP_EOL . 'اگه پیداش کردی از طریق پشتیبانی بفرستش برای ادمینامون تا به اسم خودت بزاریمش توی ربات و کانال😍😍', $tg->replyKeyboardMarkup($startKeyboard, true, false));
            die();
        } else {
            $message_text = 'لیست آهنگ های پیدا شده 👇🏻🎵🎶' . PHP_EOL . PHP_EOL;
            $num = $musics->CountMusic($text);
            $cnt = $num < $page_limit ? $num : $page_limit;
            $numbering = 1;
            $counter = 0;
            
            for ($counter; $counter < $cnt; $counter++) {
                $music_id = $AllMusics[$counter]['id'];
                $music_name = $AllMusics[$counter]['name'];
                $music_file_id = $AllMusics[$counter]['file_id'];
                $singer = $AllMusics[$counter]['singer'];
                $musicLikes = $AllMusics[$counter]['hearts'];
                $duration = gmdate("i:s", $AllMusics[$counter]['duration']);
                $message_text .= $numbering . '.' . $music_name . ' - ' . $singer . PHP_EOL.'مدت زمان:' . '⏰' . '. ' . $duration.PHP_EOL .'تعداد لایک : '.$musicLikes.'❤️🔥' . PHP_EOL . 'لینک دانلود: ' . '/dn_' . $music_id . PHP_EOL . PHP_EOL . '〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️' . PHP_EOL . PHP_EOL;
                $numbering++;
            }
            $buttons = [
                [
                    ['text' => '👉بعدی', 'callback_data' => 'next_10'],
                ],
            ];
            $users->update($user_id, 'last_search', $text);
            $murkup = $tg->InlineKeyboardMarkup($buttons);
            $tg->sendMessage($user_id, $message_text, $murkup);
            die();
        }

    }

}

//contact menu codes ...

if ($position == 'messageToAdmin') {
    if ($text == 'بازگشت🔙') {
        $helpers->backToMainMenu($user_id, $startKeyboard);

    } else {
        if ($text == 'جستجوی موزیک🔍' or $text == 'بهترین ها⭐️ ' or $text == 'جدید ترین ها🆕' or $text == 'ارتباط با پشتیبانی👨‍🦰') {
            $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, true);
            $tg->sendMessage($user_id, 'دستور نا معتبر! ' . PHP_EOL . PHP_EOL . 'برای بازگشت به منوی اصلی روی دکمه زیر کلیک کنید👇🏻👇🏻', $reply_markup);
            die();
        } else {
            $tg->sendMessage($main_admin, $text);
            $tg->sendMessage($user_id, 'پیام شما با موفقیت به دست ادمین رسید');
            $helpers->backToMainMenu($user_id, $startKeyboard);

        }
    }
}

//admin feature codes...

if ($position == 'admin') {
    if ($user_id != $main_admin) {
        die();
    }
    if ($text == 'اضافه کردن موزیک🎸') {
        $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, true);
        $tg->sendMessage($user_id, 'لطفا آهنگ یا آهنگ هایی که میخاید به لیست آهنگ های ربات اضافه شوند را بفرستید', $reply_markup);
        $users->update($user_id, 'position', 'sendMusic');
        die();
    }
    if ($text == 'نعداد کاربران ربات🏹') {
        $countNumbers = $users->countRows();
        $tg->sendMessage($user_id, 'تعداد کاربران ربات تا الان: ' . $countNumbers);
        die();
    }
    if ($text == 'بن کردن کاربر🚫') {
        $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, false);
        $tg->sendMessage($user_id, 'لطفا یوزرنیم یا آیدی عددی کاربر مورد نظر را ارسال کنید کنید', $reply_markup);
        $users->update($user_id, 'position', 'banUser');
        die();
    }
    if ($text == 'آزاد کردن✅') {
        $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, false);
        $tg->sendMessage($user_id, 'لطفا یوزرنیم یا آیدی عددی کاربر مورد نظر را ارسال کنید کنید', $reply_markup);
        $users->update($user_id, 'position', 'unbanUser');
        die();
    }
    if ($text == 'افزودن ادمین👩‍💼') {
        $reply_markup = $tg->replyKeyboardMarkup($backKeyboard, true, false);
        $tg->sendMessage($user_id, 'لطفا یوزرنیم ادمین جدید را ارسال کنید کنید', $reply_markup);
        $users->update($user_id, 'position', 'newAdmin');
        die();
    }
    if ($text == 'بازگشت🔙') {
        $helpers->backToMainMenu($user_id, $startKeyboard);
    }
} else
if ($position == 'sendMusic') {
    if ($text == 'بازگشت🔙') {
        $helpers->backToAdminMenu($user_id, $adminKeyboard);

    } else {
        if (empty($updates->message->audio)) {
            $tg->sendMessage($user_id, 'دستور نامعتبر❌');
            die();
        }
        $addMusic = $musics->insert_new_music($musicName, $music_file_id, $Music_duration, $user_id, $performer);
        if ($addMusic) {
            $tg->sendMessage($user_id, 'به لیست آهنگ های ربات اضافه شد✅', null, null, false, $message_id);
        } else {
            $tg->sendMessage($user_id, 'این آهنگ قبلا در ربات اضافه شده بود❌', null, null, false, $message_id);
        }
    }

} else
if ($position == 'banUser') {
    if ($text == 'بازگشت🔙') {
        $helpers->backToAdminMenu($user_id, $adminKeyboard);

    } else {
        if (is_numeric($text)) {
            if ($users->is_register($text)) {
                $users->update($text, 'isBanned', true);
                $tg->sendMessage($user_id, 'کاربر با موفقیت مسدود گردید✅' . PHP_EOL . 'برای بازگشت به منوی ادمین روی دکمه زیر کلیک کنید');
                die();
            } else {
                $tg->sendMessage($user_id, 'کاربر در لیست کاربران ربات وجود ندارد');
            }

        } else
        if (strpos($text, "@") !== false) {
            $ex = substr($text, 1);
            if (!empty($users->selectByUsername($ex))) {
                $users->updateWithUsername($ex, 'isBanned', true);
                $tg->sendMessage($user_id, 'کاربر با موفقیت مسدود گردید✅' . PHP_EOL . 'برای بازگشت به منوی ادمین روی دکمه زیر کلیک کنید');
                die();
            } else {
                $tg->sendMessage($user_id, 'کاربر در لیست کاربران ربات وجود ندارد');
            }

        } else {
            $tg->sendMessage($user_id, 'درخاست نامعتبر');
        }
    }
}
if ($position == 'unbanUser') {
    if ($text == 'بازگشت🔙') {
        $helpers->backToAdminMenu($user_id, $adminKeyboard);

    } else {
        if (is_numeric($text)) {
            if ($users->is_register($text)) {
                $users->update($text, 'isBanned', 0);
                $tg->sendMessage($user_id, 'کاربر با موفقیت باز شد✅' . PHP_EOL . 'برای بازگشت به منوی ادمین روی دکمه زیر کلیک کنید');
                die();
            } else {
                $tg->sendMessage($user_id, 'کاربر در لیست کاربران ربات وجود ندارد');
            }

        } else
        if (strpos($text, "@") !== false) {
            $ex = substr($text, 1);
            if (!empty($users->selectByUsername($ex))) {
                $users->updateWithUsername($ex, 'isBanned', 0);
                $tg->sendMessage($user_id, 'کاربر با موفقیت  باز شد✅' . PHP_EOL . 'برای بازگشت به منوی ادمین روی دکمه زیر کلیک کنید');
                die();
            } else {

                $tg->sendMessage($user_id, 'کاربر در لیست کاربران ربات وجود ندارد');
            }

        } else {
            $tg->sendMessage($user_id, 'درخاست نامعتبر');
        }
    }
}
if ($position == 'newAdmin') {
    if ($text == 'بازگشت🔙') {
        $helpers->backToAdminMenu($user_id, $adminKeyboard);

    }if (strpos($text, 'accsessing_') !== false) {
        $explode = explode('_', $text);
        $rule = $explode[1];
        $admin_user_id = $explode[2];
        if ($rule == 'music') {
            $dbAdmins->updateRight($admin_user_id, 'addMusic');
            $tg->editMessageReplyMarkup($user_id, $message_id, $tg->InlineKeyboardMarkup($helpers->murkubForAccessing($admin_user_id)));
            $tg->answer_query($callback_query_id, 'انجام شد');
            die();
        }
        if ($rule == 'banuser') {
            $dbAdmins->updateRight($admin_user_id, 'banuser');
            $tg->editMessageReplyMarkup($user_id, $message_id, $tg->InlineKeyboardMarkup($helpers->murkubForAccessing($admin_user_id)));
            $tg->answer_query($callback_query_id, 'انجام شد');
            die();
        }
        if ($rule == 'unbanuser') {
            $dbAdmins->updateRight($admin_user_id, 'unbanuser');
            $tg->editMessageReplyMarkup($user_id, $message_id, $tg->InlineKeyboardMarkup($helpers->murkubForAccessing($admin_user_id)));
            $tg->answer_query($callback_query_id, 'انجام شد');
            die();
        }
    }if ($text == 'done') {
        $tg->deleteMessage($user_id, $message_id);
        $helpers->backToAdminMenu($user_id, $adminKeyboard);
    } else {
        if (strpos($text, '@') === false) {
            $tg->sendMessage($user_id, 'لطفا نام کاربری را با @ وارد نمایید ');
            die();
        }
        $usernameWithoutAtsign = substr($text, 1);
        $userInfo = $users->selectByUsername($usernameWithoutAtsign);
        if (empty($userInfo)) {
            $tg->sendMessage($user_id, 'این کاربر هنوز ربات را استارت نزده یا یوزرنیم  اشتباه ارسال شده است');
            die();
        }
        if ($dbAdmins->is_register($userInfo->user_id)) {
            $inline_murkup = $tg->InlineKeyboardMarkup($helpers->murkubForAccessing($user_id));
            $tg->sendMessage($user_id, 'اختیارات ادمین را تنظیم کنید:', $inline_murkup);
            die();
        }
        $dbAdmins->insert_new_admin($userInfo->user_id);

        $inline_murkup = $tg->InlineKeyboardMarkup($helpers->murkubForAccessing($user_id));
        $tg->sendMessage($user_id, 'اختیارات ادمین را تنظیم کنید:', $inline_murkup);
        die();

    }
}

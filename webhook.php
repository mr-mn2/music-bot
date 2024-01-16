<?php
$token = '6134782747:AAGtEWKebus57EsEoiMLyRShBOgVAP1EpCg';
$url = 'https://ae28-185-245-85-6.ngrok-free.app' . '/Bots/music/bot.php';

include "vendor/autoload.php";
$bot = new \App\Telegram\telegramBot($token);
var_dump( $bot -> setWebhook($url));

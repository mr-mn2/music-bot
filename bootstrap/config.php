<?php
list($hostName,$user,$pass,$dbName) = ["localhost","mrmnproj_mr_mn2","Mohammad?1383","mrmnproj_hiddenchat"];
try {
    global $db;
    $db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4",$user,$pass);
    $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo 'successfully';
}catch (PDOException $error){
        echo $error -> getMessage();
        // echo "hdsfhdhf";
    exit();
}

const ROBAT_USERNAME = "musichome404_bot";
const BOT_TOKEN = "6194412522:AAFgDMihbDczABRpmkMSz24Adn0UGgeAEs0";//bot token
const TELEGRAM_INVITE = "http://t.me/".ROBAT_USERNAME."?start=";//your url
$main_admin = "5605789521";//Main admin5626427943//6075318717
$owner = 6020624208;//Owner

$chanells = ['-1001858049232'];
$channeUsername = "@musichome404";
$channel = "@gigatunes";
$channe2 = "@gigatunes";
$channe3 = "@gigatunes";
$channe4 = "@gigatunes";


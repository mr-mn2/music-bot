<?php

// $updats = json_decode(file_get_contents('result.txt'));

// var_dump(empty($updats->message->freom->id->id)); 
// $text = "@kasfds";
// $usernameWithoutAtsign = substr($text,1);
//echo $usernameWithoutAtsign;
list($hostName,$user,$pass,$dbName) = ["localhost","root","","music"];
            $db = new PDO("mysql:host=$hostName;dbname=$dbName;charset=utf8mb4",$user,$pass);
            $db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $string = 'Morteza ';
            
            
        echo gmdate("i:s", 234);
        
        
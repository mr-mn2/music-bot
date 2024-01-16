<?php
function put($data){
    file_put_contents('error.txt',$data);
}
function emoji($input): string
{
    if ($input==0)
        return "❌";
    else
        return "✅";
}
function putProductToTxt($data){
    file_put_contents('Data/unconfirmedProduct.txt',$data."____".FILE_APPEND);
}
function getProductFromTxt(): string{
    return file_get_contents('Data/unconfirmedProduct.txt');
}
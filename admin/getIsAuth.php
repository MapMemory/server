<?php

$TRUE_LOGIN = 'admin';
$TRUE_PASSWORD = 'admin';

function getIsAuth($login, $password)
{
    global $TRUE_LOGIN, $TRUE_PASSWORD;

    return (($TRUE_LOGIN == $login) && ($TRUE_PASSWORD == $password)) ? "True" : "False";
}

answer(getIsAuth($_GET['login'], $_GET['password']));

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

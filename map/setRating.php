<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';

function setRating($ip, $scheme, $id, $rating)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result_exist = mysqli_query($dbconn, "select exists(select `rating` from ratings where `ip`='${ip}' and `id_object`=${id} and `id_scheme`=${scheme}) as isHas;");

    if (!$result_exist->fetch_object()->isHas)
        mysqli_query($dbconn, "insert into ratings values(default, ${id}, ${scheme}, '${ip}', ${rating});") or die('Ошибка выполнения запроса к БД');

    $dbconn = null;

    answer();
}

setRating($_GET['ip'], $_GET['scheme'], $_GET['id'], $_GET['rating']);

function answer()
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
}

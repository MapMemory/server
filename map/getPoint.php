<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';

function requiredFormat($objects)
{
    $object = $objects->fetch_object();
    $result = "{\"lat\":$object->lat,\"long\":$object->long}";
    $objects = null;

    return $result;
}

function getMonuments($name)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result = mysqli_query(
        $dbconn,
        "select `lat`, `long` from (select `name`, `lat`, `long` from monuments
        union
        select `name`, `lat`, `long` from tables
        union
        select `new_name` as `name`, `start_lat` as `lat`, `start_long` as `long` from streets) as t1 where `name` Like '%${name}%' limit 1;"
    ) or die('Ошибка выполнения запроса к БД');

    $dbconn = null;

    return requiredFormat($result);
}

answer(getMonuments($_GET['name']));

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';

function requiredFormat($objects)
{
    $symbol = "]";
    $result = "[";

    while ($object = $objects->fetch_object()) {
        $result .= ((!isset($result[1])) ? '' : ',') . "{\"id\":$object->id,\"id_scheme\":3,\"old_name\":\"$object->old_name\",\"new_name\":\"$object->new_name\",\"description\":\"$object->description\",\"direction\":\"$object->direction\",\"rating\":\"$object->rating\",\"start_lat\":$object->start_lat,\"start_long\":$object->start_long,\"end_lat\":$object->end_lat,\"end_long\":$object->end_long}";
    }

    $objects = null;

    $result .= $symbol;

    return $result;
}

function getStreets($name)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result = mysqli_query($dbconn, "select `id`, `old_name`, `new_name`, `description`, (select `name` from directions where streets.`direction`=directions.`id_view` and 3=directions.`id_scheme`) as `direction`, (select round(avg(`rating`), 2) from ratings where ratings.`id_scheme`=3 and ratings.`id_object`=streets.`id`) as `rating`, `start_lat`, `start_long`, `end_lat`, `end_long` from streets where `new_name` Like '%$name%' or `old_name` Like '%$name%';") or die('Ошибка выполнения запроса к БД');

    $dbconn = null;

    return requiredFormat($result);
}

answer(getStreets($_GET['name']));

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

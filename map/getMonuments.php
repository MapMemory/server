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
        $result .= ((!isset($result[1])) ? '' : ',') . "{\"id\":$object->id,\"id_scheme\":1,\"name\":\"$object->name\",\"description\":\"$object->description\",\"direction\":\"$object->direction\",\"rating\":\"$object->rating\",\"lat\":$object->lat,\"long\":$object->long}";
    }

    $objects = null;

    $result .= $symbol;

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

    $result = mysqli_query($dbconn, "select `id`, `name`, `description`, (select `name` from directions where monuments.`direction`=directions.`id_view` and 1=directions.`id_scheme`) as `direction`, (select round(avg(`rating`), 2) from ratings where ratings.`id_scheme`=1 and ratings.`id_object`=monuments.`id`) as `rating`, `lat`, `long` from monuments where `name` Like '%$name%';") or die('Ошибка выполнения запроса к БД');

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

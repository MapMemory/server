<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';

function requiredFormatMonument($objects)
{
    $object = $objects->fetch_object();
    $result = "{\"id\":$object->id,\"id_scheme\":1,\"name\":\"$object->name\",\"description\":\"$object->description\",\"direction\":\"$object->direction\",\"lat\":$object->lat,\"long\":$object->long}";
    $objects = null;

    return $result;
}

function requiredFormatTable($objects)
{
    $object = $objects->fetch_object();
    $result = "{\"id\":$object->id,\"id_scheme\":2,\"name\":\"$object->name\",\"description\":\"$object->description\",\"direction\":\"$object->direction\",\"lat\":$object->lat,\"long\":$object->long}";
    $objects = null;

    return $result;
}

function requiredFormatStreet($objects)
{

    $object = $objects->fetch_object();
    $result =
        "{\"id\":$object->id,\"id_scheme\":3,\"old_name\":\"$object->old_name\",\"new_name\":\"$object->new_name\",\"description\":\"$object->description\",\"direction\":\"$object->direction\",\"start_lat\":$object->start_lat,\"start_long\":$object->start_long,\"end_lat\":$object->end_lat,\"end_long\":$object->end_long}";
    $objects = null;

    return $result;
}

function getAimObject($scheme, $id)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result = null;

    if ($scheme == 1) {
        $result = mysqli_query(
            $dbconn,
            "select `id`, `name`, `description`, (select `name` from directions where monuments.`direction`=directions.`id_view` and 1=directions.`id_scheme`) as `direction`, `lat`, `long` from monuments where `id`=${id};"
        ) or die('Ошибка выполнения запроса к БД');

        return requiredFormatMonument($result);
    }

    if ($scheme == 2) {
        $result = mysqli_query(
            $dbconn,
            "select `id`, `name`, `description`, (select `name` from directions where tables.`direction`=directions.`id_view` and 2=directions.`id_scheme`) as `direction`, `lat`, `long` from tables where `id`=${id};"
        ) or die('Ошибка выполнения запроса к БД');

        return requiredFormatTable($result);
    }


    if ($scheme == 3) {
        $result = mysqli_query(
            $dbconn,
            "select `id`, `old_name`, `new_name`, `description`, (select `name` from directions where streets.`direction`=directions.`id_view` and 3=directions.`id_scheme`) as `direction`, `start_lat`, `start_long`, `end_lat`, `end_long` from streets where `id`=${id};"
        ) or die('Ошибка выполнения запроса к БД');

        return requiredFormatStreet($result);
    }

    $dbconn = null;
    return 'error';
}

answer(getAimObject($_GET['scheme'], $_GET['id']));

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

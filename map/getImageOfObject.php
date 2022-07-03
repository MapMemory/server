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
        $result .= ((!isset($result[1])) ? '' : ',') . "\"http://localhost/map/media/$object->path\"";
    }

    $objects = null;

    $result .= $symbol;

    return $result;
}

function getMonuments($scheme, $id)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result = mysqli_query($dbconn, "select `path` from pictures where `id_scheme`=${scheme} and `id_view`=${id};") or die('Ошибка выполнения запроса к БД');

    $dbconn = null;

    return requiredFormat($result);
}

answer(getMonuments($_GET['scheme'], $_GET['id']));

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

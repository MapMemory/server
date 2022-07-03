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
        $result .= ((!isset($result[1])) ? '' : ',') . "{\"ip\":\"$object->ip\",\"rating\":$object->rating}";
    }

    $objects = null;

    $result .= $symbol;

    return $result;
}

function getRatingArray($id_scheme, $id_object)
{
    global $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

    $dbconn  = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

    $DATABASE = null;
    $HOST = null;
    $PORT = null;
    $USER = null;
    $PASSWORD = null;

    $result = mysqli_query($dbconn, "select ip, rating from ratings where id_scheme={$id_scheme} and id_object={$id_object};") or die('Ошибка выполнения запроса к БД');

    $dbconn = null;

    return requiredFormat($result);
}

answer(getRatingArray($_GET['id_scheme'], $_GET['id_object']));

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

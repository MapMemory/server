<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';
$dbconn = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

function deleteTables()
{
    try {
        global $dbconn;

        mysqli_query($dbconn, "delete from monuments;");
        mysqli_query($dbconn, "delete from tables;");
        mysqli_query($dbconn, "delete from streets;");
        mysqli_query($dbconn, "delete from directions;");
        mysqli_query($dbconn, "delete from pictures;");
        mysqli_query($dbconn, "delete from ratings;");

        return true;
    } catch (\Throwable $th) {
        return false;
    }
}

function clearMemory()
{
    try {
        global $dbconn, $DATABASE, $HOST, $PORT, $USER, $PASSWORD;

        $DATABASE = null;
        $HOST = null;
        $PORT = null;
        $USER = null;
        $PASSWORD = null;
        $dbconn = null;

        return true;
    } catch (\Throwable $th) {
        return false;
    }
}

if (deleteTables() && clearMemory())
    answer('true');
else
    answer('false');

function answer($msg)
{
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: GET");
    header("Content-type: text/plain;charset:utf-8");
    header("Cache-Control: public");
    print_r($msg);
}

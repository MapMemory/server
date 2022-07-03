<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';
$dbconn = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

function createBeckupDB()
{
    try {
        global $dbconn;

        $db =
            (object) [
                'monuments' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `name`, `description`, (select `name` from directions where monuments.`direction`=directions.`id_view` and 1=directions.`id_scheme`) as `direction`, `lat`, `long` from monuments;"), MYSQLI_ASSOC),
                'tables' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `name`, `description`, (select `name` from directions where tables.`direction`=directions.`id_view` and 2=directions.`id_scheme`) as `direction`, `lat`, `long` from tables;"), MYSQLI_ASSOC),
                'streets' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `old_name`, `new_name`, `description`, (select `name` from directions where streets.`direction`=directions.`id_view` and 3=directions.`id_scheme`) as `direction`, `start_lat`, `start_long`, `end_lat`, `end_long` from streets;"), MYSQLI_ASSOC),
                'directions' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `id_view`, `id_scheme`, `name` from directions;"), MYSQLI_ASSOC),
                'pictures' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `id_view`, `id_scheme`, `path` from pictures;"), MYSQLI_ASSOC),
                'ratings' => mysqli_fetch_all(mysqli_query($dbconn, "select `id`, `id_object`, `id_scheme`, `ip`, `rating` from ratings;"), MYSQLI_ASSOC)
            ];

        date_default_timezone_set('Europe/Minsk');
        file_put_contents('../backups/db/' . date('m_d_Y-h:i:s_a', time())  . '.json', json_encode($db), 1);

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

if (createBeckupDB() && clearMemory())
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

<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';
$dbconn = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

function setDataToTables($points)
{
    try {
        global $dbconn;

        $temp_db = json_decode($points);

        $id =
            (object) [
                'monuments' => 0,
                'tables' => 0,
                'streets' => 0,
                'directions' => (object) [
                    'all' => 0,
                    'monuments' => 0,
                    'tables' => 0,
                    'streets' => 0
                ],
                'pictures' => 0,
                'ratings' => 0
            ];

        foreach ($temp_db->monuments as $monument) {
            $id->monuments++;

            $direction_info = mysqli_fetch_assoc(mysqli_query($dbconn, "select exists(select `id` from directions where '{$monument->direction}'=directions.`name` and 1=directions.`id_scheme`) as result;"));

            if ($direction_info['result']) {
                $direction_id = mysqli_fetch_assoc(mysqli_query($dbconn, "select `id` as result from directions where '{$monument->direction}'=directions.`name` and 1=directions.`id_scheme`;"));
                mysqli_query($dbconn, "insert into monuments values({$id->monuments}, '{$monument->name}', '{$monument->description}', {$direction_id['result']}, {$monument->lat}, {$monument->long});") or die('Ошибка выполнения запроса к БД');
            } else {
                $id->directions->monuments++;
                mysqli_query($dbconn, "insert into directions values({$id->directions->all}, {$id->directions->monuments}, 1, '{$monument->direction}');") or die('Ошибка выполнения запроса к БД');
                mysqli_query($dbconn, "insert into monuments values({$id->monuments}, '{$monument->name}', '{$monument->description}', {$id->directions->monuments}, {$monument->lat}, {$monument->long});") or die('Ошибка выполнения запроса к БД');
            }

            foreach ($monument->ratingArray as $ratingObj) {
                $id->ratings++;
                mysqli_query($dbconn, "insert into ratings values({$id->ratings}, {$id->monuments}, 1, '{$ratingObj->ip}', {$ratingObj->rating});");
            }

            foreach ($monument->images as $key => $image) {
                if (substr($image, 0, 27) !== 'http://localhost/map/media/') {
                    $id->pictures++;
                    $mainFileFolder = '../map/media/';
                    $fileDir = '1-' . $id->monuments . '_';

                    date_default_timezone_set('Europe/Minsk');
                    $fileName = date('m_d_Y-h:i:s_a', time()) . '_' . md5($id->pictures + $key)  . '.jpg';

                    file_put_contents($mainFileFolder . $fileDir . $fileName, base64_decode($image));

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->monuments}, 1, '$fileDir$fileName');");
                } else {
                    $file = substr($image, 27);

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->monuments}, 1, '$file');");
                }
            }
        }

        foreach ($temp_db->tables as $table) {
            $id->tables++;

            $direction_info = mysqli_fetch_assoc(mysqli_query($dbconn, "select exists(select `id` from directions where '{$table->direction}'=directions.`name` and 2=directions.`id_scheme`) as result;"));

            if ($direction_info['result']) {
                $direction_id = mysqli_fetch_assoc(mysqli_query($dbconn, "select `id` as result from directions where '{$table->direction}'=directions.`name` and 2=directions.`id_scheme`;"));
                mysqli_query($dbconn, "insert into tables values({$id->tables}, '{$table->name}', '{$table->description}', {$direction_id['result']}, {$table->lat}, {$table->long});") or die('Ошибка выполнения запроса к БД');
            } else {
                $id->directions->tables++;
                mysqli_query($dbconn, "insert into directions values({$id->directions->all}, {$id->directions->tables}, 2, '{$table->direction}');") or die('Ошибка выполнения запроса к БД');
                mysqli_query($dbconn, "insert into tables values({$id->tables}, '{$table->name}', '{$table->description}', {$id->directions->tables}, {$table->lat}, {$table->long});") or die('Ошибка выполнения запроса к БД');
            }

            foreach ($table->ratingArray as $ratingObj) {
                $id->ratings++;
                mysqli_query($dbconn, "insert into ratings values({$id->ratings}, {$id->tables}, 2, '{$ratingObj->ip}', {$ratingObj->rating});");
            }

            foreach ($table->images as $key => $image) {
                if (substr($image, 0, 27) !== 'http://localhost/map/media/') {
                    $id->pictures++;
                    $mainFileFolder = '../map/media/';
                    $fileDir = '1-' . $id->tables . '_';

                    date_default_timezone_set('Europe/Minsk');
                    $fileName = date('m_d_Y-h:i:s_a', time()) . '_' . md5($id->pictures + $key)  . '.jpg';

                    file_put_contents($mainFileFolder . $fileDir . $fileName, base64_decode($image));

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->tables}, 2, '$fileDir$fileName');");
                } else {
                    $file = substr($image, 27);

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->tables}, 2, '$file');");
                }
            }
        }

        foreach ($temp_db->streets as $street) {
            $id->streets++;

            $direction_info = mysqli_fetch_assoc(mysqli_query($dbconn, "select exists(select `id` from directions where '{$street->direction}'=directions.`name` and 3=directions.`id_scheme`) as result;"));

            if ($direction_info['result']) {
                $direction_id = mysqli_fetch_assoc(mysqli_query($dbconn, "select `id` as result from directions where '{$street->direction}'=directions.`name` and 3=directions.`id_scheme`;"));
                mysqli_query($dbconn, "insert into streets values({$id->streets}, '{$street->old_name}', '{$street->new_name}', '{$street->description}', {$direction_id['result']}, {$street->start_lat}, {$street->start_long}, {$street->end_lat}, {$street->end_long});") or die('Ошибка выполнения запроса к БД');
            } else {
                $id->directions->streets++;
                mysqli_query($dbconn, "insert into directions values({$id->directions->all}, {$id->directions->streets}, 3, '{$street->direction}');") or die('Ошибка выполнения запроса к БД');
                mysqli_query($dbconn, "insert into streets values({$id->streets}, '{$street->old_name}', '{$street->new_name}', '{$street->description}', {$id->directions->streets}, {$street->start_lat}, {$street->start_long}, {$street->end_lat}, {$street->end_long});") or die('Ошибка выполнения запроса к БД');
            }

            foreach ($street->ratingArray as $ratingObj) {
                $id->ratings++;
                mysqli_query($dbconn, "insert into ratings values({$id->ratings}, {$id->streets}, 3, '{$ratingObj->ip}', {$ratingObj->rating});");
            }

            foreach ($street->images as $key => $image) {
                if (substr($image, 0, 27) !== 'http://localhost/map/media/') {
                    $id->pictures++;
                    $mainFileFolder = '../map/media/';
                    $fileDir = '1-' . $id->streets . '_';

                    date_default_timezone_set('Europe/Minsk');
                    $fileName = date('m_d_Y-h:i:s_a', time()) . '_' . md5($id->pictures + $key)  . '.jpg';

                    file_put_contents($mainFileFolder . $fileDir . $fileName, base64_decode($image));

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->streets}, 3, '$fileDir$fileName');");
                } else {
                    $file = substr($image, 27);

                    mysqli_query($dbconn, "insert into pictures values({$id->ratings}, {$id->streets}, 3, '$file');");
                }
            }
        }

        mysqli_close($dbconn);
        $dbconn = null;

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

if (setDataToTables(file_get_contents("php://input")) && clearMemory())
    answer('true');
else
    answer('false');

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

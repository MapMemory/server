<?php

$DATABASE = 'mapmemory';
$HOST = '172.17.0.1';
$PORT = 3306;
$USER = 'root';
$PASSWORD = 'root';
$dbconn = mysqli_connect($HOST, $USER, $PASSWORD, $DATABASE, $PORT) or die('Ошибка подключения к БД');

function moveFilesFromFolder($workDir, $beckupDir)
{
    try {
        global $dbconn;

        $di = new RecursiveDirectoryIterator($workDir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($ri as $file) {
            $fileName = mb_split('/', $file)[3];

            $isWas = false;
            $db_pictures = mysqli_query($dbconn, "select path from pictures;");

            while ($row = mysqli_fetch_assoc($db_pictures)) {
                if ($row['path'] == $fileName)
                    $isWas = true;
            }

            if (!$isWas)
                rename($file, $beckupDir . $fileName);
        }

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

if (moveFilesFromFolder('../map/media/', '../backups/media/') && clearMemory())
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

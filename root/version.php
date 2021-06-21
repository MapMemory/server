<?php

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET");
header("Content-type: text/plain;charset:utf-8");
header("Cache-Control: public");
print_r('0.0.1');

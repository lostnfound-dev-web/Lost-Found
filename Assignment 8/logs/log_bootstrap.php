<?php
date_default_timezone_set('Europe/Berlin');

/* === Log file paths === */
$access_file = '/home/anmata/public_html/logs/lf_access.log';
$error_file  = '/home/anmata/public_html/logs/lf_error.log';

/* === Log every request (Apache style) === */
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? '-';
$ip  = $_SERVER['REMOTE_ADDR'] ?? '-';
$met = $_SERVER['REQUEST_METHOD'] ?? '-';
$uri = $_SERVER['REQUEST_URI'] ?? '-';
$ts  = date('d/M/Y:H:i:s O');
$line = "$ip - - [$ts] \"$met $uri HTTP/1.1\" 200 0 \"-\" \"$ua\"\n";
@file_put_contents($access_file, $line, FILE_APPEND);

/* === Log PHP warnings/errors === */
set_error_handler(function($no,$str,$file,$line) use ($error_file){
    $ts = date('[D M d H:i:s Y]');
    $msg = "$ts [php:error] [client ".($_SERVER['REMOTE_ADDR']??'unknown')."] $str in $file:$line\n";
    @file_put_contents($error_file, $msg, FILE_APPEND);
    return false; // continue normal error handling
});

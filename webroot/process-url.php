<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Balsama\BostonPoliceReportParser\BpdParse;

header('Content-Type: text/plain; charset=utf-8');

$file_data = file_get_contents($_POST['urlfile']);
$name = md5($_POST['urlfile']) . '.pdf';
file_put_contents(sys_get_temp_dir() . '/' . $name, $file_data);

$bpdParse = new BpdParse(sys_get_temp_dir() . '/' . $name);
echo (string) $bpdParse;
exit(0);
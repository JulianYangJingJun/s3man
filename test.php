<?php
require_once 'vendor/autoload.php'; // 加载自动加载文件



use s3man\Extsms;
$aa = new Extsms();
$aa->tel = "13858787110";
$aa->msg = "22222222xxxxx";
print_r($aa->sendMsg());
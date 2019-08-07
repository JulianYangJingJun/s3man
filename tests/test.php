<?php
require_once '../vendor/autoload.php'; // 加载自动加载文件



use s3man\Extsms;


$aa = new Extsms();
$aa->tel = "13858787110";
$aa->msg = "中国国国国国国国国国国国国国男国国国男";
print_r($aa->sendMsg());

// use services\SendMsg;
// try {
//     $aa = new SendMsg();
//     $aa->tel = 13858787110;
//     $aa->msg = '中国人中国人asdfasf';
//     $aa->api_tag = '创建并关联mtm单';
//     $aa->addOneToSmsLog();
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }

// die();
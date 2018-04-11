<?php
/**
 * Created by PhpStorm.
 * User: 融媒中心-1
 * Date: 2018/3/30
 * Time: 17:58
 *  PHP扩展 ：php_pdo_mysql
 */
date_default_timezone_set("Asia/Shanghai");
$dbms='mysql';     //数据库类型
$host='192.168.20.104'; //数据库主机名
$dbName='weixin';    //使用的数据库
$charset='utf8';
define("THANKS","-274");
$pdo = null;
$res=array("data"=>array());
//活动开始时间
$time = strtotime("2018-04-08 13:25:00");
try {
    $pdo = new PDO("$dbms:host=$host;dbname=$dbName;charset=$charset","wuliugang","wuliugang");

} catch (PDOException $e) {
    $res["code"] = 500;
    $res["msg"] = '服务器异常！！！';
    error_log('Connection failed: ' . $e->getMessage());

    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}
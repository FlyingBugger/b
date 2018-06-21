<?php
const SALT='prize';//密码加盐
const PAGE_SIZE=10;//表格默认每页条数
const DEBUG = false;
const USER_TABLE = 'weixin.`tp_newuser`';//微信用户表
const CONFIG=[
  'baseUrl'=>'http://localhost',
  'db'=>[
    'database_type' => 'mysql',
    'database_name' => 'prize',
    'server' => '192.168.20.104',
    'username' => 'wuliugang',
    'password' => 'wuliugang',
    'charset' => 'utf8'
  ]
];
const ERROR=[
  '500'=>'服务器异常',
  '400'=>'密码错误！',
  '300'=>'登录超时！',
  '405'=>'没有访问权限！'
];

date_default_timezone_set('Asia/Shanghai');

?>
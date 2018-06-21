<?php
namespace Common;
use Config\Base as Base;
class User extends Base{
  public function updatepwd(){
    $old = md5($_POST['oldpwd'].SALT);
    $pwd = md5($_POST['pwd'].SALT);
    $table = 'admin';
    foreach($_GET['role'] as $role){
      if('business'==$role){
         $table = 'business';
      }
    }
    $index = $this->update($table,['password'=>$pwd],['id'=>$_GET['userId'],'password'=>$old]);
    if(!$index){
      echo rs_error("密码错误");
      return;
    }
    echo rs($index);
  }
  //修改微信用户终身中奖上限
  public function editUser(){
    $a = false;
    foreach($_GET['role'] as $role){
      if('admin'==$role){
         $a = true;
         break;
      }
    }
    if(!$a) return;
    if(isset($_GET['t'])&&trim($_GET['t'])!=''){
      $b = $this->update('config',['val'=>$_GET['t']],['code'=>'user_times']);
    }
    
    echo rs($b);
  }
  public function userTimes(){
    $val = $this->getOne('config','val',['code'=>'user_times']);
    echo rs($val);
  }
}
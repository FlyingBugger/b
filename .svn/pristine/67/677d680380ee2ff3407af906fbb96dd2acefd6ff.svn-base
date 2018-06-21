<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:GET');
$a = strripos($_SERVER['REQUEST_URI'],'index.php')+10;
$action=explode('?',substr($_SERVER['REQUEST_URI'],$a))[0];
$array = explode('/',$action);
$count = count($array);
if($count<2){
  exit;
}
define('APP',realpath('./app/'));
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");
require_once 'config/config.php';
$C= ucfirst($array[0]).'\\'.ucfirst($array[1]);
$M = empty($array[2]) ? 'index':$array[2];

include 'app/xssfiter.php';
include 'app/common.php';
require BASE_PATH.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

if(class_exists($C) && method_exists($C, $M)){
  if($array[1]!='login'&&$array[1]!='api'){
    $head = get_all_headers();
    $jwt= $head['token'];
    if(empty($jwt)){
      echo rs_error(ERROR['405'],405);
      exit;
    }
    $token = new \Config\Token();
    try{
      $anth = $token->refresh($jwt);
      if($anth['code']!=200){
        echo rs_error($anth['code'],$anth['code']);
        exit;
      }
      $mark = false;
      foreach($anth['token']['role'] as $role){
        if($role==$array[0]){
          $mark = true;
        }
      }
      if(!$mark){
        echo rs_error(ERROR['405'],405);
        exit;
      }
      $_GET['userId'] = $anth['token']['userId'];
      $_GET['role'] = $anth['token']['role'];
    }catch(Firebase\JWT\BeforeValidException $e){
      echo rs_error('请稍后...');
      exit;
    }catch(Firebase\JWT\ExpiredException $e){
      echo rs_error(ERROR['300'],300);
      exit;
    }catch(Exception $e){
      
      echo rs_error(ERROR['500']);
      exit;
    }
    
  }
  try{
    $cls = new $C();
    $cls->$M();
  }catch(Exception $e){
    if(!DEBUG){
      error_log($e->getMessage());
      echo rs_error(ERROR['500']);
    }else{
      throw $e;
    }
    exit;
  }
  
}else{
  echo '没有找到页面';  
    exit; 
}


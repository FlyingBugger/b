<?php
namespace Config;
use \Firebase\JWT\JWT;
const KEY = 'admin2018';
class Token {
  public function encode($userId,$role){
    $token = array(
      "iss" => CONFIG['baseUrl'],
      "iat" => time(),
      "exp"=>time()+3600*2,//6 hours
      "userId"=>$userId,
      "role"=>$role
    );
    $jwt = JWT::encode($token, KEY);
    return $jwt;
  }
  public function decode($jwt){
    $decoded = JWT::decode($jwt, KEY, array('HS256'));
    return $this->object_to_array($decoded);
  }
  public function refresh($jwt){
    $decoded = $this->decode($jwt);
    $exp = $decoded['exp'];
    if($exp-time()<0){
      return ['code'=>300];
    }
    if($exp-time()<3600*1.5){
      $decoded['exp']=time()+3600*2;
      $jwt = JWT::encode($decoded, KEY);
      $_POST['re_jwt'] = $jwt;
      return ['code'=>200,'token'=>$decoded];
    }
    return ['code'=>200,'token'=>$decoded];
  }
  private function object_to_array($obj) {
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)$this->object_to_array($v);
        }
    }
     return $obj;
  }
}
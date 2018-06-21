<?php
namespace Admin;
use Config\Base as Base;
use Config\Token as Token;
class Login extends Base{
  static $prize;
  static $openId;
  static $noprize;
  public function index(){
    $name = isset($_POST['username']) ? $_POST['username'] : null;
    $pass = isset($_POST['password']) ? $_POST['password'] : null;
    $role = isset($_POST['role']) ? $_POST['role'] : null;
    if (!$name || !$pass||!$role){
      echo rs_error('请输入账号和密码！');
      return;
    }
    $hash = md5($pass . SALT);
   
    $user = $this->getOne($role, '*',['username' => $name,'password'=>$hash,'status'=>0]);
    $user['password']=null;
    if(!isset($user['id'])){
      echo rs_error(ERROR['400'],400);
      return;
    }
    
    $t = new Token();
    $jwt=$t->encode($user['id'],[$role,'common']);
    echo rs($user,null,200,$jwt);
    $loginDate = date('Y-m-d H-i-s');
    $ip = get_ip();
    $this->update($role,['login_date'=>$loginDate,'ip'=>$ip],['id'=>$user['id']]);
  }
  //抽奖接口
  public function checkPrize(){
    
    if(empty($_GET['openid'])){
      echo rs_error('openid不存在！');
      return;
    }
    self::$openId = $_GET['openid'];
    $activityId = null;
    if(empty($_GET['ac'])||empty($_GET['c'])){
      echo rs_error('参数有误！');
      return;
    }else {
      $activityId = $this->unlock_url($_GET['ac']);
      $activityId =(Integer)$activityId;
    }
    
    //查询抽奖次数
    $record = $this->wxuserTimes($_GET['openid'],$activityId);
    if($record['times']<=$record['a']&&$record['a']){
      echo rs_error('你的抽奖次数用完了，请下次再来！');
      return;
    }
    //$fp = fopen(APP.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'lock.txt','r');
    //flock($fp,LOCK_EX);
    //奖品信息
    $prizeData =self::$db->query("SELECT prize.id,prize.business_id,prize.activity_id,prize.v FROM prize JOIN  activity 
              ON prize.activity_id = activity.id AND activity.`status` = 0
              WHERE NOW() BETWEEN activity.start_date AND activity.close_date
              AND prize.`status` = 0 AND activity_id = $activityId")->fetchAll();
    if(count($prizeData)==0){
      echo rs_error('奖品已抽完，请下次再来！');
      return ;
    }
    
    //抽中的奖品
    self::$prize= $this->getRand($prizeData);
    $limtNum= $this->unlock_url($_GET['uli']);
    self::$prize['times'] = $record['times']-$record['a']-1;
    if((int)$limtNum<=0){
      self::$prize['business_id']=null;
    }
    self::$db->action(function($db){
      try{
        $a=1;$status = 0;
        if(!empty(self::$prize['business_id'])){//抽中奖品
          $a =$db->update('prize',['num[+]'=>1],['id'=>self::$prize['id'],'amount[>]'=>'num'])->rowCount();
        }
        $limt= $this->unlock_url($_GET['c']);
        $l= $this->dayLimit();
        if($limt>0&&$l>$limt){
          self::$prize=self::$noprize;
        }
        
        if($a<1){//奖品抽完
          self::$prize=self::$noprize;
        }
        if( self::$prize['business_id']==null){
          $status =2;
        }
        $b =$db->insert('prize_record',
        ['id'=>uuid(),'openid'=>self::$openId,
        'business_id'=>self::$prize['business_id'],
        'activity_id'=>self::$prize['activity_id'],'prize_id'=>self::$prize['id'],'status'=>$status,'creatime'=>date('Y-m-d H-i-s')
        ])->rowCount();
        if($a+$b<2){
          echo rs(self::$prize);
        }else{
          echo rs(self::$prize);
        }
        return true;
      }catch (Exception $e){
        echo rs_error('服务器异常！');
        return false;
      }
    });
    //flock($fp,LOCK_UN);
    //fclose($fp);
  }
  public function hasUser($openid){
    $table = USER_TABLE;
   // $db = new \Medoo\Medoo($config);
    $mark =self::$db->query("SELECT EXISTS(SELECT 1 FROM ".$table." WHERE openid = '$openid')")->fetch();
    
    return $mark[0];
  }
  //获取本次抽奖奖品
  public function getActivity(){
    
    $activityId = null;
    if(empty($_GET['activity_id'])){
      $activityId = "(SELECT max(id) FROM activity where `status` =0 AND NOW() BETWEEN start_date AND close_date)";
    }else if(is_numeric($_GET['activity_id'])){
      $activityId =(Integer)$_GET['activity_id'];
    }else{
      echo rs_error('参数有误！');
      return;
    }
    $prizeData =self::$db->query("SELECT prize.id,prize.activity_id,prize.name,activity.day_limit,
              prize.num,activity.name as ac FROM prize JOIN  activity 
              ON prize.activity_id = activity.id AND activity.`status` = 0
              WHERE NOW() BETWEEN activity.start_date AND activity.close_date
              AND prize.`status` = 0  AND activity_id = $activityId")->fetchAll();
    return $prizeData;
  }
  //用户查询抽奖奖品
  public function getPrizeByUserId(){
    self::$db->query("UPDATE prize_record SET `status`=2 WHERE `status`=0 AND prize_id IN (SELECT id FROM prize WHERE NOW() > end_time)")->fetchAll();
    if(empty($_GET['openid'])){
      echo rs_error('openid不存在！');
      return;
    }
    $openId = $_GET['openid'];
    $sql = "SELECT r.id,p.`name` prize,p.start_time,p.end_time,r.status,b.`name` 
    FROM prize_record r JOIN prize p ON r.prize_id = p.id JOIN business b ON r.business_id = b.id
    WHERE r.openid='$openId'";
    
    $prizeData = self::$db->query($sql)->fetchAll();
    return $prizeData;
  }
  //每日抽中量
  public function dayLimit(){
     $count= self::$db->query('SELECT count(*) a FROM prize_record WHERE  to_days(creatime) = to_days(now()) AND business_id is not null')->fetch();
     return (int)$count['a'];
  }
  //用户抽中奖品次数限制
  public function limitByOpenid($openid){
    $count= self::$db->query("SELECT count(*) a FROM prize_record WHERE openid='$openid' AND business_id is not null")->fetch();
    $val = $this->getOne('config','val',['code'=>'user_times']);
    if($val==0){//0则默认无线大
      return 1;
    }
    return $val - $count['a'];
  }
  public function wxuserTimes($openId,$activity_id=''){//用户每次活动的抽奖次数
    $times = $this->getOne('activity','times',['id'=>$activity_id]);
    $record = self::$db->query("SELECT count(*) a FROM prize_record WHERE openid='$openId' and activity_id = '$activity_id'")->fetch();
    $record['times'] = $times;
    return $record;
  }
  public function is_weixin() { 
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { 
        return true; 
    } return false; 
  }
  //加密函数
  public function lock_url($string)
{
  $code = '';$string .='';
  $key = substr(md5($_SERVER['HTTP_USER_AGENT']), 8, 18);
  $keyLen = strlen($key);
  $strLen = strlen($string);
  for ($i = 0; $i < $strLen; $i++) {
    $k = $i % $keyLen;
    $code .= $string[$i] ^ $key[$k];
  }
  return base64_encode($code);
}
//解密函数
private function unlock_url($string)
{
  $string = base64_decode($string);
  $code = '';
  $key = substr(md5($_SERVER['HTTP_USER_AGENT']), 8, 18);
  $keyLen = strlen($key);
  $strLen = strlen($string);
  for ($i = 0; $i < $strLen; $i++) {
    $k = $i % $keyLen;
    $code .= $string[$i] ^ $key[$k];
  }
  return $code;
}
  //概率算法
  private function getRand($prizeData){
    $prize = null;
    $prize_arr=[];
    foreach($prizeData as $val){
      $prize_arr[$val['id']] = $val['v'];
      if(empty($val['business_id'])){
        self::$noprize = $val;
      }
    }
    $proSum = array_sum($prize_arr);
    //概率数组循环   
    foreach ($prize_arr as $key => $proCur) {  
      $randNum = mt_rand(1, $proSum);  
      if ($randNum <= $proCur) {  
          $prize = $key;  
          break;  
      } else {  
          $proSum -= $proCur;  
      }  
    }
    foreach($prizeData as $val){
      if($prize==$val['id']){
        return $val;
      }
    } 
  }
}
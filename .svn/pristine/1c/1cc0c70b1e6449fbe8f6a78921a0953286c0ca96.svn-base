<?php
namespace Admin;
use Config\Base as Base;
class Record extends Base{
  
  public function index(){
    $page = ($_GET['page']-1)*PAGE_SIZE;
    $where = ["ORDER"=>["id"=>"DESC"],"LIMIT"=>[$page,PAGE_SIZE]];
    $userTable = USER_TABLE;
    $colum = "r.id,r.`status`,r.creatime,r.flushtime,t.nickname,
    t.telphone,p.end_time,a.`name` activity,p.`name` prize,b.`name` business, r.create_status";
    $table = "
    FROM prize_record r JOIN $userTable t
    ON t.openid = r.openid
    LEFT JOIN activity a 
    ON a.id = r.activity_id
    LEFT JOIN prize p 
    ON p.id = r.prize_id
    LEFT JOIN business b
    ON b.id = r.business_id
    where r.business_id is not null
    ";
    if(!empty($_GET['id'])){
      $a = $_GET['id'];
      $table .=" AND r.`id` = '$a'";
    }
    if(!empty($_GET['nickname'])){
      $nickname = $_GET['nickname'];
      $table .=" AND t.nickname LIKE '%$nickname%'";
    }
    if(!empty($_GET['activity'])){
      $a = $_GET['activity'];
      $table .=" AND a.`name` LIKE '%$a%'";
    }
    if(!empty($_GET['business'])){
      $a = $_GET['business'];
      $table .=" AND b.`id` = '$a'";
    }
    if(isset($_GET['status'])&&trim($_GET['status'])!=''){
      $a = (int)$_GET['status'];
      $table .=" AND r.`status` = $a";
    }
    $count = self::$db->query("SELECT count(*) a $table")->fetch();
    $sql = "SELECT $colum $table ORDER BY r.id DESC LIMIT $page,".PAGE_SIZE;
   
    $stmt = self::$db->pdo->prepare($sql);
    $stmt->execute(); 
    $rows = $stmt->fetchAll();
    echo table($rows,(int)$count['a']);
  }
  public function total(){
    $userTable = USER_TABLE;
    $user = self::$db->query("SELECT count(*) a from $userTable")->fetch();
    $prize = self::$db->sum('prize','`amount`',['status'=>0,'business_id[!]'=>null]);
    $prize = $prize==null ? 0: $prize;
    $record = self::$db->query("SELECT count(DISTINCT openid) a from prize_record where status =1")->fetch();
    
    echo rs(['user'=>(int)$user['a'],'prize'=>$prize,'record'=>(int)$record['a'],'documentRoot'=>$_SERVER['DOCUMENT_ROOT']]);
  }
  //管理员兑奖
  public function save(){
    $s = new \Business\Record();
    $s->save();
  }
}
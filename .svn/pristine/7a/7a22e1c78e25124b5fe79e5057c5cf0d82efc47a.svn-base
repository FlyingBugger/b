<?php
namespace Business;
use Config\Base as Base;
class Record extends Base{
  //查询当前商家兑换记录
  public function index(){
    $busId = $_GET['userId'];
    $page = ($_GET['page']-1)*PAGE_SIZE;
    $sql = "
    SELECT
      r.id ,
      r.`status`,
      r.activity_id,
      r.creatime,
      r.flushtime,
      t.nickname,
      r.create_status,
      p.end_time,
      t.telphone,
      a.`name` activity,
      p.`name` prize
    FROM
      prize_record r
    JOIN ".USER_TABLE." t
    ON t.openid = r.openid
    LEFT JOIN activity a 
    ON a.id = r.activity_id
    LEFT JOIN prize p 
    ON p.id = r.prize_id
    where r.business_id = '$busId'" ;
    $where = "";
    if(!empty($_GET['id'])){
      $id = $_GET['id'];
      $sql .=" AND r.id = '$id'";
      $where .=" AND r.id = '$id'";
    }
    if(isset($_GET['status'])&&trim($_GET['status'])!=''){
      $id = (int)$_GET['status'];
      $sql .=" AND r.status = $id";
      $where .=" AND r.status = $id";
    }
    if(!empty($_GET['nickname'])){
      $id = $_GET['nickname'];
      $sql .=" AND t.nickname like '%$id%'";
      $where .= " AND t.nickname like '%$id%'";
    }
    $sql .= " ORDER BY  r.`status`, r.flushtime DESC LIMIT $page,".PAGE_SIZE;
    $stmt = self::$db->pdo->prepare($sql);
    $stmt->execute(); 
    $rows = $stmt->fetchAll();
    $count = self::$db->query("SELECT count(*) a FROM prize_record r JOIN ".USER_TABLE." t ON t.openid = r.openid
      where  r.business_id = '$busId' $where")->fetch();
    
    echo table($rows,(int)$count['a']);
  }
  public function save(){
    $where  = ['id'=>$_POST['id']];

    foreach($_GET['role'] as $role){
      if($role=='admin'){
        $where['business_id[!]']=null;
        break;
      }else{
        $where['business_id'] = $_GET['userId'];
      }
    }
    $status = $this->getOne('prize_record','status',$where);
    if($status==null){
      echo rs_error('兑换失败，未查询到此奖品');
      return;
    }
    if($status==1){
      echo rs_error('兑换失败，该奖品已兑换');
      return;
    }
    if($status==2){
      echo rs_error('兑换失败，购物券已过期');
      return;
    }
    $where['create_status'] = 1;
    $where['status'] = 0;
    if(empty($_GET['busId'])){
      $where['create_status'] = 0;
    }
    $index = $this->update('prize_record',['status'=>1,'flushtime'=>date('Y-m-d H-i-s')],$where);
    echo rs($index);
  }
  public function getActivity(){
    $id = $_GET['id'];
    $activity = $this->getOne('activity','*',['id'=>$id]);
    echo rs($activity);
  }
}
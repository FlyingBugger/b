<?php
namespace Admin;
use Config\Base as Base;
class Activity extends Base{

  public function index(){
    $page = ($_GET['page']-1)*PAGE_SIZE;
    $where = ["ORDER"=>["id"=>"DESC"],"LIMIT"=>[$page,PAGE_SIZE],'status'=>0];
    if(!empty($_REQUEST['name'])){
      $where["name[~]"]=$_REQUEST['name'];
      $count = self::$db->count("activity",['name[~]'=>$_REQUEST['name'],'status'=>0]);
    }else{
      $count = self::$db->count("activity",['status'=>0]);
    }
    $activity = $this->select('activity',['id','name','info','start_date','close_date','end_date','creatime','day_limit','times'],$where);
    
    echo table($activity,$count);
  }
  public function save(){
    $_POST["status"]=0;
    $_POST['creatime'] = date('Y-m-d H-i-s');

    self::$db->pdo->beginTransaction();
    $index=0;
    unset($_POST['prizeData']);
    
    try{
     
      if(empty($_POST["id"])||$_POST["id"]<0){
        unset($_POST['id']);
        $ac =$_POST;
        if(empty($ac['day_limit'])){
          $ac['day_limit']=0;
        }
        $index = $this->insert('activity',$ac);
        $activityId=self::$db->id();
      }else{
        $activityId = $_POST['id'];
        $index = $this->update('activity',$_POST,['id'=>$_POST['id']]);
        $index++;
      }
      if(!empty($_REQUEST['prizeData'])){
        $prize = json_decode($_REQUEST['prizeData'],true);
        foreach($prize as $p){
          if(empty($p['name'])||trim($p['name'])==''){
            continue;
          }
          $p['name'] =trim($p['name']);
          unset($p['business']);
          unset($p['num']);
      
          $p['start_time']=$_REQUEST['start_date'];
          $p['end_time']=$_REQUEST['end_date'];
          if(empty($p['business_id'])||trim($p['business_id'])==''){
            $p['business_id'] = null;
          }
          if(empty($p['id'])){
            $p['id'] = uuid();
            $p['activity_id']=$activityId;
            $this->insert('prize',$p);
          }else{
            $i =$this->update('prize',$p,['id'=>$p['id']]);
            if($i>0){
              $index = $i;
            }
          }
        }
        
      }else if(!empty($_REQUEST['end_date'])){
        $w =['end_time'=>$_REQUEST['end_date']]; 
        $this->update('prize',$w,['activity_id'=>$activityId]);
      }else if(!empty($_REQUEST['start_date'])){
        $this->update('prize',['start_time'=>$_REQUEST['start_date']],['activity_id'=>$activityId]);
      }

      self::$db->pdo->commit();
    }catch(Exception $e){
      self::$db->pdo->rollBack();
    }
    echo rs($index);
  }
  public function del(){
    $id = $_GET['id'];
    $index = $this->update('activity',['status'=>1],['id'=>$id]);
    $this->update('prize',['status'=>1],['activity_id'=>$id]);
    echo rs($index);
  }
  public function getPrizeByActivity(){
    self::$db->query("UPDATE prize_record SET `status`=2 WHERE `status`=0 AND prize_id IN (SELECT id FROM prize WHERE NOW() > end_time)")->fetchAll();
    $activityId = $_GET['activity'];
    $prize = self::$db->select('prize',
    ['[>]business'=>['business_id'=>'id']],
    ['prize.id','prize.name','prize.amount','prize.v','prize.remark','prize.end_time','prize.num','prize.business_id','business.name(business)'],
    ['prize.activity_id'=>$activityId,'prize.status'=>0,'ORDER'=>'prize.id']);
   
    echo rs($prize);
  }
  public function delPrize(){
    $id = $_GET['id'];
    $index = $this->update('prize',['status'=>1],['id'=>$id]);
    echo rs($index);
  }
}
<?php
namespace Admin;
use Config\Base as Base;
class Business extends Base{
  //查询商家
  public function index(){
    
    $page = ($_GET['page']-1)*PAGE_SIZE;

    $where = ["ORDER"=>["id"=>"ASC"],"LIMIT"=>[$page,PAGE_SIZE],'status'=>0];
    if(!empty($_REQUEST['name'])){
      $where["name[~]"]=$_REQUEST['name'];
      $count = self::$db->count("business",['name[~]'=>$_REQUEST['name'],'status'=>0]);
    }else{
      $count = self::$db->count("business",['status'=>0]);
    }
    $business = $this->select('business',['id','name','username','address','linkman','tel','creatime','updatime'],$where);
    
    echo table($business,$count);
  }
  public function save(){
    $_POST["status"]=0;
    if(empty($_POST["id"])){
      $name= $this->has('business',['username'=>$_POST['username'],'status'=>0]);
      if($name){
        echo rs_error("账号重复");
        return;
      }
      if($this->has('business',['name'=>$_POST['name'],'status'=>0])){
        echo rs_error("该商家已存在");
        return;
      }
      $_POST['id'] = uuid();
      $_POST['password']=md5($_POST['username'].SALT);
      $index = $this->insert('business',$_POST);
      echo rs($index);
      return;
    }
    $index = $this->update('business',$_POST,['id'=>$_POST['id']]);
    echo rs($index);
  }
  public function del(){
    $id = $_GET['id'];
    $index = $this->update('business',['status'=>1],['id'=>$id]);
    echo rs($index);
  }
  public function resetPwd(){
    $id = $_POST['id'];
    $pwd = md5($_POST['username'].SALT);
    $index = $this->update('business',['password'=>$pwd],['id'=>$id]);
    echo rs($index);
  }
  public function getList(){
    $business = $this->select('business',['id','name'],['status'=>0]);
    echo rs($business);
  }
}
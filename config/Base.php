<?php
namespace Config;
require_once BASE_PATH.'/app/common.php';
use Medoo\Medoo as Medoo;
class Base{
  protected static $db = null;
  function __construct(){
    self::$db = new Medoo(CONFIG['db']);
  }
  protected function insert($table,$data){
    $i = self::$db->insert($table,$data);
    return $i->rowCount();
  }
  protected function update($table, $data,$where){
    $i = self::$db->update($table, $data,$where);
    return $i->rowCount();
  }
  protected function delete($table, $where){
    $i =self::$db->delete($table,$where);
    return $i->rowCount();
  }
  protected function replace($table, $column, $search, $replace, $where){
    
   return self::$db->replace($table, $column, $search, $replace, $where);
  }
  protected function select($table, $column='*', $where=null){
    if($where==null){
      return self::$db->select($table,$column);
    }
    return self::$db->select($table,$column,$where);
  }
  
  protected function getOne($table, $columns='*', $where){
   return self::$db->get($table, $columns, $where);
  }
  protected function has($table, $where){
    return self::$db->has($table,$where);
  }
}

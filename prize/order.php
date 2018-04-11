<?php
/**
 * Created by PhpStorm.
 * User: 融媒中心-1
 * Date: 2018/3/30
 * Time: 17:56
 */
require_once 'config.php';

$nickname="";

$orderList;
$param = array();
if (empty($nickname)){
    $orderList ="select t.nickname ,prize.name prize from `order` o join prize on prize_id = prize.Id join tp_newuser t on t.openid = o.openid ORDER BY prize.Id";

}else{
    $orderList ="select t.nickname ,prize.name prize from `order` o join prize on prize_id = prize.Id join tp_newuser t on t.openid = o.openid WHERE nickname= :nickname ORDER BY prize.Id";
    $param[":nickname"] = $nickname;
}
$stmt = $pdo->prepare($orderList);
$stmt->execute($param);
$rows = $stmt->fetchAll();
for ($i=1;$i<=count($rows);$i++){
    $rows[$i-1]["id"]=$i;
}
$res = array("code"=>200,"data"=>$rows);
echo json_encode($res, JSON_UNESCAPED_UNICODE);

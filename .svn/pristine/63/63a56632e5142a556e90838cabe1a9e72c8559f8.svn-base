<?php
/**
 * Created by PhpStorm.
 * User: 融媒中心-1
 * Date: 2018/3/29
 * Time: 16:20
 */
require_once 'config.php';
require 'CacheLock.php';

$now = strtotime("now");
if ($time> $now){
    $res["code"] = 500;
    $res["msg"] ="抽奖时间未到";
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}
$now= date("M-d-Y",mktime(0,0,0));
if ($now>$time){
    $res["code"] = 500;
    $res["msg"] ="活动已结束！";
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($_GET["openId"])||empty($_GET["openId"])){
    $res["code"] = 500;
    $res["msg"] ='openId不存在';
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}
$openId = $_GET["openId"];
$userSql = "SELECT u.openid,o.Id FROM tp_newuser u left join `order` o on u.openid = o.openid WHERE u.openid =".$pdo->quote($_GET["openId"]);//查询用户
$stmt = $pdo->prepare($userSql);
$stmt->execute();

if ($user = $stmt->fetch(PDO::FETCH_ASSOC)){

    while (empty($user) ||!$user["openid"]){
        $res["code"] = 500;
        $res["msg"] ='你未收到邀请！';
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if (!empty($user["Id"])){
        $res["code"] = 500;
        $res["msg"] ='请下次再来';
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit;
    }
}else{
    $res["code"] = 500;
    $res["msg"] ='你未收到邀请！';
    echo json_encode($res, JSON_UNESCAPED_UNICODE);
    exit;
}
$lock = new CacheLock('key_name','lock/');
/*  这里是要同步的代码块  开始  */
$lock->lock();
$top = pageRand($pdo);//随机数生成
$pdo->beginTransaction();
try {
    //PDO预处理以及执行语句...
        $count = $pdo->exec("update prize set num=num-1 where num > 0 AND top = " . $top);//更新奖品数量
        if ($count<1){
            $res["code"] = 200;
            $res["msg"] = THANKS;

        }else{

            $prizeSql = "SELECT num,`name`,id FROM prize WHERE top = " . $top;
            $stmt = $pdo->query($prizeSql);
            $row = $stmt->fetch();

            if ($row["num"] <= 1) {
                $count = $pdo->exec("update sys_config set status=0  WHERE top !=".THANKS." and top = " . $top);//删除奖品选项

            }
            if ($row["num"] >= 0 && $top != THANKS) {
                //生成奖品记录
                $stmt = $pdo->prepare("insert into `order`(openid,prize_id,create_time) VALUES (:openid,:prizeId,:createTime)");
                $stmt->execute(array(":openid" => $openId, ":prizeId" => ($row["id"]), ":createTime" =>  date("Y-m-d H:i:s",time())));
                $res["code"] = 200;
                $res["msg"] = $top;

            }else{
                $res["code"] = 200;
                $res["msg"] = THANKS;
            }
        }
        $pdo->commit();//提交事务
        $lock->unlock();
 } catch (PDOException $e) {
        $pdo->rollBack();//事务回滚
        error_log("server error: " . $e->getMessage());
        //相关错误处理
        $lock->unlock();
        $res["code"] = 500;
        $res["msg"] = '服务器异常！';
}
echo json_encode($res, JSON_UNESCAPED_UNICODE);

function pageRand($pdo){
    $stmt = $pdo->query("SELECT top FROM sys_config where status=1");
    $top_array = array();
    if($stmt){
        $rows = $stmt->fetchAll();
        foreach ($rows as $key => $value){
            array_push($top_array,$value["top"]);
        }
    }
    if (count($top_array)<1){
        return THANKS;//谢谢参与
    }
     $index= array_rand($top_array,1);
    return $top_array[$index];
}

<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/18
 * Time: 下午5:22
 */

header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");
require_once("ios_api.php");

//获取好友列表
function get_friends($account,$timestamp){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

     $friends = fet_friends($account,$is_open_mySql,$timestamp);

     return $friends;

}

//执行------------------
$account = isset($_GET['account'])? $_GET['account']:null;
$timestamp = isset($_GET['timestamp'])?$_GET['timestamp']:0;

if(empty($account)){


    $error = array('code' => '400', 'message' => 'account 不能为 NULL');

    echo json_encode($error);

    exit;
}

$result = get_friends($account,$timestamp);

echo json_encode(array('code' =>'200','friends' =>$result));

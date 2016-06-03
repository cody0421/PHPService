<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/19
 * Time: 下午11:21
 */

header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");
require_once("ios_api.php");


$account = isset($_GET['account'])?$_GET['account']:null;
$friend_account = isset($_GET['friend_account'])?$_GET['friend_account']:null;

if(empty($account)){

    $error = array('code' => '400', 'message' => 'account 不能为 NULL');

    echo json_encode($error);

    exit;
}

if(empty($friend_account)){

    $error = array('code' => '400', 'message' => 'friend_account 不能为 NULL');

    echo json_encode($error);

    exit;
}

//打开数据库
$is_open_mySql = connectDb();

if(empty($is_open_mySql)){
    $error = array('code' => '400', 'message' => '数据库打开失败!');
    echo json_encode($error);

    exit;
}

//查询好友关系
$is_exists =  is_friend_exists($account,$friend_account,$is_open_mySql);

if($is_exists){

    echo json_encode(array('code' =>'200','exists' =>'1'));

    exit;
}else{

    echo json_encode(array('code' =>'200','exists' =>'0'));

    exit;

}
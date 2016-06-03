<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/19
 * Time: 下午4:36
 */

header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");
require_once("ios_api.php");

function inset_friend($account,$friend_account,$remark=null){
    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $result = add_friend($account,$friend_account,$is_open_mySql,$remark);

    return $result;
}

/*
 * POST数据解析
 * */
$account = isset($_POST['account'])?$_POST['account']:null;
$friend_account = isset($_POST['friend_account'])?$_POST['friend_account']:null;
$remark =  isset($_POST['remark'])?$_POST['remark']:null;

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


$result = inset_friend($account,$friend_account,$remark);

echo json_encode($result);

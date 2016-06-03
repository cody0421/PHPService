<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/18
 * Time: 下午4:27
 */

header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");
require_once("ios_api.php");


function getUserInfo($account,$search_account){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $user_array = get_user_info($account,$is_open_mySql,$search_account);

    if (count($user_array) == 0){

        return array('code' => '201', 'message' => 'account not exist !');
    }

        return array('code'=>'200','user_info' => $user_array);
}

/*
 * 解析GET请求参数
 * account : 请求人的帐号
 * search_account 要查询的帐号
 *
 * */
$account = isset($_GET['account'])?$_GET['account']:null;
$search_account = isset($_GET['search_account'])?$_GET['search_account']:null;



if(empty($account)){

    $error = array('code' => '400', 'message' => 'account not is  NULL !');

    echo json_encode($error);

    exit;
}

if(empty($search_account)){


    $error = array('code' => '400', 'message' => 'search_account not is  NULL !');

    echo json_encode($error);

    exit;

}

$result =  getUserInfo($account,$search_account);

echo json_encode($result);
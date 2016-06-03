<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/24
 * Time: 下午2:33
 *
 * 获取附近的联系人
 */


header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");

require_once("ios_api.php");


//$l1 = 28.2314040000;
//$a1 = 112.9803530000;
//
//$l2 = 28.2314940000;
//$a2 = 112.9803530000;
//
//
//echo getDistance($l1,$a1,$l2,$a2);

$longitude = isset($_GET['longitude'])?$_GET['longitude']:-1;
$latitude  = isset($_GET['latitude'])?$_GET['latitude']:-1;
$page = isset($_GET['page'])? $_GET['page']:1;
$page_size = isset($_GET['page_size'])? $_GET['page_size']:10;
$account = isset($_GET['account'])?$_GET['account']:null;

if(empty($account)){

    $error = array('code' => '400', 'message' => 'account 不能为 NULL');

    echo json_encode($error);

    exit;
}


$conn = connectDb();


if($longitude == -1 || $latitude == -1){

    echo json_encode(array('code'=>'400','message' =>'经纬度不存在!'));

}else{

    $array =  fetch_nearby_friends($conn,$longitude,$latitude,30,$page,$page_size,$account);

    echo json_encode(array('code'=>'200','friends'=>$array));


}



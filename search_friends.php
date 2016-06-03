<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/6/1
 * Time: 上午9:38
 */

header('Content-type: application/json ; charset=utf-8');
require_once("mySql_configuration.php");
require_once("ios_api.php");

$search_text = isset($_GET['search_text'])?$_GET['search_text']:null;
$account = isset($_GET['account'])?$_GET['account']:0;


if(empty($search_text)){
    echo json_encode(array('code'=>'400','message'=>'Text cannot be empty'));
    exit;
}


$accounts = searchFirends($account,$search_text);

echo json_encode(array('code'=>'200','accounts'=>$accounts));

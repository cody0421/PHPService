<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/14
 * Time: 上午2:05
 */


header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");
require_once("image_scale.php");
require_once("ios_api.php");
require_once "emchat-server-php/easemobtest.php";




$account = isset($_POST['account'])?  $_POST['account']: 0 ;
$name =  isset($_POST['name'])?    $_POST['name']:null;
$address = isset($_POST['address'])?  $_POST['address']:null;
$sex = isset($_POST['sex'])?    $_POST['sex']: 0;
$sign = isset($_POST['sign'])?  $_POST['sign']:null;
$image_path = isset($_POST['image_path'])?  $_POST['image_path']:null;
$password = isset($_POST['password'])?$_POST['password']:null;
$latitude = isset($_POST['latitude'])?$_POST['latitude']:-1;//纬度
$longitude = isset($_POST['longitude'])?$_POST['longitude']:-1;//经度


if(empty($account)){

    $error = array('code' => '400', 'message' => 'account 不能为 NULL');

    echo json_encode($error);

    exit;
}


$conn = connectDb();

//查询数据库结果
$sql = "SELECT * FROM account_info WHERE account = '$account'";

$result = mysqli_query($conn,$sql);//执行查询结果

$data_count = mysqli_num_rows($result);

//帐号是否存在
if($data_count == 0 || !$data_count){
    $error_result = array('code' => '1', 'message' => 'account is null');
    echo json_encode($error_result);
    exit;
}


//修改名字
if(!empty($name)){
    $sql_set_name = "UPDATE account_info SET name =  '$name'  WHERE account = '$account'";
    if(!mysqli_query($conn,$sql_set_name)){
        echo  json_encode(array('code'=>'201','message' => 'name 为 NULL ！'));
        exit;
    }
}


//修改地址
if(!empty($address)){
    $sql_set_address = "UPDATE account_info SET address =  '$address'  WHERE account = '$account'";
    if(!mysqli_query($conn,$sql_set_address)){
        echo  json_encode(array('code'=>'201','message' => 'address 为 NULL ！'));
        exit;
    }

}

//修改性别
if(!empty($sex)){

    $sql_set_sex = "UPDATE account_info SET sex =  '$sex'  WHERE account = '$account'";
    if(!mysqli_query($conn,$sql_set_sex)){
        echo  json_encode(array('code'=>'201','message' => 'sex 为 NULL ！'));
        exit;
    }
}

//修改个性签名
if(!empty($sign)){

    $sql_set_sign = "UPDATE account_info SET sign =  '$sign'  WHERE account = '$account'";
    if(!mysqli_query($conn,$sql_set_sign)){
        echo  json_encode(array('code'=>'201','message' => 'sign 为 NULL ！'));
        exit;
    }
}

//修改头像地址
if(!empty($image_path)){

    $images_exploade = explode(',',$image_path);

    if(count($images_exploade)==2){

        $original_host = $images_exploade[0];
        $small_host = $images_exploade[1];

        update_user_image($conn,$account,$original_host,$small_host);
    }else{

        echo json_encode(array('code' => '201','message' =>'头像地址改为 原图地址#小图地址'));
        exit;
    }
}


//修改密码
if(!empty($password)){

    $sql_set_sign = "UPDATE account_info SET password =  '$password'  WHERE account = '$account'";
    if(!mysqli_query($conn,$sql_set_sign)){
        echo  json_encode(array('code'=>'201','message' => 'password 为 NULL ！'));
        exit;
    }else{
        resetPassword($account,$password);//修改环信密码
    }
}

//更新用户位置信息
if(!empty($latitude) && $latitude != -1 && !empty($longitude) && $longitude !=-1){

    $sql_set_sign = "UPDATE account_info SET latitude = '$latitude' , longitude = '$longitude'  WHERE  account = '$account'";
    if(!mysqli_query($conn,$sql_set_sign)){
        echo  json_encode(array('code'=>'201','message' => 'password 为 NULL ！'));
        exit;
    }
}

echo json_encode(array('code' => '200', 'message' =>'修改成功'));


<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/8
 * Time: 下午4:13
 */



header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");
require_once "emchat-server-php/easemobtest.php";
require_once("ios_api.php");

function registerAction($name,$account,$password,$address=null,$sex=0,$small_path=null,$original_path=null){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }


    //搜索帐号是否存在
    $sql = "SELECT * FROM account_info WHERE account = '$account'";

    $result = mysqli_query($is_open_mySql,$sql);//执行查询结果

    $data_count = mysqli_num_rows($result);

    if($data_count != 0){
       $error = array('code' => '201' ,'message' => '帐号已经存在');
        return $error;
    }

    //创建用户二维码
    $file =  createQrcode($account);
    $code_path = HOST.$file;


    $sql = "INSERT INTO account_info (name, password, account, address , sex ,qrcode) VALUES ('$name' , '$password' ,'$account' ,'$address','$sex' ,$code_path)";

    $is_ok = mysqli_query($is_open_mySql,$sql);

    //注册环信帐号
     $easemob_rst =   createUser($account,$password,$name);
     $easemob_error =isset($easemob_rst['error'])?$easemob_rst['error']:null;
    if(isset($easemob_error)){
        $error = array('code' => '401' , 'message' => 'Easemob 创建失败!');
        return $error;
    }
    

    //更新用户头像
    update_user_image($is_open_mySql,$account,$original_path,$small_path);





    if($is_ok){

        $succeed = array('code' => '200', 'message' =>'创建成功');

        return $succeed;

    }else{

        $error = array('code' => '400' , 'message' => '创建失败!');

        return $error;

    }


}

/*
 *
 * post参数解析
 *
 * */
$name     = isset($_POST['name'])?     $_POST['name']: null;
$account  = isset($_POST['account'])?  $_POST['account']:null;
$password = isset($_POST['password'])? $_POST['password']:'123';
$address = isset($_POST['address'])? $_POST['address']:null;
$sex = isset($_POST['sex'])? $_POST['sex']:0;
$small_path = isset($_POST['small_path'])? $_POST['small_path']:null;
$original_path = isset($_POST['original_path'])?$_POST['original_path']:null;



if(empty($name)){

    $error = array('code' => '400', 'message' => 'name 不能为 NULL');

    echo json_encode($error);

    exit;
}

if(empty($account)){

    $error = array('code' => '400', 'message' => 'account 不能为 NULL');

    echo json_encode($error);

    exit;
}

if(empty($password) || $password == '123'){

    $error = array('code' => '400', 'message' => 'password 不能为 NULL');

    echo json_encode($error);

    exit;
}

$result =  registerAction($name,$account,$password,$address,$sex,$small_path,$original_path);

echo json_encode($result);



<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/8
 * Time: 下午4:12
 */

define('SUSSEED_CODE','0');//成功
define('ERROR_CODE_1','1');//帐号不存在
define('ERROR_CODE_2','2');//密码错误


header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");



/* 登录接口
 * account 帐号
 * password 密码
 * */
function login_action($account, $password){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => 400, 'message' => '数据库打开失败!');
         return $error;
    }


    //查询数据库结果
    $sql = "SELECT * FROM account_info WHERE account = '$account'";

    $result = mysqli_query($is_open_mySql,$sql);//执行查询结果

    $data_count = mysqli_num_rows($result);

    //帐号是否存在
    if($data_count == 0 || !$data_count){

        $error_result = array('code' => ERROR_CODE_1, 'message' => '帐号不存在');
        return $error_result;

    }else{
        $account_info =  mysqli_fetch_assoc($result);

        $sql_password = $account_info['password'];

        //获取发送的图片地址集
        $sql_image = "SELECT * FROM image WHERE account = '$account' ORDER BY id DESC limit 0,1 ";

        $sql_image_result = mysqli_query($is_open_mySql,$sql_image);
        $sql_image_rows = mysqli_num_rows($sql_image_result);

        $sql_header_image = null;
        if($sql_image_rows !=0){

            $image_sql =  mysqli_fetch_assoc($sql_image_result);
            $sql_header_image = array('image_id' => $image_sql['id'],
                'original_path' => $image_sql['original_path'],
                'small_path' => $image_sql['small_path']);
        }




        //密码是否正确
        if($sql_password == $password){

            $account_array = array('name' => $account_info['name'],
                'account' => $account_info['account'],
                'user_id' => $account_info['user_id'],
                'image_path'=>$sql_header_image,
                'address' => $account_info['address'],
                'sex' => $account_info['sex'],
                'sign' => $account_info['sign']);

            $succeed_result = array('code' => SUSSEED_CODE , 'accountInfo'=> $account_array);

            return $succeed_result;

        }else{


            $error_result_2 = array('code' => ERROR_CODE_2 , 'message' => '密码错误');
            return $error_result_2;

        }

    }


}


//执行
$account = isset($_GET['account'])? $_GET['account'] : 100000;
$password = isset ($_GET['password'])? $_GET['password'] : '';


$result =  login_action($account,$password);

echo  json_encode($result);
<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/18
 * Time: 下午5:13
 */

header('Content-type: application/json ; charset=utf-8');
require_once "emchat-server-php/easemobtest.php";
require_once "phpqrcode/phpqrcode.php";
require_once("mySql_configuration.php");


define('ROOT',dirname(__FILE__).'/');
define('HOST',('http://'.$_SERVER['HTTP_HOST']));
define('qrCodeFileName','/qrcode_image/');
define('ProjectName','/classes1402');

/**
 * 获取用户信息
 * $account 自己的帐号
 * $search_account 要查询的账号
 *
 */
function get_user_info($account,$conn,$search_account){

    $sql_user = "SELECT * FROM account_info WHERE account = '$search_account'";

        //查询是否已经是好友关系
    $is_friend = is_friend_exists($account,$search_account,$conn);


    $story_user_info = mysqli_query($conn,$sql_user);
    $user_count = mysqli_num_rows($story_user_info);

    $user_array = array();

    if($user_count != 0){

        $account_info = mysqli_fetch_assoc($story_user_info);

        //获取好友头像
        $sql_image = "SELECT * FROM image WHERE account = '$search_account' ORDER BY id DESC limit 0,1 ";

        $sql_image_result = mysqli_query($conn,$sql_image);
        $sql_image_rows = mysqli_num_rows($sql_image_result);

        $sql_header_image = null;
        if($sql_image_rows !=0){

            $image_sql =  mysqli_fetch_assoc($sql_image_result);
            $sql_header_image = array('image_id' => $image_sql['id'],
                'original_path' => $image_sql['original_path'],
                'small_path' => $image_sql['small_path']);
        }

        //用户是否有二维码,因为二维码是新加的功能
        $code_path =$account_info['qrcode'];//二维码地址
        if(empty($code_path)){
          //创建二维码24.187.20/classes1402/qrcode_image/F761E524
          $code_path  = HOST.ProjectName.createQrcode($search_account);
            //修改数据库
            $sql_set_qrcode = "UPDATE account_info SET qrcode =  '$code_path'  WHERE account = '$search_account'";
            mysqli_query($conn,$sql_set_qrcode);
        }


        //组装用户信息
        $user_array  = array('name' => $account_info['name'],
            'account' => $account_info['account'],
            'user_id' => $account_info['user_id'],
            'image_path'=> $sql_header_image,
            'address' => $account_info['address'],
            'sex' => $account_info['sex'],
            'sign' => $account_info['sign'],
            'qrcode' => $code_path,
            'is_friends' => $is_friend);
    }


    return $user_array;
}

//获取好友列表
function fet_friends($account,$conn,$timestamp=0){

    $sql_selet = "SELECT * FROM friends WHERE my_account = '$account' AND timestamp > '$timestamp' ORDER BY timestamp DESC";
    $sql_friends = mysqli_query($conn,$sql_selet);
    $friend_count = mysqli_num_rows($sql_friends);

    $friends = array();

    for($i=0;$i<$friend_count;$i++){

        $sql_friend = mysqli_fetch_assoc($sql_friends);
        $friend_user_id = $sql_friend['friend_account'];

        //获取个人信息
        $friend_info = get_user_info($account,$conn,$friend_user_id);

        //设置好友备注
        $friend_info['remark'] = $sql_friend['remark'];


        array_push($friends,$friend_info);

    }

return $friends;

}


//添加好友
function add_friend($account,$friend_account,$conn,$remark=null){

    //自己不能添加自己
    if($account == $friend_account){

        return array('code'=>'202','message'=>'自己不能添加自己为好友');

    }

    //查询是否已经是好友关系
      $is_friend = is_friend_exists($account,$friend_account,$conn);

    if($is_friend == true){

        return array('code'=>'201','message'=>'Friend already exists');
    }

    //插入一条好友关系
    $timestamp = time();//添加好友时间

    $sql_insert = "INSERT INTO friends (my_account, friend_account , remark ,timestamp) VALUES ('$account' , '$friend_account' , '$remark','$timestamp')";
    $is_ok = mysqli_query($conn,$sql_insert);

    if($is_ok){

         addFriend($account,$friend_account);

        return array('code' => '200', 'message' => 'add succeed !');
    }else{

        return array('code' => '201', 'message' => 'add fail !');

    }

}



//查询是否已经是好友关系
function is_friend_exists($account,$friend_account,$conn){
    $sql_selet = "SELECT * FROM friends WHERE my_account = '$account' AND friend_account ='$friend_account' ";
    $sql_friends = mysqli_query($conn,$sql_selet);
    $friend_count = mysqli_num_rows($sql_friends);

    if($friend_count>0){

         return true;
    }else{
        return false;

    }
}


//更新头像地址
function update_user_image($conn,$account,$original_path,$small_path=''){

    if(!empty($original_path) && !empty($small_path)){

        $sql_image = "INSERT INTO image (original_path, small_path , account, type) VALUES ('$original_path', '$small_path', '$account' ,'1')";
        $is_add = mysqli_query($conn,$sql_image);
        if(!$is_add){
            echo array('code' => '400' , 'image_path error');
            exit;
        }

        $sql_set_image_path = "UPDATE account_info SET image_path =  '$original_path'  WHERE account = '$account'";

        if(!mysqli_query($conn,$sql_set_image_path)){
            echo  json_encode(array('code'=>'201','image_path' => 'sex 为 NULL ！'));
            exit;
        }
        return true;
    }

}



/* 获取附近的好友
 * $conn 数据库
 * $longitude 经度
 * $latitude  纬度
 * $distance  距离(公里)
 * */
function fetch_nearby_friends($conn,$longitude,$latitude,$distance = 40,$page=1,$page_size=10,$account){

    $offset = ($page - 1) * $page_size;

//通过经纬度查询
    /*
     *  3959 表示英里计算方式,  6371 表示公里计算方式
     * */
    $sql = "SELECT *, ( 6371 * acos( cos(radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ))) AS distance FROM account_info HAVING distance < $distance ORDER BY distance LIMIT $offset , $page_size";

    $sql_friends = mysqli_query($conn,$sql);
    $friend_count = mysqli_num_rows($sql_friends);

    $friends = array();

    for($i=0;$i<$friend_count;$i++){

        $sql_friend = mysqli_fetch_assoc($sql_friends);
        $friend_account = $sql_friend['account'];

        //获取个人信息
        $friend_info = get_user_info($account,$conn,$friend_account);

        array_push($friends,$friend_info);

    }


    return $friends;

}



/* 生成二维码
 *
 * */
function createQrcode($account,$type=0){

    //二维码保存地址
    $code_path = '/'.'qrcode_image'.'/'.$account.'.png';

    $file_path = ROOT.$code_path;

    /* 二维码容错率
     *
     * 分别是 L（QR_ECLEVEL_L，7%），M（QR_ECLEVEL_M，15%），Q（QR_ECLEVEL_Q，25%），H（QR_ECLEVEL_H，30%）
     *
     */
    $errorCorrectionLevel = 'L';

    //二维码尺寸
    $matrixPointSize = 8;

    //二维码边界
    $margin = 2;

    //生成二维码
    QRcode::png($account,$file_path,$errorCorrectionLevel,$matrixPointSize,$margin);

    return $code_path;
}


//二维码图片拼接




//用户模糊查询
function searchFirends($account,$text){

    $conn = connectDb();

    $sql = "SELECT * FROM account_info WHERE name LIKE '%$text%' OR name LIKE '$text%' OR name LIKE '%$text' OR account LIKE '%$text%' OR account LIKE '$text%' OR account LIKE '%$text' ";

    $sql_friends = mysqli_query($conn,$sql);
    $friend_count = mysqli_num_rows($sql_friends);

    $friends = array();

    for($i=0;$i<$friend_count;$i++){

        $sql_friend = mysqli_fetch_assoc($sql_friends);
        $friend_account = $sql_friend['account'];

        //获取个人信息
        $friend_info = get_user_info($account,$conn,$friend_account);


        array_push($friends,$friend_info);

    }

    return $friends;

};

//通过经纬度计算坐标位置
/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; //approximate radius of earth in meters

    /*
    Convert these degrees to radians
    to work with the formula
    */
    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
    Using the
    Haversine formula

    http://en.wikipedia.org/wiki/Haversine_formula

    calculate the distance
    */
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}


//获取文件后缀名
function extend_1($file_name)
{
    $retVal="";
    $pt=strrpos($file_name, ".");
    if ($pt) $retVal=substr($file_name, $pt+1, strlen($file_name) - $pt);
    return ($retVal);
}



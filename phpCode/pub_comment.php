<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/24
 * Time: 上午11:07
 */

require_once("mySql_configuration.php");

function sendComment($user_id,$story_id,$content){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $time = time();

    $sql = "INSERT INTO comment (user_id , story_id , content , time) VALUES ('$user_id' ,'$story_id','$content','$time')";

    $add_sql = mysqli_query($is_open_mySql,$sql);

    if($add_sql){

        $getID=mysqli_insert_id($is_open_mySql);//$getID即为最后一条记录的ID


        $user_info = get_user_info($user_id,$is_open_mySql);

        $result = array('content'=>$content,
                        'time' => $time,
                        'user_info'=> $user_info,
                        'comment_id' =>$getID);


        return array('code'=>'200','message' => $result);

    }else{

        return array('code' => '400', 'message' => 'add fail !');
    }

};


//获取用户信息
function get_user_info($user_id,$conn){
    //通过user_id查询发送人详情信息
    $sql_user = "SELECT * FROM account_info WHERE user_id = '$user_id'";
    $story_user_info = mysqli_query($conn,$sql_user);
    $user_count = mysqli_num_rows($story_user_info);

    $user_array = array();
    if($user_count != 0){

        $account_info = mysqli_fetch_assoc($story_user_info);
        $user_id_story = $account_info['user_id'];

        //获取发送的图片地址集
        $sql_image = "SELECT * FROM image WHERE user_id = '$user_id_story' ORDER BY id DESC limit 0,1 ";

        $sql_image_result = mysqli_query($conn,$sql_image);
        $sql_image_rows = mysqli_num_rows($sql_image_result);

        $sql_header_image = null;
        if($sql_image_rows !=0){

            $image_sql =  mysqli_fetch_assoc($sql_image_result);
            $sql_header_image = array('image_id' => $image_sql['id'],
                'original_path' => $image_sql['original_path'],
                'small_path' => $image_sql['small_path']);
        }

        //组装用户信息
        $user_array  = array('name' => $account_info['name'],
            'account' => $account_info['account'],
            'user_id' => $account_info['user_id'],
            'image_path'=> $sql_header_image,
            'address' => $account_info['address'],
            'sex' => $account_info['sex'],
            'sign' => $account_info['sign']);
    }


    return $user_array;
}


$user_id = $_POST['user_id'];
$story_id =$_POST['story_id'];
$content = $_POST['content'];

$result = sendComment($user_id,$story_id,$content);

echo json_encode($result);

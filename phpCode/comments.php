<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/24
 * Time: 上午11:39
 */

require_once("mySql_configuration.php");

function fetchComments($story_id,$page,$page_size){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $offset = ($page - 1) * $page_size;


    $sql = "SELECT * FROM comment WHERE story_id = $story_id  ORDER BY comment_id ASC limit ".$offset.",".$page_size;

    $sql_result = mysql_query($sql);//执行查询结果

    $data_count = mysql_num_rows($sql_result);


    $comment_array =array();

    for($i =0 ; $i < $data_count ; $i++){

        $comment_sql =  mysql_fetch_assoc($sql_result);

        $comment_id = $comment_sql['comment_id'];
        $user_id = $comment_sql['user_id'];

        //获取对应用户信息
        $sql_user = "SELECT * FROM account_info WHERE user_id = '$user_id'";
        $comment_user_info = mysql_query($sql_user);
        $user_count = mysql_num_rows($comment_user_info);

        $user_array= array();

        if($user_count != 0){

            $account_info = mysql_fetch_assoc($comment_user_info);

            //获取发送的图片地址集
            $sql_image = "SELECT * FROM image WHERE user_id = '$user_id' ORDER BY id DESC limit 0,1 ";

            $sql_image_result = mysql_query($sql_image);
            $sql_image_rows = mysql_num_rows($sql_image_result);

            $sql_header_image = null;
            if($sql_image_rows !=0){

                $image_sql =  mysql_fetch_assoc($sql_image_result);
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


        $comment_info = array('content'=>$comment_sql['content'],
                               'time' => $comment_sql['time'],
                               'user_info'=>$user_array,
                                'comment_id' =>$comment_id);

        array_push($comment_array,$comment_info);

    }

    return $comment_array;

}

/////////////////////////////////////////////////
$page = isset($_GET['page'])? $_GET['page']:1;
$page_size = isset($_GET['pageSize'])? $_GET['pageSize']:10;
$story_id = isset($_GET['story_id'])? $_GET['story_id']:0;



if($page_size > 50){
    $page_size = 50;
}

$comment_array = fetchComments($story_id, $page , $page_size);

$result = array(
    'code' => '200',
    'comments' => $comment_array
);

echo json_encode($result);
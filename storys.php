<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/8
 * Time: 下午4:14
 */

define('PAGE_SIZE',10);


header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");
require_once("ios_api.php");

/*
 * 获取段子
 *
 * $type 0:表示默认时间排序; 1:按照喜欢基数排序 2:按照评论数排列
 * */
function fetchStroys($page , $page_size ,$user_id,$type = 0,$account=0){


    if(!is_numeric($page) || !is_numeric($page_size)){
       $error = array('code' => '400' , 'message' => '数据格式错误');

       return $error;
    }

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $offset = ($page - 1) * $page_size;

    //ORDER BY story_id DESC ：  order by 表示排序, story_id 排序依据, desc 倒叙
    // limit : 范围取值 min ~ max
    $sql = "SELECT * FROM story ORDER BY story_id DESC limit ".$offset.",".$page_size;

    //1表示按喜欢like_count 排序
    if($type == 1){
        $sql = "SELECT * FROM story ORDER BY like_count DESC , story_id DESC  limit ".$offset.",".$page_size;
    }elseif($type == 2){
        $sql = "SELECT * FROM story ORDER BY collect_count DESC , story_id DESC  limit ".$offset.",".$page_size;
    }


    $sql_result = mysqli_query($is_open_mySql,$sql);//执行查询结果

    $data_count = mysqli_num_rows($sql_result);


    $storys_array = array();


    for ($i = 0 ; $i <$data_count ; $i++){

        $story_sql =  mysqli_fetch_assoc($sql_result);


         $story_id =  $story_sql['story_id'];


        //搜索喜欢的记录条数
        $sql1 = "SELECT * FROM dg_class_tag  WHERE type_id = '1' AND  user_id = '$user_id' AND story_id = '$story_id'";
        $result1 = mysqli_query($is_open_mySql,$sql1);//执行查询结果
        $like_count = mysqli_num_rows($result1);

        //搜索收藏的记录条数
        $sql2 = "SELECT * FROM dg_class_tag  WHERE type_id = '2' AND  user_id = '$user_id' AND story_id = '$story_id'";
        $result2 = mysqli_query($is_open_mySql,$sql2);//执行查询结果
        $collect_count = mysqli_num_rows($result2);

        //通过user_id查询发送人详情信息
        $story_user_id = $story_sql['user_id'];
        $sql_user = "SELECT * FROM account_info WHERE user_id = '$story_user_id'";
        $story_user_info = mysqli_query($is_open_mySql,$sql_user);
        $user_count = mysqli_num_rows($story_user_info);

        $user_array = array();
        if($user_count != 0){

            $account_info = mysqli_fetch_assoc($story_user_info);
            $user_account = $account_info['account'];
            $user_array = get_user_info($account,$is_open_mySql,$user_account);
        }


        //获取发送的图片地址集
        $sql_image = "SELECT * FROM image WHERE story_id = '$story_id'";
        $sql_image_result = mysqli_query($is_open_mySql,$sql_image);
        $sql_image_rows = mysqli_num_rows($sql_image_result);


        $image_array = array();

        for ($j = 0; $j < $sql_image_rows ; $j++){

            $image_sql =  mysqli_fetch_assoc($sql_image_result);

            $image = array(
                'original_path' => $image_sql['original_path'],
                'small_path' => $image_sql['small_path'],
                'image_id' => $image_sql['id']
            );

            array_push($image_array,$image);
        }




        //////组装所有数据///////
        $story_info = array(
           'content' => $story_sql['content'],
            'story_id' => $story_id,
            'title' => $story_sql['title'],
            'like_count' =>$story_sql['like_count'],
            'collect_count' => $story_sql['collect_count'],
            'user_info' => $user_array,
            'is_like' => $like_count,
            'is_collect' => $collect_count,
            'time' => $story_sql['put_time'],
            'image_paths' => $image_array
        );

        array_push($storys_array,$story_info);

    }


return $storys_array;

}


/////////////////////////////////////////////////
$page = isset($_GET['page'])? $_GET['page']:1;
$page_size = isset($_GET['pageSize'])? $_GET['pageSize']:PAGE_SIZE;
$user_id = isset($_GET['user_id'])? $_GET['user_id']:0;
$account = isset($_GET['account'])?$_GET['account']:0;
$type = isset($_GET['type'])?$_GET['type']:0;//排列类型

if($page_size > 50){
    $page_size = 50;
}

$story_arr = fetchStroys($page , $page_size , $user_id,$type,$account);

$result = array(
    'code' => '200',
    'storys' => $story_arr
);

echo json_encode($result);
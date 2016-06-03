<?php
/**
 * Created by PhpStorm.
 * User: Fix
 * Date: 16/3/31
 * Time: 下午1:22
 */

require_once("mySql_configuration.php");


function fetch_news($page , $page_size){

    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }

    $offset = ($page - 1) * $page_size;


    $sql = "SELECT * FROM news  ORDER BY news_id ASC limit ".$offset.",".$page_size;

    $sql_result = mysqli_query($is_open_mySql,$sql);//执行查询结果

    $data_count = mysqli_num_rows($sql_result);

    $news_array =array();

    for($i =0 ; $i < $data_count ; $i++){

        $news_sql =  mysqli_fetch_assoc($sql_result);

        $news_info = array('title' => $news_sql['title'],
                            'content' => $news_sql['content'],
                            'time' => $news_sql['time'],
                            'imagePath' => $news_sql['imagePath'],
                            'news_id' => $news_sql['news_id']);

        array_push($news_array,$news_info);

    }

    $result_news = array('code' => 200 , 'content' => $news_array);

    return $result_news;


}

////////////
$page = isset($_GET['page'])? $_GET['page']:1;
$page_size = isset($_GET['page_size'])? $_GET['page_size']:10;

$result = fetch_news($page,$page_size);

echo json_encode($result);


<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/14
 * Time: 上午2:07
 */



header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");


/*
 *  $type : 1 表示喜欢； 2表示收藏
 *  $$is_delete : 0 表示
 * */
function update_action_type($type,$story_id,$user_id,$is_delete){


    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => '400', 'message' => '数据库打开失败!');
        return $error;
    }


    //搜索是否有记录存在
    $sql = "SELECT * FROM dg_class_tag  WHERE  type_id = '$type' AND  user_id = '$user_id' AND story_id = '$story_id'";


    $result = mysqli_query($is_open_mySql,$sql);//执行查询结果

    $data_count = mysqli_num_rows($result);

    if($data_count != 0){//如果请求的时候记录已经存在,并且不是做删除操作则表面app段逻辑出现错误


        if($is_delete == 0){
            $error = array('code' => '201' ,'message' => 'record exist!');
            return $error;

        }else{

            $tag_sql = mysqli_fetch_assoc($result);

            $tag_id = $tag_sql['id'];

            $sql_delete = "DELETE FROM dg_class_tag WHERE id = '$tag_id'";

            $is_delete_sql = mysqli_query($is_open_mySql,$sql_delete);

            if($is_delete_sql){

                if($type == 1){
                    $update_sql_delete = "UPDATE story SET like_count =  like_count-1  WHERE  story_id = '$story_id'";
                    $ok_delete =  mysqli_query($is_open_mySql,$update_sql_delete);



                }elseif($type == 2){

                    $update_sql_delete = "UPDATE story SET collect_count = collect_count-1   WHERE story_id = '$story_id'";
                    $ok_delete = mysqli_query($is_open_mySql,$update_sql_delete);

                }

                if(!$ok_delete){
                    return array('code' => '202', 'message' => 'update fail!');

                }

                return    $succeed_delete = array('code' =>'200','message' => 'delete succeed !');

            }else{

                return    $succeed_delete = array('code' =>'201','message' => 'delete fail !');

            }





        }
    }


    //插入一条喜欢或者收藏记录
    $insert_sql = "INSERT INTO dg_class_tag (type_id, user_id, story_id) VALUES ('$type' , '$user_id' ,'$story_id')";
    $is_ok = mysqli_query($is_open_mySql,$insert_sql);

    if($is_ok ==false){

        return array('code' => '202','message' => '添加纪录出现问题!');
    }

    $is_update = true;

    if($type == 1){
        $update_sql = "UPDATE story SET like_count =  like_count+1  WHERE  story_id = '$story_id'";
        $is_update = mysqli_query($is_open_mySql,$update_sql);


    }elseif($type == 2){

        $update_sql = "UPDATE story SET collect_count = collect_count+1   WHERE story_id = '$story_id'";
        $is_update = mysqli_query($is_open_mySql,$update_sql);

    }

    if($is_ok && $is_update){

        $succeed = array('code' => '200', 'message' =>'操作成功');

        return $succeed;

    }else{

        $error = array('code' => '400' , 'message' => '操作失败!');

        return $error;

    }

}


//////////////////////
$type =     isset($_POST['type'])? $_POST['type']:0;
$user_id =  isset($_POST['user_id'])? $_POST['user_id']:0;
$story_id = isset($_POST['story_id'])? $_POST['story_id']:0;
$is_delete = isset($_POST['is_delete'])? $_POST['is_delete']:0;


$result = update_action_type($type,$story_id,$user_id,$is_delete);

echo json_encode($result);



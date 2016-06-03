<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/8
 * Time: 下午10:46
 */


header('Content-type: application/json ; charset=utf-8');

require_once("mySql_configuration.php");


function registerAction($content,$title,$like_count,$collect_count,$user_id,$image_paths){




    $is_open_mySql = connectDb();

    if(empty($is_open_mySql)){
        $error = array('code' => 400, 'message' => '数据库打开失败!');
        return $error;
    }


//插入数据
    $put_time = time();
    $sql = "INSERT INTO story (content, title, like_count, collect_count, user_id, put_time) VALUES ('$content' , '$title' ,'$like_count','$collect_count','$user_id','$put_time')";
    $is_ok = mysqli_query($is_open_mySql,$sql);


    $story_id = mysqli_insert_id($is_open_mySql);

    if(!empty($image_paths)){


    foreach($image_paths as $image_path){



       $images_exploade = explode(',',$image_path);

        $original_host = $images_exploade[0];
        $small_host = $images_exploade[1];

        $sql_image = "INSERT INTO image (original_path, small_path , story_id, type) VALUES ('$original_host', '$small_host', '$story_id' ,'0')";
        $is_add = mysqli_query($is_open_mySql,$sql_image);
        if(!$is_add){
            return array('code' => '400' , 'image_path error');
        }
    }
      }


    if($is_ok){

        $succeed = array('code' => '200', 'message' =>'创建成功');

        return $succeed;

    }else{

        $error = array('code' => '400' , 'message' => '创建失败!');

        return $error;

    }


}


//获取文件后缀名
function extend_1($file_name)
{
    $retVal="";
    $pt=strrpos($file_name, ".");
    if ($pt) $retVal=substr($file_name, $pt+1, strlen($file_name) - $pt);
    return ($retVal);
}


$content     = isset($_POST['content'])    ? $_POST['content']: 'NULL';
$title  = isset($_POST['title']) ? $_POST['title']: 'NULL';
$like_count = isset($_POST['like_count'])? $_POST['like_count']: 0;
$collect_count = isset($_POST['collect_count'])? $_POST['collect_count']: 0;
$user_id = isset($_POST['user_id'])? $_POST['user_id']: 0;
$image_paths = isset($_POST['image_paths'])? $_POST['image_paths']: null;


if($user_id == 0){
    echo json_encode(array('code'=> '400', 'message'=>'user_id is NULL !'));
    exit;
}

$result =  registerAction($content,$title,$like_count,$collect_count,$user_id,$image_paths);

echo json_encode($result);
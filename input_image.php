<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/9
 * Time: 下午4:19
 */



header('Content-type: application/json ; charset=utf-8');

require_once("image_scale.php");

define('ROOT',dirname(__FILE__).'/');

function processFile($files)
{


    //判断用户名是否存在
    if(!isset($_POST['user_id'])){

        return array('code' =>'400', 'message' => '用户名为 NULL');
    }else{

        $fileName = $_POST['user_id'];
    }

    //以用户名创建文件夹
    $creatFile = mkdirDname($fileName);

    if(!$creatFile){

        return array('code' => '400', 'message' => '文件夹权限不足!');
    }



    $image_path_array = array();

    $ii = 0;//多张图片时累加值
    foreach ($files as $key => $value){

         $ii ++;
        $tmpPath = $value['tmp_name'];
        $postfix = extend_1($key);//获取文件后缀名

        $newPath = "images/" .$fileName.'/'. time() .$ii. "." . $postfix;
        $savePath = ROOT.$newPath;



        //移动文件你需要保证对路径的权限为（读写）
        $saveSucceed = move_uploaded_file($tmpPath, $savePath);


        if($saveSucceed){


            //压缩小图
            $image_name =  substr($newPath,0,strrpos($newPath, '.'));


            $filename =(_UPLOADPIC($newPath , $image_name, $postfix));//拷贝图像
            $show_pic_scal=show_pic_scal(100, 100, $filename);//压缩

            //小图路径
            $small_path =  resize($filename,$show_pic_scal[0],$show_pic_scal[1]);

            $host =  $_SERVER['HTTP_HOST'];//获取当前域名
            $original_host ='http://'.$host.'/classes1402/'.$newPath;
            $small_host = 'http://'.$host.'/classes1402/'.$small_path;

            $image_info = array('original_path'=> $original_host,'small_path'=>$small_host);

            array_push($image_path_array , $image_info);


        }else{

            return array('code' => '201', 'content' => '保存失败!');
        }

     }


     return array('code' => '200', 'content' => $image_path_array);


}


//创建文件夹
function mkdirDname($fileName)
{
    $base_dir=ROOT."images/";
    $fso=opendir($base_dir);

    $aimDir = $base_dir.'/'.$fileName;

    if (!file_exists($aimDir)) {
        $result = mkdir($aimDir);
        closedir($fso);
        return $result;
    }else{
        closedir($fso);
        return true;
    }
}



//获取文件后缀名
function extend_1($file_name)
{
    $retVal="";
    $pt=strrpos($file_name, "_");
    if ($pt) $retVal=substr($file_name, $pt+1, strlen($file_name) - $pt);
    return ($retVal);
}


//////////***执行代码***//////////////
//获取表单并保存
$filename = $_FILES;

if($filename){

    $result =  processFile($filename);

    echo json_encode($result);
}else{

    echo json_encode(array('code'=>'400','message' =>'文件不存在!'));
}




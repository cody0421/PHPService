
<?php
/**
 * Created by PhpStorm.
 * User: zhongweidi
 * Date: 16/3/3
 * Time: 下午4:00
 */

header('Content-type: application/json ; charset=utf-8');


define('LOCAL_HOST','rdsrv1i7bn121fpy44r4o.mysql.rds.aliyuncs.com');
define('USER','zwd3413063');
define('MYSQL_PW','zwd3413063123');


 function connectDb(){


     $conn = mysqli_connect(LOCAL_HOST,USER,MYSQL_PW);

     if($conn){

         mysqli_select_db($conn,'dg_class');

//mysql_query("set names utf8");//指定数据集格式

         //消除中文乱码问题
         mysqli_query($conn,"SET NAMES UTF8");
         mysqli_query($conn,"set character_set_client=utf8");
         mysqli_query($conn,"set character_set_results=utf8");

         return $conn;

     }else{

         $result = array(
             'code' => 400,
             'message' => 'open mySql Error!'

         );

         echo json_encode($result);

         return false;
     }

 }

?>
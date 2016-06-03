<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/18
 * Time: 下午2:41
 */

require_once "easemobtest.php";


$i=11;//设置对应的i,进行测试.
switch($i){
    case 10://获取token
        print_r(getToken());
        break;
    case 11://创建单个用户
        print_r(createUser("ben","123456",'bens'));
        break;
    case 12://创建批量用户
        var_dump(createUsers(array(
            array(
                "username"=>"zhangsan",
                "password"=>"123456"
            ),
            array(
                "username"=>"lisi",
                "password"=>"123456"
            )
        )));
        break;
    case 13://重置用户密码
        var_dump(resetPassword("zhangsan","123456"));
        break;
    case 14://获取单个用户
        var_dump(getUser("zhangsan"));
        break;
    case 15://获取批量用户---不分页(默认返回10个)
        var_dump(getUsers());
        break;
    case 16://获取批量用户----分页
        $cursor=readCursor("userfile.txt");
        var_dump(getUsersForPage(10,$cursor));
        break;
    case 17://删除单个用户
        var_dump(deleteUser("zhangsan"));
        break;
    case 18://删除批量用户
        var_dump(deleteUsers(2));
        break;
    case 19://修改昵称
        var_dump(editNickname("zhangsan","小A"));
        break;
    case 20://添加好友----
        var_dump(addFriend("zhangsan","lisi"));
        break;
    case 21://删除好友
        var_dump(deleteFriend("zhangsan","lisi"));
        break;
    case 22://查看好友
        var_dump(showFriends("zhangsan"));
        break;
    case 23://查看黑名单
        var_dump(getBlacklist("zhangsan"));
        break;
    case 24://往黑名单中加人
        $usernames=array(
            "usernames"=>array("zhangsan","lisi")
        );
        var_dump(addUserForBlacklist("wangwu",$usernames));
        break;
    case 25://从黑名单中减人
        var_dump(deleteUserFromBlacklist("zhangsan","lisi"));
        break;
    case 26://查看用户是否在线
        var_dump(isOnline("zhangsan"));
        break;
    case 27://查看用户离线消息数
        var_dump(getOfflineMessages("zhangsan"));
        break;
    case 28://查看某条消息的离线状态
        var_dump(getOfflineMessageStatus("zhangsan","77225969013752296_pd7J8-20-c3104"));
        break;
    case 29://禁用用户账号-----
        var_dump(deactiveUser("zhangsan"));
        break;
    case 30://解禁用户账号-----
        var_dump(activeUser("zhangsan"));
        break;
    case 31://强制用户下线
        var_dump(disconnectUser("zhangsan"));
        break;
    case 32://上传图片或文件
        var_dump(uploadFile("./resource/up/pujing.jpg"));
        //var_dump(uploadFile("./resource/up/mangai.mp3"));
        //var_dump(uploadFile("./resource/up/sunny.mp4"));
        break;
    case 33://下载图片或文件
        var_dump(downloadFile('01adb440-7be0-11e5-8b3f-e7e11cda33bb','Aa20SnvgEeWul_Mq8KN-Ck-613IMXvJN8i6U9kBKzYo13RL5'));
        break;
    case 34://下载图片缩略图
        var_dump(downloadThumbnail('01adb440-7be0-11e5-8b3f-e7e11cda33bb','Aa20SnvgEeWul_Mq8KN-Ck-613IMXvJN8i6U9kBKzYo13RL5'));
        break;
    case 35://发送文本消息
        $from='admin';
        $target_type="users";
        //$target_type="chatgroups";
        $target=array("zhangsan","lisi","wangwu");
        //$target=array("122633509780062768");
        $content="Hello HuanXin!";
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendText($from,$target_type,$target,$content,$ext));
        break;
    case 36://发送透传消息
        $from='admin';
        $target_type="users";
        //$target_type="chatgroups";
        $target=array("zhangsan","lisi","wangwu");
        //$target=array("122633509780062768");
        $action="Hello HuanXin!";
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendCmd($from,$target_type,$target,$action,$ext));
        break;
    case 37://发送图片消息
        $filePath="./resource/up/pujing.jpg";
        $from='admin';
        $target_type="users";
        $target=array("zhangsan","lisi");
        $filename="pujing.jpg";
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendImage($filePath,$from,$target_type,$target,$filename,$ext));
        break;
    case 38://发送语音消息
        $filePath="./resource/up/mangai.mp3";
        $from='admin';
        $target_type="users";
        $target=array("zhangsan","lisi");
        $filename="mangai.mp3";
        $length=10;
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendAudio($filePath,$from="admin",$target_type,$target,$filename,$length,$ext));
        break;
    case 39://发送视频消息
        $filePath="./resource/up/sunny.mp4";
        $from='admin';
        $target_type="users";
        $target=array("zhangsan","lisi");
        $filename="sunny.mp4";
        $length=10;//时长
        $thumb='https://a1.easemob.com/easemob-demo/chatdemoui/chatfiles/c06588c0-7df4-11e5-932c-9f90699e6d72';
        $thumb_secret='wGWIyn30EeW9AD1fA7wz23zI8-dl3PJI0yKyI3Iqk08NBqCJ';
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendVedio($filePath,$from="admin",$target_type,$target,$filename,$length,$thumb,$thumb_secret,$ext));
        break;
    case 40://发文件消息
        $filePath="./resource/up/a.rar";
        $from='admin';
        $target_type="users";
        $target=array("zhangsan","lisi");
        $filename="a.rar";
        $length=10;//时长
        $ext['a']="a";
        $ext['b']="b";
        var_dump(sendFile($filePath,$from="admin",$target_type,$target,$filename,$length,$ext));
        break;
    case 41://获取app中的所有群组-----不分页（默认返回10个）
        var_dump(getGroups());
        break;
    case 42:////获取app中的所有群组--------分页
        $cursor=readCursor("groupfile.txt");
        var_dump($cursor);
        var_dump(getGroupsForPage(2,$cursor));
        break;
    case 43://获取一个或多个群组的详情
        $group_ids=array("1445830526109","1445833238210");
        var_dump(getGroupDetail($group_ids));
        break;
    case 44://创建一个群组
        $options ['groupname'] = "group001";
        $options ['desc'] = "this is a love group";
        $options ['public'] = true;
        $options ['owner'] = "zhangsan";
        $options['members']=Array("wangwu","lisi");
        var_dump(createGroup($options));
        break;
    case 45://修改群组信息
        $group_id="124113058216804760";
        $options['groupname']="group002";
        $options['description']="修改群描述";
        $options['maxusers']=300;
        var_dump(modifyGroupInfo($group_id,$options));
        break;
    case 46://删除群组
        $group_id="124113058216804760";
        var_dump(deleteGroup($group_id));
        break;
    case 47://获取群组中的成员
        $group_id="122633509780062768";
        var_dump(getGroupUsers($group_id));
        break;
    case 48://群组单个加人-----------
        $group_id="122633509780062768";
        $username="lisi";
        var_dump(addGroupMember($group_id,$username));
        break;
    case 49://群组批量加人
        $group_id="122633509780062768";
        $usernames['usernames']=array("lisi","wangwu");
        var_dump(addGroupMembers($group_id,$usernames));
        break;
    case 50://群组单个减人
        $group_id="122633509780062768";
        $username="lisi";
        var_dump(deleteGroupMember($group_id,$username));
        break;
    case 51://群组批量减人-------
        $group_id="122633509780062768";
        //$usernames['usernames']=array("lisi","wangwu");
        $usernames='lisi,wangwu';
        var_dump(deleteGroupMembers($group_id,$usernames));
        break;
    case 52://获取一个用户参与的所有群组
        var_dump(getGroupsForUser("zhangsan"));
        break;
    case 53://群组转让
        $group_id="122633509780062768";
        $options['newowner']="lisi";
        var_dump(changeGroupOwner($group_id,$options));
        break;
    case 54://查询一个群组黑名单用户名列表
        $group_id="122633509780062768";
        var_dump(getGroupBlackList($group_id));
        break;
    case 55://群组黑名单单个加人-----
        $group_id="122633509780062768";
        $username="lisi";
        var_dump(addGroupBlackMember($group_id,$username));
        break;
    case 56://群组黑名单批量加人
        $group_id="122633509780062768";
        $usernames['usernames']=array("lisi","wangwu");
        var_dump(addGroupBlackMembers($group_id,$usernames));
        break;
    case 57://群组黑名单单个减人
        $group_id="122633509780062768";
        $username="lisi";
        var_dump(deleteGroupBlackMember($group_id,$username));
        break;
    case 58://群组黑名单批量减人
        $group_id="122633509780062768";
        $usernames['usernames']=array("lisi","wangwu");
        var_dump(deleteGroupBlackMembers($group_id,$usernames));
        break;
    case 59://创建聊天室
        $options ['name'] = "chatroom001";
        $options ['description'] = "this is a love chatroom";
        $options ['maxusers'] = 300;
        $options ['owner'] = "zhangsan";
        $options['members']=Array("wangwu","lisi");
        var_dump(createChatRoom($options));
        break;
    case 60://修改聊天室信息
        $chatroom_id="124121390293975664";
        $options['name']="chatroom002";
        $options['description']="修改聊天室描述";
        $options['maxusers']=300;
        var_dump(modifyChatRoom($chatroom_id,$options));
        break;
    case 61://删除聊天室
        $chatroom_id="124121390293975664";
        var_dump(deleteChatRoom($chatroom_id));
        break;
    case 62://获取app中所有的聊天室
        var_dump(getChatRooms());
        break;
    case 63://获取一个聊天室的详情
        $chatroom_id="124121939693277716";
        var_dump(getChatRoomDetail($chatroom_id));
        break;
    case 64://获取一个用户加入的所有聊天室
        var_dump(getChatRoomJoined("zhangsan"));
        break;
    case 65://聊天室单个成员添加-----
        $chatroom_id="124121939693277716";
        $username="zhangsan";
        var_dump(addChatRoomMember($chatroom_id,$username));
        break;
    case 66://聊天室批量成员添加
        $chatroom_id="124121939693277716";
        $usernames['usernames']=array('wangwu','lisi');
        var_dump(addChatRoomMembers($chatroom_id,$usernames));
        break;
    case 67://聊天室单个成员删除
        $chatroom_id="124121939693277716";
        $username="zhangsan";
        var_dump(deleteChatRoomMember($chatroom_id,$username));
        break;
    case 68://聊天室批量成员删除---
        $chatroom_id="124121939693277716";
        //$usernames['usernames']=array('zhangsan','lisi');
        $usernames='zhangsan,lisi';
        var_dump(deleteChatRoomMembers($chatroom_id,$usernames));
        break;
    case 69://导出聊天记录-------不分页
        $ql="select+*+where+timestamp>1435536480000";
        var_dump(getChatRecord($ql));
        break;
    case 70://导出聊天记录-------分页
        $ql="select+*+where+timestamp>1435536480000";
        $cursor=readCursor("chatfile.txt");
        //var_dump($cursor);
        var_dump(getChatRecordForPage($ql,10,$cursor));
        break;


}
<?php
include_once("setup.php");
include_once("function.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$uid=(empty($_REQUEST['uid']))?"":$_REQUEST['uid'];

//---------------流程控制區----------------------//
switch($op){
    case "init_password":
    $message=init_password($uid);
    $content=bootstrap($message.user_form($uid));
    break;
    
    case "modify_user":
    modify_user($uid);
    header("location:{$_SERVER['PHP_SELF']}");
    break;
    
    case "add_user":
    add_user();
    header("location:{$_SERVER['PHP_SELF']}");
    break;
    
    default:
    $content=bootstrap(user_form($uid));
}
//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//
function user_form($uid=""){
    global $link,$userSta_arr,$tblUser;
    if(empty($uid)){
        $op="
        <input type='hidden' name='op' value='add_user'>
        <input type='submit' value='新增使用者'>
        ";
        $name=$id=$mail=$phone=$status="";
        
    }else{
        $user_data=get_list_data_arr_from_sn($link,$tblUser,"uid",$uid);
        foreach($user_data as $i => $v){
            foreach($v as $i2=>$v2){
                $$i2=$v2;
            }
        $op="
        <input type='hidden' name='op' value='modify_user'>
        <input type='hidden' name='uid' value='{$uid}'>
        <input type='submit' value='修改使用者'>
        ";
        }
    }

    $main="
    <form class='well col-md-3' method='post' action='{$_SERVER['PHP_SELF']}'>
    <fieldset>
    <label>請輸入姓名</label>
    <input type='text' placeholder='請輸入姓名...' name='name' value='{$name}' class='form-control'>
    <label>請輸入帳號</label>
    <input type='text' placeholder='請輸入帳號...' name='id' value='{$id}' class='form-control'>
    <label>請輸入mail</label>
    <input type='text' placeholder='請輸入mail...' name='mail' value='{$mail}' class='form-control'>
    <label>請輸入手機</label>
    <input type='text' placeholder='請輸入手機...' name='phone' value='{$phone}' class='form-control'>
    <label>請核選狀態</label>".array_to_radio($userSta_arr,false,"status",$status)."
    {$op}
    </fieldset>
    </form>
    <div class='col-md-9'>".user_list()."</div>
    ";
    return $main;
}

function  add_user(){
    global $link,$tblUser;
    $sql="insert into `{$tblUser}` (`id`, `name`, `mail`, `phone`, `status`, `password`) values 
            ('{$_POST['id']}','{$_POST['name']}','{$_POST['mail']}','{$_POST['phone']}',
            '{$_POST['status']}','{$_POST['id']}')";
    $result=mysql_query($sql,$link) or die_content("新增使用者失敗".mysql_error());
}

function user_list(){
    global $link,$tblUser,$userSta_arr;
    $main="<table class='table table-condensed table-hover table-bordered table-striped'>
    <tr><th>姓名</th><th>郵件</th><th>手機</th><th>狀態</th><th>帳號</th><th>功能</th></tr>
    ";
    $data=get_list_data_arr_from_sn($link,$tblUser);
    foreach($data as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $status_show=($status=='ok')?"
        <span class='label label-success'>{$userSta_arr[$status]}</span>
        ":"
        <span class='label label-important'>{$userSta_arr[$status]}</span>    
        ";
        $main.="<tr><td>{$name}</td><td>{$mail}</td><td>{$phone}</td><td>{$status_show}</td><td>{$id}</td><td>
        <a class='btn btn-info' href='{$_SERVER['PHP_SELF']}?op=user_modify_form&uid={$uid}'>修改資料</a>
        <a class='btn btn-warning' href='{$_SERVER['PHP_SELF']}?op=init_password&uid={$uid}'>恢復密碼</a>
        </td></tr>";
    }
    $main.="</table>";
    return $main;    
}
function modify_user($uid){
    global $link,$tblUser;
    $sql="update `{$tblUser}` set `id`='{$_POST['id']}',`name`='{$_POST['name']}',`mail`='{$_POST['mail']}',`phone`='{$_POST['phone']}',`status`='{$_POST['status']}' where uid='{$uid}'";
    //die($sql);
	mysql_query($sql,$link) or die_content("修改資料失敗".mysql_error());
}
function init_password($uid){
    global $link,$tblUser;
    $sql="update `{$tblUser}` set `password`=id where uid='{$uid}'";
    //die($sql);
	mysql_query($sql,$link) or die_content("修改資料失敗".mysql_error());
    $message=mail_to($uid);
    return  $message;
}

//寄信給大家
function mail_to($uid=""){
    global $link,$tblUser,$title;
    //寄信的前置工作
    $subject_title="{$title}密碼更換通知信函";
    include_once('class/class.phpmailer.php');
    mb_internal_encoding('UTF-8');    // 內部預設編碼改為UTF-8
    $mailto=new PHPMailer(); // defaults to using php "mail()"
    $mailto->IsSendmail(); // telling the class to use SendMail transport
    $mailto->CharSet = "utf8"; 
    $mailto->SetFrom('aandd0606@gmail.com',"{$title}管理員");
    //取得個人資料
    $user_data=get_list_data_arr_from_sn($link,$tblUser,"uid",$uid);
    foreach($user_data as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
    }

    $mailto->AddAddress($mail,$name);
    $mail_subject="{$title}密碼更換通知信函";
    $mailto->Subject=mb_encode_mimeheader($mail_subject,"UTF-8");
    $mailto->AltBody= "{$title}密碼更換通知信函"; // optional,
    $body="<h1>{$name}您好</h1><br>";
    $body.="<h1>以下是您的個人資料</h1>";
    $body.="手機：{$phone}mail：{$mail}帳號：{$id}<br>您的密碼被系統管理員更改為預設密碼{$name}，請記得<a href='"._WEB_ROOT_URL."'>按此馬上回到系統更改密碼。</a>
    ";
    $mailto->MsgHTML($body);

    if(!$mailto->Send()) {
      $feedback="<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>密碼修改信件無法寄出</div>";
    } else {
      $feedback="<div class='alert alert-error'><button type='button' class='close' data-dismiss='alert'>×</button>密碼修改信件已經寄出</div>";
    }
    $mailto->ClearAddresses();
    return $feedback;
}
?>
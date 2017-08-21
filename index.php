<?php
include_once("setup.php");
include_once("function.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$book_sn=(empty($_REQUEST['book_sn']))?"":$_REQUEST['book_sn'];
$id=(empty($_REQUEST['id']))?"":$_REQUEST['id'];
$password=(empty($_REQUEST['password']))?"":$_REQUEST['password'];

//---------------流程控制區----------------------//
switch($op){
    //更換密碼流程
    case "update_password":
    update_password($_POST['password'],$_POST['id']);
    header("location:"._WEB_ROOT_URL."index.php");
    break;
    //登出
    case "logout":
    session_destroy();
    header("location:"._WEB_ROOT_URL."index.php");
    break;
    //處理ajax登入
    case "login":
    $content=user_login_check($id,$password);
    //$content=$id;
    break;
    
    default:
    $content=bootstrap(index(),"","",userajaxlogin_js_fun());
}
//---------------權限或身分檢查區域----------------------//
if(!empty($_SESSION['id'])){
    if(checkuserpassword($_SESSION['id'])){
        $content=bootstrap(checkuserpassword($_SESSION['id']));
    }
}
//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//
function index(){
    global $link;
    if(!$_SESSION){
       error_reporting(0);
    }else{
    $user_have_link=user_have_link($_SESSION['status'],$_SESSION['id']);    
    }
    $main="
    <div class='container'>
    <div class='row'>
    <div class='col-md-3'>
    <!--登入表單-->
    <div id='userloginstatus'>
    ".user_login_form()."
    </div>
    </div>
    <div class='col-md-9'>
    <!--首頁內容權限列表-->
    {$user_have_link}
    </div>
    </div>
    </div>
    ";
    return $main;
}



function userajaxlogin_js_fun(){
    $main="
    <script>
    $(function(){
        $('#login').click(function(){
            var id = $('input[name=\"id\"]').val();
            var password = $('input[name=\"password\"]').val();
            var op = $('input[name=\"op\"]').val();
            $.post('{$_SERVER['PHP_SELF']}',{id:id,password:password,op:op},function(msg){
                $('#userloginstatus').html(msg)
            });
        });
    });
    </script>
    ";
    return $main;
}

function user_login_check($input_id="",$input_password=""){
    global $link,$tblUser,$admin_password,$admin_id;
    error_reporting(0);
    //取得使用者資料
    $sql="select * from `{$tblUser}` where id='{$input_id}' AND status='ok'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
    }
    $failure="<span class='label label-important'>認證失敗，請再輸入一次帳號密碼</span>".userajaxlogin_js_fun().user_login_form();
    if($input_id==$admin_id && $input_password==$admin_password){
        $main=user_data_tbl($input_id);
        $_SESSION['id']=$input_id;
        $_SESSION['status']='admin';
    }elseif(!$result){
        $main=$failure;
    }elseif($password==$input_password){
        $main=user_data_tbl($id);
        $_SESSION['id']=$input_id;
        $_SESSION['status']=$status;
    }else{
        $main=$failure;
    }
    return $main;
}



function update_password($password="",$id=""){
    global $link,$tblUser;
    $sql="update `{$tblUser}` set password='{$password}' where id='{$id}'";
    mysql_query($sql,$link) or die_content("更新密碼失敗".mysql_error());
}
function user_have_link($status="",$id=""){
    global $link,$page_menu;
    $menu_status="";
    if($status=="admin"){
        $menu=$page_menu;
    }elseif($status=="ok"){
        $menu=array(
            "首頁"=>"index.php",
            "保養記錄管理"=>"log_admin.php",
            "各式報表匯出"=>"report.php"
        );
    }else{
        $menu=array();
    }
    foreach($page_menu as $i=>$v){
        if(in_array($v,$menu)){
            $menu_status.="
            <div class='well'>
            <a href='"._WEB_ROOT_URL."{$v}' class='btn btn-success'>{$i}</a>
            可以使用
            </div>
            ";
        }else{
            $menu_status.="
            <div class='well'>
            <a href='#' class='btn well btn-danger'>{$i}</a>
            不能使用
            </div>
            ";
        }
    }
    if(empty($id)){
        $menu_status="
    <div class='alert'>
    <button type='button' class='close' data-dismiss='alert'>×</button>
    <strong>Warning!</strong><h3>您尚未登入</h3>
    </div>
        ";
    }
    return $menu_status;
}
?>
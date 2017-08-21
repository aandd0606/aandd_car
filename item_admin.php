<?php
include_once("setup.php");
include_once("function.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$item_sn=(empty($_REQUEST['item_sn']))?"":$_REQUEST['item_sn'];
//---------------流程控制區----------------------//
switch($op){
    //ajax呈現新增表單
    case "item_add_form":
    $content=item_admin_form($item_sn);
    break;
    //ajax呈現修改表單
    case "item_modify_form":
    $content=item_admin_form($item_sn);
    break;
    //ajax刪除保修項目
    case "item_del":
    item_del($item_sn);
    break;
    //修改保修項目動作
    case "modify_item":
    modify_item();
    header("location:{$_SERVER['PHP_SELF']}");
    break;
    //新增保修項目動作
    case "add_item":
    add_item();
    header("location:{$_SERVER['PHP_SELF']}");
    break;
   
    default:
    $content=bootstrap(index(item_list()),"","",add_form_slideToggle_jsfun());
}
//---------------權限或身分檢查區域----------------------//
//檢查密碼
if(!empty($_SESSION['id'])){
    if(checkuserpassword($_SESSION['id'])){
        $content=bootstrap(checkuserpassword($_SESSION['id']));
    }
}
//檢查權限
 if(ifcheckuser("ok")){
    $content=bootstrap(checkuserstatus());
 }
//------------------輸出區----------------------//
echo $content;
//----------------------函數區-------------------------//
function index($item_content=""){
    global $link;
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
    <!--保修項目管理內容-->
    {$item_content}
    </div>
    </div>
    </div>
    ";
    return $main;
}
function item_list(){
    global $link,$tblItem;
    $main="
    <table class='table table-condensed table-hover table-bordered table-striped'>
    <tr><th>保修項目</th><th>價格</th><th>功能</th></tr>
    ";
    $item_data_arr=get_list_data_arr_from_sn($link,$tblItem);
    foreach($item_data_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $main.="<tr id='tr_{$item_sn}'><td>{$item_title}</td><td>{$item_price}</td><td>
        <a class='btn btn-success modify_item' id='modify_{$item_sn}'>修改</a>
        <a class='btn btn-danger del_item' id='del_{$item_sn}'>刪除</a>
        </td></tr>";
    }
    $main.="<tr><td colspan=3>
    <a id='add_item_form' class='btn btn-success'>新增保修項目</a>
    </td></tr>";
    $main.="
    <tr id='item_form' style='display:none;'><td colspan=4>
    </td></tr>
    ";
    $main.="</table>";
    return $main;
}

function item_admin_form($item_sn=""){
    global $link,$tblItem;
    error_reporting(0);
    if(empty($item_sn)){
        $op="
        <input type='hidden' name='op' value='add_item'>
        <input type='submit' value='新增項目' id='submit' class='form-control'>
        ";
    }else{
        $item_data=get_list_data_arr_from_sn($link,$tblItem,"item_sn",$item_sn);
        foreach($item_data as $i => $v){
            foreach($v as $i2=>$v2){
                $$i2=$v2;
            }
        }
        $op="
        <input type='hidden' name='op' value='modify_item'>
        <input type='hidden' name='item_sn' value='{$item_sn}'>
        <input type='submit' value='修改項目' id='submit' class='form-control btn btn-warning'>
        ";
    }
    $main="
    <script>
    $(function(){
        $('#submit').click(function(){
            $('#submit').fadeOut();
        });
    });
    </script>
        <form method='post' action='{$_SERVER['PHP_SELF']}'>
        <label>請輸入保修項目</label>
        <input type='text' placeholder='請輸入保修項目...' name='item_title' value='{$item_title}' class='form-control'>
        <label>請輸入費用金額</label>
        <input type='text' placeholder='請輸入費用金額...' name='item_price' value='{$item_price}' class='form-control'>
        {$op}
    </form>
    ";
    return $main;
}

function add_form_slideToggle_jsfun(){
    $main="
    <script>
    $(function(){
        $('#add_item_form').click(function(){
            $.post('{$_SERVER['PHP_SELF']}',{op:'item_add_form'},function(msg){
                //$('#item_form').slideToggle('slow');
                $('#item_form').css('display','block');
                $('#item_form td:first').html(msg);
            });
        });
        $('.del_item').click(function(){
            var del_item_sn = $(this).attr('id').split('_');
            $.post('{$_SERVER['PHP_SELF']}',{item_sn:del_item_sn[1],op:'item_del'},function(msg){
                $('#tr_' + del_item_sn[1]).remove();    
            });
        });
        $('.modify_item').click(function(){
            var modify_item_sn = $(this).attr('id').split('_');
            $.post('{$_SERVER['PHP_SELF']}',{item_sn:modify_item_sn[1],op:'item_modify_form'},function(msg){
                $('#item_form').css('display','block');
                $('#item_form td:first').html(msg);
            });

        });
        $('#submit').click(function(){
            $('#submit').fadeOut();
        });
        
    });
    </script>
    ";
    return $main;
}

function add_item(){
    global $link,$tblItem;
    $sql="insert into `{$tblItem}` (`item_price`, `item_title`) values 
    ('{$_POST['item_price']}','{$_POST['item_title']}')";
	mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
}

function item_del($item_sn=""){
    global $link,$tblItem;
    $sql="delete from `{$tblItem}` where item_sn='{$item_sn}'";
    mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
    return "刪除資料成功";
}

function modify_item(){
    global $link,$tblItem;
    $sql="replace into `{$tblItem}` (`item_price`, `item_title`, `item_sn`) values 
    ('{$_POST['item_price']}','{$_POST['item_title']}','{$_POST['item_sn']}')";
    mysql_query($sql,$link) or die_content("修改資料失敗".mysql_error());
    return "修改資料成功";
}
?>
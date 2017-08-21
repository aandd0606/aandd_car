<?php
include_once("setup.php");
include_once("function.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$car_id=(empty($_REQUEST['car_id']))?"":$_REQUEST['car_id'];
$log_sn=(empty($_REQUEST['log_sn']))?"":$_REQUEST['log_sn'];
//---------------流程控制區----------------------//
switch($op){
    //ajax刪除資料 並確認權限
    case "log_del":
    $content=log_del($log_sn);
    break;
    
    //更新資料
    case "update_mainten_log":
    insert_mainten_log($log_sn);
    header("location:{$_SERVER['PHP_SELF']}");
    
    //ajax動態載入修改表單
    case "modify_log_form":
    $content=log_form($_POST['car_id'],$_POST['log_sn']);
    break;
    
    //ajax動態載入新增表單
    case "add_log_form":
    $content=log_form($_POST['car_id']);
    break;
    
    case "insert_mainten_log":
    insert_mainten_log();
    header("location:{$_SERVER['PHP_SELF']}");
    break;
    
    case "find_car_data":
    $content=find_car_data();
    break;

    default:
    $content=bootstrap(index(find_car_form()));
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
function find_car_form(){
    global $link,$tblCar;
    //找出目前有的車號
    $car_data=get_list_data_arr_from_sn($link,$tblCar);
    foreach($car_data as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $car_id_arr[]=$car_id;
    }
   $car_id_js_arr=json_encode($car_id_arr);
   
    $main="
    <script src="._WEB_ROOT_URL."/js/My97DatePicker/WdatePicker.js></script>
    <script>
    $(function(){
		var availableTags = {$car_id_js_arr};
        $( '#car_id' ).autocomplete({
			source: availableTags
		});
        $('#car_cilck').click(function(){
            $.post(
                '{$_SERVER['PHP_SELF']}',
                {'car_id':$('#car_id').val(),'op':'find_car_data'},
                function(data){
                    $('#car_mainten_log').html(data);    
                }
            );
        });
    });
   </script>
    <form action='{$_SERVER['PHP_SELF']}' method='post'>
    輸入車牌號碼：<input name='car_id' id='car_id' type='text' class='form-control'><input id='car_cilck' type='button' name='op' value='尋找保養記錄' class='form-control btn btn-success'></button>
    </form>
    ";
    $main.="<div id='car_mainten_log'></div>";
    return $main;
}

function find_car_data(){
    global $link,$tblCar,$tblItem;
    error_reporting(0);
    //取得所有保修項目並製作下拉選單
    //$item_select=item_select();
    //取得車藉資料
    $car_data_arr=get_list_data_arr_from_sn($link,$tblCar,"car_id",$_POST['car_id']);
    foreach($car_data_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
    }
    $main="
    <script>
    $(function(){
        $('.addlogform').click(function(){
            var car_id_arr = $(this).attr('id').split('_');
            $.post(
                '{$_SERVER['PHP_SELF']}',
                {op:'add_log_form',car_id:car_id_arr[1]},
                function(msg){
                    $('#logform').html(msg);
            }); 
        });
        $('.modify_form').click(function(){
            var log_sn_arr = $(this).attr('id').split('_');
            $.post(
                '{$_SERVER['PHP_SELF']}',
                {op:'modify_log_form',log_sn:log_sn_arr[1],car_id:log_sn_arr[2]},
                function(msg){
                    $('#logform').html(msg);
            }); 
        });
        $('a.open').toggle(
            function(event){
                var log_sn = $(this).attr('id');
                 $(this).text('閉合');
                $('.detail_' + log_sn).fadeIn(); 
            },
            function(event){
                var log_sn = $(this).attr('id');
                 $(this).text('開啟');
                $('.detail_' + log_sn).fadeOut();
            }
        );
        $('.del_log').click(function(){
            if (!confirm('確定進行點選的動作'))
            return false;
            var del_log_sn = $(this).attr('id').split('_');
            $.post('{$_SERVER['PHP_SELF']}',{log_sn:del_log_sn[2],op:'log_del',uid:del_log_sn[1]},function(msg){
                if(msg == false){
                    alert('您沒有權限刪除記錄');
                }else{
                    $('#tr_' + del_log_sn[2]).remove();
                }    
            });
        });
        
    });
   </script>
    ";
    
    $main.="
    <table class='table table-condensed table-hover table-bordered table-striped'>
    <caption><h3>車藉資料</h3></caption>
    <tr><td>車廠：{$car_com}</td><td>車型：{$car_style}</td><tr>
    <tr><td>車主：{$car_owner}</td><td>手機：{$car_phone}</td><tr>
    <tr><td>電話：{$car_tel}</td><td>電子郵件：{$car_email}</td><tr> 
    </table>
    <a class='btn btn-warning addlogform' id='addlogform_{$_POST['car_id']}'>新增維修表單</a>
    ";
    $main.="<div id='logform'></div>";
    $main.=car_log_list($_POST['car_id']);
    return $main;
}

function item_select(){
    global $link,$tblCar,$tblItem;
    $item_arr=array();
    $all_item_arr=get_list_data_arr_from_sn($link,$tblItem);
    foreach($all_item_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $val="{$item_title} | {$item_price}";
        $item_arr[$val]=$val;
   }
    $item_select="
    <select id='item_select_form' name='item_select'>
    ".array_to_select($item_arr)."
    </select>
    ";
    return $item_select;
}

function insert_mainten_log($log_sn=""){
    global $link,$tblLog,$tblContent;
    
    if(!empty($log_sn)){
        $sql="delete from `{$tblContent}` where log_sn='{$log_sn}'";
        mysql_query($sql,$link) or die_content("刪除資料失敗".mysql_error());
        $sql="replace into `{$tblLog}` (`log_sn`, `car_sn`, `mainten_date`, `mainten_kilometer`, `suggest`, `uid`) values ('{$log_sn}','{$_POST['car_sn']}','{$_POST['mainten_date']}','{$_POST['mainten_kilometer']}','{$_POST['suggest']}','{$_SESSION['id']}')";
    	$result=mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
        $log_sn=mysql_insert_id();
        foreach($_POST['item_title'] as $i=>$v){
            $sql="insert into `{$tblContent}` (`log_sn`, `item`, `item_price`) values 
            ('{$log_sn}','{$v}','{$_POST['item_price'][$i]}')";
            mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
        }
    }else{
        $sql="insert into `{$tblLog}` (`car_sn`, `mainten_date`, `mainten_kilometer`, `suggest`, `uid`) values ('{$_POST['car_sn']}','{$_POST['mainten_date']}','{$_POST['mainten_kilometer']}','{$_POST['suggest']}','{$_SESSION['id']}')";
    	$result=mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
        $log_sn=mysql_insert_id();
        foreach($_POST['item_title'] as $i=>$v){
            $sql="insert into `{$tblContent}` (`log_sn`, `item`, `item_price`) values 
            ('{$log_sn}','{$v}','{$_POST['item_price'][$i]}')";
            mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
        }
    }
}

function car_log_list($car_id=""){
    global $link,$tblCar,$tblLog,$tblContent;
    $log_content=get_total_avg_price($car_id);
    $log_content.="
        <tr>
        <th>保養日期</th>
        <th>保養公里數</th>
        <th>維修人</th>
        <th>功能按鈕</th>
        </tr>
    ";
    $sql="select * from `{$tblCar}`,`{$tblLog}` where car.car_sn=log.car_sn AND car.car_id='{$car_id}'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $log_content.="
            <tr id='tr_{$log_sn}'>
            <th>{$mainten_date}</th>
            <th>{$mainten_kilometer}</th>
            <th>{$uid}</th>
            <th>
            <a class='open btn btn-warning' id='{$log_sn}'>展開</a>
            <a class='btn btn-info modify_form' id='modify_{$log_sn}_{$car_id}'>修改</a>
            <a class='del_log btn btn-danger' id='del_{$uid}_{$log_sn}'>刪除</a>
            <a class='btn btn-primary' href='print_pdf.php?log_sn={$log_sn}'>列印報表</a>
            </th>
            </tr>
            <tr class='detail_{$log_sn}' style='display:none;'><td>保養建議事項</td><td colspan=3>{$suggest}</td></tr>
            ";
        $log_content.=get_content($log_sn);
                    
    }
    $main="
    <table class='table table-condensed table-hover table-bordered table-striped'>
    {$log_content}
    </table>
    ";
    return $main;
    
}
function get_content($log_sn=""){
    global $link,$tblContent;
    $content="";
    $total_price=0;
    $content_arr=get_list_data_arr_from_sn($link,$tblContent,"log_sn",$log_sn);
    foreach($content_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $content.="<tr class='detail_{$log_sn}' style='display:none;'><td></td><td colspan=2>{$item}</td><td>{$item_price}</td></tr>";
        $total_price+=$item_price;
    }
    $content.="<tr class='detail_{$log_sn}' style='display:none;'><td></td><td colspan=2><span class='label label-important'>該次保養總金額：{$total_price}元整</span></td></tr>";
    $main="
    <tr class='detail_{$log_sn}'  style='display:none;'><th></th><th colspan=2>保修項目</th><th>保修金額</th></tr>
    {$content}
    ";
    return $main;
    
}

function get_total_avg_price($car_id=""){
    global $link,$tblContent,$tblCar,$tblLog;
    $car_arr=get_list_data_arr_from_sn($link,$tblCar,"car_id",$car_id);
    foreach($car_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
    }
    //取得該車的所有保養記錄
    $sql="select * from `{$tblLog}` where car_sn='{$car_sn}' order by log_sn";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    $num=mysql_num_rows($result);
    $all_total_price=0;
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $sql="select SUM(item_price) as total_price from `{$tblContent}` where log_sn='{$log_sn}'";

        $result2=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
        while($data2=mysql_fetch_assoc($result2)){
            foreach($data2 as $i2=>$v2){
                $$i2=$v2;
            }
            $all_total_price+=$total_price;
        }
    }
    $avg=$all_total_price/$num;
    $avg=round($avg,1);
    $main="<span  class='label label-success'>總消費金額：{$all_total_price}</span><span class='label label-info'>每次平均消費金額：{$avg}</span>";
    return $main;
}

function log_form($car_id="",$log_sn=""){
    global $link,$tblContent,$tblCar,$tblLog;
    $item_select=item_select();
    //取得車藉資料
    $car_data_arr=get_list_data_arr_from_sn($link,$tblCar,"car_id",$car_id);
    foreach($car_data_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
    }
    if($log_sn){
        //修改表單的資料
        //取得保養記錄
        $log_data_arr=get_list_data_arr_from_sn($link,$tblLog,"log_sn",$log_sn);
        foreach($log_data_arr as $i => $v){
            foreach($v as $i2=>$v2){
                $$i2=$v2;
            }
        }
        //權限判斷登入的人與線上的session相同才允許出現修改表單。
        if($uid!=$_SESSION['id']){
            return "<span class='label label-important'>您不是該次保養記錄的使用者，沒有權限更改</span>";
        }
        
        $now_date=$mainten_date;
        $title="修改保養記錄表單";
        $op="
        <input type='hidden' name='op' value='update_mainten_log'>
        <input type='hidden' name='car_sn' value='{$car_sn}'>
        <input type='hidden' name='log_sn' value='{$log_sn}'>
        <input type='hidden' name='car_id' value='{$car_id}'>
        <input type='submit' value='修改保修記錄並列印此次維修報表'>
        ";
    }else{
        //新增表單的資料
        $title="新增保養記錄表單";
        $now_date=date("Y-m-d");
        $mainten_kilometer="";
        $suggest="";
        $op="
        <input type='hidden' name='op' value='insert_mainten_log'>
        <input type='hidden' name='car_sn' value='{$car_sn}'>
        <input type='hidden' name='log_sn' value='{$log_sn}'>
        <input type='hidden' name='car_id' value='{$car_id}'>
        <input type='submit' value='新增保修記錄並列印此次維修報表'>
        ";
        //取得所有保修項目並製作下拉選單

    }
    $content_td="";
    //取得保修內容資料
    $content_data_arr=get_list_data_arr_from_sn($link,$tblContent,"log_sn",$log_sn);
    foreach($content_data_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $content_td.="
        <tr id='{$item}'><td><input type='text' name='item_title[]' value='{$item}'></td>
        <td><input type='text' name='item_price[]' value='{$item_price}'></td>
        <td><a class='btn btn-danger del_a' id='del_{$item}'>刪除</a></td></tr>
        ";
    }
    
    $main="
    <script>
    $(function(){
        $('#item_select_form').change(function(){
            var nowval = $(this).val();
            if(nowval != ''){
                var item_arr = nowval.split(' | ')

                $('tbody#form_content').append('<tr id=\"'+ item_arr[0] +'\"><td><input type=\"text\" name=\"item_title[]\" value=\"'+ item_arr[0] +'\"></td><td><input type=\"text\" name=\"item_price[]\" value=\"'+ item_arr[1] +'\"></td><td><a class=\"btn btn-warn del_a\" id=\"del_'+ item_arr[0] +'\">刪除</a></td></tr>');
            }
            cal_price();
        });
        $('a.del_a').click(function(){
            var del_id_arr = $(this).attr('id');
            del_id = del_id_arr.split('_');
            console.log(del_id[1]);
            $('#' + del_id[1]).fadeOut(500,function(){
                $(this).remove();
                cal_price();
            });
        });
        $('input[name=\"item_price[]\"]').change(function(){
            cal_price();
        });
        function cal_price(){
            var total_price=0;
            if($('input[name=\"item_price[]\"]').length ){
                $('input[name=\"item_price[]\"]').each(function(){
                    total_price += parseInt($(this).val());
                    console.log(total_price);
                    $('#total_price').html('<span class=\"label label-warning\">目前保修總價：' + total_price + '</span>');            
                });
            }else{
                total_price=0;
                $('#total_price').html('<span class=\"label label-warning\">目前保修總價：' + total_price + '</span>');
            }
        }
    });
    </script>
    <form action='{$_SERVER['PHP_SELF']}' method='post'>
    <table class='table table-condensed table-hover table-bordered table-striped'>
    <caption><h3>{$title}</h3></caption>
    <tbody id='form_content'>
    <tr><td>今次保養日期：<input type='text' name='mainten_date' value='{$now_date}' onClick='WdatePicker()'></td>
    <td colspan=2>今次保養公里數:<input type='text' name='mainten_kilometer' value='{$mainten_kilometer}'></td><tr>
    <tr><td colspan=3>保養建議事項：<textarea class='span8' name='suggest'>{$suggest}</textarea></td><tr>
    <tr><td  colspan=3>
    {$item_select}
    <div class='alert'>
    <button type='button' class='close' data-dismiss='alert'>×</button>
    <strong>Warning!</strong>請點選要保修的項目，並視情況需要修改價格與內容。
    </div>
    </td><tr>
    <tr><td  colspan=3 id='total_price'></td><tr>
    <tr><th>維修項目</th><th>維修價格</th><th>功能按鈕</th></tr>
    {$content_td}
    </tbody>
    </table>
    {$op}
    </form>
    ";
    return $main;
}

function log_del($log_sn=""){
    global $link,$tblContent,$tblLog;
    if($_POST['uid']!=$_SESSION['id']){
        return false;
    }
    $sql="delete from `{$tblContent}` where log_sn='{$log_sn}'";
    mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
    $sql="delete from `{$tblLog}` where log_sn='{$log_sn}'";
    mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
     return true;
}
?>
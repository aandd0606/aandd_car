<?php
//輸入陣列產生下拉式選單
/*$arr要出入的陣列   $default_val預設的值   $option要不要出現請選擇這個選項   $use_v以索引為值還是以值為值   $validate是否要驗證  */
function array_to_select($arr=array(),$option=true,$default_val="",$use_v=false,$validate=false){
	if(empty($arr))return;
	$opt=($option)?"<option value=''>請選擇</option>\n":"";
	foreach($arr as $i=>$v){
		//false則以陣列索引值為選單的值，true則以陣列的值為選單的值
		$val=($use_v)?$v:$i;
		$selected=($val==$default_val)?"selected":"";        //設定預設值
		$validate_check=($validate)?"class='required'":"";
		$opt.="<option value='$val' $selected $validate_check>$v</option>\n";
	}
	return  $opt;
}

//輸入陣列產生單選表單
function array_to_radio($arr=array(),$use_v=false,$name="default",$default_val=""){
	if(empty($arr))return;
	$opt="";
	foreach($arr as $i=>$v){
		$val=($use_v)?$v:$i;
		$checked=($val==$default_val)?"checked='checked'":"";
		$opt.="<input type='radio' name='{$name}' id='{$val}' value='{$val}' $checked><label for='{$val}' style='margin-right:15px;'> $v</label>";
	}
	return $opt;
}
//取出列表的陣列值
//取得資料庫一筆資料的資料陣列
//利用以下的程式碼可以將資料取出
/*
foreach($data as $i => $v){
    foreach($v as $i2=>$v2){
        $$i2=$v2;
    }
}
*/
function get_list_data_arr_from_sn($link="",$table_name="",$sn_name="",$sn=""){
	$data_list=array();
    if(empty($sn_name)){
        $sql="select * from `{$table_name}`";
    }else{
        $sql="select * from `{$table_name}` where {$sn_name}='{$sn}'";
    }
	$result=mysql_query($sql,$link) or die_content("取得{$table_name}資料失敗".mysql_error());
	//取得該資料表所有的值（有多筆資料）並儲存成一個陣列。
    while($data=mysql_fetch_assoc($result)){
        $data_list[]=$data;
    }
    return $data_list;
}

function checkuserpassword($id=""){
    global $link,$tblUser;
    if(empty($id)) return false;
    $sql="select * from `{$tblUser}` where id='{$id}'";
    $result=mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
    }
    if(!isset($password)) $password="";
    if(@$id==$password){//強制更換密碼的內容
        $main="
        <h3>您的密碼目前是預設密碼，我們將強制您更換密碼</h3>
        <form method='post' action='"._WEB_ROOT_URL."index.php'>
        輸入新密碼：<input type='text' name='password'>
        <input type='hidden' name='op' value='update_password' class='span3'><br>
        <input type='hidden' name='id' value='{$id}'> 
        <input type='submit' value='變更密碼'>
        </form>
        ";
        return $main;
    }else{
        return false;
    }
}

function user_login_form(){
    global $link,$tblUser;
    if(empty($_SESSION['id'])){
        $main="
        <form onsubmit='return false'>
        <fieldset>
        <legend>使用者登入表單</legend>
        <label>輸入帳號：</label>
        <input type='text' name='id' id='userid' class='form-control'>
        <label>輸入密碼：</label>
        <input type='text' name='password' class='form-control'>
        <input type='hidden' name='op' value='login'>
        <a type='submit' class='btn btn-primary' id='login'>登入</a>
        </fieldset>
        </form>
        ";
    }else{
        $main=user_data_tbl($_SESSION['id']);
    }
    return $main;
}

function user_data_tbl($id=""){
    global $link,$tblUser,$admin_id,$admin_phone,$admin_mail;
        $user_data_arr=get_list_data_arr_from_sn($link,$tblUser,"id",$id);
        foreach($user_data_arr as $i => $v){
            foreach($v as $i2=>$v2){
                $$i2=$v2;
            }
        }
        if($id==$admin_id){
            $title="<span class='label label-danger'>系統管理員資料</span>";
            $name=$admin_id;
            $phone=$admin_phone;
            $mail=$admin_mail;
        }else{
            $title="<span class='label label-success'>系統使用者資料</span>";
        }
        $main="
        <table class='table table-condensed table-hover table-bordered table-striped'>
        <tr><td colspan=2>{$title}</td></tr>
        <tr><td>姓名</td><td>{$name}</td></tr>
        <tr><td>手機</td><td>{$phone}</td></tr>
        <tr><td>mail</td><td>{$mail}</td></tr>
        <tr><td colspan=2><a href='"._WEB_ROOT_URL."index.php?op=logout' class='btn btn-danger'>登出</a></td></tr>
        </table>
        ";
    return $main;
}

function ifcheckuser($level='ok'){
    if($_SESSION['status']==$level){
        return false;
    }else{
        return true;
    }
}

function checkuserstatus(){
    if(empty($_SESSION['id'])){
        return "
    <div class='alert'>
    <button type='button' class='close' data-dismiss='alert'>×</button>
    <strong>Warning!</strong>您尚未登入。
    </div>
    ";
    }else{
        return "
    <div class='alert'>
    <button type='button' class='close' data-dismiss='alert'>×</button>
    <strong>Warning!</strong>您沒有使用這個頁面功能的權限。
    </div>
    ";
    }
}
?>
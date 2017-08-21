<?php
/*-----------引入檔案區--------------*/
include_once("setup.php");
include_once("function.php");
require _WEB_ROOT_PATH."class/php-excel.class.php";
/*-----------function區--------------*/
/*-----------執行動作判斷區----------*/
$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];
$car_sn=empty($_REQUEST['car_sn'])?"":intval($_REQUEST['car_sn']);
$log_sn=empty($_REQUEST['log_sn'])?"":intval($_REQUEST['log_sn']);
$item_sn=empty($_REQUEST['item_sn'])?"":intval($_REQUEST['item_sn']);

switch($op){
	case "year_report":
	year_report($_POST['year']);
    break;
	case "month_report":
	month_report($_POST['year'],$_POST['month']);
    break;
    default:
    $content=bootstrap(index(form()));
    echo $content;
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

//
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

function form(){
	$main="
    <form action='{$_SERVER['PHP_SELF']}' method='post'>
    輸入年：<input type='text' name='year' value='' class='form-control col-md-2'>
    輸入月份：<input type='text' name='month' value=''  class='form-control col-md-2'>
    <input type='hidden' name='op' value='month_report'>
    <input type='submit' value='輸出該月報表'  class='form-control col-md-2 btn btn-success'>
    </form>
    <form action='{$_SERVER['PHP_SELF']}' method='post'>
    輸入年：<input type='text' name='year' value=''  class='form-control col-md-2'>
    <input type='hidden' name='op' value='year_report'  class='form-control col-md-2'>
    <input type='submit' value='輸出該年報表'  class='form-control btn btn-danger'>
    </form>
    ";
	return $main;
}

function month_report($year="",$month=""){
    global $link,$tblCar,$tblLog,$tblContent;
    $report_log_sn_arr=check_date($year,$month);
    $content="";
    $content_arr=array();
    $all_total=0;
    $log_content_arr=array();
    $report_data=array();
    $report_data[]=array("車號","車主","電話","維修日期","維修公里數");
    foreach($report_log_sn_arr as $v){
        $sql="select * from `{$tblLog}` as log,`{$tblCar}` as car where log.car_sn=car.car_sn AND log.log_sn='{$v}'";
    	$result = mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
        while($data=mysql_fetch_assoc($result)){
            foreach($data as $i=>$v){
                $$i=$v;
            }
        }
        $report_data[]=array($car_id,$car_owner,$car_phone,$mainten_date,$mainten_kilometer);
        $sql2="select * from `{$tblContent}` where log_sn='{$log_sn}'";
    	$result2 = mysql_query($sql2,$link) or die_content("查詢資料失敗".mysql_error());
        $log_content="";
        $total=0;
        $report_data[]=array("","保修項目","價格");
        while($data2=mysql_fetch_assoc($result2)){
            foreach($data2 as $i2=>$v2){
                $$i2=$v2;
            }
            $report_data[]=array("",$item,$item_price);
            //$log_content_arr[]=array("",$item,$item_price);
            $content.="{$item}-{$item_price}<br>";
            $total+=$item_price;
            $all_total+=$item_price;
        }
        $report_data[]=array("","小計",$total);
        $log_content_arr[]=array("","小計",$total);
    }
    $report_data[]=array("本月總計",$all_total);
    $xls = new Excel_XML('UTF-8', false, 'report');
    $xls->addArray($report_data);
    $xls->generateXML("{$year}年{$month}月保修報表");

}

function year_report($year=""){
    global $link,$tblCar,$tblLog,$tblContent;
    $report_log_sn_arr=check_date($year);
    $content="";
    $content_arr=array();
    $all_total=0;
    $log_content_arr=array();
    $report_data=array();
    $report_data[]=array("車號","車主","電話","維修日期","維修公里數");
    foreach($report_log_sn_arr as $v){
        $sql="select * from `{$tblLog}` as log,`{$tblCar}` as car where log.car_sn=car.car_sn AND log.log_sn='{$v}'";
    	$result = mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
        while($data=mysql_fetch_assoc($result)){
            foreach($data as $i=>$v){
                $$i=$v;
            }
        }
        $report_data[]=array($car_id,$car_owner,$car_phone,$mainten_date,$mainten_kilometer);
        $sql2="select * from `{$tblContent}` where log_sn='{$log_sn}'";
    	$result2 = mysql_query($sql2,$link) or die_content("查詢資料失敗".mysql_error());
        $log_content="";

        $total=0;
        $report_data[]=array("","保修項目","價格");
        while($data2=mysql_fetch_assoc($result2)){
            foreach($data2 as $i2=>$v2){
                $$i2=$v2;
            }
            $report_data[]=array("",$item,$item_price);
            $content.="{$item}-{$item_price}<br>";
            $total+=$item_price;
            $all_total+=$item_price;
        }
        $report_data[]=array("","小計",$total);
        $log_content_arr[]=array("","小計",$total);
    }
    $report_data[]=array("本年總計",$all_total);
    $xls = new Excel_XML('UTF-8', false, 'report');
    $xls->addArray($report_data);
    $xls->generateXML("{$year}年保修報表");

}

function check_date($year="",$month=""){
    global $link,$tblLog;
    if(empty($month)){
        $sql="SELECT *,DATE_FORMAT(mainten_date,'%Y') as new_date FROM `{$tblLog}`";
    }else{
        //日期補零
        $month = str_pad($month,2,'0',STR_PAD_LEFT);
        $sql="SELECT *,DATE_FORMAT(mainten_date,'%Y-%m') as new_date FROM `{$tblLog}`";
    }
    $main=array();
	$result = mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        //die($new_date);
        if(empty($month)){
            if($new_date=="{$year}"){
                $main[]=$log_sn;
            }
        }else{
            if($new_date=="{$year}-{$month}"){
                $main[]=$log_sn;
            }
        }
    }
    return $main;
}
?>
<?php
//系統基本資料
session_start();
define("_WEB_ROOT_URL","http://{$_SERVER['SERVER_NAME']}/car/");
define("_WEB_ROOT_PATH","{$_SERVER['DOCUMENT_ROOT']}/car/");
//系統變數
$title="汽車保修系統";
$page_menu=array(
    "首頁"=>"index.php",
    "保修項目管理"=>"item_admin.php",
    "車藉資料管理"=>"car_admin.php",
    "保養記錄管理"=>"log_admin.php",
    "各式報表匯出"=>"report.php",
    "使用者管理"=>"user.php",
    "相關jQuery Plugin的使用"=>"jQuery Plugin.php"
    );
$userSta_arr=array('ok'=>'啟用','no'=>'禁用');
$tblUser="user";
$tblCar="car";
$tblItem="item";
$tblLog="log";
$tblContent="content";
//系統管理者
$admin_id="aandd0606";
$admin_password="123456";
$admin_mail="aandd0606@gmail.com";
$admin_phone="0911111111";
//資料庫連線
$db_id="car";//資料庫使用者//
$db_passwd="car123456";//資料庫使用者密碼//
$db_name="car";//資料庫名稱//
//動態產生導覽列
$top_nav=dy_nav($page_menu);
//連入資料庫
$link=@mysql_connect("localhost",$db_id,$db_passwd) or die_content("資料庫無法連線");
if(!mysql_select_db($db_name)) die_content("無法選擇資料庫".mysql_error());
//設定資料庫編碼
mysql_query("SET NAMES 'utf8'");
//自定輸出錯誤訊息
function die_content($content=""){
    $main="
        <!DOCTYPE html>
        <html lang='zh-TW'>
        <head>
        <meta charset='utf-8'>
        <title>輸出錯誤訊息</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='輸出錯誤訊息'>
        <meta name='author' content='aandd'>
		<!-- 引入css檔案開始 -->
        <link rel='stylesheet' href='css/bootstrap.css'>

		<!-- 引入css檔案結束 -->
		<!-- 引入js檔案開始 -->
		<script src='http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js'></script>
		<script src='http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
        <!-- 引入js檔案結束 -->
        </head>
        <body>
        <!--放入網頁主體-->
        <div class='container' id='main_content'>
          <!-- 主要內容欄位開始 -->
            <div class='hero-unit'>
			<div class='alert alert-warning alert-dismissible' role='alert'>
			<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
			<strong>警告!</strong>{$content}
			</div>
            </div>
          <!-- 主要內容欄位結束 -->
          <!-- 頁腳開始 -->
          <footer>
          </footer>
          <!-- 頁腳結束 -->
        </div> 
        <!-- 主要內容欄位結束 -->
        </body>
        </html>
    ";
    die($main);
}

//產生動態導覽列
function dy_nav($page_menu=array()){
    global $title;
    $main="<!--導覽列開始  navbar-fixed-bottom 固定在下方  navbar-fixed-top在上方-->
		<div class='navbar navbar-default navbar-fixed-top'>
		  <div class='container'>
			<div class='navbar-header'>
			  <a href='{$_SERVER['PHP_SELF']}' class='navbar-brand'>客戶管理首頁</a>
			  <button class='navbar-toggle' type='button' data-toggle='collapse' data-target='#navbar-main'>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
			  </button>
			</div>
			<div class='navbar-collapse collapse' id='navbar-main'>
			  <ul class='nav navbar-nav'>";
	$file_name=basename($_SERVER['REQUEST_URI']);
	foreach($page_menu as $i=>$v){
		$class=($file_name==$v)?"class='active'":"";
		$main.="<li {$class}><a href='{$v}'>{$i}</a></li>";
	}		  
			  
	$main.="</ul></div></div></div>";	
    return $main;
}

function bootstrap($content="",$js_link="",$css_link="",$js_fun=""){
    global $top_nav,$title;
    $main="
        <!DOCTYPE html>
        <html lang='zh-TW'>
        <head>
        <meta charset='utf-8'>
        <title>{$title}</title>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='{$title}'>
        <meta name='author' content='aandd'>
		<!-- 引入css檔案開始 -->
        <link rel='stylesheet' href='css/bootstrap.css'>
		<style type='text/css'>
          body {padding-top: 60px;padding-bottom: 20px;}
        </style>
		
		<!-- 引入js檔案開始 -->
		<script src='http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js'></script>
		<script src='http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
		<script src='js/jquery-ui-1.8.23.custom.min.js'></script>
        <!-- 引入js檔案結束 -->
        <!--引入額外的css檔案以及js檔案開始-->
        {$js_link}
        {$css_link}
        <!--引入額外的css檔案以及js檔案結束-->		
        <!--jquery語法開始-->
        {$js_fun}
        <!--jquery語法結束-->		
	
        </head>
        <body>
        <!--放入網頁主體-->
		<div class='container'>
          <!-- 主要內容欄位開始 -->           
		  {$top_nav}
		</div>
		  <div class='container'>
          {$content}
          <!-- 主要內容欄位結束 -->
          <!-- 頁腳開始 -->
          <footer>
          </footer>
          <!-- 頁腳結束 -->
		 
        </div> 
        <!-- 主要內容欄位結束 -->
        </body>
        </html>		
    ";
    return $main;
}
?>
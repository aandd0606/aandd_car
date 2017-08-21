<?php
include_once("setup.php");
include_once("function.php");
//-------------------設定區-----------------------//
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$car_sn=(empty($_REQUEST['car_sn']))?"":$_REQUEST['car_sn'];
//---------------流程控制區----------------------//
switch($op){
    //ajax呈現新增表單
    case "add_car_form":
    $content=car_admin_form($car_sn);
    break;

    //ajax刪除保修項目
    case "car_del":
    car_del($car_sn);
    break;

    
    //ajax即時修改車藉資料
    case "modify_car_data":
    $content=modify_car_data();
    break;
    //ajax即時驗證車牌是否重複
    case "ajax_car_id_check":
    $content=ajax_car_id_check();
    break;
    
    //新增車藉資料動作
    case "add_car":
    add_car();
    header("location:{$_SERVER['PHP_SELF']}");
    break;
   
    default:
    $content=bootstrap(index(car_list()),"","",jsfun());
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
function car_list(){
    global $link,$tblCar;
    $main="
    <table class='table table-condensed table-hover table-bordered table-striped'> <tr><th>車廠</th><th>車型</th><th>車牌</th><th>車主</th><th>電話</th><th>手機</th><th>mail</th><th>地址</th><th>功能</th></tr>
    ";
    $item_data_arr=get_list_data_arr_from_sn($link,$tblCar);
    foreach($item_data_arr as $i => $v){
        foreach($v as $i2=>$v2){
            $$i2=$v2;
        }
        $main.="<tr id='tr_{$car_sn}'>
        <td class='edit' id='car_com-{$car_sn}'>{$car_com}</td>
        <td class='edit' id='car_style-{$car_sn}'>{$car_style}</td>
        <td class='edit' id='car_id-{$car_sn}'>{$car_id}</td>
        <td class='edit' id='car_owner-{$car_sn}'>{$car_owner}</td>
        <td class='edit' id='car_tel-{$car_sn}'>{$car_tel}</td>
        <td class='edit' id='car_phone-{$car_sn}'>{$car_phone}</td>
        <td class='edit' id='car_email-{$car_sn}'>{$car_email}</td>
        <td class='edit' id='car_address-{$car_sn}'>{$car_address}</td>
        <td><a class='btn btn-danger del_car' id='del_{$car_sn}'>刪除</a></td>
        </tr>";
    }
    $main.="<tr><td colspan=9>
    <a id='add_car_form' class='btn btn-success'>新增車藉資料</a>
    </td></tr>";
    $main.="</table>
    <div id='car_form' class='well'></div>
    ";
    return $main;
}

function car_admin_form($car_sn=""){
    global $link,$tblCar;
    error_reporting(0);
    $op="
    <input type='hidden' name='op' value='add_car'>
    <input type='submit' value='新增車藉資料' id='submit'>
    ";
    $main="
    <link href='"._WEB_ROOT_URL."/js/Validation-Engine/css/validationEngine.jquery.css' rel='stylesheet'>
    <script src="._WEB_ROOT_URL."/js/jquery-option-tree/jquery.optionTree.js></script>
	<script src="._WEB_ROOT_URL."/js/Validation-Engine/js/jquery.validationEngine.js></script>
	<script src="._WEB_ROOT_URL."/js/Validation-Engine/js/languages/jquery.validationEngine-zh_TW.js></script>
    <script>
    $(function(){
        $('#submit').click(function(){
            $('#submit').fadeOut();
        });
        var config = { 
            choose: '請選擇...',
            loading_image:'img/loading.gif'
        };

        var option_tree = {
           'Ford 福特': {'Escape':'Ford 福特 - Escape','Focus':'Ford 福特 - Focus','Mondeo':'Ford 福特 - Mondeo','Fiesta':'Ford 福特 - Fiesta','i-Max':'Ford 福特 - i-Max','其他':'Ford 福特 - 車型'},
           'Honda 本田': {'Fit':'Honda 本田 - Fit','Accord':'Honda 本田 - Accord','Civic':'Honda 本田 - Civic','CR-V':'Honda 本田 - CR-V','CR-Z':'Honda 本田 - CR-Z','Legend':'Honda 本田 - Legend','其他':'Honda 本田 - 車型'},
            'Hyundai 現代':{'Elantra ':'Hyundai 現代 - Elantra ','ix35':'Hyundai 現代 - ix35','Sonata':'Hyundai 現代 - Sonata','Santa Fe':'Hyundai 現代 - Santa Fe','i30':'Hyundai 現代 - i30','Grand Starex':'Hyundai 現代 - Grand Starex','Matrix':'Hyundai 現代 - Matrix','Tucson':'Hyundai 現代 - Tucson','其他':'Hyundai 現代 - 車型'},
            'Lexus 凌志':{'CT':'Lexus 凌志 - CT','IS':'Lexus 凌志 - IS','ES':'Lexus 凌志 - ES','GS':'Lexus 凌志 - GS','RX':'Lexus 凌志 - RX','其他':'Lexus 凌志 - '},
            'Luxgen 納智捷':{'5 Sedan':'Luxgen 納智捷 - 5 Sedan','7 CEO':'Luxgen 納智捷 - 7 CEO','7 SUV':'Luxgen 納智捷 - 7 SUV','7 MPV':'Luxgen 納智捷 - 7 MPV','其他':'Luxgen 納智捷 - 車型'},
            'M-Benz 賓士':{'C-Class':'M-Benz 賓士 - C-Class','S-Class':'M-Benz 賓士 - S-Class',' M-Class':'M-Benz 賓士 - M-Class','R-Class':'M-Benz 賓士 - R-Class ','SL-Class':'M-Benz 賓士 - SL-Class','B-Class':'M-Benz 賓士 - B-Class','E-Class':'M-Benz 賓士 - E-Class','其他':'M-Benz 賓士 - '},
            'Mazda 馬自達':{'3':'Mazda 馬自達 - 3','5':'Mazda 馬自達 - 5','6':'Mazda 馬自達 - 6','CX-7':'Mazda 馬自達 - CX-7','CX-9':'Mazda 馬自達 - CX-9','其他':'Mazda 馬自達 - 車型'},
            'Mitsubishi 三菱':{'ASX':'Mitsubishi 三菱 - ASX','Fortis':'Mitsubishi 三菱 - Fortis','Zinger':'Mitsubishi 三菱 - Zinger','Colt':'Mitsubishi 三菱 - Colt','Outlander':'Mitsubishi 三菱 - Outlander','Savrin':'Mitsubishi 三菱 - Savrin','Grunder':'Mitsubishi 三菱 - Grunder','其他':'Mitsubishi 三菱 - 車型'},
            'Nissan 日產':{'Bluebird':'Nissan 日產 - Bluebird','Livina':'Nissan 日產 - Livina','March':'Nissan 日產 - March','Serena':'Nissan 日產 - Serena','Rogue':'Nissan 日產 - Rogue','Teana':'Nissan 日產 - Teana','Tiida':'Nissan 日產 - Tiida','Sentra':'Nissan 日產 - Sentra','Cefiro':'Nissan 日產 - Cefiro','其他':'Nissan 日產 - 車型'},
            'Toyota 豐田':{'Altis':'Toyota 豐田 - Altis','Camry':'Toyota 豐田 - Camry','Yaris':'Toyota 豐田 - Yaris','Vios':'Toyota 豐田 - Vios','RAV4':'Toyota 豐田 - RAV4','Previa':'Toyota 豐田 - Previa','Innova':'Toyota 豐田 - Innova','Wish':'Toyota 豐田 - Wish','其他':'Toyota 豐田 - 車型'},
            'Volkswagen 福斯':{'Amarok':'Volkswagen 福斯 - Amarok','Caddy':'Volkswagen 福斯 - Caddy','Caravelle':'Volkswagen 福斯 - Caravelle','Golf':'Volkswagen 福斯 - Golf','Passat':'Volkswagen 福斯 - Passat','Multivan':'Volkswagen 福斯 - Multivan','Phaeton':'Volkswagen 福斯 - Phaeton','Polo':'Volkswagen 福斯 - Polo','Tiguan':'Volkswagen 福斯 - Tiguan','Touran':'Volkswagen 福斯 - Touran','其他':'Volkswagen 福斯 - 車型'},
            'Volvo 富豪':{'C30':'Volvo 富豪 - C30','C70':'Volvo 富豪 - C70','S40':'Volvo 富豪 - S40','S60':'Volvo 富豪 - S60','S80':'Volvo 富豪 - S80','V50':'Volvo 富豪 - V50','V60':'Volvo 富豪 - V60','XC60':'Volvo 富豪 - XC60','XC70':'Volvo 富豪 - XC70','XC90':'Volvo 富豪 - XC90','其他':'Volvo 富豪 - 車型'},
            'Subaru 速霸陸':{'BRZ':'Subaru 速霸陸 - BRZ','Forester':'Subaru 速霸陸 - Forester','Impreza':'Subaru 速霸陸 - Impreza','Legacy':'Subaru 速霸陸 - Legacy','Outback':'Subaru 速霸陸 - Outback','XV':'Subaru 速霸陸 - XV','其他':'Subaru 速霸陸 - 車型'},
            'Audi 奧迪':{'A1':'Audi 奧迪 - A1','A3':'Audi 奧迪 - A3',' A4':'Audi 奧迪 -  A4',' A5':'Audi 奧迪 - A5','A6':'Audi 奧迪 - A6','A7':'Audi 奧迪 - A7','A8':'Audi 奧迪 - A8','Q3':'Audi 奧迪 - Q3','Q5':'Audi 奧迪 - Q5','Q7':'Audi 奧迪 - Q7','R8':'Audi 奧迪 - R8','TT':'Audi 奧迪 - TT','其他':'Audi 奧迪 - 車型'},
            'Chrysler 克萊斯勒':{'Town & Country':'Chrysler 克萊斯勒 - Town & Country','300C':'Chrysler 克萊斯勒 - 300C','PT Cruiser':'Chrysler 克萊斯勒 - PT Cruiser','其他':'Chrysler 克萊斯勒 - 車型'},            
            '其他車廠':{'其他':'輸入車廠 - 輸入車型'},

        };                                  
    
        $('input[name=car_fullname]').optionTree(option_tree,config);
        $('#aandd_car_form').validationEngine();

    });
    </script>
        <form method='post' action='{$_SERVER['PHP_SELF']}' id='aandd_car_form'>
        <label>請選擇車廠車型</label>
        <input type='text' name='car_fullname' / value='{$car_com} - {$car_style}' class='form-control'>
        <label>請輸入車牌號碼</label>
        <input type='text' placeholder='請輸入車牌...' name='car_id' value='{$car_id}' 
        class='validate[required,custom[onlyLetterNumber],maxSize[6]],ajax[ajaxUserCallPhp] form-control'>
        <label>請輸入車主姓名</label>
        <input type='text' placeholder='請輸入車主姓名...' name='car_owner' value='{$car_owner}'
        class='validate[required,minSize[2]] form-control'>
        <label>請輸入車主電話</label>
        <input type='text' placeholder='請輸入車主電話...' name='car_tel' value='{$car_tel}'
        class='validate[required,custom[phone]] form-control'>
        <label>請輸入車手機</label>
        <input type='text' placeholder='請輸入車主手機...' name='car_phone' value='{$car_phone}'
        class='validate[required,custom[phone]] form-control'>
        <label>請輸入車主電子郵件</label>
        <input type='text' placeholder='請輸入車主電子郵件...' name='car_email' value='{$car_email}' class='validate[required,custom[email]] form-control'>
        <label>請輸入車主地址</label>
        <input type='text' placeholder='請輸入車主地址...' name='car_address' value='{$car_address}' class='form-control'>
        {$op}
    </form>
    ";
    return $main;
}

function jsfun(){
    $main="
	<script src="._WEB_ROOT_URL."/js/jquery.jeditable.js></script>
    <script>
    $(function(){
        $('#add_car_form').click(function(){
            $.post('{$_SERVER['PHP_SELF']}',{op:'add_car_form'},function(msg){
                //$('#item_form').slideToggle('slow');
                $('#car_form').css('display','block');
                $('#car_form').html(msg);
            });
        });
        $('.edit').editable('{$_SERVER['PHP_SELF']}?op=modify_car_data',{
         indicator : '<img src=\"img/loading.gif\" />儲存中...',
         tooltip   : '按下編輯...',
         cancel    : '取消',
         submit    : '修改'
        });
        
        $('.del_car').click(function(){
            if(confirm('確認刪除動作！')){
                var del_car_sn = $(this).attr('id').split('_');
                $.post('{$_SERVER['PHP_SELF']}',{car_sn:del_car_sn[1],op:'car_del'},function(msg){
                    $('#tr_' + del_car_sn[1]).remove();    
                });
            }
        });
        $('#submit').click(function(){
            $('#submit').fadeOut();
        });
    });
    </script>
    ";
    return $main;
}

function add_car(){
    global $link,$tblCar;
    $car_arr=explode(" - ",$_POST['car_fullname']);
    $sql="insert into `{$tblCar}` (`car_com`, `car_style`, `car_owner`, `car_tel`, `car_phone`, `car_email`, `car_address`, `car_id`) values 
    ('{$car_arr[0]}','{$car_arr[1]}','{$_POST['car_owner']}','{$_POST['car_tel']}','{$_POST['car_phone']}','{$_POST['car_email']}','{$_POST['car_address']}','{$_POST['car_id']}')";
	mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
}

//ajax修改車藉資料
function modify_car_data(){
    global $link,$tblCar;
    $id_arr=explode("-",$_POST['id']);
    $sql="update `{$tblCar}` set `{$id_arr[0]}` = '{$_POST['value']}' where car_sn='{$id_arr[1]}'";
	mysql_query($sql,$link) or die("<span class='label label-important'>修改資料失敗請重新整理頁面</sapn>");
    return $_POST['value'];
}

function ajax_car_id_check(){
    global $link,$tblCar;
    //http://www.position-absolute.com/articles/using-form-ajax-validation-with-the-jquery-validation-engine-plugin/
    $arrayToJs=array();
    $sql="select * from `{$tblCar}` where car_id='{$_GET['fieldValue']}'";
    $result=mysql_query($sql,$link) or die_content("新增資料失敗".mysql_error());
    $data=mysql_fetch_assoc($result);
    if($data){
        $arrayToJs[1]=false;
    }else{
        $arrayToJs[1]=true;
    }
    $arrayToJs[0]=$_GET['fieldId'];
    return json_encode($arrayToJs);
}

function car_del($car_sn=""){
    global $link,$tblCar;
    $sql="delete from `{$tblCar}` where car_sn='{$car_sn}'";
    mysql_query($sql,$link) or die_content("刪除資料失敗".mysql_error());
    return "刪除資料成功";
}
?>
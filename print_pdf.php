<?php
/*-----------引入檔案區--------------*/
include_once("setup.php");
include_once("function.php");
require_once(_WEB_ROOT_PATH.'class/tcpdf/config/lang/eng.php');
require_once(_WEB_ROOT_PATH.'class/tcpdf/tcpdf.php');
$log_sn=empty($_REQUEST['log_sn'])?"":intval($_REQUEST['log_sn']);
$company_name="冠昌車業";
$company_desc="您的愛車為我首要顧念！";
$company_phone="07-7777777";
$company_tel="0911111111";
$company_owner="許立民";
$company_url="http://aandd.idv.tw";
//常規保養注意事項
$suggest_content="1.注意檢查輪胎系統與胎壓。<br>2.注意經常檢查剎車系統。<br>3.減輕負載。<br>4.輕加油，輕剎車，早剎車。<br>5.把握車輛的經濟時速。<br>6.減輕負載。<br>7.定期保養、更換零件。<br>";
/*-----------function區--------------*/
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($company_owner);
$pdf->SetTitle($company_name);
$pdf->SetSubject($company_desc);
$pdf->SetKeywords($company_name);
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 
    "{$company_name}（{$company_desc}）
    手機：{$company_phone} 電話：{$company_tel}",  
    "負責人：{$company_owner} 網址：{$company_url}");

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('msungstdlight', '', 14, '', true);

// add a page
$pdf->AddPage();
// Colors, line width and bold font
$pdf->SetFillColor(255, 0, 0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(128, 0, 0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('', 'B');

$pdf->Cell(180, 16,"車輛保修記錄資料", 1, 0, 'C', 1);

//取得車藉資料與保養記錄資料
    $sql="select * from `{$tblLog}` as log ,{$tblCar} as car where log.car_sn=car.car_sn AND log.log_sn={$log_sn}";
    //die($sql);
	$result = mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
    }
$pdf->Ln();
//取得車藉資料與保養記錄資料

$pdf->SetFillColor(224, 235, 255);
$pdf->SetTextColor(0);
$pdf->SetFont('');
$pdf->Cell(20, 10,"車牌號碼", 1, 0, 'C', 1);
$pdf->Cell(160, 10,"{$car_id}"  , 1, 0, 'C', 1);       
$pdf->Ln();
$pdf->Cell(20, 10,"車型", 1, 0, 'C', 1);
$pdf->Cell(70, 10,"{$car_com} - {$car_style }"  , 1, 0, 'C', 1);       
$pdf->Cell(20, 10,"里程數", 1, 0, 'C', 1);
$pdf->Cell(70, 10,"{$mainten_kilometer}公里"  , 1, 0, 'C', 1);       
$pdf->Ln();
$pdf->Cell(20, 8,"車主", 1    , 0, 'C', 1);
$pdf->Cell(40, 8,$car_owner, 1, 0, 'C', 1);
$pdf->Cell(20, 8,"手機", 1, 0, 'C', 1);
$pdf->Cell(40, 8,$car_phone , 1, 0, 'C', 1);
$pdf->Cell(20, 8,"電話", 1, 0, 'C', 1);
$pdf->Cell(40, 8,$car_tel , 1, 0, 'C', 1);
$pdf->Ln();
//取得保養人員姓名
$user_data=get_list_data_arr_from_sn($link,$tblUser,"id",$uid);
foreach($user_data as $i => $v){
    foreach($v as $i2=>$v2){
        $$i2=$v2;
    }
}
$pdf->Cell(30, 10,"保養人員"  , 1, 0, 'C', 1);
$pdf->Cell(60, 10, $name ,1, 0, 'C', 1);
$pdf->Cell(30, 10,"保養日期"  , 1, 0, 'C', 1);
$pdf->Cell(60, 10,$mainten_date, 1, 0, 'C', 1);
$pdf->Ln();

$pdf->Cell(40, 8,"地址", 1, 0, 'C', 1);
$pdf->Cell(140, 8,$car_address  , 1, 0, 'C', 1);
$pdf->Ln();
$pdf->Cell(40, 8,"Email", 1, 0, 'C', 1);
$pdf->Cell(140, 8,$car_email , 1, 0, 'C', 1);
$pdf->Ln();
$pdf->Cell(40, 20,"保養建議事項", 1, 0, 'C', 1);
$pdf->MultiCell(140, 20,$suggest  , 1, 0, 'C', 1);

$pdf->Cell(40, 50,"汽車使用提醒", 1, 0, 'C', 1);
$pdf->MultiCell(140, 50,$suggest_content  , 1, 0, 'C', 1,'','',true,0,true);


//保養內容標題
$pdf->Cell(20, 16,"項次", 1, 0, 'C', 1);
$pdf->Cell(100, 16,"內容"  , 1, 0, 'C', 1);
$pdf->Cell(60, 16,"價格"  , 1, 0, 'C', 1);
$pdf->Ln();

//取得保養內容資料
    $n=1;
    $total_price=0;
    $sql="select * from `{$tblContent}` where log_sn='{$log_sn}'";
    //die($sql);
	$result = mysql_query($sql,$link) or die_content("查詢資料失敗".mysql_error());
    while($data=mysql_fetch_assoc($result)){
        foreach($data as $i=>$v){
            $$i=$v;
        }
        $pdf->Cell(20, 10,$n, 1, 0, 'C', 1);
        $pdf->Cell(100, 10,$item  , 1, 0, 'C', 1);
        $pdf->Cell(60, 10,$item_price  , 1, 0, 'C', 1);
        $pdf->Ln();
        $n++;
        $total_price+=$item_price;
        
    }

$pdf->Cell(120, 10,"總計"  , 1, 0, 'C', 1);
$pdf->Cell(60, 10,$total_price  , 1, 0, 'C', 1);
$pdf->Ln();


// CUSTOM PADDING

// set color for background
$pdf->SetFillColor(255, 255, 215);

// set font
$pdf->SetFont('helvetica', '', 8);

// set cell padding
$pdf->setCellPaddings(2, 4, 6, 8);


// move pointer to last page
$pdf->lastPage();


//Close and output PDF document
$pdf->Output("report_{$log_sn}.pdf", 'I');
?>
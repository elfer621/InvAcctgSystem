<?php
/*ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);*/
error_reporting(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$con = $db->openDb();
// if(!$con){
	// $db->openDb_local();
// }
$readingNum = $db->getReadingnum($_SESSION['counter_num']);
switch($_REQUEST['execute']){
	case'prodlist':
		$prod = $db->resultArray("product_name,barcode,b.price",
			"tbl_product_name a left join (select sku_id,barcode,price from tbl_barcodes where divmul=1) b on a.sku_id=b.sku_id",
			"where category_id='{$_REQUEST['catid']}' order by product_name asc");
		foreach($prod as $key => $val){
			?>
			<button class="buthov" type="button" onclick="saveToSession('<?=$val['barcode']?>','touch_screen')" style="height:50px;width:100px;float:left;font-size:10px;">
				<span style="color:red;font-size:10px;position:absolute;margin:-9px 0 0 -7px;z-index:9999;"><?=number_format($val['price'],2)?></span>
				<?=strlen($val['product_name'])>40?substr($val['product_name'],0,40)."...":$val['product_name']?>
			</button>
			<?
		}
	break;
}
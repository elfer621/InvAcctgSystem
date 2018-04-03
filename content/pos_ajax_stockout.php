<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
// if(!$con){
	// $db->openDb_local();
// }
$readingNum = $db->getReadingnum($_SESSION['counter_num']);
switch($_REQUEST['execute']){
	case'costchange':
		$barcode = $_REQUEST['barcode'];
		$cost = $_REQUEST['newcost'];
		$total = ($cost * $_SESSION['stockout'][$barcode]['qty']);
		$_SESSION['count']+=1;
		$_SESSION['stockout'][$barcode]['count'] = $_SESSION['count'];
		$_SESSION['stockout'][$barcode]['cost'] = $cost;
		$_SESSION['stockout'][$barcode]['total'] = $total;
		$db->updateCost($barcode,number_format($cost,2));
		echo "success";
	break;
	case'qtychange':
		$barcode = $_REQUEST['barcode'];
		$qty = $_REQUEST['newqty'];
		$total = ($qty * $_SESSION['stockout'][$barcode]['price']);
		$_SESSION['count']+=1;
		$_SESSION['stockout'][$barcode]['count'] = $_SESSION['count'];
		$_SESSION['stockout'][$barcode]['qty'] = $qty;
		$_SESSION['stockout'][$barcode]['total'] = $total;
		echo "success";
	break;
	case"peritemdisc":
		$barcode = $_REQUEST['barcode'];
		$disc = (double)$_REQUEST['disc'];
		$_SESSION['stockout'][$barcode]['discount']=$disc;
		echo "success";
	break;
	case 'process_barcode_stockin':
		$barcode = $_REQUEST['barcode'];
		$prod_info = $db->getWHERE("sku_id,price,unit,divmul,cost","tbl_barcodes","where barcode='".$barcode."'");
		$invbal = $db->invBal($prod_info['sku_id'],date('Y-m-d'));
		if($prod_info){
			$prod_name = $db->getWHERE("product_name","tbl_product_name","where sku_id='".$prod_info['sku_id']."'");
			if($_REQUEST['type']=="uom"){
				$qty =1;
			}else{
				$qty = isset($_SESSION['stockout'][$barcode])?$_SESSION['stockout'][$barcode]['qty']+1:1;
			}
			$total = ($qty * $prod_info['price']);
			$_SESSION['count']+=1;
			$_SESSION['stockout'][$barcode]=array(
				"count"=>$_SESSION['count'],
				"bcode"=>$barcode,
				"prod_name"=>$prod_name['product_name'],
				"qty"=>$qty,
				"unit"=>$prod_info['unit'],
				"price"=>$prod_info['price'],
				"discount"=>"0",
				"total"=>$total,
				"sku"=>$prod_info['sku_id'],
				"divmul"=>$prod_info['divmul'],
				"cost"=>$prod_info['cost'],
				"id"=>$prod_info['id']
			);
			echo "success";
		}else{
			echo "Barcode not found...";
		}
	break;
	case'viewStockout':
		unset($_SESSION['stockout']);
		$ref = $_REQUEST['refid'];
		$info = $db->getWHERE("*","tbl_stockout_header","where id='".$ref."'");
		$sql = "select * from tbl_stockout_items where stockin_refid = $ref";
		$qry = mysql_query($sql);
		if($qry){
			while($row = mysql_fetch_assoc($qry)){
				$sku = $db->getWHERE("sku_id","tbl_barcodes","where barcode='".$row['barcode']."'");
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION['stockout'][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>$row['qty'],
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$sku['sku_id'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost']
				);
			}
			$_SESSION['stockin_header']=array('refid'=>$info['id'],'date'=>$info['date'],'supplier_id'=>$info['supplier_id'],'remarks'=>$info['remarks'],'total'=>$info['total']);
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'delStockout':
		$ref = $_REQUEST['refid'];
		$sql = "delete from tbl_stockout_header where id='".$ref."'";
		$qry = mysql_query($sql);
		if($qry){
			$delqry = mysql_query("delete from tbl_stockout_items where stockin_refid='".$refid."'");
			if($delqry){
				echo "success";
			}else{
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
	break;
}
?>
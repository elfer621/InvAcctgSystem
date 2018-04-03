<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
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
		$total = ($cost * $_SESSION['stockin'][$barcode]['qty']);
		$_SESSION['count']+=1;
		$_SESSION['stockin'][$barcode]['count'] = $_SESSION['count'];
		$_SESSION['stockin'][$barcode]['cost'] = $cost;
		$_SESSION['stockin'][$barcode]['total'] = $total;
		$db->updateCost($barcode,$cost);
		echo "success";
	break;
	case'qtychange':
		$barcode = $_REQUEST['barcode'];
		$qty = $_REQUEST['newqty'];
		$total = ($qty * $_SESSION['stockin'][$barcode]['price']);
		$_SESSION['count']+=1;
		$_SESSION['stockin'][$barcode]['count'] = $_SESSION['count'];
		$_SESSION['stockin'][$barcode]['qty'] = $qty;
		$_SESSION['stockin'][$barcode]['total'] = $total;
		echo "success";
	break;
	case"peritemdisc":
		$barcode = $_REQUEST['barcode'];
		$disc = (double)$_REQUEST['disc'];
		$_SESSION[$_REQUEST['page']][$barcode]['discount']=$disc;
		echo "success";
	break;
	case'addsupplier':
		$sql="insert into tbl_supplier set supplier_name='".$_REQUEST['val']."'";
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}
	break;
	case'stockinHeaderSave':
		foreach($_REQUEST as $key => $val){
			$_SESSION[$_REQUEST['sessiontype'].'_header'][$key] = $val;
		}
		//$_SESSION[$_REQUEST['sessiontype'].'_header']=array('date'=>$_POST['date'],'status'=>$_POST['status'],'supplier_id'=>$_POST['supplier'],'remarks'=>$_POST['remarks'],'refid'=>$_POST['refid']);
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
				$qty = isset($_SESSION['stockin'][$barcode])?$_SESSION['stockin'][$barcode]['qty']+1:1;
			}
			$total = ($qty * $prod_info['price']);
			$_SESSION['count']+=1;
			$_SESSION['stockin'][$barcode]=array(
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
				"cost"=>$prod_info['cost']
			);
			echo "success";
		}else{
			echo "Barcode not found...";
		}
	break;
	case'viewStockout':
		unset($_SESSION['stockin']);
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
					"cost"=>$row['cost'],
					"id"=>$row['id']
				);
			}
			$_SESSION['stockout_header']=array('refid'=>$info['id'],'date'=>$info['date'],'supplier_id'=>$info['supplier_id'],'remarks'=>$info['remarks'],'total'=>$info['total']);
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'transferPO':
		$db->openDb("main");
		unset($_SESSION["{$_REQUEST['page']}"]);
		$ref = $_REQUEST['refid'];
		//$info = $db->getWHERE("*","tbl_po_header","where id='".$ref."'");
		$iqry = mysql_query("select * from tbl_po_header where id='$ref' limit 1");
		$info = mysql_fetch_assoc($iqry);
		$sql = "select * from tbl_po_items where stockin_refid = $ref order by `count` asc";
		$qry = mysql_query($sql);
		if($qry){
			while($row = mysql_fetch_assoc($qry)){
				//$sku = $db->getWHERE("sku_id","tbl_barcodes","where barcode='".$row['barcode']."'");
				$sqry = mysql_query("select sku_id from tbl_barcodes where barcode='{$row['barcode']}'");
				$sku = mysql_fetch_assoc($sqry);
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION["{$_REQUEST['page']}"][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>$row['qty'],
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$sku['sku_id'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost'],
					"id"=>($_REQUEST['stocktransfer']?'':$row['id'])
				);
			}
			$_SESSION["{$_REQUEST['page']}_header"]=array('refid'=>$info['id'],'date'=>$info['date'],'status'=>$info['status'],'supplier_id'=>$info['supplier_id'],'remarks'=>$info['remarks'],'total'=>$info['total']);
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'viewStockin':
		unset($_SESSION["{$_REQUEST['page']}"]);
		//$lokasion = $_REQUEST['location']?$_REQUEST['location']:"tbl";
		$ref = $_REQUEST['refid'];
		if($_REQUEST['stocktransfer']){
			$db->openDb("main");
			$info = $db->getWHERE("*","tbl_stocktransfer_header","where id='".$ref."' and `to`='{$_SESSION['connect']}' and `from`='{$_REQUEST['xfrom']}'");
			$sql = "select * from tbl_stocktransfer_items where stockin_refid = $ref and `to`='{$_SESSION['connect']}' and `from`='{$_REQUEST['xfrom']}' order by `count` asc";
		}else{
			$info = $db->getWHERE("*","tbl_{$_REQUEST['page']}_header","where id='".$ref."'");
			$sql = "select * from tbl_{$_REQUEST['page']}_items where stockin_refid = $ref order by `count` asc";
		}
		$qry = mysql_query($sql);
		if($qry){
			while($row = mysql_fetch_assoc($qry)){
				//$sku = $db->getWHERE("sku_id,price","tbl_barcodes","where barcode='".$row['barcode']."'");
				$prod_name = $db->getWHERE("*","tbl_product_name","where sku_id='".$row['skuid']."'");
				$sku = $db->getWHERE("sku_id,price","tbl_barcodes","where sku_id='".$row['skuid']."'");
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION["{$_REQUEST['page']}"][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>$row['qty'],
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$sku['sku_id'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost'],
					"price"=>($row['selling']==0?$sku['price']:$row['selling']), //added new
					"id"=>($_REQUEST['stocktransfer']?'':$row['id'])
				);
				$_SESSION['persup'][$prod_name['supplier_id']]['cost']+=($row['qty'] * $row['cost']);
				$_SESSION['persup'][$prod_name['supplier_id']]['selling']+=($row['qty'] * ($row['selling']==0?$sku['price']:$row['selling']));
			}
			$_SESSION["{$_REQUEST['page']}_header"]=array(
				'refid'=>$info['id'],'date'=>$info['date'],'status'=>$info['status'],
				'supplier_id'=>$info['supplier_id'],'remarks'=>$info['remarks'],
				'glref'=>$info['glref'],
				'total'=>$info['total']
				);
			if($_REQUEST['page']=="po"){
				if($info['volume_discount']!=0){
					$_SESSION['poDISCOUNT1']=array('type'=>'volume_discount','amt'=>$info['volume_discount']);
				}
				if($info['additional_discount']!=0){
					$_SESSION['poDISCOUNT2']=array('type'=>'volume_discount','amt'=>$info['additional_discount']);
				}
			}
			if($_REQUEST['stocktransfer']){
				unset($_SESSION["{$_REQUEST['page']}_header"]['refid']);
				$_SESSION["{$_REQUEST['page']}_header"]['stid']=$ref;
				$_SESSION["{$_REQUEST['page']}_header"]['status']="Received from Branch";
				$_SESSION["{$_REQUEST['page']}_header"]['supplier_id']=$branchesid[$info['from']];
			}
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'delStockin':
		$ref = $_REQUEST['refid'];
		$sql = "update tbl_stockin_header set `status`='DELETED' where id='".$ref."'";
		$qry = mysql_query($sql);
		if($qry){
			$delqry = mysql_query("delete from tbl_stockin_items where stockin_refid='".$refid."'");
			if($delqry){
				echo "success";
			}else{
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
	break;
	case'delStockout':
		$ref = $_REQUEST['refid'];
		$sql = "update tbl_stockout_header set `status`='DELETED' where id='".$ref."'";
		$qry = mysql_query($sql);
		if($qry){
			$delqry = mysql_query("delete from tbl_stockout_items where stockin_refid='".$ref."'");
			if($delqry){
				$delonline = $con->DelTransferStock($_SESSION['connect'],$ref);
				if($delonline =="success"){
					echo "success";
				}else{
					echo $delonline;
				}
			}else{
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
	break;
	
}
?>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
$output = preg_split( "/ (and) /", $_REQUEST['where'] );
$flag=false;
$flag2=false;
foreach($output as $key => $val){
	if($db->strpos_arr($val,array("Campus")) == true){
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("begdate")) == true){
		$begdate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("enddate")) == true){
		$enddate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("product_name")) == true||$db->strpos_arr($val,array("supplier_id")) == true){
		if($flag2)$where2.=" and ";
		$where2.= "$val";
		$flag2=true;
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
$where = $where?"where $where":"where divmul <> 0";
$lessbegdate = "and `date` < '".trim($begdate[1])."'";
$lessenddate = "and `date` <= '".trim($enddate[1])."'";
$datecomb = "and (`date` between '".trim($begdate[1])."' and '".trim($enddate[1])."')";
$sql="select a.*,a.sku_id,b.product_name,b.supplier_name,b.supplier_id,c.price,c.cost,a.base_inv from 
				(select skuid as sku_id,sum(inv_sales) inv_sales,sum(inv_po_receipts) inv_po_receipts,sum(inv_transfer_receipts) inv_transfer_receipts,sum(inv_transfer) inv_transfer,sum(inv_adj) inv_adjustment,sum(bal_forwarded) inv_bal_forwarded,sum(total_inv) base_inv from 
				(
					select skuid,sum(qty*divmul) inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from (select skuid,qty,divmul,date_format(`timestamp`,'%Y-%m-%d') date from tbl_sales_items) a $where $datecomb group by skuid
					union
					select skuid,null inv_sales,sum(qty*divmul) inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from (select skuid,qty,divmul,date from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Received from Supplier') x $where $datecomb group by skuid 
					union
					select skuid,null inv_sales,null inv_po_receipts,sum(qty*divmul) inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,null total_inv from (select skuid,qty,divmul,date from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Received from Branch') x $where $datecomb group by skuid
					union
					select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,sum(qty*divmul) inv_transfer,null inv_adj,null bal_forwarded,null total_inv from (select skuid,qty,divmul,date from tbl_stockout_items a left join tbl_stockout_header b on a.stockin_refid=b.id where status='Transfer Stock') x $where $datecomb group by skuid
					union
					select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,sum(qty*divmul) inv_adj,null bal_forwarded,null total_inv from (select skuid,qty,divmul,date from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id where status='Adjustment') x $where $datecomb group by skuid
					union
					(select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) bal_forwarded,null total_inv from 
						(
						select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from (select skuid,qty,divmul,date_format(`timestamp`,'%Y-%m-%d') date from tbl_sales_items) a $where $lessbegdate group by skuid
						union
						select skuid,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total from (select skuid,qty,divmul,b.date from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id) a $where $lessbegdate group by skuid
						union
						select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from (select skuid,qty,divmul,b.date from tbl_stockout_items a left join tbl_stockout_header b on a.stockin_refid=b.id) a $where $lessbegdate group by skuid
						) 
					tbl group by skuid)
					union
					(select skuid,null inv_sales,null inv_po_receipts,null inv_transfer_receipts,null inv_transfer,null inv_adj,null bal_forwarded,COALESCE( SUM( in_total ) , 0 ) - COALESCE( SUM( out_total ) , 0 ) AS total_inv from 
						(
						select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from (select skuid,qty,divmul,date_format(`timestamp`,'%Y-%m-%d') date from tbl_sales_items) a $where $lessenddate group by skuid
						union
						select skuid,COALESCE( SUM( qty * divmul ) , 0 ) AS in_total,CONCAT(0) AS out_total from (select skuid,qty,divmul,b.date from tbl_stockin_items a left join tbl_stockin_header b on a.stockin_refid=b.id) a $where $lessenddate group by skuid
						union
						select skuid,CONCAT(0) AS in_total,COALESCE( SUM( qty * divmul ) , 0 ) AS out_total from (select skuid,qty,divmul,b.date from tbl_stockout_items a left join tbl_stockout_header b on a.stockin_refid=b.id) a $where $lessenddate group by skuid
						) 
					tbl group by skuid)
				) as tbl group by skuid) a,
				(select sku_id,product_name,supplier_name,a.supplier_id from tbl_product_name a left join tbl_supplier b on a.supplier_id=b.id) b,
				tbl_barcodes c 
				where a.sku_id=b.sku_id and a.sku_id=c.sku_id ".($where2?"and ".$where2:"");

// echo $sql;
// echo "<hr/>";

$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){ //$db->strpos_arr($val,array("Campus")) !== false
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
		
	}else{
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			}
		}else{
			$arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
		}
	}
}
//$arrs[]=$con->pdoStyle($con->con_main['ip'],$con->con_main['db'],$sql);
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}
$newlist = array();
foreach($list as $key=>$val){
	$newlist[$val['sku_id']]['sku_id']=$val['sku_id'];
	$newlist[$val['sku_id']]['supplier_name']=$val['supplier_name'];
	$newlist[$val['sku_id']]['product_name']=$val['product_name'];
	$newlist[$val['sku_id']]['price']=$val['price'];
	$newlist[$val['sku_id']]['cost']=$val['cost'];
	$newlist[$val['sku_id']]['inv_bal_forwarded']+=(double)$val['inv_bal_forwarded'];
	$newlist[$val['sku_id']]['inv_po_receipts']+=(double)$val['inv_po_receipts'];
	$newlist[$val['sku_id']]['inv_direct_purchases']+=(double)$val['inv_direct_purchases'];
	$newlist[$val['sku_id']]['inv_transfer_receipts']+=(double)$val['inv_transfer_receipts'];
	$newlist[$val['sku_id']]['inv_sales_returns']+=(double)$val['inv_sales_returns'];
	$newlist[$val['sku_id']]['inv_purchase_returns']+=(double)$val['inv_purchase_returns'];
	$newlist[$val['sku_id']]['inv_issuances']+=(double)$val['inv_issuances'];
	$newlist[$val['sku_id']]['inv_sales']+=(double)$val['inv_sales'];
	$newlist[$val['sku_id']]['inv_adjustment']+=(double)$val['inv_adjustment'];
	$newlist[$val['sku_id']]['inv_transfer']+=(double)$val['inv_transfer'];
	$newlist[$val['sku_id']]['base_inv']+=(double)$val['base_inv'];
}
//print_r($newlist);
?>
<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<script src="../js/myjs.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
	<style type="text/css">
		table.tbl,table.tbl2 {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
			font-size:10px;
			padding:5px;
		}
		table.tbl td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
			padding:5px;
		}
		table.tbl2 td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
		}
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body class="print" style="margin:0 auto 0;width:1200px;font-size:12px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<h2><?=$db->stockin_header;?><br/>Inventory Audit<br/></h2>
	<div style="clear:both;height:10px;"></div>
	<?php
		foreach($output as $key => $val){
			echo $val."<br/>";
		}
	?>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th rowspan="2">SKU</th>
				<th rowspan="2">Product Name</th>
				<th rowspan="2">Price</th>
				<th rowspan="2">Cost</th>
				<th rowspan="2">Bal Frwd</th>
				<th colspan="4">IN</th>
				<th colspan="5">OUT</th>
				
				<th rowspan="2">Ending Bal</th>
			</tr>
			<tr>
				<th>PO Receipt</th>
				<th>Direct Purchases</th>
				<th>Transfer In</th>
				<th>Sales Returns</th>
				
				<th>Pull Out</th>
				<th>Issuance</th>
				<th>Sales</th>
				<th>Adjustment</th>
				<th>Transfer Out</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$inv_bal_forwarded=0;$inv_po_receipts=0;$inv_direct_purchases=0;$inv_transfer_receipts=0;$inv_sales_returns=0;$inv_purchase_returns=0;
			$inv_issuances=0;$inv_sales=0;$inv_adjustment=0;$inv_transfer=0;$base_inv=0;
			$current_cat=null;
			foreach($newlist as $key => $row){
				if($row['supplier_name']!=$current_cat){
					echo "<tr><td colspan='15' style='font-weight:bold;'>".$row['supplier_name']."</td></tr>";
					$current_cat=$row['supplier_name'];
				}
		?>		
				<tr>
					<td><?=$row['sku_id']?></td>
					<td><?=$row['product_name']?></td>
					<td style="text-align:right;"><?=number_format($row['price'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['cost'],2)?></td>
					<td style="text-align:right;"><?=$row['inv_bal_forwarded']?></td>
					<td style="text-align:right;"><?=$row['inv_po_receipts']?></td>
					<td style="text-align:right;"><?=$row['inv_direct_purchases']?></td>
					<td style="text-align:right;"><?=$row['inv_transfer_receipts']?></td>
					<td style="text-align:right;"><?=$row['inv_sales_returns']?></td>
					<td style="text-align:right;"><?=$row['inv_purchase_returns']?></td>
					<td style="text-align:right;"><?=$row['inv_issuances']?></td>
					<td style="text-align:right;"><?=$row['inv_sales']?></td>
					<td style="text-align:right;"><?=$row['inv_adjustment']?></td>
					<td style="text-align:right;"><?=$row['inv_transfer']?></td>
					<td style="text-align:right;"><?=$row['base_inv']?></td>
				</tr>
			<?php 
				$inv_bal_forwarded+=$row['inv_bal_forwarded'];
				$inv_po_receipts+=$row['inv_po_receipts'];
				$inv_direct_purchases+=$row['inv_direct_purchases'];
				$inv_transfer_receipts+=$row['inv_transfer_receipts'];
				$inv_sales_returns+=$row['inv_sales_returns'];
				$inv_purchase_returns+=$row['inv_purchase_returns'];
				$inv_issuances+=$row['inv_issuances'];
				$inv_sales+=$row['inv_sales'];
				$inv_adjustment+=$row['inv_adjustment'];
				$inv_transfer+=$row['inv_transfer'];
				$base_inv+=$row['base_inv'];
			}	?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">Sub Total</td>
				<td style="text-align:right;"><?=$inv_bal_forwarded?></td>
				<td style="text-align:right;"><?=$inv_po_receipts?></td>
				<td style="text-align:right;"><?=$inv_direct_purchases?></td>
				<td style="text-align:right;"><?=$inv_transfer_receipts?></td>
				<td style="text-align:right;"><?=$inv_sales_returns?></td>
				<td style="text-align:right;"><?=$inv_purchase_returns?></td>
				<td style="text-align:right;"><?=$inv_issuances?></td>
				<td style="text-align:right;"><?=$inv_sales?></td>
				<td style="text-align:right;"><?=$inv_adjustment?></td>
				<td style="text-align:right;"><?=$inv_transfer?></td>
				<td style="text-align:right;"><?=$base_inv?></td>
			</tr>
		</tfoot>
	</table>
</body>

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
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
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl td {
		border-width: 1px;
		border-style: none;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
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
	body,td {
			font-size:12px !important;
		}
</style>

<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();

$sql = "select a.ar_refid,sum(dr) dr,sum(cr) cr,sum(dr-cr) ar_bal,b.customer_name from tbl_journal_entry a left join tbl_customers b on a.ar_refid=b.cust_id where ar_refid!=0 group by ar_refid";
$arrs=array();
if($_SESSION['connect'] or $_SESSION['settings']['connection_type']!="multiple"){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	}else{
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			}
		}
	}
}
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}
// echo "<pre>";
// print_r($list);
// echo "</pre>";
?>
<div class="print" style="width:900px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="7" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Account Receivable</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="7">
					<?php
						foreach($output as $key => $val){
							echo $val."<br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<th>Customer Name</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$total=array('dr'=>0,'cr'=>0,'ar_bal'=>0);
			foreach($list as $key => $row){ 
				
			?>
				<tr>
					<td><?=$row['customer_name']?></td>
					<td style="text-align:right;"><?=number_format($row['dr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['cr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['ar_bal'],2)?></td>
				</tr>
			<?php 
				$total['dr']+=$row['dr'];
				$total['cr']+=$row['cr'];
				$total['ar_bal']+=$row['ar_bal'];
			} 
			?>
		</tbody>
		<tfoot>
			<tr>
				<td><b>Sub Total</b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($total['dr'],2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($total['cr'],2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($total['ar_bal'],2)?></b></td>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>

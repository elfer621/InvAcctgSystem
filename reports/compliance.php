<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../js/myjs.js"></script>
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
		}
		table.tbl td {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			background-color: white;
			height:20px;
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
</head>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
$package = $db->getWHERE("*","tbl_packages","where id='{$_SESSION['defaultPackage']}'");
// echo "<pre>";
// print_r(unserialize($package['packages']));
// echo "</pre>";
// exit;

// $sql="select a.*,b.*,c.labs from tbl_sales_receipt_1 a 
// left join data_teletech_patient b on a.studentid=b.employee_no 
// left join (select receipt,group_concat(skuid) labs from lab_procedure_status group by receipt) c on a.receipt_id=c.receipt 
// where tblsource='data_teletech_patient'";

$sql="select b.*,a.*,c.labs from (select * from data_teletech_patient where year=year(now()) and data_reference='{$_REQUEST['data_reference']}' and company_name='{$_REQUEST['company_name']}') b 
left join tbl_sales_receipt_1 a on a.studentid=b.id 
left join (select receipt,group_concat(skuid) labs from lab_procedure_status where status='Done' group by receipt) c on b.receipt=c.receipt 
order by tracking_num asc
";
$res = $con->resultArray($con->Nection()->query($sql));
?>
<style>
td{
	padding:5px;
}
</style>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<h2><?=$db->stockin_header;?><br/>Compliance Report</h2>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Tracking Num</th>
				<th>Date Registered</th>
				<th>Full Name</th>
				<th>Age</th>
				<th>Gender</th>
				<?php
				foreach(unserialize($package['packages']) as $key => $val){
					echo "<th>{$val['prod_name']}</th>";
				}
				?>
				<th>Remarks</th>
			</tr>
		</thead>
		<tbody>
		<?
		foreach($res as $key => $row){ 
				$checkcount=0;
				$labs = explode(",",$row['labs']);
			?>
				<tr>
					<td><?=$row['tracking_num']?></td>
					<td><?=$row['date_registered']?></td>
					<td><?= $row['first_name']." ".$row['last_name']?></td>
					<td style="text-align:center;"><?= $row['age'] ?></td>
					<td style="text-align:center;"><?php echo $row['gender']; ?></td>
					<?php
					foreach(unserialize($package['packages']) as $k => $val){
						if($val['sku']=="32841425"){
							if($row['pe_data']!=null){
								echo "<td style='text-align:center;'>&#x2714;</td>";
								$checkcount+=1;
							}else{
								echo "<td></td>";
							}
						}else{
							echo in_array($val['sku'],$labs)?"<td style='text-align:center;'>&#x2714;</td>":"<td></td>";
							$checkcount+=1;
						}
					}?>
					<td><?=($checkcount==count($labs)?"Complete":"Incomplete")?></td>
				</tr>
			<? } ?>
		</tbody>
		
	</table>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#my_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});
function viewTrans(sku_id,prod_name){
	$('#dialog').dialog({
		autoOpen: false,
		width: 800,
		height: 400,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:prod_name
	});
	htmlobj=$.ajax({url:'../content/pos_ajax.php?execute=prodtrans&sku_id='+sku_id,async:false});
	$('#dialog').html('<div style="overflow:auto;max-height:360px;">'+htmlobj.responseText+'</div>');
	$('#dialog').dialog('open');
}

</script>
</html>
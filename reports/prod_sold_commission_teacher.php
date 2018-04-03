<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf8" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
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

$begdate = $_REQUEST['begdate']?$_REQUEST['begdate']:date('Y-m-01');
$enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d');
$instructor_code = "CAST(a.instructor_code AS UNSIGNED)='{$_REQUEST['teacher']}'";

$instructor = "(select distinct cast(InstructorNo as UNSIGNED) InstructorNo,InsLastName,InsFirstName from data_college_{$_SESSION['connect']} 
union 
select distinct cast(InstructorNo as UNSIGNED) InstructorNo,InsLastName,InsFirstName from data_elem_{$_SESSION['connect']} 
union 
select distinct cast(InstructorNo as UNSIGNED) InstructorNo,InsLastName,InsFirstName from data_highsch_{$_SESSION['connect']}) Ins";

$sql="select a.*,Ins.* from 
	(select distinct CAST(instructor_code AS UNSIGNED) instructor_code,count(*) num,sum(qty*selling) total_sales,sum(qty*cost) total_cost,group_concat(receipt) receipts from tbl_sales_items WHERE (date_format(timestamp,'%Y-%m-%d') between '$begdate' and '$enddate') group by CAST(instructor_code AS UNSIGNED)) a 
	left join $instructor on a.instructor_code=Ins.InstructorNo 
	".($_REQUEST['teacher']?"where ".$instructor_code:"")."
	order by InsLastName asc";

$res = $con->resultArray($con->Nection()->query($sql));



// echo "<pre>";
// print_r($items);
// echo "</pre>";

$teacher = $con->resultArray($con->Nection()->query("select * from $instructor order by InsLastName asc"));
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:10px;"></div>
	<h2><?=$db->stockin_header;?><br/>Teacher Commission Summary (<?=($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING")?>)</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;width:150px;" type="text" id="begdate" name="begdate" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;width:150px;" type="text" id="enddate" name="enddate" value="<?=$enddate?>"/>
		<div style="float:left;margin:0 5px;width:80px;">Teacher</div>
		<select name="teacher" style="float:left;">
			<option value=""></option>
			<?php foreach($teacher as $key => $val){
				echo "<option value='".$val['InstructorNo']."'>".$val['InsLastName'].", ".$val['InsFirstName']."</option>";
			} ?>
		</select>
		<input type="submit" value="Search" name="search_date" style="float:left;margin:0 5px;"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Code</th>
				<th>Teacher Name</th>
				<th>Count</th>
				<th>Total Cost</th>
				<th>5% Commission</th>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($res as $key => $row){ 
				
			?>
				<tr>
					<td><?= $row['instructor_code']?></td>
					<td><?= $row['InsFirstName']." ".$row['InsLastName']?></td>
					<td style="text-align:center;" title="<?= $row['receipts']?>"><?= $row['num']?></td>
					<td style="text-align:right;"><?= number_format($row['total_cost'],2) ?></td>
					<td style="text-align:right;"><?= number_format($row['total_cost']*.05,2) ?></td>
				</tr>
			<? 
				$total['total_sales']+=$row['total_sales'];
				$total['total_cost']+=$row['total_cost'];
				$total['total_profit']+=($row['total_sales']-$row['total_cost']);
				$total['total_comm']+=($row['total_cost']*.05);
			} ?>
		</tbody>
		<tfoot>
			<td colspan="3">TOTAL</td>
				<td style="text-align:right;"><?= number_format($total['total_cost'],2) ?></td>
				<td style="text-align:right;"><?= number_format($total['total_cost']*.05,2) ?></td>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<?php
	if($_REQUEST['teacher']){
		$student = "(select distinct cast(IDNo as UNSIGNED) IDNo,LastName,FirstName from data_college_{$_SESSION['connect']} 
		union 
		select distinct cast(IDNo as UNSIGNED) IDNo,LastName,FirstName from data_elem_{$_SESSION['connect']} 
		union 
		select distinct cast(IDNo as UNSIGNED) IDNo,LastName,FirstName from data_highsch_{$_SESSION['connect']}) b";
		$sqlitems = "select a.*,b.* from tbl_sales_items a
			left join $student on a.studentid=b.IDNo 
		where (date_format(a.timestamp,'%Y-%m-%d') between '$begdate' and '$enddate') ".($_REQUEST['teacher']?"and ".$instructor_code:"")." order by b.LastName asc";
		$items = $con->resultArray($con->Nection()->query($sqlitems));
		//print_r($items);
	?>
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Receipt</th>
				<th>Counter</th>
				<th>Date Time</th>
				<th>Student Name</th>
				<th>Item</th>
				<th>Qty</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<? 	foreach($items as $key => $row){ 
				
			?>
				<tr>
					<td><?= $row['receipt']?></td>
					<td><?= $row['counter']?></td>
					<td><?= $row['timestamp']?></td>
					<td><?= $row['LastName'].", ".$row['FirstName']?></td>
					<td style="text-align:left;"><?= $row['item_desc']?></td>
					<td style="text-align:right;"><?= $row['qty'] ?></td>
					<td style="text-align:right;"><?= number_format($row['qty']*$row['cost'],2) ?></td>
				</tr>
			<? 
			} ?>
		</tbody>
	</table>
	<?php } ?>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</body>
<script>
$(document).ready(function() {
	$('#begdate').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$('#enddate').datepicker({
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
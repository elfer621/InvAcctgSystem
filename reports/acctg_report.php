<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
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
			border-style: none;
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
		body,td {
			font-size:12px !important;
		}
	</style>
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
require_once"../content/fsquery.php";

$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:12px;">
	<h2><?=$report_name?></h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;margin-right:50px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
		<input type="submit" value="Search" name="search_date"/>
	</form>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<th>Acct Group</th>
			<th>Account</th>
			<th>Debit</th>
			<th>Credit</th>
			<th>Amount</th>
		</tr>
		<?php 
		$current_cat=null;
		$dr=0;$cr=0;$total=0;
		$grand_dr=0;$grand_cr=0;$grand_total=0;
		$number = mysql_num_rows(mysql_query($sql));
		$i = 0;
		while($row=mysql_fetch_assoc($qry)){ 
			if($row['account_group']!=$current_cat){
				if($total!=0){ //for every grouping records
					echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
				}
				echo "<tr><td colspan='5'><b>".$row['account_group']."</b></td></tr>";
				$current_cat=$row['account_group'];
				$dr=0;$cr=0;$total=0;
			}
		?>
			<tr>
				<td><?=$row['account_group']?></td>
				<td><?=$row['account_desc']?></td>
				<td style="text-align:right;"><?=number_format($row['total_dr'],2)?></td>
				<td style="text-align:right;"><?=number_format($row['total_cr'],2)?></td>
				<td style="text-align:right;"><?=number_format($row['sub_total'],2)?></td>
			</tr>
		<?php 
			$dr+=$row['total_dr'];$cr+=$row['total_cr'];$total+=$row['sub_total'];
			$i ++;
			if($number==$i){ //for last grouping records
				echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
						<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
			}
			$grand_dr+=$row['total_dr'];$grand_cr+=$row['total_cr'];$grand_total+=$row['sub_total'];
		} 
		?>
		<tfoot>
			<tr>
				<td colspan='2'><b>Grand Total</b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_dr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_cr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_total,2)?></b></td>
			</tr>
		</tfoot>
	</table>
	<div id="dialog"></div>
	<div style="clear:both;height:20px;"></div>
</body>
<script>
$(document).ready(function() {
	$('#beg_date').datepicker({
		inline: true,
		changeMonth: true,
		changeYear: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});

</script>
</html>
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
	</style>
</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = $_REQUEST['sql'];
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<table class="navigateable" id="mytbl" style="width:100%;">
		<tbody>
			<?php 	
			$dr=0;$cr=0;
			while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><a href="javascript:viewRefid(<?= $row['refid']?>,'<?=$row['type']?>')" title="View Details"><?= $row['refid']?></a></td>
				<td><?= $row['date']?></td>
				<td><?= $row['account_code']?></td>
				<td><?= $row['account_desc']?></td>
				<td><?= $row['dr']==0?"":number_format($row['dr'],2)?></td>
				<td><?= $row['cr']==0?"":number_format($row['cr'],2) ?></td>
			</tr>
			<?php $dr+=$row['dr'];$cr+=$row['cr'];} ?>
		</tbody>
		<thead>
			<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
				<th style="border:none;" >Ref#</th>
				<th style="border:none;" >Date</th>
				<th style="border:none;" >Account Code</th>
				<th style="border:none;width:300px;">Account Desc</th>
				<th style="border:none;">Debit</th>
				<th style="border:none;">Credit</th>
			</tr>
		</thead>
		<tfoot>
			<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
				<th style="border:none;" colspan="4" >Total</th>
				<th style="border:none;"><?= number_format($dr,2)?></th>
				<th style="border:none;"><?= number_format($cr,2)?></th>
			</tr>
		</tfoot>
	</table>
</body>

</html>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();

$info = $db->getWHERE("*","tbl_packages","where id='".$_REQUEST['id']."'");
$packages=unserialize($info['packages']);
?>
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
		height:15px;
		font-size:10px;
		padding:3px;
		
	}
	table.tbl2 td {
		border-width: 0px;
		border-style: none;
		font-size:10px;
		
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
</style>
<?php if($_REQUEST['id']){?>
<table class="tbl2" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td rowspan="4" style="width:25%;"><img src="../images/ebookshop.png"/></td>
		<td style="width:35%;">Name of Student:</td>
		<td style="width:5%;">&nbsp;</td>
		<td style="width:15%;">Date:</td>
		<td style="border-bottom:1px solid gray;width:25%;">&nbsp;</td>
	</tr>
	<tr>
		<td style="border-bottom:1px solid gray;"></td>
		<td>&nbsp;</td>
		<td>Campus:</td>
		<td style="border-bottom:1px solid gray;"><?=$_SESSION['connect']?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>Class:</td>
		<td style="border-bottom:1px solid gray;">&nbsp;</td>
	</tr>
	<tr>
		<td>OrderSlip #_____</td>
		<td>&nbsp;</td>
		<td>SY:</td>
		<td style="border-bottom:1px solid gray;">&nbsp;</td>
	</tr>
</table>
<div style="clear:both;height:10px;"></div>
<table class="tbl" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td colspan="5" style="text-align:center;"><?=$info['package_name']?></td></tr>
	<tr>
		<td>SUBJECT</td>
		<td>BOOK TITLE</td>
		<td>COST</td>
		<td>QTY</td>
		<td>TOTAL AMT</td>
	</tr>
	
	<?php foreach($db->subval_sort($packages,'count',arsort) as $val){ ?>
		<tr>
			<td></td>
			<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
			<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
			<td style="text-align:right;"></td>
			<td  style="text-align:right;"></td>
		</tr>
	<?php $xtotal+=$val['price'];} ?>
	<tr>
		<td colspan="2">Total</td>
		<td><?=number_format($xtotal,2)?></td>
		<td></td>
		<td></td>
	</tr>
</table>
<?php } ?>
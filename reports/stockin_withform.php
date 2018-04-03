<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<style type="text/css">
		table.tbl {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		
	}
	table.tbl th {
		border-width: 1px;
		border-style: none;
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
$sql = "select a.*,b.supplier_name from tbl_stockin_header as a left join tbl_supplier as b on a.supplier_id=b.id
	where a.id='".$_REQUEST['refid']."'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
$sql_item = mysql_query("select * from tbl_stockin_items where stockin_refid='".$_REQUEST['refid']."' order by count desc");
?>
<body style="font-size:15px;">
<div style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1100px;">
	<div style="clear:both;height:80px;"></div>
	<div style="float:left;margin-left:140px;"><?php echo $info['supplier_name'] ?></div>
	<div style="float:right;"><?php echo $info['date'] ?></div>
	<div style="clear:both;"></div>
	<div class="lbl">Remarks:</div>
	<div style="float:left;"><?php echo $info['remarks']." | ".$info['status'] ?></div>
	<div style="clear:both;height:50px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<?php
		while($row_items = mysql_fetch_assoc($sql_item)){
			echo "<tr>";
			echo "<td style='text-align:left;'>{$row_items['qty']} {$row_items['unit']}</td>";
			echo "<td>".$row_items['item_desc']."</td>";
			echo "<td style='text-align:right;'>".number_format($row_items['cost'],2)."</td>";
			echo "<td style='text-align:right;'>".number_format($row_items['total'],2)."</td>";
			echo "</tr>";
		}
		echo "<tr>";
		echo "<th colspan='3'>Total:</th>";
		echo "<th>".number_format($info['total'],2)."</th>";
		echo "</tr>";
		?>
	</table>
</div>
<?php
$db->closeDb();
?>
<script>
onload=function(){
	window.print();
	//self.close();
}
</script>
</body>
</html>
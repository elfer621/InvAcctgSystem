<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
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
$sql="select a.*,b.customer_name,b.customer_address from tbl_customers_trans a 
left join tbl_customers b on a.cust_id=b.cust_id where a.id='{$_REQUEST['refid']}'";
$qry = mysql_query($sql);
$info = mysql_fetch_assoc($qry);
?>
<body style="font-size:13px;">
<div style="font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;width:1200px;padding-left:5px;">
	<div style="clear:both;height:60px;"></div>
	<div style="float:right;margin-right:80px;"><?php echo date("Y-m-d",strtotime($info['date']))?></div>
	<div style="float:left;margin-left:100px;font-size:15px;"><?php echo $info['customer_name'] ?></div>
	<div style="clear:both;"></div>
	<div style="float:left;margin-left:100px;font-size:15px;"><?php echo $info['customer_address'] ?></div>
	<div style="clear:both;height:150px;"></div>
	<div style="min-height:100px;padding-left:10px;">
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td style="text-align:center;width:500px;"><?=$info['details']?></td>
				<td style="width:100px;text-align:right;"><?=number_format($info['amount'],2)?></td>
			</tr>
		</table>
	</div>
	<div style="clear:both;height:10px;"></div>
</div>
<?php
$db->closeDb();
?>
<script>
onload=function(){
	window.print();
	self.close();
}
</script>
</body>
</html>
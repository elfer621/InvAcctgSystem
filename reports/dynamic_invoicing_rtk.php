<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<style type="text/css">
		table.tbl,table.tbl2, p {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			font-size:14px;
		}
		table.tbl th,table.tbl2 th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			text-align:center;
			font-size:14px;
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			font-size:14px;
		}
		table.tbl2 td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			text-align:center;
			font-size:14px;
		}
		p {
			padding:0px;
			margin:0;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
			font-size:14px;
		}
		.logo {
			background-image: url('../images/RBERlogo.jpg');
			background-size: 200px 110px;
			background-repeat: no-repeat;
			
		}
		@page {
			size: A4;
			margin: 50px 25px 0 25px;
			font-size:14px;
		}
		@media print {
		  * {-webkit-print-color-adjust:exact;}
		  body {
			margin: 50px 25px 0 25px;
		  }
		  .footer {
			  position:absolute;bottom:150px;
		  }
		}
	</style>
</head>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
$header = $db->getWHERE("a.*,b.*,c.agent_name",
	"tbl_{$_REQUEST['tbltype']}_header a 
	left join tbl_customers b on a.cust_id=b.cust_id 
	left join req_agent c on a.rep=c.id ",
	"where a.id='".$_REQUEST['refid']."'","main");
$items = $db->resultArray("*","tbl_{$_REQUEST['tbltype']}_items","where refid='".$_REQUEST['refid']."'order by id desc");
?>

<body class="page" style="margin:0 auto;width:1100px;font-size:12px;height:100%;position:relative;">
		<div style="clear:both;height:120px;"></div>
		<div style="width:100%;display:table;">
			<div style="float:left;width:80%;font-size:15px;display:table-cell;vertical-align:top;box-sizing:border-box;padding-left:110px;">
				<b><?=$header['customer_name']?></b>
				<div style="clear:both;height:5px;"></div>
				<?=$header['tin']?>
				<div style="clear:both;height:5px;"></div>
				<?=$header['customer_address']?>
				<div style="clear:both;height:5px;"></div>
			</div>
			<div style="float:right;width:20%;display:table-cell;vertical-align:top;box-sizing:border-box;padding-left:50px;">
				<?=$_REQUEST['refid']?>
				<div style="clear:both;height:5px;"></div>
				<?=date('F j, Y',strtotime($header['date']))?>
				<div style="clear:both;height:5px;"></div>
				<?=$header['payment_terms']?>
				<div style="clear:both;height:5px;"></div>
				<?=$header['regnum']?>
				<div style="clear:both;height:10px;"></div>
				<?=$header['ponum']?>
				<div style="clear:both;height:5px;"></div>
				<?=$header['region']?>
				<div style="clear:both;height:5px;"></div>
				<?=$header['agent_name']?>
			</div>
		</div>
		<div style="clear:both;height:50px;"></div>
		<table class="tbl2" cellspacing="0" cellpadding="0" width="100%" border="1">
			<?php $count=1;$total=0;$num=17;
			foreach($items as $key => $val){ 
				$tbl .= '<tr>
					<td style="width:30px;">'.($val['qty']==0?"":$val['qty']).'</td>
					<td style="text-align:left;width:50px;"> '.$val['unit'].'</td>
					<td colspan="3" style="text-align:left;padding-left:10px;width:180px;">'.$val['item_spec'].'</td>
					<td style="width:100px;">'.$val['barcode'].'</td>
					<td style="width:150px;text-align:right;">'.($val['unitprice']?number_format($val['unitprice'],2):"").'</td>
					<td style="text-align:right;width:150px;">'.number_format($val['amount'],2).'</td>
				</tr>';
			$count++;$total+=$val['amount'];
			}
			if($header['agreement']&&$_REQUEST['tbltype']=="billing_statement"){
				echo "<tr>
				<td style='vertical-align: top;' rowspan='".($num-$count)."' colspan='6'>".$header['agreement']."</td>
				<td style='text-align:right;width:200px;'>".number_format($total,2)."</td>
				</tr>";
				for($x=1;$x<=($num-$count);$x++){
					echo "<tr><td>&nbsp;</td></tr>";
				}
			}else{
				echo $tbl;
				
				for($x=1;$x<=($num-$count);$x++){
					echo "<tr><td colspan='5'>&nbsp;</td></tr>";
				
					if($x==4){
						echo $header['agreement']?"<tr><td colspan='5'>{$header['agreement']}</td></tr>":"";
					}
				}

				
			}
			?>
		</table>
		<table class="tbl2" cellspacing="0" cellpadding="0" style="width:750px;position:absolute;right:-70px;bottom:-100px;">
			<tr>
				<td colspan="3" style="text-align:right;"></td>
				<td colspan="2" style="text-align:right;"><?=$header['taxtype']=="zerorated"?number_format($total,2):number_format($total-($total/9.333333),2)?></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;"><?=$header['taxtype']=="zerorated"?number_format($total,2):number_format($total-($total/9.333333),2)?></td>
				<td colspan="2" style="text-align:right;"><?=number_format(0,2)?></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;"></td>
				<td colspan="2" style="text-align:right;"><?=$header['taxtype']=="zerorated"?0.00:number_format($total/9.333333,2)?></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:right;"><?=$header['taxtype']=="zerorated"?0.00:number_format($total/9.333333,2)?></td>
				<td colspan="2" style="text-align:right;"><?=number_format($total,2)?></td>
			</tr>
		</table>
</body>

<script>
$(document).ready(function() {
	
});


</script>
</html>
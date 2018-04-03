<?php
ob_start();
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$sql = mysql_query("select a.*,b.cust_id from tbl_sales_receipt_{$_SESSION['counter_num']} a
	left join tbl_customers_trans b on a.receipt_id=b.receipt and a.reading=b.reading 
	where receipt_id='".$_REQUEST['receipt_num']."' and counter_num='".$_SESSION['counter_num']."' limit 1");
$info = mysql_fetch_assoc($sql);
$x = "select * from tbl_sales_items where receipt='".$_REQUEST['receipt_num']."' and counter='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'";
$sql_item = mysql_query($x);
$custname = $db->getWHERE("*","tbl_customers","where cust_id='{$info['cust_id']}'");
?>
<div style="rotate:-90deg;width:400px;height:750px;font-size:17px;font-family:Arial, Geneva, Helvetica, Sans-Serif;">
	<?php echo $info['receipt_id'] ?>
	<div style="clear:both;height:50px;"></div>
	<table style="width:100%;">
		<tr>
			<td style="width:50%;text-align:center;"><?php echo $custname['customer_name'] ?></td>
			<td style="width:50%;text-align:center;"><?php echo $info['date'] ?></td>
		</tr>
	</table>

	<div style="clear:both;height:50px;"></div>
	<div style="height:400px;">
		<table style="width:100%;" cellspacing="0" cellpadding="0">
		<?php
		while($row_items = mysql_fetch_assoc($sql_item)){
			?>
			<tr>
				<td style="width:100px;text-align:center;"><?=$row_items['qty']." ".$row_items['unit']?></td>
				<td style="width:300px;"><?=$row_items['item_desc']?></td>
				<td style="width:100px;text-align:center;"><?=number_format($row_items['selling'],2)?></td>
				<td style="width:100px;text-align:right;"><?=number_format($row_items['total'],2)?></td>
			</tr>
			<?
		}
		?>
		</table>
	</div>
	<div style="clear:both;height:30px;"></div>
	<div style="width:100%;text-align:right;padding-right:30px;"><?=number_format($info['amount'],2)?></div>
</div>
<?php
$db->closeDb();

     $content = ob_get_clean();

    // convert
    //require_once(dirname(__FILE__).'/../html2pdf.class.php');
	require_once('../class/html2pdf_v4.03/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($content);
        $html2pdf->Output('TRA.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>

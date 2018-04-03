<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<script src="../js/myjs.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selected {background-color: red; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
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
require_once"../class/pagination.class.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$p=new pagination();
$con=new dbUpdate();
$db->openDb();
if($_REQUEST['searchtxt']){
	$sql="select a.*,b.supplier_id,b.date,c.* from tbl_{$_REQUEST['page']}_items a 
		left join tbl_{$_REQUEST['page']}_header b on a.stockin_refid=b.id 
		left join tbl_supplier c on b.supplier_id=c.id 
		where a.item_desc like '%{$_REQUEST['searchtxt']}%' order by date desc";
	$qry = mysql_query($sql);
	if(!$qry){
		echo mysql_error();
	}
}
?>
<body style="padding:5px;height:100%;font-size:10px;">
	<fieldset>
		<legend>Search Item</legend>
		<form method="post" id="frmfilter">
			<input type="text" name="searchtxt" value="<?=$_REQUEST['searchtxt']?>" style="float:left;width:70%;margin-right:10px;"/>
			<input type="button" value="Execute" onclick="executeFilter()" style="float:left;width:80px;height:30px;"/>
		</form>
	</fieldset>
	<fieldset>
		<legend>Item Details</legend>
		<div style="float:left;width:45%;margin-right:10px;">
			<div style="width:80px;float:left;">Code:</div>
			<input type="text" name="code" style="float:left;width:350px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:80px;float:left;">Supplier:</div>
			<input type="text" name="supplier" style="float:left;width:350px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:80px;float:left;">Contact #:</div>
			<input type="text" name="contact" style="float:left;width:350px;"/>
		</div>
		<div style="float:left;width:45%;">
			<div style="width:80px;float:left;">Price:</div>
			<input type="text" name="price" style="float:left;width:150px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:80px;float:left;">Selling:</div>
			<input type="text" name="selling" style="float:left;width:150px;"/>
		</div>
		<div style="clear:both;height:5px;"></div>
		<fieldset style="width:95%;">
			<legend>Description</legend>
			<textarea id="desc" style="height:100px;width:100%;"></textarea>
		</fieldset>
	</fieldset>
	<fieldset>
		<legend>Suppliers</legend>
		<div style="height:375px;overflow:auto;padding:5px;">
			<table id="tbl" class="navigateable" style="width:100%;">
				<thead>
					<tr>
						<th>Date</th>
						<th>Ref#</th>
						<th>Code</th>
						<th>Name</th>
						<th>Contact #</th>
						<th>Product Name</th>
						<th>Price</th>
						<th>Selling</th>
					</tr>
				</thead>
				<tbody>
					<? 	while($row = mysql_fetch_assoc($qry)){ ?>
					<tr>
						<td style="font-size:12px;width:80px;"><?=$row['date']?></td>
						<td><?=$row['id']?></td>
						<td><?=$row['barcode']?></td>
						<td><?=$row['supplier_name']?></td>
						<td><?=$row['contact_number']?></td>
						<td><?=$row['item_desc']?></td>
						<td style="text-align:right;"><?=number_format($row['cost'],2)?></td>
						<td style="text-align:right;"><?=number_format($row['selling'],2)?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</fieldset>
</body>
<script>
	$("#tbl tbody tr").click(function(){
		//alert($(this).closest('tr').find('td:eq(1)').text());
		$("input[name='code']").val($(this).closest('tr').find('td:eq(2)').text());
		$("input[name='supplier']").val($(this).closest('tr').find('td:eq(3)').text());
		$("input[name='contact']").val($(this).closest('tr').find('td:eq(4)').text());
		$("input[name='price']").val($(this).closest('tr').find('td:eq(6)').text());
		$("input[name='selling']").val($(this).closest('tr').find('td:eq(7)').text());
		$("#desc").text($(this).closest('tr').find('td:eq(5)').text());
	});
	$('input[name*="date"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$(document).ready(function(){
		jQuery.tableNavigation();
		window.opener.location.reload();
	});
	function executeFilter(){
		$("#frmfilter").submit();
	}
	function transferStockin(val,xfrom){
		//window.opener.setValue(val);
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax_stockin.php?execute=viewStockin&refid='+val+'&location=tbl&stocktransfer=<?=$_REQUEST['stocktransfer']?>&xfrom='+xfrom,
			data:urls,
			type:"POST",
			success:function(data){
				window.close();
				window.opener.location.reload();
				//window.opener.refresh();
			}
		});
	}
	function transferPO(val){
		//window.opener.setValue(val);
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax_stockin.php?execute=transferPO&refid='+val,
			data:urls,
			type:"POST",
			success:function(data){
				window.close();
				window.opener.location.reload();
				//window.opener.refresh();
				//alert(data);
			}
		});
	}
	function viewPO(val){
		var ponum = val;
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax.php?execute=loadpo&ponum='+ponum,
			type:"POST",
			data:urls,
			success:function(data){
				if(data=="success"){
					window.close();
					window.opener.location.reload();
					//alert(data);
				}else{
					alert(data);
				}
			}
		});
	}
	function delStockout(val){
		var r = confirm("Are you sure you want to delete this?");
		if (r == true){
			$.ajax({
				url: 'pos_ajax_stockin.php?execute=delStockout&refid='+val,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	}
	// function viewReport(page){
		// if (window.showModalDialog) {
			// window.showModalDialog(page,"PO","dialogWidth:650px;dialogHeight:650px");
		// } else {
			// window.open(page,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		// }
	// }
	function printPreview(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
	function printPreview_withForm(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin_withform.php?refid='+refid,"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin_withform.php?refid='+refid,"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
</script>


<?php
if($_POST){
	$sql_receipt = "insert into tbl_stockin_header set 
		date='".$_REQUEST['stockin_date']."',
		supplier_id='3',
		remarks='".$_REQUEST['remarks']."',
		status='From StockTransfer',
		total='".$db->sumCostInArray($_SESSION['stockin'])."'";
	$qry_receipt = mysql_query($sql_receipt);
	$refid = mysql_insert_id();
	if($qry_receipt){
		foreach($db->subval_sort($_SESSION['stockin'],'count',arsort) as $val){ 
			$save = $db->savingEntry($refid,$val,"tbl_stockin_items");
			if(!$save){
				echo mysql_error();
			}else{
				echo $db->updateInvNew($val['sku']);
			}
		}
	}else{
		echo mysql_error();
	}
	//************************************************************//
	$sql_receipt = "insert into tbl_stockout_header set 
		date='".$_REQUEST['stockin_date']."',
		supplier_id='3',
		remarks='".$_REQUEST['remarks']."',
		status='From StockTransfer',
		total='".$db->sumCostInArray($_SESSION['stockout'])."'";
	$qry_receipt = mysql_query($sql_receipt);
	$refid = mysql_insert_id();
	if($qry_receipt){
		foreach($db->subval_sort($_SESSION['stockout'],'count',arsort) as $val){ 
			$save = $db->savingEntry($refid,$val,"tbl_stockout_items");
			if(!$save){
				echo mysql_error();
			}else{
				echo $db->updateInvNew($val['sku']);
			}
		}
	}else{
		echo mysql_error();
	}
	unset($_SESSION['stockin']);unset($_SESSION['stockout']);unset($_SESSION['count']);
	header("location:$location");
}
?>
<h2>Transfer Stock</h2>
<div style="clear:both;height:10px;"></div>
<form method="post" onsubmit="return validate();" name="frm_transfer">
	<div style="float:left;margin-right:10px;">
		<div style="float:left;margin-right:5px;">
			<div style="float:left;margin-right:10px;width:80px;">Date:</div>
			<input style="float:left;width:196px;" type="text" name="stockin_date" id="stockin_date" value="<?php echo date('Y-m-d'); ?>"/>
		</div>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;">
			<div style="float:left;margin-right:10px;width:80px;">Remarks:</div>
			<input style="float:left;width:430px;" type="text" name="remarks" id="remarks" />
		</div>
	</div>
	<input type="submit" style="float:left;height:40px;width:120px;" value="Save"/>
</form>
<div style="clear:both;height:5px;"></div>
<fieldset>
	<legend>IN</legend>
	<div style="height:200px;overflow:auto;">
		<table class="navigateableMain mytbl" id="mytbl_in" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th style="border:none;">Barcodes</th>
					<th style="border:none;" width="420px">Desc</th>
					<th style="border:none;">Cost</th>
					<th style="border:none;">Qty</th>
					<th style="border:none;">Unit</th>
					<th style="border:none;">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php if(isset($_SESSION['stockin'])){ $change="";$xtotal=0;?>
					<?php $count=1; foreach($db->subval_sort($_SESSION['stockin'],'count',arsort) as $val){ ?>
						<tr>
							<td><a href="javascript:backToBarcode('<?=$val['bcode']?>');" class="activationMain"><?php echo $val['bcode']; ?></a></td>
							<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
							<td style="text-align:right;"><?php echo number_format($val['cost'],2) ?></td>
							<td style="text-align:right;"><?php echo $val['qty'] ?></td>
							<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
							<td  style="text-align:right;"><?php echo number_format(($val['cost']*$val['qty'])*(1-$val['discount']),2) ?></td>
							<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
						</tr>
					<?php $xtotal+=(($val['cost']*$val['qty'])*(1-$val['discount']));$count++;} ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;height:10px;"></div>
	<input onkeypress="return runScript(event,this)" type="text" name="barcode_in" id="barcode_in" style="float:left;height:25px;width:100%"/>
</fieldset>
<fieldset>
	<legend>OUT</legend>
	<div style="height:200px;overflow:auto;">
		<table class="navigateableMain mytbl" id="mytbl_out" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th style="border:none;">Barcodes</th>
					<th style="border:none;" width="420px">Desc</th>
					<th style="border:none;">Cost</th>
					<th style="border:none;">Qty</th>
					<th style="border:none;">Unit</th>
					<th style="border:none;">Amount</th>
				</tr>
			</thead>
			<tbody>
				<?php if(isset($_SESSION['stockout'])){ $change="";$xtotal=0;?>
					<?php $count=1; foreach($db->subval_sort($_SESSION['stockout'],'count',arsort) as $val){ ?>
						<tr>
							<td><a href="javascript:backToBarcode('<?=$val['bcode']?>');" class="activationMain"><?php echo $val['bcode']; ?></a></td>
							<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
							<td style="text-align:right;"><?php echo number_format($val['cost'],2) ?></td>
							<td style="text-align:right;"><?php echo $val['qty'] ?></td>
							<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
							<td  style="text-align:right;"><?php echo number_format(($val['cost']*$val['qty'])*(1-$val['discount']),2) ?></td>
							<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
						</tr>
					<?php $xtotal+=(($val['cost']*$val['qty'])*(1-$val['discount']));$count++;} ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div style="clear:both;height:10px;"></div>
	<input onkeypress="return runScript(event,this)" type="text" name="barcode_out" id="barcode_out" style="float:left;height:25px;width:100%"/>
</fieldset>
<script>
$("#mytbl_in").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		delProdsale($("tr.selected").find('td:eq(0)').text(),'barcode_in');
	}else if(chCode==113){ //pressing f2
		qtyclick($("tr.selected").find('td:eq(0)').text(),'barcode_in');
	}else if(chCode==119){ //pressing f8
		priceclick($("tr.selected").find('td:eq(0)').text(),'barcode_in');
	}else if(chCode==115){ //pressing f4
		uomlist($("tr.selected").find('td:eq(0)').text());
		jQuery.tableNavigationUom();
	}
});
$("#mytbl_out").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		delProdsale($("tr.selected").find('td:eq(0)').text(),'barcode_out');
	}else if(chCode==113){ //pressing f2
		qtyclick($("tr.selected").find('td:eq(0)').text(),'barcode_out');
	}else if(chCode==119){ //pressing f8
		priceclick($("tr.selected").find('td:eq(0)').text(),'barcode_out');
	}else if(chCode==115){ //pressing f4
		uomlist($("tr.selected").find('td:eq(0)').text());
		jQuery.tableNavigationUom();
	}
});
function pricechange(bcode,newprice,type){
	if(bcode!=""){
		$.ajax({
			url: './content/pos_ajax.php?execute=pricechange&barcode='+bcode+'&newprice='+newprice+'&type='+type,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location=document.URL;
				}else{
					alert(data);
					window.location=document.URL;
				}
			}
		});
	}
}
function validate(){
	var frm = document.frm_transfer;
	if(frm.remarks.value==""){
		alert("Please input remarks before saving...");
		return false;
	}
	return true;
}
</script>
<?php
// if($db->constatus=='lizgan_warehouse'){ 
	// $db->openDb("main");
// }
// $res = $db->resultArray("a.*,b.*,c.*","tbl_inv_allbranch a 
		// left join tbl_product_name b on a.sku_id=b.sku_id
		// left join tbl_barcodes c on a.sku_id=c.sku_id","where b.supplier_id='{$_REQUEST['supplier']}'");
// if($_SESSION['connect']=='warehouse'or$_SESSION['connect']=='ucmambaling'){
	// $inv = $con->getInvAllBranchWarehouse();
// }else{
	// $inv = $con->getInvAllBranch();
// }
$inv = $con->getInvAllBranch();
$where = $_REQUEST['supplier']||$_REQUEST['prodname']?"where":"";
$and = $_REQUEST['supplier']&&$_REQUEST['prodname']?"and":"";
$supplier = $_REQUEST['supplier']?"b.supplier_id='{$_REQUEST['supplier']}'":"";
$prodname = $_REQUEST['prodname']?"b.product_name like '%{$_REQUEST['prodname']}%'":"";
$res = $db->resultArray("b.*,c.*","tbl_product_name b 
		left join tbl_barcodes c on b.sku_id=c.sku_id","$where $supplier $and $prodname");
$supplier = $db->resultArray("*","tbl_supplier","");
?>
<div style="min-height:650px;overflow:auto;background-color:transparent;padding:20px;border:1px solid gray;">
	<form method="post">
		<fieldset>
			<legend>Filter:</legend>
			<div style="float:left;margin-right:5px;">Supplier:</div>
			<select name="supplier" style="width:300px;float:left;margin-right:10px;">
				<option value="">Select Supplier</option>
				<?php foreach($supplier as $k=>$v){ 
					echo "<option ".($_REQUEST['supplier']==$v['id']?"selected":"")." value='{$v['id']}'>{$v['supplier_name']}</option>";
				} ?>
			</select>
			<div style="float:left;margin-right:5px;">Product:</div>
			<input type="text" name="prodname" id="prodname" style="float:left;width:200px;" value="<?=$_REQUEST['prodname']?$_REQUEST['prodname']:""?>"/>
			<div style="clear:both;height:5px;"></div>
			<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
			<input type="submit" value="Execute" style="float:right;width:100px;"/>
		</fieldset>
	</form>
	<div style="clear:both;height:10px;"></div>
	<div style="height:600px;overflow:auto;">
		<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th rowspan="2">Barcodes</th>
					<th rowspan="2">Desc</th>
					<th rowspan="2">Cost</th>
					<th rowspan="2">Selling</th>
					<th colspan="6">Stock OnHand</th>
					<th rowspan="2">Total Worth</th>
				</tr>
				<tr>
					<th>UC-Main</th>
					<th>UC-LM</th>
					<th>UC-Mambaling</th>
					<th>UC-Banilad</th>
					<th>Warehouse</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
			<? 	
				foreach($res as $k => $row){ 
					$ucmain=$db->outputInvBal($inv[$row['sku_id']]['ucmain'],$row['sku_id']);
					$uclm=$db->outputInvBal($inv[$row['sku_id']]['uclm'],$row['sku_id']);
					$ucmambaling=$db->outputInvBal($inv[$row['sku_id']]['ucmambaling'],$row['sku_id']);
					$ucbanilad=$db->outputInvBal($inv[$row['sku_id']]['ucbanilad'],$row['sku_id']);
					$warehouse=$db->outputInvBal($inv[$row['sku_id']]['warehouse'],$row['sku_id']);
					$total=$ucmain+$uclm+$ucmambaling+$ucbanilad+$warehouse;
					//if($ucmain>0||$uclm>0||$ucmambaling>0||$ucbanilad>0||$warehouse>0){	
					
					?>
						<tr>
							<td class="data_lightgray"><a href="javascript:viewTrans('<?php echo $row['sku_id'] ?>','<?php echo $row['product_name'] ?>')"><?php echo $row['barcode'] ?></a></td>
							<td class="data_lightgray" style="text-align:left;"><?= $row['product_name']?></td>
							<td class="data_lightgray" style="text-align:right;"><?= number_format($row['cost'],2) ?></td>
							<td class="data_lightgray" style="text-align:right;"><?= number_format($row['price'],2) ?></td>
							<td class="data_lightgray" style="text-align:center;"><?= $ucmain==0?"":$db->outputInvBal($ucmain,$row['sku_id']); ?></td>
							<td class="data_lightgray" style="text-align:center;"><?= $uclm==0?"":$db->outputInvBal($uclm,$row['sku_id']); ?></td>
							<td class="data_lightgray" style="text-align:center;"><?= $ucmambaling==0?"":$db->outputInvBal($ucmambaling,$row['sku_id']); ?></td>
							<td class="data_lightgray" style="text-align:center;"><?= $ucbanilad==0?"":$db->outputInvBal($ucbanilad,$row['sku_id']); ?></td>
							<td class="data_lightgray" style="text-align:center;"><?= $warehouse==0?"":$db->outputInvBal($warehouse,$row['sku_id']); ?></td>
							<td class="data_lightgray" style="text-align:center;"><?=$total?></td>
							<td class="data_lightgray" style="text-align:center;"><?=number_format($total*$row['cost'],2)?></td>
						</tr>
					<? $subtotal+=$total*$row['cost'];
					//}
				}
				
			?>
			</tbody>
		</table>
	</div>
</div>
<?php
$lokasion = $_REQUEST['location']?$_REQUEST['location']:"tbl";
// $tbl_header=$_REQUEST['page']!="stockin"?"{$lokasion}_stockout_header":"{$lokasion}_stockin_header";
// $tbl_items=$_REQUEST['page']!="stockin"?"{$lokasion}_stockout_items":"{$lokasion}_stockin_items";
$tbl_header="{$lokasion}_{$_REQUEST['page']}_header";
$tbl_items="{$lokasion}_{$_REQUEST['page']}_items";
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$sessiontype = $_REQUEST['page'];
if($_POST['stockin_date']){
	$refid = $_REQUEST['refid'];
	if($refid){
		$sql_receipt = "UPDATE $tbl_header set 
			date='".$_REQUEST['stockin_date']."',
			supplier_id='".$_REQUEST['supplier_id']."',
			remarks='".$_REQUEST['remarks']."',
			status='".$_REQUEST['status']."',
			".($_SESSION['poDISCOUNT1']?"`{$_SESSION['poDISCOUNT1']['type']}`='{$_SESSION['poDISCOUNT1']['amt']}',":"")."
			".($_SESSION['poDISCOUNT2']?"`{$_SESSION['poDISCOUNT2']['type']}`='{$_SESSION['poDISCOUNT2']['amt']}',":"")."
			total='".str_replace(",","",$_REQUEST['xtotal'])."'
			where id='".$refid."'"; 
		//exit;
		$qry_receipt = mysql_query($sql_receipt);
		if($qry_receipt){
			$save = $db->savingEntry2($refid,$_SESSION[$sessiontype],"$tbl_items");
			if(!$save){
				$_SESSION['error'] .= "Update 1.)". mysql_error()."<br/><hr/><br/>";
			}
		}else{
			$_SESSION['error'] .= "Update 2.)". mysql_error()."<br/><hr/><br/>";
		}
	}else{
		$sql_receipt = "insert into $tbl_header set 
			date='".$_REQUEST['stockin_date']."',
			supplier_id='".$_REQUEST['supplier_id']."',
			remarks='".$_REQUEST['remarks']."',
			status='".$_REQUEST['status']."',
			total='".str_replace(",","",$_REQUEST['xtotal'])."'"; 
		$qry_receipt = mysql_query($sql_receipt);
		$refid = mysql_insert_id();
		if($qry_receipt){
			$save = $db->savingEntry2($refid,$_SESSION[$sessiontype],"$tbl_items");
			if(!$save){
				$_SESSION['error'] .= "Insert 1.)". mysql_error()."<br/><hr/><br/>";
			}
			if($_REQUEST['poid']){
				//mysql_query("update tbl_po_header set status='RECEIVED' where id='{$_REQUEST['poid']}'");
				$con->executeReceivedPO($_REQUEST['poid'],$refid);
			}
		}else{
			$_SESSION['error'] .= "Insert 2.)". mysql_error()."<br/><hr/><br/>";
		}
	}
	
	// if($_REQUEST['page']=="po"){
		// $con->executePOupdate();
	// }else
	if($_REQUEST['page']=="stockout"){
		if($_REQUEST['status']=="Transfer Stock"){
			switch($_REQUEST['supplier_id']){
				case 1:
					$to="ucbanilad";
				break;
				case 2:
					$to="uclm";
				break;
				case 3:
					$to="ucmain";
				break;
				case 4:
					$to="ucmambaling";
				break;
				case 5:
					$to="warehouse";
				break;
			}
			$con->transferStock('main',$to,"tbl_stockout_header","tbl_stocktransfer_header",$refid,"id");
			$con->transferStock('main',$to,"tbl_stockout_items","tbl_stocktransfer_items",$refid,"stockin_refid");
		}
	}
	if($_SESSION[$sessiontype.'_header']['stid']){
		$update = $con->updateStocktransfer($_SESSION[$sessiontype.'_header']['stid'],$_SESSION['connect']);
		// $qry=mysql_query("update tbl_stocktransfer_header set `status`='Received Stock' where id='".$_SESSION[$sessiontype.'_header']['stid']."'");
		if(!$update){
			echo "Stocktransfer: Update Error...";
		}else{
			echo "Stocktransfer: Updated successfully...";
		}
	}
	
	//echo "<script>$(document).ready(function(){viewReceipt(".$refid.");});</script>";
	unset($_SESSION[$sessiontype]);unset($_SESSION[$sessiontype.'_header']);unset($_SESSION['count']);
	unset($_SESSION['poDISCOUNT1']);unset($_SESSION['poDISCOUNT2']);
	header("location:$location");
	
}
$qrysup = mysql_query("select * from tbl_supplier");
?>
<div class="top">
	<div class="header">
		<div>
			<form method="post" name="frm_stockin" style="padding:10px 0 10px 0;">
			<div style="float:left;border:1px solid #000;margin-right:5px;padding:5px;">
				<div style="float:left;margin-right:5px;">
					<div style="float:left;margin-right:10px;width:80px;">Date:</div>
					<input style="float:left;width:196px;" type="text" name="stockin_date" id="stockin_date" value="<?php echo $_SESSION[$sessiontype.'_header']['date']?$_SESSION[$sessiontype.'_header']['date']:date('Y-m-d'); ?>"/>
				</div>

				<div style="clear:both;height:5px;"></div>
				<?php if($_REQUEST['page']=='stockin'){ ?>
					<div style="float:left;margin-right:10px;width:80px;">Status:</div>
					<select style="float:left;width:200px;" name="status" id="status">
						<option <?=$_SESSION[$sessiontype.'_header']['status']=='Received from Supplier'?'selected':''?> value="Received from Supplier">Received from Supplier</option>
						<option <?=$_SESSION[$sessiontype.'_header']['status']=='Received from Branch'?'selected':''?> value="Received from Branch">Received from Branch</option>
					</select>
				<?php }elseif($_REQUEST['page']=='stockout'){ ?>
					<div style="float:left;margin-right:10px;width:80px;">Status:</div>
					<select style="float:left;width:200px;" name="status" id="status">
						<option <?=$_SESSION[$sessiontype.'_header']['status']=='Transfer Stock'?'selected':''?> value="Transfer Stock">Transfer Stock</option>
						<?php if($_SESSION['connect']=='warehouse'){ ?>
						<option <?=$_SESSION[$sessiontype.'_header']['status']=='Return Stock'?'selected':''?> value="Return Stock">Return Stock</option>
						<?php } ?>
					</select>
				<?php }else{ ?>
					<div style="float:left;margin-right:10px;width:80px;">Status:</div>
					<select style="float:left;width:200px;" name="status" id="status">
						<option <?=$_SESSION[$sessiontype.'_header']['status']=='PO'?'selected':''?> value="PO">PO</option>
					</select>
				<?php } ?>
				
				<?php if($_REQUEST['page']=='stockin'&&$_SESSION['connect']=='warehouse'){ ?>
					<div style="float:left;margin-left:20px;">
						<div id="poidref" style="float:left;margin-right:5px;width:72px;">PO #:</div>
						<input style="float:left;width:133px;" type="text" name="poid" id="poid" value="<?php echo $_SESSION[$sessiontype.'_header']['poid']?$_SESSION[$sessiontype.'_header']['poid']:""; ?>"/>
					</div>
				<?php }?>
				<div style="clear:both;height:5px;"></div>
				<?php if((isset($_SESSION['connect'])&&$_SESSION['connect']!='warehouse') or ($_SESSION[$sessiontype.'_header']['status']=='Transfer Stock') or ($_SESSION[$sessiontype.'_header']['status']=='Received from Branch')){ ?>
					<div style="float:left;margin-right:10px;width:80px;">Branch:</div>
					<select name="supplier_id" id="supplier_id" style="float:left;width:200px;">
						<option value="">Select Branch</option>
						<?php foreach($branches as $key=>$val){ ?>
							<option <?=$_SESSION[$sessiontype.'_header']['supplier_id']==$key?'selected':''?> value="<?=$key?>"><?=$val?></option>
						<?php } ?>
					</select>
				<?php }else{ ?>
					<div style="float:left;margin-right:10px;width:80px;">Supplier:</div>
					<select name="supplier_id" id="supplier_id" style="float:left;width:200px;">
						<option value="">Select Supplier</option>
						<?php while($rowsup = mysql_fetch_assoc($qrysup)){ ?>
							<option <?=$_SESSION[$sessiontype.'_header']['supplier_id']==$rowsup['id']?'selected':''?> value="<?=$rowsup['id']?>"><?=$rowsup['supplier_name']?></option>
						<?php } ?>
					</select>
				<?php } ?>
				<div style="float:left;margin-left:20px;">
					<div style="float:left;margin-right:5px;width:72px;">REF #:</div>
					<input style="float:left;width:133px;" readonly type="text" name="refid" id="refid" value="<?php echo $_SESSION[$sessiontype.'_header']['refid']?$_SESSION[$sessiontype.'_header']['refid']:""; ?>"/>
				</div>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:5px;">
					<div style="float:left;margin-right:10px;width:80px;">Remarks:</div>
					<input style="float:left;width:430px;" onkeypress="saveHeaderToSession(event)" type="text" name="remarks" id="remarks" value="<?=$_SESSION[$sessiontype.'_header']['remarks']?>"/>
				</div>
				<div style="clear:both;height:5px;"></div>
			</div>
			<fieldset>
				<legend><strong>TOTAL</strong></legend>
				<div style="font-size:15px;width:100%;color:red;">
					<?php if($_SESSION['poDISCOUNT1']){ ?>
					<div style="float:left;width:100%;">Volume Disc: <span style="float:right;"><?=number_format($_SESSION['poDISCOUNT1']['amt'],2)?></span></div>
					<?php } ?><br/>
					<?php if($_SESSION['poDISCOUNT2']){ ?>
					<div style="float:left;width:100%;">Add Disc: <span style="float:right;"><?=number_format($_SESSION['poDISCOUNT2']['amt'],2)?></span></div>
					<?php } ?>
				</div>
				<div style="clear:both;"></div>
				<input readonly type="text" id="xtotal" name="xtotal" style="float:right;width:375px;font-size:55px;margin-top:10px;text-align:right;border:none;border-color:transparent;background:transparent;"/>
			</fieldset>
			</form>
		</div>
	</div>
	<div class="content" style="min-height:300px;">
		<div style="float:left;width:170px;text-align:center;">
			<fieldset style="padding:5px;text-align:center;">
				<span style="font-size:20px;"><strong><?= strtoupper($_REQUEST['page'])?></strong></span><br/>
				<span style="font-size:12px;color:red;"><?= "Branch: ".($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING");?></span>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="padding:5px;text-align:center;">
				<legend>&nbsp; MENU &nbsp;</legend>
				<button id="bt1" class="buthov" type="button" onclick="saveStockin()" style="height:40px;width:150px;">Save <?= strtoupper($_REQUEST['page'])?></button>
				<input id="bt2" class="buthov" type="button" value="Add Supplier" onclick="addSupplier();" style="height:40px;width:150px;"/>
				<input id="bt3" class="buthov" type="button" value="Add NewProd" onclick="prodAdd();" style="height:40px;width:150px;"/>
				<button id="bt4" class="buthov" type="button" onclick="totalDiscounting()" style="height:40px;width:150px;">Total D<span style="font-weight:bold;text-decoration:underline;">i</span>scounting</button>
				<?if($_REQUEST['page']=='stockin'){?>
					<?php if($_SESSION['connect']=='warehouse'){ ?>
						<input id="bt9" class="buthov" type="button" value="PO List" onclick="viewPOlist();" style="height:40px;width:150px;"/>
					<?php } ?>
					<input id="bt8" class="buthov" type="button" value="TransferedStock" onclick="viewTransferedStock();" style="height:40px;width:150px;"/>
				<?}?>
				<input id="bt5" class="buthov" type="button" value="View Records" onclick="viewStockin();" style="height:40px;width:150px;"/>
				<? if($_SESSION['restrictionid']==5){ ?>
					<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="float:right;height:30px;width:100%;float:left;"/>
				<? }else{ ?>
					<input id="bt7" class="buthov" type="button" value="Main Dashboard" onclick="window.location='index.php'" style="height:40px;width:150px;"/>
				<? } ?>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="padding:5px;font-size:10px;">
				<legend>LEGEND: Press</legend>
				<div style="float:left;"><span style="color:red;">[F2]</span> (Change Qty)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[F1]</span> (Per Item Disc)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[F4]</span> (UOM List)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[F8]</span> (Change Price)</div>
				<br/>
				<div style="float:left;"><span style="color:red;">[Delete]</span> (Remove Item List)</div>
			</fieldset>
		</div>
		<div style="float:right;width:800px;">
			<div style="height:380px;overflow:auto;">
				<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th style="border:none;">Barcodes</th>
							<th style="border:none;" width="420px">Desc</th>
							<th style="border:none;">Cost</th>
							<th style="border:none;">Qty</th>
							<th style="border:none;">Unit</th>
							<th style="border:none;">Disc</th>
							<th style="border:none;">Amount</th>
							<th style="border:none;color:red;<?= $_SESSION[$sessiontype.'_header']['frompo']?'':'display:none;' ?>">PO Qty</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($_SESSION[$sessiontype])){ $change="";$xtotal=0;?>
							<?php $count=1; foreach($db->subval_sort($_SESSION[$sessiontype],'count',arsort) as $val){ ?>
								<tr>
									<td><a href="javascript:backToBarcode('<?=$val['bcode']?>');" class="activationMain"><?php echo $val['bcode']; ?></a></td>
									<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
									<td style="text-align:right;"><?php echo number_format($val['cost'],2) ?></td>
									<td style="text-align:right;"><?php echo $val['qty'] ?></td>
									<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
									<td  style="text-align:left;"><?php echo $val['discount'] ?></td>
									<td  style="text-align:right;"><?php echo number_format(($val['cost']*$val['qty'])*(1-$val['discount']),2) ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['id'] ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['divmul'] ?></td>
									<td  style="text-align:center;color:red;<?= $_SESSION[$sessiontype.'_header']['frompo']?'':'display:none;' ?>"><?php echo $val['poqty'] ?></td>
								</tr>
							<?php $xtotal+=(($val['cost']*$val['qty'])*(1-$val['discount']));$count++;} ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div style="clear:both;height:10px;"></div>
			<fieldset>
			<legend>Scan Barcode / Press Enter</legend>
			<input onkeypress="return runScript(event,this)" type="text" name="barcode" id="barcode" style="float:left;height:25px;width:100%"/>
			</fieldset>
		</div>
	</div>
	<div style="clear:both;height:10px;"></div>
</div>
<div id="addsup"></div>
<div id="dialogbox"></div>
<script>
$(document).ready(function() {
	var rem = '<?php echo $_SESSION[$sessiontype.'_header']['remarks'] ?>';
	$("#xtotal").val('<?php echo number_format($xtotal-($_SESSION['poDISCOUNT1']['amt']+$_SESSION['poDISCOUNT2']['amt']),2);?>');
	if(rem==''){
		$('#supplier_id').focus();
		$("#barcode").prop('disabled', true);
	}else{
		$('#barcode').focus();
	}
});
function refresh(){
	window.location='<?=$location?>';
}
$("#mytbl").click(function(e){
	jQuery.tableNavigationMain();
	return false;
});
$("#status").change(function(){
	//alert($(this).val());
	var val = $(this).val();
	if(val=="Received from Branch"){
		$("#poidref").html('ST#');
	}else if(val=="Received from Supplier"){
		$("#poidref").html('PO#');
	}
	dynamicSup(val);
});
function dynamicSup(type){
	$.ajax({
		url: './content/pos_ajax.php?execute=dynamic_supplier&type='+type,
		type:"POST",
		success:function(data){
			$("#supplier_id").html(data);
		}
	});
}
$("#poid").change(function(){
	var ponum = $(this).val();
	var sessiontype = '<?=$_REQUEST['page']?>';
	$.ajax({
		url: './content/pos_ajax.php?execute=loadpo&ponum='+ponum,
		type:"POST",
		data:{sessiontype:sessiontype},
		success:function(data){
			if(data=="success"){
				window.location.reload();
			}else{
				alert(data);
			}
		}
	});
	return false;
});
/*$("#remarks" ).change(function() {
  var formvalue = serializing('#frm_stockin');
	var type=getParam('page');
	if(formvalue.id!="" &&formvalue.date!="" && formvalue.supplier!='' && formvalue.remarks!='' && formvalue.status !='' && formvalue.currency !=''){
		$.ajax({
			url: './content/pos_ajax_stockin.php?execute=stockinHeaderSave&sessiontype='+type,
			type:"POST",
			data:formvalue,
			success:function(data){
				if(data=="success"){
					window.location=document.URL;
				}else{
					alert(data);
				}
			}
		});
	}else{
		alert("Pls fill-up all the information needed.");
	}
});*/
function saveHeaderToSession(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==13){
		var date = $('#stockin_date').val();
		var supplier = $('#supplier_id').val();
		var remarks = $('#remarks').val();
		var refid = $('#refid').val();
		var status = $('#status').val();
		var type=getParam('page');
		if(date!="" && supplier!='' && remarks!='' && status !=''){
			$.ajax({
				url: './content/pos_ajax_stockin.php?execute=stockinHeaderSave&sessiontype='+type,
				type:"POST",
				data:{date:date,supplier:supplier,remarks:remarks,refid:refid,status:status},
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}else{
			alert("Pls fill-up all the information needed.");
		}
	}
}
function setheader(date,supplier,remarks){
	$("input[name='stockin_date']").val(date);
	$("select[name='supplier_id']").val(supplier);
	$("input[name='remarks']").val(remarks);
}
function viewTransferedStock(){
	var urls = $.param(getUrl());
	if (window.showModalDialog) {
		window.showModalDialog('./content/view_stockin.php?'+urls+'&stocktransfer=true',"Receipt","dialogWidth:800px;dialogHeight:420px");
	} else {
		window.open('./content/view_stockin.php?'+urls+'&stocktransfer=true',"Receipt",'height=420,width=800,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
function viewPOlist(){
	var urls = $.param(getUrl());
	if (window.showModalDialog) {
		window.showModalDialog('./content/view_stockin.php?'+urls+'&po=true',"Receipt","dialogWidth:800px;dialogHeight:420px");
	} else {
		window.open('./content/view_stockin.php?'+urls+'&po=true',"Receipt",'height=420,width=800,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
function viewStockin(){
	var urls = $.param(getUrl());
	if (window.showModalDialog) {
		window.showModalDialog('./content/view_stockin.php?'+urls,"Receipt","dialogWidth:900px;dialogHeight:420px");
	} else {
		window.open('./content/view_stockin.php?'+urls,"Receipt",'height=420,width=900,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
/*function viewStockin(){
	if (window.showModalDialog) {
		window.showModalDialog('./content/view_stockin.php?location=<?=$_REQUEST['location']?>',"Receipt","dialogWidth:650px;dialogHeight:470px");
	} else {
		window.open('./content/view_stockin.php?location=<?=$_REQUEST['location']?>',"Receipt",'height=470,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}*/
function viewStockout(){
	if (window.showModalDialog) {
		window.showModalDialog('./content/view_stockout.php',"Receipt","dialogWidth:650px;dialogHeight:470px");
	} else {
		window.open('./content/view_stockout.php',"Receipt",'height=470,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
function saveStockin(){
	var frm = document.frm_stockin;
	if(frm.xtotal.value==0){
		alert("Pls input item before saving...");
		return false;
	}
	document.frm_stockin.submit();
}
function addsup(){
	var val = $("#supplier_name").val();
	if(val!=""){
		$.ajax({
			url: './content/pos_ajax_stockin.php?execute=addsupplier&val='+val,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location.reload();
				}else{
					alert(data);
				}
			}
		});
	}else{
		alert("Please fill-out the supplier name...");
	}
}
function viewReceipt(num) {
	if (window.showModalDialog) {
		window.showModalDialog('./reports/receipt.php?receipt_num='+num,"Receipt","dialogWidth:350px;dialogHeight:350px");
	} else {
		window.open('./reports/receipt.php?receipt_num='+num,"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
	//window.open('./reports/receipt.php?receipt_num='+num,"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
}
$("#mytbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	var type=getParam('page');
	if(chCode==46){ //pressing delete button
		delProdsale($("tr.selected").find('td:eq(0)').text(),type);
	}else if(chCode==113){ //pressing f2
		qtyclick($("tr.selected").find('td:eq(0)').text(),type,$("tr.selected").find('td:eq(9)').text());
	}else if(chCode==119){ //pressing f8
		priceclick($("tr.selected").find('td:eq(0)').text());
	}else if(chCode==112){ //pressing f3
		//uomlist($("tr.selected").find('td:eq(0)').text(),"Cost");
		peritemdisc($("tr.selected").find('td:eq(0)').text());
	}else if(chCode==115){ //pressing f4
		uomlist($("tr.selected").find('td:eq(0)').text());
		jQuery.tableNavigationUom();
	}
});
$(document).bind('keydown',function(e){
	var ctrlPressed=0;
	var altPressed=0;
	var shiftPressed=0;
	var evt = (e==null ? event:e);
	shiftPressed=evt.shiftKey;
	altPressed  =evt.altKey;
	ctrlPressed =evt.ctrlKey;
	
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(altPressed && chCode==73){
		totalDiscounting();
	}
	//alert(chCode);
	//27 esc
});
function addSupplier(){
	clickDialog("addsup",400,100,"addsup","Add Supplier");
}
function totalDiscounting(){
	clickDialog('dialogbox',400,300,'discounting_frm&page=<?=$_REQUEST['page']?>&total_amount='+parseFloat(($('#xtotal').val()).replace(',','')),'Discounting');
}
/*function prodAdd(){
	//var win=window.open('index.php?page=prod_maintenance_whole','_blank');
	//win.focus();
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=prod_maintenance_whole',"Prod Maintenance","dialogWidth:700px;dialogHeight:500px");
	} else {
		window.open('index.php?page=prod_maintenance_whole',"Prod Maintenance",'height=500,width=700,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}*/
function peritemdisc(bcode){
	var disc = prompt("Enter discount");
	if(disc !=""){
		if($.isNumeric(disc)){
			discchange(bcode,disc);
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new qty...");
	}
}
function discchange(bcode,disc){
	$.ajax({
		url: './content/pos_ajax_stockin.php?execute=peritemdisc&barcode='+bcode+'&disc='+disc+'&page=<?=$_REQUEST['page']?>',
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
/*function qtyclick(bcode){
	var newqty = prompt("Enter new qty");
	if(newqty !=""){
		if($.isNumeric(newqty)){
			qtychange(bcode,newqty);
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new qty...");
	}
}
function qtychange(val,newqty){
	var sessiontype = '&sessiontype='+getParam('page');
	var url = './content/pos_ajax.php?execute=qtychange&barcode='+val+'&newqty='+newqty+sessiontype;
	$.ajax({
		//url: './content/pos_ajax_stockin.php?execute=qtychange&barcode='+bcode+'&newqty='+newqty,
		url:url,
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alert(data);
			}
		}
	});
}*/
function priceclick(bcode){
	var newcost = prompt("Enter new price");
	if(newcost !=""){
		if($.isNumeric(newcost)){
			costchange(bcode,newcost);
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new price...");
	}
}
function costchange(val,newcost){
	var sessiontype = '&sessiontype='+getParam('page');
	var url = './content/pos_ajax.php?execute=pricechange&barcode='+val+'&newprice='+newcost+sessiontype;
	$.ajax({
		//url: './content/pos_ajax_stockin.php?execute=costchange&barcode='+bcode+'&newcost='+newcost,
		url:url,
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
</script>
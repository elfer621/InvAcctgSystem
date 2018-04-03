<?php
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$sessiontype = $_REQUEST['page'];
if($_POST){
	// print_r($_POST);
	// print_r($_SESSION[$sessiontype]);
	$sql ="insert into tbl_packages (id,package_name,packages,total_amt) values 
		('".$_REQUEST['refid']."','".$_REQUEST['package_name']."','".serialize($_SESSION[$sessiontype])."','".str_replace(",","",$_REQUEST['xtotal'])."') 
		on duplicate key update package_name=values(package_name),packages=values(packages),total_amt=values(total_amt)";
	$qry = mysql_query($sql);
	if(!$qry){
		echo mysql_error();
	}else{
		unset($_SESSION[$sessiontype]);
		unset($_SESSION[$sessiontype.'_header']);
		header("location:$location");
	}
}	
//header("location:$location");

//print_r($_SESSION[$sessiontype]);
//unset($_SESSION[$sessiontype]);
?>
<div class="top">
	<div class="header">
		<div>
			<form method="post" name="frm_stockin" style="padding:10px 0 10px 0;">
			<div style="float:left;border:1px solid #000;margin-right:5px;padding:5px;">
				<div style="float:left;margin-right:5px;width:80px;">REF #:</div>
				<input style="float:left;width:133px;" readonly type="text" name="refid" id="refid" value="<?php echo $_SESSION[$sessiontype.'_header']['id']?$_SESSION[$sessiontype.'_header']['id']:""; ?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:5px;">
					<div style="float:left;margin-right:5px;width:80px;">Package Name:</div>
					<input style="float:left;width:430px;" type="text" name="package_name" id="package_name" value="<?php echo $_SESSION[$sessiontype.'_header']['package_name']?$_SESSION[$sessiontype.'_header']['package_name']:""; ?>"/>
				</div>
				<div style="clear:both;height:5px;"></div>
			</div>
			<fieldset>
				<legend><strong>TOTAL</strong></legend>
				<input readonly type="text" id="xtotal" name="xtotal" style="float:right;width:375px;font-size:55px;margin-top:-10px;text-align:right;border:none;border-color:transparent;background:transparent;"/>
			</fieldset>
			</form>
		</div>
	</div>
	<div class="content" style="min-height:300px;">
		<div style="float:left;width:170px;text-align:center;">
			<fieldset style="padding:5px;text-align:center;">
				<span style="font-size:20px;"><strong>PACKAGE</strong></span><br/>
				<span style="font-size:12px;color:red;"><?= "Branch: ".($_SESSION['connect']?strtoupper($_SESSION['connect']):"ACCOUNTING");?></span>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="padding:5px;text-align:center;">
				<legend>&nbsp; MENU &nbsp;</legend>
				<button id="bt1" class="buthov" type="button" onclick="saveStockin()" style="height:40px;width:150px;">Save</button>
				<input id="bt3" class="buthov" type="button" value="Add NewProd" onclick="prodAdd();" style="height:40px;width:150px;"/>
				<input id="bt5" class="buthov" type="button" value="View Records" onclick="viewRecords();" style="height:40px;width:150px;"/>
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
			<div style="height:400px;overflow:auto;">
				<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th style="border:none;">Barcodes</th>
							<th style="border:none;" width="420px">Desc</th>
							<th style="border:none;">Selling</th>
							<th style="border:none;">Qty</th>
							<th style="border:none;">Unit</th>
							<th style="border:none;">Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php if(isset($_SESSION[$sessiontype])){ $change="";$xtotal=0;?>
							<?php $count=1; foreach($db->subval_sort($_SESSION[$sessiontype],'count',arsort) as $val){ 
								$rec = $db->getWHERE("*","tbl_barcodes","where sku_id='{$val['sku']}'");
								?>
								<tr>
									<td><a href="javascript:backToBarcode('<?=$val['bcode']?>');" class="activationMain"><?php echo $val['bcode']; ?></a></td>
									<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
									<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
									<td style="text-align:right;"><?php echo $val['qty'] ?></td>
									<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
									<td  style="text-align:right;"><?php echo number_format(($val['price']*$val['qty']),2) ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['id'] ?></td>
									<td  style="text-align:right;display:none;"><?php echo $val['divmul'] ?></td>
								</tr>
							<?php $xtotal+=(($val['price']*$val['qty']));$count++;} ?>
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
	$("#xtotal").val('<?php echo number_format($xtotal,2);?>');
	$('#barcode').focus();
});
$("#mytbl").click(function(e){
	jQuery.tableNavigationMain();
	return false;
});

function viewRecords(){
	var urls = getUrl();
	$("#prodlist").html("");
	clickDialog('prodlist',1000,550,'packageslist','Package List',urls);
	jQuery.tableNavigation();
	$("#search_prodname").focus();
}
function saveStockin(){
	var frm = document.frm_stockin;
	if(frm.xtotal.value==0){
		alert("Pls input item before saving...");
		return false;
	}
	if(frm.package_name.value==""){
		alert("Pls specify Package Name...");
		return false;
	}
	document.frm_stockin.submit();
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
	if(val!=""){
		var url = './content/pos_ajax.php?execute=pricechange&barcode='+val+'&newprice='+newcost+sessiontype;
		$.ajax({
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
	}else{
		alert("No item selected...");
	}
}
</script>
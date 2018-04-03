<?php
if($_REQUEST['execute']){
	session_start();
	require_once"../../settings.php";
	require_once"../../class/dbConnection.php";
	require_once"../../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		case'toSession':
			$_SESSION['quotation']['custid']=$_REQUEST['custid'];
			if($_SESSION['quotation']){
				echo "success";
			}
		break;
		case'list':
			if($_REQUEST['search']){
				// $list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
				// left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
				// left join tbl_customers cust on a.cust_id=cust.cust_id",
				// " where a.id like '%{$_REQUEST['search']}%' or a.ponum like '%{$_REQUEST['search']}%' or a.date like '%{$_REQUEST['search']}%'");
				$list = $db->resultArray("a.*,b.total","tbl_quotation_header a left join (select refid,sum(amount) total from tbl_quotation_items group by refid) b on a.id=b.refid"," where a.reference_title like '%{$_REQUEST['search']}%' or a.agreement like '%{$_REQUEST['search']}%'");
			}else{
				$list = $db->resultArray("a.*,b.total","tbl_quotation_header a left join (select refid,sum(amount) total from tbl_quotation_items group by refid) b on a.id=b.refid"," where a.cust_id='{$_REQUEST['custid']}'");
			}
			
		?>
			<input type="text" name="search" id="search" style="float:left;width:85%;" />
			<input type="button" style="float;right;width:10%;margin-left:5px;" value="Search" onclick="search();"/>
			<div style="clear:both;height:5px;"></div>
			<table class="navigateableMain" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;">Quotation #</th>
						<th style="border:none;">Date</th>
						<th style="border:none;">Amount</th>
						<th style="border:none;">Reference Title</th>
						<th style="border:none;">Menu</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($list as $key => $val){ ?>
						<tr>
							<td><a href="?page=quotation&refid=<?=$val['id']?>"><?=$val['id']?></a></td>
							<td style="text-align:center;"><?=$val['date']?></td>
							<td style="text-align:right;"><?=number_format($val['total'],2)?></td>
							<td style="text-align:right;"><?=$val['reference_title']?></td>
							<td style="text-align:center;"><a href="./reports/quotation.php?refid=<?=$val['id']?>" target="_blank"><img src="./images/print.png" style="width:25px;height:25px;" title="Print"/></a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?
		break;
	}
}else{
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		mysql_query("BEGIN");
		// $sql="insert into tbl_quotation_header (id,cust_id,date,validity_of_offer,payment_terms,delivery,rfqnum,footer1,footer2,footer3,attn) values 
			// ('{$_REQUEST['refid']}','{$_REQUEST['business_name']}','{$_REQUEST['info']['date']}','{$_REQUEST['info']['validity_of_offer']}','{$_REQUEST['info']['payment_terms']}','{$_REQUEST['info']['delivery']}','{$_REQUEST['info']['rfqnum']}','{$_REQUEST['info']['footer1']}','{$_REQUEST['info']['footer2']}','{$_REQUEST['info']['footer3']}',
			// '{$_REQUEST['info']['attn']}') 
			// on duplicate key update  attn=values(attn),footer1=values(footer1),footer2=values(footer2),footer3=values(footer3),date=values(date),validity_of_offer=values(validity_of_offer),
			// payment_terms=values(payment_terms),delivery=values(delivery),rfqnum=values(rfqnum)";
		$data=array(
			'id'=>"{$_REQUEST['refid']}",
			'cust_id'=>"{$_REQUEST['business_name']}",
			'date'=>"{$_REQUEST['info']['date']}",
			'validity_of_offer'=>"{$_REQUEST['info']['validity_of_offer']}",
			'payment_terms'=>"{$_REQUEST['info']['payment_terms']}",
			'delivery'=>"{$_REQUEST['info']['delivery']}",
			'rfqnum'=>"{$_REQUEST['info']['rfqnum']}",
			'footer1'=>"{$_REQUEST['info']['footer1']}",
			'footer2'=>"{$_REQUEST['info']['footer2']}",
			'footer3'=>"{$_REQUEST['info']['footer3']}",
			'attn'=>"{$_REQUEST['info']['attn']}",
			'reference_title'=>"{$_REQUEST['info']['reference_title']}",
			'vat_status'=>"{$_REQUEST['info']['vat_status']}",
			'agreement'=>"{$_REQUEST['agreement']}"
		);
		$sql = $db->genSqlInsert($data,'tbl_quotation_header');
		$qry1 = mysql_query($sql);
		if($_REQUEST['items']){
			if($qry1){
				$refid=$_REQUEST['refid']?$_REQUEST['refid']:mysql_insert_id();
				$flag=false;
				$sql2="insert into tbl_quotation_items (id,refid,item_spec,qty,unit,unitprice,amount) values ";
				foreach($_REQUEST['items'] as $key=>$val){
					if($flag)$sql2.=",";
					$sql2.="('{$val['id']}','{$refid}','".mysql_real_escape_string($val['items_spec'])."','{$val['qty']}','".mysql_real_escape_string($val['unit'])."','".preg_replace("/[^0-9.-]/", "", $val['unit_price'])."','".preg_replace("/[^0-9.-]/", "", $val['amount'])."')";
					$flag=true;
				}
				$sql2.=" on duplicate key update item_spec=values(item_spec),qty=values(qty),unit=values(unit),unitprice=values(unitprice),amount=values(amount)";
				$qry2=mysql_query($sql2);
				if(!$qry2){
					echo mysql_error();
				}
			}
			if($qry1 && $qry2){
				mysql_query("COMMIT");
				echo "<script>$(document).ready(function(){alertMsg('Successfully Save...');});</script>";
			}else{
				mysql_query("ROLLBACK");
				echo mysql_error();
			}
		}else{
			if($qry1){
				mysql_query("COMMIT");
				echo "<script>$(document).ready(function(){alertMsg('Successfully Save...');});</script>";
			}else{
				mysql_query("ROLLBACK");
				echo mysql_error();
			}
		}
	}
$info = $db->getWHERE("*","tbl_customers","where cust_id='".$_SESSION['quotation']['custid']."'");
if($_REQUEST['refid']){
	$header = $db->getWHERE("*","tbl_quotation_header","where id='".$_REQUEST['refid']."'");
	$items = $db->resultArray("*","tbl_quotation_items","where refid='".$_REQUEST['refid']."'");
}
?>
<div class="content" style="min-height:300px;width:100%!important;">
	<h2>Quotation Creation</h2>
	<form name="frminfo" id="frminfo" method="post">
	<div style="float:left;margin-right:5px;width:55%;">
		<div style="float:left;margin-right:5px;width:100px;">Business Name:</div>
		<div style="width:500px;float:left;">
			<select  name="business_name" id="business_name" data-placeholder="Choose Customer Name..." class="chosen-select"  tabindex="1">
				<option value=""></option>
				<?
				$qry =mysql_query("select * from tbl_customers");
				while($row=mysql_fetch_assoc($qry)){
					echo "<option ".($refid==$row['cust_id']?"selected":"")." value='{$row['cust_id']}'>{$row['customer_name']}</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div style="clear:both;height:10px;"></div>
		<div style="width:25%;float:left;">
			<fieldset>
				<legend>Customer Information</legend>
				<div style="width:150px;float:left;">Address</div>
				<input value="<?=$info['customer_address']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Telephone Number</div>
				<input value="<?=$info['contact_number']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Fax Number</div>
				<input value="<?=$info['fax_person']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Contact Person</div>
				<input value="<?=$header['attn']?$header['attn']:$info['contact_person']?>" type="text" name="info[attn]" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">TIN</div>
				<input value="<?=$info['tin']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset>
				<legend>Details</legend>
					<input type="hidden" name="refid" value="<?=$header['id']?>"/>
					<div style="width:150px;float:left;">Date</div>
					<input value="<?=$header['date']?>" type="text" name="info[date]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Validity of Offer</div>
					<input value="<?=$header['validity_of_offer']?>" type="text" name="info[validity_of_offer]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Payment Terms</div>
					<input value="<?=$header['payment_terms']?>" type="text" name="info[payment_terms]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Delivery</div>
					<input value="<?=$header['delivery']?>" type="text" name="info[delivery]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">RFQ No.</div>
					<input value="<?=$header['rfqnum']?>" type="text" name="info[rfqnum]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Reference Title</div>
					<input value="<?=$header['reference_title']?>" type="text" name="info[reference_title]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">VAT %</div>
					<input value="<?=$header['vat_status']?>" type="text" name="info[vat_status]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="button" value="SAVE" onclick="save()" style="float:left;height:30px;width:130px;"/>
			</fieldset>
			<fieldset>
				<legend>Very truly yours,</legend>
					<input value="<?=$header['footer1']?$header['footer1']:"JOVEN E. RAMIRO"?>" type="text" name="info[footer1]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<input value="<?=$header['footer2']?$header['footer2']:"Marketing Manager"?>" type="text" name="info[footer2]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<input value="<?=$header['footer3']?$header['footer3']:"H/P 0922-854-4608"?>" type="text" name="info[footer3]" style="width:300px;"/>
			</fieldset>
		</div>
		<div style="width:70%;float:right;">
			<fieldset style="float:left;width:98%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="button" value="Quotation List" onclick="viewlist()" style="float:right;height:40px;width:150px;float:left;"/>
				<a href="?page=quotation"><input id="bt8" class="buthov" type="button" value="New Quotation" style="float:right;height:40px;width:150px;float:left;"/></a>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<legend>Item Details <input type="button" value="+" id="items" style="width:20px;height:20px;"/></legend>
					<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;">&nbsp;</th>
								<th style="border:none;width:250px;">Item/Specification</th>
								<th style="border:none;">Qty</th>
								<th style="border:none;">Unit</th>
								<th style="border:none;">Unit Price</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php
						if($items){
							$count=0;
							$subtotal=0;
							foreach($items as $key => $val){
								echo '<tr>
									<td><input type="checkbox" ><input type="hidden" name="items['.$count.'][id]" value="'.$val['id'].'"/></td>
									<td><input type="text" name="items['.$count.'][items_spec]" value="'.$val['item_spec'].'" style="width:100%;"/></td>
									<td><input type="text" class="qty" name="items['.$count.'][qty]" value="'.$val['qty'].'" style="width:100%;"/></td>
									<td><input type="text" name="items['.$count.'][unit]" value="'.$val['unit'].'" style="width:100%;"/></td>
									<td><input type="text" class="unitprice" name="items['.$count.'][unit_price]" value="'.$val['unitprice'].'" style="width:100%;"/></td>
									<td><input type="text" class="amt" name="items['.$count.'][amount]" style="width:100%;text-align:right;" value="'.$val['amount'].'"/></td>
								</tr>';
								$count++;
								$subtotal+=$val['amount'];
							}
						}
						?>
						</tbody>
						<tfoot>
							<th colspan="5">Total</th>
							<th><?=number_format($subtotal,2)?></th>
						</tfoot>
					</table>
				</fieldset>
			</div>
			<div style="clear:both;height:5px;"></div>
			<textarea style="width:100%;height:300px;" name="agreement"><?=$header['agreement']?></textarea>
		</div>
	</form>
</div>
<script>

$(document).ready(function() {
	tinymce.init({ selector:'textarea' });
	$("#business_name").val(<?=$_SESSION['quotation']['custid']?>);
	var config = {
	  '.chosen-select'           : {width: "80%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		toSession();
	});
	$('input[name="info[date]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	setChanges();
});
function search(){
	var custid = $("#business_name").val();
	var search = $("#search").val();
	clickDialogUrl("dialogbox3",900,500,'./content/rber/quotation.php?execute=list&custid='+custid+'&search='+search,"Listing");
}
function toSession(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/rber/quotation.php?execute=toSession&custid='+custid,
			type:"POST",
			success:function(data){
				window.location=removeURLParameter(document.URL, 'refid');
			}
		});
	}
}
function viewlist(){
	var custid = $("#business_name").val();
	if(custid!=""){
		clickDialogUrl("dialogbox3",900,500,'./content/rber/quotation.php?execute=list&custid='+custid,"Listing");
	}
}
$("input[value='+']").on('click',function(){
	var id = $(this).attr('id');
	var num =$("#"+id+"_tbl tbody tr").length;
	switch(id){
		case'items':
			var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="items['+num+'][items_spec]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="qty" name="items['+num+'][qty]" value="" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][unit]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="unitprice" name="items['+num+'][unit_price]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="amt" name="items['+num+'][amount]" style="width:100%;text-align:right;" value="0.00"/></td>\
				</tr>';
		break;
	}
	$("#"+id+"_tbl tbody").prepend(txt);
	setChanges();
	
});
function setChanges(){
	$(".qty, .unitprice").on("change paste keyup",function(){
		var tr = $(this).closest("tr");
		var qty = strtodouble(tr.find(".qty").val());
		var price = strtodouble(tr.find(".unitprice").val());
		var total = strtocurrency(qty*price);
		tr.find(".amt").val(total);
		var subtotal = sumName(".amt");
		$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
	});
}
function sumName(name){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(name).each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return sum;
}
$("#items_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		$('input[type="checkbox"]:checked').closest("tr").remove();
	}
});
function save(){
	$("#frminfo").submit();
}
</script>
<?php } ?>
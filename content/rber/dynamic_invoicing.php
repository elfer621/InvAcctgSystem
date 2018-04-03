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
		case'createNew':
			unset($_SESSION['dynamic_invoicing']['custid']);
		break;
		case'getStockOut':
			$list = $db->resultArray("*","tbl_stockout_items","where stockin_refid='{$_REQUEST['stockoutref']}'");
			echo '{"rec":'. json_encode($list) .',"msg":"success"}';
		break;
		case'toSession':
			$_SESSION['dynamic_invoicing']['custid']=$_REQUEST['custid'];
			if($_SESSION['dynamic_invoicing']){
				echo "success";
			}
		break;
		case'list':
			if($_REQUEST['search']||$_REQUEST['date']){
				$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
				left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
				left join tbl_customers cust on a.cust_id=cust.cust_id",
				" where a.id like '%{$_REQUEST['search']}%' or a.ponum like '%{$_REQUEST['search']}%' or a.date >= '{$_REQUEST['date']}'");
			}else{
				$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
				left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
				left join tbl_customers cust on a.cust_id=cust.cust_id",
				" where a.cust_id='{$_REQUEST['custid']}'");
			}
			
		?>
			
			<input type="text" name="date" style="width:100px;float:left;margin-right:10px;" value="<?=date('Y-m-d')?>"/>
			<input type="text" name="search" id="search" style="float:left;width:75%;" />
			<input type="button" style="float;right;width:10%;margin-left:5px;" value="Search" onclick="search();"/>
			<div style="clear:both;height:5px;"></div>
			<table class="navigateableMain" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;">Ref #</th>
						<th style="border:none;">Customer Name</th>
						<th style="border:none;">Date</th>
						<th style="border:none;">Amount</th>
						<th style="border:none;">Prepared By</th>
						<th style="border:none;">Menu</th>
					</tr>
				</thead>
				<tbody style="font-size:10px;">
					<?php foreach($list as $key => $val){ ?>
						<tr>
							<td><a href="?page=dynamic_invoicing&tbltype=<?=$_REQUEST['tbltype']?>&refid=<?=$val['id']?>"><?=$val['id']?></a></td>
							<td style="text-align:left;"><?=$val['customer_name']?></td>
							<td style="text-align:center;"><?=$val['date']?></td>
							<td style="text-align:right;"><?=number_format($val['total'],2)?></td>
							<td style="text-align:center;"><?=$val['preparedby']?></td>
							<td style="text-align:center;"><a href="./reports/dynamic_invoicing.php?refid=<?=$val['id']?>&tbltype=<?=$_REQUEST['tbltype']?>" target="_blank"><img src="./images/print.png" style="width:25px;height:25px;" title="Print"/></a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<script>
			$(document).ready(function() {
				$('input[name="date"]').datepicker({
					changeMonth: true,
					changeYear: true,
					inline: true,
					dateFormat:"yy-mm-dd"
				});
			});
			</script>
		<?
		break;
	}
}else{
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		mysql_query("BEGIN");
		// $sql="insert into tbl_{$_REQUEST['tbltype']}_header (id,cust_id,date,payment_terms,ponum,regnum) values 
			// ('{$_REQUEST['refid']}','{$_REQUEST['business_name']}','{$_REQUEST['info']['date']}','{$_REQUEST['info']['payment_terms']}','{$_REQUEST['info']['ponum']}','{$_REQUEST['info']['regnum']}') 
			// on duplicate key update  date=values(date),payment_terms=values(payment_terms),ponum=values(ponum),regnum=values(regnum)";
		$data=array(
				'id'=>$_REQUEST['refid'],
				'cust_id'=>$_REQUEST['business_name'],
				'date'=>$_REQUEST['info']['date'],
				'payment_terms'=>$_REQUEST['info']['payment_terms'],
				'ponum'=>$_REQUEST['info']['ponum'],
				'regnum'=>$_REQUEST['info']['regnum'],
				'taxtype'=>$_REQUEST['taxtype'],
				'agreement'=>$_REQUEST['agreement'],
				'preparedby'=>$_SESSION['complete_name']
			);
		$sql = $db->genSqlInsert($data,"tbl_{$_REQUEST['tbltype']}_header");
		$qry1 = mysql_query($sql);
		
		if($qry1){
			$refid=$_REQUEST['refid']?$_REQUEST['refid']:mysql_insert_id();
			$flag=false;
			$sql2="insert into tbl_{$_REQUEST['tbltype']}_items (id,refid,item_spec,qty,unit,unitprice,amount) values ";
			$xtotal=0;
			foreach($_REQUEST['items'] as $key=>$val){
				if($flag)$sql2.=",";
				$sql2.="('{$val['id']}','{$refid}','".mysql_real_escape_string($val['items_spec'])."','{$val['qty']}','".mysql_real_escape_string($val['unit'])."','".preg_replace("/[^0-9.-]/", "", $val['unit_price'])."','".preg_replace("/[^0-9.-]/", "", $val['amount'])."')";
				$flag=true;
				$xtotal+=preg_replace("/[^0-9.-]/", "", $val['amount']);
			}
			$sql2.=" on duplicate key update item_spec=values(item_spec),qty=values(qty),unit=values(unit),unitprice=values(unitprice),amount=values(amount)";
			$qry2=mysql_query($sql2);
			if(!$qry2){
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
		if($qry1 && $qry2){
			mysql_query("COMMIT");
			//Insert Customer Transaction
			$data=array(
				'cust_id'=>$_REQUEST['business_name'],
				'receipt'=>$refid,
				'date'=>$_REQUEST['info']['date'],
				'transtype'=>$_REQUEST['tbltype'],
				'amount'=>str_replace(",","",$xtotal),
				'details'=>strtoupper(str_replace("_"," ",$_REQUEST['tbltype']))." ".$refid
			);
			$sql_cust_trans = $db->genSqlInsert($data,'tbl_customers_trans');
			//echo $sql; exit;
			$qry_cust_trans = mysql_query($sql_cust_trans);
			if(!$qry_cust_trans){
				echo "Error CustTrans:".mysql_error()."<br/>";
			}
			//Insert Customer Transaction
			echo "<script>$(document).ready(function(){alertMsg('Successfully Save...');});</script>";
		}else{
			mysql_query("ROLLBACK");
			echo mysql_error();
		}
	}

if($_REQUEST['refid']){
	$header = $db->getWHERE("*","tbl_{$_REQUEST['tbltype']}_header","where id='".$_REQUEST['refid']."'");
	$items = $db->resultArray("*","tbl_{$_REQUEST['tbltype']}_items","where refid='".$_REQUEST['refid']."'");
	$_SESSION['dynamic_invoicing']['custid']=$header['cust_id'];
}
$info = $db->getWHERE("*","tbl_customers","where cust_id='".($header['cust_id']?$header['cust_id']:$_SESSION['dynamic_invoicing']['custid'])."'");
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2><?=strtoupper(str_replace("_"," ",$_REQUEST['tbltype']))?> CREATION</h2>
	<form name="frminfo" id="frminfo" method="post">
	<div style="float:left;margin-right:5px;width:55%;">
		<div style="float:left;margin-right:5px;width:100px;">Business Name:</div>
		<div style="width:500px;float:left;">
			<select  name="business_name" id="business_name" data-placeholder="Choose Customer Name..." class="chosen-select"  tabindex="1">
				<option value=""></option>
				<?
				$qry =mysql_query("select * from tbl_customers order by customer_name asc");
				while($row=mysql_fetch_assoc($qry)){
					echo "<option ".($header['cust_id']==$row['cust_id']?"selected":"")." value='{$row['cust_id']}'>{$row['customer_name']}</option>";
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
					<div style="width:150px;float:left;">Ref ID</div>
					<input type="text" name="refid" value="<?=$header['id']?>" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Date</div>
					<input value="<?=$header['date']?>" type="text" name="info[date]" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">PO#</div>
					<input value="<?=$header['ponum']?>" type="text" name="info[ponum]" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Reg No.</div>
					<input value="<?=$header['regnum']?>" type="text" name="info[regnum]" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Payment Terms</div>
					<input value="<?=$header['payment_terms']?>" type="text" name="info[payment_terms]" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="button" value="SAVE" onclick="save()" style="float:left;height:30px;width:130px;"/>
			</fieldset>
			<fieldset>
				<legend>Tax Type</legend>
				<input type="radio" name="taxtype" value="vatable" <?=$header['taxtype']=="vatable"?'checked="checked"':""?>/> VATable Sales </br>
				<input type="radio" name="taxtype" value="vatexempt" <?=$header['taxtype']=="vatexempt"?'checked="checked"':""?>/> VAT - Exempt Sales </br>
				<input type="radio" name="taxtype" value="zerorated" <?=$header['taxtype']=="zerorated"?'checked="checked"':""?>/> Zero Rated Sales </br>
			</fieldset>
		</div>
		<div style="width:70%;float:right;">
			<fieldset style="float:left;width:95%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="button" value="List" onclick="viewlist()" style="float:right;height:40px;width:150px;float:left;"/>
				<input id="bt8" class="buthov" type="button" value="New" style="float:right;height:40px;width:150px;float:left;" onclick="createNew()"/>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<legend>Item Details 
						<input type="button" value="+" id="items" style="width:20px;height:20px;"/>
						<input type="text" name="stockoutref" id="stockoutref" style="float:left;width:80px;height:15px;float:right;margin-left:10px;"/>
						<input type="button" value="OUT" id="items" style="width:40px;height:20px;float:right;margin-left:10px;" onclick="getStockOut()"/>
					</legend>
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
						}else{
							if($_REQUEST['tbltype']=="billing_statement"){
								$count=1;
								echo '<tr>
									<td><input type="checkbox" ><input type="hidden" name="items['.$count.'][id]" value=""/></td>
									<td><input type="text" name="items['.$count.'][items_spec]" value="SERVICES BILL" style="width:100%;"/></td>
									<td><input type="text" class="qty" name="items['.$count.'][qty]" value="1" style="width:100%;"/></td>
									<td><input type="text" name="items['.$count.'][unit]" value="JOB" style="width:100%;"/></td>
									<td><input type="text" class="unitprice" name="items['.$count.'][unit_price]" value="0" style="width:100%;"/></td>
									<td><input type="text" class="amt" name="items['.$count.'][amount]" style="width:100%;text-align:right;" value="0"/></td>
								</tr>';
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
	$("#business_name").val(<?=$_SESSION['dynamic_invoicing']['custid']?>);
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
function getStockOut(){
	var ref = $("#stockoutref").val();
	$.ajax({
		url: './content/rber/dynamic_invoicing.php?execute=getStockOut&stockoutref='+ref,
		type:"POST",
		dataType: 'json',
		success:function(data){
			//console.log(data.rec);
			$.each(data.rec, function(k, v) {
				var num =$("#items_tbl tbody tr").length;
				var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="items['+num+'][items_spec]" value="'+v['item_desc']+'" style="width:100%;"/></td>\
					<td><input type="text" class="qty" name="items['+num+'][qty]" value="'+v['qty']+'" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][unit]" value="'+v['unit']+'" style="width:100%;"/></td>\
					<td><input type="text" class="unitprice" name="items['+num+'][unit_price]" value="'+v['selling']+'" style="width:100%;"/></td>\
					<td><input type="text" class="amt" name="items['+num+'][amount]" style="width:100%;text-align:right;" value="'+v['total']+'"/></td>\
				</tr>';
				$("#items_tbl tbody").prepend(txt);
			});
			var subtotal = sumName(".amt");
			$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
		}
	});
}
function toSession(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/rber/dynamic_invoicing.php?execute=toSession&custid='+custid,
			type:"POST",
			success:function(data){
				window.location=removeURLParameter(document.URL, 'refid');
			}
		});
	}
}
function createNew(){
	$.ajax({
		url: './content/rber/dynamic_invoicing.php?execute=createNew',
		type:"POST",
		success:function(data){
			window.location=removeURLParameter(document.URL, 'refid');
		}
	});
}
function viewlist(){
	var custid = $("#business_name").val();
	clickDialogUrl("dialogbox3",900,500,'./content/rber/dynamic_invoicing.php?tbltype='+getParam('tbltype')+'&execute=list&custid='+custid,"Listing");
}
function search(){
	var search = $("#search").val();
	var date = $("input[name='date']").val();
	clickDialogUrl("dialogbox3",900,500,'./content/rber/dynamic_invoicing.php?tbltype='+getParam('tbltype')+'&execute=list&search='+search+'&date='+date,"Listing");
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
	if($("input[name='refid']").val()==""){
		alert("Reference Number is required...");
		return false;
	}else{
		$("#frminfo").submit();
	}
}
</script>
<?php } ?>
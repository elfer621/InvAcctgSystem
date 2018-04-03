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
		case'del':
			$sql="delete from tbl_soa_other_charges_daily where id='{$_REQUEST['id']}'";
			$qry=mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
		case'edit':
			$info = $db->getWHERE("*","tbl_soa_other_charges_daily","where id='".$_REQUEST['id']."'");
			echo '{"rec":'. json_encode($info) .'}';
		break;
		case'toSession':
			$_SESSION['otherchargesdaily']['custid']=$_REQUEST['custid'];
			$_SESSION['otherchargesdaily']['forthemonth']=$_REQUEST['forthemonth'];
			if($_SESSION['otherchargesdaily']){
				echo "success";
			}
		break;
		case'catInfo':
			$info = $db->getWHERE("*","tbl_soa_other_charges_category_name","where category_id='".$_REQUEST['catid']."'");
			if($info['persqmcharging']=='Y'){
				$cust = $db->getWHERE("floor_area_sqm","tbl_customers_contract","where custid='".$_REQUEST['custid']."'");
				$info['unit']="hr x ({$info['rate']} x {$cust['floor_area_sqm']}sqm)";
				$info['rate']=$info['rate']*$cust['floor_area_sqm'];
			}
			echo '{"rec":'. json_encode($info) .'}';
		break;
		case'newcatadd':
			$sql="insert into tbl_soa_other_charges_category_name (`Description`,`unit`,`rate`,`persqmcharging`) values ('{$_REQUEST['categoryname']}','{$_REQUEST['unit']}','{$_REQUEST['rate']}','{$_REQUEST['persqmcharging']}')";
			$qry = mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
		case'newcategory':
			?>
			<form name="frmnewcat" id="frmnewcat">
				<div style="float:left;width:150px;">Category Name</div>
				<input type="text" name="categoryname" style="float:right;width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:150px;">Default Rate</div>
				<input type="text" name="rate" style="float:right;width:145px;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:150px;">Default Unit</div>
				<input type="text" name="unit" style="float:right;width:145px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:150px;">Per Sqm Charging</div>
				<select name="persqmcharging" style="float:right;width:145px;">
					<option value="N">No</option>
					<option value="Y">Yes</option>
				</select>
				<div style="clear:both;height:25px;"></div>
				<input type="button" value="Save" onclick="saveNewCat()" style="height:30px;width:150px;float:right;"/>
			</form>
			<?
		break;
		case'saveCharges':
			// echo "<pre>";
			// print_r($_REQUEST);
			// echo "</pre>";
			$sql="insert into tbl_soa_other_charges_daily (id,custid,forthemonth,category_id,date,reference,qty,unit,rate,amount,stime) value ";
			$sql.="('{$_REQUEST['charges']['id']}','{$_REQUEST['custid']}','{$_REQUEST['forthemonth']}','{$_REQUEST['charges']['category_id']}','{$_REQUEST['charges']['date']}','{$_REQUEST['charges']['reference']}','{$_REQUEST['charges']['qty']}','{$_REQUEST['charges']['unit']}','{$_REQUEST['charges']['rate']}','{$_REQUEST['charges']['amount']}','{$_REQUEST['charges']['stime']}')";
			$qry=mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
		case'chargeslist':
			$rec = $db->resultArray("a.*,b.Description","tbl_soa_other_charges_daily a left join tbl_soa_other_charges_category_name b on a.category_id=b.category_id","where custid='{$_REQUEST['custid']}' and forthemonth='{$_REQUEST['forthemonth']}'");
			foreach($rec as $key => $val){
				$list .= "<tr>
					<td>".$val['Description']."</td>
					<td style='text-align:right;'>".number_format($val['amount'],2)."</td>
					<td>
						<input type='button' value='Edit' onclick='edit(".$val['id'].")' style='height:20px;width:50px;'/>
						<input type='button' value='Del' onclick='del(".$val['id'].")' style='height:20px;width:50px;'/>
					</td>
				</tr>";
			}
			echo $list;
		break;
	}
}else{
$cat = $db->resultArray("*","tbl_soa_other_charges_category_name","");	
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2>Other Charges Daily</h2>
	<div style="float:left;margin-right:5px;width:55%;">
		<div style="float:left;margin-right:5px;width:100px;">For the Month:</div>
		<input type="text" name="forthemonth" id="forthemonth" style="width:200px;"/>
		<div style="clear:both;height:10px;"></div>
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
	<div style="width:45%;float:left;">
		<fieldset>
			<legend>Details</legend>
			<form name="frmpdc" id="frmpdc">
				<div style="width:150px;float:left;">Charge Category</div>
				<select name="charges[category_id]" style="width:300px;">
					<option value=''>Select Option</option>
				<?php
					foreach($cat as $key => $val){
						echo "<option value='".$val['category_id']."'>".$val['Description']."</option>";
					}
				?>
				</select>
				<input type="hidden" name="charges[id]" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Date</div>
				<input type="text" name="charges[date]" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Reference</div>
				<input type="text" name="charges[reference]" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">TIME</div>
				<input type="text" name="charges[stime]" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Qty</div>
				<input type="text" name="charges[qty]" style="width:300px;" value="1"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Unit</div>
				<input type="text" name="charges[unit]" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Rate</div>
				<input type="text" name="charges[rate]" style="width:300px;" readonly />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Amount</div>
				<input type="text" name="charges[amount]" style="width:300px;" readonly />
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="SAVE" onclick="saveCharges()" style="float:left;height:30px;width:130px;"/>
			</form>
		</fieldset>
	</div>
	<div style="width:45%;float:right;">
		<fieldset>
			<legend>Charges Summary</legend>
			<table id="chargeslist" style="width:100%;border:1px solid gray;">
				<thead>
					<tr>
						<td>Charge Description</td>
						<td>Amount</td>
						<td>Menu</td>
					</tr>
				</thead>
				<tbody style="border:1px solid gray;"></tbody>
			</table>
		</fieldset>
	</div>
	<div style="clear:both;height:10px;"></div>
	<fieldset style="float:left;width:100%;">
		<legend>Menu</legend>
		<input id="bt7" class="buthov" type="button" value="New Category" onclick="newCat()" style="float:right;height:40px;width:130px;float:left;"/>
		<a href="./?page=dynamictbl&tblname=tbl_soa_other_charges_category_name"><input id="bt9" class="buthov" type="button" value="Edit/Del Cat" style="float:right;height:40px;width:130px;float:left;"/></a>
		<input id="bt8" class="buthov" type="button" value="Report Details" onclick="viewDetails()" style="float:right;height:40px;width:130px;float:left;"/>
		<input id="bt10" class="buthov" type="button" value="Security Report" onclick="viewSecPosting()" style="float:right;height:40px;width:200px;float:left;"/>
	</fieldset>
</div>
<script>

$(document).ready(function() {
	$("#business_name").val(<?=$_SESSION['otherchargesdaily']['custid']?>);
	$("#forthemonth").val('<?=$_SESSION['otherchargesdaily']['forthemonth']?>');
	var config = {
	  '.chosen-select'           : {width: "80%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		toSession();
	});
	$('input[name="charges[date]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$(function(){   
		$('input[name="forthemonth"]').datepicker({
			dateFormat: 'yy-mm',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			onClose: function(dateText, inst) {
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
				toSession();
			}
		});
		$('input[name="forthemonth"]').focus(function () {
			$(".ui-datepicker-calendar").hide();
			$("#ui-datepicker-div").position({
				my: "center top",
				at: "center bottom",
				of: $(this)
			});
		});
	});
	viewRec();
});
function del(id){
	$.ajax({
		url: './content/ccrc/othercharges.php?execute=del&id='+id,
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alert(data);
			}
		}
	});
}
function edit(id){
	$.ajax({
		url: './content/ccrc/othercharges.php?execute=edit&id='+id,
		type:"POST",
		dataType:"json",
		success:function(data){
			var res = data.rec;
			$('input[name="charges[id]"]').val(res.id);
			$('select[name="charges[category_id]"]').val(res.category_id);
			$('input[name="charges[date]"]').val(res.date);
			$('input[name="charges[reference]"]').val(res.reference);
			$('input[name="charges[stime]"]').val(res.stime);
			$('input[name="charges[qty]"]').val(res.qty);
			$('input[name="charges[unit]"]').val(res.unit);
			$('input[name="charges[rate]"]').val(res.rate);
			$('input[name="charges[amount]"]').val(res.amount);
		}
	});
}
function viewDetails(){
	var custid = $("#business_name").val();
	var forthemonth = $("#forthemonth").val();
	window.open('./reports/otherchargesdaily.php?custid='+custid+'&forthemonth='+forthemonth,'_blank');
}
$("select[name='charges[category_id]']").on("change paste keyup",function(){
	var custid = $("#business_name").val();
	$.ajax({
		url: './content/ccrc/othercharges.php?execute=catInfo&catid='+$(this).val()+'&custid='+custid,
		type:"POST",
		dataType:"json",
		success:function(data){
			var res = data.rec;
			$("input[name='charges[rate]']").val(res.rate);
			$("input[name='charges[unit]']").val(res.unit);
		}
	});
});
$("input[name='charges[qty]']").on("change paste keyup",function(){
	var qty = $("input[name='charges[qty]']").val();
	var rate = $("input[name='charges[rate]']").val();
	$("input[name='charges[amount]']").val(rate * qty);
});
function toSession(){
	var custid = $("#business_name").val();
	var forthemonth = $("#forthemonth").val();
	if(custid!=""&&forthemonth!=""){
		$.ajax({
			url: './content/ccrc/othercharges.php?execute=toSession&custid='+custid+'&forthemonth='+forthemonth,
			type:"POST",
			success:function(data){
				viewRec();
			}
		});
	}
}
function viewRec(){
	var custid = $("#business_name").val();
	var forthemonth = $("#forthemonth").val();
	if(custid!=""&&forthemonth!=""){
		$.ajax({
			url: './content/ccrc/othercharges.php?execute=chargeslist&custid='+custid+'&forthemonth='+forthemonth,
			type:"POST",
			success:function(data){
				$("#chargeslist tbody").html(data);
			}
		});
	}
}
function newCat(){
	clickDialog('dialogbox2',500,250,'newcategory','New Category',null,'ccrc/othercharges.php');
}
function saveNewCat(){
	var datastring = $("#frmnewcat").serialize();
	$.ajax({
		url: './content/ccrc/othercharges.php?execute=newcatadd',
		data:datastring,
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alert(data);
			}
		}
	});
	
}
function saveCharges(){
	var datastring = $("#frmpdc").serialize();
	var custid = $("#business_name").val();
	var forthemonth = $("#forthemonth").val();
	if(custid!=""&&forthemonth!=""){
		$.ajax({
			url: './content/ccrc/othercharges.php?execute=saveCharges&custid='+custid+'&forthemonth='+forthemonth,
			data:datastring,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location=document.URL;
				}else{
					alert(data);
				}
			}
		});
	}else{
		alert("Pls select business name or For the Month...");
	}
}
function viewSecPosting(){
	var page = './reports/security_posting_report.php?forthemonth='+$("#forthemonth").val();
	var win=window.open(page,'_blank');
	win.focus();
}
</script>
<?php } ?>
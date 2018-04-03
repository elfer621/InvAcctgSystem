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
			$_SESSION['pdcinput']['custid']=$_REQUEST['custid'];
			if($_SESSION['pdcinput']){
				echo "success";
			}
		break;
		case'delPDC':
			$sql="delete from tbl_soa_pdclist where id='{$_REQUEST['id']}'";
			$qry=mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
		case'savePDC':
			// echo "<pre>";
			// print_r($_REQUEST);
			// echo "</pre>";
			$sql="insert into tbl_soa_pdclist (id,custid,monthrent,checkdate,bank,checknum,amount) value ";
			$sql.="('{$_REQUEST['pdc']['id']}','{$_REQUEST['custid']}','{$_REQUEST['pdc']['monthrent']}','{$_REQUEST['pdc']['checkdate']}','{$_REQUEST['pdc']['bank']}','{$_REQUEST['pdc']['checknum']}','{$_REQUEST['pdc']['amount']}') 
			on duplicate key update monthrent=values(monthrent),checkdate=values(checkdate),bank=values(bank),checknum=values(checknum),amount=values(amount)";
			$qry=mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		break;
		case'editPDC':
			$info = $db->getWHERE("*","tbl_soa_pdclist","where id='".$_REQUEST['id']."'");
			echo '{"rec":'. json_encode($info) .'}';
		break;
		case'pdclist':
			$rec = $db->resultArray("*","tbl_soa_pdclist","where custid='{$_REQUEST['custid']}'");
			foreach($rec as $key => $val){
				$list .= "<tr>
					<td>".$val['monthrent']."</td>
					<td>".$val['checkdate']."</td>
					<td>".$val['bank']."</td>
					<td>".$val['checknum']."</td>
					<td>".number_format($val['amount'],2)."</td>
					<td>
						<input type='button' value='Edit' onclick='editPDC(".$val['id'].")' style='height:20px;width:50px;'/>
						<input type='button' value='Del' onclick='delPDC(".$val['id'].")' style='height:20px;width:50px;'/>
					</td>
				</tr>";
			}
			echo $list;
		break;
	}
}else{
?>
<div class="content" style="min-height:300px;width:100%!important;">
	<h2>PDC Input</h2>
	<div style="float:left;margin-right:5px;">
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
	<fieldset style="float:left;width:40%;">
		<legend>Menu</legend>
		<input onclick="viewRep('./reports/pdc_acknowledgement.php?custid=')" id="bt9" class="buthov" type="button" value="Acknowledgement Receipt" style="float:right;height:40px;width:250px;float:left;"/>
	</fieldset>
	<div style="clear:both;height:10px;"></div>
	<div style="width:45%;float:left;">
		<fieldset>
			<legend>PDC Details</legend>
			<form name="frmpdc" id="frmpdc">
				<input type="hidden" name="pdc[id]"/>
				<div style="width:150px;float:left;">For MonthRent</div>
				<input type="text" name="pdc[monthrent]" style="width:200px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Check Date</div>
				<input type="text" name="pdc[checkdate]" style="width:200px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Bank</div>
				<input type="text" name="pdc[bank]" style="width:200px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Check Number</div>
				<input type="text" name="pdc[checknum]" style="width:200px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Amount</div>
				<input type="text" name="pdc[amount]" style="width:200px;"/>
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="SAVE" onclick="savePDC()" style="float:left;height:30px;width:130px;"/>
			</form>
		</fieldset>
	</div>
	<div style="width:45%;float:right;">
		<fieldset>
			<legend>PDC List</legend>
			<table id="pdclist" style="width:100%;border:1px solid gray;">
				<thead>
					<tr>
						<td>For MonthRent</td>
						<td>Check Date</td>
						<td>Bank</td>
						<td>Check Number</td>
						<td>Amount</td>
						<td>Menu</td>
					</tr>
				</thead>
				<tbody style="border:1px solid gray;"></tbody>
			</table>
		</fieldset>
	</div>
</div>
<script>
$(document).ready(function() {
	$("#business_name").val(<?=$_SESSION['pdcinput']['custid']?>);
	var config = {
	  '.chosen-select'           : {width: "80%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		toSession();
		viewRec();
	});
	$('input[name="pdc[checkdate]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$(function(){   
		$('input[name="pdc[monthrent]"]').datepicker({
			dateFormat: 'yy-mm',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			onClose: function(dateText, inst) {
				var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
				var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
				$(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
			}
		});
		$('input[name="pdc[monthrent]"]').focus(function () {
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
function delPDC(id){
	$.ajax({
		url: './content/ccrc/pdc.php?execute=delPDC&id='+id,
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
function editPDC(id){
	$.ajax({
		url: './content/ccrc/pdc.php?execute=editPDC&id='+id,
		type:"POST",
		dataType:"json",
		success:function(data){
			var res = data.rec;
			$('input[name="pdc[id]"]').val(res.id);
			$('input[name="pdc[monthrent]"]').val(res.monthrent);
			$('input[name="pdc[checkdate]"]').val(res.checkdate);
			$('input[name="pdc[bank]"]').val(res.bank);
			$('input[name="pdc[checknum]"]').val(res.checknum);
			$('input[name="pdc[amount]"]').val(res.amount);
		}
	});
}
function viewRec(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/ccrc/pdc.php?execute=pdclist&custid='+custid,
			type:"POST",
			success:function(data){
				$("#pdclist tbody").html(data);
			}
		});
	}
}
function toSession(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/ccrc/pdc.php?execute=toSession&custid='+custid,
			type:"POST",
			success:function(data){
				viewRec();
			}
		});
	}
}
function savePDC(){
	var datastring = $("#frmpdc").serialize();
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/ccrc/pdc.php?execute=savePDC&custid='+custid,
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
		alert("Pls select business name...");
	}
}
function viewRep(page){
	var win=window.open(page+$("#business_name").val(),'_blank');
	win.focus();
}
</script>
<?php } ?>
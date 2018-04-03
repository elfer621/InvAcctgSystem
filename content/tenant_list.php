<?php
$sql = "select * from tbl_customers order by customer_name asc";
$qry =  mysql_query($sql);
?>
<div style="clear:both;height:10px;"></div>
<div style="float:left;margin-right:10px;padding-top:5px;">Search: </div>
<select style="width:250px;float:left;" name="custname" id="custname" data-placeholder="Choose Customer Name..." class="chosen-select"  tabindex="1">
	<option value=""></option>
	<?
	while($row=mysql_fetch_assoc($qry)){
		echo "<option value='{$row['cust_id']}'>{$row['customer_name']}</option>";
	}
	?>
</select>
<!--input type="button" id="custokbt" value="Ok" onclick="ok()" style="float:right;height:28px;width:150px;"/-->
<div style="clear:both;height:5px;"></div>
<div id="custdetails"></div>
<div style="clear:both;height:5px;"></div>
<fieldset style="position:absolute;bottom:5px;">
	<legend>Menu</legend>
	<input id="bt1" class="buthov" type="button" value="Add Customer" style="float:left;height:40px;width:200px;" onclick="addcustomer()"/>
	<?php if($_SESSION['restrictionid']!=2){ ?>
		<input id="bt2" class="buthov" type="button" value="View Trans" style="float:left;height:40px;width:200px;" onclick="viewTrans()"/>
		<input id="bt3" class="buthov" type="button" value="Debit Memo" style="float:left;height:40px;width:200px;" onclick="adjustment()"/>
	<? } ?>
	<input id="bt4" class="buthov" type="button" value="Collection Encode" style="float:left;height:40px;width:200px;" onclick="payment_received()"/>
	<input id="bt5" class="buthov" type="button" value="Credit Memo (-)" style="float:left;height:40px;width:200px;" onclick="credit_memo()"/>
	<input id="bt1" class="buthov" type="button" value="Collection Reports" style="float:left;height:40px;width:200px;" onclick="viewReport('reports/cust_trans<?=$_SESSION['repExtension']?>.php');"/>
	<input id="bt2" class="buthov" type="button" value="Undelivered Receipt" style="float:left;height:40px;width:200px;" onclick="custTrans_Rep('undelivered_receipt')"/>
	<input id="bt6" class="buthov" type="button" value="SOA" style="float:left;height:40px;width:200px;" onclick="openSOA();"/>
	<!--input id="bt6" class="buthov" type="button" value="Set As Default" style="float:left;height:40px;width:200px;" onclick="setAsdefault()"/-->
	<!--input id="bt6" class="buthov" type="button" value="Work Orders" style="float:left;height:40px;width:160px;" onclick="viewWorkOrders()"/-->
</fieldset>
<!--<link rel="stylesheet" href="./js/chosen/docsupport/style.css">-->
<!--<link rel="stylesheet" href="./js/chosen/docsupport/prism.css">-->
<!--link rel="stylesheet" href="./js/chosen/chosen.css">
<script src="./js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="./js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script-->
<script type="text/javascript">
	var config = {
	  '.chosen-select'           : {width: "40%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		showCustdetails(this.value);
	});
	function showCustdetails(refid){
		$.ajax({
			url: './content/pos_ajax.php?execute=custdetails',
			data:{cust_id:refid},
			type:"POST",
			success:function(data){
				$('#custdetails').html(data);
			}
		});
	}
	function openSOA(){
		var acctid = $('#acctid').html();
		viewReport('./reports/soa<?=$_SESSION['repExtension']?>.php?acctid='+acctid);
	}
	function ok(){
		saveCustToSession($("#custname").val(),$('#custname>option:selected').text());
	}
	function setAsdefault(){
		$.ajax({
			url: './content/pos_ajax.php?execute=setAsdefaultCust',
			data:{cust_id:$("#custname").val(),cust_name:$('#custname>option:selected').text()},
			type:"POST",
			success:function(data){
				if(data=="success"){
					alert($("#custname").val()+":"+$('#custname>option:selected').text()+" set as default...");
				}else{
					alert(data);
				}
			}
		});
	}
	function saveCustToSession(cust_id,cust_name){
		$.ajax({
			url: './content/pos_ajax.php?execute=savecusttosession',
			data:{cust_id:cust_id,cust_name:cust_name},
			type:"POST",
			success:function(data){
				if(data=="success"){
					$("#name_cust").html("Customer Name: "+$('#custname>option:selected').text());
					$('#dialogbox').dialog('close');
				}else{
					alert(data);
				}
			}
		});
	}
	function viewTrans(){
		var acctid = $('#acctid').html();
		if(!acctid){
			alert("Please select customer first...");
		}else{
			clickDialog('dialogbox2',1000,500,'custtransdetails&acctid='+acctid,'CustTrans Details');
		}
	}
	function viewWorkOrders(){
		var acctid = $('#acctid').html();
		if(!acctid){
			alert("Please select customer first...");
		}else{
			clickDialog('dialogbox2',700,500,'viewWorkorders&acctid='+acctid,'Work Orders');
		}
	}
	function addcustomer(){
		clickDialog('dialogbox2',450,450,'addcustomer','Add Customer');
	}
	function payment_received(){
		var acctid = $('#acctid').html();
		if(!acctid){
			alert("Please select customer first...");
		}else{
			clickDialog('dialogbox2',900,500,'payment_received&acctid='+acctid,'Customer Payment Received');
		}
	}
	function credit_memo(){
		var acctid = $('#acctid').html();
		if(!acctid){
			alert("Please select customer first...");
		}else{
			clickDialog('dialogbox2',400,300,'credit_memo&acctid='+acctid,'Credit Memo');
		}
	}
	function adjustment(){
		var acctid = $('#acctid').html();
		if(!acctid){
			alert("Please select customer first...");
		}else{
			clickDialog('dialogbox2',400,300,'adjustment&acctid='+acctid,'Adjustment');
		}
	}
</script>
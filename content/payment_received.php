<?php
if($_POST){
	//print_r($_POST);
	mysql_query("BEGIN");
	//$rec = array('particular'=>$_REQUEST['particular'],'amt'=>$_REQUEST['amt']);
	$receipt="insert into tbl_receipt_manual (`receipt`,`date`,`cust_id`,`receivedfrom`,`paymentof`,
		`cash_amt`,`check_amt`,`check_details`,`check_date`,`particular`,rent_amount,other_amount,total_amount,bp_date,bp_bank,bp_amt,inwords,receipt_type,withholding,vat,vatsales) values 
		('{$_REQUEST['ornum']}','".$_REQUEST['ordate']."','{$_REQUEST['CRJ']['customer_name']}','".mysql_real_escape_string($_REQUEST['CRJ']['receivedfrom'])."','{$_REQUEST['CRJ']['paymentof']}',
		'{$_REQUEST['CRJ']['cash_amt']}','{$_REQUEST['CRJ']['check_amt']}','".$_REQUEST['CRJ']['check_bank']." ".$_REQUEST['CRJ']['check_num']."',
		'{$_REQUEST['CRJ']['check_date']}','".$_REQUEST['particular']."','".preg_replace("/[^0-9.]/", "", $_REQUEST['rent_amount'])."','".preg_replace("/[^0-9.]/", "", $_REQUEST['other_amount'])."','".preg_replace("/[^0-9.]/", "", $_REQUEST['xtotal'])."',
		'{$_REQUEST['CRJ']['bp_date']}','{$_REQUEST['CRJ']['bp_bank']}','{$_REQUEST['CRJ']['bp_amt']}','".$_REQUEST['inwords']."','".$_REQUEST['receipt_type']."',
		'".preg_replace("/[^0-9.]/", "", $_REQUEST['wholdings'])."','".preg_replace("/[^0-9.]/", "", $_REQUEST['vat'])."','".preg_replace("/[^0-9.]/", "", $_REQUEST['vatable_sales'])."')";
	$qry1=mysql_query($receipt);
	if(!$qry1){
		echo "Error tbl_receipt_manual:".mysql_error();
	}
	
	$sql="insert into tbl_customers_trans (cust_id,receipt,transtype,details,amount) values 
		('{$_REQUEST['CRJ']['customer_name']}','{$_REQUEST['ornum']}','PAYMENT','".implode(" ",$_REQUEST['particular'])."','".str_replace( ',', '', $_REQUEST['xtotal'])."') 
			on duplicate key update details=values(details),amount=values(amount)";
	$qry2=mysql_query($sql);
	if(!$qry2){
		echo "Error tbl_customers_trans:".mysql_error();
	}
	
	if($qry1 && $qry2){
		mysql_query("COMMIT");
		//Journal
		$entry[]=array('account_code'=>'1002','account_desc'=>'CASH IN BANK','dr'=>str_replace( ',', '', $_REQUEST['xtotal']),
			'payto'=>'CEBU CENTRAL REALTY CORPORATION','check_date'=>$_REQUEST['CRJ']['check_date'],
			'check_number'=>$_REQUEST['CRJ']['check_num'],'bank'=>$_REQUEST['CRJ']['check_bank']);
		$entry[]=array('account_code'=>'1020','account_desc'=>'ACCOUNTS RECEIVABLE','cr'=>str_replace( ',', '', $_REQUEST['xtotal']),
		'ar_refid'=>$_REQUEST['CRJ']['customer_name']);
		$glref = $con->getNextJournalID('CRJ');
		$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`) values 
			('".$glref."','','".date('Y-m-d')."','CRJ',
			'TO RECORDS PAYMENT ".implode(" ",$_REQUEST['particular'])."',
			'".str_replace( ',', '', $_REQUEST['xtotal'])."','".$_SESSION['xid']."','ForApproval') 
			on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
		//echo "<br/><hr/>";
		$glid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry);
		//Journal End
		//Printing OR
		// switch($_REQUEST['receipt_type']){
			// case'acknowledgement_receipt':
				// $reportname = "./reports/acknowledgement_receipt.php?receipt={$_REQUEST['ornum']}";
			// break;
			// default:
				// $reportname = "./reports/receipt_manual.php?receipt={$_REQUEST['ornum']}";
			// break;
		// }
		// echo "<script>$(document).ready(function(){loadiFrame('$reportname');PrintIframe();});</script>";
	}else{
		mysql_query("ROLLBACK");
	}
}
$customer=$db->resultArray("*","tbl_customers"," order by customer_name asc");
?>
<form method="post" name="frm_received_payment" id="frm_received_payment">
	<div style="float:left;width:50%;">
		<div style="float:left;margin-right:10px;width:100px;">OR Date</div>
		<input type="text" name="ordate" value="<?=date('Y-m-d')?>" style="float:left;width:100px;margin-right:10px;border:1px solid red;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">OR #</div>
		<input type="text" name="ornum" style="float:left;width:100px;margin-right:10px;border:1px solid red;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Acct #</div>
		<input readonly type="text" name="CRJ[customer_acctnum]" style="float:left;width:100px;margin-right:10px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Customer Name</div>
		<select name="CRJ[customer_name]" style="float:left;width:350px;">
			<option value="">Select Customer</option>
		<?php
			foreach($customer as $key=>$val){
				echo "<option value='".$val['cust_id']."'>".$val['customer_name']."</option>";
			}
		?>
		</select>
		<div style="clear:both;height:10px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Received From</div>
		<input type="text" name="CRJ[receivedfrom]" style="float:left;width:350px;margin-right:10px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Address</div>
		<input readonly type="text" name="CRJ[customer_address]" style="float:left;width:350px;margin-right:10px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Business Style</div>
		<input readonly type="text" name="CRJ[business_style]" style="float:left;width:350px;margin-right:10px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">TIN#</div>
		<input readonly type="text" name="CRJ[customer_tin]" style="float:left;width:350px;margin-right:10px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:100px;">Payment of</div>
		<input type="text" name="CRJ[paymentof]" style="float:left;width:350px;margin-right:10px;border:1px solid red;"/>
		<div style="clear:both;height:5px;"></div>
		<input type="button" style="height:50px;width:150px;" value="Save" onclick="saveForm();"/>
		<fieldset style="float:right;">
			<legend>Select type of Receipt</legend>
			<div style="float:left;margin-right:10px;"><input type="radio" name="receipt_type" value="receipt_manual" checked /> Official Receipt</div>
			<div style="float:left;margin-right:10px;"><input type="radio" name="receipt_type" value="acknowledgement_receipt"/> Acknowledgement Receipt</div>
		</fieldset>
	</div>
	<div style="float:right;width:40%;">
		<fieldset>
			<legend><input type="checkbox" name="CRJ[cashorcheck][]" value="cash" checked="checked"/> Cash Received</legend>
			<div style="float:left;margin-right:10px;width:25%;">Amount</div>
			<input class="cash_field" type="text" name="CRJ[cash_amt]" style="float:left;width:70%;"/>
		</fieldset>
		<fieldset>
			<legend><input type="checkbox" name="CRJ[cashorcheck][]" value="check" checked="checked"/> Cheque Received</legend>
			<div style="float:left;margin-right:10px;width:25%;">Check Date</div>
			<input class="check_field xdate" type="text" name="CRJ[check_date]" style="float:left;width:70%"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:25%;">Check Number</div>
			<input class="check_field" type="text" name="CRJ[check_num]" style="float:left;width:70%"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:25%;">Bank</div>
			<input class="check_field" type="text" name="CRJ[check_bank]" style="float:left;width:70%"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:25%;">Check Amount</div>
			<input class="check_field" type="text" name="CRJ[check_amt]" style="float:left;width:70%"/>
		</fieldset>
		<fieldset>
			<legend><input type="checkbox" name="CRJ[cashorcheck][]" value="check" checked="checked"/> Bank Payment</legend>
			<div style="float:left;margin-right:10px;width:25%;">Trans Date</div>
			<input class="check_field xdate" type="text" name="CRJ[bp_date]" style="float:left;width:70%"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:25%;">Bank</div>
			<input class="check_field" type="text" name="CRJ[bp_bank]" style="float:left;width:70%"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:25%;">Amount</div>
			<input class="check_field" type="text" name="CRJ[bp_amt]" style="float:left;width:70%"/>
		</fieldset>
	</div>
	<div style="clear:both;height:5px;"></div>
	<div style="height:250px;overflow:auto;">
		<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">Particular (SOA# XXXXX)</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="particular" name="particular" style="text-align:right;background:transparent;border:1px solid red;width:100%;" />
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">Rental Due After Tax</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="rent_amount" value="0.00" name="rent_amount" style="text-align:right;background:transparent;border:none;width:100%;border:1px solid red;"/>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">Other Charges</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="other_amount" name="other_amount" style="text-align:right;background:transparent;border:none;width:100%;" />
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">Total Sales (VAT Inclusive)</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="xtotal" name="xtotal" style="text-align:right;background:transparent;border:1px solid red;width:100%;" />
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">With Holdings</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="wholdings" name="wholdings" style="text-align:right;background:transparent;border:none;width:100%;" />
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">VAT</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="vat" name="vat" style="text-align:right;background:transparent;border:none;width:100%;" />
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #1DA5EA;" width="600px">VATable Sales</td>
					<td style="border:1px solid #1DA5EA;">
						<input type="text" id="vatable_sales" name="vatable_sales" style="text-align:right;background:transparent;border:none;width:100%;" />
					</td>
				</tr>
				<tr>
					<td colspan="2"><input type="text" name="inwords" id="inwords" style="width:100%;height:30px;font-size:12px;" value=""/></td>
				</tr>
				<!--tr>
					<th style="border:1px solid #1DA5EA;" width="600px">Particular</th>
					<th style="border:1px solid #1DA5EA;">Amount</th>
				</tr-->
			</thead>
			<tbody></tbody>
		</table>
	</div>
</form>
<div style="clear:both;height:10px;"></div>
<!--fieldset>
	<legend>Particular Entry</legend>
	<input id="bt5" class="buthov" type="button" onclick="unpaidSOA();" value="Unpaid SOA" style="height:30px;width:150px;float:left;"/>
	<input onkeypress="return addParticular(event,this)" type="text" name="addparticular" id="addparticular" style="float:right;height:25px;width:80%;border:1px solid red;"/>
</fieldset-->
<div style="clear:both;height:10px;"></div>
<fieldset>
	<legend>Menu</legend>
	<!--input id="bt13" class="buthov" type="button" onclick="dailyCollection()" value="Daily Collection" style="height:40px;width:150px;float:left;"/-->
	<input id="bt13" class="buthov" type="button" onclick="viewReport('reports/collection_deposit.php');" value="Daily Collection" style="height:40px;width:150px;float:left;"/>
	<input id="bt9" class="buthov" type="button" onclick="viewReceiptManual()" value="Receipt Listing" style="height:40px;width:150px;float:right;"/>
</fieldset>
<script>
$(document).ready(function() {
	window.scrollTo(0,0);
	$("input[name='CRJ[check_date]']").datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$("input[name='ordate']").datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$("input[name='CRJ[bp_date]']").datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	
});
$("#xtotal").live('change',function(){
	$(this).val(new Number($(this).val()).formatMoney(2));
});
$("#rent_amount").change(function(){
	$(this).val(new Number($(this).val()).formatMoney(2));
	var xtotal = new Number(strtodouble($("#xtotal").val()));
	var rent = new Number(strtodouble($("#rent_amount").val()));
	if(xtotal>0){
		var other = xtotal - rent;
		$("#other_amount").val(new Number(other).formatMoney(2));
	}
	calculate();
});
$("#other_amount").change(function(){
	$(this).val(new Number($(this).val()).formatMoney(2));
	var rent = strtodouble($("#rent_amount").val());
	var other = strtodouble($("#other_amount").val());
	var total = rent+other;
	$("#xtotal").val(new Number(total).formatMoney(2));
	calculate();
});
function inwords(amt){
	$.ajax({
		url: './content/pos_ajax.php?execute=inwords&amt='+amt,
		type:"POST",
		success:function(data){
			$("#inwords").val(data);
		}
	});
}
function calculate(){
	var rent = strtodouble($("#rent_amount").val());
	var other_amount = strtodouble($("#other_amount").val());
	// var total = rent+other_amount;
	// $("#xtotal").val(new Number(total).formatMoney(2));
	var total = strtodouble($("#xtotal").val());
	
	inwords(total);
	var basic_rent = (rent/1.07);
	var wholding= (basic_rent*.05);
	var vat = (basic_rent*0.12)+((total-rent)/9.3333333);
	var vatable = (total+wholding)-vat;
	
	if($("input[name='receipt_type']:checked").val()=="acknowledgement_receipt"){
		$("#wholdings").val(new Number(0).formatMoney(2));
		$("#vat").val(new Number(0).formatMoney(2));
		$("#vatable_sales").val(new Number(0).formatMoney(2));
	}else{
		$("#wholdings").val(new Number(wholding).formatMoney(2));
		$("#vat").val(new Number(vat).formatMoney(2));
		$("#vatable_sales").val(new Number(vatable).formatMoney(2));
	}
	
}


function saveForm(){
	var frm = document.frm_received_payment;
	if($("input[name='ornum']").val()==""){
		alert("Dont forget O.R. Number..");
		return false;
	}
	if($("input[name='xtotal']").val()==0){
		alert("Dont forget Total Amount...");
		return false;
	}
	document.frm_received_payment.submit();
}
function dailyCollection(){
	if (window.showModalDialog) {
		window.showModalDialog('./reports/cust_trans.php',"Daily Collection","dialogWidth:900px;dialogHeight:650px");
		} else {
		window.open('./reports/cust_trans.php',"Daily Collection",'height=650,width=900,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
function unpaidSOA(){
	var cust_id = $('input[name="CRJ[customer_acctnum]"]').val();
	clickDialog('dialogbox',700,400,'unpaidSOA&cust_id='+cust_id,'SOA Listing');
}
function viewReceiptManual(){
	clickDialog('dialogbox',800,400,'receipt_manual','Receipt List');
}
function addParticular(e,arg){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if (chCode == 13) {
		var txt = '<tr>\
					<td><input type="text" name="particular[]" value="'+arg.value+'" style="width:100%;border:none;background:transparent;"/></td>\
					<td><input onchange="sumVoucherAmt()" type="text" class="amt" name="amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="0.00"/></td>\
				</tr>';
		$("#mytbl tbody").prepend(txt);
		arg.value="";
		sumVoucherAmt();
	}
}
function sumVoucherAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val();
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#xtotal").val(new Number(sum).formatMoney(2));
}
$('select[name="CRJ[customer_name]"]').change(function(){
	$('input[name="CRJ[customer_acctnum]"]').val($(this).val());
	$.ajax({
		url: './content/pos_ajax.php?execute=customerInfo&cust_id='+$(this).val(),
		type:"POST",
		dataType:"json",
		success:function(data){
			console.log(data);
			$('input[name="CRJ[customer_address]"]').val(data.customer_address);
			$('input[name="CRJ[business_style]"]').val(data.nature_of_business);
			$('input[name="CRJ[customer_tin]"]').val(data.tin);
			$('input[name="CRJ[receivedfrom]"]').val(data.customer_name);
			$('input[name="CRJ[paymentof]"]').val("RENTAL AND OTHER CHARGES");
		}
	});
});
function loadiFrame(src){
	$("#iframeplaceholder").html("<iframe id='myiframe' name='myname' src='" + src + "' />");
}
function PrintIframe() { 
	$("#myiframe").load( 
		function() {
			window.frames['myname'].focus();
			window.frames['myname'].print();
			window.frames['myname'].close();
			window.frames['myname'].onafterprint = function () {
				window.location=document.URL;
			}
		}
	 );
}
</script>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
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
			$_SESSION['contract']['custid']=$_REQUEST['custid'];
			if($_SESSION['contract']){
				echo "success";
			}
		break;
		case'list':
			switch($_REQUEST['filter']){
				case'expiry':
					$filter = "date_format(a.contract_end_date,'%Y-%m') between '".date('Y-m')."' and '".date('Y-12')."'";
					$order = "order by a.contract_end_date asc";
				break;
				default:
					$filter="";
					$order="";
				break;
			}
			
			if($_REQUEST['search']){
				$list = $db->resultArray("a.*,b.customer_name","tbl_customers_contract a",
				"left join tbl_customers b on a.custid=b.cust_id where a.mall_unit_number like '%{$_REQUEST['search']}%' or b.customer_name like '%{$_REQUEST['search']}%' ".($filter?"and $filter":"")." $order");
			}else{
				$list = $db->resultArray("a.*,b.customer_name","tbl_customers_contract a","left join tbl_customers b on a.custid=b.cust_id ".($filter?"where $filter":"")." $order");
			}
			echo $sql;
			// print_r($list);
		?>
			
			<input type="text" name="search" id="search" style="float:left;width:50%;margin-right:10px;margin-top:20px;" />
			<fieldset style="float:left;width:30%;margin-right:10px;">
				<legend>Filter</legend>
				<select name="filter" id="filter" style="width:100%;">
					<option value="">Select</option>
					<option value="expiry">Expiry</option>
					<option value="active">Active</option>
					<option value="inactive">In-Active</option>
					<option value="vacant">Vacant</option>
				</select>
			</fieldset>
			<input type="button" style="float;right;width:10%;margin-left:5px;margin-top:20px;" value="Search" onclick="search();"/>
			<div style="clear:both;height:5px;"></div>
			<table class="navigateableMain" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;">Contract #</th>
						<th style="border:none;">Customer Name</th>
						<th style="border:none;">mall_unit_number</th>
						<th style="border:none;">floor_area_sqm</th>
						<th style="border:none;">contract_start_date</th>
						<th style="border:none;">contract_end_date</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($list as $key => $val){ ?>
						<tr>
							<td><a href="?page=contract&custid=<?=$val['custid']?>"><?=$val['contract_number']?></a></td>
							<td style="text-align:center;"><?=$val['customer_name']?></td>
							<td style="text-align:center;"><?=$val['mall_unit_number']?></td>
							<td style="text-align:right;"><?=$val['floor_area_sqm']?></td>
							<td style="text-align:center;"><?=$val['contract_start_date']?></td>
							<td style="text-align:center;"><?=$val['contract_end_date']?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			
		<?
		break;
	}
}else{
	if($_REQUEST['custid']){
		$_SESSION['contract']['custid']=$_REQUEST['custid'];
	}
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		mysql_query("BEGIN");
		$sql="insert into tbl_customers_contract (`custid`,`".implode("`,`",array_keys($_POST['info']))."`) values ('{$_REQUEST['business_name']}','".implode("','",str_replace(",","",array_values($_POST['info'])))."') 
			on duplicate key update ";
		$flag=false;
		foreach(array_keys($_POST['info']) as $key => $val){
			if($flag)$sql.=",";
			$sql.="`$val`=values(`$val`)";
			$flag=true;
		}
		if($_POST['recurring']){
			$del = mysql_query("delete from tbl_customers_contract_recurring_charges where custid='{$_REQUEST['business_name']}'");
			if($del){
				$sql2="insert into tbl_customers_contract_recurring_charges (id,custid,charge_description,amount) values ";
				$flag=false;
				foreach(array_values($_POST['recurring']) as $key => $val){
					if($flag)$sql2.=",";
					$sql2.="('{$val['id']}','{$_REQUEST['business_name']}','{$val['charge_description']}','{$val['amount']}')";
					$flag=true;
				}
				$sql2.=" on duplicate key update charge_description=values(charge_description),amount=values(amount)";
				$qry2=mysql_query($sql2);
			}
		}else{
			$qry2=true; //means its ok to save even no recurring values
		}
		// echo $sql;
		$qry1 = mysql_query($sql);
		if($qry1 && $qry2){
			mysql_query("COMMIT");
			echo "<script>$(document).ready(function(){alertMsg('Successfully Save...');});</script>";
		}else{
			mysql_query("ROLLBACK");
			echo mysql_error();
		}
	}
	$info = $db->getWHERE("a.*,b.customer_address,b.nature_of_business,b.tin,b.contact_person","tbl_customers_contract a left join tbl_customers b on a.custid=b.cust_id","where custid='".$_SESSION['contract']['custid']."'");
	$recurring = $db->resultArray("*","tbl_customers_contract_recurring_charges","where custid='{$_SESSION['contract']['custid']}'");
	
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2>Lease Contract Information</h2>
	<form name="frminfo" id="frminfo" method="post">
	<div style="float:left;margin-right:5px;width:50%;">
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
		<input id="bt7" class="buthov" type="button" value="View Contract" onclick="viewContract()" style="float:right;height:40px;width:150px;float:left;"/>
	</fieldset>
	<div style="clear:both;height:10px;"></div>
		<div style="width:45%;float:left;">
			<fieldset>
				<legend>Tenant Information</legend>
				<div style="width:150px;float:left;">Address</div>
				<input value="<?=$info['customer_address']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Telephone Number</div>
				<input value="<?=$info['contact_number']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Fax Number</div>
				<input value="<?=$info['fax_number']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Contact Person</div>
				<input value="<?=$info['contact_person']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">TIN</div>
				<input value="<?=$info['tin']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Nature of Business</div>
				<input value="<?=$info['nature_of_business']?>" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset>
				<legend>Details</legend>
					<div style="width:150px;float:left;">Contract Date</div>
					<input value="<?=$info['contract_date']?>" type="text" name="info[contract_date]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Contract Start Date</div>
					<input value="<?=$info['contract_start_date']?>" type="text" name="info[contract_start_date]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Contract End Date</div>
					<input value="<?=$info['contract_end_date']?>" type="text" name="info[contract_end_date]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Start Bill Period</div>
					<input value="<?=$info['start_bill_period']?>" type="text" name="info[start_bill_period]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Fixed Monthly Rental</div>
					<input value="<?=number_format($info['fixed_monthly_rental'],2)?>" type="text" name="info[fixed_monthly_rental]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Min. Monthly Rental</div>
					<input value="<?=$info['min_monthly_rental']?>" type="text" name="info[min_monthly_rental]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Escalation Rate %</div>
					<input value="<?=$info['escalation_rate']?>" type="text" name="info[escalation_rate]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Number of Years</div>
					<input value="<?=$db->dateDiff($info['contract_date'],date('Y-m-d'))?>" type="text" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Insurance Company</div>
					<input value="<?=$info['insurance_company']?>" type="text" name="info[insurance_company]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Actual Open Date</div>
					<input value="<?=$info['actual_open_date']?>" type="text" name="info[actual_open_date]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Withholding Tax %</div>
					<input value="<?=$info['withholding_tax']?>" type="text" name="info[withholding_tax]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">VAT %</div>
					<input value="<?=$info['vat']?>" type="text" name="info[vat]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Discount %</div>
					<input value="<?=$info['discount']?>" type="text" name="info[discount]" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="button" value="SAVE" onclick="save()" style="float:left;height:30px;width:130px;"/>
			</fieldset>
		</div>
		<div style="width:55%;float:right;">
			<fieldset style="float:left;width:45%;">
				<legend>Electricity (per sqm charges)</legend>
				<div style="width:40%;float:left;">Minimum Charge</div>
				<input value="<?=number_format($info['elect_mincharge'],2)?>" type="text" name="info[elect_mincharge]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:40%;float:left;">Rate</div>
				<input value="<?=number_format($info['elect_rate'],2)?>" type="text" name="info[elect_rate]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset style="float:left;width:45%;">
				<legend>Water (per sqm charges)</legend>
				<div style="width:40%;float:left;">Minimum Charge</div>
				<input value="<?=number_format($info['water_mincharge'],2)?>" type="text" name="info[water_mincharge]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:40%;float:left;">Rate</div>
				<input value="<?=number_format($info['water_rate'],2)?>" type="text" name="info[water_rate]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset style="float:left;width:45%;">
				<legend>Others (per sqm charges)</legend>
				<div style="width:40%;float:left;">CUSA Rate</div>
				<input value="<?=number_format($info['others_cusa_rate'],2)?>" type="text" name="info[others_cusa_rate]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:40%;float:left;">Aircon Rate</div>
				<input value="<?=number_format($info['others_aircon_rate'],2)?>" type="text" name="info[others_aircon_rate]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:40%;float:left;">Pest Control %</div>
				<input value="<?=$info['others_pestcontrol_percent']?>" type="text" name="info[others_pestcontrol_percent]" style="width:55%;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset style="float:left;width:45%;">
				<legend>Remarks</legend>
				<textarea style="min-height:94px;width:100%;" name="info[remarks]"><?=$info['remarks']?></textarea>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset>
				<legend>Other Recurring Charges (Fixed Charges)</legend>
				<div style="height:85px;overflow:auto;">
						<table class="navigateableMain" id="rc_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th style="border:none;">&nbsp;</th>
									<th style="border:none;width:250px;">Charge Description</th>
									<th style="border:none;">Amount</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($recurring){
								$count=0;
								foreach($recurring as $key => $val){
									echo '<tr>
										<td><input type="checkbox" ><input type="hidden" name="recurring['.$count.'][id]" value="'.$val['id'].'" style="width:10px;"/></td>
										<td><input type="text" name="recurring['.$count.'][charge_description]" value="'.$val['charge_description'].'" style="width:100%;"/></td></td>
										<td><input type="text" class="amt" name="recurring['.$count.'][amount]" style="width:100%;text-align:right;" value="'.$val['amount'].'"/></td>
									</tr>';
									$count++;
								}
							}
							?>
							</tbody>
						</table>
				</div>
				<div style="clear:both;height:10px;"></div>
				<fieldset>
					<legend>Particular Entry</legend>
					<input onkeypress="return addParticular(event,this)" type="text" id="rc" style="float:left;height:25px;width:100%"/>
				</fieldset>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="float:left;width:45%;">
				<legend>Bonds/Deposits</legend>
				<div style="width:150px;float:left;">Security Deposit</div>
				<input value="<?=number_format($info['security_deposit'],2)?>" type="text" name="info[security_deposit]" style="width:100px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Electrical Deposit</div>
				<input value="<?=number_format($info['electrical_deposit'],2)?>" type="text" name="info[electrical_deposit]" style="width:100px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Adv Rental</div>
				<input value="<?=number_format($info['adv_rental'],2)?>" type="text" name="info[adv_rental]" style="width:100px;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset style="float:left;width:45%;">
				<legend>Mall Unit & Floor Area</legend>
				<div style="width:130px;float:left;">Mall Unit Number</div>
				<input value="<?=$info['mall_unit_number']?>" type="text" name="info[mall_unit_number]" style="width:250px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:130px;float:left;">Floor Area Sqm</div>
				<input value="<?=$info['floor_area_sqm']?>" type="text" name="info[floor_area_sqm]" style="width:250px;"/>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
		</div>
	</form>
</div>
<script>

$(document).ready(function() {
	$("#business_name").val(<?=$_SESSION['contract']['custid']?>);
	var config = {
	  '.chosen-select'           : {width: "80%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		toSession();
	});
	$('input[name="info[contract_start_date]"],input[name="info[contract_end_date]"],input[name="info[contract_date]"],input[name="info[actual_open_date]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
});
function search(){
	var search = $("#search").val();
	var filter = $("#filter").val();
	clickDialogUrl("dialogbox3",900,500,'./content/ccrc/contract.php?execute=list&search='+search+'&filter='+filter,"Listing");
}
function viewContract(){
	clickDialogUrl("dialogbox3",900,500,'./content/ccrc/contract.php?execute=list',"Listing");
}
function toSession(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/ccrc/contract.php?execute=toSession&custid='+custid,
			type:"POST",
			success:function(data){
				window.location=document.URL;
			}
		});
	}
}
function addParticular(e,arg){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	var num =$("#"+arg.id+"_tbl tbody tr").length;
	if (chCode == 13) {
		switch(arg.id){
			case'rc':
				var txt = '<tr>\
						<td><input type="checkbox" ></td>\
						<td><input type="text" name="recurring['+num+'][charge_description]" value="'+arg.value+'" style="width:100%;"/></td>\
						<td><input type="text" class="amt" name="recurring['+num+'][amount]" style="width:100%;text-align:right;" value="0.00"/></td>\
					</tr>';
			break;
		}
		$("#"+arg.id+"_tbl tbody").prepend(txt);
		arg.value="";
	}
}
$("#rc_tbl").bind('keydown',function(e){
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
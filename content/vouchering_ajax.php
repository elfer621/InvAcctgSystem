<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
error_reporting(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
switch($_REQUEST['execute']){
	case'delJLentry':
		$sql="delete from tbl_vouchering where id='{$_REQUEST['refid']}' and COALESCE(type,'')='{$_REQUEST['type']}'";
		$qry = mysql_query($sql);
		if($qry){
			$entry = "delete from tbl_journal_entry where refid='{$_REQUEST['refid']}' and COALESCE(type,'')='{$_REQUEST['type']}'";
			$qryentry=mysql_query($entry);
			if($qryentry){
				echo "success";
			}else{
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
	break;
	case'addInfo':
		//$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_SESSION["vouchering"]['refid']}' and COALESCE(type,'')='{$_SESSION["vouchering"]['type']}' order by dr desc");
		$count=$_REQUEST['id'];
		$id="seq$count";
		$val = $db->getWHERE("*","tbl_chart_of_account","where account_code='{$_REQUEST['acctcode']}'");
		switch($val['sub_account']){
			case'CASH':
				$modal = '<fieldset><input type="hidden" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][acctcode]" value="'.$val['account_code'].'"/>
						<div style="float:left;margin-right:10px;width:100px;">PayTo</div>
						<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][payto]" style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Check Date</div>
						<input class="check_field xdate" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_date]"  style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Check Number</div>
						<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_num]"  style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Bank</div>
						<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_bank]"  style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Bank Validation</div>
						<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][bank_validation]"  style="float:left;width:150px;"/></fieldset>';
			break;
			case'ACCOUNTS RECEIVABLE':
				$option="";
				$customer=$db->resultArray("*","tbl_customers","");
				foreach($customer as $k=>$v){
					$option.= "<option ".($val['ar_refid']==$v['cust_id']?"selected":"")." value='".$v['cust_id']."'>".$v['customer_name']."</option>";
				}
				$modal = '<div style="float:left;margin-right:10px;width:100px;">Customer Name</div>'.
					'<select name="entry['.$count.'][ar_refid]" style="float:left;width:250px;">'.
					'<option value="">Select Customer</option>'.$option.'</select>
					<div style="clear:both;height:5px;"></div>';
			break;
			case'CURRENT LIABILITIES':
				$option="";
				$sup=$db->resultArray("*","tbl_supplier","");
				foreach($sup as $k=>$v){
					$option.= "<option ".($val['ap_refid']==$v['id']?"selected":"")." value='".$v['id']."'>".$v['supplier_name']."</option>";
				}
				$modal = '<div style="float:left;margin-right:10px;width:100px;">Supplier Name</div>'.
					'<select name="entry['.$count.'][ap_refid]" style="float:left;width:250px;">'.
					'<option value="">Select Supplier</option>'.$option.'</select>
					<div style="clear:both;height:5px;"></div>';
			break;
			case'SALES':
			case'OTHER INCOME':
				$option="";
				$branch=$db->resultArray("cost_center name","tbl_cost_center","");
				foreach($branch as $k=>$v){
					$option.= "<option ".($val['center']==$v['name']?"selected":"")." value='".$v['name']."'>".$v['name']."</option>";
				}
				$modal = '<div style="float:left;margin-right:10px;width:100px;">Cost Center</div>'.
					'<select name="entry['.$count.'][cost_center]" style="float:left;width:250px;">'.
					'<option value="">Select Cost Center</option>'.$option.'</select>
					<div style="clear:both;height:5px;"></div>';
			break;
		}
		if($val['account_group']=="EXPENSES"){
			$option="";
			$branch=$db->resultArray("cost_center name","tbl_cost_center","");
			foreach($branch as $k=>$v){
				$option.= "<option ".($val['center']==$v['name']?"selected":"")." value='".$v['name']."'>".$v['name']."</option>";
			}
			$modal = '<div style="float:left;margin-right:10px;width:100px;">Cost Center</div>'.
				'<select name="entry['.$count.'][cost_center]" style="float:left;width:250px;">'.
				'<option value="">Select Cost Center</option>'.$option.'</select>
				<div style="clear:both;height:5px;"></div>';
		}
	
		$close ='<span class="close" onclick="closeModal(\''.$id.'\');">&times;</span><div style="clear:both;height:5px;"></div>';
		echo '<div class="modal-content">'.$close.$modal.'</div>';
	break;
	case'addInfoOld':
		//To be continue codding $count = 
		if(strpos($_REQUEST['desc'],"CASH IN BANK") !== false){
			echo '<div class="modal-content">';
			echo '<span class="close" onclick="closeModal(\'seq'.$_REQUEST['id'].'\');">&times;</span><div style="clear:both;height:5px;"></div>';
			?>
			<fieldset><input type="hidden" name="checkinfo[<?=$_REQUEST['acctcode']?>][acctcode]" value="<?=$_REQUEST['acctcode']?>"/>
			<div style="float:left;margin-right:10px;width:100px;">PayTo</div>
			<input class="check_field" type="text" name="checkinfo[<?=$_REQUEST['acctcode']?>][payto]" style="float:left;width:150px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:100px;">Check Date</div>
			<input class="check_field xdate" type="text" name="checkinfo[<?=$_REQUEST['acctcode']?>][check_date]" style="float:left;width:150px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:100px;">Check Number</div>
			<input class="check_field" type="text" name="checkinfo[<?=$_REQUEST['acctcode']?>][check_num]" style="float:left;width:150px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:100px;">Bank</div>
			<input class="check_field" type="text" name="checkinfo[<?=$_REQUEST['acctcode']?>][check_bank]" style="float:left;width:150px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:10px;width:100px;">Check Amount</div>
			<input class="check_field" type="text" name="checkinfo[<?=$_REQUEST['acctcode']?>][check_amt]" style="float:left;width:150px;"/></fieldset>
			<?
			echo '<div style="clear:both;height:5px;"></div></div>';
		}
		if($db->strpos_arr($_REQUEST['desc'],array("ACCOUNTS RECEIVABLE","A/R")) !== false){
			echo '<div class="modal-content">';
			echo '<span class="close" onclick="closeModal(\'seq'.$_REQUEST['id'].'\');">&times;</span><div style="clear:both;height:5px;"></div>';
			$customer=$db->resultArray("*","tbl_customers","");
			?>
			<div style="float:left;margin-right:10px;width:100px;">Customer Name</div>
			<select name="ar_refid[<?=$_REQUEST['acctcode']?>]" style="float:left;width:250px;">
				<option value="">Select Customer</option>
			<?php
				foreach($customer as $key=>$val){
					echo "<option value='".$val['cust_id']."'>".$val['customer_name']."</option>";
				}
			?>
			</select>
			<?
			echo '<div style="clear:both;height:5px;"></div></div>';
		}
		if($db->strpos_arr($_REQUEST['desc'],array("ACCOUNTS PAYABLE","ACCRUED EXPENSES")) !== false){
			echo '<div class="modal-content">';
			echo '<span class="close" onclick="closeModal(\'seq'.$_REQUEST['id'].'\');">&times;</span><div style="clear:both;height:5px;"></div>';
			$sup=$db->resultArray("*","tbl_supplier","");
			?>
			<div style="float:left;margin-right:10px;width:100px;">Supplier Name</div>
			<select name="ap_refid[<?=$_REQUEST['acctcode']?>]" style="float:left;width:250px;">
				<option value="">Select Supplier</option>
			<?php
				foreach($sup as $key=>$val){
					echo "<option value='".$val['id']."'>".$val['supplier_name']."</option>";
				}
			?>
			</select>
			<?
			echo '<div style="clear:both;height:5px;"></div></div>';
		}
		if($db->strpos_arr($_REQUEST['desc'],array("SALES","EXPENSE","EXP")) !== false){
			echo '<div class="modal-content">';
			echo '<span class="close" onclick="closeModal(\'seq'.$_REQUEST['id'].'\');">&times;</span><div style="clear:both;height:5px;"></div>';
			$branch=$db->resultArray("*","tbl_branch","");
			?>
			<div style="float:left;margin-right:10px;width:100px;">Cost Center</div>
			<select name="cost_center[<?=$_REQUEST['acctcode']?>]" style="float:left;width:250px;">
				<option value="">Select Cost Center</option>
			<?php
				foreach($branch as $key=>$val){
					echo "<option value='".$val['name']."'>".$val['name']."</option>";
				}
			?>
			</select>
			<?
			echo '<div style="clear:both;height:5px;"></div></div>';
		}
	break;
	case'particularSelection':
		switch($_REQUEST['type']){
			case'CDJ':
			?>
				<div style="float:left;">
					<div style="float:left;margin-right:10px;width:50px;">Acct #</div>
					<input type="text" name="customer_acctnum" style="float:left;width:100px;margin-right:10px;"/>
					<div style="float:left;margin-right:10px;width:100px;">Pay To</div>
					<input type="text" name="customer_name" style="float:left;width:250px;"/>
					<input type="button" value="..." style="float:left;width:30px;margin-left:5px;"/>
				</div>
				<div style="clear:both;height:5px;"></div>
				<fieldset style="float:left;width:45%;">
					<legend><input type="checkbox" name="cashorcheck[]" value="cash" checked="checked"/> Cash</legend>
					<div style="float:left;margin-right:10px;width:100px;">Amount</div>
					<input class="cash_field" type="text" name="cash_amt" style="float:left;width:150px;"/>
				</fieldset>
				<fieldset style="float:right;width:45%;">
					<legend><input type="checkbox" name="cashorcheck[]" value="check" checked="checked"/> Check</legend>
					<div style="float:left;margin-right:10px;width:100px;">Check Date</div>
					<input class="check_field" type="text" name="check_date" style="float:left;width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:10px;width:100px;">Check Number</div>
					<input class="check_field" type="text" name="check_num" style="float:left;width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:10px;width:100px;">Bank</div>
					<input class="check_field" type="text" name="check_bank" style="float:left;width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:10px;width:100px;">Check Amount</div>
					<input class="check_field" type="text" name="check_amt" style="float:left;width:150px;"/>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<div style="height:100px;overflow:auto;">
					<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;" width="600px">Particular</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div style="clear:both;height:10px;"></div>
				<fieldset>
					<legend>Particular Entry</legend>
					<input onkeypress="return addParticular(event,this)" type="text" name="addparticular" id="addparticular" style="float:left;height:25px;width:100%"/>
				</fieldset>
				<script>
					$("input:checkbox[name='cashorcheck[]']").click(function(){
						console.log($(this)[0]['value']);
						console.log($(this)[0]['checked']);
						if($(this)[0]['checked']==false){
							$("input."+$(this)[0]['value']+"_field").attr("disabled", true);
						}else{
							$("input."+$(this)[0]['value']+"_field").attr("disabled", false);
						}
						 
					});
				</script>
			<?
			break;
			case'CRJ':
				$customer=$db->resultArray("*","tbl_customers","");
				?>
				<div style="float:left;width:45%;">
					<div style="float:left;margin-right:10px;width:100px;">Acct #</div>
					<input readonly type="text" name="CRJ[customer_acctnum]" style="float:left;width:100px;margin-right:10px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:10px;width:100px;">Customer Name</div>
					<select name="CRJ[customer_name]" style="float:left;width:250px;">
						<option value="">Select Customer</option>
					<?php
						foreach($customer as $key=>$val){
							echo "<option value='".$val['cust_id']."'>".$val['customer_name']."</option>";
						}
					?>
					</select>
					<!--div style="clear:both;height:5px;"></div>
					<input type="button" value="View Unpaid SOA" style="height:30px;width:150px;"/-->
				</div>
				<div style="float:right;width:45%;">
					<fieldset>
						<legend><input type="checkbox" name="CRJ[cashorcheck][]" value="cash" checked="checked"/> Cash Received</legend>
						<div style="float:left;margin-right:10px;width:100px;">Amount</div>
						<input class="cash_field" type="text" name="CRJ[cash_amt]" style="float:left;width:150px;"/>
					</fieldset>
					<fieldset>
						<legend><input type="checkbox" name="CRJ[cashorcheck][]" value="check" checked="checked"/> Check Received</legend>
						<div style="float:left;margin-right:10px;width:100px;">Check Date</div>
						<input class="check_field xdate" type="text" name="CRJ[check_date]" style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Check Number</div>
						<input class="check_field" type="text" name="CRJ[check_num]" style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Bank</div>
						<input class="check_field" type="text" name="CRJ[check_bank]" style="float:left;width:150px;"/>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Check Amount</div>
						<input class="check_field" type="text" name="CRJ[check_amt]" style="float:left;width:150px;"/>
					</fieldset>
				</div>
				<div style="clear:both;height:5px;"></div>
				<div style="height:100px;overflow:auto;">
					<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;" width="600px">Particular</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div style="clear:both;height:10px;"></div>
				<fieldset>
					<legend>Particular Entry</legend>
					<input onkeypress="return addParticular(event,this)" type="text" name="addparticular" id="addparticular" style="float:left;height:25px;width:100%"/>
				</fieldset>
				<script>
					$(document).ready(function() {
						$('.xdate').datepicker({
							inline: true,
							dateFormat:"yy-mm-dd"
						});
					});
					$("input:checkbox[name='CRJ[cashorcheck][]']").click(function(){
						console.log($(this)[0]['value']);
						console.log($(this)[0]['checked']);
						if($(this)[0]['checked']==false){
							$("input."+$(this)[0]['value']+"_field").attr("disabled", true);
						}else{
							$("input."+$(this)[0]['value']+"_field").attr("disabled", false);
						}
						 
					});
					$('select[name="CRJ[customer_name]"]').change(function(){
						$('input[name="CRJ[customer_acctnum]"]').val($(this).val());
					});
				</script>
				<?
			break;
			case'GJ':
			?>
			
			<?
			break;
			case'PJ':
			$supplier=$db->resultArray("*","tbl_supplier","");
			?>
				<div style="float:left;width:45%;">
					<div style="float:left;margin-right:10px;width:100px;">Acct #</div>
					<input readonly type="text" name="PJ[supid]" style="float:left;width:100px;margin-right:10px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:10px;width:100px;">Customer Name</div>
					<select name="PJ[supname]" style="float:left;width:250px;">
						<option value="">Select Supplier</option>
					<?php
						foreach($supplier as $key=>$val){
							echo "<option value='".$val['id']."'>".$val['supplier_name']."</option>";
						}
					?>
					</select>
				</div>
				<div style="clear:both;height:5px;"></div>
				<div style="height:100px;overflow:auto;">
					<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;" width="600px">Particular</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody>
						
						</tbody>
					</table>
				</div>
				<div style="clear:both;height:10px;"></div>
				<fieldset>
					<legend>Particular Entry</legend>
					<input onkeypress="return addParticular(event,this)" type="text" name="addparticular" id="addparticular" style="float:left;height:25px;width:100%"/>
				</fieldset>
			<?
			break;
			case'SJ':
			?>
			
			<?
			break;
			case'TJ':
			?>
			
			<?
			break;
		}
	break;
	case'viewUnpaidSoa':
		$sql="select * from tbl_soa where cust_id='{$_REQUEST['cust_id']}' and payment_ref is null";
		$qry = mysql_query($sql);
		echo '<table class="navigateable tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td></td>
				<td>SOA</td>
				<td>Amount</td>
			</tr>';
		while($row=mysql_fetch_assoc($qry)){
			echo '<tr>
				<td><input type="checkbox"></td>
				<td></td>
				<td></td>
			</tr>';
		}
	break;
	case'saveChequeDetails':
		$sql = "insert into tbl_bank_entry (voucher_ref,bank_refid,cheque_num,amount,cheque_date) values 
			('".$_REQUEST['refid']."','".$_REQUEST['bank']."','".$_REQUEST['chequenum']."','".$_REQUEST['chequeamt']."','".$_REQUEST['chequedate']."') 
			on duplicate key update bank_refid=values(bank_refid),cheque_num=values(cheque_num),amount=values(amount),cheque_date=values(cheque_date)";
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
			$_SESSION["vouchering"]['refid']=$_REQUEST['refid'];
		}else{
			echo mysql_error();
		}
	break;
	case'ChequeEntry':
		$sqlbank = mysql_query("select * from tbl_bank_account");
		$rec = $db->getWHERE("*","tbl_bank_entry","where voucher_ref='{$_REQUEST['refid']}'");
		?>
		<form name="frmcheque" id="frmcheque" method="post">
			<input type="hidden" name="refid" value="<?=$_REQUEST['refid']?>"/>
			<div style="float:left;margin-right:5px;width:100px;">Cheque Date</div>
			<input type="text" name="chequedate" id="chequedate" style="float:left;width:300px;" value="<?=$rec['cheque_date']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Bank</div>
			<select name="bank" style="float:left;width:300px;">
				<option value="">Select Bank</option>
				<?php 
					while($row=mysql_fetch_assoc($sqlbank)){
						echo "<option ".($rec['bank_refid']==$row['id']?"selected":"")." value='".$row['id']."'>".$row['bank_name']." [".$row['bank_account']."]</option>";
					}
				?>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Cheque No.</div>
			<input type="text" name="chequenum" id="chequenum" style="float:left;width:300px;" value="<?=$rec['cheque_num']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Amount</div>
			<input type="text" readonly name="chequeamt" id="chequeamt" style="float:left;width:300px;text-align:right;" value="<?=$_REQUEST['amt']?>"/>
			<div style="clear:both;height:5px;"></div>
			<input type="button" style="height:30px;width:150px;" value="Save" onclick="saveChequeDetails()"/>
		</form>
		<script>
			$('#chequedate').datepicker({
				inline: true,
				changeMonth: true,
				changeYear: true,
				dateFormat:"yy-mm-dd"
			});
			function saveChequeDetails(){
				var datastring = $("#frmcheque").serialize();
					$.ajax({
						url: './content/vouchering_ajax.php?execute=saveChequeDetails',
						data:datastring,
						type:"POST",
						success:function(data){
							alert(data);
							if(data=="success"){
								window.location.reload();
							}
						}
					});
			}
		</script>
		<?
	break;
	case'loadTrans':
		switch($_REQUEST['type']){
			case'ForApproval':
				$sql = "select * from tbl_vouchering where (approvedby is null or status='ForApproval') and `status`<>'Rejected' order by id desc";
			break;
			case'Approved':
				$sql = "select * from tbl_vouchering where approvedby is not null order by id desc";
			break;
			case'Rejected':
				$sql = "select * from tbl_vouchering where `status`='Rejected' order by id desc";
			break;
		}
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
		?>
		<table class="navigateable" id="mytbl" style="width:100%;">
			<thead>
				<tr>
					<th>RefID</th>
					<th>Status</th>
					<th>Type</th>
					<th>Date</th>
					<th>Remarks</th>
					<th>Total</th>
					<th>Menu</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:transferStockin('<?php echo $row['id'] ?>','<?=$row['type']?>')" class="activation"><?php echo $row['id'] ?></a></td>
					<td><?= $row['status']?></td>
					<td><?= $row['type']?></td>
					<td><?= $row['date']?></td>
					<td><?= $row['remarks']?></td>
					<td><?= number_format($row['total'],2) ?></td>
					<td>
						<img src="./images/print.png" style="width:20px;height:20px;float:left;" onclick="viewReport('./reports/vouchering.php?refid=<?php echo $row['id'] ?>')"/>
					</td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<?
	break;
	case'view_vouchering':
		if($db->constatus=="lizgan_main"){
			include_once"view_vouchering_main.php";
		}else{
			include_once"view_vouchering.php";
		}
	break;
	case'rejectUpdate':
		if($_SESSION['restrictionid']==1){
			$newnotes = date('Y-m-d h:i:s A')."<br/>".$_REQUEST['notes']."<br/>";
			$sql="update tbl_vouchering set `notes`=concat(`notes`,'".$newnotes."'),`status`='Rejected' where id='".$_REQUEST['refid']."'";
			$qry = mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		}else{
			echo "You don't have permission for this functionality....";
		}
	break;
	case'rejectView':
	?>
	<textarea id="notes" style="height:200px;width:370px;"></textarea>
	<div style="clear:both;height:5px;"></div>
	<input type="button" value="Save" onclick="rejectUpdate(<?=$_REQUEST['id']?>)" style="height:30px;width:100px;"/>
	<script>
	function rejectUpdate(id){
		$.ajax({
			url: './content/vouchering_ajax.php?execute=rejectUpdate&refid='+id,
			type:"POST",
			data:{notes:$("#notes").val()},
			success:function(data){
				alert(data);
				if(data=="success"){
					window.location.reload();
				}
				
			}
		});
	}
	</script>
	<?
	break;
	case'validate':
		if($_SESSION['restrictionid']==1){
			$stat = $_REQUEST['type']=="approvedby"?"Approved":"CertifiedCorrect";
			$sql="update tbl_vouchering set `{$_REQUEST['type']}`='".$_SESSION['xid']."',`status`='".$stat."' where id='".$_REQUEST['refid']."'";
			$qry = mysql_query($sql);
			if($qry){
				echo "success";
			}else{
				echo mysql_error();
			}
		}else{
			echo "You don't have permission for this functionality....";
		}
	break;
	case'viewVoucher':
		unset($_SESSION["vouchering"]);
		$_SESSION["vouchering"]['refid']=$_REQUEST['refid'];
		$_SESSION["vouchering"]['type']=$_REQUEST['type'];
		$_SESSION["vouchering"]['center']=$_REQUEST['center'];
		
		echo "success";
	break;
	case'chartofaccount':
		$sql="select * from tbl_chart_of_account order by account_desc asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
		?>
		<div id="tbl_content" name="tbl_content" style="overflow:auto;height:400px;font-size:15px;">
			<table class="navigateable" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
				<thead>
					<tr>
						<th>Account Code</th>
						<th>Account Desc</th>
						<th>Group</th>
						<th>Type</th>
						<th>Default Side</th>
					</tr>
				</thead>
				<tbody>
					<? 	
					if(mysql_num_rows($qry)==0){
						echo "<tr>
								<td colspan='4' align='center'>No Records Found...</td>
							</tr>";
					}
					while($row = mysql_fetch_assoc($qry)){ ?>
						<tr>
							<td><a href="javascript:itemSelected('<?php echo $row['account_code'] ?>','<?=$row['account_desc']?>','<?=$row['default_side']?>')" class="activation"><?php echo $row['account_code'] ?></a></td>
							<td><?= $row['account_desc']?></td>
							<td><?= $row['account_group'] ?></td>
							<td><?= $row['account_type'] ?></td>
							<td><?= $row['default_side']=='D'?'DR':'CR' ?></td>
						</tr>
					<? $count++;} ?>
				</tbody>
			</table>
			<div id="loading"></div>
		</div>
		<div style="clear:both;height:5px;"></div>
		<fieldset>
		<legend>Search</legend>
		<input onchange="search_prod(this.value);" type="text" id="search_prodname" name="search_prodname" style="float:left;width:100%;"/>
		</fieldset>
		<script>
			function search_prod(val){
				if(val.length >= 3){
					$.ajax({
					  url: './content/vouchering_ajax.php?execute=chartofaccountlist&search_prodname='+val,
					  async: false,
					  beforeSend:function(){$("#loading").show();},
					  success: function(data) {
						$("#tbl_content").html(data);
						jQuery.tableNavigation();
						//window.scrollTo(0,0);
					  }
					});
				}else{
					alert("Input more than 3 character when searching...");
				}
				
			}
		</script>
	<?
	break;
	case'chartofaccountlist':
		$sql="select * from tbl_chart_of_account where account_code like '%{$_REQUEST['search_prodname']}%' or account_desc like '%{$_REQUEST['search_prodname']}%' or account_group like '%{$_REQUEST['search_prodname']}%' order by account_desc asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
	?>
		<table class="navigateable" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<thead>
				<tr>
					<th>Account Code</th>
					<th>Account Desc</th>
					<th>Group</th>
					<th>Type</th>
					<th>Default Side</th>
				</tr>
			</thead>
			<tbody>
				<? 	
				if(mysql_num_rows($qry)==0){
					echo "<tr>
							<td colspan='4' align='center'>No Records Found...</td>
						</tr>";
				}
				while($row = mysql_fetch_assoc($qry)){ ?>
					<tr>
						<td><a href="javascript:itemSelected('<?php echo $row['account_code'] ?>','<?=$row['account_desc']?>','<?=$row['default_side']?>');" class="activation"><?php echo $row['account_code'] ?></a></td>
						<td><?= $row['account_desc']?></td>
						<td><?= $row['account_group'] ?></td>
						<td><?= $row['account_type'] ?></td>
						<td><?= $row['default_side']=='D'?'DR':'CR' ?></td>
					</tr>
				<? $count++;} ?>
			</tbody>
		</table>
	<?
	break;
}
?>
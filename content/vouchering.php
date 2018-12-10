<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$sessiontype = $_REQUEST['page'];
if($_POST){
	// print_r($_POST)."<br/>";
	// echo serialize($_POST['checkinfo'])."<br/>";
	// print_r(unserialize(serialize($_POST['checkinfo'])))."<br/>";
	// echo "<pre>";
	// echo print_r($_REQUEST['entry']);
	// echo "</pre>";
	// exit;
	mysql_query("BEGIN");
	if(isset($_REQUEST['refid'])&&$_REQUEST['refid']!=0){
		$glref = $_REQUEST['refid'];
		$del = mysql_query("delete from tbl_vouchering where id='$glref' and type='{$_REQUEST['type']}'");
		$del_jnl = mysql_query("delete from tbl_journal_entry where refid='$glref' and type='{$_REQUEST['type']}'");
	}else{
		$glref = $con->getNextJournalID($_REQUEST['type']);
	}
	$sql="insert into tbl_vouchering (id,center,date,type,payee,remarks,particular_array,amount_array,total,preparedby,`status`,typeinfo_array,reference) values 
		('$glref','{$_SESSION['connect']}','{$_REQUEST['xdate']}','{$_REQUEST['type']}','{$_REQUEST['payee']}','{$_REQUEST['remarks']}','".serialize($_REQUEST['particular'])."','".serialize($_REQUEST['amt'])."','".str_replace( ',', '', $_REQUEST['xtotal'])."','".$_SESSION['xid']."','ForApproval','".serialize($_REQUEST[$_REQUEST['type']])."','{$_REQUEST['reference']}') 
		on duplicate key update `date`=values(`date`),center=values(center),type=values(type),payee=values(payee),remarks=values(remarks),particular_array=values(particular_array),amount_array=values(amount_array),total=values(total),typeinfo_array=values(typeinfo_array),reference=values(reference)";	
	$qry1 = mysql_query($sql);
	if(!$qry1){
		echo "Saving Vouchering: ".mysql_error();
	}else{
			$flag=false;
			foreach($_REQUEST['entry'] as $key => $val){ //$i=0;$i<count($_REQUEST['code']);$i++
				$account_info = $db->getWHERE("*","tbl_chart_of_account","where account_code='{$val['code']}'");
				unset($account_info['default_side']);
				$data=array(
					'id'=>$val['id'],
					'refid'=>$glref,
					'center'=>$val['cost_center'],
					'fiscal_year'=>($_REQUEST['fiscal_year']?$_REQUEST['fiscal_year']:date('Y')),
					'date'=>($_REQUEST['xdate']?$_REQUEST['xdate']:date('Y-m-d')),
					'type'=>$_REQUEST['type'],
					'dr'=>preg_replace("/[^0-9.-]/", "", $val['dr_amt']),
					'cr'=>preg_replace("/[^0-9.-]/", "", $val['cr_amt']),
					'ref_id'=>'',
					'payto'=>$val['checkinfo'][$val['code']]['payto'],
					'check_date'=>$val['checkinfo'][$val['code']]['check_date'],
					'check_number'=>$val['checkinfo'][$val['code']]['check_num'],
					'bank'=>$val['checkinfo'][$val['code']]['check_bank'],
					'bank_validation'=>$val['checkinfo'][$val['code']]['bank_validation'],
					'ar_refid'=>$val['ar_refid'],
					'ar_nontrade_refid'=>$val['ar_nontrade_refid'],
					'ar_nontrade_refinfo'=>$val['ar_nontrade_refinfo'],
					'ar_nontrade_remarks'=>$val['ar_nontrade_remarks'],
					'ap_refid'=>$val['ap_refid']
				);
				$rec = array_merge($data,$account_info);
				if($flag)$sql_data.=",";
				$sql_data.= "('".implode("', '", array_map('mysql_real_escape_string', $rec))."')";
				$flag=true;
			}
			$sql = "insert into tbl_journal_entry (`".implode("`,`",array_keys($rec))."`) values $sql_data 
				on duplicate key update "; 
			$flag=false;
			foreach(array_keys($rec) as $a => $b){
				if($flag)$sql.=",";
				$sql.="$b=values($b)";
				$flag=true;
			}
			// echo $sql;
			// exit;
			$qry2=mysql_query($sql);
			if(!$qry2){
				echo "Saving Journal: ".mysql_error();
			}
			if($qry1 && $qry2){
				mysql_query("COMMIT");
				header("location:$location");
			}else{
				mysql_query("ROLLBACK");
			}
		// switch($_REQUEST['type']){
			// case'CRJ':
				// $sql="insert into tbl_customers_trans (cust_id,receipt,transtype,details,amount) values 
				// ('{$_REQUEST[$_REQUEST['type']]['customer_acctnum']}','$glref','PAYMENT','{$_REQUEST['remarks']}','".str_replace( ',', '', $_REQUEST['xtotal'])."') 
					// on duplicate key update details=values(details),amount=values(amount)";
				// $qry=mysql_query($sql);
				// if(!$qry){
					// echo "Error tbl_customers_trans:".mysql_error();
				// }
			// break;
		// }
	}
}
if($_REQUEST['begbal']){
	$info = $db->getWHERE("a.*,b.user certifiedby,c.user approver",
		"tbl_vouchering a left join tbl_user b on a.certifiedcorrect=b.id left join tbl_user c on a.approvedby=c.id",
		"where a.type='BEGBAL'");
	$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$info['id']}' order by dr desc");
}
if($_SESSION["vouchering"]['refid']){
	$info = $db->getWHERE("a.*,b.user certifiedby,c.user approver",
		"tbl_vouchering a left join tbl_user b on a.certifiedcorrect=b.id left join tbl_user c on a.approvedby=c.id",
		"where a.id='{$_SESSION["vouchering"]['refid']}' and COALESCE(a.type,'')='{$_SESSION["vouchering"]['type']}'"); // and COALESCE(a.type,'') like '%{$_SESSION["vouchering"]['type']}%'
	$journal = $db->resultArray("*","tbl_journal_entry","where refid='{$_SESSION["vouchering"]['refid']}' and COALESCE(type,'')='{$_SESSION["vouchering"]['type']}' order by dr desc"); // and COALESCE(a.type,'') like '%{$_SESSION["vouchering"]['type']}%'
	//print_r($journal);
	unset($_SESSION["vouchering"]);
}
?>
<link rel="stylesheet" href="./js/chosen/chosen.css">
<script src="./js/chosen/chosen.jquery.js" type="text/javascript"></script>
<script src="./js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>

<div class="top">
	<form method="post" name="frm_stockin" style="padding:10px 0 10px 0;">
		<div class="header">
			<div>
				<div style="float:left;border:1px solid #000;margin-right:5px;padding:5px;">
					<div style="float:left;margin-right:5px;">
						<?php ?>
						<div style="float:left;margin-right:5px;width:60px;">Journal:</div>
						<select name="type" id="type" style="float:left;width:239px;">
							<?php if($_REQUEST['begbal']==true){ 
								echo '<option value="BEGBAL">Beginning Balance</option>';
							 }else{ 
								$type=$db->resultArray("*","tbl_journal_category","");
								echo '<option value="">Select Type</option>';
								foreach($type as $key => $val){ 
									echo '<option '.($info['type']==$val['code']?"selected":"").' value="'.$val['code'].'">'."({$val['code']}) ".$val['description'].'</option>';
								}
							} ?>
						</select>
						<div style="clear:both;height:5px;"></div>
						<div id="payto_area" style="float:left;margin-right:5px;">
							<div id="lblref" style="float:left;margin-right:5px;width:60px;">Fiscal Yr:</div>
							<input style="float:left;width:235px;" type="text" name="fiscal_year" id="fiscal_year" value="<?=$info?date('Y',strtotime($info['date'])):date('Y')?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:5px;">
							<div style="float:left;margin-right:5px;width:60px;">Voucher #:</div>
							<textarea name="reference" id="reference" style="float:left;width:235px;height:50px;"><?=($info['reference']?$info['reference']:"")?></textarea>
						</div>
					</div>
					<div style="float:left;">
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:50px;">Date:</div>
							<input style="float:left;width:133px;" readonly type="text" name="xdate" id="xdate" value="<?=$info['date']?$info['date']:date('Y-m-d');?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-left:20px;">
							<div style="float:left;margin-right:5px;width:50px;">Entry #:</div>
							<input style="float:left;width:133px;" readonly type="text" name="refid" id="refid" value="<?=$info['id']?>" />
						</div>
						<div style="clear:both;height:5px;"></div>
						<input id="bt15" class="buthov" type="button" value="Remarks" onclick="viewReference();" style="float:right;height:30px;width:190px;"/>
					</div>
					<div style="clear:both;height:5px;"></div>
					<div class="modal" style="float:left;margin-right:5px;display:none;" id="remarks_area">
						<div class="modal-content" style="width:700px;height:250px;">
							<span style="float:left;">Remarks</span><span style="float:right;" class="close" onclick="$('#remarks_area').hide();">&times;</span>
							<div style="clear:both;height:5px;"></div>
							<textarea name="remarks" id="remarks" style="float:left;width:100%;height:200px;"><?=$info['remarks']?></textarea>
							<div style="clear:both;height:5px;"></div>
						</div>
					</div>
				</div>
				<fieldset>
					<legend><strong>TOTAL</strong></legend>
					<input readonly type="text" id="xtotal" name="xtotal" value="<?=($info['total']?number_format($db->sum_array($journal,'cr'),2):"0.00")?>" style="float:right;width:375px;font-size:55px;margin-top:10px;text-align:right;border:none;border-color:transparent;background:transparent;"/>
				</fieldset>
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
					<?php
					if($info['approvedby']){
					?>
						<input id="bt6" class="buthov" type="button" value="Close" onclick="window.location.reload();" style="float:right;height:40px;width:100%;float:left;"/>
						<?if($_REQUEST['page']!="gl"){?>
						<input id="bt4" class="buthov" type="button" value="ChequeEntry" onclick="ChequeEntry(<?=$info['id']?>,<?=$info['total']?>);" style="height:40px;width:150px;"/>
						<? } ?>
					<?}else{ ?>
						<button id="bt1" class="buthov" type="button" onclick="frmSave()" style="height:40px;width:150px;">Save</button>
					<?php } ?>
					<input id="bt3" class="buthov" type="button" value="Add Acct" onclick="ChartOfAccount();" style="height:40px;width:150px;"/>
					<!--input id="bt5" class="buthov" type="button" value="Entry Search" onclick="viewVouchering();" style="height:40px;width:150px;"/-->
					<!--input id="bt5" class="buthov" type="button" value="Entry Search" onclick="showDataFilter(6);" style="height:40px;width:150px;"/-->
					<input id="bt5" class="buthov" type="button" value="Entry Search" onclick="showNewDataFilterNotJqgrid(60);" style="height:40px;width:150px;"/>
					<input id="bt6" class="buthov" type="button" value="Review Entry" onclick="showNewDataFilterNotJqgrid(61);" style="height:40px;width:150px;"/>
					<input id="bt9" class="buthov" type="button" value="Print" onclick="viewReport('./reports/vouchering<?=$repExtension?>.php?refid=<?=$info['id'] ?>&center=<?=$info['center']?>&type=<?=$info['type']?>');" style="height:40px;width:150px;"/>
					<!--input id="bt7" class="buthov" type="button" value="Main Dashboard" onclick="window.location='index.php'" style="height:40px;width:150px;"/-->
				</fieldset>
				<div style="clear:both;height:10px;"></div>
				<fieldset style="padding:5px;text-align:center;border-color:red;">
					<legend style="color:red;">&nbsp; VALIDATION &nbsp;</legend>
					<input id="bt7" class="buthov" type="button" value="Delete" onclick="delJLentry(<?=$info['id']?>,'<?=$info['type']?>')" style="height:40px;width:150px;"/>
					<?php
					if($info['certifiedcorrect']){
						echo "<fieldset><legend style='text-align:left;'>CertifiedCorrect By:</legend>".$info['certifiedby']."</fieldset>";
					}else{ ?>
						<input id="bt8" class="buthov" type="button" value="CertifiedCorrect" onclick="validate(<?=$info['id']?>,'certifiedcorrect');" style="height:40px;width:150px;"/>
					<?php } ?>
					<?php
					if($info['approvedby']){
						echo "<fieldset><legend style='text-align:left;'>Approved By:</legend>".$info['approver']."</fieldset>";
					}else{ ?>
						<input id="bt9" class="buthov" type="button" value="Approved" onclick="validate(<?=$info['id']?>,'approvedby');" style="height:40px;width:150px;"/>
					<?php } ?>
					<input id="bt12" class="buthov" type="button" value="Reject" onclick="reject(<?=$info['id']?>)" style="height:40px;width:150px;"/>
				</fieldset>
				<?php if($info['approvedby']){ ?>
					<fieldset style="padding:5px;text-align:center;border-color:red;">
						<legend style="color:blue;">&nbsp; REPORTS &nbsp;</legend>
							<input id="bt10" class="buthov" type="button" value="Voucher" onclick="viewReport('./reports/vouchering.php?refid=<?php echo $info['id'] ?>')" style="height:40px;width:150px;"/>
							<?if($_REQUEST['page']!="gl"){?>
							<input id="bt11" class="buthov" type="button" value="Cheque" onclick="viewReport('./reports/cheque_printing.php?refid=<?php echo $info['id'] ?>')" style="height:40px;width:150px;"/>
							<? } ?>
					</fieldset>
				<?php } ?>
			</div>
			<div style="float:right;width:800px;">
				<div id="particular_area">
					
				</div>
				<div style="clear:both;height:10px;"></div>
				<div style="display: flex;">
					<fieldset style="align-items: stretch;">
						<legend>Journal Entry</legend>
							<table class="navigateablejournal" id="journalTbl" cellspacing="0" cellpadding="0" width="100%">
								<thead>
									<tr>
										<th style="border:none;width:50px;">&nbsp;</th>
										<th style="border:none;" >Account Code</th>
										<th style="border:none;width:800px;">Account Desc</th>
										<th style="border:none;">Debit</th>
										<th style="border:none;">Credit</th>
									</tr>
								</thead>
								<tbody>
								<?php
								if($journal){
									$sumdr=0;$sumcr=0;
									$count=1;
									foreach($journal as $key=>$val){
										$id="seq$count";
										$chart = $db->getWHERE("*","tbl_chart_of_account","where account_code='{$val['account_code']}'");
										switch($chart['sub_account']){
											case'CASH':
												$modal = '<fieldset><input type="hidden" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][acctcode]" value="'.$val['account_code'].'"/>
														<div style="float:left;margin-right:10px;width:100px;">PayTo</div>
														<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][payto]" value="'.$val['payto'].'" style="float:left;width:150px;"/>
														<div style="clear:both;height:5px;"></div>
														<div style="float:left;margin-right:10px;width:100px;">Check Date</div>
														<input class="check_field xdate" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_date]" value="'.$val['check_date'].'" style="float:left;width:150px;"/>
														<div style="clear:both;height:5px;"></div>
														<div style="float:left;margin-right:10px;width:100px;">Check Number</div>
														<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_num]" value="'.$val['check_number'].'" style="float:left;width:150px;"/>
														<div style="clear:both;height:5px;"></div>
														<div style="float:left;margin-right:10px;width:100px;">Bank</div>
														<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][check_bank]" value="'.$val['bank'].'" style="float:left;width:150px;"/>
														<div style="clear:both;height:5px;"></div>
														<div style="float:left;margin-right:10px;width:100px;">Bank Validation</div>
														<input class="check_field" type="text" name="entry['.$count.'][checkinfo]['.$val['account_code'].'][bank_validation]" value="'.$val['bank_validation'].'" style="float:left;width:150px;"/></fieldset>';
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
											case'ACCOUNTS RECEIVABLE - NONTRADE':
												$option="";
												$customer=$db->resultArray("*,concat(firstname,' ',lastname) name","tbl_employees","");
												foreach($customer as $k=>$v){
													$option.= "<option ".($val['ar_nontrade_refid']==$v['id']?"selected":"")." value='".$v['id']."'>".$v['name']."</option>";
												}
												$modal = '<div style="float:left;margin-right:10px;width:100px;">Select Employee</div>'.
													'<select name="entry['.$count.'][ar_nontrade_refid]" style="float:left;width:250px;">'.
													'<option value="">Select Select Employee</option>'.$option.'</select>
													<div style="clear:both;height:5px;"></div>'.
													'<div style="float:left;margin-right:10px;width:100px;">Reference Number</div>'.
													'<input type="text" name="entry['.$count.'][ar_nontrade_refinfo]" value="'.$val['ar_nontrade_refinfo'].'" style="float:left;width:250px;"/>'.
													'<div style="clear:both;height:5px;"></div>'.
													'<div style="float:left;margin-right:10px;width:100px;">Remarks</div>'.
													'<input type="text" name="entry['.$count.'][ar_nontrade_remarks]" value="'.$val['ar_nontrade_remarks'].'" style="float:left;width:250px;"/>'.
													'<div style="clear:both;height:5px;"></div>';
											break;
										}
										if($chart['account_group']=="EXPENSES"){
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
										echo '<tr id="'.$id.'">
											<td><input type="checkbox" style="float:left;"><img onclick="viewAddInfo(\''.$id.'\')" src="./images/cashdetails.png" style="float:left;width:20px;height:20px;" title="More Info"/></td>
											<td><input readonly type="text" name="entry['.$count.'][code]" value="'.$val['account_code'].'" style="width:100px;"/></td>
											<td><input readonly type="text" name="entry['.$count.'][desc]" value="'.$val['account_desc'].'" style="width:100%;"/></td>
											<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="entry['.$count.'][dr_amt]" style="text-align:right;background:transparent;border:none;width:100px;" value="'.($val['dr']!=0?number_format($val['dr'],2):"").'"/></td>
											<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="entry['.$count.'][cr_amt]" style="text-align:right;background:transparent;border:none;width:100px;" value="'.($val['cr']!=0?number_format($val['cr'],2):"").'"/></td>
											<td class="modal" style="display:none;"><div class="modal-content">'.$close.$modal.'</div></td>
											<td style="display:none;"><input type="hidden" name="entry['.$count.'][id]" value="'.$val['id'].'"/></td>
										</tr>';
										
										$sumdr+=$val['dr'];$sumcr+=$val['cr'];
										$count++;
									}
								}
								?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="3">Total</th>
										<th id="dr_amt_total" style="text-align:right;"><?=number_format($sumdr,2)?></th>
										<th id="cr_amt_total" style="text-align:right;"><?=number_format($sumcr,2)?></th>
									</tr>
								</tfoot>
							</table>
					</fieldset>
				</div>
				<div style="clear:both;height:5px;"></div>
				<? if($info['notes']){?>
					<fieldset>
						<legend>NOTES</legend>
						<?=$info['notes']?>
					</fieldset>
				<?}?>
			</div>
		</div>
	</form>
	<div style="clear:both;height:20px;"></div>
</div>
<script>
var default_AR_code = '<?=$_SESSION['settings']['default_AR_code']?>';
var default_AP_code = '<?=$_SESSION['settings']['default_AP_code']?>';
var default_CASH_code = '<?=$_SESSION['settings']['default_CASH_code']?>';
$(document).ready(function() {
	$('#xdate').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	var defaulttype = getParam('defaulttype');
	if(defaulttype){
		$('select[name="type"]').val(defaulttype);
		autoJournalEntry(defaulttype);
	}
	window.scrollTo(0,0);
	sumCRAmt();
});
$("input[name='dr_amt[]'], input[name='cr_amt[]']").live('change',function(){
	$(this).val(new Number($(this).val()).formatMoney(2));
});
$("input[name='dr_amt[]'], input[name='cr_amt[]']").on('change',function(){
	$(this).val(new Number($(this).val()).formatMoney(2));
});
function delJLentry(id,type){
	$.ajax({
		url: './content/vouchering_ajax.php?execute=delJLentry&refid='+id+'&type='+type,
		type:"POST",
		success:function(data){
			if(data=="success"){
				alert("Successfully deleted...");
				window.location.reload();
			}
		}
	});
}
function checkType(val){
	$.ajax({
		url: './content/vouchering_ajax.php?execute=particularSelection&type='+val,
		type:"POST",
		success:function(data){
			$("#particular_area").html(data);
		}
	});
}
$("#type").change(function(){
	//autoJournalEntry();
});
function viewReference(){
	$("#remarks_area").show();
}
function autoJournalEntry(mytype=""){
	var type = mytype!=""?mytype:$('select[name="type"]').val();
	var amount = parseFloat($("#xtotal").val().replace(',',''));
	var num =$("#journalTbl tbody tr").length; //count the tr for sequencial id
	var id='';
	if(amount==0){
		$("#journalTbl tbody").html("");
		switch(type){
			case'CDJ':
				id='seq'+(num+1); //generate sequence id
				var dr = '<tr id="'+id+'">\
					<td><input type="checkbox" style="float:left;"></td>\
					<td><input readonly type="text" name="code[]" value="'+default_AP_code+'" style="width:100px;"/></td>\
					<td><input readonly type="text" name="desc[]" value="ACCOUNTS PAYABLE - TRADE" style="width:100%;"/></td>\
					<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+amount+'"/></td>\
					<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value=""/></td>\
					<td class="modal" style="display:none;"></td>\
				</tr>';
				$("#journalTbl tbody").append(dr);
				getAddInfo(id);
				id='seq'+(num+2); //change sequence id
				var cr = '<tr id="'+id+'">\
					<td><input type="checkbox" style="float:left;"></td>\
					<td><input readonly type="text" name="code[]" value="'+default_CASH_code+'" style="width:100px;"/></td>\
					<td><input readonly type="text" name="desc[]" value="CASH IN BANK" style="width:100%;"/></td>\
					<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value=""/></td>\
					<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+amount+'"/></td>\
					<td class="modal" style="display:none;"></td>\
				</tr>';
				$("#journalTbl tbody").append(cr);
				getAddInfo(id);
			break;
			case'CRJ':
				id='seq'+(num+1); //generate sequence id
				var dr = '<tr id="'+id+'">\
					<td><input type="checkbox" style="float:left;"></td>\
					<td><input readonly type="text" name="code[]" value="'+default_CASH_code+'" style="width:100px;"/></td>\
					<td><input readonly type="text" name="desc[]" value="CASH IN BANK" style="width:100%;"/></td>\
					<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+amount+'"/></td>\
					<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value=""/></td>\
					<td class="modal" style="display:none;"></td>\
				</tr>';
				$("#journalTbl tbody").append(dr);
				getAddInfo(id);
				id='seq'+(num+2); //change sequence id
				var cr = '<tr id="'+id+'">\
					<td><input type="checkbox" style="float:left;"></td>\
					<td><input readonly type="text" name="code[]" value="'+default_AR_code+'" style="width:100px;"/></td>\
					<td><input readonly type="text" name="desc[]" value="ACCOUNTS RECEIVABLE" style="width:100%;"/></td>\
					<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="dr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value=""/></td>\
					<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="cr_amt[]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+amount+'"/></td>\
					<td class="modal" style="display:none;"></td>\
				</tr>';
				$("#journalTbl tbody").append(cr);
				getAddInfo(id);
			break;
		}
		sumDRAmt();
		sumCRAmt();
	}
}
function frmSave(){
	var frm = document.frm_stockin;
	var begbal = getParam('begbal');
	if(begbal=='true'){
		frm.submit();
	}else{
		var xtotal = parseFloat($("#xtotal").val().replace(',',''));
		if(xtotal==0){
			alert("Pls be more particular....");
			return false;
		}
		// switch($("#type").val()){
			// case'CRJ':
				// var total_amt=0;
				// $('input[name="CRJ[cashorcheck][]"]:checked').each(function() {
				   // var val =  $('input[name="CRJ['+this.value+'_amt]"]').val();
				   // if(val==""){
						// alert(this.value+" no amount...");
						// return false;
				   // }else{
					   // total_amt += parseFloat($('input[name="CRJ['+this.value+"_amt"+']"]').val());
				   // }
				// });
				
				// if(parseFloat(total_amt)!=xtotal){
					// console.log("TotalAmt:"+parseFloat(total_amt));
					// console.log("xTotal:"+xtotal);
					// alert("Payment is not equal to total...");
					// return false;
				// }
				
			// break;
		// }
		
		if($("#type").val()==""){
			alert("Pls choose Journal Type...");
			return false;
		}
		if($("#dr_amt_total").text()==""||$("#cr_amt_total").text()==""){
			alert("Don't forget the Journal Entry....");
			return false;
		}
		if(parseFloat($("#dr_amt_total").text())!=parseFloat($("#cr_amt_total").text())){
			alert("Debit and Credit should be equal....");
			return false;
		}
		if(parseFloat($("#dr_amt_total").text().replace(',',''))!=xtotal){
			alert("Journal Amount should be equal to Total Amount....");
			return false;
		}
		frm.submit();
	}
}
// function viewReport(page){
	// if (window.showModalDialog) {
		// window.showModalDialog(page,"PO","dialogWidth:650px;dialogHeight:650px");
	// } else {
		// window.open(page,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	// }
// }
function ChequeEntry(refid,amt){
	var urls = getUrl();
	clickDialog('dialogbox2',500,200,'ChequeEntry&refid='+refid+'&amt='+amt,'Cheque Details',urls,'vouchering_ajax.php');
}
// function addPayee(){
	// var urls = getUrl();
	// clickDialog('dialogbox2',1000,500,'addPayee','Add Payee',urls,'vouchering_ajax.php');
// }
function validate(id,type){
	$.ajax({
		url: './content/vouchering_ajax.php?execute=validate&refid='+id+'&type='+type,
		type:"POST",
		success:function(data){
			alert(data);
			if(data=="success"){
				window.location.reload();
			}
			
		}
	});
}
function reject(id){
	var urls = getUrl();
	clickDialog('dialogbox2',400,300,'rejectView&id='+id,'Reject Voucher',urls,'vouchering_ajax.php');
}
/*$("#mytbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		//$("tr.selected").remove();
		$('input[type="checkbox"]:checked').closest("tr").remove();
	}
});*/
$("#journalTbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		//$("tr.selectedjournal").remove();
		$('input[type="checkbox"]:checked').closest("tr").remove();
		sumDRAmt();
		sumCRAmt();
	}
	//event.preventDefault();
});
var coaAdd=true;
function itemSelected(code,desc,defside){
	var num =$("#journalTbl tbody tr").length; //count the tr for sequencial id
	var id='';
	if(coaAdd){
		id='seq'+(num+1); //generate sequence id
		var txt = '<tr id="'+id+'">'+
				'<td><input type="checkbox" style="float:left;"></td>'+
				'<td><input readonly type="text" name="entry['+(num+1)+'][code]" value="'+code+'" style="width:100px;"/></td>'+
				'<td><input readonly type="text" name="entry['+(num+1)+'][desc]" value="'+desc+'" style="width:100%;"/></td>'+
				'<td><input onchange="sumDRAmt()"  type="text" class="dr_amt" name="entry['+(num+1)+'][dr_amt]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+(defside=='D'?'0.00':'')+'"/></td>'+
				'<td><input onchange="sumCRAmt()" type="text" class="cr_amt" name="entry['+(num+1)+'][cr_amt]" style="text-align:right;background:transparent;border:none;width:100px;" value="'+(defside=='C'?'0.00':'')+'"/></td>'+
				'<td class="modal" style="display:none;"></td>'+
			'</tr>';
		$("#journalTbl tbody").append(txt);
		getAddInfo((num+1));	
		coaAdd=false;
	}
	$('#prodlist').dialog('close');
	//event.stopPropagation();
	//event.stopImmediatePropagation();
	//jQuery.tableNavigationJournal();
	//event.preventDefault();
}
function getAddInfo(id){
	var code =$('#seq'+id).find("td input[name='entry["+id+"][code]']").val();
	var desc = $('#seq'+id).find("td input[name='entry["+id+"][desc]']").val();
	var icon = '<img onclick="viewAddInfo(\'seq'+id+'\')" src="./images/cashdetails.png" style="float:left;width:20px;height:20px;"/>';
	$.ajax({
		url: './content/vouchering_ajax.php?execute=addInfo&acctcode='+code+'&id='+id+'&desc='+desc,
		type:"POST",
		success:function(data){
			if(data!=''){
				$('#seq'+id).find("td").eq(0).append(icon);
				$('#seq'+id).find("td").eq(5).html(data);
			}
		}
	});
}
function viewAddInfo(id){
	$('#'+id).find("td").eq(5).show();
	$('.xdate').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
}
function closeModal(id){
	$('#'+id).find("td").eq(5).hide();
}
function ChartOfAccount(){
	coaAdd=true;
	var urls = getUrl();
	clickDialog('prodlist',1000,500,'chartofaccount','Chart of Account',urls,'vouchering_ajax.php');
	jQuery.tableNavigation();
}
function addParticular(e,arg){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if (chCode == 13) {
		var txt = '<tr>\
					<td><input type="text" name="particular[]" value="'+arg.value+'" style="width:100%;border:none;background:transparent;"/></td>\
					<td><input onchange="sumVoucherAmt()" type="text" class="amt" name="amt[]" style="text-align:right;background:transparent;border:none;" value="0.00"/></td>\
				</tr>';
		$("#mytbl tbody").prepend(txt);
		arg.value="";
		sumVoucherAmt();
	}
}

function viewVouchering(){
	var urls = getUrl();
	clickDialog('prodlist',1000,500,'view_vouchering','View Records',urls,'vouchering_ajax.php');
	jQuery.tableNavigation();
}
function sumVoucherAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = strtodouble($(this).val());
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#xtotal").val(new Number(sum).formatMoney(2));
}
function sumDRAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".dr_amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = strtodouble($(this).val());
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#dr_amt_total").html(new Number(sum).formatMoney(2));
}
function sumCRAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".cr_amt").each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = strtodouble($(this).val());
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	//return new Number(sum).formatMoney(2);
	$("#cr_amt_total").html(new Number(sum).formatMoney(2));
	//if($("#type").val()=="General Ledger"){
		$("#xtotal").val(new Number(sum).formatMoney(2));
	//}
}
</script>
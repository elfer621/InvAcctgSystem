<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
set_time_limit(0);
session_start();
header("Access-Control-Allow-Origin: *");
require_once"../settings.php";
require_once"../class/dbUpdate.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
switch($_REQUEST['execute']){
	case'new_data_filter':
		switch($_REQUEST['fltr_refid']){
			case 53: //Sales Summary
				$tbl['column_master']="center|Cost Center,counter|Station,date|Date";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("Campus","=","");
					$defVal[2] = array("counter","=","");
					$defVal[3] = array("date",">=",date('Y-m-01'));
					$defVal[4] = array("date","<=",date('Y-m-d'));
				}
				$reportname = "./reports/sales_reports_lizgan.php";
				//$selection['Campus']=array(array("ucmain","UC-Main"),array("ucmambaling","METC"),array("ucbanilad","UC-Banilad"),array("uclm","UCLM"));
				$selection['center']=$db->resultSingleArray("cost_center,cost_center","tbl_cost_center","");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 54: //Sales Receipt
				$tbl['column_master']="center|Cost Center|Station,date|Date,isVoid,receipt|Receipt,instructor_code|NoTeacherCode";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("counter_num","=","");
					$defVal[2] = array("date",">=",date('Y-m-01'));
					$defVal[3] = array("date","<=",date('Y-m-d'));
				}
				$reportname = "./reports/sales_journal.php";
				//$selection['Campus']=array(array("ucmain","UC-Main"),array("ucmambaling","METC"),array("ucbanilad","UC-Banilad"),array("uclm","UCLM"));
				$selection['center']=$db->resultSingleArray("cost_center,cost_center","tbl_cost_center","");
				$selection['isVoid']=array(array("Y","Yes"),array("N","No"));
				$selection['instructor_code']=array(array("Y","Yes"),array("N","No"));
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 60: //Journal Entry Search
				$tbl['column_master']="fiscal_year|FY,date|Entry Date,type|Journal,refid|Entry #,reference|Reference,
				remarks|Particulars,account_code|Account,account_desc|Account Desc,account_group|Account Group,check_number|Check Ref,check_date|Check Date,payto|Payee";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("date",">=",date('Y-m-01'));
					$defVal[2] = array("date","<=",date('Y-m-d'));
					$defVal[3] = array("type","=","");
					$defVal[4] = array("refid","=","");
					$defVal[5] = array("reference","like","");
				}
				$reportname = "./content/vouchering_filter.php";
				$selection['type']=$db->resultSingleArray("code,code","tbl_journal_category","");
				$selection['account_code']=$db->resultSingleArray("account_code,account_desc","tbl_chart_of_account"," order by account_desc asc");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 61: //Review Entry
				$tbl['column_master']="fiscal_year|FY,begdate|Start Date,enddate|End Date,type|Journal,refid|Entry #,reference|Reference,
				remarks|Particulars,account_code|Account,account_desc|Account Desc,account_group|Account Group,check_number|Check Ref,check_date|Check Date,payto|Payee";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("refid","=","");
				}
				$reportname = "./reports/ledger_report.php";
				$selection['account_code']=$db->resultSingleArray("account_code,account_desc","tbl_chart_of_account"," order by account_desc asc");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 62: //inventory audit
				$tbl['column_master']="product_name|Product Name,ending_bal|Ending Bal,supplier_id|Supplier Name,Campus,begdate|Start Date,enddate|End Date";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("begdate",">=",date('Y-m-01'));
					$defVal[2] = array("enddate","<=",date('Y-m-d'));
					$defVal[3] = array("product_name","like","");
					$defVal[4] = array("supplier_id","=","");
				}
				$reportname = "./reports/inventory_audit.php";
				$selection['supplier_id']=$db->resultSingleArray("id,supplier_name","tbl_supplier"," order by supplier_name asc");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 63: //Ledger Summary
				$tbl['column_master']="fiscal_year|FY,begdate|Start Date,enddate|End Date,type|Journal,refid|Entry #,reference|Reference,
				remarks|Particulars,account_code|Account,account_desc|Account Desc,sub_account|Sub Account,account_group|Account Group,
				check_number|Check Ref,check_date|Check Date,payto|Payee,center|Cost Center,ar_refid|Debtors/Customers,ap_refid|Creditors/Suppliers";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("begdate",">=",date('Y-m-01'));
					$defVal[2] = array("enddate","<=",date('Y-m-d'));
					$defVal[3] = array("account_code","=","");
					$defVal[4] = array("sub_account","=","");
				}
				$reportname = "./reports/ledger_summary.php";
				$selection['account_code']=$db->resultSingleArray("account_code,account_desc","tbl_chart_of_account"," order by account_desc asc");
				$selection['sub_account']=$db->resultSingleArray("sub_account,sub_account","tbl_chart_of_account_subaccount"," order by sub_account asc");
				$selection['ar_refid']=$db->resultSingleArray("cust_id,customer_name","tbl_customers"," order by customer_name asc");
				$selection['ap_refid']=$db->resultSingleArray("id,supplier_name","tbl_supplier"," order by supplier_name asc");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 64: //Trial Balance
				$tbl['column_master']="fiscal_year|FY,begdate|Beg Date,enddate|End Date";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("fiscal_year","=",date('Y'));
					$defVal[2] = array("begdate",">=",date('Y-m-01'));
					$defVal[3] = array("enddate","<=",date('Y-m-d'));
				}
				$reportname = "./reports/fs_trial_balance.php";
				$comparison=array("Equals"=>"=","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=");
			break;
			case 65: //Income Statement
				$tbl['column_master']="fiscal_year|FY,date|Entry Date,type|Journal,center|Cost Center";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("date",">=",date('Y-m-01'));
					$defVal[2] = array("date","<=",date('Y-m-d'));
					$defVal[3] = array("type","=","SJ");
					$defVal[4] = array("center","=","");
				}
				$reportname = "./reports/fs_income_statement.php";
				$selection['type']=$db->resultSingleArray("code,code","tbl_journal_category","");
				$selection['center']=$db->resultSingleArray("cost_center,cost_center","tbl_cost_center","");
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
			case 66: //Balance Sheet
				$tbl['column_master']="fiscal_year|FY,asofdate|Date As Of";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("fiscal_year","=",date('Y'));
					$defVal[2] = array("asofdate","<=",date('Y-m-d'));
				}
				$reportname = "./reports/fs_balance_sheet.php";
				$comparison=array("Equals"=>"=","Lesser Than or Equals"=>"<=");
			break;
			case 67: //Accounts Receivable
			
			break;
			case 68: //Sales Receipt Lab
				$tbl['column_master']="counter_num|Station,date|Date,isVoid,receipt|Receipt,instructor_code|NoTeacherCode";
				if($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]){
					foreach($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']] as $key => $val){
						$rec=explode("|",trim(str_replace(array("` ","`"," '","'","%"),array("`|","'","|'","",""),$val)));
						$defVal[$key+1]=$rec;
					}
				}else{
					$defVal[1] = array("counter_num","=","");
					$defVal[2] = array("date",">=",date('Y-m-01'));
					$defVal[3] = array("date","<=",date('Y-m-d'));
				}
				$reportname = "./reports/sales_journal_lab.php";
				//$selection['Campus']=array(array("ucmain","UC-Main"),array("ucmambaling","METC"),array("ucbanilad","UC-Banilad"),array("uclm","UCLM"));
				$selection['isVoid']=array(array("Y","Yes"),array("N","No"));
				$selection['instructor_code']=array(array("Y","Yes"),array("N","No"));
				$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			break;
		}
		
		if($_REQUEST['selection']){
			if($selection[$_REQUEST['field']]){
				echo "<select class='val' name='val[]' style='width:100%;'>";
				echo "<option value=''>&nbsp;</option>";
				foreach($selection[$_REQUEST['field']] as $key => $val){
					echo "<option value='".$val[0]."'>".$val[1]."</option>";
				}
				echo "</select>";
			}
			else{
				echo '<input class="val '.($db->strpos_arr($_REQUEST['field'],array("date","begdate","enddate","StartDate","EndDate"))!==false?"xdate":"").'" type="text" name="val[]" style="text-align:right;width:100%;" value=""/>';
			}
		}else{
			//$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
			?>
			<div style="width:100%;">
				<table class="tbl" id="tbl_datafilter" cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th style="min-width:200px;">Field</th>
							<th>Comparison</th>
							<th>Value</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php for($x=1;$x<=7;$x++){ ?>
						<tr id="tr<?=$x?>">
							<td>
								<select name="field[]" style="width:100%;">
									<option value=""></option>
									<?php foreach(explode(",",$tbl['column_master']) as $key => $val){
										$col = explode("|",$val);
										if(count($col)>1){
											echo "<option value='{$col[0]}' ".($defVal[$x][0]==$col[0]?" selected ":"").">{$col[1]}</option>";
										}else{
											echo "<option value='$val' ".($defVal[$x][0]==$val?" selected ":"").">$val</option>";
										}
										
									} ?>
								</select>
							</td>
							<td>
								<select name="comparison[]" style="width:100%;">
									<option value=""></option>
									<?php foreach($comparison as $key => $val){
										echo "<option value='$val' ".($defVal[$x][1]==$val?" selected ":"").">$key</option>";
									} ?>
								</select>
							</td>
							<?php if($selection[$defVal[$x][0]]){ 
								echo "<td><select class='val' name='val[]' style='width:100%;'>";
								echo "<option value=''>&nbsp;</option>";
								foreach($selection[$defVal[$x][0]] as $key => $val){
									echo "<option value='".$val[0]."' ".($defVal[$x][2]==$val[0]?" selected ":"").">".$val[1]."</option>";
								}
								echo "</select></td>";
							}else{ ?>
								<td><input class="val <?=$db->strpos_arr($defVal[$x][0],array("date","Date"))!== false?"xdate":""?>" type="text" name="val[]" style="text-align:right;width:100%;" value="<?=$defVal[$x][2]?>"/></td>
							<?php } ?>
							<td><input type="button" onclick="defSelection(<?=$x?>);" value="..." style="width:20px;height:20px;"/></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<input type="hidden" name="report_name" value="<?=$reportname?>"/>
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="Execute" onclick="executeDataFilterNew();" style="height:30px;width:100px;float:left;"/>
				<input type="button" value="Clear" onclick="clearFltrSession();" style="height:30px;width:100px;float:right;"/>
				<script>
					$(document).ready(function(){
						$('.xdate').datepicker({
							inline: true,
							changeMonth: true,
							changeYear: true,
							dateFormat:"yy-mm-dd"
						});
					});
					function executeDataFilterNew(){
						var where = getDataFilterValue();
						// if(where){ //if return true
							// var report_name = $("input[name='report_name']").val();
							// viewReport(report_name+'?where='+where);
						// }
						var report_name = $("input[name='report_name']").val();
						viewReport(report_name+'?where='+where);
						//save to session the filter
						var id=getParam('fltr_refid');
						$.ajax({
							url: './content/jqgrid_ajax.php?execute=saveToSessionFilter&fltr_refid='+id+'&where='+where,
							type:"POST",
							success:function(data){
								
							}
						});
					}
					function clearFltrSession(){
						var id=getParam('fltr_refid');
						$.ajax({
							url: './content/jqgrid_ajax.php?execute=clearFltrSession&fltr_refid='+id,
							type:"POST",
							success:function(data){
								showNewDataFilterNotJqgrid(id);
							}
						});
					}
					function defSelection(trid){
						var field = $("#tr"+trid).find('td:eq(0)').find("select[name='field[]']").val();
						var id=getParam('fltr_refid');
						$.ajax({
							url: './content/jqgrid_ajax.php?execute=new_data_filter&fltr_refid='+id+'&field='+field+'&selection=true',
							type:"POST",
							success:function(data){
								$("#tr"+trid).find('td:eq(2)').html(data);
								$('.xdate').datepicker({
									inline: true,
									changeMonth: true,
									changeYear: true,
									dateFormat:"yy-mm-dd"
								});
							}
						});
					}
					$("select[name='field[]']").change(function(){
						switch($(this).val()){
							case'begdate':
							case'enddate':
							case'StartDate':
							case'EndDate':
							case'Date':
							case'date':
								$(this).closest('tr').find('td:eq(1)').find("select[name='comparison[]']").val(">=");
								$(this).closest('tr').find('td:eq(2)').find("input[name='val[]']").val($.datepicker.formatDate('yy-mm-dd', new Date()));
								$(this).closest('tr').find('td:eq(2)').find("input[name='val[]']").datepicker({
									changeMonth: true,
									changeYear: true,
									inline: true,
									dateFormat:"yy-mm-dd"
								});
							break;
						}
						console.log($(this).val());
						var trid = $(this).closest('tr').attr('id');
						defSelection(trid.replace("tr",""));
					});
				</script>
			<?
		}
	break;
	case'saveToSessionFilter':
		$output = preg_split( "/ (and) /", $_REQUEST['where'] );
		$_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]=$output;
		echo "<pre>";
		print_r($output);
		echo "</pre>";
		
	break;
	case'clearFltrSession':
		unset($_SESSION['fltr_refid'][$_REQUEST['fltr_refid']]);
	break;
	case'data_filter':
		if($_REQUEST['refid']==50){ //Accounting Reports
			$tbl['column_master']="date";
		}elseif($_REQUEST['refid']==51){ //AR
			$tbl['column_master']="Date,CustomerName";
		}elseif($_REQUEST['refid']==52){ //Ledger
			$tbl['column_master']="fiscal_year|FY,date|Entry Date,type|Journal,refid|Entry #,reference|Reference,
				remarks|Particulars,account_code|Account,account_desc|Account Desc,account_group|Account Group,check_number|Check Ref,check_date|Check Date,payto|Payee";
		}elseif($_REQUEST['refid']==53){ //Sales Summary
			$tbl['column_master']="date|Date,campus|Campus";
		}else{
			$tbl = $db->getWHERE("*","tbl_master_details","where id='{$_REQUEST['refid']}'");
		}
		$comparison=array("Equals"=>"=","Not Equals"=>"!=","Greater Than"=>">","Lesser Than"=>"<","Greater Than or Equals"=>">=","Lesser Than or Equals"=>"<=","Contains"=>"like");
		?>
		<div style="width:500px;height:280px;">
			<table class="tbl" id="tbl_datafilter" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th>Field</th>
						<th>Comparison</th>
						<th>Value</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php for($x=1;$x<=7;$x++){ ?>
					<tr id="tr<?=$x?>">
						<td>
							<select name="field[]" style="width:100%;">
								<option value=""></option>
								<?php foreach(explode(",",$tbl['column_master']) as $key => $val){
									$col = explode("|",$val);
									if(count($col)>1){
										echo "<option value='{$col[0]}'>{$col[1]}</option>";
									}else{
										echo "<option value='$val'>$val</option>";
									}
									
								} ?>
							</select>
						</td>
						<td>
							<select name="comparison[]" style="width:100%;">
								<option value=""></option>
								<?php foreach($comparison as $key => $val){
									echo "<option value='$val'>$key</option>";
								} ?>
							</select>
						</td>
						<td><input class="val" type="text" name="val[]" style="width:100%;"/></td>
						<td><input type="button" onclick="getSuggestedVal(<?=$x?>)" value="..." style="width:20px;height:20px;"/></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div style="clear:both;height:5px;"></div>
			<input type="button" value="Execute" onclick="executeDataFilter(<?=$_REQUEST['refid']?>);" style="height:30px;width:100px;"/>
		</div>
		<script>
			$(document).ready(function(){
				$("input[name='connection']:radio").change(function(){
					con=$("input[name='connection']:checked").val();
					console.log(con);
				});
				getDefaultTemp();
			});
			function getSuggestedVal(trid){
				//alert($("#tr"+trid).index());
				var where = getDataFilterValue($("#tr"+trid).index());
				var field = $("#tr"+trid).find('td:eq(0)').find("select[name='field[]']").val();
				var id=getParam('refid');
				$.ajax({
					url: './content/jqgrid_ajax.php?execute=getSuggestedVal&refid='+id+'&field='+field+'&con='+con+'&where='+where,
					type:"POST",
					success:function(data){
						$("#tr"+trid).find('td:eq(2)').html(data);
					}
				});
			}
			function getDefaultTemp(){
				var id=getParam('refid');
				$.ajax({
					url: './content/jqgrid_ajax.php?execute=defaultTemp&refid='+id,
					type:"POST",
					dataType:"json",
					success:function(data){
						console.log(data);
						$.each( data, function( key, val ) {
							$('tr:eq('+(key+1)+')').find('td:eq(0)').find("select[name='field[]']").val(val[0]);
							$('tr:eq('+(key+1)+')').find('td:eq(1)').find("select[name='comparison[]']").val(val[1]);
							$('tr:eq('+(key+1)+')').find('td:eq(2)').find("input[name='val[]']").val(val[2]);
							if(val[0]=="date"){
								$('tr:eq('+(key+1)+')').find('td:eq(2)').find("input[name='val[]']").datepicker({
									changeMonth: true,
									changeYear: true,
									inline: true,
									dateFormat:"yy-mm-dd"
								});
							}
						});
					}
				});
			}
		</script>
		<script>
			$("select[name='field[]']").change(function(){
				//alert($(this).closest('tr').index());
				switch($(this).val()){
					case'StartDate':
					case'EndDate':
					case'Date':
					case'date':
						$(this).closest('tr').find('td:eq(1)').find("select[name='comparison[]']").val(">=");
						$(this).closest('tr').find('td:eq(2)').find("input[name='val[]']").val($.datepicker.formatDate('yy-mm-dd', new Date()));
						$(this).closest('tr').find('td:eq(2)').find("input[name='val[]']").datepicker({
							changeMonth: true,
							changeYear: true,
							inline: true,
							dateFormat:"yy-mm-dd"
						});
					break;
				}
				
			});
		</script>
		<?
	break;
	case'getSuggestedVal':
		if($_REQUEST['con']!="undefined"){
			$db->openDb($_REQUEST['con']);
		}
		if($_REQUEST['refid']==52){
			$sql="select distinct `{$_REQUEST['field']}` from (select a.*,b.remarks,b.reference from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id) tbl ".($_REQUEST['where']?"where {$_REQUEST['where']}":"")." order by `{$_REQUEST['field']}` asc";
		}else{
			$tbl = $db->getWHERE("*","tbl_master_details","where id='{$_REQUEST['refid']}'");
			$sql = "select distinct `{$_REQUEST['field']}` from {$tbl['tbl_master']} ".($_REQUEST['where']?"where {$_REQUEST['where']}":"")." order by `{$_REQUEST['field']}` asc";
		}
		//echo $sql;
		$qry = mysql_query($sql);
		echo "<select class='val' name='val[]' style='width:100%;'>";
		while($row=mysql_fetch_assoc($qry)){
			echo "<option value='".$row[$_REQUEST['field']]."'>".$row[$_REQUEST['field']]."</option>";
		}
		echo "</select>";
	break;
	case'defaultTemp':
		if($_REQUEST['refid']==52){ //Ledger
			$rec['default_template']="date,>=,|date,<=,|type,=,";
		}else{
			$rec = $db->getWHERE("default_template","tbl_master_details","where id='{$_REQUEST['refid']}'");
		}
		$x=explode("|",$rec['default_template']);
		foreach($x as $k=>$v){
			$y=explode(",",$v);
			if($y[0]=="date"){
				$y[2]=date('Y-m-d');
			}
			$res[]=array($y[0],$y[1],$y[2]);
		}
		echo json_encode($res);
	break;
	case'jqConfig':
		$tbl = $db->getWHERE("*","tbl_master_details","where id='{$_REQUEST['refid']}'");
		foreach(explode(",",$tbl[$_REQUEST['col']]) as $key=>$val){
			$col = explode("|",$val);
			if(count($col)>1){
				$x = $col[1];
				$origCol=$col[0];
			}else{
				$x = $val;
				$origCol=$val;
			}
			$cN[]=$x;
			
			if($origCol=="item_desc"||$origCol=="studentname"||$origCol=="unit"||$origCol=="account_desc"){
				$colM[]=array("name"=>$origCol,"index"=>$origCol);
			}elseif($origCol=="dr"||$origCol=="cr"){
				$colM[]=array("name"=>$origCol,"index"=>$origCol,'align'=>"right",'formatter'=>"number");
			}else{
				$colM[]=array("name"=>$origCol,"index"=>$origCol,'align'=>"right",'formatter'=>"currencyFmatter");
			}
		}
		$rec['colNames']=$cN; //explode(",",$tbl[$_REQUEST['col']])
		$rec['colModel']=$colM;
		$rec['title']=$tbl['title'];
		$rec['isgrouping']=$tbl['isgrouping'];
		echo json_encode($rec);
	break;
	case'master':
		$tbl = $db->getWHERE("*","tbl_master_details","where id='{$_REQUEST['refid']}'");
		$sql = "SELECT * FROM {$tbl['tbl_master']} ".($_REQUEST['q']?"where {$_REQUEST['q']}":"");
		//echo $sql;
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		$i=0;
		if(($_REQUEST['con']&&$_REQUEST['con']!="undefined")){
			$con->getBranch2($_REQUEST['con']);
			$arrs=array();
			$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
			$list = array();
			foreach($arrs as $arr) {
				if(is_array($arr)) {
					$list = array_merge($list, $arr);
				}
			}
		}elseif($_SESSION['settings']['mode']=="branch"||isset($_SESSION['connect'])){
			$con->getBranch();
			$arrs=array();
			$arrs[]=$con->pdoStyle($con->ipadd,$con->dbname,$sql);
			$list = array();
			foreach($arrs as $arr) {
				if(is_array($arr)) {
					$list = array_merge($list, $arr);
				}
			}
		}else{
			$arrs=array();
			$arrs[]=$con->pdoStyle($con->con_main['ip'],$con->con_main['db'],$sql);
			$arrs[]=$con->pdoStyle($con->con_ucmambaling['ip'],$con->con_ucmambaling['db'],$sql);
			$arrs[]=$con->pdoStyle($con->con_uclm['ip'],$con->con_uclm['db'],$sql);
			$arrs[]=$con->pdoStyle($con->con_ucmain['ip'],$con->con_ucmain['db'],$sql);
			//$arrs[]=$con->pdoStyle($con->con_ucbanilad['ip'],$con->con_ucbanilad['db'],$sql);
			$list = array();
			foreach($arrs as $arr) {
				if(is_array($arr)) {
					$list = array_merge($list, $arr);
				}
			}
		}
		// $r = mysql_query("SELECT DATABASE()");
		// echo mysql_result($r,0)."<br/>";
		//print_r($list);
		foreach($list as $ref => $row){
			$id="";$flag=false;
			foreach(explode("-",$tbl['primary_col_master']) as $key=>$val){
				if($flag)$id.="-";
				$id.=$row[$val];
				$flag=true;
			}
			$responce->rows[$i]['id']=$id;
			foreach(explode(",",$tbl['column_master']) as $key=>$val){
				$col = explode("|",$val);
				if(count($col)>1){
					$origCol=$col[0];
				}else{
					$origCol=$val;
				}
				if($origCol=="amount"||$origCol=="vat"||$origCol=="total"||$origCol=="price"||$origCol=="cost"){
					$rec[$i][]=number_format($row[$origCol],2);
				}else{
					$rec[$i][]=mysql_real_escape_string($row[$origCol]);
				}
				//echo $row[$origCol]."\n";
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		//print_r($responce);
		echo json_encode($db->utf8ize($responce));
	break;
	case'master_details':
		if($_REQUEST['con']!="undefined"){
			$db->openDb($_REQUEST['con']);
		}
		$tbl = $db->getWHERE("*","tbl_master_details","where id='{$_REQUEST['refid']}'");
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$id = explode("|",$_GET['id']);
		if(!$sidx) $sidx =1;
		//get primary_col dynamic array
		$flag=false;
		$count=0;
		foreach(explode("|",$tbl['primary_col_details']) as $key=>$val){
			if($flag)$where.=" and ";
			$where .= "`$val`='{$id[$count]}'";
			$flag=true;
			$count++;
		}
		//get primary_col dynamic array
		
		$result = mysql_query("SELECT COUNT(*) AS count FROM {$tbl['tbl_details']} where $where");
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$count = $row['count'];

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) $start = 0;
		$SQL = "SELECT {$tbl['column_details']} FROM {$tbl['tbl_details']} where $where";
		//echo $SQL;
		$result = mysql_query( $SQL ) or die("Couldn?t execute query.".mysql_error());
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row[num];
			//$responce->rows[$i]['cell']=array($row['id'],$row['item_desc'],$row['qty'],$row['unit'],number_format($row['qty']*$row['unit'],2,'.',' '));
			$txt="";
			$flag=false;
			foreach(explode(",",$tbl['column_details']) as $key=>$val){
				if($flag)$txt.="|";
				if($val=="selling"||$val=="total"){
					$txt.=number_format($row[$val],2);
				}else{
					$txt.=$row[$val];
				}
				
				$flag=true;
			}
			$responce->rows[$i]['cell']=explode("|",$txt);
			$i++;
		} 
		
		echo json_encode($responce);
	break;
	case'ledger':
		$and = $_REQUEST['q']?"and ".str_replace('`date`','a.`date`',$_REQUEST['q']):"";
		$sql="select a.date,a.account_desc,concat('EMALL'),b.remarks,b.reference,a.dr,a.cr,a.refid,a.center from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id where a.account_code='{$_REQUEST['account_code']}' $and 
			union 
			select a.date,a.account_desc,concat('UCLM'),b.remarks,b.reference,a.dr,a.cr,a.refid,a.center from tbl_journal_entry_uclm a left join tbl_vouchering_uclm b on a.refid=b.id where a.account_code='{$_REQUEST['account_code']}' $and 
			union 
			select a.date,a.account_desc,concat('UCMAIN'),b.remarks,b.reference,a.dr,a.cr,a.refid,a.center from tbl_journal_entry_ucmain a left join tbl_vouchering_ucmain b on a.refid=b.id where a.account_code='{$_REQUEST['account_code']}' $and 
			union 
			select a.date,a.account_desc,concat('UCMAMBALING'),b.remarks,b.reference,a.dr,a.cr,a.refid,a.center from tbl_journal_entry_ucmambaling a left join tbl_vouchering_ucmambaling b on a.refid=b.id where a.account_code='{$_REQUEST['account_code']}' $and 
			union 
			select a.date,a.account_desc,concat('WAREHOUSE'),b.remarks,b.reference,a.dr,a.cr,a.refid,a.center from tbl_journal_entry_warehouse a left join tbl_vouchering_warehouse b on a.refid=b.id where a.account_code='{$_REQUEST['account_code']}' $and ";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['refid']."|".$row['center'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'ap_content':
		$sql="select a.*,b.supplier_name from (select ap_refid,sum(dr) dr,sum(cr) cr,sum(cr-dr) ap_bal from tbl_journal_entry 
			where (account_desc like '%ACCOUNTS PAYABLE%' or account_desc like '%A/P%') group by ap_refid) a 
			left join tbl_supplier b on a.ap_refid=b.id";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['ap_refid'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'ar_content':
		$sql="select a.*,b.customer_name from (select ar_refid,sum(dr) dr,sum(cr) cr,sum(dr-cr) ar_bal from tbl_journal_entry 
			where (account_desc like '%ACCOUNTS RECEIVABLE%' or account_desc like '%A/R%') group by ar_refid) a 
			left join tbl_customers b on a.ar_refid=b.cust_id";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['ar_refid'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'generateAPreportDetails':
		$sql="select b.id,b.date,b.remarks,a.dr,a.cr from tbl_journal_entry a 
			left join tbl_vouchering b on a.refid=b.id
			where a.ap_refid='{$_REQUEST['supid']}'";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['id'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'generateARreportDetails':
		$sql="select b.id,b.date,b.remarks,a.dr,a.cr from tbl_journal_entry a 
			left join tbl_vouchering b on a.refid=b.id
			where a.ar_refid='{$_REQUEST['custid']}'";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['id'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'acctg_report':
		$where = $_REQUEST['q']?"where {$_REQUEST['q']}":"where a.fiscal_year='".date('Y')."'";
		if($_REQUEST['report_type']=="TRIALBAL"){
			$report_type ="";
		}elseif($_REQUEST['report_type']=="BS"){
			$report_type ="and a.report_type!='PNL'";
		}else{
			$report_type = $_REQUEST['report_type']?"and a.report_type='{$_REQUEST['report_type']}'":"";
		}
		switch($_REQUEST['report_type']){
			case'PNL':
				// $sql = "select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) as sub_total from 
				// (
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,if(b.account_type='COS',if(b.default_side='C',sum(cr-dr),sum(dr-cr))*-1,if(b.default_side='C',sum(cr-dr),sum(dr-cr))) as sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,if(b.account_type='COS',if(b.default_side='C',sum(cr-dr),sum(dr-cr))*-1,if(b.default_side='C',sum(cr-dr),sum(dr-cr))) as sub_total from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,if(b.account_type='COS',if(b.default_side='C',sum(cr-dr),sum(dr-cr))*-1,if(b.default_side='C',sum(cr-dr),sum(dr-cr))) as sub_total from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,if(b.account_type='COS',if(b.default_side='C',sum(cr-dr),sum(dr-cr))*-1,if(b.default_side='C',sum(cr-dr),sum(dr-cr))) as sub_total from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,if(b.account_type='COS',if(b.default_side='C',sum(cr-dr),sum(dr-cr))*-1,if(b.default_side='C',sum(cr-dr),sum(dr-cr))) as sub_total from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				// ) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				// group by tbl.account_code order by b.id";
				// $sql = "select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) as sub_total from 
				// (
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				// ) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				// group by tbl.account_code order by b.id";
				$sql = "select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) as sub_total from 
				(
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				group by tbl.account_code order by b.id";
			break;
			case'BS':
				// $sql = "select account_type,account_code,account_group,account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) sub_total from 
				// (
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				// ) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				// group by tbl.account_code order by b.id,tbl.account_desc";
				$sql = "select account_type,account_code,account_group,account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) sub_total from 
				(
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				group by tbl.account_code order by b.id,tbl.account_desc";
			break;
			case'TRIALBAL':
				// $beg="select sum(num) num,account_type,account_code,account_group,account_desc,sum(total_dr-total_cr) as begbal,0 as total_dr,0 as total_cr from 
				// (
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				// ) tbl 
				// group by account_code order by account_group";
				// $sql = "select sum(num) num,account_type,account_code,account_group,account_desc,0 begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr from 
				// (
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
					 // union 
					// select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				// ) tbl 
				// group by account_code";
				// $sql="select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(begbal) begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr,((sum(begbal)+sum(total_dr))-sum(total_cr)) as sub_total from 
				// (".$sql." union ".$beg.") 
				// tbl2 group by account_code order by account_group";
				
				$beg="select sum(num) num,account_type,account_code,account_group,account_desc,sum(total_dr-total_cr) as begbal,0 as total_dr,0 as total_cr from 
				(
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				) tbl 
				group by account_code order by account_group";
				$sql = "select sum(num) num,account_type,account_code,account_group,account_desc,0 begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr from 
				(
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				) tbl 
				group by account_code";
				$sql="select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(begbal) begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr,((sum(begbal)+sum(total_dr))-sum(total_cr)) as sub_total from 
				(".$sql." union ".$beg.") 
				tbl2 group by account_code order by account_group";
			break;
		}
		//echo $sql;
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$responce->rows[$i]['id']=$row['account_code'];
			foreach($row as $key=>$val){
				$rec[$i][]=$val;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	case'dynamicJqGridSQL':
		$con->getBranch();
		$ret = $con->pdoStyle($con->ipadd,$con->dbname,$_REQUEST['sql']);
		echo json_encode($ret);
	break;
	case'dynamic_tbl_col':
		$sql="SHOW COLUMNS FROM {$_REQUEST['tblname']}";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$type=explode("(",$row['Type']);
			if($type[0]=='int'){
				$colM[]=array("name"=>$row['Field'],"index"=>$row['Field'],'align'=>"right",'formatter'=>"currencyFmatter");
			}else{
				$colM[]=array("name"=>$row['Field'],"index"=>$row['Field']);
			}
			$colName[]=$row['Field'];
		}
		$rec['colModel']=$colM;
		$rec['colNames']=$colName;
		echo json_encode($rec);
	break;
	case'dynamic_add_records':
		if($_REQUEST['refid']){
			$rec = $db->getWHERE("*","{$_REQUEST['tblname']}","where `{$_REQUEST['colname']}`='{$_REQUEST['refid']}'");
		}else{
			$rec=null;
		}
		$sql="SHOW COLUMNS FROM {$_REQUEST['tblname']}";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		echo "<form name='dynamic_frm' id='dynamic_frm'>";
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<div style='float:left;margin-right:10px;width:150px;'>{$row['Field']}</div>
			<input type='text' ".($_REQUEST['colname']==$row['Field']?"readonly":"")." name='{$row['Field']}' value='{$rec[$row['Field']]}' style='float:left;width:250px;'/>
				<div style='clear:both;height:5px;'></div>";
		}
		echo "<input type='button' value='Save' onclick='jqgridSaveRec(\"{$_REQUEST['tblname']}\")' name='' style='height:30px;width:100px;' />";
		echo "</form>";
	break;
	case'jqgridSaveRec':
		$sql="SHOW COLUMNS FROM {$_REQUEST['tblname']}";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$flag=false;
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			if($flag){$header.=",";$data.=",";$update.=",";}
			$header.="`{$row['Field']}`";
			$data.="'".$_REQUEST[$row['Field']]."'";
			$update.="`{$row['Field']}`=values(`{$row['Field']}`)";
			$flag=true;
		}
		$sql2="insert into {$_REQUEST['tblname']} ($header) values ($data) on duplicate key update $update";
		$qry = mysql_query($sql2) or die("Couldn t execute query.".mysql_error());
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
		
	break;
	case'dynamic_tbl_content':
		$page = $_GET['page']; // get the requested page
		$limit = $_GET['rows']; // get how many rows we want to have into the grid
		$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
		$sord = $_GET['sord']; // get the direction
		$orderby = $sidx?"ORDER BY $sidx $sord":"";
		if(!$sidx) $sidx =1;
		
		$result = mysql_query("SELECT COUNT(*) AS count FROM {$_REQUEST['tblname']}");
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		$count = $row['count'];

		if( $count >0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) $start = 0;
		
		$sql="select * from {$_REQUEST['tblname']} $orderby LIMIT $start , $limit";
		$result = mysql_query($sql) or die("Couldn t execute query.".mysql_error());
		$responce->page = 1;
		$responce->total = 1;
		$responce->records = 1;
		$i=0;
		
		while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			$first=true;
			foreach($row as $key=>$val){
				if($first){
				$responce->rows[$i]['id']=$val;
				}
				$rec[$i][]=$val;
				$first=false;
			}
			$responce->rows[$i]['cell']=$rec[$i];
			$i++;
		}
		echo json_encode($responce);
	break;
	
}
?>
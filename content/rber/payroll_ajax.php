<?php
session_start();
require_once"../../settings.php";
require_once"../../class/dbConnection.php";
require_once"../../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
switch($_REQUEST['execute']){
	case'savePayroll':
		mysql_query("BEGIN");
		$refid = $_REQUEST['refid']?$_REQUEST['refid']:$db->getNextID('id','tbl_payroll_header');
		$sql_header = "insert into tbl_payroll_header (id,begdate,enddate,remarks) values ('".$refid."','{$_REQUEST['begdate']}','{$_REQUEST['enddate']}','{$_REQUEST['remarks']}') on duplicate key update begdate=values(begdate),enddate=values(enddate),remarks=values(remarks)";
		$sql_list = "insert into tbl_payroll_entry (id,refid,empid,name,dailyrate,regtime,othours,otamt,added_amt,gross_total,sss,philhealth,pagibig,others,lesstotal,adjustment,nettotal) values ";
		$flag=false;
		foreach($_REQUEST['payrollList'] as $key => $val){
			if($flag)$sql_list .=",";
			$sql_list.="('{$val['id']}','".$refid."','{$val['empid']}','{$val['name']}',
				'".preg_replace("/[^0-9.]/", "", $val['dailyrate'])."',
				'".preg_replace("/[^0-9.]/", "", $val['regtime'])."',
				'".preg_replace("/[^0-9.]/", "", $val['othours'])."',
				'".preg_replace("/[^0-9.]/", "", $val['otamt'])."',
				'".preg_replace("/[^0-9.]/", "", $val['added_amt'])."',
				'".preg_replace("/[^0-9.]/", "", $val['gross_total'])."',
				'".preg_replace("/[^0-9.]/", "", $val['sss'])."',
				'".preg_replace("/[^0-9.]/", "", $val['philhealth'])."',
				'".preg_replace("/[^0-9.]/", "", $val['pagibig'])."',
				'".preg_replace("/[^0-9.]/", "", $val['others'])."',
				'".preg_replace("/[^0-9.]/", "", $val['lesstotal'])."',
				'".preg_replace("/[^0-9.-]/", "", $val['adjustment'])."',
				'".preg_replace("/[^0-9.-]/", "", $val['nettotal'])."')";
			$flag=true;
			if((double)preg_replace("/[^0-9.-]/", "", $val['nettotal'])<0){
				if((double)preg_replace("/[^0-9.]/", "", $val['gross_total'])==0){
					$neg.="('{$val['empid']}','NegativePay-$refid','SSS','".preg_replace("/[^0-9.]/", "", $val['sss'])."'),";
					$neg.="('{$val['empid']}','NegativePay-$refid','philhealth','".preg_replace("/[^0-9.]/", "", $val['philhealth'])."'),";
					$neg.="('{$val['empid']}','NegativePay-$refid','pagibig','".preg_replace("/[^0-9.]/", "", $val['pagibig'])."'),";
					$neg.=$val['others']!=0?"('{$val['empid']}','NegativePay-$refid','(others)','".preg_replace("/[^0-9.]/", "", $val['others'])."'),":"";
				}else{
					$neg.="('{$val['empid']}','NegativePay-$refid','','".preg_replace("/[^0-9.]/", "", $val['nettotal'])."'),";
				}
			}
		}
		$sql_list.=" on duplicate key update name=values(name),dailyrate=values(dailyrate),regtime=values(regtime),othours=values(othours),otamt=values(otamt),added_amt=values(added_amt),gross_total=values(gross_total),sss=values(sss),philhealth=values(philhealth),pagibig=values(pagibig),others=values(others),lesstotal=values(lesstotal),adjustment=values(adjustment),nettotal=values(nettotal)";
		$qry1 = mysql_query($sql_header);
		$del=mysql_query("delete from tbl_payroll_entry where refid='$refid'");
		if($del){
			$qry2 = mysql_query($sql_list);
		}
		if($qry1 && $qry2){
			mysql_query("COMMIT");
			$del2=mysql_query("delete from tbl_payroll_advances where `details`='NegativePay-$refid'");
			if($del2){
				$neg="insert into tbl_payroll_advances (empid,details,rem,amount) values ".$neg.";";
				$neg = str_replace(",;","",$neg);
				$qryneg= mysql_query($neg);
			}
			echo "Saved";
		}else{
			echo mysql_error();
			mysql_query("ROLLBACK");
		}
	break;
	case'showList':
		$list = $db->resultArray("id,details,amount","tbl_payroll_advances","where empid='{$_REQUEST['empid']}'");
		?>
		<table border='1' id='empAdvances' style='width:100%;'>
			<thead>
				<tr>
					<td style='width:20px;'>&nbsp;</td>
					<td>Details</td>
					<td style='width:100px;'>Amount</td>
				</tr>
			</thead>
			<tbody>
			<?php 
			$count=1;
			foreach($list as $key => $val){ ?>
				<tr>
					<td><input type='checkbox' ><input type="hidden" name="advances[<?=$count?>][id]" value="<?=$val['id']?>"/></td>
					<td><input type='text' name='advances[<?=$count?>][remarks]' value="<?=$val['details']?>" style='width:100%;'/></td>
					<td><input type='text'  name='advances[<?=$count?>][amount]' value="<?=$val['amount']?>" style='width:100%;'/></td>
				</tr>
			<?php $count++;} ?>
			</tbody>
		</table>
		<script>
			$("#empAdvances").bind('keydown',function(e){
				var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
				if(chCode==46){ //pressing delete button
					$('input[type="checkbox"]:checked').closest("tr").remove();
				}
			});
		</script>
		<?
	break;
	case'advancesEntry':
		$emp = $db->resultArray("*","tbl_employee","");
		?>
		<input type='button' value='SAVE' onclick='saveEmpAdvances();' style='float:right;'/><div style='clear:both;height:5px;'></div>
		
			<table border='1' style='width:100%;'>
					<thead><tr>
						<td>Employee</td>
						<td style='width:150px;'>Remarks</td>
					</tr></thead>
					<tbody>
						<tr>
							<td>
								<select name="advances[emp]" style="width:100%;">
								<option value="">Select Employee</option>
								<?php foreach($emp as $key => $val){
									echo "<option value='{$val['id']}'>".$val['first_name']." ".$val['last_name']."</option>";
								} ?>
								</select>
							</td>
							<td><input type="button" value="New Entry" onclick="addNew();" style="width:100%;height:30px;"/></td>
						</tr>
					</tbody>
			</table>
		
		<div style="clear:both;height:10px;"></div>
		<form name="frmEmpAdvances" id="frmEmpAdvances">
			<div id="listArea" style="width:100%;"></div>
		</form>
		<script>
			function addNew(){
				var id = $("#empAdvances tbody tr").length + 1;
				var txt = '<tr>\
							<td><input type="checkbox" ></td>\
							<td><input type="text" name="advances['+id+'][remarks]" style="width:100%;"/></td>\
							<td><input type="text"  name="advances['+id+'][amount]" style="width:100%;"/></td>\
						</tr>';
				$("#empAdvances tbody").prepend(txt);
			}
			function saveEmpAdvances(){
				var datastring = $("#frmEmpAdvances").serialize();
				$.ajax({
					url: './content/rber/payroll_ajax.php?execute=saveEmpAdvances&empid='+$("select[name='advances[emp]']").val(),
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
			$("select[name='advances[emp]']").change(function(){
				$.ajax({
					url: './content/rber/payroll_ajax.php?execute=showList&empid='+$(this).val(),
					type:"POST",
					success:function(data){
						$("#listArea").html(data);
					}
				});
			});
		</script>
		<?
	break;
	case'saveEmpAdvances':
		// echo "<pre>";
		// print_r($_REQUEST);
		// echo "</pre>";
		$del = mysql_query("delete from tbl_payroll_advances where empid='{$_REQUEST['empid']}'");
		$sql="insert into tbl_payroll_advances (id,empid,details,amount) values ";
		$flag=false;
		foreach($_REQUEST['advances'] as $key => $val){
			if($flag)$sql.=",";
			$sql.="('{$val['id']}','{$_REQUEST['empid']}','{$val['remarks']}','{$val['amount']}')";
			$flag=true;
		}
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
		
		
	break;
	case'adjustment':
		$res = $db->resultArray("*","tbl_payroll_adjustment","where payrollid='{$_REQUEST['payrollref']}' and empid='{$_REQUEST['empid']}'");
		?>
		<input type='button' value='Add New' onclick='addNew();' style='float:left;'/>
		<input type='button' value='SAVE' onclick='saveAdjustment();' style='float:right;'/><div style='clear:both;height:5px;'></div>
		<form name="frmOthersExp" id="frmOthersExp">
			<input type="hidden" name="empid" value="<?=$_REQUEST['empid']?>"/>
			<input type="hidden" name="payrollref" value="<?=$_REQUEST['payrollref']?>"/>
			<table border='1' id='adj_exps' style='width:100%;'>
					<thead><tr>
						<td style='width:20px;'>&nbsp;</td>
						<td style='width:30px;'>ID</td>
						<td>Adjustment Name</td>
						<td>Remarks</td>
						<td style='width:100px;'>Amount</td>
					</tr></thead>
					<tbody>
						<?php if($res){ 
							foreach($res as $key => $val){
								?>
									<tr>
										<td><input class="adj" type='checkbox' ></td>
										<td><input type='text' name='adj[id][]' value="<?=$val['id']?>" style='width:100%;'/></td>
										<td><input type='text' name='adj[remarks][]' value="<?=$val['details']?>" style='width:100%;'/></td>
										<td><input type='text' name='adj[rem][]' value="<?=$val['rem']?>" style='width:100%;'/></td>
										<td><input type='text' class='adj_amt' name='adj[amount][]' value="<?=$val['amount']?>" style='width:100%;'/></td>
									</tr>
								<?
							}
						 } ?>
						<tr>
							<td><input type='checkbox' class="adj" ></td>
							<td><input type='text' name='adj[id][]' style='width:100%;'/></td>
							<td><input type='text' name='adj[remarks][]' style='width:100%;'/></td>
							<td><input type='text' name='adj[rem][]' style='width:100%;'/></td>
							<td><input type='text' class='adj_amt' name='adj[amount][]' style='width:100%;'/></td>
						</tr>
					</tbody>
			</table>
		</form>
		<script>
			$("#adj_exps").bind('keydown',function(e){
				var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
				if(chCode==46){ //pressing delete button
					$('input.adj[type="checkbox"]:checked').closest("tr").remove();
				}
			});
			function addNew(){
				var txt = "<tr>"+
							"<td><input type='checkbox' ></td>"+
							"<td><input type='text' name='adj[id][]' style='width:100%;'/></td>"+
							"<td><input type='text' name='adj[remarks][]' style='width:100%;'/></td>"+
							"<td><input type='text' name='adj[rem][]' style='width:100%;'/></td>"+
							"<td><input type='text' class='adj_amt' name='adj[amount][]' style='width:100%;'/></td>"+
						"</tr>";
				$("#adj_exps tbody").append(txt);
			}
			function saveAdjustment(){
				var datastring = $("#frmOthersExp").serialize();
				$.ajax({
					url: './content/rber/payroll_ajax.php?execute=saveAdjustment',
					data:datastring,
					type:"POST",
					success:function(data){
						if(data=="success"){
							var adj = sumName(".adj_amt");
							var row = $("#emp"+$("input[name='empid']").val());
							row.find(".pladjustment").val(adj);
							$("#dialogbox2").dialog("close");
							//$("#emp"+$("input[name='empid']").val()).closest("tr").find(".pllesstotal").val(sumLess($("#emp"+$("input[name='empid']").val()).closest("tr").find(".less")));
							netTotal($("#emp"+$("input[name='empid']").val()).closest("tr"));
							savePayroll();
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case'saveAdjustment':
		$del=mysql_query("delete from tbl_payroll_adjustment where payrollid='{$_REQUEST['payrollref']}' and empid='{$_REQUEST['empid']}'");
		$sql="insert into tbl_payroll_adjustment (id,payrollid,empid,details,rem,amount) values ";
		$flag=false;
		for($x=0;$x<count($_REQUEST['adj']['remarks']);$x++){
			if($_REQUEST['adj']['remarks'][$x]!=''){
				if($flag)$sql.=",";
				$sql.="('{$_REQUEST['adj']['id'][$x]}','{$_REQUEST['payrollref']}','{$_REQUEST['empid']}','{$_REQUEST['adj']['remarks'][$x]}','{$_REQUEST['adj']['rem'][$x]}','".preg_replace("/[^0-9.-]/", "", $_REQUEST['adj']['amount'][$x])."')";
				$flag=true;
			}
		}
		$sql.=" on duplicate key update details=values(details),rem=values(rem),amount=values(amount)";
		$qry=mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'others_exps':
		$res = $db->resultArray("*","tbl_payroll_deduction","where payrollid='{$_REQUEST['payrollref']}' and empid='{$_REQUEST['empid']}'");
		$advances = $db->resultArray("*,sum(amount) as total","tbl_payroll_advances","where empid='{$_REQUEST['empid']}' group by details");
		$deduction = $db->resultArray("*,sum(amount) as total","tbl_payroll_deduction","where empid='{$_REQUEST['empid']}' group by details");
		$auto = $db->resultArray("a.*,sum(a.amount)-coalesce((select sum(amount) less from tbl_payroll_deduction where empid=a.empid and `details`=a.`details`),0) balance,
		(select amount from tbl_payroll_deduction where empid=a.empid and `details`=a.`details` order by id desc limit 1) amort","tbl_payroll_advances a","where a.empid='{$_REQUEST['empid']}' and (`details` like '%Loan%' or `details` like '%NegativePay%') group by details,rem");
		// echo "<pre>";
		// print_r($auto);
		// echo "</pre>";
		foreach($deduction as $k => $v){
			$less[$v['details']]=$v['total'];
		}
		?>
		<input type='button' value='Add New' onclick='addNew();' style='float:left;'/>
		<input type='button' value='SAVE' onclick='saveOthersExp();' style='float:right;'/><div style='clear:both;height:5px;'></div>
		<form name="frmOthersExp" id="frmOthersExp">
			<input type="hidden" name="empid" value="<?=$_REQUEST['empid']?>"/>
			<input type="hidden" name="payrollref" value="<?=$_REQUEST['payrollref']?>"/>
			<fieldset>
				<legend>Manual Entry</legend>
				<table border='1' id='others_exps' style='width:100%;'>
						<thead><tr>
							<td style='width:20px;'>&nbsp;</td>
							<td style='width:30px;'>ID</td>
							<td>Deduction Name</td>
							<td>Remarks</td>
							<td style='width:100px;'>Amount</td>
						</tr></thead>
						<tbody>
							<?php if($res){ 
								foreach($res as $key => $val){
									?>
										<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='others[id][]' value="<?=$val['id']?>" style='width:100%;'/></td>
											<td><input type='text' name='others[remarks][]' value="<?=$val['details']?>" style='width:100%;'/></td>
											<td><input type='text' name='others[rem][]' value="<?=$val['rem']?>" style='width:100%;'/></td>
											<td><input type='text' class='others_amt' name='others[amount][]' value="<?=$val['amount']?>" style='width:100%;'/></td>
										</tr>
									<?
								}
							 } ?>
							<tr>
								<td><input type='checkbox' ></td>
								<td><input type='text' name='others[id][]' style='width:100%;'/></td>
								<td><input type='text' name='others[remarks][]' style='width:100%;'/></td>
								<td><input type='text' name='others[rem][]' style='width:100%;'/></td>
								<td><input type='text' class='others_amt' name='others[amount][]' style='width:100%;'/></td>
							</tr>
						</tbody>
				</table>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset>
				<legend>Auto Entry</legend>
				<table border='1' id='auto_others_exps' style='width:100%;'>
						<thead><tr>
							<td style='width:20px;'>&nbsp;</td>
							<td style='width:30px;'>ID</td>
							<td>Deduction Name</td>
							<td>Remarks</td>
							<td style='width:100px;'>Amount</td>
						</tr></thead>
						<tbody>
						<?php if(!$res){ 
								foreach($auto as $key => $val){
									echo $val['balance'];
									if($val['balance']!=0){
									?>
										<tr>
											<td><input type='checkbox' ></td>
											<td><input type='text' name='others[id][]' value="" style='width:100%;'/></td>
											<td><input type='text' name='others[remarks][]' value="<?=$val['details']?>" style='width:100%;'/></td>
											<td><input type='text' name='others[rem][]' value="<?=$val['rem']?>" style='width:100%;'/></td>
											<td><input type='text' class='others_amt' name='others[amount][]' value="<?=$val['amort']?$val['amort']:$val['amount']?>" style='width:100%;'/></td>
										</tr>
									<?
									}
								}
							 } ?>
						</tbody>
				</table>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<table border='1' style='width:100%;'>
				<thead>
					<tr>
						<td>Deduction Name</td>
						<td style='width:100px;'>Amount</td>
						<td style='width:100px;'>Deduction</td>
						<td style='width:100px;'>Balance</td>
					</tr>
				</thead>
				<tbody>
				<?php foreach($advances as $key => $val){ ?>
					<tr>
						<td><?=$val['details']?></td>
						<td><?=number_format($val['total'],2)?></td>
						<td><?=number_format($less[$val['details']],2)?></td>
						<td><?=number_format($val['total']-$less[$val['details']],2)?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</form>
		<script>
			$("#others_exps,#auto_others_exps").bind('keydown',function(e){
				var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
				if(chCode==46){ //pressing delete button
					$('input[type="checkbox"]:checked').closest("tr").remove();
				}
			});
			function addNew(){
				var txt = "<tr>"+
							"<td><input type='checkbox' ></td>"+
							"<td><input type='text' name='others[id][]' style='width:100%;'/></td>"+
							"<td><input type='text' name='others[remarks][]' style='width:100%;'/></td>"+
							"<td><input type='text' name='others[rem][]' style='width:100%;'/></td>"+
							"<td><input type='text' class='others_amt' name='others[amount][]' style='width:100%;'/></td>"+
						"</tr>";
				$("#others_exps tbody").append(txt);
			}
			function saveOthersExp(){
				var datastring = $("#frmOthersExp").serialize();
				$.ajax({
					url: './content/rber/payroll_ajax.php?execute=saveOthersExp',
					data:datastring,
					type:"POST",
					success:function(data){
						var otherstotal = sumName(".others_amt");
						
						var row = $("#emp"+$("input[name='empid']").val());
						row.find(".plother").val(otherstotal);
						$("#dialogbox2").dialog("close");
						//netTotal($("#emp"+$("input[name='empid']").val()).closest("tr"));
						$("#emp"+$("input[name='empid']").val()).closest("tr").find(".pllesstotal").val(sumLess($("#emp"+$("input[name='empid']").val()).closest("tr").find(".less")));
						netTotal($("#emp"+$("input[name='empid']").val()).closest("tr"));
						savePayroll();
					}
				});
			}
		</script>
		<?
	break;
	case'saveOthersExp':
		$del=mysql_query("delete from tbl_payroll_deduction where payrollid='{$_REQUEST['payrollref']}' and empid='{$_REQUEST['empid']}'");
		$sql="insert into tbl_payroll_deduction (id,payrollid,empid,details,rem,amount) values ";
		$flag=false;
		for($x=0;$x<count($_REQUEST['others']['remarks']);$x++){
			if($_REQUEST['others']['remarks'][$x]!=''){
				if($flag)$sql.=",";
				$sql.="('{$_REQUEST['others']['id'][$x]}','{$_REQUEST['payrollref']}','{$_REQUEST['empid']}','{$_REQUEST['others']['remarks'][$x]}','{$_REQUEST['others']['rem'][$x]}','".preg_replace("/[^0-9.]/", "", $_REQUEST['others']['amount'][$x])."')";
				$flag=true;
			}
		}
		$sql.=" on duplicate key update details=values(details),rem=values(rem),amount=values(amount)";
		// echo $sql;
		// exit;
		$qry=mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			//echo mysql_error();
			echo "No entry...";
		}
	break;
	case'payrollList':
		$sql="select * from tbl_payroll_header";
		$qry=mysql_query($sql);
		echo "<table border='1' style='width:100%;'>
			<tr>
				<td>REFID</td>
				<td>Payroll Date</td>
				<td>Remarks</td>
			</tr>";
		while($row=mysql_fetch_assoc($qry)){
			echo "<tr>
					<td>".$row['id']."</td>
					<td><a href='?page=payroll&refid=".$row['id']."'>".$row['begdate']." - ".$row['enddate']."</a></td>
					<td>".$row['remarks']."</td>
				</tr>";
		}
		echo "</table>";
	break;
	case'emplist':
		$sql="select * from tbl_employee order by id asc";
		$qry=mysql_query($sql);
		echo "<table border='1' style='width:100%;'>
			<tr>
				<td>Menu</td>
				<td>Employee</td>
			</tr>";
		while($row=mysql_fetch_assoc($qry)){
			echo "<tr>
					<td><input type='button' value='Add' onclick='addEntry(\"".$row['id']."\",\"".$row['first_name']." ".$row['last_name']."\",\"".$row['daily_rate']."\");' style='width:100%;'/></td>
					<td>".$row['first_name']." ".$row['last_name']."</td>
				</tr>";
		}
		echo "</table>";
		?>
		<script>
			function addEntry(empid,name,rate){
				var txt = '<tr id="emp'+empid+'">\
							<td><input class="empcheckbox" type="checkbox" ></td>\
							<td><input type="text" name="payrollList['+empid+'][id]" value="" style="width:100%;"/></td>\
							<td>\
								<input type="button" value="DTR" onclick="dtr('+empid+')" style="width:100%;"/>\
								<input type="button" value="Others" onclick="others('+empid+')" style="width:45px;float:left;"/>\
							</td>\
							<td><input type="text" class="plempid" name="payrollList['+empid+'][empid]" value="'+empid+'" style="width:100%;"/></td>\
							<td><input type="text" class="plname" name="payrollList['+empid+'][name]" value="'+name+'" style="width:100%;"/></td>\
							<td><input type="text" class="pldailyrate" name="payrollList['+empid+'][dailyrate]" value="'+rate+'" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plregtime" name="payrollList['+empid+'][regtime]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plothours" name="payrollList['+empid+'][othours]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plotamt" name="payrollList['+empid+'][otamt]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="pladded_amt" name="payrollList['+empid+'][added_amt]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plgross_total" name="payrollList['+empid+'][gross_total]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plsss less" name="payrollList['+empid+'][sss]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plphilhealth less" name="payrollList['+empid+'][philhealth]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plpagibig less" name="payrollList['+empid+'][pagibig]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plother less" name="payrollList['+empid+'][others]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="pllesstotal" name="payrollList['+empid+'][lesstotal]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="pladjustment" name="payrollList['+empid+'][adjustment]" style="width:100%;text-align:right;"/></td>\
							<td><input type="text" class="plnettotal" name="payrollList['+empid+'][nettotal]" style="width:100%;text-align:right;"/></td>\
						</tr>';
				$("#tblPayroll tbody").append(txt);
				setTimeout(function(){
				  savePayroll();
				  if(!getParam('refid')){
					window.location = document.URL+'&refid='+$("#refid").val();
				  }else{
					window.location = document.URL  
				  }
				}, 800);
			}
		</script>
		<?
	break;
	case'dtr':
		$emp = $db->getWHERE("*","tbl_employee","where id='{$_REQUEST['empid']}'");
		$details= $db->getWHERE("a.*,b.sss,b.pagibig,b.philhealth","tbl_employee a left join tbl_payroll_entry b on a.id=b.empid and b.refid='{$_REQUEST['payrollref']}'","where a.id='{$_REQUEST['empid']}'");
		if($db->getWeek($_REQUEST['enddate'])>=5){
			$sss=($details['sss']!=0?$details['sss']:0);
			$pagibig=($details['pagibig']!=0?$details['pagibig']:0);
			$philhealth=($details['philhealth']!=0?$details['philhealth']:0);
		}else{
			$sss=($details['sss']==0?$emp['sss']:$details['sss']);
			$pagibig=($details['pagibig']==0?$emp['pagibig']:$details['pagibig']);
			$philhealth=($details['philhealth']==0?$emp['philhealth']:$details['philhealth']);
		}
		
		?>
		<form name="frmDTR" id="frmDTR" method="post">
			<input type="hidden" name="payrollref" value="<?=$_REQUEST['payrollref']?>"/>
			<input type="text" name="entryid" value="<?=$_REQUEST['entryid']?>" style="float:left;width:50px;" readonly />
			<div style="float:left;margin-right:10px;">Daily Rate</div>
			<input type="text" name="rate" style="float:left;width:80px;;margin-right:10px;" value="<?=$_REQUEST['rate']?>" />
			<div style="float:left;margin-right:10px;">EmpID</div>
			<input type="text" name="empid" style="float:left;width:80px;margin-right:10px;" value="<?=$_REQUEST['empid']?>" readonly />
			<div style="float:left;margin-right:10px;">Name</div>
			<input type="text" name="empname" style="float:left;width:300px;" value="<?=$details['first_name']." ".$details['last_name']?>" readonly />
			<div style="float:left;width:150px;">Week No. <?=$db->getWeek($_REQUEST['enddate'])?></div>
			<div style="clear:both;height:5px;"></div>
			<table border='1' style="width:100%;">
				<tr>
					<td>Date</td>
					<td>Day</td>
					<td>Type</td>
					<td>Status</td>
					<td>Reg Hrs</td>
					<td>Total</td>
					<td style="width:100px;">OT Hours</td>
					<td style="width:100px;">OT Amt</td>
					<td style="width:80px;">Add</td>
					<td style="width:100px;">Remarks</td>
					<td>Sub Total</td>
				</tr>
				<?php
					// Start date
					$date = date ("Y-m-d", strtotime($_REQUEST['begdate']));
					// End date
					$end_date = $_REQUEST['enddate'];
					$count=1;
					while (strtotime($date) <= strtotime($end_date)) {
						$info = $db->getWHERE("*","tbl_payroll_dtr","where empid='{$_REQUEST['empid']}' and date='".$date."'");
						//print_r($info);
						$day = date ("D", strtotime($date));
						echo "<tr>
								<td><input class='dtrdate' type='text' name='dtr[".$count."][date]' value='".$date."' style='width:100%;'/></td>
								<td style='".($day=="Sun"?"color:red;":"")."'>".$day."</td>
								<td>
									<select class='dtrtype' name='dtr[".$count."][type]' style='width:100%;'>
										<option ".($info['type']=='REG'?'SELECTED':'')." value='REG'>REG</option>
										<option ".($info['type']=='DAY-OFF'?'SELECTED':'')." value='DAY-OFF'>DAY-OFF</option>
										<option ".($info['type']=='LEGAL-HOL'?'SELECTED':'')." value='LEGAL-HOL'>LEGAL-HOL</option>
										<option ".($info['type']=='SPECIAL-HOL'?'SELECTED':'')." value='SPECIAL-HOL'>SPECIAL-HOL</option>
									</select>
								</td>
								<td>
									<select class='dtrstatus' name='dtr[".$count."][status]' style='width:100%;'>
										<option ".($info['status']=='PRESENT'?'SELECTED':'')." value='PRESENT'>PRESENT</option>
										<option ".($info['status']=='ABSENT'?'SELECTED':'')." value='ABSENT'>ABSENT</option>
										<option ".($info['status']=='NO-WORK(WITH-PAY)'?'SELECTED':'')." value='NO-WORK(WITH-PAY)'>NO-WORK(WITH-PAY)</option>
										<option ".($info['status']=='NO-WORK(NO-PAY)'?'SELECTED':'')." value='NO-WORK(NO-PAY)'>NO-WORK(NO-PAY)</option>
									</select>
								</td>
								<td><input class='dtrreghours' type='text' name='dtr[".$count."][reghours]' value='".($info['reghours']?$info['reghours']:0)."' style='width:100%;text-align:center;'/></td>
								<td><input class='dtrregtotal' type='text' name='dtr[".$count."][regtotal]' value='".($info['regtotal']?$info['regtotal']:0)."' style='width:100%;text-align:right;'/></td>
								<td><input class='dtrothours' type='text' name='dtr[".$count."][othours]' value='".($info?$info['othours']:0)."' style='width:100%;text-align:center;'/></td>
								<td><input class='dtrotamt' type='text' name='dtr[".$count."][otamt]' value='".($info?$info['otamt']:0)."' style='width:100%;text-align:right;'/></td>
								<td><input class='dtradd' type='text' name='dtr[".$count."][add]' value='".($info['add']?$info['add']:0)."' style='width:100%;text-align:right;'/></td>
								<td><input class='dtradd_remarks' type='text' name='dtr[".$count."][add_remarks]' value='".($info['add_remarks']?$info['add_remarks']:'')."' style='width:100%;text-align:left;font-size:10px;'/></td>
								<td><input class='dtrsubtotal' type='text' name='dtr[".$count."][subtotal]' value='".($info['subtotal']?$info['subtotal']:0)."' style='width:100%;text-align:right;'/></td>
							</tr>";
						$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
						$count++;
					}
				?>
				<tr>
					<td colspan="2">Total</td>
					<td><input type="text" name="total[type]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td><input type="text" name="total[status]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td><input type="text" name="total[reghours]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td><input type="text" name="total[regtotal]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td><input type="text" name="total[othours]" style="width:100%;text-align:center;font-weight:bold;"/></td>
					<td><input type="text" name="total[otamt]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td><input type="text" name="total[add]" style="width:100%;text-align:right;font-weight:bold;"/></td>
					<td></td>
					<td><input type="text" name="total[subtotal]" style="width:100%;text-align:right;font-weight:bold;"/></td>
				</tr>
			</table>
			<div style="clear:both;height:5px;"></div>
			<table border='1' style="width:100%;">
					<td>SSS</td>
					<td><input type="text" name="deduction[sss]" value="<?=$sss?>" style="width:100%;text-align:right;"/></td>
					<td>PagIbig</td>
					<td><input type="text" name="deduction[pagibig]" value="<?=$pagibig?>" style="width:100%;text-align:right;"/></td>
					<td>PhilHealth</td>
					<td><input type="text" name="deduction[philhealth]" value="<?=$philhealth?>" style="width:100%;text-align:right;"/></td>
			</table>
			<div style="clear:both;height:10px;"></div>
			<input type="button" value="SAVE" onclick="saveDTR()" name="dtrSave" style="height:40px;width:150px;"/>
		</form>
		<script>
			var defhr = '<?=$emp['defhr']?>';
			defhr = defhr==''?8:defhr;
			$(document).ready(function() {
				refreshOT();
			});
			function refreshOT(){
				$("input[name='total[reghours]']").val(sumName(".dtrreghours"));
				$("input[name='total[regtotal]']").val(sumName(".dtrregtotal"));
				$("input[name='total[othours]']").val(sumName(".dtrothours"));
				$("input[name='total[otamt]']").val(sumName(".dtrotamt"));
				$("input[name='total[subtotal]']").val(sumName(".dtrsubtotal"));
				$("input[name='total[add]']").val(sumName(".dtradd"));
			}
			
			$(".dtrstatus, .dtrtype, .dtrreghours, .dtrothours, .dtradd").on("change paste keyup",function(){
				var tr = $(this).closest("tr");
				var rate = strtodouble($("input[name='rate']").val());
				var type = tr.find(".dtrtype").val();
				var status = tr.find(".dtrstatus").val();
				var reghr = tr.find(".dtrreghours").val();
				var othr = tr.find(".dtrothours").val();
				var add = strtodouble(tr.find(".dtradd").val());
				if(type=='REG'){
					switch(status){
						case'PRESENT':
							var newrate = (rate/defhr);
							var regtotal = tr.find(".dtrregtotal").val(strtocurrency(reghr * newrate));
							var ottotal = tr.find(".dtrotamt").val(strtocurrency((othr * newrate)* otreg_percent));
							tr.find(".dtrsubtotal").val(strtocurrency(strtodouble(regtotal.val())+strtodouble(ottotal.val())+add));
						break;
						case'NO-WORK(NO-PAY)':
						case'ABSENT':
							tr.find(".dtrregtotal").val(0);
							tr.find(".dtrothours").val(0);
							tr.find(".dtrreghours").val(0);
							tr.find(".dtrotamt").val(0);
							tr.find(".dtrsubtotal").val(0);
						break;
					}
				}
				if(type=='LEGAL-HOL'){
					switch(status){
						case'PRESENT':
							var newrate = (rate/defhr)*legal_hol_percent;
							var regtotal = tr.find(".dtrregtotal").val(strtocurrency(reghr * newrate));
							var ottotal = tr.find(".dtrotamt").val(strtocurrency((othr * newrate)* otreg_percent));
							tr.find(".dtrsubtotal").val(strtocurrency(strtodouble(regtotal.val())+strtodouble(ottotal.val())+add));
						break;
						case'NO-WORK(WITH-PAY)':
							//tr.find(".dtrregtotal").val(strtocurrency(rate));
							//tr.find(".dtrreghours").val(0);
							//tr.find(".dtrsubtotal").val(strtocurrency(rate+add));
							tr.find(".dtrregtotal").val(strtocurrency(reghr * (rate/defhr)));
							tr.find(".dtrsubtotal").val(strtocurrency((reghr * (rate/defhr))+add));
							
						break;
						case'NO-WORK(NO-PAY)':
						case'ABSENT':
							tr.find(".dtrregtotal").val(0);
							tr.find(".dtrothours").val(0);
							tr.find(".dtrreghours").val(0);
							tr.find(".dtrotamt").val(0);
							tr.find(".dtrsubtotal").val(0);
						break;
					}
				}
				if(type=='SPECIAL-HOL'){
					switch(status){
						case'PRESENT':
							var newrate = (rate/defhr)*special_hol_percent;
							var regtotal = tr.find(".dtrregtotal").val(strtocurrency(reghr * newrate));
							var ottotal = tr.find(".dtrotamt").val(strtocurrency((othr * newrate)* otreg_percent));
							tr.find(".dtrsubtotal").val(strtocurrency(strtodouble(regtotal.val())+strtodouble(ottotal.val())+add));
						break;
						case'NO-WORK(WITH-PAY)':
							//tr.find(".dtrregtotal").val(strtocurrency(rate));
							//tr.find(".dtrreghours").val(0);
							//tr.find(".dtrsubtotal").val(strtocurrency(rate+add));
							tr.find(".dtrregtotal").val(strtocurrency(reghr * (rate/defhr)));
							tr.find(".dtrsubtotal").val(strtocurrency((reghr * (rate/defhr))+add));
						break;
						case'NO-WORK(NO-PAY)':
						case'ABSENT':
							tr.find(".dtrregtotal").val(0);
							tr.find(".dtrothours").val(0);
							tr.find(".dtrreghours").val(0);
							tr.find(".dtrotamt").val(0);
							tr.find(".dtrsubtotal").val(0);
						break;
					}
				}
				if(type=='DAY-OFF'){
					switch(status){
						case'PRESENT':
							var newrate = (rate/defhr)*wod_percent;
							var regtotal = tr.find(".dtrregtotal").val(strtocurrency(reghr * newrate));
							var ottotal = tr.find(".dtrotamt").val(strtocurrency((othr * newrate)* otreg_percent));
							tr.find(".dtrsubtotal").val(strtocurrency(strtodouble(regtotal.val())+strtodouble(ottotal.val())+add));
						break;
						case'NO-WORK(WITH-PAY)':
						case'NO-WORK(NO-PAY)':
						case'ABSENT':
							tr.find(".dtrregtotal").val(0);
							tr.find(".dtrothours").val(0);
							tr.find(".dtrreghours").val(0);
							tr.find(".dtrotamt").val(0);
							tr.find(".dtrsubtotal").val(0);
						break;
					}
				}
				refreshOT();
			});
			function saveDTR(){
				var datastring = $("#frmDTR").serialize();
				$.ajax({
					url: './content/rber/payroll_ajax.php?execute=saveDTR',
					data:datastring,
					type:"POST",
					success:function(data){
						//console.log(data);
						if(data=="success"){
							var row = $("#emp"+$("input[name='empid']").val());
							var regtotal = new Number(strtodouble($("input[name='total[regtotal]']").val()));
							var othours = new Number(strtodouble($("input[name='total[othours]']").val()));
							var otamt = new Number(strtodouble($("input[name='total[otamt]']").val()));
							var added = new Number(strtodouble($("input[name='total[add]']").val()));
							
							row.find(".plregtime").val(strtocurrency(regtotal));
							row.find(".plothours").val(strtocurrency(othours));
							row.find(".plotamt").val(strtocurrency(otamt));
							row.find(".pladded_amt").val(strtocurrency(added));
							
							var sss = new Number(strtodouble($("input[name='deduction[sss]']").val()));
							var philhealth = new Number(strtodouble($("input[name='deduction[philhealth]']").val()));
							var pagibig = new Number(strtodouble($("input[name='deduction[pagibig]']").val()));
							var others = new Number(strtodouble(row.find(".plother").val()));
							row.find(".plsss").val(strtocurrency(sss));
							row.find(".plphilhealth").val(strtocurrency(philhealth));
							row.find(".plpagibig").val(strtocurrency(pagibig));
							var total_deduction = sss+philhealth+pagibig+others;
							row.find(".pllesstotal").val(strtocurrency(total_deduction));
							
							row.find(".plgross_total").val(strtocurrency(regtotal+otamt+added));
							row.find(".plnettotal").val(strtocurrency((regtotal+otamt+added)-total_deduction));
							
							$("#dialogbox2").dialog("close");
							netTotal($("#emp"+$("input[name='empid']").val()).closest("tr"));
							savePayroll();
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case'saveDTR':
		// echo "<pre>";
		// print_r($_REQUEST['deduction']);
		// echo "</pre>";
		mysql_query("BEGIN");
		$sql = "insert into tbl_payroll_dtr (empid,date,type,status,reghours,regtotal,othours,otamt,subtotal,`add`,`add_remarks`) values ";
		$flag=false;
		foreach($_REQUEST['dtr'] as $key => $val){
			if($flag)$sql.=",";
			$sql.="('{$_REQUEST['empid']}','{$val['date']}','{$val['type']}','{$val['status']}','{$val['reghours']}','".preg_replace("/[^0-9.]/", "", $val['regtotal'])."','{$val['othours']}','".preg_replace("/[^0-9.]/", "", $val['otamt'])."','".preg_replace("/[^0-9.]/", "", $val['subtotal'])."','".preg_replace("/[^0-9.]/", "", $val['add'])."','{$val['add_remarks']}')";
			$flag=true;
		}
		$qry=mysql_query($sql." on duplicate key update type=values(type),status=values(status),reghours=values(reghours),regtotal=values(regtotal),
			othours=values(othours),otamt=values(otamt),subtotal=values(subtotal),`add`=values(`add`),add_remarks=values(add_remarks)");
		$sql2="update tbl_payroll_entry set sss='{$_REQUEST['deduction']['sss']}',philhealth='{$_REQUEST['deduction']['philhealth']}',pagibig='{$_REQUEST['deduction']['pagibig']}' where refid='{$_REQUEST['payrollref']}' and empid='{$_REQUEST['empid']}'";
		$qry2=mysql_query($sql2);
		if($qry && $qry2){
			mysql_query("COMMIT");
			echo "success";
		}else{
			mysql_query("ROLLBACK");
			echo mysql_error();
		}
	break;
}
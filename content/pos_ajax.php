<?php
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);

session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
/*$con = $db->openDb();
if(!$con){
	$db->openDb_local();
}*/
$readingNum = $db->getReadingnum($_SESSION['counter_num']);
switch($_REQUEST['execute']){
	case'changebranch':
		$_SESSION['connect']=$_REQUEST['branchname'];
		echo "success";
	break;
	case'popUpforReports':
		switch($_REQUEST['reptype']){
	
			case'compliance':
				$complist = $db->resultArray("distinct company_name","data_teletech_patient","where year=year(now())");
				$dataref = $db->resultArray("distinct data_reference","data_teletech_patient","where year=year(now())");
				$package = $db->resultArray("*","tbl_packages","");
			?>
				<div style="float:left;margin-right:30px;width:150px;">Company Name:</div>
				<select name="company_name" id="company_name" style="float:left;width:200px;">
					<option value="">Select</option>
					<?php foreach($complist as $k => $v){ ?>
					<option <?= $_SESSION['records_filter']['company_name']==$v['company_name']?"selected":""?> value="<?=$v['company_name']?>"><?=$v['company_name']?></option>
					<?php } ?>
				</select>
				<div style="clear:both;height:5px;"></div>
				<div id="cashier_area">
					<div style="float:left;margin-right:30px;width:150px;">Data Reference:</div>
					<select name="data_reference" id="data_reference" style="float:left;width:200px;">
						<option value="">Select</option>
						<?php foreach($dataref as $k => $v){ ?>
						<option <?= $_SESSION['records_filter']['data_reference']==$v['data_reference']?"selected":""?> value="<?=$v['data_reference']?>"><?=$v['data_reference']?></option>
						<?php } ?>
					</select>
				</div>
				<div style="clear:both;height:5px;"></div>
				<div id="cashier_area">
					<div style="float:left;margin-right:30px;width:150px;">Package:</div>
					<select name="package" id="package" style="float:left;width:200px;">
						<option value="">Select</option>
						<?php foreach($package as $k => $v){ ?>
						<option <?= $_SESSION['defaultPackage']==$v['id']?"selected":""?> value="<?=$v['id']?>"><?=$v['package_name']?></option>
						<?php } ?>
					</select>
				</div>
				<input type="button" value="View" onclick="previewRep('<?=$_REQUEST['reptype']?>','reports/compliance.php');" style="width:100px;"/>
			<?
			break;
		}
		?>
		<script>
		$(document).ready(function() {
			$('#beg_date').datepicker({
				inline: true,
				changeMonth: true,
				changeYear: true,
				dateFormat:"yy-mm-dd"
			});
			$('#end_date').datepicker({
				inline: true,
				changeMonth: true,
				changeYear: true,
				dateFormat:"yy-mm-dd"
			});
		});
		$("select[name='package']").change(function(){
			setTodefault($(this).val());
		});
		function setTodefault(id){
			$.ajax({
			  url: './content/pos_ajax.php?execute=setTodefaultPackage&id='+id,
			  async: false,
			  success: function(data) {
				if(data=="success"){
					alert("This package is set to default...");
				}
			  }
			});
		}
		<?
	break;
	case'updateReceiptWithStudentID':
		$sql="update tbl_sales_receipt_{$_REQUEST['counternum']} set studentid='{$_REQUEST['studentid']}' WHERE receipt_id='{$_REQUEST['ornum']}' and counter_num='{$_REQUEST['counternum']}'";
		$qry=mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case'inwords':
		echo ucwords($db->intToWords(preg_replace("/[^0-9.]/", "", number_format($_REQUEST['amt'],2))));
	break;
	case'receipt_manual':
		$list = $db->resultArray("a.*,b.customer_name","tbl_receipt_manual a left join tbl_customers b on a.cust_id=b.cust_id","order by id desc limit 20");
		echo '<input id="bt3" class="buthov" type="button" onclick="printRM();" value="Print" style="height:30px;width:150px;float:left;"/>';
		echo '<table class="tbl" id="soatbl" cellspacing="0" border="1" cellpadding="0" width="100%" >
				<tr>
					<td>Select</td>
					<td>OR</td>
					<td>Date</td>
					<td>Customer Name</td>
					<td>Amount</td>
					<td>Status</td>
					<td>Menu</td>
				</tr>
				';
		foreach($list as $key => $val){
			echo "<tr>
					<td><input type='checkbox' name='receipt' value='".$val['receipt']."'/></td>
					<td>{$val['receipt']}</td>
					<td>{$val['date']}</td>
					<td>".$val['customer_name']."</td>
					<td style='text-align:right;'>".number_format($val['total_amount'],2)."</td>
					<td>".$val['status']."</td>
					<td><input type='button' value='Delete' style='width:100px;' onclick='delOR(".$val['receipt'].")'/></td>
				</tr>";
		}
		echo '</table>';
		?>
		<script>
			function delOR(refid){
				var type = $("input[name='receipt_type']:checked").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=delOR&receipt='+refid+'&type='+type,
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
			function printRM(){
				var ids='';
				$('#soatbl').find('tr').each(function () {
					var row = $(this);
					if (row.find('input[type="checkbox"]').is(':checked')) {
						ids+=row.find('input[type="checkbox"]').val();
					}
				});
				if (window.showModalDialog) {
					window.showModalDialog('./reports/receipt_manual.php?receipt='+ids,"Receipt","dialogWidth:900px;dialogHeight:650px");
				} else {
					window.open('./reports/receipt_manual.php?receipt='+ids,"Receipt",'height=650,width=900,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
				}
			}
		</script>
		<?
	break;
	case'delOR':
		$sql = "update tbl_receipt_manual set status='CANCELED' where receipt='".$_REQUEST['receipt']."' and receipt_type='".$_REQUEST['type']."'";
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo "Error: ".mysql_error();
		}
	break;
	case'unpaidSOA':
		$list = $db->resultArray("*","tbl_soa","where cust_id='{$_REQUEST['cust_id']}' and payment_ref is null");
		echo '<input id="bt3" class="buthov" type="button" onclick="selectSOA();" value="Select" style="height:30px;width:150px;float:left;"/>';
		echo '<table class="tbl" id="soatbl" cellspacing="0" border="1" cellpadding="0" width="100%" >
				<tr>
					<td>Select</td>
					<td>Date</td>
					<td>Remarks</td>
					<td>Amount</td>
					<td>Due Date</td>
				</tr>
				';
		foreach($list as $key => $val){
			echo "<tr>
					<td><input type='checkbox' name='soanum' value='".$val['id']."'/></td>
					<td>{$val['date']}</td>
					<td>SOA #".$val['year'].$val['month']."</td>
					<td>".number_format($val['amount'],2)."</td>
					<td>".$val['due_date']."</td>
				</tr>";
		}
		echo '</table>';
		?>
		<script>
			function selectSOA(){
				 $('#soatbl').find('tr').each(function () {
					var row = $(this);
					if (row.find('input[type="checkbox"]').is(':checked')) {
						var txt = row.find("td").eq(2).html()+' Date: '+row.find("td").eq(1).html()+' DueDate: '+row.find("td").eq(4).html();
						var val = '<tr>\
									<td><input type="text" name="particular[]" value="'+txt+'" style="width:100%;border:none;background:transparent;"/></td>\
									<td><input onchange="sumVoucherAmt()" value="'+row.find("td").eq(3).html().replace(/,/g, "")+'" type="text" class="amt" name="amt[]" style="text-align:right;background:transparent;border:none;width:100%;" value="0.00"/></td>\
								</tr>';
						$("#mytbl tbody").prepend(val);
						sumVoucherAmt();
					}
				});
			}
		</script>
		<?
	break;
	case'customerInfo':
		$info = $db->getWHERE("*","tbl_customers","where cust_id='{$_REQUEST['cust_id']}'");
		echo json_encode($info);
	break;
	case'viewFloorDetails':
		$info = $db->getWHERE("a.*,a.floor xfloor,b.*","tbl_floor_mapping a left join tbl_customers b on a.floor=b.floor and a.unit=b.mall_unit_number","where a.floor='{$_REQUEST['floor']}' and a.unit='{$_REQUEST['unit']}'");
		$sql = "select x.*,y.total_undelivered from 
				(
				select *,
				COALESCE(sum(if(transtype='Payment' or transtype='Credit Memo',amount * -1,if(transtype='Cash',0,amount))),0) as total_bal 
				from (
				select tbl_main.*,c.customer_name
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt) as tbl_main
				left join tbl_customers c on tbl_main.cust_id=c.cust_id) as tbl group by cust_id
				) as x
				left join
				(
				select cust_id,amount,date_delivered,transtype,coalesce(sum(if(date_delivered is null,amount,0)),0) as total_undelivered from (select tbl_main.* 
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt where transtype!='Cash' and transtype!='Payment' and transtype!='Adjustment' and transtype!='Credit Memo') as tbl_main) as tbl group by cust_id
				) as y on x.cust_id=y.cust_id where x.cust_id='{$info['cust_id']}'
				";	
		$qry = mysql_query($sql);
		$cust = mysql_fetch_assoc($qry);
		?>
		<div style="width:100%;height:100%;">
			<div style="float:left;width:80px;margin-right:10px;">Floor:</div>
			<input readonly type="text" style="float:left;width:150px;" value="<?=$info['xfloor']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:80px;margin-right:10px;">Unit:</div>
			<input readonly type="text" style="float:left;width:150px;" value="<?=$info['unit']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:80px;margin-right:10px;">Owner:</div>
			<input readonly type="text" style="float:left;width:150px;" value="<?=$info['customer_name']?>"/>
			<div style="clear:both;height:5px;"></div>
			<fieldset>
					<legend>Financial Obligation</legend>
					<div style="float:left;margin-right:5px;width:120px;">Total Balance:</div>
					<div style="float:left;width:"><?= number_format($cust['total_bal'],2)?></div>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:5px;width:120px;">Undelivered:</div>
					<div style="float:left;color:red;"><?= number_format($cust['total_undelivered'],2)?></div>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:5px;width:120px;">Actual Balance:</div>
					<div style="float:left;"><?= number_format($cust['total_bal']-$cust['total_undelivered'],2)?></div>
				</fieldset>
			<div style="clear:both;height:5px;"></div>
			<textarea style="width:100%;height:150px;"><?=$info['details']?></textarea>
		</div>
		<?
	break;
	case'viewReportPopup':
		?>
		<div style="float:left;margin-right:10px;width:120px;">Counter</div>
		<input type="text" name="counter_num" id="counter_num" style="float:left;width:100px;" value="1"/>
		<div style="clear:both;height:10px;"></div>
		<div style="float:left;margin-right:10px;width:120px;">Reading</div>
		<input type="text" name="reading" id="reading" style="float:left;width:100px;" value="1"/>
		<div style="clear:both;height:10px;"></div>
		<div style="float:left;margin-right:10px;width:120px;">Start Date</div>
		<input type="text" name="begdate" id="begdate" style="float:left;width:100px;" />
		<div style="clear:both;height:10px;"></div>
		<div style="float:left;margin-right:10px;width:120px;">End Date</div>
		<input type="text" name="enddate" id="enddate" style="float:left;width:100px;" />
		<div style="clear:both;height:10px;"></div>
		<input type="button" value="View" style="height:30px;width:150px;" onclick="viewSalesJournal()"/>
		<script>
			$(document).ready(function() {
				$('#begdate').datepicker({
					inline: true,
					dateFormat:"yy-mm-dd"
				});
				$('#enddate').datepicker({
					inline: true,
					dateFormat:"yy-mm-dd"
				});
			});
		</script>
		<?
	break;
	case'showReadingList':
		$sql = "select * from tbl_reading where reading_num='{$_REQUEST['reading_num']}'";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
	?>
	<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th style="border:none;">Menu</th>
			<th style="border:none;">Reading Num</th>
			<th style="border:none;">Counter</th>
			<th style="border:none;">Date Start</th>
			<th style="border:none;">Time Start</th>
			<th style="border:none;">Date End</th>
			<th style="border:none;">Time End</th>
			<th style="border:none;">Amount</th>
			<th style="border:none;">Cost</th>
			<th style="border:none;">Gain</th>
			<th style="border:none;">Menu</th>
		</tr>	
		<? while($row = mysql_fetch_assoc($qry)){ 
		?>
			<tr>
				<td class="data_lightgray"><a href="javascript:viewReport('reports/sales_reports.php?rep=perreading&reading=<?=$row['reading_num']?>&counter=<?=$row['counter']?>&type=<?=$tbl?>')">perItem</a></td>
				<td class="data_lightgray"><a href="javascript:reprintReading(<?=$row['reading_num']?>,<?=$row['counter']?>)"><?= $row['reading_num']?></a></td>
				<td class="data_lightgray"><?= $row['counter']?></td>
				<td class="data_lightgray"><?= date("D M j, Y",strtotime($row['start_date'])) ?></td>
				<td class="data_lightgray"><?= $row['start_time']?></td>
				<td class="data_lightgray"><?= $row['end_date']?></td>
				<td class="data_lightgray"><?= $row['end_time']?></td>
				<td class="data_lightgray" style="text-align:right;"><?= number_format($row['amount'],2) ?></td>
				<td class="data_lightgray"><?=number_format($cost = $db->sumTotalCost($row['reading_num'],$row['counter'],$tbl."_sales_items"),2)?></td>
				<td class="data_lightgray" style="text-align:right;"><?= number_format($row['amount']-$cost,2) ?></td>
				<?php if($row['end_date']=="0000-00-00"){ ?>
					<td class="data_lightgray"><a href="javascript:closeTx(<?=$row['reading_num']?>,<?=$row['counter']?>)">CloseTx</a></td>
				<?php }else{ ?>
					<td class="data_lightgray">
						<img onclick="viewReport('./reports/vouchering.php?refid=<?=$row['glref'] ?>&center=<?=$_SESSION['connect']?>&type=SJ')" src="./images/search.png" style="width:20px;height:20px;float:left;" />
					</td>
				<?php } ?>
			</tr>
		<? $total_amt+=$row['amount'];$total_cost+=$cost;} ?>
		<tr>
			<th style="border:none;" colspan="7">Sub Total</th>
			<th style="border:none;"><?= number_format($total_amt,2) ?></th>
			<th style="border:none;"><?= number_format($total_cost,2) ?></th>
			<th style="border:none;"><?= number_format($total_amt-$total_cost,2) ?></th>
			<th style="border:none;">&nbsp;</th>
		</tr>
	</table>
	<script>
		function closeTx(reading_num,counter){
			var r=confirm("Are you sure you want to CloseTx?");
			if (r==true){
				$.ajax({
					url: './content/pos_ajax.php?execute=closeTx&readingnum='+reading_num+'&counter_num='+counter,
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
			
		}
	</script>
	<?
	break;
	case'closeTx':
		$qry_endreading = mysql_query("update tbl_reading set end_date=now(),end_time=curtime(),
							amount=(select sum(amount) as total_sales from `tbl_sales_receipt_{$_REQUEST['counter_num']}` 
									where reading='".$_REQUEST['readingnum']."' and counter_num='".$_REQUEST['counter_num']."' and type!='VOID' group by reading,counter_num limit 1) 
						where reading_num='".$_REQUEST['readingnum']."' and counter='".$_REQUEST['counter_num']."'");
		if(!$qry_endreading){
			echo mysql_error();
		}else{
			echo "success";
		}
	break;
	case'zEndReading':
		$checkopen = $db->resultArray("*","tbl_reading","where end_date='0000-00-00' and reading_num='".$_REQUEST['reading_num']."'");
		if($checkopen){
			$flag=false;
			foreach($checkopen as $k => $v){
				if($flag)$station.=",";
				$station.=$v['counter'];
				$flag=true;
			}
			echo "There's still open reading per station, pls perform End Reading every station!\n\nStation:$station";
		}else{
			$qry_endreading2 = mysql_query("update tbl_reading_server set end_date=now(),end_time=curtime(),total_amount=(select sum(amount) as total_sales from `tbl_reading` where reading_num='".$_REQUEST['reading_num']."' group by reading_num limit 1) where reading_num='".$_REQUEST['reading_num']."'");
			if(!$qry_endreading2){
				echo mysql_error();
			}else{
				echo "success";
			}
		}
	break;
	case'setCategory':
		$cat = $db->getWHERE("*","tbl_category","where category_id='{$_REQUEST['catid']}'");
		$_SESSION['category']['catid'] = $_REQUEST['catid'];
		$_SESSION['category']['catname']=$cat['category_name'];
		echo "success";
	break;
	case'selectcategory':
		$cat = $db->resultArray("*","tbl_category","order by category_name asc");
		foreach($cat as $key => $val){
			echo '<button id="bt19" class="buthov" type="button" onclick="selectCategory('.$val['category_id'].')" style="height:50px;width:100%;float:left;font-size:11px;">'.$val['category_name'].'</button><br/>';
		}
	break;
	case'cleandb':
		$tables = array('tbl_cashdetails','tbl_cashout','tbl_chequepayment_details',
			'tbl_creditcardpayment_details','tbl_customers','tbl_customers_trans','tbl_customers_trans_delivery',
			'tbl_order_items','tbl_order_receipt','tbl_reading','tbl_sales_receipt_'.$_SESSION['counter_num'],'tbl_sales_items','tbl_sales_moreinfo','tbl_sales_splitpayment',
			'tbl_voucherdeduction_details','tbl_voucherpayment_details','tbl_stockin_header','tbl_stockin_items',
			'tbl_stockout_header','tbl_stockout_items','tbl_stocktransfer_header','tbl_stocktransfer_items','tbl_po_header','tbl_po_items');
		foreach($tables as $key=>$val){
			$qry = mysql_query("TRUNCATE `$val`");
			if(!$qry){
				echo "Error $val:".mysql_error()."<br/>";
			}else{
				echo "$val are now Empty...<br/>";
			}
		}
	break;
	case'dynamic_supplier':
		if($_REQUEST['type']=="Received from Supplier"||$_REQUEST['type']=="Return Stock"||$_REQUEST['type']=="PO"){ 
			$qrysup = mysql_query("select * from tbl_supplier order by supplier_name asc");
		?>
			<option value="">Select Supplier</option>
			<?php while($rowsup = mysql_fetch_assoc($qrysup)){ ?>
				<option <?=$_SESSION[$sessiontype.'_header']['supplier_id']==$rowsup['id']?'selected':''?> value="<?=$rowsup['id']?>"><?=$rowsup['supplier_name']?></option>
			<?php } ?>
		<?php }elseif($_REQUEST['type']=="Sold Stock"){ 
			$qrysup = mysql_query("select *,cust_id id from tbl_customers");
		?>
			<option value="">Select Customer</option>
			<?php while($rowsup = mysql_fetch_assoc($qrysup)){ ?>
				<option <?=$_SESSION[$sessiontype.'_header']['supplier_id']==$rowsup['id']?'selected':''?> value="<?=$rowsup['id']?>"><?=$rowsup['customer_name']?></option>
			<?php } ?>
		<?php }else{ 
			$branches=$db->resultArray("id,name","tbl_branch","where name!='{$_SESSION['connect']}'");
			?>
				<option value="">Select Branch</option>
				<?php foreach($branches as $key=>$val){ ?>
					<option <?=$_SESSION[$sessiontype.'_header']['supplier_id']==$val['id']?'selected':''?> value="<?=$val['id']?>"><?=$val['name']?></option>
				<?php } ?>
		<?php } 
	break;
	case'getTeachername':
		switch($_REQUEST['schlevel']){
			case'College':
				$sql="select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_college_ucmain group by InstructorNo";
			break;
			case'HighSchool':
				//$sql="select Subject,SubjType from data_highsch_ucmain group by Subject,SubjType";
				$sql="select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_highsch_ucmain group by InstructorNo";
			break;
			case'Elementary':
				//$sql="select Subject,SubjType from data_elem_ucmain group by Subject,SubjType";
				$sql="select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_elem_ucmain group by InstructorNo";
			break;
			default:
				$sql="
				select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_college_ucmain group by InstructorNo
				union
				select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_highsch_ucmain group by InstructorNo
				union
				select InstructorNo,concat(InsLastName,',',InsFirstName) as name from data_elem_ucmain group by InstructorNo";
			break;
		}
		
		$qry = mysql_query($sql);
		while($row=mysql_fetch_assoc($qry)){
			$rec[]=$row;
		}
		echo '{"tbl_rec":'. json_encode($rec) .'}';
	break;
	case'getSubject':
		// switch($_REQUEST['schlevel']){
			// case'College':
				// $sql="select Subject,SubjType from data_college_ucmain group by Subject,SubjType";
			// break;
			// case'HighSchool':
				// $sql="select Subject,SubjType from data_highsch_ucmain group by Subject,SubjType";
			// break;
			// case'Elementary':
				// $sql="select Subject,SubjType from data_elem_ucmain group by Subject,SubjType";
			// break;
			// default:
				// $sql="
				// select Subject,SubjType from data_college_ucmain group by Subject,SubjType
				// union
				// select Subject,SubjType from data_highsch_ucmain group by Subject,SubjType
				// union
				// select Subject,SubjType from data_elem_ucmain group by Subject,SubjType";
			// break;
		// }
		if($_REQUEST['schlevel']=="all"){
			$sql="select code,subject_name from tbl_subject_name";
		}else{
			$sql="select code,subject_name from tbl_subject_name where school_level='{$_REQUEST['schlevel']}'";
		}
		$qry = mysql_query($sql);
		while($row=mysql_fetch_assoc($qry)){
			$rec[]=$row;
		}
		echo '{"tbl_rec":'. json_encode($rec) .'}';
	break;
	case'loadpo':
		unset($_SESSION[$_REQUEST['sessiontype'].'_header']);
		unset($_SESSION[$_REQUEST['sessiontype']]);
		$db->openDb("main");
		$header = $db->getWHERE("*","tbl_po_header","where id='{$_REQUEST['ponum']}'");
		$status = explode(" ",$header['status']);
		if($status[0]=='PO'){
			$_SESSION["{$_REQUEST['page']}_header"]=array('date'=>$header['date'],'supplier_id'=>$header['supplier_id'],'remarks'=>$header['remarks'],'poid'=>$header['id'],'frompo'=>true,'pototal'=>$header['total']);
			$sql = "select * from tbl_po_items where stockin_refid='{$_REQUEST['ponum']}'";
			$qry = mysql_query($sql);
			while($row = mysql_fetch_assoc($qry)){
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION["{$_REQUEST['page']}"][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>0,
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$row['skuid'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost'],
					"poqty"=>$row['qty']
				);
			}
			echo "success";
		}elseif($status[0]=='Partial'){
			$_SESSION["{$_REQUEST['page']}_header"]=array('date'=>$header['date'],'supplier_id'=>$header['supplier_id'],'remarks'=>$header['remarks'],'poid'=>$header['id'],'frompo'=>true,'pototal'=>$header['total'],'refid'=>$header['rr_num']);
			$sql = "select * from tbl_po_items where stockin_refid='{$_REQUEST['ponum']}'";
			$qry = mysql_query($sql);
			while($row = mysql_fetch_assoc($qry)){
				$info = $con->MySQLigetWHERElocal("*","tbl_stockin_items","where stockin_refid='{$header['rr_num']}' and skuid='{$row['skuid']}'");
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION["{$_REQUEST['page']}"][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>($info['qty']?$info['qty']:0),
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$row['skuid'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost'],
					"poqty"=>$row['qty']
				);
			}
			echo "success";
		}else{
			echo "PO Number: {$_REQUEST['ponum']} Are already received...";
		}
	break;
	case'loadpo_old':
		unset($_SESSION[$_REQUEST['sessiontype'].'_header']);
		unset($_SESSION[$_REQUEST['sessiontype']]);
		$db->openDb("main");
		$header = $db->getWHERE("*","tbl_po_header","where id='{$_REQUEST['ponum']}'");
		//$header = mysql_fetch_assoc(mysql_query("select * from tbl_po_header where id='{$_REQUEST['ponum']}'"));
		if($header['status']!='RECEIVED'){
			$_SESSION[$_REQUEST['sessiontype'].'_header']=array('date'=>$header['date'],'supplier_id'=>$header['supplier_id'],'remarks'=>$header['remarks'],'poid'=>$header['id'],'frompo'=>true);
			$sql = "select * from tbl_po_items where stockin_refid='{$_REQUEST['ponum']}'";
			$qry = mysql_query($sql);
			while($row = mysql_fetch_assoc($qry)){
				$_SESSION['count'] = $row['count']==0?0:$_SESSION['count']+1;
				$_SESSION[$_REQUEST['sessiontype']][$row['barcode']]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$row['barcode'],
					"prod_name"=>$row['item_desc'],
					"qty"=>0,
					"unit"=>$row['unit'],
					"total"=>$row['total'],
					"discount"=>$row['discount'],
					"sku"=>$row['skuid'],
					"divmul"=>$row['divmul'],
					"cost"=>$row['cost'],
					"poqty"=>$row['qty']
				);
			}
			echo "success";
		}else{
			echo "PO Number: {$_REQUEST['ponum']} Are already received...";
		}
	break;
	case"touch_screen":
		$cat = $db->resultArray("*","tbl_category","order by category_name asc");
		?>
		<style>
		td{
		padding:0 !important;
		}
		</style>
		<div class="top">
			<div class="content" style="min-height:150px;">
				<div style="float:left;width:300px;height:500px;overflow:auto;" id="content">
					<fieldset style="padding:10px;min-height:500px;">
						<legend>&nbsp; CATEGORY &nbsp;</legend>
						<table class="tbl">
						<?php 
							$i=0;
							foreach($cat as $key => $val){
								$btn = '<button id="bt19" class="buthov" type="button" onclick="showProd('.$val['category_id'].')" style="height:50px;width:100%;float:left;font-size:11px;">'.$val['category_name'].'</button>';
								$tbl.=$i%2==0?"<tr><td>$btn</td>":"<td>$btn</td></tr>";
								$i++;
							}
							echo $tbl;
						?>
						</table>
					</fieldset>
				</div>
				<div style="float:right;width:680px;">
					<div id="prodarea" style="height:450px;overflow:auto;width:60%;float:left;">
					</div>
					<div style="height:450px;width:40%;float:left;height:450px;font-size:10px;">
						<div style="overflow:auto;height:400px;">
							<table class="navigateableMain" id="mytbl_ts" cellspacing="0" cellpadding="0" width="100%">
								<thead>
									<tr>
										<th style="border:none;">Bcode</th>
										<th style="border:none;">Desc</th>
										<th style="border:none;">Price</th>
										<th style="border:none;">Qty</th>
										<th style="border:none;">Amount</th>
									</tr>
								</thead>
								<tbody>
									<?php if(isset($_SESSION['sales'])){ $change="";$xtotal=0;?>
										<?php $count=1; foreach($db->subval_sort($_SESSION['sales'],'count',arsort) as $val){ ?>
											<tr>
												<td><a href="javascript:backToBarcode();" class="activationMain"><?php echo $val['bcode']; ?></a></td>
												<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
												<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
												<td style="text-align:right;"><?php echo $val['qty'] ." ". $val['unit'] ?></td>
												<td  style="text-align:right;"><?php echo number_format($val['total'],2) ?></td>
												<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
												<td  style="text-align:right;display:none;"><?php echo $val['cost'] ?></td>
											</tr>
										<?php $xtotal+=$val['total'];$count++;} ?>
									<?php } ?>
								</tbody>
								
							</table>
						</div>
						<div style="clear:both;height:5px;"></div>
							<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
								<thead>
									<tr>
										<th colspan="3">Total</th>
										<th colspan="2" style="font-size:15px;text-align:right;color:red;"><?=number_format($xtotal,2)?></th>
									</tr>
								</thead>
							</table>
					</div>
					<div style="clear:both;height:5px;"></div>
					<div style="width:100%;">
						<fieldset style="float:left;">
							<legend>Menu:</legend>
							<input id="bt6" class="buthov" type="button" value="Close" onclick="xclose();" style="height:40px;width:100px;float:left;font-size:11px;"/>
							<input id="bt20" class="buthov" type="button" value="Up"  style="height:40px;width:100px;float:left;font-size:11px;"/>
							<input id="bt21" class="buthov" type="button" value="Down"  style="height:40px;width:100px;float:left;font-size:11px;"/>
						</fieldset>
					</div>
				</div>
				<div style="clear:both;height:10px;"></div>
			</div>
		</div>
		<script>
		var step = 25;
		var scrolling = false;
		$("#bt20").bind("click", function(event) {
			event.preventDefault();
			$("#content").animate({
				scrollTop: "-=" + step + "px"
			});
		});
		$("#bt21").bind("click", function(event) {
			event.preventDefault();
			$("#content").animate({
				scrollTop: "+=" + step + "px"
			});
		});
		function scrollContent(direction) {
			var amount = (direction === "up" ? "-=1px" : "+=1px");
			$("#content").animate({
				scrollTop: amount
			}, 1, function() {
				if (scrolling) {
					scrollContent(direction);
				}
			});
		}
		$(document).ready(function() {
			jQuery.tableNavigationMain();
			return false;
		});
		$("#mytbl_ts").bind('keydown',function(e){
			var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
			if(chCode==46){ //pressing delete button
				ts_delProdsale($("#mytbl_ts tbody tr.selected").find('td:eq(0)').text());
			}else if(chCode==113){ //pressing f2
				ts_qtyclick($("#mytbl_ts tbody tr.selected").find('td:eq(0)').text());
			}else if(chCode==119){ //pressing f8
				ts_priceclick($("#mytbl_ts tbody tr.selected").find('td:eq(0)').text());
			}
		});
		function ts_priceclick(bcode,type){
			var newprice = prompt("Enter new price");
			if(newprice !=""){
				if($.isNumeric(newprice)){
					if(bcode!=""){
						var price_markup = new Number('<?=$allow_cost_markup?>');
						var allow_price_below_cost = '<?=$allow_price_below_cost?"true":"false";?>';
						var cost = new Number($("tr.selected").find('td:eq(7)').text())==0?new Number($("tbody tr:first").find('td:eq(7)').text()):new Number($("tr.selected").find('td:eq(7)').text());
						if(newprice>(cost * (1+ price_markup))){
							alert("Not allowed Price Mark-Up above "+(price_markup*100)+"%");
							return false;
						}
						if(allow_price_below_cost=="false"){
							if(newprice < cost){
								alert("Not allowed price below cost...");
								$('#xlogin').dialog({
									autoOpen: false,
									width: 400,
									height: 170,
									modal: true,
									resizable: false,
									close:function(event){$('#barcode').focus();},
									closeOnEscape: true,
									title:'Below Cost Authorization',
									open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
								});
								htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=xlogin&type_name=belowcost&barcode='+bcode+'&newprice='+newprice,async:false});
								$('#xlogin').html(htmlobj.responseText);
								$('#xlogin').dialog('open');
								return;
							}
						}
							$.ajax({
								url: './content/pos_ajax.php?execute=pricechange&barcode='+bcode+'&newprice='+newprice,
								type:"POST",
								success:function(data){
									if(data=="success"){
										showTS();
									}else{
										alert(data);
									}
								}
							});
					}
				}else{
					alert("Please input number value only...");
				}
			}else{
				alert("Please provide new price...");
			}
		}
		function ts_qtyclick(bcode,type,divmul){
			var newqty = prompt("Enter new qty");
			if(newqty !=""){
				if($.isNumeric(newqty)){
					if(bcode!=""){
						var sessiontype = '&sessiontype='+getParam('page');
						var url = './content/pos_ajax.php?execute=qtychange&barcode='+bcode+'&newqty='+newqty+sessiontype+'&divmul='+divmul;
						$.ajax({
							url:url,
							type:"POST",
							success:function(data){
								if(data=="success"){
									showTS();
								}else{
									alert(data);
								}
							}
						});
					}
				}else{
					alert("Please input number value only...");
				}
			}else{
				alert("Please provide new qty...");
			}
		}
		function ts_delProdsale(val,type){
			if(type=="barcode"){
				alert("Pls select item to delete...");return false;
			}
			var id = $("tbody tr.selected").find('td:eq(8)').text();
			$.ajax({
				url: './content/pos_ajax.php?execute=delprodsale',
				type:"POST",
				data: {ref:val,type:type,id:id},
				success:function(data){
					if(data=="success"){
						showTS();
					}else{
						alert(data);
					}
				}
			});
		}
		function xclose(){
			//window.close();
			//window.opener.backToBarcode();
			window.location.reload();
		}
		function showProd(catid){
			htmlobj=$.ajax({url:'./touchscreen/touchscreen_ajax.php?execute=prodlist&catid='+catid,async:false});
			$('#prodarea').html(htmlobj.responseText);
		}
		</script>
		<?
	break;
	case"reprint";
		?>
		<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Menu</th>
				</tr>
			</thead>
			<tbody>
				<tr><td><a class="activation" href="javascript:viewReport('./reports/reading_end.php?readingnum=');">X-Reading</a></td></tr>
				<tr><td><a class="activation" href="javascript:viewReport('./reports/sales_reports_sm.php?rep=perreading&counter=<?=$_SESSION['counter_num']?>&reading=');">Product Sold</a></td></tr>
				<tr><td><a class="activation" href="javascript:viewReport('./reports/sales_journal.php?counter=<?=$_SESSION['counter_num']?>&reading=');">Sales Journal</a></td></tr>
			</tbody>
		</table>
		<?
	break;
	case"restaurant_mode":
		?>
		<div style="width:380px;text-align:center;">
			<input onclick="orderButton();" type="button" value="Order" style="height:50px;width:250px;"/>
			<input onclick="saleButton()" type="button" value="Sale" style="height:50px;width:250px;"/>
		</div>
		<script>
			function saleButton(){
				$('#dialogbox').dialog('close');
				openPaymentBox();
				//boxTenderPayment();
			}
			function orderButton(){
				var table = prompt("Table Number");
				if(table !=""){
					if($.isNumeric(table)){
						var newInput = document.createElement("INPUT");
							newInput.name = "table";
							newInput.type = "text";
							newInput.value = table;
						document.frm_pos.appendChild(newInput);
						document.frm_pos.xtype.value="order";
						document.frm_pos.submit();
					}else{
						alert("Please input number value only...");
					}
				}else{
					alert("Please provide Table Number...");
				}
			}
		</script>
		<?
	break;
	case"shoppers_card":
		?>
		<div style="float:left;margin-right:10px;">Card ID:</div>
		<input type="text" name="card_id" id="card_id" onchange="selectCard_ID(this.value)" />
		<div style="clear:both;height:5px;"></div>
		<div id="shoppers_info" style="font-size:20px;"></div>
		<script>
			function selectCard_ID(val){
				var card_id=$("#card_id").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=selectCard&card_id='+val,
					type:"POST",
					success:function(data){
						$("#shoppers_info").html(data);
					}
				});
			}
		</script>
		<?
	break;
	case"selectCard":
		$sql = "select * from tbl_shoppers_card where shoppers_card_id='".$_REQUEST['card_id']."' limit 1";
		$qry = mysql_query($sql);
		$info = mysql_fetch_assoc($qry);
		$_SESSION['shoppers_card'] = array(
			'shoppers_card_id'=>$info['shoppers_card_id'],
			'name'=>$info['first_name']." ".$info['last_name']);
		echo $_SESSION['shoppers_card']['name']."</br>";
		echo $info['address'];
	break;
	case"updatesalesvoid":
		$void = $db->salesvoid($_REQUEST['refid']);
		echo $void;
	break; 
	case"salesvoid":
		?>
		<div style="float:left;padding-top:10px;">
		<div style="float:left;width:130px;">Receipt #:</div><input type="text" name="salesvoid_receipt_num" id="salesvoid_receipt_num" onchange="$('#xsalesvoid').focus()" style="float:left;width:140px;"/>
		</div>
		<input type="button" value="Execute" name="salesvoid" id="xsalesvoid" onclick="updatesalesvoid();" style="float:left;height:40px;width:100px;margin-left:5px;"/>
		<?
	break;
	case"changePass":
		?>
		<form method="post">
			<div style="float:left;width:130px;">Current Password:</div><input type="password" name="current_password" style="float:left;width:140px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:130px;">New Password:</div><input type="password" name="new_password1" style="float:left;width:140px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:130px;">Retype Password:</div><input type="password" name="new_password2" style="float:left;width:140px;"/>
			<div style="clear:both;height:5px;"></div>
			<input type="submit" value="Login" name="changepass" style="height:40px;width:100px;"/>
		</form>
		<?
	break;
	case "displayChange":
		?>
		<div style="text-align:center;width:500px;padding:5px;">
			<span style="float:left;font-size:25px;">Don't forget your change! <span style="color:red;">Php <?=number_format($_REQUEST['change'],2)?></span></span>
			<div style="clear:both;height:10px;"></div>
			<?php /*
			<form name="frmchangedisp" method="post" action="<?=$location?>">
			<input type="submit" id="changebutton" value="Enter" onblur="stillFocus();" style="margin-left:10px;float:right;width:200px;height:60px;"/>
			</form>
			*/?>
			<input type="button" id="changebutton" value="Enter" onclick="reFresh();" style="margin-left:10px;float:right;width:200px;height:60px;"/>
		</div>
		<script>
		function stillFocus(){
			document.frmchangedisp.submit();
		}
		function reFresh(){
			window.location=document.URL;
		}
		</script>
		<?
	break;
	case"order_list":
		$sql = "select * from tbl_order_receipt order by id asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
		?>
		<div style="height:405px;overflow:auto;">
			<table id="orderlist" class="navigateable" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th><?= $view==4?'Table':'Receipt' ?></th>
						<th>Patient Name</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<? 	while($row = mysql_fetch_assoc($qry)){ ?>
					<tr>
						<td><a href="javascript:transferOrder('<?php echo $row['receipt_id'] ?>')" class="activation"><?php echo $row['receipt_id'] ?></a></td>
						<td><?= $row['studentname']?></td>
						<td><?= number_format($row['amount'],2) ?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
		<!--div style="clear:both;height:20px;"></div>
		<input type="button" value="Print Bill" style="width:200px;height:50px;float:left;" onclick="printBill()"/>
		<input type="button" value="Join Order" style="width:100px;height:50px;float:right;" onclick="joinOrder()"/-->
		<script>
			function printBill(){
				var ref = $("tbody tr.selected").find('td:eq(0)').text();
				loadiFrame("./reports/receipt_bill.php?receipt_num="+ref);
				PrintIframe(0,'ORDER');
			}
		</script>
		<?
	break;
	case "prodtrans":
		$tbl = $_REQUEST['type']?$_REQUEST['type']:"tbl";
		$reading = $_REQUEST['reading']&&$tbl=="old"?"and reading='{$_REQUEST['reading']}'":"";
		$sku_id = $_REQUEST['sku_id'];
		// $sql = "select * from (
		// (SELECT DATE_FORMAT(tbl_stockin_header.date,'%Y-%m-%d') AS dated,
			// tbl_stockin_items.item_desc prodname,
			// qty,unit,CONCAT('IN') AS stat,concat(tbl_stockin_header.status,' REF:',stockin_refid,' ',tbl_stockin_header.remarks) remarks,divmul,concat(0) selling,cost,tbl_stockin_items.id,concat('tbl_stockin_items') as tblname
			// FROM tbl_stockin_items LEFT JOIN tbl_stockin_header ON tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='$sku_id') 
		// UNION (SELECT DATE_FORMAT(TIMESTAMP,'%Y-%m-%d') AS dated,
			// item_desc prodname,qty,unit,CONCAT('OUT') AS stat,
			// CONCAT('<a href=\"javascript:viewReceipt(',receipt,',',reading,',',counter,');\">Receipt: ',receipt,' Counter: ',counter,' Reading: ',reading,'</a>') AS remarks,divmul,selling,cost,{$tbl}_sales_items.id,concat('{$tbl}_sales_items') as tblname 
			// FROM {$tbl}_sales_items WHERE skuid='$sku_id' $reading)
		// UNION (SELECT DATE_FORMAT(tbl_stockout_header.date,'%Y-%m-%d') AS dated,
			// tbl_stockout_items.item_desc prodname,
			// qty,unit,CONCAT('OUT') AS stat,concat(tbl_stockout_header.status,' REF:',stockin_refid,' ',tbl_stockout_header.remarks) remarks,divmul,concat(0) selling,cost,tbl_stockout_items.id,concat('tbl_stockout_items') as tblname
			// FROM tbl_stockout_items LEFT JOIN tbl_stockout_header ON tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='$sku_id')
			// )as tbl order by dated asc,stat desc;";
		$sql = "select * from (
		(SELECT null as siref,DATE_FORMAT(tbl_stockin_header.date,'%Y-%m-%d') AS dated,
			tbl_stockin_items.item_desc prodname,
			qty,unit,CONCAT('IN') AS stat,concat(tbl_stockin_header.status,' REF:',stockin_refid,' ',tbl_stockin_header.remarks) remarks,divmul,concat(0) selling,cost,tbl_stockin_items.id,concat('tbl_stockin_items') as tblname
			FROM tbl_stockin_items LEFT JOIN tbl_stockin_header ON tbl_stockin_items.stockin_refid=tbl_stockin_header.id WHERE skuid='$sku_id') 
		UNION (SELECT null as siref,DATE_FORMAT(TIMESTAMP,'%Y-%m-%d') AS dated,
			item_desc prodname,qty,unit,CONCAT('OUT') AS stat,
			CONCAT('<a href=\"javascript:viewReceipt(',receipt,',',reading,',',counter,');\">Receipt: ',receipt,' Counter: ',counter,' Reading: ',reading,'</a>') AS remarks,divmul,selling,cost,{$tbl}_sales_items.id,concat('{$tbl}_sales_items') as tblname 
			FROM {$tbl}_sales_items WHERE skuid='$sku_id' $reading)";
		switch($_SESSION['settings']['system_name']){
			case"TKC":
			case"RTK":
				$sql.=" UNION (SELECT refid as siref,DATE_FORMAT(date,'%Y-%m-%d') AS dated,
					item_spec prodname,qty,unit,CONCAT('OUT') AS stat,
					CONCAT(' Sales Invoice #',refid) AS remarks,divmul,unitprice,0 cost,refid,concat('tbl_sales_invoice') as tblname 
					FROM (select a.*,b.date from tbl_sales_invoice_items a 
						left join tbl_sales_invoice_header b on a.refid=b.id 
						) invoice WHERE skuid='$sku_id') ";
			break;
		}
		$sql.=" UNION (SELECT null as siref,DATE_FORMAT(tbl_stockout_header.date,'%Y-%m-%d') AS dated,
			tbl_stockout_items.item_desc prodname,
			qty,unit,CONCAT('OUT') AS stat,concat(tbl_stockout_header.status,' REF:',stockin_refid,' ',tbl_stockout_header.remarks) remarks,divmul,concat(0) selling,cost,tbl_stockout_items.id,concat('tbl_stockout_items') as tblname
			FROM tbl_stockout_items LEFT JOIN tbl_stockout_header ON tbl_stockout_items.stockin_refid=tbl_stockout_header.id WHERE skuid='$sku_id')
			)as tbl order by dated asc,stat desc;";

		$qry = mysql_query($sql);
		$prod = $db->resultArray("a.sku_id,a.product_name","tbl_product_name a","where a.product_name !='' order by product_name asc");
		?>
		<?php /*
		<fieldset>
			<legend>Transfer Records To:</legend>
			<div style="float:left;width:130px;margin-right:5px;">Product Name:</div>
			<select name="product_name" id="product_name" class="chosen-select" style="z-index:999999;" tabindex="1">
				<option value="">Select Product Name</option>
			<?php foreach($prod as $key => $val){ ?>
				<option value="<?=$val['sku_id']?>"><?=$val['product_name']?></option>
			<?php } ?>
			</select>
			<input type="button" value="Transfer" onclick="transferProd()" style="width:150px;float:right;"/>
		</fieldset>
		*/ ?>
		<div style="clear:both;height:5px;"></div>
		<?
		echo "<div style='width:100%;height:300px;overflow:auto;'>";
		echo '<table class="tbluom" cellspacing="0" cellpadding="0" width="100%" border="1" style="font-size:10px;">
				<tr>
					<th>Date</th>
					<th>Qty</th>
					<th>Unit</th>
					<th>Inv</th>
					<th>Status</th>
					<th>Product Name</th>
					<th>Remarks</th>
					<th>Selling</th>
					<th>Cost</th>
					<th>TBL</th>
				</tr>';
		$bal=0;
		while($row=mysql_fetch_assoc($qry)){
			$color ="";
			if($row['stat']=="OUT"){
				$color = "color:red;"; 
				$bal -= $row['qty'];
			}else{
				$bal += $row['qty'];
			}
			if($row['tblname']=="tbl_sales_invoice"){
				$tbl = "<a href='./reports/dynamic_invoicing_rtk.php?refid=".$row['siref']."&tbltype=sales_invoice' target='_blank'>View Invoice</a>";
			}else{
				$tbl = $row['tblname'];
			}
			echo "<tr>
				<td style='$color'>".$row['dated']."</td>
				<td style='$color;text-align:right;'>".$row['qty']."</td>
				<td style='$color' ondblclick='changeUnit(".$row['id'].",\"".$row['tblname']."\",\"".$sku_id."\")'>".$row['unit']."</td>
				<td style='text-align:right;$color'>".$bal."</td>
				<td style='$color'>".$row['stat']."</td>
				<td>".$row['prodname']."</td>
				<td style='$color'>".$row['remarks']."</td>
				<td style='$color'>".number_format($row['selling'],2)."</td>
				<td style='$color' ondblclick='changeCost(".$row['id'].",\"".$row['tblname']."\",\"".$sku_id."\")'>".number_format($row['cost'],2)."</td>
				<td>".
				$tbl."</td>
			</tr>"; //$db->outputInvBal($db->invBal($sku_id,$row['dated']),$sku_id) $db->outputInvBal($db->invBal($sku_id,$row['dated']),$sku_id)
		}
		echo "</table>";
		
		?>
		<link rel="stylesheet" href="../js/chosen/chosen.css">
		<script src="../js/chosen/chosen.jquery.js" type="text/javascript"></script>
		</div>
		<script>
			$(document).ready(function() {
				var config = {
				  '.chosen-select'           : {width: "350px;",style:"float:left;height:40px;"}
				}
				for (var selector in config) {
				  $(selector).chosen(config[selector]);
				}
			});
			function changeCost(id,tbl,sku_id){
				var newcost = prompt("Enter new cost");
				if(newcost !=""){
					if($.isNumeric(newcost)){
						$.ajax({
							url: '../content/pos_ajax.php?execute=costUpdate',
							data:{id:id,tbl:tbl,newcost:newcost},
							type:"POST",
							success:function(data){
								//window.location="<?=$location?>";
								viewTrans(sku_id,'');
							}
						});
					}else{
						alert("Please input number value only...");
					}
				}else{
					alert("Please provide new cost...");
				}
			}
			function changeUnit(id,tbl,sku_id){
				$.ajax({
					url: '../content/pos_ajax.php?execute=changeUnit',
					data:{id:id,tbl:tbl,sku_id:sku_id},
					type:"POST",
					success:function(data){
						$('#dialog2').dialog({
							autoOpen: false,
							width: 400,
							height: 300,
							modal: true,
							resizable: false,
							close:function(event){$('#barcode').focus();},
							title:'Select Unit'
						});
						$('#dialog2').html('<div style="overflow:auto;max-height:360px;">'+data+'</div>');
						$('#dialog2').dialog('open');
					}
				});
			}
			function viewReceipt(num,readingnum,counter) {
				if (window.showModalDialog) {
					window.showModalDialog('./receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt","dialogWidth:350px;dialogHeight:350px");
				} else {
					window.open('./receipt.php?receipt_num='+num+'&readingnum='+readingnum+'&counter='+counter+'&reprint=true',"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
				}
			}
			function transferProd(){
				var prodFrom = '<?=$sku_id?>';
				var prodTo = $("#product_name").val();
				if(prodFrom!=""){
					$.ajax({
						url: '../content/pos_ajax.php?execute=transProd',
						data:{prodFrom:prodFrom,prodTo:prodTo},
						type:"POST",
						success:function(data){
							alert(data);
							window.location=document.URL;
						}
					});
				}
			}
		</script>
		<?
	break;
	case'transProd':
		mysql_query("BEGIN");
		$prodTo=$_REQUEST['prodTo'];
		$prodFrom=$_REQUEST['prodFrom'];
		$sql1="update tbl_sales_items set skuid='{$prodTo}' where skuid='{$prodFrom}'";
		$sql2="update tbl_stockin_items set skuid='{$prodTo}' where skuid='{$prodFrom}'";
		$sql3="update tbl_stockout_items set skuid='{$prodTo}' where skuid='{$prodFrom}'";
		$qry1=mysql_query($sql1);
		$qry2=mysql_query($sql2);
		$qry3=mysql_query($sql3);
		if($qry1&&$qry2&&$qry3){
			mysql_query("COMMIT");
			echo "success";
		}else{
			mysql_query("ROLLBACK");
			echo "Un-Successfull";
		}
	
	break;
	case 'changeUnit':
		$sql = "select * from tbl_barcodes where sku_id='{$_REQUEST['sku_id']}'";
		$qry = mysql_query($sql);
		?>
		<table class="tbluom" cellspacing="0" cellpadding="0" width="100%" border="1" style="font-size:10px;">
			<tr>
				<th>--</th>
				<th>Unit</th>
				<th>Price</th>
				<th>Cost</th>
				<th>DivMul</th>
			</tr>
		<?php while($row=mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><input type="radio" name="unitval" id="unitval" value="<?=$row['barcode']?>,<?=$row['unit']?>,<?=$row['cost']?>,<?=$row['divmul']?>"/></td>
				<td><?=$row['unit']?></td>
				<td><?=$row['price']?></td>
				<td><?=$row['cost']?></td>
				<td><?=$row['divmul']?></td>
			</tr>
		<?php } ?>
		</table>
		<input type="button" value="Update" onclick="updateUnit('<?=$_REQUEST['id']?>','<?=$_REQUEST['tbl']?>','<?=$_REQUEST['sku_id']?>')" />
		<script>
			function updateUnit(id,tbl,sku_id){
				var radioval = $('input[name=unitval]:radio:checked').val().split(',');
				$.ajax({
					url: '../content/pos_ajax.php?execute=updateUnit',
					data:{id:id,tbl:tbl,barcode:radioval[0],unit:radioval[1],cost:radioval[2],divmul:radioval[3]},
					type:"POST",
					success:function(data){
						$('#dialog2').dialog('close');
						viewTrans(sku_id,'');
					}
				});
			}
		</script>
		<?
	break;
	case'updateUnit':
		$sql = "update {$_REQUEST['tbl']} set barcode='{$_REQUEST['barcode']}',unit='{$_REQUEST['unit']}',cost='{$_REQUEST['cost']}',divmul='{$_REQUEST['divmul']}' 
			where id='{$_REQUEST['id']}'";
		$qry = mysql_query($sql);
		if($qry){
			echo"success";
		}else{
			echo mysql_error();
		}
	break;
	case 'costUpdate':
		$sql = "update {$_REQUEST['tbl']} set cost='{$_REQUEST['newcost']}' where id='{$_REQUEST['id']}'";
		$qry = mysql_query($sql);
		if($qry){
			echo"success";
		}else{
			echo mysql_error();
		}
	break;
	case'uomDel':
		$sql = "delete from tbl_barcodes WHERE sku_id='".$_POST['sku']."' and barcode='".$_POST['bcode']."'";
		$qry=mysql_query($sql);
		if($qry){
			echo"success";
		}else{
			echo mysql_error();
		}
	break;
	case'uomSave':
		$basecost = $_POST['cost']/$_POST['divmul'];
		/*if($_POST['divmul']){
			$sql="insert into tbl_barcodes set sku_id='".$_POST['sku']."',
				barcode='".$_POST['bcode']."',price='".$_POST['price']."',cost='".$_POST['cost']."',unit='".$_POST['unit']."',divmul='".$_POST['divmul']."'";
		}else{
			$sql="UPDATE tbl_barcodes set price='".$_POST['price']."',
				cost='".$_POST['cost']."' WHERE sku_id='".$_POST['sku']."' and barcode='".$_POST['bcode']."'";
		}*/
		$sql="insert into tbl_barcodes set sku_id='".$_POST['sku']."',
				barcode='".$_POST['bcode']."',price='".$_POST['price']."',cost='".$_POST['cost']."',unit='".$_POST['unit']."',divmul='".$_POST['divmul']."'
				ON DUPLICATE KEY UPDATE 
				barcode='".$_POST['bcode']."',price='".$_POST['price']."',cost='".$_POST['cost']."',unit='".$_POST['unit']."',divmul='".$_POST['divmul']."'
				";
		$qry=mysql_query($sql);
		if($qry){
			$qryupdateall = "update tbl_barcodes set cost=($basecost*divmul) where sku_id='".$_POST['sku']."' and barcode not in ('{$_POST['bcode']}')";
			mysql_query($qryupdateall);
			echo"success";
		}else{
			echo mysql_error();
		}
	break;
	
	case'addsup':
		?>
		<div style="float:left;margin-right:10px;">Supplier Name: </div>
		<input type="text" name="supplier_name" id="supplier_name" style="float:right;"/>
		<div style="clear:both;height:5px;"></div>
		<input style="float:right;height:30px;width:100px;" type="button" value="Save" onclick="addsup()"/>
		<?php
	break;
	
	case'cashDetails_del':
		$sql="delete from tbl_cashdetails where id='".$_REQUEST['refid']."'";
		
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}
	break;
	case'cashDetails_add':
		$sql="insert into tbl_cashdetails set 
		counter_num='".$_SESSION['counter_num']."',
		reading='".$_SESSION['readingnum']."',
		money='".$_REQUEST['money']."',
		count='".$_REQUEST['count']."',
		total='".$_REQUEST['total']."'";
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}
	break;
	case'cashDetailsShow':
		$qry=mysql_query("select * from tbl_cashdetails where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");?>
			<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
				<tr>
					<th>Money</th>
					<th>Count</th>
					<th>Total</th>
					<th>Menu</th>
				</tr>
			<?$cdtotal=0;while($row=mysql_fetch_assoc($qry)){?>
				<tr>
					<td><input tabindex="-1" readonly type="text" style="text-align:right;width:95%;" value="<?=$row['money']?>"/></td>
					<td><input tabindex="-1" readonly type="text" style="text-align:right;width:95%;" value="<?=$row['count']?>"/></td>
					<td><input tabindex="-1" readonly type="text" style="text-align:right;width:95%;" value="<?=number_format($row['total'],2)?>"/></td>
					<td><input type="button" onclick="cashDetails_del(<?=$row['id']?>);" value="Del" style="width:95%;"/></td>
				</tr>
			<?$cdtotal+=$row['total'];}?>
				<tr>
					<td colspan="2">Total</td>
					<td colspan="2"><input tabindex="-1" readonly type="text" style="text-align:center;width:95%;" value="<?=number_format($cdtotal,2)?>"/></td>
					
				</tr>
			</table>
	<?
	break;
	case'cashDetails':
	?>
		<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<th>Money</th>
				<th>Count</th>
				<th>Total</th>
				<th>Menu</th>
			</tr>
			<tr>
				<td><input type="text" name="money" id="money" style="text-align:right;width:95%;" onchange="moneyxcount();" /></td>
				<td><input type="text" name="count" id="count" style="text-align:right;width:95%;" onchange="moneyxcount();" /></td>
				<td><input readonly type="text" name="total" id="total" style="text-align:right;width:95%;" /></td>
				<td><input type="button" onclick="cashDetails_add();" value="Add" /></td>
			</tr>
		</table>
		<div id="result" style="overflow:auto;min-height:200px;width:100%;"></div>
	<?
	break;
	case'cashout':
		?>
		<form name="frm_cashout" method="post" onsubmit="return val_cashout()">
			<div style="float:left;margin-right:20px;">Date: </div>
			<input readonly type="text" name="cashout_date" style="width:150px;float:right;" value="<?=date('Y-m-d')?>"/>
			<div style="clear:both;height:5px;"></div>
			<fieldset>
				<legend>Remarks:</legend>
				<textarea name="cashout_remarks" style="width:100%;"></textarea>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:20px;">Amount: </div>
			<input type="text" name="cashout_amount" style="width:150px;float:right;" />
			<div style="clear:both;height:5px;"></div>
			<input type="submit" value="Save" name="cashout_save" style="height:30px;width:150px;"/>
		</form>
		<script>
			function val_cashout(){
				var frm = document.frm_cashout;
				if(frm.cashout_remarks.value==""){
					alert("Please fill-up remarks...");
					return false;
				}
				if(frm.cashout_amount.value==""){
					alert("Please fill-up amount...");
					return false;
				}
				return true;
			}
		</script>
		<?
	break;
	case'signout':
		$qrydel = mysql_query("delete from tbl_counter_logged where counter_num='".$_SESSION['counter_num']."'");
		if($qrydel){
			echo "success";
			session_destroy();
		}
	break;
	case'reprintreceipt':
		$readingNum = $db->getReadingnum($_SESSION['counter_num']);
		$receiptnum = $_REQUEST['receiptnum']?"and receipt_id='".$_REQUEST['receiptnum']."' or studentname like '%".$_REQUEST['receiptnum']."%'":"";
		$date = $_REQUEST['rdate']?"and date='".$_REQUEST['rdate']."'":"";
		if($_REQUEST['receiptnum']||$_REQUEST['rdate']){
			$sql = "select * from tbl_sales_receipt_{$_SESSION['counter_num']} where counter_num='".$_SESSION['counter_num']."' $receiptnum $date order by id desc";
		}else{
			$sql = "select * from tbl_sales_receipt_{$_SESSION['counter_num']} where counter_num='".$_SESSION['counter_num']."' and reading='".$readingNum."' $receiptnum $date order by id desc";
		}
		$qry = mysql_query($sql);
		?>
		<div style="width:100%;height:400px;overflow:auto;">
			<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th>ReceiptNum</th>
						<th>OrderSlip</th>
						<th>Total</th>
						<th>Tender</th>
						<th>Change</th>
						<th>Type</th>
						<th>StudentName</th>
					</tr>
				</thead>
				<tbody>
					<? 	while($row = mysql_fetch_assoc($qry)){ ?>
					<tr>
						<td><a href="javascript:viewReceipt(<?=$row['receipt_id']?>,<?=$row['reading']?>);" class="activation"><?=$row['receipt_id']?></a></td>
						<td><?=$row['orderslip']?></td>
						<td><?=number_format($row['amount'],2)?></td>
						<td><?=number_format($row['tender'],2)?></td>
						<td><?=number_format($row['change'],2)?></td>
						<td><?=$row['type']=='SALES'?$row['payment']:$row['type']?></td>
						<td><?=$row['studentname']?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
		<div style="width:100px;float:left;margin-right:5px;">Search</div>
		<input type="text" id="receiptnum" style="width:150px;float:left;margin-right:10px;" />
		<div style="width:80px;float:left;margin-right:5px;">Date</div>
		<input type="text" readonly id="rdate" style="width:150px;float:left;margin-right:10px;" />
		<input type="button" value="Execute" style="float:left;width:100px;" onclick="searchReceipt()"/>
		<script>
		$('#rdate').datepicker({
			inline: true,
			dateFormat:"yy-mm-dd"
		});
		function searchReceipt(){
			var receiptnum = $("#receiptnum").val();
			clickDialog("reprintreceipt",700,500,"reprintreceipt&receiptnum="+receiptnum+"&rdate="+$("#rdate").val(),"Receipt List");
			jQuery.tableNavigation();
		}
		function viewReceipt(num,readingnum) {
			if (window.showModalDialog) {
				window.showModalDialog('./reports/<?=$_SESSION['reports']['Receipt']['report_link']?>?receipt_num='+num+'&readingnum='+readingnum+'&reprint=true',"Receipt","dialogWidth:350px;dialogHeight:350px");
			} else {
				window.open('./reports/<?=$_SESSION['reports']['Receipt']['report_link']?>?receipt_num='+num+'&readingnum='+readingnum+'&reprint=true',"Receipt",'height=350,width=350,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
			}
			/*
			var receipt = '<?=$receipt_type?>';
			//loadiFrame('./reports/receipt.php?receipt_num='+num);
			loadiFrame(receipt+num+'&readingnum='+readingnum);
			$("#myiframe").load( 
				function() {
					window.frames['myname'].focus();
					window.frames['myname'].print();
					window.frames['myname'].close();
					window.frames['myname'].onafterprint = function () {
						//window.location="index.php";
						window.location="<?=$location?>";
					}
					
				}
			 );*/
			
		}
		</script>
		<?php
	break;
	case'printWS':
		$readingNum = $db->getReadingnum($_SESSION['counter_num']);
		$sql = "select * from tbl_sales_receipt_{$_SESSION['counter_num']} where counter_num='".$_SESSION['counter_num']."' and reading='".$readingNum."' order by id desc";
		$qry = mysql_query($sql);
		?>
		<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>TRA/DR</th>
					<th>Total</th>
					<th>Payment</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:viewWSprint(<?=$row['receipt_id']?>);" class="activation"><?=$row['receipt_id']?></a></td>
					<td><?=number_format($row['amount'],2)?></td>
					<td><?=$row['payment']?></td>
					<td><?=$row['type']?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<script>
		function viewWSprint(num) {
			loadiFrame('./reports/receipt_ws.php?receipt_num='+num);
			$("#myiframe").load( 
				function() {
					window.frames['myname'].focus();
					window.frames['myname'].print();
					window.frames['myname'].close();
					window.frames['myname'].onafterprint = function () {
						//window.location="index.php";
						window.location="<?=$location?>";
					}
				}
			 );
			
		}
		</script>
		<?php
	break;
	case'barcodeInfo':
		$where = $_REQUEST['barcode']!=''?"(tblbcode.barcode='".$_REQUEST['barcode']."' or tblprod.sku_id='".$_REQUEST['barcode']."')":"tblprod.sku_id='".$_REQUEST['sku']."'";
		$sql="select tblbcode.*,tblprod.sku_id  as skuid,tblprod.supplier_id,tblprod.manufacturer_id,tblprod.category_id from tbl_product_name tblprod left join tbl_barcodes tblbcode  
			on tblprod.sku_id=tblbcode.sku_id where $where limit 1";
		$qry = mysql_query($sql);
		$row = mysql_fetch_assoc($qry);
		$output['details'] = $row;
		$qry = mysql_query("select * from tbl_barcodes where sku_id='".$row['sku_id']."'"); //and barcode != '".$_REQUEST['barcode']."'
		while($row=mysql_fetch_assoc($qry)){
			$output['uom'][] = $row;
			//modified on 8-3-14
			if($row['divmul']==1 && $row['cost']!=0){
				$output['new_base_unit']=$row['unit'];
			}
			//modified on 8-3-14
		}
		if($output){
			echo json_encode($output);
		}else{
			echo '{"result":"Not Found"}';
		}
	break;
	case'computeCommission':
		$sql="update tbl_sales_items set 
			cost='{$_REQUEST['cost']}',
			gain='{$_REQUEST['gain']}'
			where id='{$_REQUEST['refid']}' and item_desc='{$_REQUEST['item']}'
			";
			//where barcode='{$_REQUEST['barcode']}' and (date_format(timestamp,'%Y-%m-%d') between '{$_REQUEST['begdate']}' and '{$_REQUEST['enddate']}')
			//";
		$qry = mysql_query($sql);
		if($qry){
			echo "success,".$db->getCommission($_REQUEST['gain'],$_REQUEST['item'],$_REQUEST['qty']);
		}else{
			echo mysql_error();
		}
	break;
	case"savestudenttosession":
		$_SESSION['student_info']=array(
			'idno'=>$_REQUEST['idno'],
			'name'=>$_REQUEST['studentname'],
			'course'=>$_REQUEST['course'],
			'yr'=>$_REQUEST['yr']
		);
		echo "success";
	break;
	case'studentdetails':
		// switch($_REQUEST['type']){
			// case'College':
				// $sql="select *,concat(FirstName,' ',Lastname) as name from data_college_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'";
			// break;
			// case'HighSchool':
				// $sql="select *,concat(FirstName,' ',Lastname) as name from data_highsch_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'";
			// break;
			// case'Elementary':
				// $sql="select *,concat(FirstName,' ',Lastname) as name from data_elem_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'";
			// break;
		// }
		$sql="select Subject,SubjType,InstructorNo,InsLastName,InsLastName,concat(FirstName,' ',Lastname) as name from data_college_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'
				union 
			  select Subject,SubjType,InstructorNo,InsLastName,InsLastName,concat(FirstName,' ',Lastname) as name from data_highsch_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'
			  union
			  select Subject,SubjType,InstructorNo,InsLastName,InsLastName,concat(FirstName,' ',Lastname) as name from data_elem_{$_SESSION['connect']} where IDNo='".$_REQUEST['refid']."'";
		$qry=mysql_query($sql);
		?>
		<div style="clear:both;height:5px;"></div>
		<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" border="1" width="100%">
			<thead>
				<tr>
					<th>SUBJECT</th>
					<th>Instructor #</th>
					<th>InstructorName</th>
				</tr>
			</thead>
			<tbody>
		<?
		while($row=mysql_fetch_assoc($qry)){
			echo "<tr>
				<td>".$row['Subject']."".(trim($row['SubjType'])!=""?"({$row['SubjType']})":"")."</td>
				<td>{$row['InstructorNo']}</td>
				<td>".$row['InsLastName'].", ".$row['InsFirstName']."</td>
			</tr>";
		}
		echo "</tbody></table>";
	break;
	case'patientsearch':
		$sql = "select * from {$_REQUEST['tblsource']} where first_name like '%".$_REQUEST['queryString']."%' or last_name like '%".$_REQUEST['queryString']."%' order by last_name asc";
		$qry = mysql_query($sql);
		echo '<table cellspacing="0" cellpadding="0" border="1" width="100%">';
		while($row = mysql_fetch_assoc($qry)){
			echo "<tr><td><a href='javascript:fillpatientInfo(\"".$row['idno']."\",\"".$_REQUEST['tblsource']."\");'>".$row['first_name']." ".$row['last_name']."</a></td></tr>";
		}
		echo "</table>";
	break;
	case'patientlist':
	?>
	<div style="height:350px;overflow:auto;position:relative;">
		<div id="autoSuggestionsList" style="position:absolute;top:150px;display:none;border:1px solid #E6E6E6;z-index:9999999;width:420px;height:195px;overflow:auto;background-color:white;"></div>
		<div style="float:left;width:30%;">
			<fieldset style="width:100%;">
				<legend>Select Records</legend>
				<select name="rec_tbl">
					<option value="data_walkin_patient">Walk-In Patient</option>
					<option value="data_teletech_patient?>">Ambulatory Records</option>
				</select>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="width:100%;">
				<legend>Search Patient:</legend>
				<div style="width:55px;float:left;">ID No</div>
				<input type="text" name="idnum" id="idnum" style="float:left;width:150px;" />
				<input readonly type="button" value="..." onclick="lookupPatient($('#idnum').val());" style="float:left;width:30px;margin-left:5px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:55px;float:left;">Name</div>
				<input autocomplete=off type="text" name="sname" id="sname" style="float:left;width:150px;" value="<?=$_REQUEST['name']?>"/>
				<input type="button" value="..." onclick="lookupPatient($('#sname').val());" style="float:left;width:30px;margin-left:5px;"/>
			</fieldset>
		</div>
		<fieldset style="width:55%;float:right;">
			<form name="patientInfo" id="patientInfo">
			<legend>Patient Info:</legend>
			<div style="width:85px;float:left;">First Name</div>
			<input autocomplete=off type="text" name="first_name" style="float:left;width:200px;" value="<?=$_SESSION['patient_info']['first_name']?>" />
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Last Name</div>
			<input autocomplete=off type="text" name="last_name" style="float:left;width:200px;" value="<?=$_SESSION['patient_info']['last_name']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Middle Name</div>
			<input autocomplete=off type="text" name="middle_name" style="float:left;width:200px;" value="<?=$_SESSION['patient_info']['middle_name']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Birth Date</div>
			<input autocomplete=off type="text" name="birth_date" style="float:left;width:200px;" value="<?=$_SESSION['patient_info']['birth_date']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Gender</div>
			<select name="gender" style="float:left;width:200px;">
				<option value="">Pls Select</option>
				<option <?=$_SESSION['patient_info']['gender']=="Male"?"selected":""?> value="Male">Male</option>
				<option <?=$_SESSION['patient_info']['gender']=="Female"?"selected":""?> value="Female">Female</option>
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Address</div>
			<input autocomplete=off type="text" name="address" style="float:left;width:200px;" value="<?=$_SESSION['patient_info']['address']?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="width:85px;float:left;">Req Physician</div>
			<input autocomplete=off type="text" name="reqphysician" style="float:left;width:200px;" value=""/>
			<div style="clear:both;height:5px;"></div>
			<input type="button" value="Save Info" onclick="saveInfo()"/>
			</form>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<input type="button" id="custokbt" value="Ok" onclick="ok()" style="height:28px;width:80px;float:left;"/>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			$('input[name="birth_date"]').datepicker({
				changeMonth: true,
				changeYear: true,
				inline: true,
				yearRange: '1900:+0',
				dateFormat:"yy-mm-dd"
			});
		});
		function ok(){
			var idno=$("input[name='idnum']").val();
			if(idno==""){
				alert("You need to Save Info or Search names...");
			}else{
				var patientname=$("input[name='first_name']").val()+' '+$("input[name='last_name']").val();
				var patientInfo = serializing("#patientInfo");
				$.ajax({
					url: './content/pos_ajax.php?execute=savepatienttosession',
					data:{idno:idno,patientname:patientname,tblsource:$("select[name='rec_tbl'] option:selected").val(),patientInfo:patientInfo},
					async: true,
					type:"POST",
					beforeSend:function(){showLoading();},
					success:function(data){
						if(data=="success"){
							// $("#name_student").html("Patient Name: ["+idno+"] "+patientname);
							// $('#dialogbox').dialog('close');
							// $('#popuploading').dialog('close');
							window.location=document.URL;
						}else{
							alert(data);
						}
					}
				});
			}
			
		}
		function saveInfo(){
			var patientInfo = serializing("#patientInfo");
			$.ajax({
				url: './content/pos_ajax.php?execute=savepatientInfo',
				data:{tblsource:$("select[name='rec_tbl'] option:selected").val(),patientInfo:patientInfo},
				async: true,
				type:"POST",
				dataType:"json",
				beforeSend:function(){showLoading();},
				success:function(data){
					// if(data=="success"){
						// $('#popuploading').dialog('close');
					// }else{
						// alert(data);
					// }
					
					if(data['msg']=="success"){
						$("input[name='idnum']").val(data['idno']);
						$('#popuploading').dialog('close');
					}else{
						alert(data['msg']);
					}
				}
			});
		}
	</script>
	<?
	break;
	case"savepatientInfo":
		$data=array(
			'first_name'=>$_REQUEST['patientInfo']['first_name'],
			'last_name'=>$_REQUEST['patientInfo']['last_name'],
			'middle_name'=>$_REQUEST['patientInfo']['middle_name'],
			'birth_date'=>$_REQUEST['patientInfo']['birth_date'],
			'gender'=>$_REQUEST['patientInfo']['gender'],
			'address'=>$_REQUEST['patientInfo']['address']
		);
		$sql = $db->genSqlInsert($data,"{$_REQUEST['tblsource']}");
		$qry = mysql_query($sql);
		$refid = mysql_insert_id();
		if($qry){
			$_SESSION['patient_info']=array(
				'idno'=>$refid,
				'name'=>$_REQUEST['patientInfo']['first_name']." ".$_REQUEST['patientInfo']['last_name'],
				'tblsource'=>$_REQUEST['tblsource'],
				'patientInfo'=>$_REQUEST['patientInfo']
			);
			//echo "success";
			echo '{"idno":'. $refid .',"msg":"success"}';
		}else{
			echo mysql_error();
		}
	break;
	case"savepatienttosession":
		$_SESSION['patient_info']=array(
			'idno'=>$_REQUEST['idno'],
			'name'=>$_REQUEST['patientname'],
			'tblsource'=>$_REQUEST['tblsource'],
			'patientInfo'=>$_REQUEST['patientInfo']
		);
		echo "success";
	break;
	case'patientInfo':
		$info = $db->getWHERE("*","{$_REQUEST['tblsource']}","where idno='{$_REQUEST['idno']}'");
		echo json_encode($info);
	break;
	
	case'studentsearch':
		switch($_REQUEST['type']){
			case'College':
				$sql = "select IDNo,concat(FirstName,' ',Lastname) as name,Course,Yr from data_college_{$_SESSION['connect']} where FirstName like '%".$_REQUEST['queryString']."%' or Lastname like '%".$_REQUEST['queryString']."%' group by IDNo order by name asc";
			break;
			case'HighSchool':
				$sql = "select IDNo,concat(FirstName,' ',Lastname) as name,concat('HS') Course,Yr from data_highsch_{$_SESSION['connect']} where FirstName like '%".$_REQUEST['queryString']."%' or Lastname like '%".$_REQUEST['queryString']."%' group by IDNo order by name asc";
			break;
			case'Elementary':
				$sql = "select IDNo,concat(FirstName,' ',Lastname) as name,concat('ELEM') Course,Yr from data_elem_{$_SESSION['connect']} where FirstName like '%".$_REQUEST['queryString']."%' or Lastname like '%".$_REQUEST['queryString']."%' group by IDNo order by name asc";
			break;
		}
		$qry = mysql_query($sql);
		echo '<table cellspacing="0" cellpadding="0" border="1" width="100%">';
		while($row = mysql_fetch_assoc($qry)){
			echo "<tr><td><a href='javascript:fill(\"".$row['IDNo']."\",\"".$row['name']."\",\"{$row['Course']}\",\"{$row['Yr']}\");'>".$row['name']."</a></td></tr>";
		}
		echo "</table>";
	break;
	case'studentlist':
		?>
		<div style="height:350px;overflow:auto;">
			<div style="float:left;width:400px;">
				<fieldset style="width:100%;">
					<legend>Grade Level</legend>
					<div style="float:left;margin-right:10px;">
						<input type="radio" name="gradelevel" value="College" checked /> College
					</div>
					<div style="float:left;margin-right:10px;">
						<input type="radio" name="gradelevel" value="HighSchool"/> HighSchool
					</div>
					<div style="float:left;margin-right:10px;">
						<input type="radio" name="gradelevel" value="Elementary"/> Elementary
					</div>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<fieldset style="width:100%;">
					<legend>Student Info:</legend>
					<div style="width:55px;float:left;">ID No</div>
					<input type="text" name="idnum" id="idnum" style="float:left;width:300px;" />
					<input type="button" value="..." onclick="lookup($('#idnum').val());" style="float:left;width:30px;margin-left:5px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:55px;float:left;">Name</div>
					<input autocomplete=off type="text" name="sname" id="sname" style="float:left;width:300px;" value="<?=$_REQUEST['name']?>"/>
					<input type="button" value="..." onclick="lookup($('#sname').val());" style="float:left;width:30px;margin-left:5px;"/>
				</fieldset>
				<div style="clear:both;"></div>
				<div id="autoSuggestionsList" style="display:none;border:1px solid #E6E6E6;z-index:9999999;width:420px;height:195px;overflow:auto;"></div>
			</div>
			<div style="float:left;width:170px;margin-left:40px;">
				<?php if($_REQUEST['com']==true){ ?>
				<input type="button" value="Update" onclick="update()" style="height:28px;width:80px;float:left;"/>
				<?php }else{ ?>
				<input type="button" id="custokbt" value="Ok" onclick="ok()" style="height:28px;width:80px;float:left;"/>
				<?php } ?>
				<div style="clear:both;height:5px;"></div>
				<fieldset style="width:100%;">
					<legend>Course & Year</legend>
					<div style="float:left;width:60px;">Course</div><input type="text" name="course" id="course" style="float:left;width:85px;"/>
					<div style="clear:both;"></div>
					<div style="float:left;width:60px;">Yr</div><input type="text" name="yr" id="yr" style="float:left;width:85px;"/>
				</fieldset>
			</div>
			<div style="clear:both;height:5px;"></div>
			<div id="studentdetails"></div>
			
		</div>
		
		<script type="text/javascript">
			function ok(){
				var idno=$("#idnum").val();
				var studentname=$('#sname').val();
				var course=$("#course").val();
				var yr = $("#yr").val();
				// $('#dialogbox').dialog('close');
				// $("#name_student").html("Student Name: ["+idno+"] "+studentname+" ["+course+" "+yr+"]");
				$.ajax({
					url: './content/pos_ajax.php?execute=savestudenttosession',
					data:{idno:idno,studentname:studentname,course:course,yr:yr},
					async: true,
					type:"POST",
					beforeSend:function(){showLoading();},
					success:function(data){
						if(data=="success"){
							$("#name_student").html("Student Name: ["+idno+"] "+studentname+" ["+course+" "+yr+"]");
							$('#dialogbox').dialog('close');
							$('#popuploading').dialog('close');
						}else{
							alert(data);
						}
					}
				});
				
			}
		</script>
		<?
	break;
	case"customerlist":
		$sql = "select * from tbl_customers order by customer_name asc";
		
		$qry =  mysql_query($sql);
		?>
		<?php /* if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7){ 
		$info=$db->getWHERE("*","(select COALESCE(sum(if(transtype='Cash',0,if(transtype='Payment' or transtype='Credit Memo',amount * -1,amount))),0) as bal from tbl_customers_trans) as tbl","");
		?>
		<div style="float:left;margin-left:20px;">Total AR: <?=number_format($info['bal'],2)?></div>
		<?php } */?>
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
		
		<input type="button" id="custokbt" value="Ok" onclick="ok()" style="float:right;height:28px;width:150px;"/>
		<div style="clear:both;height:5px;"></div>
		<div id="custdetails"></div>
		<div style="clear:both;height:5px;"></div>
		<fieldset>
			<legend>Menu</legend>
			<input id="bt1" class="buthov" type="button" value="Add Customer" style="float:left;height:40px;width:160px;" onclick="addcustomer()"/>
			<?php if($_SESSION['restrictionid']!=2){ ?>
				<input id="bt2" class="buthov" type="button" value="View Trans" style="float:left;height:40px;width:160px;" onclick="viewTrans()"/>
				<input id="bt3" class="buthov" type="button" value="Adjustment" style="float:left;height:40px;width:160px;" onclick="adjustment()"/>
			<? } ?>
			<input id="bt4" class="buthov" type="button" value="Received Payment" style="float:left;height:40px;width:160px;" onclick="payment_received()"/>
			<input id="bt5" class="buthov" type="button" value="Credit Memo" style="float:left;height:40px;width:160px;" onclick="credit_memo()"/>
			<input id="bt6" class="buthov" type="button" value="Work Orders" style="float:left;height:40px;width:160px;" onclick="viewWorkOrders()"/>
		</fieldset>
		<!--<link rel="stylesheet" href="./js/chosen/docsupport/style.css">-->
		<!--<link rel="stylesheet" href="./js/chosen/docsupport/prism.css">-->
		<link rel="stylesheet" href="./js/chosen/chosen.css">
		<script src="./js/chosen/chosen.jquery.js" type="text/javascript"></script>
		<script src="./js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
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
			function ok(){
				saveCustToSession($("#custname").val(),$('#custname>option:selected').text());
				
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
					clickDialog('dialogbox2',900,500,'custtransdetails&acctid='+acctid,'Customer Transaction Details');
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
					clickDialog('dialogbox2',400,300,'payment_received&acctid='+acctid,'Customer Payment Received');
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
		<?
	break;
	case"payment_received":
		if($_REQUEST['receipt']){
			$rec=$db->resultArray("*","tbl_customers_trans","where receipt='{$_REQUEST['receipt']}'");
			foreach($rec as $k=>$val){
				if(strpos($val['details'],"Cheque:")!== false||strpos($val['details'],"Cash:")!== false){
					$info=$val;
				}
				if(strpos($val['details'],"W/Holding 2306:")!== false){
					$wholding2306=$val;
				}
				if(strpos($val['details'],"W/Holding:")!== false || strpos($val['details'],"W/Holding 2307:")!== false){
					$wholding=$val;
				}
				if(strpos($val['details'],"Discount:")!== false){
					$discount=$val;
				}
				if(strpos($val['details'],"Penalty:")!== false){
					$penalty=$val;
				}
			}
			$paid_invoices = $db->resultArray("*","tbl_sales_invoice_paid","where or_number='{$_REQUEST['receipt']}'");
		}
		$invoices = $db->resultArray("tbl_customers_trans.*,paid.total","tbl_customers_trans 
			left join (select si_number,sum(amount_paid) total from tbl_sales_invoice_paid group by si_number) paid on tbl_customers_trans.receipt=paid.si_number",
			"where cust_id='{$_REQUEST['acctid']}' and (transtype='sales_invoice' or transtype='Adjustment') and (or_ref='' or or_ref is null) and amount>0");
		
		?>
		<style>
		#invTbl tr td{
			font-size:10px;
		}
		</style>
		<div style="float:left;width:45%">
			<input type="hidden" name="edit" id="edit" value="<?=$_REQUEST['receipt']?>"/>
			<input type="hidden" name="refid" id="refid" value="<?=$info?$info['id']:""?>"/>
			<div style="float:left;margin-right:5px;width:100px;">OR #:</div>
			<input type="text" name="ornum" id="ornum" style="float:left;width:65%;" value="<?=$info?$info['receipt']:""?>"/>
			<input type="button" value="Search OR" style="float:left;width:80px;margin-left:5px;" onclick="searchOR()"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Date:</div>
			<input type="text" name="xdate" id="xdate" style="float:left;width:95%;" autocomplete="off" value="<?=$info?date("Y-m-d",strtotime($info['date'])):""?>"/>
			<!--div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Details:</div>
			<textarea name="custpay_details" id="custpay_details" style="float:left;width:95%;height:150px;"></textarea-->
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Amount:</div>
			<input type="text" name="custpay_amount" id="custpay_amount" style="float:left;width:95%;" value="<?=$info?$info['amount']:""?>"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Collector:</div>
			<select name="collector" id="collector" style="float:left;width:95%;">
				<option value="">Select Collector</option>
				<?php
				$emp = $db->resultArray("concat(firstname,' ',lastname) name","tbl_employees","");
				foreach($emp as $k => $v){
					echo "<option value='{$v['name']}'>{$v['name']}</option>";
				}
				?>
				
			</select>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:45%;">
				<fieldset>
					<legend>W/Holding 2306</legend>
					<input type="text" name="wholding2306" placeholder="0.00 %" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="text" name="wholding2306_amount" placeholder="W/Holding Amount" style="width:150px;" value="<?=$wholding2306?$wholding2306['amount']:""?>"/>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<fieldset>
					<legend>W/Holding 2307</legend>
					<input type="text" name="wholding" placeholder="0.00 %" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="text" name="wholding_amount" placeholder="W/Holding Amount" style="width:150px;" value="<?=$wholding?$wholding['amount']:""?>"/>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<fieldset>
					<legend>Discount</legend>
					<input type="text" name="discount_percent" placeholder="0.00 %" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="text" name="discount_amount" placeholder="Discount Amount" style="width:150px;" value="<?=$discount?$discount['amount']:""?>"/>
				</fieldset>
			</div>
			<div style="float:left;width:50%;">
				<fieldset>
					<legend>Paid In</legend>
					<div style="float:left;width:40%;"><input checked="checked" type="radio" name="paidstatus" value="Full" /> Full </div>
					<div style="float:left;width:40%;"><input type="radio" name="paidstatus" value="Partial" /> Partial </div>
				</fieldset>
				<fieldset>
					<legend>Paid In</legend>
					<div style="float:left;width:40%;"><input checked="checked" type="radio" name="paidtype" value="Cash" /> Cash </div>
					<div style="float:left;width:41%;"><input type="radio" name="paidtype" value="Cheque" /> Cheque </div>
					<div style="clear:both;height:5px;"></div>
					<input type="text" placeholder="Cheque Details" name="cheque_details" style="width:176px;"/>
				</fieldset>
				<div style="clear:both;height:5px;"></div>
				<fieldset>
					<legend>Penalty</legend>
					<input type="text" name="penalty_percent" placeholder="0.00 %" style="width:150px;"/>
					<div style="clear:both;height:5px;"></div>
					<input type="text" name="penalty_amount" placeholder="Penalty Amount" style="width:150px;" value="<?=$discount?$discount['amount']:""?>"/>
				</fieldset>
			</div>
			
			
			<div style="clear:both;height:5px;"></div>
			<input type="button" value="Save" style="float:left;height:40px;width:180px;" onclick="savecustpay()"/>
		</div>
		<div style="float:left;width:50%;">
			<table class="navigateableMain" id="invTbl" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;" colspan="5">Total Checked</th>
						<th style="border:none;" colspan="2" class="totalChecked"></th>
					</tr>
					<tr>
						<th style="border:none;">&nbsp;</th>
						<th style="border:none;">Inv #</th>
						<th style="border:none;">Date</th>
						<th style="border:none;">Details</th>
						<th style="border:none;">Amount</th>
						<th style="border:none;">Paid Amount</th>
						<th style="border:none;">Balance</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="checkbox" value="Unapplied" class="myCheckbox"></td>
						<td>Unapplied</td>
						<td>-</td>
						<td>-</td>
						<td style="text-align:right;"><?=number_format(0,2)?></td>
						<td><input type="text" name="paidamt[]" style="width:100%;"/></td>
					</tr>
					<?php foreach($invoices as $key => $val){ ?>
						<tr>
							<td><input type="checkbox" value="<?=$val['receipt']?>" class="myCheckbox"></td>
							<td><?=$val['receipt']?></td>
							<td><?=date('Y-m-d',strtotime($val['date']))?></td>
							<td><?=$val['details']?></td>
							<td style="text-align:right;"><?=number_format($val['amount'],2)?></td>
							<td><input type="text" name="paidamt[]" style="width:100%;"/></td>
							<td><?=number_format($val['amount']-$val['total'],2)?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<script>
			$('#xdate').datepicker({
				changeMonth: true,
				changeYear: true,
				inline: true,
				dateFormat:"yy-mm-dd"
			});
			function getCheckVal(){
				var xrec = [];
				$('.myCheckbox:checked').each(function(index,elem){
					var si = $(this).val();
					var amtpaid = $(this).closest('tr').find("input[name='paidamt[]']").val();
					xrec.push({'si':si,'amtpaid':amtpaid});
				});
				return xrec;
			}
			function savecustpay(){
				var edit = $("#edit").val();
				var ornum = $("#ornum").val();
				var acctid = '<?=$_REQUEST['acctid']?>';
				var custpay_details = $("#custpay_details").val();
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var ar = new Number($(".totalChecked").html().replace(/,/g, ""));
				var date = $("#xdate").val();
				var checked = $('.myCheckbox:checked').map(function() {return this.value;}).get().join(',');
				var collector=$("#collector").val();
				var paidtype = $("input[name='paidtype']:checked").val();
				var paidstatus = $("input[name='paidstatus']:checked").val();
				var wholding_amount = $("input[name='wholding_amount']").val().replace(/,/g, "");
				var wholding2306_amount = $("input[name='wholding2306_amount']").val().replace(/,/g, "");
				var discount_amount = $("input[name='discount_amount']").val().replace(/,/g, "");
				var penalty_amount = $("input[name='penalty_amount']").val().replace(/,/g, "");
				var xrec = getCheckVal();//JSON.stringify()
				
				$.ajax({
					url: './content/pos_ajax.php?execute=savecustpay',
					data:{ar:ar,wholding2306_amount:wholding2306_amount,edit:edit,xrec:xrec,penalty_amount:penalty_amount,discount_amount:discount_amount,wholding_amount:wholding_amount,paidstatus:paidstatus,paidtype:paidtype,collector:collector,ornum:ornum,date:date,custpay_details:custpay_details,custpay_amount:custpay_amount,acctid:acctid,receipts:checked},
					type:"POST",
					success:function(data){
						if(data=="success"){
							viewTrans();
						}else{
							alert(data);
						}
					}
				});
			}
			function searchOR(){
				var refid = $("#ornum").val();
				var acctid = $('#acctid').html();
				if(!acctid){
					alert("Please select customer first...");
				}else{
					clickDialog('dialogbox2',900,500,'payment_received&acctid='+acctid+"&receipt="+refid,'Customer Payment Received');
				}
			}
			$("input[name='wholding']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var wholding = (amt/1.12)*$(this).val();
				$("input[name='wholding_amount']").val(new Number(wholding).formatMoney(2));
				$("#custpay_amount").val(new Number(custpay_amount - wholding).formatMoney(2));
				
			});
			$("input[name='wholding_amount']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var wholding = $(this).val();
				$("input[name='wholding']").val(new Number(wholding/amt).formatMoney(2));
				$("#custpay_amount").val(new Number(custpay_amount - wholding).formatMoney(2));
				
			});
			$("input[name='wholding2306']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var wholding = (amt/1.12)*$(this).val();
				$("input[name='wholding2306_amount']").val(new Number(wholding).formatMoney(2));
				$("#custpay_amount").val(new Number(custpay_amount - wholding).formatMoney(2));
				
			});
			$("input[name='wholding2306_amount']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var wholding = $(this).val();
				$("input[name='wholding2306']").val(new Number(wholding/amt).formatMoney(2));
				$("#custpay_amount").val(new Number(custpay_amount - wholding).formatMoney(2));
				
			});
			
			//--------------------
			$("input[name='discount_percent']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var disc_amt=amt*$(this).val();
				$("input[name='discount_amount']").val(disc_amt);
				$("#custpay_amount").val(new Number(custpay_amount - disc_amt).formatMoney(2));
			});
			$("input[name='discount_amount']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var disc_amt=$(this).val();
				$("input[name='discount_amount']").val(disc_amt);
				$("#custpay_amount").val(new Number(custpay_amount - disc_amt).formatMoney(2));
			});
			
			$("input[name='penalty_percent']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var penalty_amt = amt*$(this).val();
				$("input[name='penalty_amount']").val(penalty_amt);
				$("#custpay_amount").val(new Number(custpay_amount + penalty_amt).formatMoney(2));
			});
			
			$("input[name='penalty_amount']").change(function(){
				var custpay_amount = $("#custpay_amount").val().replace(/,/g, "");
				var amt = new Number($(".totalChecked").html().replace(/,/g, ""));
				var res = new Number(custpay_amount) + new Number($(this).val());
				$("#custpay_amount").val(new Number(res).formatMoney(2));
			});
			$("input[name='paidamt[]").change(function(){
				$(".totalChecked").html(new Number(getTotal()).formatMoney(2));
			});
			$(".myCheckbox").change(function(){
				$(".totalChecked").html(new Number(getTotal()).formatMoney(2));
			});
			function getTotal(){
				var total = 0;
			   $('.myCheckbox:checked').each(function(){
					total+=parseFloat($(this).closest("tr").find('td:eq(5) input').val().replace(/,/g, ""));
			   });
			   $("#custpay_amount").val(new Number(total).formatMoney(2));
			   return total;
			}
		</script>
		<?
	break;
	case"savecustpay":
		if($_REQUEST['edit']){
			$del="delete from tbl_customers_trans where receipt='{$_REQUEST['ornum']}' and cust_id='{$_REQUEST['acctid']}'";
			//$del2="delete from tbl_sales_invoice_paid where or_number={$_REQUEST['ornum']}";
			$del_qry1 = mysql_query($del);
			//$del_qry2 = mysql_query($del2);
			if(!$del_qry1){
				echo mysql_error();
			}
			// if(!$del_qry2){
				// echo mysql_error();
			// }
		}
		$data = array(
				'date'=>$_REQUEST['date'],
				'cust_id'=>$_REQUEST['acctid'],
				'receipt'=>$_REQUEST['ornum'],
				'counter'=>$_SESSION['counter_num'],
				'reading'=>$_SESSION['readingnum'],
				'transtype'=>'Payment',
				'details'=>$_REQUEST['paidtype'].":".$_REQUEST['custpay_details']." Ref # ".$_REQUEST['receipts'],
				'amount'=>str_replace(",","",number_format($_POST['custpay_amount'],2)));
		$sql = "insert into tbl_customers_trans (`".implode("`,`",array_keys($data))."`) values ('".implode("','",$data)."')";
		$stat = mysql_query($sql);
		$total_amt = str_replace(",","",$_POST['custpay_amount']);
		if($_REQUEST['wholding2306_amount']){
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'6431','account_desc'=>'FINAL WITH HOLDING VAT','cr'=>'','dr'=>$_POST['wholding2306_amount'],'center'=>"$cost_center",'type'=>'GJ');
			$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['date'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>$_REQUEST['ornum'],
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Payment',
					'details'=>"W/Holding 2306: Ref #".$_REQUEST['receipts'],
					'amount'=>str_replace(",","",number_format(($_POST['wholding2306_amount']),2))));
		}
		if($_REQUEST['wholding_amount']){
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'1228','account_desc'=>'CREDITABLE TAX WITHHELD','cr'=>'','dr'=>$_POST['wholding_amount'],'center'=>"$cost_center",'type'=>'GJ');
			$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['date'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>$_REQUEST['ornum'],
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Payment',
					'details'=>"W/Holding 2307: Ref #".$_REQUEST['receipts'],
					'amount'=>str_replace(",","",number_format(($_POST['wholding_amount']),2))));
		}
		if($_REQUEST['discount_amount']){
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'5005','account_desc'=>'PROMPT PAYMENT DISCOUNT','cr'=>'','dr'=>$_POST['discount_amount'],'center'=>"$cost_center",'type'=>'GJ');
			$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['date'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>$_REQUEST['ornum'],
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Payment',
					'details'=>"Discount: Ref #".$_REQUEST['receipts'],
					'amount'=>str_replace(",","",number_format(($_POST['discount_amount']),2))));
		}
		if($_REQUEST['penalty_amount']){
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'4204','account_desc'=>'OTHER INCOME - OTHERS','cr'=>$_POST['penalty_amount'],'dr'=>'','center'=>"$cost_center",'type'=>'GJ');
			$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['date'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>$_REQUEST['ornum'],
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Payment',
					'details'=>"Penalty: Ref #".$_REQUEST['receipts'],
					'amount'=>str_replace(",","",number_format(($_POST['penalty_amount']),2))));
			//$total_amt = (double)$total_amt + (double)str_replace(",","",number_format(($_POST['penalty_amount']),2));
		}
		if($stat){
			
			if($_REQUEST['paidstatus']=="Full"){
				$receipts = explode(",",$_REQUEST['receipts']);
				$sql="update tbl_customers_trans set or_ref='{$_REQUEST['ornum']}' where receipt in ('".implode("','",$receipts)."')";
				$qry=mysql_query($sql);
			}
			
			$header="insert into tbl_sales_invoice_paid (si_number,or_number,amount_paid) values ";
			$flag=false;
			foreach($_POST['xrec'] as $k => $v){
				if($flag)$items.=",";
				$items.="('{$v['si']}','{$_REQUEST['ornum']}','{$v['amtpaid']}')";
				$flag=true;
			}
			$inv = mysql_query($header.$items." on duplicate key update amount_paid=values(amount_paid)");
			if(!$inv){
				echo mysql_error();
			}
			//Auto SJ
			$glref=$con->getNextJournalID('GJ');
			
			$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`,`reference`) values 
				('".$glref."','{$_SESSION['connect']}','".date('Y-m-d')."','GJ', 
				'TO RECORD PAYMENT','".$total_amt."', 
				'".$_SESSION['xid']."','ForApproval','CR#{$_REQUEST['ornum']}') 
				on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'7502','account_desc'=>'ACCOUNTS RECEIVABLE-TRADE','cr'=>str_replace(",","",number_format(($_POST['ar']),2)),'dr'=>'','center'=>"",'ar_refid'=>"{$_REQUEST['acctid']}",'type'=>'GJ');
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'1002','account_desc'=>'CASH IN BANK','cr'=>'','dr'=>$total_amt,'center'=>"$cost_center",'type'=>'GJ');
			$glid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry,'GJ');
			//Auto SJ
			echo "success";
		}else{
			echo $stat;
		}
	break;
	case"adjustment":
		$info=$db->getWHERE("*","tbl_customers_trans","where id='{$_REQUEST['refid']}'");
		if($_REQUEST['type']=="credit_memo"){
			$invoices = $db->resultArray("*","tbl_customers_trans","where cust_id='{$_REQUEST['acctid']}' and (transtype='sales_invoice' or transtype='Adjustment') and (or_ref='' or or_ref is null) and amount>0");
		}
		?>
		<div style="float:left;width:40%;">
			<div style="float:left;margin-right:5px;width:100px;">REF #:</div>
			<input value="<?=$info['refnum']?>" type="text" name="refnum" id="refnum" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Date:</div>
			<input autocomplete=off value="<?=$info?date("Y-m-d",$info['date']):""?>" type="text" name="xdate" id="xdate" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Reason:</div>
			<textarea name="adj_details" id="adj_details" style="float:left;width:215px;height:150px;"><?=$info?$info['details']:""?></textarea>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Amount:</div>
			<input value="<?=$info?number_format($info['amount'],2):""?>" type="text" name="adj_amount" id="adj_amount" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Invoice Reference:</div>
			<input value="<?=$info?$info['or_ref']:""?>" type="text" name="inv_ref" id="inv_ref" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<input type="button" value="Save" style="float:left;height:40px;width:180px;" onclick="save_adjustment()"/>
		</div>
		<div style="width:60%;float:left;">
			<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;"><input type="button" value="+" id="items" style="width:20px;height:25px;"/></th>
						<th style="border:none;width:250px;">Remarks</th>
						<th style="border:none;">QTY</th>
						<th style="border:none;">UNIT</th>
						<th style="border:none;">PRICE</th>
						<th style="border:none;">Amount</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<div style="clear:both;height:5px;"></div>
			<?php if($_REQUEST['type']=="credit_memo"){ ?>
			<table class="navigateableMain" id="invTbl" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;" colspan="4">Total Checked</th>
						<th style="border:none;" colspan="2" class="totalChecked"></th>
					</tr>
					<tr>
						<th style="border:none;">&nbsp;</th>
						<th style="border:none;">Inv #</th>
						<th style="border:none;">Date</th>
						<th style="border:none;">Details</th>
						<th style="border:none;">Amount</th>
						<th style="border:none;">Paid Amount</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="checkbox" value="Unapplied" class="myCheckbox"></td>
						<td>Unapplied</td>
						<td>-</td>
						<td>-</td>
						<td style="text-align:right;"><?=number_format(0,2)?></td>
						<td><input type="text" name="paidamt[]" style="width:100%;"/></td>
					</tr>
					<?php foreach($invoices as $key => $val){ ?>
						<tr>
							<td><input type="checkbox" value="<?=$val['receipt']?>" class="myCheckbox"></td>
							<td><?=$val['receipt']?></td>
							<td><?=date('Y-m-d',strtotime($val['date']))?></td>
							<td><?=$val['details']?></td>
							<td style="text-align:right;"><?=number_format($val['amount'],2)?></td>
							<td><input type="text" name="paidamt[]" style="width:100%;"/></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php } ?>
		</div>
		
		<script>
			$(document).ready(function() {
				$('#xdate').datepicker({
					inline: true,
					changeMonth: true,
					changeYear: true,
					dateFormat:"yy-mm-dd"
				});
			});
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
			$("input[value='+']").on('click',function(){
				var id = $(this).attr('id');
				var num =$("#"+id+"_tbl tbody tr").length;
				switch(id){
					case'items':
						var txt = '<tr>\
								<td><input type="checkbox" ></td>\
								<td><input type="text" name="items['+num+'][details]" value="" style="width:100%;"/></td>\
								<td><input type="text" name="items['+num+'][qty]" class="qty" value="" style="width:100%;"/></td>\
								<td><input type="text" name="items['+num+'][unit]" value="" style="width:100%;"/></td>\
								<td><input type="text" name="items['+num+'][price]" class="price" value="" style="width:100%;"/></td>\
								<td><input type="text" name="items['+num+'][amount]" class="adjamt" value="" style="width:100%;"/></td>\
							</tr>';
					break;
				}
				$("#"+id+"_tbl tbody").prepend(txt);
				setChanges();
			});
			function setChanges(){
				$(".adjamt, .qty, .price").on("change paste keyup",function(){
					var tr = $(this).closest("tr");
					var qty = strtodouble(tr.find(".qty").val());
					var price = strtodouble(tr.find(".price").val());
					var total = (qty*price);
					tr.find(".adjamt").val(strtocurrency(total));
					
					var subtotal = sumName(".adjamt");
					$("#adj_amount").val(subtotal);
				});
			}
			function tblContent(tblId){
				var TableData = new Array();
					
				$(tblId+' tr').each(function(row, tr){
					TableData[row]={
						"details" :$(tr).find('td:eq(1) input').val()
						, "amount" : $(tr).find('td:eq(2) input').val()
					}
				}); 
				TableData.shift();  // first row is the table header - so remove
				return TableData;
			}
			function save_adjustment(){
				var refnum = $("#refnum").val();
				var acctid = '<?=$_REQUEST['acctid']?>';
				var adj_details = $("#adj_details").val();
				var adj_amount = $("#adj_amount").val().replace(/,/g, "");
				var totalCheck = new Number(getTotal());
				var xdate = $("#xdate").val();
				var checked = $('.myCheckbox:checked').map(function() {return this.value;}).get().join(',');
				var pTableData =JSON.stringify(tblContent("#items_tbl"));
				if(adj_amount<totalCheck){
					alert("Amount not matched in the selected invoice...");
				}else{
					$.ajax({
						url: './content/pos_ajax.php?execute=save_adj&type=<?=$_REQUEST['type']?>',
						data:{refnum:refnum,receipts:checked,xdate:xdate,adj_details:adj_details,adj_amount:adj_amount,acctid:acctid,pTableData:pTableData},
						type:"POST",
						success:function(data){
							res = data.split(",");
							if(res[0]=="success"){
								viewCMDM(res[1]);
								//viewTrans();
								$('#dialogbox2').dialog('close');
								
							}else{
								alert(data);
							}
						}
					});
				}
			}
			$("input[name='paidamt[]").change(function(){
				$(".totalChecked").html(new Number(getTotal()).formatMoney(2));
			});
			function viewCMDM(id){
				if (window.showModalDialog) {
					window.showModalDialog('./reports/credit_memo.php?refid='+id,"Credit Memo","dialogWidth:700px;dialogHeight:550px");
				} else {
					window.open('./reports/credit_memo.php?refid='+id,"Credit Memo",'height=550,width=700,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
				}
			}
			$(".myCheckbox").change(function(){
				$(".totalChecked").html(new Number(getTotal()).formatMoney(2));
			});
			function getTotal(){
				var total = 0;
			   $('.myCheckbox:checked').each(function(){
					total+=parseFloat($(this).closest("tr").find('td:eq(5) input').val().replace(/,/g, ""));
			   });
			   $("#adj_amount").val(new Number(total).formatMoney(2));
			   return total;
			}
		</script>
		<?
	break;
	case"save_adj":
		if($_REQUEST['type']=="credit_memo"){
			$refdesc="CM";
			if($_REQUEST['edit']){
				$stat = $db->editCustTrans(array(
					'date'=>$_REQUEST['xdate'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>'',
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Credit Memo',
					'details'=>"CR:".$_REQUEST['adj_details']." Ref # ".$_REQUEST['receipts'],
					'more_details'=>$_REQUEST['pTableData'],
					'amount'=>str_replace(",","",number_format($_POST['adj_amount'],2))));

			}else{
				$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['xdate'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>'',
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Credit Memo',
					'details'=>"CR:".$_REQUEST['adj_details']." Ref # ".$_REQUEST['receipts'],
					'more_details'=>$_REQUEST['pTableData'],
					'amount'=>str_replace(",","",number_format($_POST['adj_amount'],2))));
			}
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'7502','account_desc'=>'ACCOUNTS RECEIVABLE-TRADE','cr'=>str_replace(",","",$_POST['adj_amount']),'dr'=>'','center'=>"",'ar_refid'=>"{$_REQUEST['acctid']}",'type'=>'GJ');
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'4004','account_desc'=>'SALES RETURNS','cr'=>'','dr'=>str_replace(",","",$_POST['adj_amount']),'center'=>"$cost_center",'type'=>'GJ');
		}else{
			$refdesc="DM";
			if($_REQUEST['edit']){
				$stat = $db->editCustTrans(array(
					'date'=>$_REQUEST['xdate'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>'',
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Adjustment',
					'details'=>"DR:".$_REQUEST['adj_details'],
					'more_details'=>$_REQUEST['pTableData'],
					'amount'=>str_replace(",","",number_format($_POST['adj_amount'],2))));
			}else{
				$stat = $db->saveCustTrans(array(
					'date'=>$_REQUEST['xdate'],
					'cust_id'=>$_REQUEST['acctid'],
					'receipt'=>'',
					'counter'=>$_SESSION['counter_num'],
					'reading'=>$_SESSION['readingnum'],
					'transtype'=>'Adjustment',
					'details'=>"DR:".$_REQUEST['adj_details'],
					'more_details'=>$_REQUEST['pTableData'],
					'amount'=>str_replace(",","",number_format($_POST['adj_amount'],2))));
			}
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'7502','account_desc'=>'ACCOUNTS RECEIVABLE-TRADE','cr'=>'','dr'=>str_replace(",","",$_POST['adj_amount']),'center'=>"",'ar_refid'=>"{$_REQUEST['acctid']}",'type'=>'GJ');
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'4000','account_desc'=>'SALES','cr'=>str_replace(",","",$_POST['adj_amount']),'dr'=>'','center'=>"",'type'=>'GJ');
		}
		$refid = mysql_insert_id();
		if($stat){
			// if($_REQUEST['type']=="credit_memo"){
				// $receipts = explode(",",$_REQUEST['receipts']);
				// $sql="update tbl_customers_trans set or_ref='{$refid}' where receipt in ('".implode("','",$receipts)."')";
				// $qry=mysql_query($sql);
			// }
			
			//Auto SJ
			$glref=$con->getNextJournalID('GJ');
			$total_amt = str_replace(",","",$_POST['adj_amount']);
			$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`,`reference`) values 
				('".$glref."','{$_SESSION['connect']}','".date('Y-m-d')."','GJ', 
				'TO RECORD {$_REQUEST['type']}','".$total_amt."', 
		'".$_SESSION['xid']."','ForApproval','{$refdesc}#{$_REQUEST['refnum']}') 
				on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
			
			$glid=$con->insertSJDiffApproach($glref,$sql,date('Y-m-d'),$entry,'GJ');
			//Auto SJ
			
			echo "success,$refid";
		}else{
			echo $stat;
		}
	break;
	case"credit_memo":
		?>
		<div style="float:left;margin-right:5px;width:100px;">Date:</div>
		<input type="text" name="xdate" id="xdate" style="float:left;width:215px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:100px;">Details:</div>
		<textarea name="cm_details" id="cm_details" style="float:left;width:215px;height:150px;"></textarea>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:100px;">Amount:</div>
		<input type="text" name="cm_amount" id="cm_amount" style="float:left;width:215px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:100px;">Invoice Reference:</div>
		<input type="text" name="inv_ref" id="inv_ref" style="float:left;width:215px;"/>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" style="float:left;height:40px;width:180px;" onclick="savecredit_memo()"/>
		<script>
			$(document).ready(function() {
				$('#xdate').datepicker({
					inline: true,
					changeMonth: true,
					changeYear: true,
					dateFormat:"yy-mm-dd"
				});
			});
			function savecredit_memo(){
				var acctid = '<?=$_REQUEST['acctid']?>';
				var cm_details = $("#cm_details").val();
				var cm_amount = $("#cm_amount").val();
				var xdate = $("#xdate").val();
				var inv_ref = $("#inv_ref").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=savecm',
					data:{xdate:xdate,cm_details:cm_details,cm_amount:cm_amount,acctid:acctid,inv_ref:inv_ref},
					type:"POST",
					success:function(data){
						res = data.split(",");
						if(res[0]=="success"){
							viewCM(res[1]);
							//viewTrans();
						}else{
							alert(data);
						}
					}
				});
			}
			function viewCM(id){
				if (window.showModalDialog) {
					window.showModalDialog('./reports/credit_memo.php?refid='+id,"Credit Memo","dialogWidth:700px;dialogHeight:550px");
				} else {
					window.open('./reports/credit_memo.php?refid='+id,"Credit Memo",'height=550,width=700,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
				}
			}
		</script>
		<?
	break;
	case"savecm":
		$stat = $db->saveCustTrans(array(
				'date'=>$_REQUEST['xdate'],
				'cust_id'=>$_REQUEST['acctid'],
				'receipt'=>'',
				'counter'=>$_SESSION['counter_num'],
				'reading'=>$_SESSION['readingnum'],
				'transtype'=>'Credit Memo',
				'details'=>"CR:".$_REQUEST['cm_details'],
				'amount'=>str_replace(",","",number_format($_POST['cm_amount'],2))));
		$refid = mysql_insert_id();
		if($stat){
			echo "success,$refid";
		}else{
			echo $stat;
		}
	break;
	case"updateDateDelivered":
		?>
		<form method="post">
		<div style="float:left;margin-right:10px;">Date Delivered</div>
		<input type="text" id="date_delivered" name="date_delivered" style="float:left;margin-right:10px;"/>
		<input type="submit" value="Update" style="float:left;height:30px;width:100px;"/>
		<input type="hidden" id="cust_id" name="cust_id" value="<?=$_REQUEST['cust_id']?>"/>
		<input type="hidden" id="receipt" name="receipt" value="<?=$_REQUEST['receipt']?>"/>
		
		</form>
		<script>
		$(document).ready(function() {
			$('#date_delivered').datepicker({
				inline: true,
				dateFormat:"yy-mm-dd"
			});
		});
		</script>
		<?
	break;
	case"addcustomer":
		?>
		<form name="frmcust" id="frmcust" method="post">
			<div style="float:left;margin-right:5px;width:150px;">Name:</div>
			<input type="text" name="customer_name" id="customer_name" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Address:</div>
			<textarea name="customer_address" id="customer_address" style="float:left;width:215px;"></textarea>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Nature of Business:</div>
			<input type="text" name="nature_of_business" id="nature_of_business" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">TIN:</div>
			<input type="text" name="tin" id="tin" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<?php if($view==6){ ?>
			<div style="float:left;margin-right:5px;width:150px;">Mall Unit Number:</div>
			<input type="text" name="mall_unit_number" id="mall_unit_number" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Contract Start Date:</div>
			<input type="text" name="contract_start_date" id="contract_start_date" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Contract End Date:</div>
			<input type="text" name="contract_end_date" id="contract_end_date" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Floor Area (sqm):</div>
			<input type="text" name="floor_area_sqm" id="floor_area_sqm" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Fixed Rental/sqm:</div>
			<input type="text" name="fixed_rental_per_sqm" id="fixed_rental_per_sqm" style="float:left;width:215px;"/>
			<div style="clear:both;height:5px;"></div>
			<?php } ?>
			<input type="button" value="Save" style="float:left;height:40px;width:180px;" onclick="saveAddcust()"/>
		</form>
		<script>
			$(document).ready(function() {
				$('#contract_start_date').datepicker({
					changeMonth: true,
					changeYear: true,
					inline: true,
					dateFormat:"yy-mm-dd"
				});
				$('#contract_end_date').datepicker({
					changeMonth: true,
					changeYear: true,
					inline: true,
					dateFormat:"yy-mm-dd"
				});
			});
			function saveAddcust(){
				var datastring = $("#frmcust").serialize();
				// var customer_name = $("#customer_name").val();
				// var customer_address = $("#customer_address").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=saveAddcust',
					//data:{customer_name:customer_name,customer_address:customer_address},
					data:datastring,
					type:"POST",
					success:function(data){
						if(data=="success"){
							show_custlist();
							$('#dialogbox2').dialog('close');
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case"saveAddcust":
		//$sql = "insert into tbl_customers set customer_name='{$_REQUEST['customer_name']}',customer_address='{$_REQUEST['customer_address']}'";
		// $sql = "insert into tbl_customers (cust_id,customer_name,customer_address,nature_of_business,mall_unit_number,tin,contract_start_date,contract_end_date,floor_area_sqm,fixed_rental_per_sqm) values 
		// ('".$_REQUEST['cust_id']."','".mysql_real_escape_string($_REQUEST['customer_name'])."','".mysql_real_escape_string($_REQUEST['customer_address'])."','".mysql_real_escape_string($_REQUEST['nature_of_business'])."','".$_REQUEST['mall_unit_number']."','".$_REQUEST['tin']."','".$_REQUEST['contract_start_date']."','".$_REQUEST['contract_end_date']."','".$_REQUEST['floor_area_sqm']."','".$_REQUEST['fixed_rental_per_sqm']."') 
		// on duplicate key update customer_name=values(customer_name),customer_address=values(customer_address),nature_of_business=values(nature_of_business),mall_unit_number=values(mall_unit_number),tin=values(tin),contract_start_date=values(contract_start_date),contract_end_date=values(contract_end_date),floor_area_sqm=values(floor_area_sqm),fixed_rental_per_sqm=values(fixed_rental_per_sqm)";
		unset($_REQUEST['execute']);
		$sql_data= "('".implode("', '", array_map('mysql_real_escape_string', $_REQUEST))."')";
		$sql="insert into tbl_customers (`".implode('`,`',array_keys($_REQUEST))."`) values 
			$sql_data 
			on duplicate key update ";
		$flag=false;
		foreach(array_keys($_REQUEST) as $a => $b){
			if($flag)$sql.=",";
			$sql.="$b=values($b)";
			$flag=true;
		}
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "success";
		}
	break;
	case'viewWorkorders':
	$sql="select * from tbl_workorders where cust_id='{$_REQUEST['acctid']}'";
	$qry = mysql_query($sql);
	echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%" border="1">
			<tr>
				<th>Date</th>
				<th>Details</th>
				<th>Status</th>
			</tr>';
	while($row=mysql_fetch_assoc($qry)){
		echo "<tr>
			<td>".$row['date']."</td>
			<td>".$row['details']."</td>
			<td>".$row['status']."</td>
		</tr>";
	}
	echo '</table>';
	break;
	case"custtransdetails":
		$txtsearch="";
		if($_REQUEST['txtsearch']){
			$txtsearch="and a.receipt in (select refid from tbl_sales_invoice_items where item_spec like '%{$_REQUEST['txtsearch']}%' or barcode like '%{$_REQUEST['txtsearch']}%')";
		}
		$sql = "select a.*,b.date_delivered from tbl_customers_trans a
		left join tbl_customers_trans_delivery b on a.receipt=b.receipt and a.cust_id=b.cust_id 
		where a.cust_id='{$_REQUEST['acctid']}' $txtsearch order by a.date desc";
		//$sql = "select * from tbl_customers_trans where cust_id='{$_REQUEST['acctid']}' order by date desc";
		$qry = mysql_query($sql);
		//echo '<table style="float:right;">';
		//echo '<tr>';
		//echo '<td><input value="Print" onclick="window.print();" style="width:100px;" type="button"></input></td>';
		//echo '</tr>';
		//echo '</table>';
		echo "<fieldset>
		<div style='float:left;margin-right:10px;'>Search:</div>
		<input type='text' name='txtsearch' id='txtsearch' style='width:300px;float:left;'/>
		<input type='button' value='Execute' style='width:150px;height:25px;float:right;' onclick='refreshTrans()'/>
		</fieldset>";
		echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%" border="1" style="font-size:12px;">';
		echo '<tr>
				<th>ReceiptDate</th>
				<th>Details</th>
				<th>Amount</th>
				<th>DateDelivered</th>
				'.($_SESSION['settings']['system_name']=='Rber System'?'
				<th>Paid Date</th>
				<th>Payment Details</th>
				<th>Paid Amount</th>
				<th>W/Vat</th>
				<th>Menu</th>':'').'
			</tr>';
		while($row=mysql_fetch_assoc($qry)){
			$items = $db->resultArray("*","tbl_sales_invoice_items","where refid='{$row['receipt']}'");
			$items_="<table style='width:100%;'><tr><td colspan='5'>".($row['transtype']=='Payment'?"OR# ".$row['receipt']:"")." {$row['details']}</td></td>";
			foreach($items as $k=>$v){
				$items_.="<tr><td>&nbsp;&nbsp;{$v['barcode']}</td><td>{$v['item_spec']}</td><td>{$v['qty']}</td><td>".number_format($v['unitprice']-($v['unitprice']/9.333333),2)."</td><td>".number_format($v['unitprice'],2)."</td><td style='text-align:right;'>".number_format($v['amount'],2)."</td></tr>";
			}
			$items_.="</table>";
			$amt_display = number_format($row['amount'],2);
			if($row['transtype']=='Payment' or $row['transtype']=='Credit Memo'){
				$color = 'style="color:red;"';
				$amt_display = "(".number_format($row['amount'],2).")";
			}else if($row['date_delivered']=='' and $row['transtype']!="Adjustment"){
				$color = 'style="color:green;"';
			}else{
				$color = '';
			}
			$delbtn = $_SESSION['restrictionid']==1?'<img src="./images/del.png" title="Delete Records" onclick="delCustRec('.$row['id'].')"/>':'';
			$paidbtn = '<img src="./images/cashdetails.png" title="Paid" style="width:20px;height:20px;float:left;" onclick="setPaid('.$row['id'].')"/>';
			//$statementbtn = '<img src="./images/print.png" style="width:20px;height:20px;float:left;" onclick="viewReport(\'./reports/soa.php?refid='.$row['receipt'].'\')"/>';
			echo '<tr>
					<td '.$color.'>'.($row['date']=="0000-00-00 00:00:00"?"":date('Y-m-d',strtotime($row['date']))).'</td>
					<td '.$color.'>'.$items_.'</td>
					<td '.$color.' align="right">'.$amt_display.'</td>
					<td>'.$row['date_delivered'].'</td>'.($_SESSION['settings']['system_name']=='Rber System'?
					'<td>'.($row['paid_date']=='0000-00-00'?'':$row['paid_date']).'</td>
					<td>'.$row['paid_details'].'</td>
					<td align="right">'.number_format($row['paid_amount'],2).'</td>
					<td align="right">'.number_format($row['paid_wvat'],2).'</td>
					<td>'.$paidbtn.'</td>':'').'
				</tr>';
		}
		echo '</table>';
		?>
		<script>
			function refreshTrans(){
				var txtsearch=$("#txtsearch").val();
				clickDialog('dialogbox2',1000,500,'custtransdetails&acctid=<?=$_REQUEST['acctid']?>&txtsearch='+txtsearch,'Customer Transaction Details');
			}
			function setPaid(refid){
				clickDialog('dialogbox3',400,300,'setPaid&refid='+refid,'Payment Details');
			}
			function delCustRec(id){
				var r=confirm("Are you sure to Delete this record?");
					if (r==true){
					  $.ajax({
							url: './content/pos_ajax.php?execute=delCustRec',
							data:{refid:id},
							type:"POST",
							success:function(data){
								if(data=="success"){
									viewTrans();
								}else{
									alert(data);
								}
							}
						});
					}
				
			}
		</script>
		<?php
	break;
	case'setPaid':
		$info = $db->getWHERE("*","tbl_customers_trans","where id='{$_REQUEST['refid']}'");
	?>
	<form name="frmpaid" id="frmpaid">
	<input type="hidden" value="<?=$_REQUEST['refid']?>" name="refid"/>
	<input type="hidden" value="<?=$info['amount']?>" id="invoice_amt"/>
	<div style="float:left;margin-right:5px;width:100px;">Date</div>
	<input value="<?=$info['paid_date']?>" type="text" id="paid_date" name="paid_date" style="float:left;width:250px;"/>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;margin-right:5px;width:100px;">Details</div>
	<input value="<?=$info['paid_details']?>" type="text" name="paid_details" style="float:left;width:250px;"/>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;margin-right:5px;width:100px;">Paid Amount</div>
	<input value="<?=$info['paid_amount']?>" type="text" name="paid_amount" id="paid_amount" style="float:left;width:250px;"/>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;margin-right:5px;width:100px;">Type</div>
	<select name="paid_type" id="paid_type" style="float:left;width:250px;">
		<option <?=$info['paid_type']=="ZERO VAT"?"SELECTED":""?> value="ZERO VAT">ZERO VAT</option>
		<option <?=$info['paid_type']=="WITH VAT"?"SELECTED":""?> value="WITH VAT">W/VAT</option>
	</select>
	<div style="clear:both;height:5px;"></div>
	<div style="float:left;margin-right:5px;width:100px;">Paid W/VAT</div>
	<input value="<?=$info['paid_wvat']?>" type="text" name="paid_wvat" id="paid_wvat" style="float:left;width:250px;"/>
	<div style="clear:both;height:5px;"></div>
	<input type="button" value="SAVE" style="height:30px;width:150px;" onclick="updatePaid()"/>
	</form>
	<script>
	$(document).ready(function() {
		$('#paid_date').datepicker({
			changeMonth: true,
			changeYear: true,
			inline: true,
			dateFormat:"yy-mm-dd"
		});
	});
	$( "#paid_type" ).change(function() {
	  if($(this).val()=="WITH VAT"){
		  $("#paid_wvat").val(strtocurrency($("#invoice_amt").val()-strtodouble($("#paid_amount").val())));
	  }
	});
	function updatePaid(){
		var datastring = $("#frmpaid").serialize();
		$.ajax({
			url: './content/pos_ajax.php?execute=updatePaid',
			data:datastring,
			type:"POST",
			success:function(data){
				if(data=="success"){
					$("#dialogbox3").dialog('close');
					viewTrans();
				}else{
					alert(data);
				}
			}
		});
	}
	</script>
	<?
	break;
	case'updatePaid':
		$data=array(
			'id'=>$_REQUEST['refid'],
			'paid_date'=>$_REQUEST['paid_date'],
			'paid_details'=>$_REQUEST['paid_details'],
			'paid_type'=>$_REQUEST['paid_type'],
			'paid_wvat'=>str_replace( ',', '', $_REQUEST['paid_wvat']),
			'paid_amount'=>str_replace( ',', '', $_REQUEST['paid_amount'])
		);
		$sql = $db->genSqlInsert($data,'tbl_customers_trans');
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case"delCustRec":
		$sql="delete from tbl_customers_trans where id='".$_REQUEST['refid']."'";
		$qry = mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case"savecusttosession":
		$_SESSION['customer_info']=array('cust_id'=>$_REQUEST['cust_id'],'name'=>$_REQUEST['cust_name']);
		echo "success";
	break;
	case'setAsdefaultCust':
		$_SESSION['custid']=$_REQUEST['cust_id'];
		echo "success";
	break;
	case"custdetails":
		/*$sql = "select a.*,b.bal from tbl_customers as a
			left join 
			(select cust_id,COALESCE(sum(if(transtype!='Cash' or transtype!='Adjustment',if(transtype='Payment' or transtype='Credit Memo',amount * -1,amount),0)),0) as bal from tbl_customers_trans 
			where cust_id='{$_REQUEST['cust_id']}') as b on a.cust_id=b.cust_id 
			where a.cust_id='".$_REQUEST['cust_id']."' limit 1";*/
		if($_SESSION['default_db']=='rber_db'){
		$sql = "select x.*,y.total_undelivered from 
				(
				select *,
				COALESCE(sum(if(transtype='Payment' or transtype='Credit Memo',amount * -1,if(transtype='Cash',0,(amount-(COALESCE(paid_amount,0)+COALESCE(paid_wvat,0)))))),0) as total_bal 
				from (
				select tbl_main.*,c.customer_name
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt) as tbl_main
				left join tbl_customers c on tbl_main.cust_id=c.cust_id) as tbl group by cust_id
				) as x
				left join
				(
				select cust_id,amount,date_delivered,transtype,coalesce(sum(if(date_delivered is null,(amount-(COALESCE(paid_amount,0)+COALESCE(paid_wvat,0))),0)),0) as total_undelivered from (select tbl_main.* 
				from (select a.*,b.date_delivered from tbl_customers_trans a 
				left join tbl_customers_trans_delivery b
				on a.cust_id=b.cust_id and a.receipt=b.receipt where transtype!='Cash' and transtype!='Payment' and transtype!='Adjustment' and transtype!='Credit Memo') as tbl_main) as tbl group by cust_id
				) as y on x.cust_id=y.cust_id where x.cust_id='{$_REQUEST['cust_id']}'
				";
		}else{
			$sql = "select a.*,b.bal total_bal from tbl_customers as a
			left join 
			(select cust_id,COALESCE(sum(if(transtype!='Cash' or transtype!='Adjustment',if(transtype='Payment' or transtype='Credit Memo',amount * -1,amount),0)),0) as bal from tbl_customers_trans 
			where cust_id='{$_REQUEST['cust_id']}') as b on a.cust_id=b.cust_id 
			where a.cust_id='".$_REQUEST['cust_id']."' limit 1";
		}
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			$info = mysql_fetch_assoc($qry);
			$custdetails = $db->getWHERE("*","tbl_customers","where cust_id='{$_REQUEST['cust_id']}'");
		}
		//print_r($info);
		?>
		<div style="width:45%;float:left;">
			<div style="float:left;margin-right:5px;width:100px;">Acct No:</div>
			<div style="float:left;" id="acctid"><?= $db->customeFormat($_REQUEST['cust_id'],5)?></div>
			<div style="clear:both;height:5px;"></div>
			<!--div style="float:left;margin-right:5px;width:100px;">Unit:</div>
			<div style="float:left;"><?= $db->customeFormat(1,3)?></div-->
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Name:</div>
			<div style="float:left;"><?= $custdetails['customer_name']?></div>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:100px;">Address:</div>
			<div style="float:left;"><?= $custdetails['customer_address']?></div>
			<div style="clear:both;height:5px;"></div>
			<?php if($_SESSION['restrictionid']==1||$_SESSION['restrictionid']==7){ ?>
				<fieldset>
					<legend>Financial Obligation</legend>
					<div style="float:left;margin-right:5px;width:100px;">Total Balance:</div>
					<div style="float:left;"><?= number_format($info['total_bal'],2)?></div>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:5px;width:100px;">Undelivered:</div>
					<div style="float:left;color:red;"><?= number_format($info['total_undelivered'],2)?></div>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;margin-right:5px;width:100px;">Actual Balance:</div>
					<div style="float:left;"><?= number_format($info['total_bal']-$info['total_undelivered'],2)?></div>
				</fieldset>
			<?php } ?>
		</div>
		<div style="width:45%;float:right;">
			<fieldset>
					<legend>Other Information</legend>
					<div style="float:left;margin-right:10px;">
						<div style="float:left;margin-right:5px;width:100px;">LandLine:</div>
						<div style="float:left;"><?= $custdetails['contact_number']?></div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:5px;width:100px;">Mobile:</div>
						<div style="float:left;"><?= $custdetails['mobile_number']?></div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:5px;width:100px;">Email:</div>
						<div style="float:left;"><?= $custdetails['email_add']?></div>
						<div style="clear:both;height:5px;"></div>
						<!--div style="float:left;margin-right:5px;width:100px;">Parking Slot:</div>
						<div style="float:left;"></div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:5px;width:100px;">Vehicle Type:</div>
						<div style="float:left;"></div>
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:5px;width:100px;">Plate #:</div>
						<div style="float:left;"></div>
						<div style="clear:both;height:5px;"></div-->
					</div>
					<!--img src="./images/profilepix.jpg" style="float:right;width:200px;height:200px;"/-->
			</fieldset>
		</div>
		<?
	break;
	case"input_moreinfo":
		$qry_agent = mysql_query("select * from req_agent");
		$area = $db->resultArray("*","tbl_location","");
		?>
		<div style="float:left;margin-right:5px;width:130px;">W/drawal Slip: </div>
		<input value="<?= $_SESSION['more_info']['ws']!=""?$_SESSION['more_info']['ws']:"" ?>" type="text" name="ws" id="ws" style="float:left;" />
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:130px;">Order Book: </div>
		<input value="<?= $_SESSION['more_info']['ob']!=""?$_SESSION['more_info']['ob']:"" ?>" type="text" name="ob" id="ob" style="float:left;" />
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:130px;">Location: </div>
		<select name="location" id="location" style="float:left;width:185px;">
			<option value="">Select</option>
			<?php foreach($area as $key => $val){ ?>
				<option <?= $_SESSION['more_info']['location']==$val['location_name']?"Selected":"" ?> value="<?=$val['location_name']?>"><?=$val['location_name']?></option>
			<?php } ?>
		</select>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:130px;">Agent: </div>
		<select name="agent" id="agent" style="float:left;width:185px;">
			<option value="">Select</option>
			<? while($row = mysql_fetch_assoc($qry_agent)){ ?>
				<option <?= $_SESSION['more_info']['agent']==$row['agent_name']?"Selected":"" ?> value="<?=$row['agent_name']?>"><?=$row['agent_name']?></option>
			<? } ?>
		</select>
		<div style="clear:both;height:5px;"></div>
		<input onclick="saveMoreInfo()" type="button" id="savetra" value="Save" style="height:40px;width:100px;"/>
		<script>
			function saveMoreInfo(){
				var ws = $("#ws").val();
				var ob = $("#ob").val();
				var location = $("#location").val();
				var agent = $("#agent").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=saveMoreInfo',
					data:{ws:ws,ob:ob,location:location,agent:agent},
					type:"POST",
					success:function(data){
						if(data=="success"){
							$('#dialogbox').dialog('close');
							//window.location="index.php";
							//window.location="index.php?page=pos";
							window.location="<?=$location?>";
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case"saveMoreInfo":
		$_SESSION['more_info']=array(
			'ws'=>$_REQUEST['ws'],
			'ob'=>$_REQUEST['ob'],
			'location'=>$_REQUEST['location'],
			'agent'=>$_REQUEST['agent']
		);
		if($_SESSION['more_info']){
			echo "success";
		}
	break;
	case"input_tra":
		?>
		<div style="float:left;margin-right:5px;">TRA/DR Num: </div>
		<input type="text" name="tra_num" id="tra_num" style="float:left;" onchange="$('#savetra').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<input onclick="saveTRA()" type="button" id="savetra" value="Save" style="height:40px;width:100px;"/>
		<script>
			function saveTRA(){
				var tranum = $("#tra_num").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=savetra',
					data:{tranum:tranum},
					type:"POST",
					success:function(data){
						if(data=="success"){
							$('#dialogbox').dialog('close');
							//window.location="index.php";
							window.location="<?=$location?>";
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case"savetra":
		$_SESSION['tra_num']=$_REQUEST['tranum'];
		if($_SESSION['tra_num']){
			echo "success";
		}
	break;
	case'startreading':
	?>
		<form method="post">
			<div style="float:left;width:55px;margin-right:10px;">Station:</div><input readonly type="text" name="station" value="<?=$_SESSION['counter_num']?>" style="width:170px;margin-left:20px;text-align:right;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:55px;margin-right:10px;">Date:</div><input readonly type="text" name="date" value="<?php echo date('Y-m-d') ?>" style="width:170px;margin-left:20px;text-align:right;"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;width:55px;margin-right:10px;">User:</div><input readonly type="text" name="username" value="<?=$_SESSION['xuser_pos']?>" style="width:170px;margin-left:20px;text-align:right;"/>
			<div style="clear:both;height:15px;"></div>
			
			<input type="submit" value="StartReading" name="startreading" style="height:40px;width:150px;float:left;"/>
		</form>
	<?php
	break;
	case'startServerReading':
		$sr = $db->getServerReadingnum();
		if($sr){
			echo "<h3>Currently ServerReading: $sr are still open...</h3>";
		}else{
	?>
		<form method="post">
			<div style="float:left;width:55px;margin-right:10px;">Date:</div><input readonly type="text" name="date" value="<?php echo date('Y-m-d') ?>" style="width:175px;margin-left:20px;text-align:right;"/>
			<div style="clear:both;height:15px;"></div>
			<input type="submit" value="StartServerReading" name="startserverreading" style="height:40px;width:150px;float:left;"/>
		</form>
	<?php
		}
	break;
	case'checkLoginCredential':
		$sql = "select * from tbl_user where user='".$_REQUEST['user']."' and password='".$_REQUEST['password']."' limit 1";
		$qry = mysql_query($sql);
		if($qry){
			if(mysql_num_rows($qry)>0){
				$rec=mysql_fetch_assoc($qry);
			}else{
				$rec['msg']="No records found...";
			}
		}else{
			$rec['msg']=mysql_error();
		}
		echo '{"tbl_rec":'. json_encode($rec) .'}';
	break;
	case'checkLoginCredentialFront':
		$openform = false;
		$msg="";
		if($_REQUEST['type_name']=="reading_end"){
			if($ask_cashdetails){
				$cashbreakdown = mysql_query("select total from tbl_cashdetails where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
				if(mysql_num_rows($cashbreakdown)>0){
					$openform = true;
				}else{
					$msg="Please input your Cash Details first...";
				}
			}else{
				$openform = true;
			}
		}else if($_REQUEST['type_name']=="reading_end_server"){
			$checkopen = $db->resultArray("*","tbl_reading","where end_date='0000-00-00' and reading_num='".$db->getServerReadingnum()."'");
			if($checkopen){
				$flag=false;
				foreach($checkopen as $k => $v){
					if($flag)$station.=",";
					$station.=$v['counter'];
					$flag=true;
				}
				$msg="There's still open reading per station, pls perform End Reading every station!<br/><br/>Station:$station";
			}else{
				$checkServer = $db->getWHERE("*","tbl_reading_server","where end_date='0000-00-00'");
				if($checkServer){
					$openform = true;
				}else{
					$msg="There's no open reading right now...";
				}
				
			}
		}else{
			$openform = true;
		}
		if($openform==true){
		?>
			<form name="frmlogin" id="frmlogin" method="post">
				<div style="float:left;width:130px;">User:</div><input type="text" name="user" onchange="$('#password').focus()" style="float:left;width:140px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:130px;">Password:</div><input autocomplete="off" type="password" name="password" id="password" onchange="$('#xxlogin').focus()" style="float:left;width:140px;"/>
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="Login" name="xxlogin" id="xxlogin" onclick="document.frmlogin.submit();" style="height:40px;width:100px;"/>
				<input type="hidden" name="type_name" value="<?= $_REQUEST['type_name']?>"/>
				<input type="hidden" name="xlogin" value="true"/>
				<?
				foreach($_REQUEST as $key => $val){
					if($key!="type_name"&&$key!="execute"){
						echo '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
					}
				}
				?>
			</form>
		<?php
		}else{
			echo $msg;
		}?>
			<script>
				function checkLoginCredential(){
					var datastring = $("#frmlogin").serialize();
					$.ajax({
						url: './content/pos_ajax.php?execute=checkLoginCredential',
						dataType:"json",
						data:datastring,
						type:"POST",
						success:function(data){
							if(data.tbl_rec.restriction_id==1){
								$('#xlogin').dialog('close');
								permission=true;
							}
						}
					});
				}
			</script>
			<?
	break;
	case'xlogin':
		$openform = false;
		$msg="";
		if($_REQUEST['type_name']=="reading_end"){
			if($ask_cashdetails){
				$cashbreakdown = mysql_query("select total from tbl_cashdetails where counter_num='".$_SESSION['counter_num']."' and reading='".$_SESSION['readingnum']."'");
				if(mysql_num_rows($cashbreakdown)>0){
					$openform = true;
				}else{
					$msg="Please input your Cash Details first...";
				}
			}else{
				$openform = true;
			}
		}else{
			$openform = true;
		}
		if($openform==true){
	?>
		<form name="frmlogin" method="post">
			<div style="width:440px;">
				<div style="float:left;">
					<img src="./images/login.jpg" style="width:125px;height:125px;"/>
				</div>
				<div style="float:right;padding-top:25px;">
					<div style="float:left;width:130px;">User:</div><input type="text" name="user" onchange="$('#password').focus()" style="float:left;width:140px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="float:left;width:130px;">Password:</div><input autocomplete="off" type="password" name="password" id="password" onchange="$('#xxlogin').focus()" style="float:left;width:140px;"/>
				</div>
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="Clear"  onclick="signOut();" style="float:left;height:40px;width:100px;"/>
				<input type="button" value="Login" name="xxlogin" id="xxlogin" onclick="document.frmlogin.submit();" style="float:right;height:40px;width:100px;"/>
				<input type="hidden" name="type_name" value="<?= $_REQUEST['type_name']?>"/>
				<input type="hidden" name="xlogin" value="true"/>
				<?
				foreach($_REQUEST as $key => $val){
					if($key!="type_name"&&$key!="execute"){
						echo '<input type="hidden" name="'.$key.'" value="'.$val.'"/>';
					}
				}
				?>
			</div>
		</form>
	<?php
		}else{
			echo $msg;
		}
	break;
	case'openRefundBox':
		$content = '<div style="height:100%;width:100%;">
		<div style="float:left;margin-right:5px;width:150px;">Total:</div>
		<input readonly type="text" id="total" name="total" value="'.str_replace(",","",number_format($_REQUEST['total'],2)).'"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Type of Payment:</div>
		<input type="text" name="payment_type" id="payment_type" style="width:200px;" value="REFUND" />
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" onclick="saveRefund()" style="width:150px;height:30px;position:absolute;bottom:10px;left:10px;"/>
		<input type="button" value="Close" onclick="closePaymentBox()" style="width:150px;height:30px;position:absolute;bottom:10px;right:10px;"/>
		</div>';
		echo $db->createOverlay(450,300,$content);
		?>
		<script>
			function saveRefund(){
				document.frm_pos.submit();
			}
			function closePaymentBox(){
				$('#barcode').val("");
				$('#barcode').focus();
				$('#tenderPayment').html("");
				$("#modal-background").toggleClass();
			}
		</script>
		<?
	break;
	case'tender_payment':
		if($view==1){
			$payment_type = '<option value="Cheque">Cheque</option>';
		}else if($view==2){
			$payment_type = '
				<option value="COD">COD</option>
				<option value="Term 1week">Term 1week</option>
				<option value="Term 10days">Term 10days</option>
				<option value="Term 15days">Term 15days</option>
				<option value="Term 30days">Term 30days</option>';
		}else if($view==3){
			$payment_type = '<option value="Cheque">Cheque</option>
							<option value="Term 1week">Term 1week</option>
							<option value="Term 10days">Term 10days</option>
							<option value="Term 15days">Term 15days</option>
							<option value="Term 30days">Term 30days</option>';
		}else if($view==4){ //la marea settings
			$payment_type = '<option value="Cheque">Cheque</option>
							<option value="CreditCard">Credit Card</option>
							<option value="NonSale">NonSale</option>';
		}else if($view==5){ //lizgan settings
			$payment_type = '<option value="Voucher">Voucher</option>
							<option value="Cheque">Cheque</option>
							<option value="Term 30days">Term 30days</option>';
		}else{
			$payment_type = '
							<option value="30 Days">30 Days</option>
							<option value="45 Days">45 Days</option>
							<option value="60 Days">60 Days</option>
							<option value="15 Days">15 Days</option>';
		}
		$content = '<div style="height:100%;width:100%;">
		<div style="float:left;margin-right:5px;width:150px;">Receipt Num:</div>
		<input readonly type="text" name="receipt_num" value="'.$_SESSION['receiptnum'].'" style="text-align:right;border:none;background:none;" readonly/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Total:</div>
		<input readonly type="text" id="total" name="total" value="'.str_replace(",","",number_format($_REQUEST['total'],2)).'"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Type of Payment:</div>
		<select name="payment_type" id="payment_type" style="width:200px;" onchange="paymentTypeSelect(this.value)">'.
			($_SESSION['customer_info']?'':'<option value="Cash">Cash</option>').'
			'.$payment_type.'
		</select>
		<div id="amtarea">
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Amount:</div>
			<input type="text" id="payment" onkeypress="return paymentGetChar(event,this.value);" name="payment" value="'.($_SESSION['order_info']['tender']?$_SESSION['order_info']['tender']:"").'"/>
			<div style="clear:both;height:5px;"></div>
			<div style="float:left;margin-right:5px;width:150px;">Change:</div>
			<input type="text" id="change" name="change"/>
		</div>
		<div style="clear:both;height:5px;"></div>
		<table id="multiple_payment" cellspacing="0" cellpadding="0" border="1" width="100%"></table>
		<input type="button" value="Save" onclick="document.frm_pos.submit()" style="width:150px;height:30px;position:absolute;bottom:10px;left:10px;"/>
		<input type="button" value="Close" onclick="closePaymentBox()" style="width:150px;height:30px;position:absolute;bottom:10px;right:10px;"/>
		</div>';
		//echo $content;
		echo $db->createOverlay(450,300,$content);
		?>
		<script>
			$(document).ready(function() {
				$("#payment").focus();
			});
			function closePaymentBox(){
				$('#barcode').val("");
				$('#barcode').focus();
				$('#tenderPayment').html("");
				$("#modal-background").toggleClass();
				window.location=removeURLParameter(document.URL, 'specialcase')+'&specialcase=2';
			}
			function paymentTypeSelect(val){
				if(val!=""){
					if(val != "Cash"){ //not cash
						if(val=="Cheque"){
							chequeInput();
						}else if(val=="CreditCard"){
							creditcardInput();
						}else if(val=="NonSale"){
							var r=confirm("Are you sure this is "+val+"?");
								if (r==true){
								  document.frm_pos.submit();
								}
						}else if(val=="Voucher"){
							voucherInput();
						}else if(val=="SplitPayment"){
							splitPayment();
						}else{ //if mode of payment are terms
							var cust ='<?=$_SESSION['customer_info']['cust_id']?>';
							$("#amtarea").css("display","none");
							var view = '<?=$view?>';
							var moreinfo = '<?=$_SESSION['more_info']['ws']?>';
							if(moreinfo=='' && view==2){
								alert("Dont forget to fill-up more info...");
								window.location="<?=$location?>";
							}else{
								var r=confirm("Are you sure this is "+val+"?");
								if (r==true){
								  if(cust!=''){
									document.frm_pos.submit();
								  }else{
									alert('Please select customer first...');
									window.location="<?=$location?>";
								  }
								}
							}
						}
					}else{
						$("#amtarea").css("display","block");
						$("#payment").focus();
					}
				}
			}
			function voucherInput(){
				clickDialog('dialogbox2',400,200,'voucherDetails','Voucher Details');
			}
			function chequeInput(){
				clickDialog('dialogbox2',400,200,'chequeDetails','Cheque Details');
			}
			function creditcardInput(){
				clickDialog('dialogbox2',400,300,'creditcardDetails','Credit Card Details');
			}
			function splitPayment(){
				clickDialog('dialogbox3',500,200,'splitPayment','Split Payment');
			}
		</script>
		<?
	break;
	case'splitPayment':
	?>
		<table id="mytbl" class="navigateable"  cellspacing="0" cellpadding="0" border="1" width="100%">
			<thead>
				<tr>
					<th>PaymentMode</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<select name="payment_type[]" style="width:200px;" onchange="paymentTypeSelect(this.value)">
							<option value="">Select</option>
							<option value="Voucher">Voucher</option>
						</select>	
					</td>
					<td>
						<input type="text" name="amount[]" id="amount1st" readonly onchange="changeAmt()"/>
					</td>
				</tr>
				<tr>
					<td>
						<select name="payment_type[]" style="width:200px;" onchange="paymentTypeSelect(this.value)">
							<option value="Cash">Cash</option>
						</select>	
					</td>
					<td>
						<input type="text" name="amount[]" id="amount2nd" onchange="changeAmt()"/>
					</td>
				</tr>
				<tr>
					<td>Total</td>
					<td><input type="text" name="total_amt" id="total_amt" readonly /></td>
				</tr>
			</tbody>
		</table>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" style="height:30px;width:150px;" onclick="splitSave();"/>
		<script>
			function changeAmt(){
				$("#total_amt").val(new Number($("#amount1st").val())+new Number($("#amount2nd").val()));
			}
			function splitSave(){
				$("#dialogbox3").dialog("close");
				//$("#payment").val($("#total_amt").val());
				$("#tenderPayment").find("#payment").val($("#total_amt").val());
			}
		</script>
	<?
	break;
	case"saveOrderSlip":
		$check = $db->getWHERE("studentname","tbl_sales_receipt_{$_SESSION['counter_num']}","where orderslip='{$_REQUEST['OrderSlip']}' and date='".date('Y-m-d')."'");
		if($check){
			echo "Order slip already in used under ".$check['studentname'];
		}else{
			$_SESSION['OrderSlip']=$_REQUEST['OrderSlip'];
			echo "success";
		}
	break;
	case"voucherDetails":
		?>
		<div style="float:left;margin-right:10px;width:150px;">Voucher No. </div>
		<input type="text" style="float:left;" name="voucher_num" id="voucher_num" onchange="$('#voucher_details').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Voucher Details: </div>
		<input type="text" style="float:left;" name="voucher_details" id="voucher_details" onchange="$('#voucher_amount').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Amount: </div>
		<input type="text" style="float:left;" name="voucher_amount" id="voucher_amount" onchange="$('#btnChequeSave').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" name="btnChequeSave" onclick="voucherToSession()" style="height:30px;width:150px;"/>
		<script>
			function voucherToSession(){
				var voucher_num = $("#voucher_num").val();
				var voucher_details = $("#voucher_details").val();
				var voucher_amount = $("#voucher_amount").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=voucherToSession',
					data:{voucher_details:voucher_details,voucher_amount:voucher_amount,voucher_num:voucher_num},
					type:"POST",
					success:function(data){
						if(data=="success"){
							$("#dialogbox2").dialog("close");
							if($("#dialogbox3").find("#amount1st").length > 0) {
								$("#dialogbox3").find("#amount1st").val(voucher_amount);
							}else{
								$("#payment").val(voucher_amount);
								$("#payment").focus();
							}
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case"voucherToSession":
		$voucher_num=$_REQUEST['voucher_num'];
		$voucher_amount_details = $_REQUEST['voucher_details'];
		$voucher_amount_amount = $_REQUEST['voucher_amount'];
		$_SESSION['voucher']=array('voucher_num'=>$voucher_num,'details'=>$voucher_amount_details,'amount'=>$voucher_amount_amount);
		echo "success";
	break;
	case"creditcardDetails":
	?>
		<div style="float:left;margin-right:10px;width:150px;">CC Num: </div>
		<input type="text" style="float:left;" name="cc_num" id="cc_num" onchange="$('#cc_custname').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">CC Cust Name: </div>
		<input type="text" style="float:left;" name="cc_custname" id="cc_custname" onchange="$('#cc_approval').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Approval Code: </div>
		<input type="text" style="float:left;" name="cc_approval" id="cc_approval" onchange="$('#cheque_amount').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Card Type: </div>
		<select name="card_type" id="card_type" style="float:left;width:200px;">
			<option value="MasterCard">MasterCard</option>
			<option value="VISA">VISA</option>
		</select>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Amount: </div>
		<input type="text" style="float:left;" name="cc_amount" id="cc_amount" />
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" name="btnCCSave" onclick="ccToSession()" style="height:30px;width:150px;"/>
		<script>
			function ccToSession(){
				var cc_num = $("#cc_num").val();
				var cc_custname = $("#cc_custname").val();
				var cc_approval = $("#cc_approval").val();
				var card_type = $("#card_type").val();
				var cc_amount = $("#cc_amount").val();
				if(cc_num != "" && cc_custname != "" && cc_approval !="" && card_type != "" && cc_amount !=""){
					$.ajax({
						url: './content/pos_ajax.php?execute=ccToSession',
						data:{cc_num:cc_num,cc_custname:cc_custname,cc_approval:cc_approval,card_type:card_type,cc_amount:cc_amount},
						type:"POST",
						success:function(data){
							if(data=="success"){
								$("#dialogbox2").dialog("close");
								$("#payment").val(cc_amount);
								$("#payment").focus();
							}else{
								alert(data);
							}
						}
					});
				}else{
					alert("Pls fill-out all information needed...");
				}
			}
		</script>
	<?
	break;
	case"ccToSession":
		$cc_num = $_REQUEST['cc_num'];
		$cc_custname = $_REQUEST['cc_custname'];
		$cc_approval = $_REQUEST['cc_approval'];
		$card_type = $_REQUEST['card_type'];
		$cc_amount = $_REQUEST['cc_amount'];
		$_SESSION['creditcard']=array('cc_num'=>$cc_num,'cc_custname'=>$cc_custname,'cc_approval'=>$cc_approval,'card_type'=>$card_type,'cc_amount'=>$cc_amount);
		echo "success";
	break;
	case"chequeDetails":
		?>
		<div style="float:left;margin-right:10px;width:150px;">Cheque Details: </div>
		<input type="text" style="float:left;" name="cheque_details" id="cheque_details" onchange="$('#cheque_amount').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:150px;">Amount: </div>
		<input type="text" style="float:left;" name="cheque_amount" id="cheque_amount" onchange="$('#btnChequeSave').focus()"/>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" name="btnChequeSave" onclick="chequeToSession()" style="height:30px;width:150px;"/>
		<script>
			function chequeToSession(){
				var cheque_details = $("#cheque_details").val();
				var cheque_amount = $("#cheque_amount").val();
				$.ajax({
					url: './content/pos_ajax.php?execute=chequeToSession',
					data:{cheque_details:cheque_details,cheque_amount:cheque_amount},
					type:"POST",
					success:function(data){
						if(data=="success"){
							$("#dialogbox2").dialog("close");
							$("#payment").val(cheque_amount);
							$("#payment").focus();
						}else{
							alert(data);
						}
					}
				});
			}
		</script>
		<?
	break;
	case"chequeToSession":
		$cheque_details = $_REQUEST['cheque_details'];
		$cheque_amount = $_REQUEST['cheque_amount'];
		$_SESSION['cheque']=array('details'=>$cheque_details,'amount'=>$cheque_amount);
		echo "success";
	break;
	case'removeOrder':
		$receipt = $_REQUEST['order_receipt'];
		$del1 = mysql_query("delete from tbl_order_receipt where receipt_id=$receipt");
		if($del1){
			$del2 = mysql_query("delete from tbl_order_items where receipt=$receipt");
			if($del1){
				echo "success";
			}else{
				echo mysql_error();
			}
		}
	break;
	case'discounting_frm':
	?>
	<div style="padding:5px;">
		<div style="float:left;margin-right:5px;width:150px;">Discount Type:</div>
		<select name="disc_type" id="disc_type" style="width:200px;">
			<? if($_REQUEST['page']=="stockin"){ ?>
				<option value="Supplier Discount">Supplier Discount</option>
			<?}elseif($_REQUEST['page']=="po"){?>
				<option value="Volume Discount">Volume Discount</option>
				<option value="Additional Discount">Additional Discount</option>
			<?}else{?>
				<option value="Senior Citizen">Senior Citizen</option>
				<option value="PWD">PWD</option>
			<?}?>
		</select>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Total Amount:</div>
		<input readonly type="text" name="total_amount" id="total_amount" value="<?=str_replace(",","",$_REQUEST['total_amount']) ?>"/>
		<div style="clear:both;height:5px;"></div>
		<?php if($view==4){ ?>
		<div style="float:left;margin-right:5px;width:150px;">Number of Person:</div>
		<input type="text" name="numperson" id="numperson" value="1" onchange="numperson(this.value);"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Name:</div>
		<input type="text" name="custname" id="custname"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">ID#:</div>
		<input type="text" name="idnum" id="idnum"/>
		<div style="clear:both;height:5px;"></div>
		<?php } ?>
		<div style="float:left;margin-right:5px;width:150px;">% Discount:</div>
		<input type="text" name="disc_percent" id="disc_percent" onchange="calcDisc(this.value)"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:5px;width:150px;">Total Discount:</div>
		<input type="text" name="total_disc" id="total_disc"/>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Save" id="btnDisc" style="height:40px;width:140px;" onclick="add_discount()"/>
	</div>
	<script>
		function numperson(val){
			var disc_percent = $('#disc_percent').val();
			if(disc_percent==""){
				$("#disc_percent").focus();
			}else{
				$('#total_disc').val((($('#total_amount').val()/new Number(val)).toFixed(2))*disc_percent);
			}
		}
		function calcDisc(val){
			var total = new Number($('#total_amount').val());
			// var numperson = $("#numperson").val();
			// if(numperson==""){
				// $('#total_disc').val((total * val).toFixed(2));
			// }else{
				// $('#total_disc').val(((total/numperson) * new Number(val)).toFixed(2));
			// }
			$('#total_disc').val((total * val).toFixed(2));
			$('#btnDisc').focus();
		}
		function add_discount(){
			var total_amount = $('#total_amount').val();
			var total_disc = $('#total_disc').val();
			var disc_type = $('#disc_type').val();
			var disc_percent = $('#disc_percent').val();
			var numperson = $("#numperson").val();
			var custname = $("#custname").val();
			var idnum = $("#idnum").val();
			$.ajax({
				url: './content/pos_ajax.php?execute=add_discount',
				data:{total_amount:total_amount,total_disc:total_disc,disc_type:disc_type,disc_percent:disc_percent,page:'<?=$_REQUEST['page']?>',numperson:numperson,custname:custname,idnum:idnum},
				type:"POST",
				success:function(data){
					if(data=="success"){
						$('#dialogbox').dialog('close');
						//window.location="index.php";
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	</script>
	<?
	break;
	case'add_discount':
		$total_amount = $_REQUEST['total_amount'];
		$disc_type = $_REQUEST['disc_type'];
		$total_disc = $_REQUEST['total_disc'];
		$disc_percent=$_REQUEST['disc_percent'];
		$numperson=$_REQUEST['numperson'];
		$custname=$_REQUEST['custname'];
		$idnum=$_REQUEST['idnum'];
		$_SESSION['count']+=1;
			if($_REQUEST['page']=="stockin"){
				$_SESSION['stockin']['DISCOUNT:'.$_SESSION['count']]=array(
					"count"=>0,
					"bcode"=>'DISCOUNT:'.$_SESSION['count'],
					"prod_name"=>$disc_type.": $total_amount ".($disc_percent?"x $disc_percent":""),
					"qty"=>-1,
					"unit"=>'trans',
					"price"=>$total_disc,
					"total"=>$total_disc * -1,
					"sku"=>'DISCOUNT:',
					"divmul"=>1,
					"cost"=>$total_disc
				);
			}elseif($_REQUEST['page']=="po"){
				// $_SESSION['po']['DISCOUNT:']=array(
					// "count"=>0,
					// "bcode"=>'DISCOUNT:',
					// "prod_name"=>$disc_type.": $total_amount ".($disc_percent?"x $disc_percent":""),
					// "qty"=>-1,
					// "unit"=>'trans',
					// "price"=>$total_disc,
					// "total"=>$total_disc * -1,
					// "sku"=>'DISCOUNT:',
					// "divmul"=>1,
					// "cost"=>$total_disc
				// );
				if($disc_type=="Volume Discount"){
					$_SESSION['poDISCOUNT1']=array('type'=>'volume_discount','amt'=>$total_disc);
				}else{
					$_SESSION['poDISCOUNT2']=array('type'=>'additional_discount','amt'=>$total_disc);
				}
			}else{
				$_SESSION['sales']['DISCOUNT:']=array(
					"count"=>0,
					"bcode"=>'DISCOUNT:',
					"prod_name"=>$disc_type.": ".($numperson?number_format($total_amount,2)." / $numperson ":number_format($total_amount,2)).($disc_percent?"x ".($disc_percent*100)."%":""),
					"qty"=>-1,
					"unit"=>'trans',
					"price"=>$total_disc,
					"total"=>$total_disc * -1,
					"sku"=>'DISCOUNT:',
					"divmul"=>1,
					"cost"=>$total_disc
				);
				$_SESSION['disc_info']=array(
					"disc_type"=>$disc_type,
					"cust_name"=>$custname,
					"id_num"=>$idnum,
					"details"=>($numperson?number_format($total_amount,2)." / $numperson ":number_format($total_amount,2)).($disc_percent?"x ".($disc_percent*100)."%":""),
					"total_disc"=>$total_disc
				);
			}
		echo "success";
	break;
	case'joinorderToSession':
		//unset($_SESSION['sales']);
		$receipt = $_REQUEST['order_receipt'];
		$info = $db->getWHERE("*","tbl_order_receipt","where receipt_id='".$receipt."'");
		$sql = "select * from tbl_order_items where receipt = $receipt";
		$qry = mysql_query($sql);
		if($qry){
			while($row = mysql_fetch_assoc($qry)){
				$_SESSION['count']+=1;
				if($_SESSION['sales'][$row['barcode']]){
					$_SESSION['sales'][$row['barcode']]=array(
						"count"=>$_SESSION['count'],
						"bcode"=>$row['barcode'],
						"prod_name"=>$row['item_desc'],
						"unit"=>$row['unit'],
						"price"=>$row['selling'],
						"sku"=>$row['skuid'],
						"divmul"=>$row['divmul'],
						"cost"=>$row['cost'],
						"qty"=>$_SESSION['sales'][$row['barcode']]['qty']+$row['qty'],
						"total"=>$_SESSION['sales'][$row['barcode']]['total']+$row['total']
					);
				}else{
					$_SESSION['sales'][$row['barcode']]=array(
						"count"=>$_SESSION['count'],
						"bcode"=>$row['barcode'],
						"prod_name"=>$row['item_desc'],
						"qty"=>$row['qty'],
						"unit"=>$row['unit'],
						"price"=>$row['selling'],
						"total"=>$row['total'],
						"sku"=>$row['skuid'],
						"divmul"=>$row['divmul'],
						"cost"=>$row['cost']
					);
				}
			}
			$_SESSION['order_info'] = array("tender"=>$info['tender'],"sales_clerk"=>$info['cashier']);
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	
	case'previewToSession':
		unset($_SESSION['sales']);
		$receipt = $_REQUEST['receipt_num'];
		$_SESSION['tra_num'] = $receipt;
		$info = $db->getWHERE("*","tbl_sales_receipt_{$_SESSION['counter_num']}","where receipt_id='".$receipt."'");
		$sql = "select * from  tbl_sales_items where receipt = $receipt";
		$qry = mysql_query($sql);
		if($qry){
			if($info['type']!="VOID"){
				while($row = mysql_fetch_assoc($qry)){
					$_SESSION['count']+=1;
					$_SESSION['sales'][$row['barcode']]=array(
						"count"=>$_SESSION['count'],
						"bcode"=>$row['barcode'],
						"prod_name"=>$row['item_desc'],
						"qty"=>$row['qty'],
						"unit"=>$row['unit'],
						"price"=>$row['selling'],
						"total"=>$row['total'],
						"sku"=>$row['skuid'],
						"divmul"=>$row['divmul'],
						"cost"=>$row['cost']
					);
				}
				if($view==2){
					$moreinfo = $db->getWHERE("*","tbl_sales_moreinfo","where sales_refid='$receipt'");
					$_SESSION['more_info']=array(
						'ws'=>$moreinfo['ws'],
						'ob'=>$moreinfo['ob'],
						'location'=>$moreinfo['location'],
						'agent'=>$moreinfo['agent']
					);
				}
				$cust = $db->getWHERE("b.cust_id,b.customer_name,a.transtype,a.date","tbl_customers_trans a 
					left join tbl_customers b on a.cust_id=b.cust_id","where a.receipt='$receipt'");
				if($cust){
					$_SESSION['customer_info']=array('cust_id'=>$cust['cust_id'],'name'=>$cust['customer_name'],'transtype'=>$cust['transtype'],'date'=>$cust['date']);
				}
				$_SESSION['previewToSession']=true;
				echo "success";
			}else{
				echo "$receipt is VOID...";
			}
		}else{
			echo mysql_error();
		}
	break;
	case'unsetSession':
		unset($_SESSION['sales']);unset($_SESSION['order_info']);unset($_SESSION['customer_info']);unset($_SESSION['tra_num']);unset($_SESSION['cheque']);
		unset($_SESSION['more_info']);unset($_SESSION['shoppers_card']);unset($_SESSION['previewToSession']);
		echo "success";
	break;
	case'saveorderToSession':
		unset($_SESSION['sales']);
		$receipt = $_REQUEST['order_receipt'];
		$info = $db->getWHERE("*","tbl_order_receipt"," where receipt_id='".$receipt."'");
		
		$patientInfo = $db->getWHERE("*","{$info['tblsource']}"," where idno='".$info['studentid']."'");
		$_SESSION['patient_info']=array(
			'idno'=>$info['studentid'],
			'name'=>$info['studentname'],
			'tblsource'=>$_REQUEST['tblsource'],
			'patientInfo'=>$patientInfo
		);
		
		$sql = "select * from tbl_order_items where receipt = $receipt";
		$qry = mysql_query($sql);
		if($qry){
			while($row = mysql_fetch_assoc($qry)){
				$_SESSION['count']+=1;
				$barcode = $row['barcode'];
				$prod_info = $db->getWHERE("sku_id,price,unit,divmul,cost","tbl_barcodes","where barcode='".$barcode."'");
				$prod_name = $db->getWHERE("*","tbl_product_name","where sku_id='".$prod_info['sku_id']."'");
				if($_REQUEST['type']=="uom"){
					$qty =1;
				}else{
					$qty = isset($_SESSION['sales'][$barcode])?$_SESSION['sales'][$barcode]['qty']+1:1;
				}
				$total = ($qty * $prod_info['price']);
				$_SESSION['sales'][$barcode]=array(
					"count"=>$_SESSION['count'],
					"bcode"=>$barcode,
					"prod_name"=>$prod_name['product_name'],
					"subjnametype"=>($prod_name['subject_name']?$prod_name['subject_name']."|".$prod_name['subjtype']:''),
					"qty"=>$qty,
					"unit"=>$prod_info['unit'],
					"price"=>$prod_info['price'],
					"total"=>$total,
					"sku"=>$prod_info['sku_id'],
					"divmul"=>$prod_info['divmul'],
					"cost"=>$prod_info['cost'],
					"tax_type"=>$prod_name['tax_type'],
					"category_id"=>$prod_name['category_id'],
					"discount"=>"0"
				);
			}
			$_SESSION['order_info'] = array("tender"=>$info['tender'],"sales_clerk"=>$info['cashier']);
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
	case 'process_barcode':
		$free=false;
		if(strpos($_REQUEST['barcode'],"|")){
			$free=true;
			$bcode = explode("|",$_REQUEST['barcode']);
		}
		
		$session_type=$_REQUEST['sessiontype']; //new added modification sept 14, 2014
		$barcode = $free?$bcode[0]:$_REQUEST['barcode'];
		$prod_info = $db->getWHERE("sku_id,price,unit,divmul,cost","tbl_barcodes","where barcode='".$barcode."'");
		$invbal = $db->invBal($prod_info['sku_id'],date('Y-m-d'));
		if($prod_info){
			if($session_type!='stockin'){ //sales and stockout restriction
				if($allow_negative_inv=="false"){
					if((float)$invbal<=0){
						//echo "Not allowed to make transaction with negative inventory...";
						echo '{"msg":"Not allowed to make transaction with negative inventory..."}';
						exit();
					}
				}
				if($allow_cost_markup!=0){
					if($prod_info['price']>($prod_info['cost']*($allow_cost_markup+1))){
						echo '{"msg":"Not allowed Price Mark-Up above '.($allow_cost_markup*100).'%"}';
						exit();
					}
				}
			}
			if($session_type=="sales"){
				if($allow_cost_markup!=0){
					if($prod_info['cost']==0){
						//echo "Not allowed 0 Cost...";
						echo '{"msg":"Not allowed 0 Cost..."}';
						exit();
					}
				}
				if($_SESSION['settings']['allow_price_below_cost']==false){
					if($prod_info['cost']>$prod_info['price']){
						//echo "Not allowed Price below Cost...";
						echo '{"msg":"Not allowed Price below Cost..."}';
						exit();
					}
				}
				
			}
			$prod_name = $db->getWHERE("*","tbl_product_name","where sku_id='".$prod_info['sku_id']."'");
			if($_REQUEST['type']=="uom" or $free==true){
				$qty =1;
			}else{
				$qty = isset($_SESSION[$session_type][$barcode])?$_SESSION[$session_type][$barcode]['qty']+1:1;
			}
			$total = ($qty * $prod_info['price']);
			$_SESSION['count']+=1;
			$_SESSION[$session_type][$free?$bcode[0]."|FREE":$barcode]=array(
				"count"=>$_SESSION['count'],
				"bcode"=>$free?$bcode[0]."|FREE":$barcode,
				"prod_name"=>$prod_name['product_name'],
				"subjnametype"=>($prod_name['subject_name']?$prod_name['subject_name']."|".$prod_name['subjtype']:''),
				"qty"=>$qty,
				"unit"=>$prod_info['unit'],
				"price"=>$prod_info['price'],
				"total"=>$total,
				"sku"=>$prod_info['sku_id'],
				"divmul"=>$prod_info['divmul'],
				"cost"=>$prod_info['cost'],
				"tax_type"=>$prod_name['tax_type'],
				"category_id"=>$prod_name['category_id'],
				"discount"=>"0"
			);
			/*Added to group by supplier*/
			$_SESSION['persup'][$prod_name['supplier_id']]['cost']+=($qty * $prod_info['cost']);
			$_SESSION['persup'][$prod_name['supplier_id']]['selling']+=$total;
			//echo "success";
			echo '{"rec":'. json_encode($_SESSION[$session_type][$barcode]) .',"msg":"success"}';
		}else{
			echo '{"msg":"Barcode not found..."}';
		}
	break;
	case'qtychange':
		$session_type=$_REQUEST['sessiontype']?$_REQUEST['sessiontype']:'sales';
		$session_type=$session_type=="touchscreen_prodlist"?"sales":$session_type;
		$barcode = $_REQUEST['barcode'];
		$qty = $_REQUEST['newqty'];
		$prod_info = $db->getWHERE("sku_id,price,unit,divmul,cost","tbl_barcodes","where barcode='".$barcode."'");
		$invbal = $db->invBal($prod_info['sku_id']);
		if($session_type!="stockin"){
			if(!$allow_negative_inv){
				if(($qty*$_REQUEST['divmul'])>$invbal){
					echo "New Qty are greater than actual inventory...";
					exit;
				}
			}
		}
		$_SESSION['count']+=1;
		$total = ($qty * $_SESSION[$session_type][$barcode]['price']);
		$_SESSION[$session_type][$barcode]['count'] = $_SESSION['count'];
		$_SESSION[$session_type][$barcode]['qty'] = $qty;
		$_SESSION[$session_type][$barcode]['total'] = $total;
		echo "success";
	break;
	case'pricechange':
		$session_type=$_REQUEST['sessiontype']?$_REQUEST['sessiontype']:'sales';
		$session_type=$session_type=="touchscreen_prodlist"?"sales":$session_type;
		$barcode = $_REQUEST['barcode'];
		$price = $_REQUEST['newprice'];
		$_SESSION['count']+=1;
		$total = ($price * $_SESSION[$session_type][$barcode]['qty']);
		$_SESSION[$session_type][$barcode]['count']=$_SESSION['count'];
		$_SESSION[$session_type][$barcode]['total']=$total;
		if($session_type=="sales"||$session_type=="package_create"){
			$_SESSION[$session_type][$barcode]['price']=$price;
			//$db->updatePrice($barcode,$price);
		}else{
			$_SESSION[$session_type][$barcode]['cost'] = $price;
			$db->updateCost($barcode,$price);
		}
		echo "success";
	break;
	case'delprodsale':
		$ref = $_REQUEST['ref'];
		switch($_REQUEST['type']){
			case'barcode_in':case'stockin':
				unset($_SESSION['stockin'][$ref]);
				if($_REQUEST['id']){
					$del = $db->delProd($_REQUEST['id'],"tbl_stockin_items");
				}
			break;
			case'barcode_out':case'stockout':
				unset($_SESSION['stockout'][$ref]);
				if($_REQUEST['id']){
					$del = $db->delProd($_REQUEST['id'],"tbl_stockout_items");
				}
			break;
			default:
				//unset($_SESSION['sales'][$ref]);
				if($_REQUEST['page']!="package_create"){
					$db->delProd($_REQUEST['id'],"tbl_{$_REQUEST['page']}_items");
				}
				if($_REQUEST['page']=="order"){
					unset($_SESSION['sales'][$ref]);
				}else{
					unset($_SESSION[$_REQUEST['page']][$ref]);
				}
			break;
		}
		echo "success";
	break;
	
	case'uom_list':
		$barcode = $_REQUEST['sku_id'];
		$info = $db->getWHERE("*","tbl_barcodes","where barcode='".$barcode."'");
		$sql = "select * from tbl_barcodes where sku_id='".$info['sku_id']."'";
				$qry = mysql_query($sql);
				if(!$qry){
					echo mysql_error();
				}
		?>
		<table class="navigateableuom" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcode</th>
					<th>Price</th>
					<th>Unit</th>
				</tr>
			</thead>
			<tbody>
				<? 	while($row = mysql_fetch_assoc($qry)){ ?>
				<tr>
					<td><a href="javascript:uomselect('<?php echo $row['barcode'] ?>')" class="activationuom"><?php echo $row['barcode'] ?></a></td>
					<td><?= number_format($row['price'],2)?></td>
					<td><?= $row['unit'] ?></td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<?
	break;
	case'packageslist':
		$where = $_REQUEST['search_desc']?"where package_name like '%{$_REQUEST['search_desc']}%'":"";
		$sql="select * from tbl_packages $where order by package_name asc";
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
		?>
		<div id="tbl_content" name="tbl_content" style="overflow:auto;height:420px;font-size:15px;">
			<table class="navigateable tbl" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th>RefID</th>
						<th width="500px">Desc</th>
						<th>Total Amount</th>
						<th>Menu</th>
					</tr>
				</thead>
				<tbody>
					<? 	
					if(mysql_num_rows($qry)==0){
						echo "<tr>
								<td colspan='3' align='center'>No Records Found...</td>
							</tr>";
					}
					while($row = mysql_fetch_assoc($qry)){ ?>
						<tr>
							<td><a href="javascript:packageSelected('<?php echo $row['id'] ?>')" class="activation"><?php echo $row['id'] ?></a></td>
							<td><?= $row['package_name']?></td>
							<td style="text-align:right;"><?= number_format($row['total_amt'],2) ?></td>
							<td>
								<img onclick="viewReport('./reports/orderslip.php?id=<?php echo $row['id'] ?>')" src="./images/print.png" style="width:20px;height:20px;float:left;" />
								<input type="checkbox" name="packages_check" value="<?=$row['id'] ?>"/>
							</td>
						</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
		<div style="clear:both;height:5px;"></div>
		<fieldset>
		<legend>Search Tracks</legend>
		<input onchange="search_tracks(this.value);" type="text" id="search_prodname" name="search_prodname" style="float:left;width:100%;"/>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
		<input type="button" value="Set To Default" style="width:100px;height:30px;" onclick="setTodefault();"/>
		<?php }else{ ?>
		<input type="button" value="Riso" style="width:100px;height:30px;" onclick="riso();"/>
		<?php } ?>
		<script>
			function search_tracks(val){
				var urls = getUrl();
				$("#prodlist").html("");
				clickDialog('prodlist',1000,550,'packageslist&search_desc='+val,'Package List',urls);
				jQuery.tableNavigation();
				$("#search_prodname").focus();
			}
			function riso(){
				var checkboxesChecked = [];
				$("input:checkbox[name=packages_check]:checked").each(function(){
					checkboxesChecked.push($(this).val());
				});
				// console.log(checkboxesChecked.length);
				// console.log(checkboxesChecked.join());
				if(checkboxesChecked.length<=4){
					viewReport('./reports/orderslip_whole.php?ids='+checkboxesChecked.join());
				}else{
					alert("Pls select not more than 4 packages....");
				}
			}
			function setTodefault(){
				$.ajax({
				  url: './content/pos_ajax.php?execute=setTodefaultPackage&id='+$("tr.selected").find('td:eq(0)').first().text(),
				  async: false,
				  success: function(data) {
					//window.location=document.URL;
					if(data=="success"){
						alert("This package is set to default...");
					}
				  }
				});
			}
			function packageSelected(id){
				var urls = $.param(getUrl());
				$.ajax({
					  url: './content/pos_ajax.php?execute=processPackage&id='+id,
					  async: false,
					  data:urls,
					  success: function(data) {
						$('#prodlist').dialog('close');
						window.location=document.URL;
					  }
					});
				
			}
			// function viewReport(page){
				// if (window.showModalDialog) {
					// window.showModalDialog(page,"Packages","dialogWidth:650px;dialogHeight:650px");
				// } else {
					// window.open(page,"Packages",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
				// }
			// }
		</script>
		<?
	break;
	case'setTodefaultPackage':
		$_SESSION['defaultPackage']=$_REQUEST['id'];
		echo "success";
	break;
	case'processPackage':
		$info = $db->getWHERE("*","tbl_packages","where id='".$_REQUEST['id']."'");
		$_SESSION[$_REQUEST['page']]=unserialize($info['packages']);
		if($_REQUEST['page']=="package_create"){
			$_SESSION[$_REQUEST['page'].'_header'] = array('id'=>$info['id'],'package_name'=>$info['package_name']);
		}
		echo "success";
	break;
	case'lotexpdateSave':
		$sql="insert into tbl_product_lotexp (id,skuid,refid,qty_in,lotno,expdate) values ";
		$flag=false;
		//print_r($_REQUEST['rec']);exit;
		foreach($_REQUEST['rec'] as $key => $val){
			if($flag)$sql.=",";
			$sql.="('".$val['idnum']."','{$_REQUEST['skuid']}','{$_REQUEST['refid']}','{$val['qty_in']}','{$val['lotno']}','{$val['expdate']}')";
			$flag=true;
		}
		$sql .=" on duplicate key update qty_in=values(qty_in),lotno=values(lotno),expdate=values(expdate)";
		$qry =mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "success";
		}
	break;
	case'lotexpdateAdd':
		$sql ="select * from tbl_product_lotexp where skuid='{$_REQUEST['skuid']}' and refid='{$_REQUEST['refid']}' order by expdate asc";
		$qry = mysql_query($sql);
		?>
		<form name="lotexp" id="lotexp">
			<table id="lottbl" class="tbl" cellspacing="0" cellpadding="0" width="100%" >
				<thead>
					<tr>
						<td><input type="button" value="+" onclick="addEntry()" style="height:30px;width:30px;"/></td>
						<td>Lot #</td>
						<td>Exp Date</td>
						<td>QTY In</td>
					</tr>
				</thead>
				<tbody>
			<? $count=1;
			while($row=mysql_fetch_assoc($qry)){	?>
					
					<tr>
						<td>
							<input type="radio"/>
							<input type="hidden" name="rec[<?=$count?>][idnum]" value="<?=$row['id']?>"/>
						</td>
						<td><input type="text" name="rec[<?=$count?>][lotno]" value="<?=$row['lotno']?>" style="width:100%;"/></td>
						<td><input type="text" class="xdatelot" name="rec[<?=$count?>][expdate]" value="<?=$row['expdate']!="0000-00-00"?$row['expdate']:""?>" style="width:100%;"/></td>
						<td><input type="text" name="rec[<?=$count?>][qty_in]" value="<?=$row['qty_in']?>" style="width:100%;"/></td>
					</tr>
			<? $count++;} ?>
				</tbody>
			</table>
			<input type="button" value="Save" onclick="saveLotTbl()" />
		</form>
		<script>
			$(document).ready(function() {
				$('.xdatelot').datepicker({
					inline: true,
					dateFormat:"yy-mm-dd"
				});
				$("#lottbl").bind('keydown',function(e){
					var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
					if(chCode==46){ //pressing delete button
						$('input[type="checkbox"]:checked').closest("tr").remove();
					}
				});
			});
			
			function addEntry(){
				var num =$("#lottbl tbody tr").length;
				var txt = '<tr>\
						<td><input type="radio"/></td>\
						<td><input type="text" name="rec['+num+'][lotno]" value="" style="width:100%;"/></td>\
						<td><input type="text" name="rec['+num+'][expdate]" value="" style="width:100%;"/></td>\
						<td><input type="text" name="rec['+num+'][qty_in]" value="" style="width:100%;"/></td>\
					</tr>';
				$("#lottbl tbody").prepend(txt);
			}
			function saveLotTbl(){
				var formvalue = serializing('#lotexp');
				var skuid = $("#mytbl tr.selected").find("td:eq(7)").html();
				var refid = $("#refid").val();
				if(refid!=''&&skuid!=''){
					$.ajax({
						url: './content/pos_ajax.php?execute=lotexpdateSave&skuid='+skuid+'&refid='+refid,
						type:"POST",
						data:formvalue,
						success:function(data){
							if(data=="success"){
								window.location=document.URL;
							}else{
								alert(data);
							}
						}
					});
				}else{
					alert("Pls save first before inputing Lot # & Exp Date...");
				}
			}
		</script>
		<?
	break;
	case'lotexpdate':
		$sql ="select * from tbl_product_lotexp where skuid='{$_REQUEST['skuid']}' order by expdate asc";
		$qry = mysql_query($sql);
		?>
		<table class="tbl" id="tbllotexp" cellspacing="0" cellpadding="0" width="100%" >
			<tr>
				<td></td>
				<td>Ref Code</td>
				<td>Lot #</td>
				<td>Exp Date</td>
			</tr>
		<? while($row=mysql_fetch_assoc($qry)){	?>
				<tr>
					<td><input type="radio"/></td>
					<td><a href="javascript:itemSelected('<?php echo $row['skuid'] ?>','');" class="activation"><?php echo $row['skuid'] ?></a></td>
					<td><?=$row['lotno']?></td>
					<td><?=$row['expdate']!="0000-00-00"?$row['expdate']:""?></td>
				</tr>
		<? } ?>
		</table>
		<?
	break;
	case'prodlist':
		?>
		<div id="tbl_content" name="tbl_content" style="overflow:auto;height:75%;font-size:15px;">
			<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
				<tr>
					<th style="border:none;">Barcodes</th>
					<th style="border:none;" width="500px">Desc</th>
					<th style="border:none;">Price</th>
					<th style="border:none;">Unit</th>
				</tr>
			</table>
			<div id="loading"></div>
		</div>
		<div style="clear:both;height:5px;"></div>
		<fieldset>
		<legend>Search Product</legend>
		<input onchange="search_prod(this.value);" type="text" id="search_prodname" name="search_prodname" style="float:left;width:100%;"/>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<input id="bt3" class="buthov" type="button" value="Add NewProd" onclick="prodAdd();" style="float:left;height:40px;width:150px;"/>
		<input id="bt5" class="buthov" type="button" value="Refresh Inv" onclick="SendInvToAdmin();" style="float:left;height:40px;width:150px;"/>
		<input id="bt4" class="buthov" type="button" value="Update Items" onclick="updateItems();" style="float:left;height:40px;width:150px;"/>
		<input id="bt7" class="buthov" type="button" value="Free" onclick="freeprod();" style="float:right;height:40px;width:150px;"/>
		<input id="bt6" class="buthov" type="button" value="Lot#/Exp Date" onclick="lotexpdate();" style="float:right;height:40px;width:150px;"/>
		<!--fieldset style="width:250px;float:right;">
			<div style="float:left;margin-right:5px;"><input type="radio" value="False" name="status_hidden" checked /> Not-Hidden </div>
			<div style="float:left;"><input type="radio" value="" name="status_hidden"/> All </div>
		</fieldset-->
		<script>
			var stat = false;
			function search_prod(val){
				if(val.length >= 1){
					$.ajax({
					  url: './content/pos_ajax.php?execute=prodlist_item&search_prodname='+val+'&bcodeid=<?=$_REQUEST['id']?>&location='+getParam('location')+'&page='+getParam('page')+'&status_hidden='+$('input[name=status_hidden]:checked').val(),
					  async: false,
					  beforeSend:function(){$("#loading").show();},
					  success: function(data) {
						$("#tbl_content").html(data);
						jQuery.tableNavigation();
						stat=true;
					  }
					});
				}else{
					alert("Be specific when searching...");
				}
			}
			function freeprod(){
				var skuid = $("#tbl tr.selected").find("td:eq(1)").html()+"|FREE";
				var id='';
				if(stat){
					setValue(skuid,id);
					$('#prodlist').dialog('close');
					stat=false;
				}
			}
			function lotexpdate(){
				var skuid = $("#tbl tr.selected").find("td:eq(1)").html();
				clickDialog("dialogbox2",400,300,"lotexpdate&skuid="+skuid,"Lot # & Exp Date");
				jQuery.tableNavigation();
			}
			
		</script>
		<?
	break;
	case"prodlist_item--":
		$db->openDb("main");
		if($_REQUEST['page']=="stockin"||$_REQUEST['page']=="po"||$_REQUEST['page']=="prod_maintenance"){
			$bal = "";
		}else{
			$bal = " and bal_total>0";
		}
		if($_SESSION['settings']['allow_negative_inv']==true){
			$bal = "";
		}
		if($_SESSION['settings']['system_name']=="TKC"){
			$prodsearch = "prod.product_name like '%".$_REQUEST['search_prodname']."%' or prod.brand like '%".$_REQUEST['search_prodname']."%' or prod.inventory_code like '%".$_REQUEST['search_prodname']."%' 
				or prod.location like '%".$_REQUEST['search_prodname']."%' or prod.class like '%".$_REQUEST['search_prodname']."%' or prod.parts_number like '%".$_REQUEST['search_prodname']."%'";
		}else{
			$prodsearch = "prod.product_name like '%".$_REQUEST['search_prodname']."%' or prod.sku_id like '%".$_REQUEST['search_prodname']."%'";
		}
		// $sql="select prod.*,bcode.barcode,bcode.price,bcode.cost,bcode.unit,cat.category_name,tbl_2.* from 
			// tbl_product_name prod 
			// left join 
			// (select skuid,sum(in_total) in_total,sum(out_total) out_total,sum(in_total-out_total) bal_total from 
				// (select skuid,coalesce(sum(qty*coalesce(divmul,1)),0) in_total,0 out_total from tbl_stockin_items group by skuid
					// union
				// select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_sales_items group by skuid
					// union
				// select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_stockout_items group by skuid
				// ) tbl_1 
			// group by skuid) tbl_2 on prod.sku_id=tbl_2.skuid 
			// left join tbl_barcodes bcode on prod.sku_id=bcode.sku_id 
			// left join tbl_category cat on prod.category_id=cat.category_id 
			// where $prodsearch $bal order by prod.product_name asc";
		$sql="select prod.*,bcode.barcode,bcode.price,bcode.cost,bcode.unit,cat.category_name,tbl_2.* from 
			tbl_product_name prod 
			left join 
			(select skuid,sum(in_total) in_total,sum(out_total) out_total,sum(in_total-out_total) bal_total from 
				(select skuid,coalesce(sum(qty*coalesce(divmul,1)),0) in_total,0 out_total from tbl_stockin_items group by skuid
					union
				select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_sales_items group by skuid
					union
				";
		switch($_SESSION['settings']['system_name']){
			case"TKC":
			case"RTK":
				$sql.=" select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_sales_invoice_items group by skuid
						union ";
			break;
		}
		$sql.="select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_stockout_items group by skuid
				) tbl_1 
			group by skuid) tbl_2 on prod.sku_id=tbl_2.skuid 
			left join tbl_barcodes bcode on prod.sku_id=bcode.sku_id 
			left join tbl_category cat on prod.category_id=cat.category_id 
			where $prodsearch $bal order by prod.product_name asc";
		$res = $con->resultArray($con->Nection()->query($sql));
		?>
		<table class="navigateable tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcodes</th>
					<th style="display:none;">SKU</th>
					<th>Desc</th>
					<?php if($_SESSION['settings']['system_name']=="TKC"){ ?>
					<th>Brand</th>
					<th>Inventory Code</th>
					<th>Location</th>
					<th>Class</th>
					<th>Parts Number</th>
					<?php } ?>
					<th>Price</th>
					<th style="display:none;">Cost</th>
					<th>Unit</th>
					<th>Stock OnHand</th>
					<th>Category</th>
				</tr>
			</thead>
			<tbody>
				<? 	
				if(!$res){
					echo "<tr>
							<td colspan='7' align='center'>No Records Found...</td>
						</tr>";
				}else{
				foreach($res as $key => $row){ 
				?>
					<tr style="color:<?=($row['bal_total']>0?"blue;":"black;")?>">
						<td><a href="javascript:itemSelected('<?php echo $row['barcode'] ?>','<?=$_REQUEST['bcodeid']?>');" class="activation"><?php echo $row['barcode'] ?></a></td>
						<td style="display:none;"><?= $row['sku_id']?></td>
						<td><?= $row['product_name']?></td>
						<?php if($_SESSION['settings']['system_name']=="TKC"){ ?>
						<td><?= $row['brand']?></td>
						<td><?= $row['inventory_code']?></td>
						<td><?= $row['location']?></td>
						<td><?= $row['class']?></td>
						<td><?= $row['parts_number']?></td>
						<?php } ?>
						<td><?= number_format($row['price'],2) ?></td>
						<td style="display:none;"><?= number_format($row['cost'],2) ?></td>
						<td><?= $row['unit'] ?></td>
						<td style="text-align:center;"><?= $row['bal_total'] ?></td>
						<td><?= $row['category_name'] ?></td>
					</tr>
				<? $count++;}
				}?>
			</tbody>
		</table>
		<script>
			function itemSelected(barcode,id){
				if(stat){
					setValue(barcode,id);
					$('#prodlist').dialog('close');
					stat=false;
				}
				//barcode_area(barcode,id); //process barcodes
			}
		</script>
		<?
	break;
	case"prodlist_item":
		$db->openDb("main");
		if($_REQUEST['page']=="stockin"||$_REQUEST['page']=="po"||$_REQUEST['page']=="prod_maintenance"){
			$bal = "";
		}else{
			$bal = " and bal_total>0";
		}
		if($_SESSION['settings']['allow_negative_inv']==true){
			$bal = "";
		}
		if($_SESSION['settings']['system_name']=="TKC"){
			$prodsearch = "prod.product_name like '%".$_REQUEST['search_prodname']."%' or prod.brand like '%".$_REQUEST['search_prodname']."%' or prod.inventory_code like '%".$_REQUEST['search_prodname']."%' 
				or prod.location like '%".$_REQUEST['search_prodname']."%' or prod.class like '%".$_REQUEST['search_prodname']."%' or prod.parts_number like '%".$_REQUEST['search_prodname']."%'";
		}else{
			$prodsearch = "prod.product_name like '%".$_REQUEST['search_prodname']."%' or prod.sku_id like '%".$_REQUEST['search_prodname']."%'";
			$prodsearch2 = "where skuid in (select sku_id from tbl_product_name prod where product_name like '%".$_REQUEST['search_prodname']."%' or prod.sku_id like '%".$_REQUEST['search_prodname']."%')";
		}
		$sql="select prod.*,bcode.barcode,bcode.price,bcode.cost,bcode.unit,cat.category_name from 
			tbl_product_name prod 
			left join tbl_barcodes bcode on prod.sku_id=bcode.sku_id 
			left join tbl_category cat on prod.category_id=cat.category_id 
			where $prodsearch order by prod.product_name asc";
		$res = $con->resultArray($con->Nection()->query($sql));
		
		$onhand_sql = "select skuid,sum(in_total) in_total,sum(out_total) out_total,sum(in_total-out_total) bal_total from 
				(select skuid,coalesce(sum(qty*coalesce(divmul,1)),0) in_total,0 out_total from tbl_stockin_items $prodsearch2 group by skuid
					union
				select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_sales_items $prodsearch2 group by skuid
					union
				select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_sales_invoice_items $prodsearch2 group by skuid
						union
				select skuid,0 in_total,coalesce(sum(qty*coalesce(divmul,1)),0) out_total from tbl_stockout_items $prodsearch2 group by skuid
					) tbl_1 group by skuid";
		if(isset($_SESSION['connect'])){
			$onhand = $con->pdoStyle($_SESSION['conlist'][$_SESSION['connect']]['ipaddress'],$_SESSION['conlist'][$_SESSION['connect']]['db_name'],$onhand_sql);
		}else{
			$onhand = $con->pdoStyle($_SESSION['conlist']['main']['ipaddress'],$_SESSION['conlist']['main']['db_name'],$onhand_sql);
		}
		
		foreach($onhand as $k => $val){
			$bal_total[$val['skuid']]=$val['bal_total'];
		}
		?>
		<table class="navigateable tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcodes</th>
					<th style="display:none;">SKU</th>
					<th>Desc</th>
					<?php if($_SESSION['settings']['system_name']=="TKC"){ ?>
					<th>Brand</th>
					<th>Inventory Code</th>
					<th>Location</th>
					<th>Class</th>
					<th>Parts Number</th>
					<?php } ?>
					<th>Price</th>
					<th style="display:none;">Cost</th>
					<th>Unit</th>
					<th>Stock OnHand</th>
					<th>Category</th>
				</tr>
			</thead>
			<tbody>
				<? 	
				if(!$res){
					echo "<tr>
							<td colspan='7' align='center'>No Records Found...</td>
						</tr>";
				}else{
				foreach($res as $key => $row){ 
				?>
					<tr style="color:<?=($row['bal_total']>0?"blue;":"black;")?>">
						<td><a href="javascript:itemSelected('<?php echo $row['barcode'] ?>','<?=$_REQUEST['bcodeid']?>');" class="activation"><?php echo $row['barcode'] ?></a></td>
						<td style="display:none;"><?= $row['sku_id']?></td>
						<td><?= $row['product_name']?></td>
						<?php if($_SESSION['settings']['system_name']=="TKC"){ ?>
						<td><?= $row['brand']?></td>
						<td><?= $row['inventory_code']?></td>
						<td><?= $row['location']?></td>
						<td><?= $row['class']?></td>
						<td><?= $row['parts_number']?></td>
						<?php } ?>
						<td><?= number_format($row['price'],2) ?></td>
						<td style="display:none;"><?= number_format($row['cost'],2) ?></td>
						<td><?= $row['unit'] ?></td>
						<td style="text-align:center;"><?= $bal_total[$row['sku_id']] ?></td>
						<td><?= $row['category_name'] ?></td>
					</tr>
				<? $count++;}
				}?>
			</tbody>
		</table>
		<script>
			function itemSelected(barcode,id){
				if(stat){
					setValue(barcode,id);
					$('#prodlist').dialog('close');
					stat=false;
				}
				//barcode_area(barcode,id); //process barcodes
			}
		</script>
		<?
	break;
	case"prodlist_item_old":
		$hidden = ($_REQUEST['status_hidden']=="False"?" and (a.`hidden` is null or a.`hidden`='')":"");
		//$hidden = "";
		$lokasion = $_REQUEST['location']?$_REQUEST['location']:"tbl";
		$catqry="";
		if($_SESSION['category']['catid']){
			$catqry = "and a.category_id={$_SESSION['category']['catid']}";
		}
		if($_REQUEST['search_prodname']!=" "&&$_REQUEST['search_prodname']!=""){
			if($search_list_show_all_uom){
				$uom_show = "";
			}else{
				$uom_show = "where divmul=1";
			}
			if($show_zero_inv==false && $_REQUEST['page']!="stockin" && $_REQUEST['page']!="prod_maintenance" && $_REQUEST['page']!="prod_maintenance_whole"){
				$inv=" and a.base_inv > 0";
			}else{
				$inv="";
			}
			$sql = "select c.category_name,a.base_inv,a.sku_id,cost,product_name,barcode,price,unit,concat(base_inv,' ',base_unit) as stockonhand from {$lokasion}_product_name as a
				left join (select * from {$lokasion}_barcodes $uom_show) as b on a.sku_id=b.sku_id 
				left join tbl_category c on a.category_id=c.category_id 
				where (a.product_name like '%".$_REQUEST['search_prodname']."%' or b.barcode like '%".$_REQUEST['search_prodname']."%' or a.sku_id like '%".$_REQUEST['search_prodname']."%') $catqry $inv $hidden 
				order by base_inv desc,product_name asc";
			//(select * from tbl_barcodes where divmul=1)
			$qry = mysql_query($sql);
			if(!$qry){
				echo mysql_error();
			}
		}
		$qry = mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}
		?>
		<table class="navigateable tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
			<thead>
				<tr>
					<th>Barcodes</th>
					<th style="display:none;">SKU</th>
					<th>Desc</th>
					<th>Price</th>
					<th style="display:none;">Cost</th>
					<th>Unit</th>
					<th>Stock OnHand</th>
					<th>Category</th>
				</tr>
			</thead>
			<tbody>
				<? 	
				if(mysql_num_rows($qry)==0){
					echo "<tr>
							<td colspan='7' align='center'>No Records Found...</td>
						</tr>";
				}
				while($row = mysql_fetch_assoc($qry)){ 
					$xinv = $db->invBal($row['sku_id'],date('Y-m-d'));
					//$xinv = $row['base_inv'];
				?>
					<tr style="color:<?=($xinv>0?"blue;":"black;")?>">
						<td><a href="javascript:itemSelected('<?php echo $row['sku_id'] ?>','<?=$_REQUEST['bcodeid']?>')" class="activation"><?php echo $row['sku_id'] ?></a></td>
						<td style="display:none;"><?= $row['sku_id']?></td>
						<td><?= $row['product_name']?></td>
						<td><?= number_format($row['price'],2) ?></td>
						<td style="display:none;"><?= number_format($row['cost'],2) ?></td>
						<td><?= $row['unit'] ?></td>
						<td style="text-align:center;"><?= $db->outputInvBal($xinv,$row['sku_id'])//$db->outputInvBal($row['base_inv'],$row['sku_id']) //$db->outputInvBal($db->invBal($row['sku_id'],date('Y-m-d')),$row['sku_id']) ?></td>
						<td><?= $row['category_name'] ?></td>
					</tr>
				<? $count++;} ?>
			</tbody>
		</table>
		<script>
			function itemSelected(barcode,id){
				setValue(barcode,id);
				
				//barcode_area(barcode,id); //process barcodes
				$('#prodlist').dialog('close');
			}
		</script>
		<?
	break;
	//require_once"pos_ajax_stockin.php";
}
$db->closeDb();
?>
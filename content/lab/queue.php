<?php
// echo $_SESSION['connect'];
// print_r($_SESSION['conlist']);

if($_REQUEST['execute']){
	// ini_set('display_errors', 1);
	// ini_set('log_errors', 1);
	// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
	// error_reporting(E_ALL);
	session_start();
	require_once"../../settings.php";
	require_once"../../class/dbConnection.php";
	require_once"../../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		case'updateProcAll':
			//echo "*","tbl_sales_items","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}'";
			$rec = $db->resultArray("*","tbl_sales_items","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}'");
			foreach($rec as $key => $val){
				$data=array(
					'receipt'=>$_REQUEST['receipt'],
					'counter'=>$_REQUEST['counter'],
					'reading'=>$_REQUEST['reading'],
					'skuid'=>$val['skuid'],
					'category_id'=>$val['category_id'],
					'datetime'=>date('Y-m-d H:m'),
					'medical_tech'=>$_SESSION['complete_name'],
					'status'=>'Done'
				);
				$sql = $db->genSqlInsert($data,'lab_procedure_status');
				$qry=mysql_query($sql);
				if(!$qry){
					echo "Error: [$sql] ".mysql_error();
				}else{
					echo "success";
				}
			}
			
			
		break;
	}
}else{
	if($_POST){
		
	}
mysql_query("SET SESSION group_concat_max_len = 9999999999999999999999");

switch($_REQUEST['queuetype']){
	case'printing':
		$type = " 
			group_concat(
				case 
				WHEN item_desc='Physical Examination' THEN
					concat('<td>',category_name,'</td><td>',res.report_title,'</td><td>',item_desc,'</td><td>
					<a target=\"_blank\" href=\"content/lab/reports/physical_exam.php?catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/print.png\" title=\"Print Results\" style=\"width:25px;height:25px;\"/></a>
					<a href=\"?page=dynamic_lab&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/cashdetails.png\" title=\"Edit Results\" style=\"width:25px;height:25px;\"/></a>
					</td>')
				WHEN res.report_title='Physical Exam' THEN 
					concat('<td>',category_name,'</td><td>',res.report_title,'</td><td>',item_desc,'</td><td>
					<a target=\"_blank\" href=\"content/lab/reports/physical_exam.php?catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/print.png\" title=\"Print Results\" style=\"width:25px;height:25px;\"/></a>
					<a href=\"?page=dynamic_lab&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/cashdetails.png\" title=\"Edit Results\" style=\"width:25px;height:25px;\"/></a>
					</td>') 
				WHEN res.report_title='X-Ray' THEN 
					concat('<td>',category_name,'</td><td>',res.report_title,'</td><td>',item_desc,'</td><td>
					<a target=\"_blank\" href=\"content/lab/reports/xray_results.php?catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/print.png\" title=\"Print Results\" style=\"width:25px;height:25px;\"/></a>
					<a href=\"?page=dynamic_lab&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/cashdetails.png\" title=\"Edit Results\" style=\"width:25px;height:25px;\"/></a>
					</td>') 
				ELSE 
					concat('<td>',category_name,'</td><td>',res.report_title,'</td><td>',item_desc,'</td><td>
					<a target=\"_blank\" href=\"content/lab/reports/lab_results.php?catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/print.png\" title=\"Print Results\" style=\"width:25px;height:25px;\"/></a>
					<a href=\"?page=dynamic_lab&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\"><img src=\"./images/cashdetails.png\" title=\"Edit Results\" style=\"width:25px;height:25px;\"/></a>
					</td>') 
				END 
			separator '|') as dept_exam 
		 ";
		 
		 //$type="group_concat( case WHEN res.report_title='Physical Exam' THEN '<td>PE</td>' ELSE '<td>NOT-PE END</td>' END separator '|') as dept_exam";
		 
		$join = " left join lab_results res on a.receipt=res.receipt and a.counter=res.counter and a.reading=res.reading and a.skuid=res.skuid and a.category_id=res.category_id 
				left join lab_procedure_status proc on a.receipt=proc.receipt and a.counter=proc.counter and a.reading=proc.reading and a.skuid=proc.skuid and a.category_id=proc.category_id ";
		$where = " where res.lab_results is not null and proc.status = 'Done'";
	break;
	case'results':
		$type = " group_concat('<td>',category_name,'</td><td></td><td>',item_desc,'</td><td>
			<a href=\"javascript:updateProc(\'?page=dynamic_lab&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\');\"><img src=\"./images/discount.png\" title=\"Input\" style=\"width:25px;height:25px;\"/></a>
			<a href=\"javascript:updateProc(\'?page=procedure_status&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\');\"><img src=\"./images/cashdetails.png\" title=\"Edit\" style=\"width:25px;height:25px;\"/></a>
		</td>' separator '|') dept_exam ";
		
		$join = " left join lab_results res on a.receipt=res.receipt and a.counter=res.counter and a.reading=res.reading and a.skuid=res.skuid and a.category_id=res.category_id 
				left join lab_procedure_status proc on a.receipt=proc.receipt and a.counter=proc.counter and a.reading=proc.reading and a.skuid=proc.skuid and a.category_id=proc.category_id 
				";
		$where = " where res.lab_results is null and proc.status = 'Done'";
	break;
	default:
		$type = "group_concat('<td>',category_name,'</td><td></td><td>',item_desc,'</td>
		<td>
			<a href=\"javascript:updateProc(\'?page=procedure_status&catid=',a.category_id,'&skuid=',a.skuid,'&idno=',b.studentid,'&datainfo=',b.tblsource,'&receipt=',a.receipt,'&counter=',a.counter,'&reading=',a.reading,'\');\"><img src=\"./images/touch.png\" title=\"Update Status\" style=\"width:25px;height:25px;\"/></a></td>' separator '|') dept_exam";
		$join = " left join lab_procedure_status res on a.receipt=res.receipt and a.counter=res.counter and a.reading=res.reading and a.skuid=res.skuid and a.category_id=res.category_id ";
		$where = " where res.status is null"; // or res.status !='Done'
	break;
}
$txtsearch = $_REQUEST['txtsearch']?"where receipt in (select receipt_id from tbl_sales_receipt_1 where studentname like '%{$_REQUEST['txtsearch']}%') ":"";
$where2 = $_SESSION['records_filter']['company_name']?" and otherinfo.company_name='{$_SESSION['records_filter']['company_name']}' and otherinfo.data_reference='{$_SESSION['records_filter']['data_reference']}'":"";
$sql ="select a.*,count(*) num,b.studentname,b.studentid,b.tblsource,c.category_name,
	$type 
	from (select * from tbl_sales_items $txtsearch) a 
	left join tbl_sales_receipt_1 b on a.receipt=b.receipt_id and a.counter=b.counter_num and a.reading=b.reading 
	left join tbl_category c on a.category_id=c.category_id 
	
	left join data_teletech_patient otherinfo on otherinfo.id=b.studentid 
	
	$join 
	$where $where2 
	group by receipt,counter,reading order by receipt ASC limit 200";

$qry=mysql_query($sql);
if(!$qry){
	echo mysql_error();
}

$complist = $db->resultArray("distinct company_name","data_teletech_patient","where year=year(now())");
$dataref = $db->resultArray("distinct data_reference","data_teletech_patient","where year=year(now())");
?>
<style type="text/css">
	table.tbl,table.tbl2 {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		
	}
	table.tbl th,table.tbl2 th {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		height:20px;
		text-align:center;
	}
	table.tbl td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
	}
	table.tbl2 td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		text-align:center;
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
</style>
<div class="content" style="min-height:300px;width:100%!important;">
	<h2>Queue <?=$_REQUEST['queuetype']?strtoupper($_REQUEST['queuetype']):"PROCEDURE"?></h2>
	<form name="frm" method="post" >
		<fieldset>
		<legend>Search</legend>
		<input type="text" name="txtsearch" style="float:left;width:200px;margin-top:10px;"/>
		
		
		<fieldset style="width:60%;float:left;margin-left:10px;">
			<legend>Records Filter</legend>
			<div style="width:100px;float:left;">Company Name</div>
			<select id="company_name" style="float:left;width:150px;">
				<option value="">Select</option>
				<?php foreach($complist as $k => $v){ ?>
				<option <?= $_SESSION['records_filter']['company_name']==$v['company_name']?"selected":""?> value="<?=$v['company_name']?>"><?=$v['company_name']?></option>
				<?php } ?>
			</select>
			<div style="width:100px;float:left;">Data Reference</div>
			<select id="data_reference" style="float:left;width:150px;">
				<option value="">Select</option>
				<?php foreach($dataref as $k => $v){ ?>
				<option <?= $_SESSION['records_filter']['data_reference']==$v['data_reference']?"selected":""?> value="<?=$v['data_reference']?>"><?=$v['data_reference']?></option>
				<?php } ?>
			</select>
		</fieldset>
		
		<input type="submit" value="Search" style="float:right;width:150px;margin-top:10px;"/>
		</fieldset>
	</form>
	<div style="clear:both;height:10px;"></div>
	<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th>Ref #</th>
			<th>Patient Name</th>
			<th>Company</th>
			<th>Department</th>
			<th>Lab Form</th>
			<th>Lab Exam</th>
			<th>Menu</th>
		</tr>
		<? while($row = mysql_fetch_assoc($qry)){ 
		// echo "<pre>";
		// print_r($row);
		// echo "</pre>";
		$exam = explode("|",$row['dept_exam']);
		if($row['tblsource']=='data_teletech_patient'){
			$otherinfo = $db->getWHERE("*","data_teletech_patient","where id='{$row['studentid']}'");
		}else{
			$otherinfo = $db->getWHERE("*","data_walkin_patient","where idno='{$row['studentid']}'");
		}
		?>
		<tr>
			<td rowspan="<?=$row['num']?>">
				<?php
					if($_REQUEST['queuetype']=="printing"){
						echo "(".$row['receipt'].") ";
						echo '<a target="_blank" href="content/lab/reports/lab_results.php?receipt='.$row['receipt'].'&counter='.$row['counter'].'&reading='.$row['reading'].'">Print All</a>';
					}elseif(!isset($_REQUEST['queuetype'])){
						echo "(".$row['receipt'].") ";
						echo '<a target="_blank" href="javascript:updateProcAll('.$row['receipt'].','.$row['counter'].','.$row['reading'].')">Update All</a>';
					}else{
						echo $row['receipt'];
					}
				?>
			</td>
			<td rowspan="<?=$row['num']?>"><?=$row['studentname']==""?$otherinfo['first_name']." ".$otherinfo['last_name']:$row['studentname']?></td>
			<td rowspan="<?=$row['num']?>"><?=$otherinfo['company_name']."/".$otherinfo['data_reference']?></td>
			
			<?php
			echo $exam[0];
			if(is_array($exam)){
				for($x=1;$x<count($exam);$x++){
					echo "<tr>{$exam[$x]}</tr>";
				}
			}
			?>
		</tr>
		<? } ?>
	</table>
</div>
<script>
     var time = new Date().getTime();
     $(document.body).bind("mousemove keypress", function(e) {
         time = new Date().getTime();
     });

     function refresh() {
         if(new Date().getTime() - time >= 60000) 
             window.location.reload(true);
         else 
             setTimeout(refresh, 10000);
     }

     setTimeout(refresh, 10000);
</script>
<script>

$(document).ready(function() {
	
});
function viewReport(page){
	var win=window.open(page,'_blank');
	win.focus();
}
function updateProc(page){
	if (window.showModalDialog) {
		window.showModalDialog(page+'&txtsearch=<?=$_REQUEST['txtsearch']?>',"Update","dialogWidth:1050px;dialogHeight:650px");
	} else {
		window.open(page+'&txtsearch=<?=$_REQUEST['txtsearch']?>',"Update",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
$("#company_name").change(function(){
	var val = $(this).val();
	$.ajax({
		url: './content/lab/teletech_input.php?execute=toSession&company_name='+val,
		type:"POST",
		success:function(data){
			//window.location=removeURLParameter(document.URL, 'refid');
			$("input[name='data[company_name]']").val(val);
		}
	});
});
function updateProcAll(receipt,counter,reading){
	var r=confirm("Are you sure you want to update all?");
	if (r==true){
		$.ajax({
			url: './content/lab/queue.php?execute=updateProcAll',
			data:{receipt:receipt,counter:counter,reading:reading},
			type:"POST",
			success:function(data){
				window.location=document.URL;
			}
		});
	}
	
}
$("#data_reference").change(function(){
	var val = $(this).val();
	$.ajax({
		url: './content/lab/teletech_input.php?execute=toSession&data_reference='+val,
		type:"POST",
		success:function(data){
			//window.location=removeURLParameter(document.URL, 'refid');
			$("input[name='data[data_reference]']").val(val);
		}
	});
});
</script>
<?php } ?>
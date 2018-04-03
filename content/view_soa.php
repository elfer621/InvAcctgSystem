<?php
$month = array('01'=>'(01) January','02'=>'(02) February','03'=>'(03) March','04'=>'(04) April','05'=>'(05) May',
	'06'=>'(06) June','07'=>'(07) July','08'=>'(08) August','09'=>'(09) September','10'=>'(10) October','11'=>'(11) November','12'=>'(12) December');
$year = $_REQUEST['year']?$_REQUEST['year']:date('Y');
$m = $_REQUEST['month']?$_REQUEST['month']:$db->customeFormat(((double)date('m')-1),2);
$sql = "select a.*,b.customer_name from tbl_soa a left join tbl_customers b on a.cust_id=b.cust_id 
	where a.year='$year' and a.month='$m' 
	order by year desc,month desc";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<div style="height:88%;overflow:auto;background-color:white;">
	<table class="navigateable" id="mytbl" style="width:100%;">
		<thead>
			<tr>
				<th>Billing Statement</th>
				<th>Customer Name</th>
				<th>Amount</th>
				<th>Due Date</th>
				<th>Menu</th>
			</tr>
		</thead>
		<tbody>
			<? 	while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td><a href="javascript:transferStockin('<?php echo $row['id'] ?>')" class="activation"><?php echo $row['year']."-".$db->customeFormat($row['month'],2) ?></a></td>
				<td><?= $row['customer_name']?></td>
				<td><?= number_format($row['amount'],2)?></td>
				<td><?= $row['due_date']?></td>
				<td>
					<img src="./images/print.png" style="width:20px;height:20px;float:left;" onclick="viewReport('./reports/soa.php?refid=<?php echo $row['id'] ?>')"/>
				</td>
			</tr>
			<? } ?>
		</tbody>
	</table>
</div>
<div style="clear:both;height:5px;"></div>
<fieldset>
	<legend>Statement for the Month Of</legend>
	<input type="text" name="syear" id="syear" value="<?=date('Y')?>" style="float:left;margin-right:5px;width:80px;"/>
	<select name="smonth" id="smonth" style="float:left;width:180px;">
	<?php
	foreach($month as $key=>$val){
		echo "<option ".($m==$key?"selected":"")." value='{$key}'>{$val}</option>";
	}
	?>
	</select>
	<input type="button" value="Show" onclick="searchSOA()" style="width:130px;float:right;"/>
</fieldset>
<script>
	$(document).ready(function(){
		jQuery.tableNavigation();
		
	});
	function searchSOA(){
		var year="&year="+$("#syear").val();
		var month ="&month="+$("#smonth").val();
		clickDialog('prodlist',1000,500,'view_listing'+year+month,'View Records',null,'soa_ajax.php');
		jQuery.tableNavigation();
	}
	function transferStockin(val){
		var urls = getUrl();
		$.ajax({
			url: './content/soa_ajax.php?execute=viewSOA&refid='+val,
			data:urls,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location.href="?page=soa_create";
				}
			}
		});
	}
	function loadTrans(type){
		$.ajax({
			url: './content/vouchering_ajax.php?execute=loadTrans&type='+type,
			type:"POST",
			success:function(data){
				$("#tabs-1").html("");
				$("#tabs-2").html("");
				$("#tabs-3").html("");
				switch(type){
					case'ForApproval':
						$("#tabs-1").html(data);
					break;
					case'Approved':
						$("#tabs-2").html(data);
					break;
					case'Rejected':
						$("#tabs-3").html(data);
					break;
				}
				jQuery.tableNavigation();
			}
		});
	}
	function delStockin(val){
		var r = confirm("Are you sure you want to delete this?");
		if (r == true){
			$.ajax({
				url: 'pos_ajax_stockin.php?execute=delStockin&refid='+val,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	}
	function viewReport(page){
		if (window.showModalDialog) {
			window.showModalDialog(page,"PO","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open(page,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
	function printPreview(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
	function printPreview_withForm(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin_withform.php?refid='+refid,"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin_withform.php?refid='+refid,"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
</script>


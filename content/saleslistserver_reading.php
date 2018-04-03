<?php
$tbl = $_REQUEST['type']?$_REQUEST['type']:"tbl";

$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');
$sql = "select * from tbl_reading_server where start_date between '$begdate' and '$enddate' order by reading_num desc";
$qry = mysql_query($sql);
echo mysql_error();

?>
<div style="clear:both;height:10px;"></div>
<form name="frm_cust" method="post">
	<div style="float:left;margin-right:30px;">Beg Date</div>
	<input style="float:left;margin-right:50px;width:100px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
	<div style="float:left;margin-right:30px;">End Date</div>
	<input style="float:left;width:100px;margin-right:30px;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
	<input type="submit" value="Search" name="search_date"/>
</form>
<div style="clear:both;height:10px;"></div>
<div style="height:300px;overflow:auto;">
	<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%" >
		<tr>
			<th style="border:none;">Menu</th>
			<th style="border:none;">Reading Num</th>
			<th style="border:none;">DateStart</th>
			<th style="border:none;">TimeStart</th>
			<th style="border:none;">DateEnd</th>
			<th style="border:none;">TimeEnd</th>
			<th style="border:none;">Amount</th>
			<th style="border:none;">Menu</th>
		</tr>
		<?php while($row = mysql_fetch_assoc($qry)){ ?>
			<tr>
				<td class="data_lightgray"><a href="javascript:showReadingList(<?=$row['reading_num']?>)">Show</a></td>
				<td class="data_lightgray"><a href="javascript:viewReport('reports/<?=$_SESSION['reports']['XReading']['report_link']?>?readingnum=<?=$row['reading_num']?>&all=true')"><?=$row['reading_num']?></a></td>
				<td class="data_lightgray"><?=$row['start_date']?></td>
				<td class="data_lightgray"><?=$row['start_time']?></td>
				<td class="data_lightgray"><?=$row['end_date']?></td>
				<td class="data_lightgray"><?=$row['end_time']?></td>
				<td class="data_lightgray"><?=number_format($row['total_amount'],2)?></td>
				<?php if($row['end_date']=="0000-00-00"){ ?>
					<td class="data_lightgray"><a href="javascript:zEndReading(<?=$row['reading_num']?>)">EndReading</a></td>
				<?php }else{ ?>
					<td class="data_lightgray"></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</table>
</div>
<div style="clear:both;height:10px;"></div>
<div id="display_reading" style="height:180px;overflow:auto;"></div>

<script>
	//var rep = './reports/reading_end_reprint.php?readingnum=';
	var rep = '<?=$_SESSION['reports']['XReading']['report_link']?$_SESSION['reports']['XReading']['report_link']:'./reports/reading_end.php?readingnum='?>';
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
	function zEndReading(reading_num){
		var r=confirm("Are you sure you want to EndReading?");
		if (r==true){
			$.ajax({
				url: './content/pos_ajax.php?execute=zEndReading&reading_num='+reading_num,
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
	function showReadingList(reading_num){
		
		$.ajax({
			url: './content/pos_ajax.php?execute=showReadingList&reading_num='+reading_num,
			type:"POST",
			success:function(data){
				$("#display_reading").html(data);
			}
		});
	}
	function reprintReading(readingnum,counter_num){
		if (window.showModalDialog) {
			window.showModalDialog(rep+readingnum+'&counter_num='+counter_num,"Reading Reprint","dialogWidth:500px;dialogHeight:600px");
		} else {
			window.open(rep+readingnum+'&counter_num='+counter_num,"Reading Reprint",'height=600,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
		}
	
	}
	function viewReport(page){
		var win=window.open(page,'_blank');
		win.focus();
	}
</script>
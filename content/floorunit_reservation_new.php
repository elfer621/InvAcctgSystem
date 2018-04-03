<?php
$begdate=$_REQUEST['xbegdate']?$_REQUEST['xbegdate']:date('Y-m-d');
$enddate=$_REQUEST['xenddate']?$_REQUEST['xenddate']:date('Y-m-d',strtotime(date('m/d/Y')."+10 days"));
$where = "where date between '$begdate' and '$enddate'";
// $sql="select a.*,b.reserved_dates,b.header_ref from tbl_floor_mapping a 
	// left join (select floor,unit,group_concat(date) as reserved_dates,header_ref from tbl_floorunit_reservation $where group by floor,unit) b on a.floor=b.floor and a.unit=b.unit";
$sql="select a.* from rooms a left join reservation b on a.id=b.room_id";
$qry=mysql_query($sql);
function reservedDate($start,$end,$beg,$end,$header_ref){
	//$dates = explode(",",$array);
	$dates = date_range($start,$end);
	$td='';
	for($x=date('Y-m-d',strtotime($beg));$x<=date('Y-m-d',strtotime($end));$x=date('Y-m-d',strtotime($x."+1 days"))){
		if(in_array($x,$dates)){
			$td.= "<td onclick='checkin(".$header_ref.")' style='background-color:red;'>&nbsp;</td>";
		}else{
			$td.= "<td>&nbsp;</td>";
		}
	}
	return $td;
}
function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}
?>
<h1>Room Reservation</h1>
<div style="float:right;padding:5px;">
	<div style="width:15px;height:15px;background-color:red;float:left;border:1px solid gray;"></div>
	<div style="width:100px;float:left;">Occupied</div>
	<div style="width:15px;height:15px;background-color:white;float:left;border:1px solid gray;"></div>
	<div style="width:100px;float:left;">Vacant</div>
	<div style="width:15px;height:15px;background-color:green;float:left;border:1px solid gray;"></div>
	<div style="width:100px;float:left;">Reserved</div>
	<div style="width:15px;height:15px;background-color:yellow;float:left;border:1px solid gray;"></div>
	<div style="width:100px;float:left;">OnlineBooking</div>
</div>
<table class="tbl" border="1" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<th>Type</th>
		<th>Room</th>
		<?php for($x=date('Y-m-d',strtotime($begdate));$x<=date('Y-m-d',strtotime($enddate));$x=date('Y-m-d',strtotime($x."+1 days"))){ ?>
		<th><?=$x?></th>
		<?php } ?>
	</tr>
<?php
while($row=mysql_fetch_assoc($qry)){
	echo "<tr>
		<td>{$row['type']}</td>
		<td>{$row['unit']}</td>
		".reservedDate($row['reserved_dates'],$begdate,$enddate,$row['header_ref'])."
	</tr>";
}
?>
</table>
<fieldset style="position:absolute;bottom:5px;">
	<legend>Menu</legend>
	<input onclick="checkin()" id="bt2" class="buthov" type="button" value="Guest CheckIn" style="float:left;height:40px;width:160px;" />
	<input onclick="checkin()" id="bt3" class="buthov" type="button" value="Guest CheckOut" style="float:right;height:40px;width:160px;" />
</fieldset>
<div id="reservationDialog"></div>
<div id="paymentDialog"></div>
<script>
function hotelDialog(id,xwidth,xheight,xtitle,contentpath){
	$('#'+id).dialog({
		autoOpen: false,
		width: xwidth,
		height: xheight,
		modal: true,
		close:function(event){
			//window.location="?page=floorunit_reservation";
		},
		resizable: false,
		title:xtitle
	});
	htmlobj=$.ajax({url:'./content/floorunit_reservation_ajax.php?execute='+contentpath,async:false});
	
	$('#'+id).html(htmlobj.responseText);
	$('#'+id).dialog('open');
}
function checkin(refid){
	hotelDialog("reservationDialog",1100,600,"Check-In","checkin&refid="+refid);
}

</script>
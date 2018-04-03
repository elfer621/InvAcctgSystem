<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
switch($_REQUEST['type']){
	case 'graph':
		$begdate = $_REQUEST['startdate'];$enddate=$_REQUEST['enddate'];
		include_once"reports_query.php";
		$qry = mysql_query($sql_graph);
		$content="[";
		$flag=false;
		while($row = mysql_fetch_assoc($qry)){
			$date = (strtotime($row['xdate']) * 1000);
			if($flag)$content.=",";
			$content.='['.$date.','.$row['rec1'].']';
			$flag=true;
		}
		$content.="]";
		header("Content-type: text/json");
		echo $content;
	break;
	case 'graph_monthly':
		$begdate = $_REQUEST['startdate'];$enddate=$_REQUEST['enddate'];
		include_once"reports_query.php";
		$qry = mysql_query($sql_graph);
		$content="[";
		$flag=false;
		while($row = mysql_fetch_assoc($qry)){
			$date = (strtotime($row['xdate']) * 1000);
			if($flag)$content.=",";
			$content.='['.$date.','.$row['rec1'].']';
			$flag=true;
		}
		$content.="]";
		header("Content-type: text/json");
		echo $content;
	break;
}
?>
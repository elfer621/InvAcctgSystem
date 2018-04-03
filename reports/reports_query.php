<?php
switch($_REQUEST['data']){
		case"Sales":
			$sql_graph = "select date_format(`timestamp`,'%Y-%m-%d') as xdate,sum(qty * selling) as rec1 from tbl_sales_items where date_format(`timestamp`,'%Y-%m-%d') between '$begdate' and '$enddate' 
			 group by date_format(`timestamp`,'%Y-%m-%d')";
		break;
		case"salesMonthly":
			$sql_graph = "select date_format(`timestamp`,'%Y-%m') as xdate,sum(qty * selling) as rec1 from tbl_sales_items where date_format(`timestamp`,'%Y-%m-%d') between '$begdate' and '$enddate' 
			 group by date_format(`timestamp`,'%Y-%m')";
		break;
	}
?>
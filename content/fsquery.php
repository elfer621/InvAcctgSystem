<?php
//$where = $_REQUEST['q']?"where {$_REQUEST['q']}":"where a.fiscal_year='".date('Y')."'";
$where = "where date>='$begdate' and date<='$enddate'";
if($_REQUEST['report_type']=="TRIALBAL"){
	$report_type ="";
}else{
	$report_type = $_REQUEST['report_type']?"and a.report_type='{$_REQUEST['report_type']}'":"";
}
switch($_REQUEST['report_type']){
	case'PNL':
		$sql = "select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) as sub_total from 
		(
			select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
			 union 
			select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
			 union 
			select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
			 union 
			select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
			 union 
			select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(cr-dr) as sub_total from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
		) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
		group by tbl.account_code order by b.id";
	break;
	case'BS':
		$sql = "select account_type,account_code,account_group,account_desc,sum(total_dr) as total_dr,sum(total_cr) as total_cr,sum(sub_total) sub_total from 
				(
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 union 
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 union 
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 union 
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
					 union 
					select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr,sum(dr-cr) sub_total from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type group by account_code
				) tbl left join tbl_group_sorting b on tbl.account_group=b.group_name 
				group by tbl.account_code order by b.id,tbl.account_desc";
	break;
	default:
		$beg="select sum(num) num,account_type,account_code,account_group,account_desc,sum(total_dr-total_cr) as begbal,0 as total_dr,0 as total_cr from 
			(
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type='BEGBAL') group by account_code
			) tbl 
			group by account_code order by account_group";
			$sql = "select sum(num) num,account_type,account_code,account_group,account_desc,0 begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr from 
			(
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_uclm a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmain a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_ucmambaling a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
				 union 
				select count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry_warehouse a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type and (a.type!='BEGBAL') group by account_code
			) tbl 
			group by account_code";
			$sql="select account_type,account_code,account_group,concat(account_desc,' (',sum(num),')') account_desc,sum(begbal) begbal,sum(total_dr) as total_dr,sum(total_cr) as total_cr,((sum(begbal)+sum(total_dr))-sum(total_cr)) as sub_total from 
			(".$sql." union ".$beg.") 
			tbl2 group by account_code order by account_group";
	break;
}
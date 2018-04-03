var con;
function getDataFilterValue(num=null){
	var field=[];
	var comparison=[];
	var val=[];
	
	// $("select[name='field[]']").each(function() {
		// var value = $(this).val().replace(/,/g, "");
		// if(value!=""){
			// field.push(value);
		// }
	// });
	// $("select[name='comparison[]']").each(function() {
		// var value = $(this).val().replace(/,/g, "");
		// if(value!=""){
			// comparison.push(value);
		// }
	// });
	
	// $("input[name='val[]']").each(function() {
		// var value = $(this).val().replace(/,/g, "");
		// if(value!=""){
			// val.push(value);
		// }
	// });
	
	$(".val").each(function() {
		var id = $(this).closest('tr').attr('id');
		var value = $(this).val().replace(/,/g, "");
		if(value!=""){
			val.push(value);
			field.push($("#"+id).find('td:eq(0)').find("select[name='field[]']").val());
			comparison.push($("#"+id).find('td:eq(1)').find("select[name='comparison[]']").val());
			
		}
		
	});
	
	// $("select[name='val[]']").each(function() {
		// var value = $(this).val().replace(/,/g, "");
		// if(value!=""){
			// val.push(value);
		// }
	// });
	if(field.length==0){
		return "";
	}else{
		// console.log(field);
		// console.log(comparison);
		// console.log(val);
		if(num==null && (field.length!=comparison.length || field.length!=val.length || comparison.length!=val.length)){
			alert("Value not equal...");
			return false;
			event.preventDefault();
		}else{
			var q="";
			var flag=false;
			for(var i=0;i<(num==null?field.length:num);i++){
				if(flag)q+=" and ";
				if(comparison[i]=="like"){
					q+="`"+field[i]+"` "+comparison[i]+" '%"+val[i]+"%'";
				}else{
					q+="`"+field[i]+"` "+comparison[i]+" '"+val[i]+"'";
				}
				flag=true;
			}
			return q;
		}
		
	}
}
function showDataFilter(refid){
	var page = getParam('page');
	if(page!="jqgrid"){
		window.location = "?page=jqgrid&refid="+refid;
	}else{
		$('#dialogbox3').dialog({
			autoOpen: false,
			width: 550,
			height: 350,
			modal: true,
			resizable: false,
			title:"Data Filter"
		});
		htmlobj=$.ajax({url:'./content/jqgrid_ajax.php?execute=data_filter&refid='+refid,async:false});
		$('#dialogbox3').html(htmlobj.responseText);
		$('#dialogbox3').dialog('open');
	}
	
	//window.location = "?page=jqgrid&refid="+refid;
}
function showNewDataFilterNotJqgrid(refid){
	location.hash="&fltr_refid="+refid;
	$('#dialogbox3').dialog({
		autoOpen: false,
		width: 700,
		height: 400,
		modal: true,
		resizable: false,
		title:"Data Filter"
	});
	htmlobj=$.ajax({url:'./content/jqgrid_ajax.php?execute=new_data_filter&fltr_refid='+refid,async:false});
	$('#dialogbox3').html(htmlobj.responseText);
	$('#dialogbox3').dialog('open');
}
function showDataFilterNotJqgrid(refid){
	location.hash="&refid="+refid;
	$('#dialogbox3').dialog({
		autoOpen: false,
		width: 550,
		height: 350,
		modal: true,
		resizable: false,
		title:"Data Filter"
	});
	htmlobj=$.ajax({url:'./content/jqgrid_ajax.php?execute=data_filter&refid='+refid,async:false});
	$('#dialogbox3').html(htmlobj.responseText);
	$('#dialogbox3').dialog('open');
}

function executeDataFilter(refid){
	var where = getDataFilterValue();
	if(refid==53){
		viewReport('reports/sales_reports_lizgan.php?where='+where);
	}else if(refid==52){
		viewReport('reports/ledger_summary.php?where='+where);
	}else{
		openJqGridPage(refid,where);
	}
	$('#dialogbox3').dialog('close');
}

function openJqGridPage(refid,where){
	populateTab(refid,where);
}
$(function() {
	$(".ui-closable-tab").live( "click", function() {
		var num = $( this ).closest( "li" ).index() + 1;
		$( this ).closest( "li" ).remove();
		$("#tab"+num).remove();
	});
});
function populateTab(refid,where){
	$.ajax({
		url: './content/jqgrid_ajax.php?execute=jqConfig&col=column_master&refid='+refid,
		type:"POST",
		dataType:"json",
		success:function(data){
			var reptype = getParam('report_type')?getParam('report_type'):"Acctg Report";
			var num_tabs = $("div#tabs ul li").length + 1;
			var content = "<input id='bt11' class='buthov' type='button' value='Export' onclick='ExportToExcelJqgrid(\"list"+num_tabs+"\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
						<div style='clear:both;height:10px;'></div>\
						<table id='list"+num_tabs+"'></table>\
						<div id='pager"+num_tabs+"'></div>\
						<br />\
						<table id='list_d"+num_tabs+"'></table>\
						<div id='pager_d"+num_tabs+"'></div>";
			$("div#tabs ul").append(
				"<li><a href='#tab" + num_tabs + "'>" + (data.title?data.title:reptype) + "</a><span class='ui-icon ui-icon-circle-close ui-closable-tab'></li>"
			);
			$("div#tabs").append(
				"<div id='tab" + num_tabs + "'>" + content + "</div>"
			);
			$("div#tabs").tabs("refresh").tabs({ active:num_tabs - 1});
			switch(getParam('report_type')){
				case'AccountReceivable':
					generateARreportContent(where,num_tabs);
				break;
				case'AccountPayable':
					generateAPreportContent(where,num_tabs);
				break;
				default:
					initializeGrid(refid,where,num_tabs);
				break;
			}
			
		}
	});
}
// function generateARreport(){
	// window.location="?page=jqgrid&refid=51&report_type=AccountReceivable";
	// showDataFilter();
// }
function generateAPreportContent(where,tab){
	var url = "./content/jqgrid_ajax.php?execute=ap_content";
	jQuery("#list"+tab).jqGrid({
			url:url+"&q="+where,
			datatype: "json",
			colNames:['ID','DR','CR','Balance','Supplier Name'],
			colModel:[
				{name:'ap_refid',index:'ap_refid'},
				{name:'dr',index:'dr',align:"right",formatter:"number"},
				{name:'cr',index:'cr',align:"right",formatter:"number"},
				{name:'ap_bal',index:'ar_bal',align:"right",formatter:"number"},
				{name:'supplier_name',index:'supplier_name'},
			],
			rowNum:-1,
			rowList:[50,100,200],
			pager: '#pager'+tab,
			sortname: 'supplier_name',
			autowidth: true,
			shrinkToFit: true,
			height:500,
			viewrecords: true,
			sortorder: "asc",
			multiselect: false,
			grouping:false,
			caption: "Records",
			footerrow: true,
			userDataOnFooter: true,
			gridComplete: function(){
				var parseDr=  $(this).jqGrid('getCol', 'dr', false, 'sum');
				var parseCr=  $(this).jqGrid('getCol', 'cr', false, 'sum');
				var parseTotal=  $(this).jqGrid('getCol', 'ap_bal', false, 'sum');
				 $(this).jqGrid('footerData', 'set', {ar_refid:"GrandTotal", dr: parseDr,cr: parseCr,ap_bal:parseTotal});
			  },
			onSelectRow: function(ids) {
				console.log(ids);
				console.log(where);
				generateAPreportDetails(ids,where);
			}
		});
	
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function createTab(){
	var num_tabs = $("div#tabs ul li").length + 1;
	var content = "<input id='bt11' class='buthov' type='button' value='Export' onclick='ExportToExcelJqgrid(\"list"+num_tabs+"\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
				<div style='clear:both;height:10px;'></div>\
				<table id='list"+num_tabs+"'></table>\
				<div id='pager"+num_tabs+"'></div>\
				<br />\
				<table id='list_d"+num_tabs+"'></table>\
				<div id='pager_d"+num_tabs+"'></div>";
	$("div#tabs ul").append(
		"<li><a href='#tab" + num_tabs + "'>RecordsDetails</a><span class='ui-icon ui-icon-circle-close ui-closable-tab'></li>"
	);
	$("div#tabs").append(
		"<div id='tab" + num_tabs + "'>" + content + "</div>"
	);
	$("div#tabs").tabs("refresh").tabs({ active:num_tabs - 1});
	return num_tabs;
}
function generateAPreportDetails(supid,where){
	var tab=createTab();
	var url='./content/jqgrid_ajax.php?execute=generateAPreportDetails&supid='+supid;
	jQuery("#list"+tab).jqGrid({
		url:url+"&q="+where,
		datatype: "json",
		colNames:['ID','Date','Remarks','DR','CR'],
		colModel:[
			{name:'id',index:'id'},
			{name:'date',index:'date'},
			{name:'remarks',index:'remarks'},
			{name:'dr',index:'dr',align:"right",formatter:"number"},
			{name:'cr',index:'cr',align:"right",formatter:"number"}
		],
		rowNum:-1,
		rowList:[50,100,200],
		pager: '#pager'+tab,
		sortname: 'date',
		autowidth: true,
        shrinkToFit: true,
		height:500,
		viewrecords: true,
		sortorder: "asc",
		multiselect: false,
		grouping:false,
		gridComplete: function(){
            var parseDr=  $(this).jqGrid('getCol', 'dr', false, 'sum');
			var parseCr=  $(this).jqGrid('getCol', 'cr', false, 'sum');
			var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
             $(this).jqGrid('footerData', 'set', {remarks:"GrandTotal", dr: parseDr,cr: parseCr});
          },
		caption: "Records",
		footerrow: true,
		userDataOnFooter: true,
		onSelectRow: function(ids) {
			//alert($("#"+ids).find('td').eq(6).text());
			// var id =ids.split("|");
			// console.log(ids);
			// console.log(where);
			viewReport('./reports/vouchering.php?refid='+ids);
		}
	});
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function generateARreportContent(where,tab){
	var url = "./content/jqgrid_ajax.php?execute=ar_content";
	jQuery("#list"+tab).jqGrid({
			url:url+"&q="+where,
			datatype: "json",
			colNames:['ID','DR','CR','Balance','Customer Name'],
			colModel:[
				{name:'ar_refid',index:'ar_refid'},
				{name:'dr',index:'dr',align:"right",formatter:"number"},
				{name:'cr',index:'cr',align:"right",formatter:"number"},
				{name:'ar_bal',index:'ar_bal',align:"right",formatter:"number"},
				{name:'customer_name',index:'customer_name'},
			],
			rowNum:-1,
			rowList:[50,100,200],
			pager: '#pager'+tab,
			sortname: 'customer_name',
			autowidth: true,
			shrinkToFit: true,
			height:500,
			viewrecords: true,
			sortorder: "asc",
			multiselect: false,
			grouping:false,
			caption: "Records",
			footerrow: true,
			userDataOnFooter: true,
			gridComplete: function(){
				var parseDr=  $(this).jqGrid('getCol', 'dr', false, 'sum');
				var parseCr=  $(this).jqGrid('getCol', 'cr', false, 'sum');
				var parseTotal=  $(this).jqGrid('getCol', 'ar_bal', false, 'sum');
				 $(this).jqGrid('footerData', 'set', {ar_refid:"GrandTotal", dr: parseDr,cr: parseCr,ar_bal:parseTotal});
			  },
			onSelectRow: function(ids) {
				console.log(ids);
				console.log(where);
				generateARreportDetails(ids,where);
			}
		});
	
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function generateARreportDetails(custid,where){
	var tab=createTab();
	var url='./content/jqgrid_ajax.php?execute=generateARreportDetails&custid='+custid;
	jQuery("#list"+tab).jqGrid({
		url:url+"&q="+where,
		datatype: "json",
		colNames:['ID','Date','Remarks','DR','CR'],
		colModel:[
			{name:'id',index:'id'},
			{name:'date',index:'date'},
			{name:'remarks',index:'remarks'},
			{name:'dr',index:'dr',align:"right",formatter:"number"},
			{name:'cr',index:'cr',align:"right",formatter:"number"}
		],
		rowNum:-1,
		rowList:[50,100,200],
		pager: '#pager'+tab,
		sortname: 'date',
		autowidth: true,
        shrinkToFit: true,
		height:500,
		viewrecords: true,
		sortorder: "asc",
		multiselect: false,
		grouping:false,
		gridComplete: function(){
            var parseDr=  $(this).jqGrid('getCol', 'dr', false, 'sum');
			var parseCr=  $(this).jqGrid('getCol', 'cr', false, 'sum');
			var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
             $(this).jqGrid('footerData', 'set', {remarks:"GrandTotal", dr: parseDr,cr: parseCr});
          },
		caption: "Records",
		footerrow: true,
		userDataOnFooter: true,
		onSelectRow: function(ids) {
			//alert($("#"+ids).find('td').eq(6).text());
			// var id =ids.split("|");
			// console.log(ids);
			// console.log(where);
			viewReport('./reports/vouchering.php?refid='+ids);
		}
	});
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function initializeGrid(refid,where,tab=""){
	//master
	if(refid==50){
		setTimeout(function() {creatGridAcctgReport(where,tab,'./content/jqgrid_ajax.php?execute=acctg_report'+'&report_type='+getParam('report_type'));},50);
	}else{
		$.ajax({
		   type: "POST",
		   url: './content/jqgrid_ajax.php?execute=jqConfig&col=column_master&refid='+refid,
		   dataType: "json",
		   success: function(result)
		   {
				colNm = result.colNames;
				colMm = result.colModel;
				if(result.isgrouping){
					setTimeout(function() {creatGridMasterGroupings(refid,colNm,colMm,where,tab,result.isgrouping);},50);
				}else{
					setTimeout(function() {creatGridMaster(refid,colNm,colMm,where,tab);},50);
					//details
					$.ajax({
					   type: "POST",
					   url: './content/jqgrid_ajax.php?execute=jqConfig&col=column_details&refid='+refid,
					   data: "",
					   dataType: "json",
					   success: function(result)
					   {
							colNd = result.colNames;
							colMd = result.colModel;
							setTimeout(function() {creatGridDetails(refid,colNd,colMd,tab);},50);
							
					   },
					   error: function(x, e)
					   {
							alert(x.readyState + " "+ x.status +" "+ e.msg);   
					   }
					});
				}
				
		   },
		   error: function(x, e)
		   {
				alert(x.readyState + " "+ x.status +" "+ e.msg);   
		   }
		});
	}
	
}
function jqgridSaveRec(tblname){
	var data = $("#dynamic_frm").serialize();
	$.ajax({
		url: './content/jqgrid_ajax.php?execute=jqgridSaveRec&tblname='+tblname+'&'+data,
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}else{
				alertMsg(data);
			}
		}
	});
}
function editRec(tblname,colname){
	var refid = $("#refid").val();
	if(refid == null){
		alert("Pls select records to Edit...");
		return false;
	}else{
		clickDialogUrl("dialogbox3","450","500","./content/jqgrid_ajax.php?execute=dynamic_add_records&refid="+refid+"&colname="+colname+"&tblname="+tblname,"Add Records");
	}
}
function dynamicJqgrid(){
	var tblname = getParam('tblname');
	$.ajax({
	   type: "POST",
	   url: './content/jqgrid_ajax.php?execute=dynamic_tbl_col&tblname='+tblname,
	   dataType: "json",
	   success: function(result)
	   {
			colNm = result.colNames;
			colMm = result.colModel;
			setTimeout(function() {
				var num_tabs = $("div#tabs ul li").length + 1;
				var content = "<input id='bt11' class='buthov' type='button' value='Export' onclick='ExportToExcelJqgrid(\"list"+num_tabs+"\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
						<input id='bt10' class='buthov' type='button' value='Add' onclick='clickDialogUrl(\"dialogbox3\",\"450\",\"500\",\"./content/jqgrid_ajax.php?execute=dynamic_add_records&tblname="+tblname+"\",\"Add Records\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
						<input id='bt11' class='buthov' type='button' value='Edit' onclick='editRec(\""+tblname+"\",\""+colNm[0]+"\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
						<input type='hidden' id='refid'/>\
						<div style='clear:both;height:10px;'></div>\
							<table id='list"+num_tabs+"'></table>\
							<div id='pager"+num_tabs+"'></div>\
							<br />\
							<table id='list_d"+num_tabs+"'></table>\
							<div id='pager_d"+num_tabs+"'></div>";
				$("div#tabs ul").append(
					"<li><a href='#tab" + num_tabs + "'>View Records</a><span class='ui-icon ui-icon-circle-close ui-closable-tab'></li>"
				);
				$("div#tabs").append(
					"<div id='tab" + num_tabs + "'>" + content + "</div>"
				);
				$("div#tabs").tabs("refresh").tabs({ active:num_tabs - 1});
				var tab=num_tabs;
				var url='./content/jqgrid_ajax.php?execute=dynamic_tbl_content&tblname='+tblname;
				jQuery("#list"+tab).jqGrid({
					url:url,
					datatype: "json",
					colNames:colNm,
					colModel:colMm,
					rowNum:1000,
					rowList:[1000,2000,3000],
					pager: '#pager'+tab,
					autowidth: true,
					shrinkToFit: true,
					height:450,
					viewrecords: true,
					sortorder: "desc",
					multiselect: false,
					caption: "Header",
					onSelectRow: function(ids) {
						//alert($("#"+ids).find('td').eq(6).text());
						//console.log($(this));
						console.log(colNm[0]);
						$("#refid").val(ids);
						// var id =ids.split("_");
						// console.log(id[0]);
					}
				});
				jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
			},50);
	   },
	   error: function(x, e)
	   {
			alert(x.readyState + " "+ x.status +" "+ e.msg);   
	   }
	});
}
function creatGridMaster(refid,colN,colM,where,tab=""){	
	jQuery("#list"+tab).jqGrid({
		url:'./content/jqgrid_ajax.php?execute=master&refid='+refid+"&q="+where+'&con='+con,
		datatype: "json",
		colNames:colN,
		colModel:colM,
		rowNum:-1,
		//rowList:[50,100,200],
		//pager: '#pager'+tab,
		sortname: 'id',
		autowidth: true,
        shrinkToFit: true,
		height:300,
		viewrecords: true,
		sortorder: "desc",
		multiselect: false,
		caption: "Header",
		footerrow: false,
		userDataOnFooter: true,
		gridComplete: function(){
            var sumAmt=  $(this).jqGrid('getCol', 'amount', false, 'sum');
			var sumVat=  $(this).jqGrid('getCol', 'vat', false, 'sum');
			//var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
             $(this).jqGrid('footerData', 'set', {studentname:"GrandTotal", amount: new Number(sumAmt).formatMoney(2),vat: new Number(sumVat).formatMoney(2)});
          },
		onSelectRow: function(ids) {
			//console.log(ids);
			//alert($("#"+ids).find('td').eq(6).text());
			//alert($("#"+ids).find('td:last-child').text());
			//console.log($(this));
			// console.log(ids);
			// var id =ids.split("_");
			// console.log(id[0]);
			if(ids == null) {
				ids=0;
				if(jQuery("#list_d"+tab).jqGrid('getGridParam','records') >0 )
				{
					jQuery("#list_d"+tab).jqGrid('setGridParam',{url:"./content/jqgrid_ajax.php?execute=master_details&refid="+refid+"&id="+ids+'&con='+$("#"+ids).find('td:last-child').text(),page:1});
					jQuery("#list_d"+tab).jqGrid('setCaption',"Invoice Detail: "+ids)
					.trigger('reloadGrid');
				}
			} else {
				jQuery("#list_d"+tab).jqGrid('setGridParam',{url:"./content/jqgrid_ajax.php?execute=master_details&refid="+refid+"&id="+ids+'&con='+$("#"+ids).find('td:last-child').text(),page:1});
				jQuery("#list_d"+tab).jqGrid('setCaption',"Invoice Detail: "+ids)
				.trigger('reloadGrid');			
			}
		}
	});
	//jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function creatGridDetails(refid,colN,colM,tab=""){
	jQuery("#list_d"+tab).jqGrid({
		height: 200,
		autowidth: true,
        shrinkToFit: true,
		url:'./content/jqgrid_ajax.php?execute=master_details&refid='+refid+'&con='+con,
		datatype: "json",
		colNames:colN,
		colModel:colM,
		rowNum:-1,
		//rowList:[50,100,200],
		//pager: '#pager_d'+tab,
		viewrecords: true,
		sortorder: "asc",
		multiselect: false,
		caption:"Details"
	});//.navGrid('#pager_d'+tab,{add:false,edit:false,del:false});
}
function creatGridMasterGroupings(refid,colN,colM,where,tab="",groupfield){	
	console.log("Master Groupings...");
	jQuery("#list"+tab).jqGrid({
		url:'./content/jqgrid_ajax.php?execute=master&refid='+refid+"&q="+where+'&con='+con,
		datatype: "json",
		colNames:colN,
		colModel:colM,
		rowNum:-1,
		rowList:[50,100,200],
		pager: '#pager'+tab,
		sortname: groupfield,
		autowidth: true,
        shrinkToFit: true,
		height:500,
		viewrecords: true,
		sortorder: "asc",
		multiselect: false,
		grouping:true,
		groupingView : {
			groupField : [groupfield],
			groupSummary : [true],
			groupColumnShow : [false],
			groupText : ['<b>Entry # {0}</b>'],
			// groupText: function(group){
				// return group;
			// },
			groupCollapse : false,
			groupOrder: ['asc']
		},
		onSelectRow: function(ids) {
			// console.log(ids);
			// alert($("#"+ids).find('td').eq(4).text());
			var id =ids.split("-");
			// console.log(ids);
			// console.log(where);
			//viewReport('./reports/vouchering.php?refid='+id[0]+'&center='+id[1]);
			if(getParam('refid')==6){
				$.ajax({
					url: './content/vouchering_ajax.php?execute=viewVoucher&refid='+id[0]+'&type='+id[1],
					type:"POST",
					success:function(data){
						if(data=="success"){
							window.location.href="?page=vouchering";
						}
					}
				});
			}
		},
		caption: "Records"
	});
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
	
}
function createLedger(account_code,where){
	var num_tabs = $("div#tabs ul li").length + 1;
	var content = "<input id='bt11' class='buthov' type='button' value='Export' onclick='ExportToExcelJqgrid(\"list"+num_tabs+"\");' style='float:left;width:100px;height:30px;margin:5px 10px;'/>\
				<div style='clear:both;height:10px;'></div>\
				<table id='list"+num_tabs+"'></table>\
				<div id='pager"+num_tabs+"'></div>\
				<br />\
				<table id='list_d"+num_tabs+"'></table>\
				<div id='pager_d"+num_tabs+"'></div>";
	$("div#tabs ul").append(
		"<li><a href='#tab" + num_tabs + "'>Ledger</a><span class='ui-icon ui-icon-circle-close ui-closable-tab'></li>"
	);
	$("div#tabs").append(
		"<div id='tab" + num_tabs + "'>" + content + "</div>"
	);
	$("div#tabs").tabs("refresh").tabs({ active:num_tabs - 1});
	var tab=num_tabs;
	var url='./content/jqgrid_ajax.php?execute=ledger&account_code='+account_code;
	jQuery("#list"+tab).jqGrid({
		url:url+"&q="+where,
		datatype: "json",
		colNames:['Date','Account Desc','Center','Remarks','Reference','DR','CR'],
		colModel:[
			{name:'date',index:'date'},
			{name:'account_desc',index:'account_desc'},
			{name:'center',index:'center'},
			{name:'remarks',index:'remarks',summaryType:'count',summaryTpl:'<b>{0} SubTotal</b>'},
			{name:'reference',index:'reference'},
			{name:'dr',index:'dr',align:"right",formatter:"number",summaryType:'sum'},
			{name:'cr',index:'cr',align:"right",formatter:"number",summaryType:'sum'}
		],
		rowNum:-1,
		rowList:[50,100,200],
		pager: '#pager'+tab,
		sortname: 'date',
		autowidth: true,
        shrinkToFit: true,
		height:500,
		viewrecords: true,
		sortorder: "asc",
		multiselect: false,
		grouping:true,
		groupingView : {
			groupField : ['account_desc'],
			groupSummary : [true],
			groupColumnShow : [false],
			groupText : ['<b>{0}</b>'],
			groupCollapse : false,
			groupOrder: ['asc']
		},
		gridComplete: function(){
            var parseDr=  $(this).jqGrid('getCol', 'dr', false, 'sum');
			var parseCr=  $(this).jqGrid('getCol', 'cr', false, 'sum');
			var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
             $(this).jqGrid('footerData', 'set', {remarks:"GrandTotal", dr: parseDr,cr: parseCr});
          },
		caption: "Records",
		footerrow: true,
		userDataOnFooter: true,
		onSelectRow: function(ids) {
			//alert($("#"+ids).find('td').eq(6).text());
			var id =ids.split("-");
			console.log(ids);
			console.log(where);
			viewReport('./reports/vouchering.php?refid='+id[0]+'&center='+id[1]);
		}
	});
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function creatGridAcctgReport(where,tab="",url){	
	var reptype = getParam('report_type');
	if(reptype=='TRIALBAL'){
		jQuery("#list"+tab).jqGrid({
			url:url+"&q="+where,
			datatype: "json",
			colNames:['Type','Code','Account Group','Account Desc','BegBal','DR','CR','Sub Total'],
			colModel:[
				{name:'account_type',index:'account_type'},
				{name:'account_code',index:'account_code'},
				{name:'account_group',index:'account_group'},
				{name:'account_desc',index:'account_desc',summaryType:'count',summaryTpl:'<b>SubTotal</b>'},
				{name:'begbal',index:'begbal',align:"right",formatter:"number",summaryType:'sum'},
				{name:'total_dr',index:'total_dr',align:"right",formatter:"number",summaryType:'sum'},
				{name:'total_cr',index:'total_cr',align:"right",formatter:"number",summaryType:'sum'},
				{name:'sub_total',index:'sub_total',align:"right",formatter:"number",summaryType:'sum'}
			],
			rowNum:-1,
			rowList:[50,100,200],
			pager: '#pager'+tab,
			sortname: 'account_group',
			autowidth: true,
			shrinkToFit: true,
			height:500,
			viewrecords: true,
			sortorder: "asc",
			multiselect: false,
			grouping:true,
			groupingView : {
				groupField : ['account_group'],
				groupSummary : [true],
				groupColumnShow : [false],
				groupText : ['<b>Acct. Group: {0}</b>'],
				groupCollapse : false,
				groupOrder: ['asc']
			},
			gridComplete: function(){
				var parseBeg=  $(this).jqGrid('getCol', 'begbal', false, 'sum');
				var parseDr=  $(this).jqGrid('getCol', 'total_dr', false, 'sum');
				var parseCr=  $(this).jqGrid('getCol', 'total_cr', false, 'sum');
				var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
				 $(this).jqGrid('footerData', 'set', {account_desc:"GrandTotal", begbal:parseBeg,total_dr: parseDr,total_cr: parseCr,sub_total: parseTotal});
			  },
			caption: "Records",
			footerrow: true,
			userDataOnFooter: true,
			onSelectRow: function(ids) {
				//alert($("#"+ids).find('td').eq(6).text());
				console.log(ids);
				console.log(where);
				createLedger(ids,where);
			}
		});
	}else{
		jQuery("#list"+tab).jqGrid({
			url:url+"&q="+where,
			datatype: "json",
			colNames:['Type','Code','Account Group','Account Desc','DR','CR','Total'],
			colModel:[
				{name:'account_type',index:'account_type'},
				{name:'account_code',index:'account_code'},
				{name:'account_group',index:'account_group'},
				{name:'account_desc',index:'account_desc',summaryType:'count',summaryTpl:'<b>SubTotal</b>'},
				{name:'total_dr',index:'total_dr',align:"right",formatter:"number",summaryType:'sum'},
				{name:'total_cr',index:'total_cr',align:"right",formatter:"number",summaryType:'sum'},
				{name:'sub_total',index:'sub_total',align:"right",formatter:"number",summaryType:'sum'}
			],
			rowNum:-1,
			rowList:[50,100,200],
			pager: '#pager'+tab,
			sortname: 'account_group',
			autowidth: true,
			shrinkToFit: true,
			height:500,
			viewrecords: true,
			sortorder: "asc",
			multiselect: false,
			grouping:true,
			groupingView : {
				groupField : ['account_group'],
				groupSummary : [true],
				groupColumnShow : [false],
				groupText : ['<b>{0}</b>'],
				groupCollapse : false,
				groupOrder: ['asc']
			},
			gridComplete: function(){
				var parseDr=  $(this).jqGrid('getCol', 'total_dr', false, 'sum');
				var parseCr=  $(this).jqGrid('getCol', 'total_cr', false, 'sum');
				var parseTotal=  $(this).jqGrid('getCol', 'sub_total', false, 'sum');
				 $(this).jqGrid('footerData', 'set', {account_desc:"GrandTotal", total_dr: parseDr,total_cr: parseCr,sub_total: parseTotal});
			  },
			caption: "Records",
			footerrow: true,
			userDataOnFooter: true,
			onSelectRow: function(ids) {
				console.log(ids);
				console.log(where);
				createLedger(ids,where);
			}
		});
	}
	jQuery("#list"+tab).jqGrid('navGrid','#pager'+tab,{add:false,edit:false,del:false});
}
function currencyFmatter (cellvalue, options, rowObject){
 
    var value = parseFloat(cellvalue), retult,
        op = $.extend({}, $.jgrid.formatter.number); // or $.jgrid.formatter.integer

    if(!$.fmatter.isUndefined(options.colModel.formatoptions)) {
        op = $.extend({}, op,options.colModel.formatoptions);
    }
    retult = $.fmatter.util.NumberFormat(Math.abs(value), op);
    return (value >= 0 ? retult : '<span style="color:red;">(' + retult + ')</span>');
}


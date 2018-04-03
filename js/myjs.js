//javascript:window.history.forward(1); //disable back button
/*(function (global) { 

    if(typeof (global) === "undefined") {
        throw new Error("window is undefined");
    }

    var _hash = "!";
    var noBackPlease = function () {
        global.location.href += "#";

        // making sure we have the fruit available for juice (^__^)
        global.setTimeout(function () {
            global.location.href += "!";
        }, 50);
    };

    global.onhashchange = function () {
        if (global.location.hash !== _hash) {
            global.location.hash = _hash;
        }
    };

    global.onload = function () {            
        noBackPlease();

        // disables backspace on page except on input fields and textarea..
        document.body.onkeydown = function (e) {
            var elm = e.target.nodeName.toLowerCase();
            if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                e.preventDefault();
            }
            // stopping event bubbling up the DOM tree..
            e.stopPropagation();
        };          
    }

})(window);*/
$(document).ready(function() {
	msieversion();
});
function show_patientinfo(){
	clickDialog('dialogbox',700,500,'patientlist','Patient Info');
}
function strtodouble(strnum){
	if(strnum!=undefined){
		return Number(strnum.replace(/[^0-9\.-]+/g,""));
	}else{
		return 0;
	}
	
	
}
function strtocurrency(strnum){
	return new Number(strnum).formatMoney(2);
}
function msieversion() 
{
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0) // If Internet Explorer, return version number
    {
        alert(parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)))+"\n The system is not compatible with IE browser...");
		window.location='error.html';
    }
    return false;
}
function generateUUID() {
    var d = new Date().getTime();
    if(window.performance && typeof window.performance.now === "function"){
        d += performance.now();; //use high-precision timer if available
    }
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return '&uuid='+uuid;
};
function updateItems(){
	showLoading();
	$.ajax({
		url: './content/db_ajax.php?execute=updateItems',
		type:"POST",
		success:function(data){
			$('#popuploading').dialog('close');
			alert(data);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$('#popuploading').dialog('close');
			alert(xhr.status);
			alert(thrownError);
		}
	});
}
function SendInvToAdmin(){
	showLoading();
	$.ajax({
		url: './content/db_ajax.php?execute=sendInvToAdmin',
		type:"POST",
		success:function(data){
			$('#popuploading').dialog('close');
			alert(data);
			window.location=document.URL;
		},
		error: function (xhr, ajaxOptions, thrownError) {
			$('#popuploading').dialog('close');
			alert(xhr.status);
			alert(thrownError);
		}
	});
}
var tblToExcel = (function () {
	var uri = 'data:application/vnd.ms-excel;base64,'
	, template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
	, base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
	, format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
	return function (table,filename,name) {
		if (!table.nodeType) table = document.getElementById(table)
		var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }

		document.getElementById("dlink").href = uri + base64(format(template, ctx));
		document.getElementById("dlink").download = filename+".xls";
		document.getElementById("dlink").click();

	}
})();
function ExportToExcel(mytblId){
   var htmltable= document.getElementById(mytblId);
   var html = htmltable.outerHTML;
   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
function ExportToExcelJqgrid(mytblId){
	var cols = [];
	var mycolModel = $("#"+mytblId).getGridParam("colModel");
	var tbl='<table><tr>';
	$.each(mycolModel, function(i) {
		if (!this.hidden) {
			//cols.push(this.name);
			tbl+="<td>"+this.name+"</td>";
		}
	});
	tbl+="<tr/></table>";
   var htmltable= document.getElementById(mytblId);
   var html = tbl+htmltable.outerHTML;
   window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}
function serializing(formid){
	var paramObj = {};
	$.each($(formid).serializeArray(), function(_, kv) {
	  paramObj[kv.name] = kv.value;
	});
	return paramObj;
}
function getUrl(param) {
	var vars = {};
	window.location.href.replace( location.hash, '' ).replace( 
		/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
		function( m, key, value ) { // callback
			vars[key] = value !== undefined ? value : '';
		}
	);

	if ( param ) {
		return vars[param] ? vars[param] : null;	
	}
	return vars;
}
Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
function clickDialog(idname,xwidth,xheight,contentpath,xtitle,param=null,file=null){
	$('#'+idname).dialog({
		autoOpen: false,
		width: (xwidth>$( document ).width()?($( document ).width() * .9):xwidth),
		height: (xheight>$( document ).height()?($( document ).height() * .9):xheight),
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:xtitle
		/*buttons: {
			"Save": function() {
				$("form").submit();
			},
			"Cancel": function() {
				$(this).dialog("close");
			}
		}*/
	});
	if(file==null){
		htmlobj=$.ajax({url:'./content/pos_ajax.php?execute='+contentpath,data:param,async:false});
	}else{
		htmlobj=$.ajax({url:'./content/'+file+'?execute='+contentpath,data:param,async:false});
	}
	
	$('#'+idname).html(htmlobj.responseText);
	$('#'+idname).dialog('open');
	//$(".ui-dialog-titlebar").hide();
}
function alertMsg(msg,focusid=null){
	$('#msg').dialog({
		autoOpen: false,
		width: 300,
		height: 200,
		modal: true,
		resizable: false,
		close:function(event){
			if(focusid==null){
				$('#barcode').focus();
			}else{
				$('#'+focusid).focus();
			}
			
		},
		title:"NOTIFICATION",
		buttons: {
			"Close": function() {
				$(this).dialog("close");
				window.location=document.URL;
			}
		},
		open: function () {
			$(this).parent().find('button:nth-child(1)').focus();
		}
	});
	var txt = "<div style='width:100%;font-size:15px;text-align:center;margin-top:15px;'>"+msg+"</div>";
	$('#msg').html(txt);
	$('#msg').dialog('open');
	//$(".ui-dialog-titlebar").hide();
}
function errorMsg(msg){
	$('#msg').dialog({
		autoOpen: false,
		width: 300,
		height: 200,
		modal: true,
		resizable: false,
		close:function(event){
			window.location="./";
		},
		title:"NOTIFICATION",
		buttons: {
			"Close": function() {
				window.location=removeURLParameter(document.URL, 'error_msg');
			}
		},
		open: function () {
			$(this).parent().find('button:nth-child(1)').focus();
			//$('.ui-widget-overlay').css({opacity:1});
		}
	});
	var txt = "<div style='width:100%;font-size:20px;text-align:center;margin-top:15px;'>"+msg+"</div>";
	$('#msg').html(txt);
	$('#msg').dialog('open');
	//$(".ui-dialog-titlebar").hide();
}
function alertMsgServerReading(msg){
	$('#msg').dialog({
		autoOpen: false,
		width: 300,
		height: 200,
		modal: true,
		resizable: false,
		close:function(event){
			window.location="./";
		},
		title:"NOTIFICATION",
		buttons: {
			"Close": function() {
				$(this).dialog("close");
			}
		},
		open: function () {
			$(this).parent().find('button:nth-child(1)').focus();
			$('.ui-widget-overlay').css({opacity:1});
		}
	});
	var txt = "<div style='width:100%;font-size:20px;text-align:center;margin-top:15px;'>"+msg+"</div>";
	$('#msg').html(txt);
	$('#msg').dialog('open');
	//$(".ui-dialog-titlebar").hide();
}

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}
function boxTenderPayment(){
	var total = document.frm_pos.total.value;
	$('#tenderPayment').dialog({
		autoOpen: false,
		width: 500,
		height: 300,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:"Tender Payment",
		position: { my: 'left bottom', at: 'left top', of: $("#bt3") }
	});
	htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=tender_payment&total='+total,async:false});
	$('#tenderPayment').html(htmlobj.responseText);
	$('#tenderPayment').dialog('open');
	$(".ui-dialog-titlebar").hide();
}
function clickDialogUrl(idname,xwidth,xheight,contentpath,xtitle){
	$('#'+idname).dialog({
		autoOpen: false,
		width: (xwidth>$( document ).width()?($( document ).width() * .9):xwidth),
		height: (xheight>$( document ).height()?($( document ).height() * .9):xheight),
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:xtitle
	});
	htmlobj=$.ajax({url:contentpath,async:false});
	$('#'+idname).html(htmlobj.responseText);
	$('#'+idname).dialog('open');
	//$(".ui-dialog-titlebar").hide();
}
function showLoading(){
	$('#popuploading').dialog({
		autoOpen: false,
		width: 250,
		height: 250,
		modal: true,
		resizable: false,
		close:function(event){$('#barcode').focus();},
		title:"Loading"
	});
	$('#popuploading').html('<img src="./images/ajax-loader.gif" style="width:100px;height:100px;margin: 50px auto 0 55px;"/>');
	$('#popuploading').dialog('open');
	$(".ui-dialog-titlebar").hide();
}
function isNumberKey(evt){
 var key = (evt.which) ? evt.which : evt.keyCode
 //if (charCode > 31 && (charCode < 48 || charCode > 57))
 if ((key < 48 || key > 57) && !(key == 8 || key == 9 || key == 13 || key == 37 || key == 39 || key == 46 || key == 45) ){
	return false;
}

 return true;
}
function calcSum(objname){
	try{
		var xtotal =0;
		for (var i=0; i < objname.length; i++) {
			var val = isNaN(parseFloat(objname[i].value))?0:parseFloat(objname[i].value);
			xtotal = xtotal + val;
		}
		return xtotal.toFixed(2);
	}catch(e){
		alert(e);
	}
}
function delrows(tableID) {
	try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;
		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
				if(rowCount <= 1) {
					alert("Cannot delete all the rows.");
					break;
				}
				table.deleteRow(i);
				rowCount--;
				i--;
			}
	}
	}catch(e) {
		alert(e);
	}
}
function delrow(tableID,id){
	try {
		var table = document.getElementById(tableID);
		table.deleteRow(id);
	}catch(e) {
		alert(e);
	}
}
function changePass(){
	clickDialog('dialogbox',400,200,'changePass','Change Pass');
}
//********pos function**********
$(".tbl tr").hover(
	function(){
		$(this).children().addClass('selected');
		$(this).siblings().children().removeClass('selected');
	},
	function(){
		$(this).children().removeClass('selected');
		$(this).siblings().children().removeClass('selected');
	}
);

function runScript(e,arg) { //function that triggers onkeypress in barcode area
	var id = arg.getAttribute('id'); //argument that get the id of barcode fields
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	var barcode_val = $("tbody tr.selected").find('td:eq(0)').text();
	
	if (chCode == 13) { //pressing enter
		var bcode = $("#"+id).val();
		if(bcode==""){
			prodlist_box(id); //id name will pass to search area
		}else{
			barcode_area(bcode,id); //process barcodes
		}
    }else if(chCode == 61){ //pressing = (equal sign)
		tenderPayment(e);
		e.preventDefault();
		
	}else if(chCode == 38 || chCode == 40){ //pressing arrow up and down  || chCode == 40
		if(barcode_val==""){
			jQuery.tableNavigationMain();
			return false;
		}else{
			window.location=document.URL;
		}
	}
	
	//select option no highlight
	if(barcode_val == ""){
		var type = getParam('page');
		if(type!='stocktransfer'){
			if(chCode==46){ //pressing delete button
				delProdsale($("tbody tr:first").find('td:eq(0)').text(),id);
			}else if(chCode==113){ //pressing f2
				qtyclick($("tbody tr:first").find('td:eq(0)').text(),id,$("tbody tr:first").find('td:eq(9)').text());
			}else if(chCode==119){ //pressing f8
				priceclick($("tbody tr:first").find('td:eq(0)').text());
			}else if(e.keyCode==115){ //pressing f4
				uomlist($("tbody tr:first").find('td:eq(0)').text());
				jQuery.tableNavigationUom();
			}
		}
	}
	//select option 
	
}

function prod_search(){
	if (window.showModalDialog) {
		window.showModalDialog('./content/prod_search.php',"Receipt","dialogWidth:900px;dialogHeight:630px");
	} else {
		window.open('./content/prod_search.php',"Receipt",'height=630,width=900,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=no');
	}
}
function prodlist_box(id){
	var urls = getUrl();
	$("#prodlist").html("");
	clickDialog('prodlist',1200,550,'prodlist&id='+id,'Product List',urls);
	$("#search_prodname").focus();
}

function getParam( name ) {
  name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  var regexS = "[\\?&]"+name+"=([^&#]*)";
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  if( results == null )
    return "";
  else
    return results[1];
}
function uomselect(bcode){
	var itemSelected = $("tr.selected").find('td:eq(0)').text();
	delProdsale(itemSelected)
	saveToSession(bcode,'uom');
	
}
function setValue(oVal,id) {
	if(id!=''){
		document.getElementById(id).value = oVal;
		document.getElementById(id).focus();
		
		//added to avoid double enter 11-8-16
		setTimeout(function() {
			try{
				var bcode = $("#"+id).val();
				barcode_area(bcode,id); //process barcodes
			}catch(err){
				alert(err);
			}
			finally{
				document.getElementById(id).value = "";
			}
		}, 100);
	}else{
		barcode_area(oVal,id);
	}
	
}
function barcode_area(val,id){
	var page = getParam('page'); //get URL parameter
	if(val=="="){
		$('#barcode').val('');
	}else{
		if(page=="prod_maintenance"){
			$('#barcode').val(val);
			//getBarcodeInfo(val);
			window.location = "?page=prod_maintenance&bcodeinfo="+val;
		}else if(page=="prod_maintenance_whole"){
			$('#barcode').val(val);
			//getBarcodeInfo(val);
			window.location = "?page=prod_maintenance_whole&bcodeinfo="+val;
		}else if(page=="dynamic_invoicing"){
			addItemDetails(val);
			return;
		}else{
			saveToSession(val,id); //save barcode to session
		}
	}
}
function saveToSession(val,type){
	if(val!=""){
		if(getParam('page')=="stockin"){
			var sessiontype = '&sessiontype=stockin';
		}else if(getParam('page')=="stockout"){
			var sessiontype = '&sessiontype=stockout';
		}else if(getParam('page')=="po"){
			var sessiontype = '&sessiontype=po';
		}else if(getParam('page')=="package_create"){
			var sessiontype = '&sessiontype=package_create';
		}else if(getParam('page')=="stocktransfer"){
			switch(type){
				case'barcode_in':
					var sessiontype = '&sessiontype=stockin';
				break;
				case'barcode_out':
					var sessiontype = '&sessiontype=stockout';
				break;
			}
		}else{
			var sessiontype = '&sessiontype=sales';
		}
		var url = './content/pos_ajax.php?execute=process_barcode&barcode='+val+'&type='+type+sessiontype;
		$.ajax({
			url: url,
			type:"POST",
			dataType:"json",
			success:function(data){
				//add to avoid refreshing the page 11-9-16
				if(data['msg']=="success"){
					if(getParam('page')=="sales"||getParam('page')==undefined){
						//noRefresh(data);
						window.location=document.URL;
					}else{
						if(type=="touch_screen"){
							showTS();
						}else{
							window.location=document.URL;
						}
					}
				}else{
					alert(data['msg']);
					$('#barcode').val('');
				}
				
			}
		});
	}
}
function noRefresh(data){
	var r = data.rec;
	var myElem = document.getElementById(r.bcode);
	if (myElem === null){
		var txt ='<tr id="'+r.bcode+'">\
					<td><a href="javascript:backToBarcode();" class="activationMain">'+r.bcode+'</a></td>\
					<td  width="400px" style="text-align:left;">'+r.prod_name+'</td>\
					<td style="text-align:right;">'+new Number(r.price).formatMoney(2)+'</td>\
					<td style="text-align:right;">'+r.qty+'</td>\
					<td  style="text-align:left;">'+r.unit+'</td>\
					<td  style="text-align:right;" class="totalamt">'+new Number(r.total).formatMoney(2)+'</td>\
					<td  style="text-align:right;display:none;">'+r.sku+'</td>\
					<td  style="text-align:right;display:none;">'+r.cost+'</td>\
					<td  style="text-align:right;">'+r.subjnametype+'</td>\
				</tr>';
		$("#mytbl").prepend(txt);
	}else{
		try{
			$("#"+r.bcode).remove();
		}catch(err){
			alert(err);
		}
		finally{
			var txt ='<tr id="'+r.bcode+'">\
						<td><a href="javascript:backToBarcode();" class="activationMain">'+r.bcode+'</a></td>\
						<td  width="400px" style="text-align:left;">'+r.prod_name+'</td>\
						<td style="text-align:right;">'+new Number(r.price).formatMoney(2)+'</td>\
						<td style="text-align:right;">'+r.qty+'</td>\
						<td  style="text-align:left;">'+r.unit+'</td>\
						<td  style="text-align:right;" class="totalamt">'+new Number(r.total).formatMoney(2)+'</td>\
						<td  style="text-align:right;display:none;">'+r.sku+'</td>\
						<td  style="text-align:right;display:none;">'+r.cost+'</td>\
						<td  style="text-align:right;">'+r.subjnametype+'</td>\
					</tr>';
			//$("#"+r.bcode).html(txt);
			$("#mytbl").prepend(txt);
			
		}
	}
	$("#xtotal").html(sumTotalAmt);
	$('#barcode').val('');
	
}
function sumTotalAmt(){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(".totalamt").each(function() {
		var value = strtodouble($(this).text());
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	//return sum;
	return new Number(sum).formatMoney(2);
}
function showTS(){
	clickDialog("dialogbox",1000,620,'touch_screen',"Touch Screen");
}
function getBarcodeInfo(val,sku){ //prod_maintenance
	$.post('./content/pos_ajax.php?execute=barcodeInfo', {barcode: val,sku:sku},
		function(data){
			console.log(data);
			if(data['details']==false){
				alert("Records not found...");
				$("#sku_id").attr('readonly',false);
			}else{
				$("#sku_id").attr('readonly',true);
			}
			/*$("#sku_id").val(data['details']==false?"":data['details']['skuid']);
			$("#product_name").val(data['details']==false?"":data['details']['product_name']);
			$("#xbarcode").val(data['details']==false?"":data['details']['barcode']);
			$("#price").val(data['details']==false?"":data['details']['price']);
			$("#cost").val(data['details']==false?"":data['details']['cost']);
			$("#unit").val(data['details']==false?"":data['details']['unit']);
			$("#buom").val(data['details']==false?"":data['details']['base_unit']);
			$("#reorder_level").val(data['details']==false?"":data['details']['reorder_level']);*/
			//$("#subinfo").hide();
			$("#supplier_name").val(data['details']==false?"":data['details']['supplier_id']);
			$("#manufacturer_name").val(data['details']==false?"":data['details']['manufacturer_id']);
			$("#category").val(data['details']==false?"":data['details']['category_id']);
			//$("#sch_level").val(data['details']==false?"":data['details']['school_level']);
			//$("#subject_name").val(data['details']==false?"":data['details']['subject_name']+'|'+data['details']['subjtype']);
			//console.log(data['details']['subject_name']);
			//$("#subject_name").val((data['details']==false?"":data['details']['subject_name']));
			//$("#teacher_name").val(data['details']==false?"":data['details']['teacher_name']);
			$("#tax_type").val(data['details']==false?"":data['details']['tax_type']);
			$("#hide").val(data['details']==false?"":data['details']['hidden']);
			var tbl = '<table id="tbluom" cellspacing="0" cellpadding="3" border="1">'+
							'<tr>'+
								'<td>Barcode</td>'+
								'<td>Price</td>'+
								'<td>Cost</td>'+
								'<td colspan="2">per BaseUOM</td>'+
								'<td colspan="2">UOM</td>'+
								'<td>MENU</td>'+
							'</tr>'+
						'</table>';
			$("#tabs-2").html(tbl);
			var txt2 ="<tr>"+
							"<td><input onchange=\"uomInput(this.value,'bcode',1)\" style='width:150px;font-size:10px;' type='text' name='uom_bcode[]' /></td>"+
							"<td><input type='text' name='uom_price[]' /></td>"+
							"<td><input type='text' name='uom_cost[]' /></td>"+
							"<td style='text-align:right;'><input onchange=\"uomInput(this.value,'divmul',1)\" style='width:50px;' type='text' name='uom_divmul[]' /></td>"+
							"<td style='text-align:left;'>"+data['new_base_unit']+"</td>"+
							"<td>per</td>"+
							"<td><input type='text' name='uom_unit[]' onchange=\"uomInput(this.value,'unit',1)\" /></td>"+
							"<td><a href='javascript:uomSave("+(1)+",\""+data['details']['sku_id']+"\")'><img title='Save' src='./images/save.png' style='width:25px;height:25px;'/></a>"+
						"</tr>";
			$("#tbluom").append(txt2);
			if(data['uom']){
				for(var i=0;i<data['uom'].length;i++){
					var txt ="<tr>"+
								"<td><input style='width:150px;font-size:10px;' readonly type='text' name='uom_bcode[]' value='"+data['uom'][i]['barcode']+"'/></td>"+
								"<td><input type='text' name='uom_price[]' value='"+data['uom'][i]['price']+"'/></td>"+
								"<td><input type='text' name='uom_cost[]' value='"+data['uom'][i]['cost']+"'/></td>"+
								"<td style='text-align:right;'>"+data['uom'][i]['divmul']+"</td>"+
								//"<td style='text-align:left;'>"+data['details']['base_unit']+"</td>"+    //change on 8-3-14
								"<td style='text-align:left;'>"+data['new_base_unit']+"</td>"+
								"<td>per</td>"+
								"<td>"+data['uom'][i]['unit']+"</td>"+
								"<td><a href='javascript:uomSave("+(i+2)+",\""+data['details']['sku_id']+"\")'><img title='Save' src='./images/save.png' style='width:25px;height:25px;'/></a>"+
								"<a href='javascript:uomDel("+(i+2)+",\""+data['details']['sku_id']+"\")'><img title='Remove' src='./images/remove.png' style='width:25px;height:25px;'/></a></a></td>"+
							"</tr>";
					$("#tbluom").append(txt);
				}
			}
		},
	'json');
}
function uomInput(val,field,rowCount){
	var table = document.getElementById('tbluom');
	switch(field){
		case'bcode':
			table.rows[rowCount].cells[3].childNodes[0].focus();
		break;
		case'divmul':
			var base_cost = $("#cost").val();
			table.rows[rowCount].cells[2].childNodes[0].value=(base_cost * val).toFixed(2);
			table.rows[rowCount].cells[6].childNodes[0].focus();
		break;
		case'unit':
			table.rows[rowCount].cells[1].childNodes[0].focus();
		break;
	}
}
function uomDel(rowCount,sku){
	var table = document.getElementById('tbluom');
	var bcode = table.rows[rowCount].cells[0].childNodes[0].value;
	$.ajax({
		url: './content/pos_ajax.php?execute=uomDel',
		type:"POST",
		data:{sku:sku,bcode:bcode},
		success:function(data){
			if(data=="success"){
				//window.location=document.URL;
				window.location = "?page=prod_maintenance&sku="+sku;
			}
		}
	});
}
function uomSave(rowCount,sku){
	var table = document.getElementById('tbluom');
	var bcode = table.rows[rowCount].cells[0].childNodes[0].value;
	var price = table.rows[rowCount].cells[1].childNodes[0].value;
	var cost = table.rows[rowCount].cells[2].childNodes[0].value;
	if(parseInt(rowCount)==1){ //new entry
		var divmul = table.rows[rowCount].cells[3].childNodes[0].value;
		var unit = table.rows[rowCount].cells[6].childNodes[0].value;
		if(bcode !="" && price!="" && cost!="" && divmul!="" && unit!="" && divmul!=0){
			$.ajax({
				url: './content/pos_ajax.php?execute=uomSave',
				type:"POST",
				data:{sku:sku,bcode:bcode,price:price,cost:cost,divmul:divmul,unit:unit},
				success:function(data){
					if(data=="success"){
						//window.location=document.URL;
						//getBarcodeInfo("'"+bcode+"'");
						//window.location = "?page=prod_maintenance_whole&sku="+sku;
						//window.location=document.URL+"&sku="+sku;
						window.location=removeURLParameter(document.URL, 'sku');
					}else{
						alert(data);
					}
				}
			});
		}else{
			alert("Please fill-up all the info and per BaseUOM should not be zero!");
		}
	}else{
		var divmul = table.rows[rowCount].cells[3].innerHTML;
		var unit = table.rows[rowCount].cells[6].innerHTML;
		$.ajax({
			url: './content/pos_ajax.php?execute=uomSave',
			type:"POST",
			data:{sku:sku,bcode:bcode,price:price,cost:cost,divmul:divmul,unit:unit},
			success:function(data){
				if(data=="success"){
					//window.location=document.URL;
					//getBarcodeInfo(bcode);
					//window.location = "?page=prod_maintenance_whole&sku="+sku;
					//window.location=document.URL+"&sku="+sku;
					window.location=removeURLParameter(document.URL, 'sku');
				}
			}
		});
	}
}

function printingModal(path,title,height,width){
	if (window.showModalDialog) {
		window.showModalDialog(path,title,"dialogWidth:"+width+"px;dialogHeight:"+height+"px");
	} else {
		window.open(path,title,'height='+height+',width='+width+',toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
function delProdsale(val,type){
	var urls=arrayToUrl(getUrl());
	if(type=="barcode"){
		alert("Pls select item to delete...");return false;
	}
	var id = $("tbody tr.selected").find('td:eq(8)').text();
	$.ajax({
		url: './content/pos_ajax.php?execute=delprodsale'+urls,
		type:"POST",
		data: {ref:val,type:type,id:id},
		success:function(data){
			if(data=="success"){
				if(getParam('page')=="sales"){
					$("tr.selected").remove();
					$("#xtotal").html(sumTotalAmt);
				}
				// else{
					// window.location=document.URL;
				// }
				window.location=document.URL;
			}else{
				alert(data);
			}
		}
	});
	//$("#xtotal").html(sumTotalAmt);
}
function arrayToUrl(param){
	var txt='';
	$.each(param, function(key, val) {
	  if(val!=undefined){
		txt+='&'+key+'='+val;
	  }
	});
	return txt;
}
function loginPermission(type,param=null){
	$('#xlogin').dialog({
		autoOpen: false,
		width: 400,
		height: 170,
		modal: true,
		resizable: false,
		closeOnEscape: true,
		close:function(event){$('#barcode').focus();},
		title:'Need Permission',
		open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:0.96}); }
	});
	var addparam='';
	if(param){
		addparam= arrayToUrl(param);
	}
	htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=checkLoginCredentialFront&type_name='+type+addparam,async:false});
	$('#xlogin').html(htmlobj.responseText);
	$('#xlogin').dialog('open');
}
function backToBarcode(){
	location.reload();
}
function qtyclick(bcode,type,divmul){
	var newqty = prompt("Enter new qty");
	if(newqty !=""){
		if($.isNumeric(newqty)){
			if(bcode!=""){
				if(newqty<1){
					loginPermission('negativeQty',{bcode:bcode,newqty:newqty,type:type,divmul:divmul});
				}else{
					qtychange(bcode,newqty,type,divmul);
				}
			}
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new qty...");
	}
}
function qtychange(bcode,newqty,type,divmul){
	var sessiontype = '&sessiontype='+getParam('page');
	var url = './content/pos_ajax.php?execute=qtychange&barcode='+bcode+'&newqty='+newqty+sessiontype+'&divmul='+divmul;
	$.ajax({
		//url: './content/pos_ajax.php?execute=qtychange&barcode='+bcode+'&newqty='+newqty+'&type='+type,
		url:url,
		type:"POST",
		success:function(data){
			if(data=="success"){
				//window.location.reload();
				window.location=document.URL;
			}else{
				alert(data);
			}
		}
	});
}
function priceclick(bcode,type){
	var newprice = prompt("Enter new price");
	if(newprice !=""){
		if($.isNumeric(newprice)){
			if(bcode!=""){
			pricechange(bcode,newprice,type);
			}
		}else{
			alert("Please input number value only...");
		}
	}else{
		alert("Please provide new price...");
	}
}
function uomlist(val,type){
	/*if (window.showModalDialog) {
		window.showModalDialog('./content/uom_list.php?sku_id='+val+'&type='+type,"UOM List","dialogWidth:500px;dialogHeight:500px");
	}else{
		window.open('./content/uom_list.php?sku_id='+val+'&type='+type,"UOM List",'height=500,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=no');
	}*/
	clickDialog('dialogbox',400,400,'uom_list&sku_id='+val,'Product List');
}
//********pos function**********
function moneyxcount(){
	var money = $("#money").val();
	var count = $("#count").val();
	if(money !="" && count!=""){
		$("#total").val(parseFloat(money) * parseFloat(count));
	}
}
function signOut(){
	$.ajax({
		url: './content/pos_ajax.php?execute=signout',
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location='./';
				//window.location.reload();
			}
		}
	});
}
function loadiFrame(src){
	$("#iframeplaceholder").html("<iframe id='myiframe' name='myname' src='" + src + "' />");
}
function PrintIframe(change,payment_type) { 
	$("#myiframe").load( 
		function() {
			window.frames['myname'].focus();
			window.frames['myname'].print();
			window.frames['myname'].close();
			window.frames['myname'].onafterprint = function () {
				if(payment_type=="Cash"){
					alert(payment_type);
					displayChange(parseFloat(change));
				}else{
					//window.location.reload();
					window.location=document.URL;
				}
			}
		}
	 );
}
function prodAdd(){
	//var win=window.open('index.php?page=prod_maintenance_whole','_blank');
	//win.focus();
	
	var bcodeinfo = $(".navigateableMain tbody tr.selected").find('td:eq(0)').text()=="No Records Found..."?"":$(".navigateableMain tbody tr.selected").find('td:eq(0)').text();
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=prod_maintenance_whole&bcodeinfo='+bcodeinfo,"Prod Maintenance","dialogWidth:1200px;dialogHeight:550px");
	} else {
		window.open('index.php?page=prod_maintenance_whole&bcodeinfo='+bcodeinfo,"Prod Maintenance",'height=550,width=1200,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
	}
}
// function viewReport(page){
	// var reading = prompt("Enter Reading Num:");
	// if(reading !="" && reading != null){
		// if (window.showModalDialog) {
			// window.showModalDialog(page+reading,"RePrint Reports","dialogWidth:500px;dialogHeight:500px");
		// } else {
			// window.open(page+reading,"RePrint Reports",'height=500,width=500,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
		// }
	// }else{
		// alert("Please provide Reading Num...");
	// }
// }
function viewReport(page){
	if (window.showModalDialog) {
		var dialogFeatures = 'center:yes; dialogWidth:1000px; location:no; dialogHeight:500px; edge:raised; help:no; resizable:no; scroll:no; status:no; statusbar:no; toolbar:no; menubar:no; addressbar:no; titlebar:no;';
		window.showModalDialog(page,"View Report",dialogFeatures);
	} 
	else {
		window.open(page,"View Report",'height=500,width=900,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes');
	}
}
function LoadAjaxContent(url){
	$.ajax({
		mimeType: 'text/html; charset=utf-8', // ! Need set mimeType only when run from local file
		url: url,
		type: 'GET',
		success: function(data) {
			$('#dialogbox').html(data);
		},
		error: function (jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		dataType: "html",
		async: false
	});
}

function viewRecordsPackages(){
	var urls = getUrl();
	$("#prodlist").html("");
	clickDialog('prodlist',1000,550,'packageslist','Package List',urls);
	jQuery.tableNavigation();
	$("#search_prodname").focus();
}
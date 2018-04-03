function lookupPatient(inputString){
	var tblsource=$("select[name='rec_tbl'] option:selected").val();
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#autoSuggestionsList').hide();
	} else {
		console.log("Looking in the database...");
		$.post("./content/pos_ajax.php?execute=patientsearch&tblsource="+tblsource, {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#autoSuggestionsList').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
}


function lookup(inputString) {
	var type=$('input[name="gradelevel"]:checked').val();
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#autoSuggestionsList').hide();
	} else {
		$.post("./content/pos_ajax.php?execute=studentsearch&type="+type, {queryString: ""+inputString+""}, function(data){
			if(data.length >0) {
				$('#autoSuggestionsList').show();
				$('#autoSuggestionsList').html(data);
			}
		});
	}
} // lookup

function fill(id,name,course,yr) {
	$("#idnum").val(id);
	$("#sname").val(name);
	$("#course").val(course);
	$("#yr").val(yr);
	setTimeout("$('#autoSuggestionsList').hide();", 200);
}
function fillpatientInfo(id,tblsource){
	var url = './content/pos_ajax.php?execute=patientInfo&idno='+id+'&tblsource='+tblsource;
	$.ajax({
		url: url,
		type:"POST",
		dataType:"json",
		success:function(data){
			$("input[name='idnum']").val(data['idno']);
			$("input[name='first_name']").val(data['first_name']);
			$("input[name='last_name']").val(data['last_name']);
			$("input[name='middle_name']").val(data['middle_name']);
			$("input[name='birth_date']").val(data['birth_date']);
			$("select[name='gender']").val(data['gender']);
			$("input[name='address']").val(data['address']);
			setTimeout("$('#autoSuggestionsList').hide();", 200);
		}
	});
}
function addslashes(str) {
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}
function str_replace (search, replace, subject, count) {
        j = 0,
        temp = '',
        repl = '',
        sl = 0,        fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    } 
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}
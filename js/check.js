function checkall(form) {
	$('input[name="allowstr[]"]').attr("checked",true);
}
function checkother(form) {
	$('input[name="allowstr[]"]').attr("checked",false);

}

function checkallmember() {
	$('input[name="memberstr[]"]').attr("checked",true);
}
function checkothermember() {
	$('input[name="memberstr[]"]').attr("checked",false);

}

function checker(n,m){
	for (var i=0;i<m;i++ ){
		document.getElementById(n+i).checked = document.getElementById(n).checked;
	}
}
function checkerd(m){
		document.getElementById(m).checked = true;
}
//
function select_all(obj,cName){
	var checkboxs = document.getElementsByName(cName);
    for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
}
function select_all(frm)
{
  var i,slength=0;
  if (frm.sel_id==null) { return false; }
  var sall=frm.sel_all.checked;
  if (frm.sel_id.length)
  {
    slength=frm.sel_id.length;
    for (i=0;i<slength;i++) { frm.sel_id[i].checked=sall; }
  }
  else
  { frm.sel_id.checked=sall; }
}

function sel_click(frm)
{
  var i,slength=0,issel=false;
  if (frm.sel_id!=null)
  {
    if (frm.sel_id.length)
    {
      slength=frm.sel_id.length;
      for (i=0;i<slength;i++) {
		  if (frm.sel_id[i].checked==true) { 
			  issel=true;
			  frm.sel_ids.value = frm.sel_id[i].value;
			  for (j=i+1; j < slength ; j++ ){
				if (frm.sel_id[j].checked==true) {
					frm.sel_ids.value += ","; //用空格做分割符 
					frm.sel_ids.value += frm.sel_id[j].value; 
				}
			  }
			  break;
		  }
	  }
    }
    else
    { if (frm.sel_id.checked==true) { frm.sel_ids.value = frm.sel_id.value;issel=true; } }
  }
  if (issel==true)
  {
    var cf=window.confirm("执行此操作后可能无法恢复！你确定吗？");
    if (cf)
    { return true; }
    else
    { return false; }
  }
  else
  {
    alert("没有选择任何记录！");
    return false;
  }
}
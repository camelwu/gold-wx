//
var msg_id = 0;//默认为0，1开始有效，计算是第几个共几个。
var msg_tit = ' <div class="appmsg_title"><a href="javascript:void(0);" onclick="return false;" target="_blank">标题</a></div>';
var msg_img = '<div class="appmsg_thumb_wrp"><img src="css/images/nopic.jpg"></div>';
var json = [];//获得的json
var msg = [];//选中的数据
var ctype=$("select[name='ctype']");
//ctype=ctype?ctype.val():1;
var types=$("select[name='types']");
var keys=$("input[name='keyword']");
var html=$("#html");
var content=$("textarea[name='content']");
var title=$("input[name='title']");
function search_req(){
if(msg_id>0){
	$.getJSON("search.php?action=getmsg&ctype="+ctype.val()+"&keys="+keys.val(), function(data){
		if(data&&data.length>0){
			json[msg_id] = data;
			var cstr = '';
			$.each(data, function(i,item){
				cstr += '<input type="radio" name="rid" value="'+item.id+'" id="'+i+'" data-name="'+item.title+'" data-url="'+item.url+'" data-price2="'+item.price2+'"><label for="'+i+'">'+item.title+' </label><br>';
				//cstr += '<input type="checkbox" name="cid[]" value="'+item.id+'" id="'+item.id+'" data-name="'+item.title+'" data-url="'+item.url+'" data-price2="'+item.price2+'"><label for="'+item.id+'">'+item.title+' <br>';
			});
			opentip(cstr,'_sel_check();');
		}else{
			alert("未查询到“"+keys.val()+"”相关的数据！");
		}
	});
}else{
	alert("请先在左侧添加！");//_sel_msg();
}
}
//
function _sel_msg(id){//如有id，是否设定为编辑？
	if(typeof(id)==undefined || id==undefined){
		var id = msg_id==0?1:msg_id+1;
	}else{
		id = parseInt(id);
	}
	if(msg_id<10){
	top_dom=document.getElementById("news");
	add_dom=document.getElementById("add_item");
	if(!document.getElementById("news0"+id)){
		msg_id = id;
	}else{
		id+=1;
		msg_id = id;
	}
	var oDiv = document.createElement('div');
		oDiv.id = "news0"+id;
		oClass = id==1?"first_item":"normal_item";
		oDiv.className = oClass;
		msg_mask = id==1?'<div class="appmsg_edit_mask"><a data-id="1" onclick="_sel_edit(this);" href="javascript:void(0);">编辑</a></div>':'<div class="appmsg_edit_mask"><a data-id="'+id+'" onclick="_sel_edit(this);" href="javascript:void(0);">编辑</a><a data-id="'+id+'" onclick="_sel_del('+id+');" href="javascript:void(0);">删除</a></div>';
		oDiv.innerHTML = msg_tit+msg_img+msg_mask;
		top_dom.insertBefore(oDiv,add_dom);
	
	}else{
		alert('最多9条信息！');
	}
	document.getElementById("tab_td").innerHTML = ' 编辑第'+id+'条图文消息';
	_sel_show(0);
}
//checksel
function _sel_check(){
	var str="";
	//复选框
	/*var checkboxs = document.getElementsByName("cid[]");
    for(var i=0;i<checkboxs.length;i++){
		if(checkboxs[i].checked) str+=checkboxs[i].value+",";
	}
	if(str.substr(-1)==',') str = str.substring(0,str.length-1);*/
	//单选框
	var n = -1;
	var checkboxs = document.getElementsByName("rid");
	for(var i=0;i<checkboxs.length;i++){
		if(checkboxs[i].checked){
			n = i;
			msg[msg_id] = json[msg_id][i];
			var ids = msg_id<10?"0"+msg_id:msg_id;
			//左侧处理
			$("#news"+ids+" .appmsg_title a").html(checkboxs[i].getAttribute("data-name"));
			$("#news"+ids+" img").attr("src",checkboxs[i].getAttribute("data-url"));
			//$("#news"+ids+" .appmsg_edit_mask a").attr("data-id",checkboxs[i].value);
			break;
		}
	}
	_sel_show(msg_id);
}
function _sel_show(id){//右侧处理
	if(id!=0){
		$("#title").val(msg[id].title);
		$("#note").val(msg[id].feature);
		$("#content").val(msg[id].stroke);
		$("#mid").text(msg[id].id);
		$("#price").text(msg[id].price2);
	}else{
		$("#title").val('');
		$("#note").val('');
		$("#content").val('');
		$("#mid").text('');
		$("#price").text('');
	}
}
function _sel_save(){
	var id = parseInt($("#mid").text());
	msg[id]["title"] = $("#title").val();
	msg[id]["feature"] = $("#note").val();
	msg[id]["stroke"] = $("#content").val();
}
function _sel_edit(e){
	var _id = e.getAttribute("data-id");
	_sel_show(_id);
}
function _sel_del(id){
	var node = document.getElementById("news0"+id);
	if (node){
		node.parentNode.removeChild(node);
		for(var i=id+1;i<=msg_id;i++){
			document.getElementById("news0"+i).setAttribute("id",i-1);
		}
		msg.splice(id,1)
		msg_id = msg_id-1;
	}
	document.getElementById("tab_td").innerHTML = ' 删除一条图文消息';
	_sel_show(0);
}
function sub_frm(frm){
	msg.splice(0,1);
	for(var i=0;i<msg.length;i++){
		var mydata = {"id":msg[i].id,"title":msg[i].title,"url":msg[i].url,"price2":msg[i].price2,"feature":msg[i].feature,"stroke":msg[i].stroke};
		$.ajax({
		type:"POST",
		url: "./admin_gallery.php?action=pic",
		async:false,
		data: mydata,
		dataType:"json",
		context:document.getElementById("add_frm"),
		success: function(data){
    		//$(this).addClass("done");
		}
	});
}
	return false;
}

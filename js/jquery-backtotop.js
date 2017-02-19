
$(function(){
	$(window).scroll(function(){  //只要窗口滚动,就触发下面代码 
		var scrollt = document.documentElement.scrollTop + document.body.scrollTop; //获取滚动后的高度 
		if( scrollt >200 ){  //判断滚动后高度超过200px,就显示  
			$("#gotop").fadeIn(400); //淡出     
		}else{      
			$("#gotop").stop().fadeOut(400); //如果返回或者没有超过,就淡入.必须加上stop()停止之前动画,否则会出现闪动   
		}
	});
	$("#gotop").click(function(){ //当点击标签的时候,使用animate在200毫秒的时间内,滚到顶部
			$("html,body").animate({scrollTop:"0px"},200);
	});
});
/**/
//function
function isNum(str){
	var reg = /^[0-9]*$/;
	return reg.test(str);
}
function isTel(tel){
	var reg = /^[\d|\-|\s|\_]+$/;//只允许使用数字-空格等
	return reg.test(tel);
}
function ismail(str){
	var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
	return reg.test(str);
}
function searchFocus(dm){
	dm.value==dm.title?dm.value="":null;
}
function searchBlur(dm){
	dm.value==""?dm.value=dm.title:null;
}
function format_number(n){
	var b=parseInt(n).toString();
	var len=b.length;
	if(len<=3){return b;}
	var r=len%3;
	return r>0?b.slice(0,r)+","+b.slice(r,len).match(/\d{3}/g).join(","):b.slice(r,len).match(/\d{3}/g).join(",");
}
//shopping cart
var cart=[];
var tab_all=0;
var tab_begin='<tbody><tr><td class="td5" colspan="6"><img style="vertical-align:middle;float:left;padding:5px 10px 0 0;" src="images/icon_cart.png"> Your Shopping Cart</td></tr><tr class="tr2"><td width="22%">CATALOG No.</td><td width="30%">PRODUCT NAME</td><td width="12%">PRICE</td><td width="12%">QUANTITY</td><td width="12%">SUBTOTAL</td><td></td></tr>';
function add2cart(id){
	var str=$.cookie('cart')==null||$.cookie('cart')== undefined?'':$.cookie('cart');
	for(var i=1;i<4;i++){
		if($("#select"+i).val()!=0){
			str+=str==""?"":"#&";
			str+=id+"@"+$("#catalog").val()+"@"+$("#name").val()+"@"+$("#price"+i).val()+"@"+$("#select"+i).val();
		}
	}
	if(str){$.cookie('cart',str);var cache=str.split("#&");alert("u had add to cart!");document.getElementById("cart_num").innerHTML=cache.length;}else{alert("Please select quantity!");}
}
function del2cart(id){
	var str=$.cookie('cart')==null||$.cookie('cart')== undefined?'':$.cookie('cart');
	if(isNum(id)&&str){
		cart.splice(id,1);
		$.cookie('cart', cart.join("#&"));
		$("#tr"+id).fadeOut();
		document.getElementById("cart_num").innerHTML=cart.length;
		showcart();
	}else{
		alert('error!');
	}
}
function recount(id,val){
	var oprice=$("#price"+id).html().substring(1);
	oprice = parseInt(oprice.replace(/[,'"]/g,""));
	var nprice = parseInt($("#p"+id).val()*val);
	$("#price"+id).html("$"+format_number(nprice)+".00");
	var tprice = $("#price").html().substring(1);
	tprice = parseInt(tprice.replace(/[,'"]/g,""));
	$("#price").html("$"+format_number(tprice-oprice+nprice)+".00");
	//
	var cache = cart[id].split("@");
	var cach ="";
	for(var i=0;i<cache.length-1;i++){
		cach+=cache[i]+"@";
	}
	cart[id]=cach+val;
	$.cookie('cart', cart.join("#&"));
}
function showcart(){
	var cstr='',tab_all=0;
	cart = $.cookie('cart').split("#&");
	if(cart.length>0){//data
		for(var i=0;i<cart.length;i++){
			if(cart[i]){
			var cache = cart[i].split("@");
			cstr+='<tr class="tr3" id="tr'+i+'"><td>'+cache[1]+'</td><td>'+cache[2]+'</td><td>$'+cache[3]+'g</td><td><select name="select'+i+'" id="select'+i+'" onchange="recount('+i+',this.value);">';
			for(var j=1;j<=10;j++){
				var check=j==parseInt(cache[4])?' selected="selected"':'';
				cstr+='<option value="'+j+'"'+check+'>'+j+'</option>';
			}
			var cprice=parseInt(cache[3].substring(0,cache[3].indexOf("/")))*cache[4];//
			cstr+='</select><input type="hidden" value="'+parseInt(cache[3].substring(0,cache[3].indexOf("/")))+'" name="p'+i+'" id="p'+i+'"><input type="hidden" value="$'+format_number(cprice)+'.00" name="price01" id="price01" /></td><td width="12%" id="price'+i+'">$'+format_number(cprice)+'.00</td><td><a href="javascript:void(0);" onclick="del2cart('+i+');">remove</a></td></tr>';
			tab_all+=cprice;
			}
		}
var tab_end='<tr class="tr4"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>Total</td><td id="price">$ '+format_number(tab_all)+'.00</td><td>&nbsp;</td></tr></tbody>';
		document.getElementById("tab1").innerHTML=tab_begin+cstr+tab_end;
		//$("#tab1").html(tab_begin+cstr+tab_end);
	}
}
$(document).ready(function() {
	var links = [],i=-1,hrefstr=decodeURI(document.location.href),host = window.location.host;
	var para = hrefstr.substring(7+host.length)
	var aryc = para.split("/");
	links[0]=$(".nav_ul a");links[1]=$(".nav_left a");
	if($(".nav_porperty_ul")){links[2]=$(".nav_porperty_ul a");}
	if(para.indexOf("/")<0||aryc[aryc.length-1]==""||aryc[aryc.length-1]==null||aryc[aryc.length-1]==undefined){//home page
		links[0][0].parentNode?links[0][0].parentNode.className = links[0][0].parentNode.className+"a":null;
	}else{
		userloop:
		for(var j=0;j<links.length;j++){
			for(var k=0;k<links[j].length;k++){
				if(hrefstr==links[j][k].href){
					if(j<2)
					links[j][k].parentNode?links[j][k].parentNode.className = links[j][k].parentNode.className+"a":null;
					else//产品列表页，特殊处理
					links[j][k].parentNode?links[j][k].parentNode.className = "nav_pro_lia":null;
					break userloop;
				}
			}
		}
	}
	//cookie&cart
	if($.cookie('cart')){
		cart = $.cookie('cart').split("#&");
		//document.getElementById("cart_num").innerHTML=cart.length;
	}else{
		//document.getElementById("cart_num").innerHTML=0;
	}
	if(aryc[aryc.length-1]=="product_cart.php"&&$.cookie('cart')) showcart();
	//if(aryc[aryc.length-1]=="product_view.php") alert("ok");
});
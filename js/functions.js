window.onerror=function(a,b,c){return true;}

var openview=true;

function open_win(url,name,width,height,scroll){
  var Left_size = (screen.width) ? (screen.width-width)/2 : 0;
  var Top_size = (screen.height) ? (screen.height-height)/2 : 0;
  var open_win=window.open(url,name,'width=' + width + ',height=' + height + ',left=' + Left_size + ',top=' + Top_size + ',toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=' + scroll + ',resizable=no' );
}

function jk_insert_em(emvar,em_frm,em_word)
{
  var obj_tmp=eval("document."+em_frm+"."+em_word);
  obj_tmp.value+=emvar;
  obj_tmp.focus();
}

function login_true()
{
  if (document.login_frm.username.value=="") { alert("���������ڱ�վע��ʱ�� �û����� ��"); document.login_frm.username.focus(); return false; }
  if (document.login_frm.password.value=="") { alert("���������ڱ�վע��ʱ�� ��½���� ��"); document.login_frm.password.focus(); return false; }
}

function picsize(obj,MaxWidth){
  img=new Image();
  img.src=obj.src;
  if (img.width>MaxWidth)
  {
    return MaxWidth;
  }
  else
  {
    return img.width;
  }
}

function bbimg(o){
 var zoom=parseInt(o.style.zoom, 10)||100;zoom+=event.wheelDelta/12;if (zoom>0) o.style.zoom=zoom+'%';
 return false;
}

function viewPage(ipage){
	window.location="?page="+ipage;
}

function change_class(m,n){
if (n==1)
	{eval('document.all.'+m+'.className="IMG2"')
	}
else
	{eval('document.all.'+m+'.className="IMG1"')
	}
}
function changeimg(n,m,s)
{
	if (n=="1"){
	eval('document.all.i'+m+'.src="'+s+'"')
	}
	else
	{
	eval('document.all.i'+m+'.src="'+s+'"')
	}
}

function vtrue(vvar)
{
  var tmp1=false;
  if (vvar=="True" || vvar=="true") { tmp1=true; }
  return tmp1;
}

function vnull(vvar)
{
  var tmp1=true;
  if (vvar=="" || vvar==null) { tmp1=false; }
  return tmp1;
}

function click_return(cvar,ct)
{
  var nvar='';
  switch (ct)
  {
    case 1:
      nvar=cvar;
      break;
    default :
      nvar='��ȷ��Ҫ'+cvar+'��\n\nִ�иò����󽫲��ɻָ���';
      break;
  }
  var cf=window.confirm(nvar);
  if (cf) { return true; }
  return false;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_changeProp(objName,x,theProp,theValue) { //v6.0
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)){
    if (theValue == true || theValue == false)
      eval("obj."+theProp+"="+theValue);
    else eval("obj."+theProp+"='"+theValue+"'");
  }
}

function format_now_time()
{
  var d_time=new Date();
  var week_dim=new Array("������","����һ","���ڶ�","������","������","������","������");
  //var week_dim=week_dim.concat("������","������","������");
  var temp1="�����ǣ�";
  temp1+=d_time.getYear()+"��";
  temp1+=(d_time.getMonth() + 1) + "��";
  temp1+=d_time.getDate() + "��&nbsp;";
  temp1+=week_dim[d_time.getDay()]
  temp1+="&nbsp;&nbsp;";
  return temp1;
}

function frm_submitonce(theform)
{
  if (document.all||document.getElementById)
  {
    for (i=0;i<theform.length;i++)
    {
      var tempobj=theform.elements[i]
      if(tempobj.type.toLowerCase()=="submit"||tempobj.type.toLowerCase()=="reset") { tempobj.disabled=true; }
    }
  }
}

function frm_quicksubmit(eventobject)
{
  if (event.keyCode==13 && event.ctrlKey)
  {
    frm_submitonce(eval("document.write_frm"));
    write_frm.submit();
  }
}

function open_view(ourl,ot)
{
  if (openview==true)
  { window.open(ourl); }
  else
  { document.location.href=""+web_dir+ourl+""; }
}

function play(ourl)
{
document.write('<object name="wmplayer" classid=CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95 width=0 height=0 codebase=http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701 id=MIDIPlayer type=application/x-oleobject VIEWASTEXT standby="���� Microsoft Windows Media Player ���...">');
  document.write('<param name="FileName" value="'+ourl+'">');
  document.write('<param name="AutoStart" value="true">');
  document.write('<param name="AutoRewind" value="-1">');
  document.write('<param name="AnimationAtStart" value="false">');
  document.write('<param name="ShowControls" value="false">');
  document.write('<param name="ClickToPlay" value="false">');
  document.write('<param name="EnableContextMenu" value="true">');
  document.write('<param name="EnablePositionControls" value="false">');
  document.write('<param name="Balance" value="0">');
  document.write('<param name="ShowStatusBar" value="false">');
  document.write('<param name="AutoSize" value="0">');
  document.write('<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/" filename src  autostart="false" enablecontextmenu="false" clicktoplay="false" enablepositioncontrols="false" showcontrols="0" showstatusbar="1" showdisplay="0">');
  document.write('</embed>');
document.write('</object>');
}
//-------------------------------------�ַ�����--------------------------------------
function code_html(vars,chtype,cutenum)
{
  if (vars==null || vars=="") { return(""); }
  var strer=vars;
  //strer=health_var(strer,1)
  if (cutenum>0) { strer=cuted(strer,cutenum); }
  // / &#47;
  if (chtype>10)
  {
    strer=strer.replace(/\'/gi,"\'")		//������&#39;
    strer=strer.replace(/\"/gi,"\"")		//˫����&#34;
  }
  else
  {
    strer=strer.replace(/</gi,"&lt;")		//&#60;
    strer=strer.replace(/>/gi,"&gt;")		//&#62;
    strer=strer.replace(/\'/gi,"&#39;")		//������&#39;
    strer=strer.replace(/\"/gi,"&quot;")	//˫����&#34;
    strer=strer.replace(/ /gi,"&nbsp;")		//�ո�
  }
  switch (chtype)
  {
    case 1,11:
      strer=strer.replace(/\r/gi,"")		//table
      strer=strer.replace(/\n/gi,"")		//�س�
    case 2,22:
      strer=strer.replace(/\r/gi,"����")	//table
      strer=strer.replace(/\n/gi,"<br>")	//�س�
  }
  return(strer);
}

//------------------------------------�ַ��ָ�--------------------------------------
function cuted(vars,num)
{
  if (vars==null || vars=="") { return(""); }
  var ctypes=vars;
  var cnum=num;
  var cute_d="";
  var tc=0,cc=0,c_mod=2;
  for (ci=1;ci<=ctypes.length;ci++)
  {
    if (cnum<0) { cute_d=cute_d+"..."; break; }
    tt=ctypes.charCodeAt(ci-1);
    if (tt>696 || tt<0)
    {
      cnum-=1;
      if (cnum<0) { cute_d+="..."; break; }
      cute_d+=ctypes.substring(ci-1,ci);
    }
    else
    {
      cc+=1;
      if (cc>c_mod && c_mod>0)
      {
        tc+=1;
        cc=0;
      }
      tc+=1;
      if (tc>1)
      {
        tc=0;
        cnum-=1;
        if (cnum<0) { cute_d+="..."; break; }
      }
      cute_d+=ctypes.substring(ci-1,ci);
    }
  }
  return(cute_d);
}

function sel_mod_num(n1,n2)
{
  var ni=0;
  var nn1=n2/n1;
  var nn2=Math.ceil(nn1);
  if (nn1==nn2) { return true; }
  return false;
}

function time_diff(dt,fir_tim,last_tim)
{
  var a=fir_tim.replace(/-/gi,"/");
  var b=last_tim.replace(/-/gi,"/");
  var aa=Date.parse(a);
  var bb=Date.parse(b);
  var ta=bb-aa;
  switch (dt)
  {
    case "h":
      ta=ta/360000;
      break;
    case "m":
      ta=ta/60000;
      break;
    case "s":
      ta=ta/1000;
      break;
  }
  return ta;
}

function url_encode(vars)
{
  if (js_ver==true)
  { return(encodeURI(vars)); }
  else
  { return(vars); }
}

function isCharsInBag(inputchar, bagchar)
{ 
  var ii,cc;
  for (ii = 0; ii < inputchar.length; ii++)
  { 
    cc = inputchar.charAt(ii);
    if (bagchar.indexOf(cc) > -1) { return "no"; }
    else { return "yes"; }
  }
}
function check(write_frm)
{
  var errorcharname="><,[>{}?/+=|\\'\":;~!#$%()`@";
  var errorcharqq="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz><,[>{}?/+=|\\'\":;~!#$%()`";
  var wr_name=write_frm.wrname.value;
  if(wr_name=="" || wr_name==null)
  {
    alert("�㻹û��ȫ����������Ϣ��\r\n��� ���� �Ǳ���Ҫ�ġ�");
    document.write_frm.wrname.focus();
    return false;
  }
  var errorname=isCharsInBag(wr_name,errorcharname);
  if ( errorname=="no" )
  {
    alert("��� ���� ���ú��������ַ���\r\n  ><,[{}?/+=|\\'\":;~!#$%()`");
    document.write_frm.wrname.focus();
    return false;
  }
  var wr_qq=write_frm.wrqq.value;
  if(write_frm.wrqq.value!=="")
  {
    var errorqq=isCharsInBag(wr_qq,errorcharqq);
    if ( errorqq=="no" )
    {
      alert("��� �绰ֻ�������֣�\r\n���������롣");
      document.write_frm.wrqq.focus();
      return false;
    }
  }
  var wr_email=write_frm.wremail.value;
  if(wr_email!=="")
  {
    var AtSym = wr_email.indexOf('@');
    var Period = wr_email.lastIndexOf('.');
    var Space = wr_email.indexOf(' ');
    var Length = wr_email.length - 1;
    if ((AtSym < 1) || (Period <= AtSym+1) || (Period == Length ) || (Space != -1))
    { 
      alert('��� eMail��ַ ��ʽ���ԣ�\r\n���������롣');
      document.write_frm.wremail.focus();
      return false;
    } 
  }
  var wr_whe=write_frm.wrwhe.value;
  if(wr_whe!=="")
  {
    var errorwhe=isCharsInBag(wr_whe,errorcharname);
    if ( errorwhe=="no" )
    {
      alert("��� ���� ���ú��������ַ���\r\n  ><,[{}?/+=|\\'\":;~!#$%()`");
      document.write_frm.wrwhe.focus();
      return false;
    }
  }
  var wr_topic=write_frm.wrtopic.value;
  if(wr_topic=="")
  {
    alert("�㻹û��ȫ����������Ϣ��\r\n��� ���� �Ǳ���Ҫ�ġ�");
    document.write_frm.wrtopic.focus();
    return false;
  }
  var wr_word=write_frm.wrword.value;
  if(wr_word=="")
  {
    alert("�㻹û��ȫ����������Ϣ��\r\n��� �������� �Ǳ���Ҫ�ġ�");
    document.write_frm.wrword.focus();
    return false;
  }
}
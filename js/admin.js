<!--
function re_load() { location.reload(); }

function click_return(cvar,ct)
{
  var nvar='';
  switch (ct)
  {
    case 1:
      nvar='提示信息！';
    default :
      nvar='您确定要'+cvar+'吗？\n\n执行该操作后将不可恢复！';
  }
  var cf=window.confirm(nvar);
  if (cf) { return true; }
  return false;
}

function open_win(url,name,width,height,scroll)
{
  var Left_size = (screen.width) ? (screen.width-width)/2 : 0;
  var Top_size = (screen.height) ? (screen.height-height)/2 : 0;
  var open_win=window.open(url,name,'width=' + width + ',height=' + height + ',left=' + Left_size + ',top=' + Top_size + ',toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=' + scroll + ',resizable=no' );
}

//***********默认设置定义.*********************
var tPopWait=50;    //停留tWait豪秒后显示提示。
var tPopShow=6000;    //显示tShow豪秒后关闭提示
var showPopStep=20;
var popOpacity=95;
var tfontcolor="#000000";
var tbgcolor="#ededed";
var tbordercolor="#808080";

//***************内部变量定义*****************
var sPop=null,curShow=null,tFadeOut=null,tFadeIn=null,tFadeWaiting=null;

document.write("<style type='text/css'id='defaultPopStyle'>");
document.write(".cPopText {  background-color: " + tbgcolor + ";color:" + tfontcolor + "; border: 1px " + tbordercolor + " solid;font-color: font-size: 12px; padding-right: 4px; padding-left: 4px; height: 20px; padding-top: 2px; padding-bottom: 2px; filter: Alpha(Opacity=0)}");
document.write("</style>");
document.write("<div id='dypopLayer' style='position:absolute;z-index:1000;' class='cPopText'></div>");

function showPopupText()
{
  var o=event.srcElement;
  MouseX=event.x;
  MouseY=event.y;
  if(o.alt!=null && o.alt!=""){o.dypop=o.alt;o.alt=""};
  if(o.title!=null && o.title!=""){o.dypop=o.title;o.title=""};
  if(o.dypop!=sPop)
  {
    sPop=o.dypop;
    clearTimeout(curShow);
    clearTimeout(tFadeOut);
    clearTimeout(tFadeIn);
    clearTimeout(tFadeWaiting);  
    if(sPop==null || sPop=="")
    {
      dypopLayer.innerHTML="";
      dypopLayer.style.filter="Alpha()";
      dypopLayer.filters.Alpha.opacity=0;  
    }
    else
    {
      if(o.dyclass!=null) popStyle=o.dyclass 
      else popStyle="cPopText";
      curShow=setTimeout("showIt()",tPopWait);
    }
  }
}

function showIt()
{
  dypopLayer.className=popStyle;
  dypopLayer.innerHTML=sPop;
  popWidth=dypopLayer.clientWidth;
  popHeight=dypopLayer.clientHeight;
  if(MouseX+12+popWidth>document.body.clientWidth) popLeftAdjust=-popWidth-24
    else popLeftAdjust=0;
  if(MouseY+12+popHeight>document.body.clientHeight) popTopAdjust=-popHeight-24
    else popTopAdjust=0;
  dypopLayer.style.left=MouseX+12+document.body.scrollLeft+popLeftAdjust;
  dypopLayer.style.top=MouseY+12+document.body.scrollTop+popTopAdjust;
  dypopLayer.style.filter="Alpha(Opacity=0)";
  fadeOut();
}

function fadeOut(){
  if(dypopLayer.filters.Alpha.opacity<popOpacity)
  {
    dypopLayer.filters.Alpha.opacity+=showPopStep;
    tFadeOut=setTimeout("fadeOut()",1);
  }
  else
  {
    dypopLayer.filters.Alpha.opacity=popOpacity;
    tFadeWaiting=setTimeout("fadeIn()",tPopShow);
  }
}

function fadeIn()
{
  if(dypopLayer.filters.Alpha.opacity>0)
  {
    dypopLayer.filters.Alpha.opacity-=1;
    tFadeIn=setTimeout("fadeIn()",1);
  }
}

document.onmouseover=showPopupText;
-->

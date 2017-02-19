
var defmode="advmode";		//默认模式，可选 normalmode, advmode, 或 helpmode
var ubb_w=450;
var ubb_h=350;
var ubb_name="UBB代码 - ";

if (defmode == "advmode")
{ helpmode = false; normalmode = false; advmode = true; }
else if (defmode == "helpmode")
{ helpmode = true; normalmode = false; advmode = false; }
else
{ helpmode = false; normalmode = true; advmode = false; }

function jk_ubb_mode(swtch)
{
  if (swtch == 1)
  {
    advmode = false; normalmode = false; helpmode = true;
    alert(ubb_name+"帮助信息\n\n点击相应的代码按钮即可获得相应的说明和提示");
  }
  else if (swtch == 0)
  {
    helpmode = false; normalmode = false; advmode = true;
    alert(ubb_name+"直接插入\n\n点击代码按钮后不出现提示即直接插入相应代码");
  }
  else if (swtch == 2)
  {
    helpmode = false; advmode = false; normalmode = true;
    alert(ubb_name+"提示插入\n\n点击代码按钮后出现向导窗口帮助您完成代码插入");
  }
}

function storeCaret (textEl)
{ if(textEl.createTextRange){ textEl.caretPos = document.selection.createRange().duplicate();} }

function insertAtCaret (textEl, text)
{
  if (textEl.createTextRange && textEl.caretPos)
  {
    var caretPos = textEl.caretPos;
    caretPos.text += caretPos.text.charAt(caretPos.text.length - 2) == ' ' ? text + ' ' : text;
  }
  else if(textEl)
  { textEl.value += text; }
  else
  { textEl.value = text; }
}

function jk_ubb_email()
{
  if (helpmode)
  { alert(ubb_name+"插入邮件地址\n\n插入邮件地址连接！\n例如：\n[email]joekoe@etang.com[/email]\n[email=joekoe@etang.com]Joekoe[/email]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[email]" + range.text + "[/email]"; }
  else if (advmode)
  { AddTxt="[email][/email]"; AddText(AddTxt); }
  else
  { 
    txt2=prompt(ubb_name+"请输入链接显示的文字，如果留空则直接显示邮件地址！",""); 
    if (txt2!=null)
    {
      txt=prompt(ubb_name+"请输入邮件地址！例：joekoe@etang.com","");      
      if (txt!=null)
      {
        if (txt2=="")
        { AddTxt="[email]"+txt+"[/email]"; }
        else
        { AddTxt="[email="+txt+"]"+txt2+"[/email]"; } 
        AddText(AddTxt);
      }
    }
  }
}

function jk_ubb_size(size)
{
  if (helpmode)
  { alert(ubb_name+"设置字号\n\n将标签所包围的文字设置成指定字号！\n例如：[size=3]文字大小为 3[/size]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[size=" + size + "]" + range.text + "[/size]"; }
  else if (advmode)
  { AddTxt="[size="+size+"][/size]"; AddText(AddTxt); }
  else
  {           
    txt=prompt(ubb_name+"请输入要设置为字号 "+size+" 的文字！","文字"); 
    if (txt!=null) { AddTxt="[size="+size+"]"+txt; AddText(AddTxt); AddText("[/size]"); }  
  }
}

function jk_ubb_font(font)
{
  if (helpmode)
  { alert(ubb_name+"设定字体\n\n将标签所包围的文字设置成指定字体！\n例如：[face=仿宋]字体为仿宋[/face]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[face=" + font + "]" + range.text + "[/face]"; }
  else if (advmode)
  { AddTxt="[face="+font+"][/face]"; AddText(AddTxt); }
  else
  {      
    txt=prompt(ubb_name+"请输入要设置成 "+font+" 的文字！","文字");
    if (txt!=null) { AddTxt="[face="+font+"]"+txt; AddText(AddTxt); AddText("[/face]"); }  
  }
}


function jk_ubb_bold()
{
  if (helpmode)
  { alert(ubb_name+"插入粗体文本\n\n将标签所包围的文本变成粗体！\n例如：[b]乔客在线 Joekoe.com[/b]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[b]" + range.text + "[/b]"; }
  else if (advmode)
  { AddTxt="[b][/b]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"请输入要设置成粗体的文字！","文字");     
    if (txt!=null) { AddTxt="[b]"+txt; AddText(AddTxt); AddText("[/b]"); }       
  }
}

function jk_ubb_italicize()
{
  if (helpmode)
  { alert(ubb_name+"插入斜体文本\n\n将标签所包围的文本变成斜体！\n例如：[i]乔客在线 Joekoe.com[/i]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[i]" + range.text + "[/i]"; }
  else if (advmode)
  { AddTxt="[i][/i]"; AddText(AddTxt); }
  else
  {   
    txt=prompt(ubb_name+"请输入要设置成斜体的文字！","文字");     
    if (txt!=null) { AddTxt="[i]"+txt; AddText(AddTxt); AddText("[/i]"); }         
  }
}

function jk_ubb_quote()
{
  if (helpmode)
  { alert(ubb_name+"插入引用\n\n将标签所包围的文本作为引用特殊显示！\n例如：[quote]乔客在线 Joekoe.com[/quote]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[quote]" + range.text + "[/quote]"; }
  else if (advmode)
  { AddTxt="\r[quote]\r[/quote]"; AddText(AddTxt); }
  else
  {
    txt=prompt(ubb_name+"请输入要作为引用显示的文字！","文字");     
    if(txt!=null) { AddTxt="\r[quote]\r"+txt; AddText(AddTxt); AddText("\r[/quote]"); }         
  }
}

function jk_ubb_color(color)
{
  if (helpmode)
  { alert(ubb_name+"插入定义颜色文本\n\n将标签所包围的文本变为制定颜色！\n例如：[color=red]红颜色[/color]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[color=" + color + "]" + range.text + "[/color]"; }
  else if (advmode)
  { AddTxt="[color="+color+"][/color]"; AddText(AddTxt); }
  else
  {
    txt=prompt(ubb_name+"请输入要设置成颜色 "+color+" 的文字！","文字");
    if(txt!=null) { AddTxt="[color="+color+"]"+txt; AddText(AddTxt); AddText("[/color]"); }
  }
}

function jk_ubb_center()
{
  if (helpmode)
  { alert(ubb_name+"居中对齐\n\n将标签所包围的文本居中对齐显示！\n例如：[align=center]内容居中[/align]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[center]" + range.text + "[/center]"; }
  else if (advmode)
  { AddTxt="[align=center][/align]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"请输入要居中对齐的文字！","文字");     
    if (txt!=null) { AddTxt="\r[align=center]"+txt; AddText(AddTxt); AddText("[/align]"); }        
  }
}

function jk_ubb_link()
{
  if (helpmode)
  { alert(ubb_name+"插入超级链接\n\n插入一个超级连接！\n例如：\n[url]http://www.delphiun.com/[/url]\n[url=http://www.delphiun.com/]乔客在线 Joekoe.com[/url]"); }
  else if (advmode)
  { AddTxt="[url][/url]"; AddText(AddTxt); }
  else
  { 
    txt2=prompt(ubb_name+"请输入链接显示的文字，如果留空则直接显示链接！",""); 
    if (txt2!=null)
    {
      txt=prompt(ubb_name+"请输入 URL！例：http://www.delphiun.com/","http://");      
      if (txt!=null)
      {
        if (txt2=="")
        { AddTxt="[url]"+txt; AddText(AddTxt); AddText("[/url]"); }
        else
        { AddTxt="[url="+txt+"]"+txt2; AddText(AddTxt); AddText("[/url]"); }   
      } 
    }
  }
}

function jk_ubb_image()
{
  if (helpmode)
  { alert(ubb_name+"插入图像\n\n在文本中插入一幅图像！\n例如：[IMG]http://www.delphiun.com/images/logo.gif[/IMG]"); }
  else if (advmode)
  { AddTxt="[IMG][/IMG]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"请输入图像的 URL！例：http://www.delphiun.com/images/logo.gif","http://");    
    if(txt!=null) { AddTxt="\r[IMG]"+txt; AddText(AddTxt); AddText("[/IMG]");
    }       
  }
}

function jk_ubb_code()
{
  if (helpmode)
  { alert(ubb_name+"插入代码\n\n插入程序或脚本原始代码！\n例如：[code]乔客在线 Joekoe.com[/code]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[code]" + range.text + "[/code]"; }
  else if (advmode)
  { AddTxt="\r[code][/code]"; AddText(AddTxt); }
  else
  {   
    txt=prompt(ubb_name+"请输入要插入的代码！","");     
    if (txt!=null) { AddTxt="\r[code]"+txt; AddText(AddTxt); AddText("[/code]"); }
  }
}

function jk_ubb_flash()
{
  if (helpmode)
  { alert(ubb_name+"插入 Flash\n\n在文本中插入 Flash 动画！\n例如：[FLASH="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/banner.swf[/FLASH]"); }
  else if (advmode)
  { AddTxt="[FLASH="+ubb_w+","+ubb_h+"][/FLASH]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"请输入 Flash 动画的大校　",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"请输入 Flash 动画的地址！","http://");
      if(txt!=null) { AddTxt="\r[FLASH="+stxt+"]"+txt; AddText(AddTxt); AddText("[/FLASH]"); }
    }
  }
}

function jk_ubb_rm()
{
  if (helpmode)
  { alert(ubb_name+"插入 RM\n\n在文本中插入 Realplay 视频文件！\n例如：[RM="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/test.ram[/RM]"); }
  else if (advmode)
  { AddTxt="[RM="+ubb_w+","+ubb_h+"][/RM]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"请输入 Realplay 视频文件的大小！",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"请输入 Realplay 视频文件的地址 rstp://等都支持","http://");
      if(txt!=null) { AddTxt="\r[RM="+stxt+"]"+txt; AddText(AddTxt); AddText("[/RM]"); }
    }
  }
}

function jk_ubb_mp()
{
  if (helpmode)
  { alert(ubb_name+"插入 MP\n\n在文本中插入 Windows Media Player 视频文件！\n例如：[MP="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/test.wmv[/MP]"); }
  else if (advmode)
  { AddTxt="[MP="+ubb_w+","+ubb_h+"][/MP]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"请输入 Windows Media Player 视频文件的大小！",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"请输入 Windows Media Player 视频文件的地址，各种地址头都支持！","http://");    
      if(txt!=null) { AddTxt="\r[MP="+stxt+"]"+txt; AddText(AddTxt); AddText("[/MP]"); }
    }
  }
}

function jk_ubb_underline()
{
  if (helpmode)
  { alert(ubb_name+"插入下划线\n\n给标签所包围的文本加上下划线！\n例如：[u]乔客在线 Joekoe.com[/u]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[u]" + range.text + "[/u]"; }
  else if (advmode)
  { AddTxt="[u][/u]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"请输入要加下划线的文字！","文字");
    if (txt!=null) { AddTxt="[u]"+txt; AddText(AddTxt); AddText("[/u]"); }         
  }
}

function jk_ubb_reply()
{
  if (helpmode)
  { alert(ubb_name+"回复贴\n\n将标签所包围的内容变成需要回复才能浏览！\n例如：[replyview]『SKDWHOME2004』 SKDW.126.COM[/replyview]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[replyview]" + range.text + "[/replyview]"; }
  else if (advmode)
  { AddTxt="[replyview][/replyview]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"请输入要设置成回复贴的内容！","内容");     
    if (txt!=null) { AddTxt="[replyview]"+txt; AddText(AddTxt); AddText("[/replyview]"); }       
  }
}

function jk_ubb_usemoney()
{
  if (helpmode)
  { alert(ubb_name+"买卖贴\n\n将标签所包围的内容变成需要购买才能浏览！\n例如：[usemoney=8]『SKDWHOME2004』 SKDW.126.COM[/usemoney]"); }
   else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[usemoney=10]" + range.text + "[/usemoney]"; }
  else if (advmode)
  { AddTxt="[usemoney=10][/usemoney]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"请输入金钱数！","10");     
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"请输入买卖贴的内容！","内容");
      if(txt!=null) { AddTxt="\r[usemoney="+stxt+"]"+txt; AddText(AddTxt); AddText("[/usemoney]"); }
    }
  }
}

function jk_ubb_money()
{
  if (helpmode)
  { alert(ubb_name+"金钱贴\n\n将标签所包围的内容变成需要金钱数才能浏览！\n例如：[money=8]『SKDWHOME2004』 SKDW.126.COM[/money]"); }
   else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[money=10]" + range.text + "[/money]"; }
  else if (advmode)
  { AddTxt="[money=10][/money]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"请输入金钱数！","10");     
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"请输入金钱贴的内容！","内容");
      if(txt!=null) { AddTxt="\r[money="+stxt+"]"+txt; AddText(AddTxt); AddText("[/money]"); }
    }
  }
}

function jk_ubb_Post(){
	if (helpmode){
		alert(ubb_name+"贴子数\n\n将标签所包围的内容变成需要贴子数才能浏览！\n例如：[Post=8]『SKDWHOME2004』 SKDW.126.COM[/Post]");
	}else if (document.selection && document.selection.type == "Text"){
		var range = document.selection.createRange();
		range.text;
	}
}
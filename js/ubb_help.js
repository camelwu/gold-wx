
var defmode="advmode";		//Ĭ��ģʽ����ѡ normalmode, advmode, �� helpmode
var ubb_w=450;
var ubb_h=350;
var ubb_name="UBB���� - ";

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
    alert(ubb_name+"������Ϣ\n\n�����Ӧ�Ĵ��밴ť���ɻ����Ӧ��˵������ʾ");
  }
  else if (swtch == 0)
  {
    helpmode = false; normalmode = false; advmode = true;
    alert(ubb_name+"ֱ�Ӳ���\n\n������밴ť�󲻳�����ʾ��ֱ�Ӳ�����Ӧ����");
  }
  else if (swtch == 2)
  {
    helpmode = false; advmode = false; normalmode = true;
    alert(ubb_name+"��ʾ����\n\n������밴ť������򵼴��ڰ�������ɴ������");
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
  { alert(ubb_name+"�����ʼ���ַ\n\n�����ʼ���ַ���ӣ�\n���磺\n[email]joekoe@etang.com[/email]\n[email=joekoe@etang.com]Joekoe[/email]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[email]" + range.text + "[/email]"; }
  else if (advmode)
  { AddTxt="[email][/email]"; AddText(AddTxt); }
  else
  { 
    txt2=prompt(ubb_name+"������������ʾ�����֣����������ֱ����ʾ�ʼ���ַ��",""); 
    if (txt2!=null)
    {
      txt=prompt(ubb_name+"�������ʼ���ַ������joekoe@etang.com","");      
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
  { alert(ubb_name+"�����ֺ�\n\n����ǩ����Χ���������ó�ָ���ֺţ�\n���磺[size=3]���ִ�СΪ 3[/size]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[size=" + size + "]" + range.text + "[/size]"; }
  else if (advmode)
  { AddTxt="[size="+size+"][/size]"; AddText(AddTxt); }
  else
  {           
    txt=prompt(ubb_name+"������Ҫ����Ϊ�ֺ� "+size+" �����֣�","����"); 
    if (txt!=null) { AddTxt="[size="+size+"]"+txt; AddText(AddTxt); AddText("[/size]"); }  
  }
}

function jk_ubb_font(font)
{
  if (helpmode)
  { alert(ubb_name+"�趨����\n\n����ǩ����Χ���������ó�ָ�����壡\n���磺[face=����]����Ϊ����[/face]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[face=" + font + "]" + range.text + "[/face]"; }
  else if (advmode)
  { AddTxt="[face="+font+"][/face]"; AddText(AddTxt); }
  else
  {      
    txt=prompt(ubb_name+"������Ҫ���ó� "+font+" �����֣�","����");
    if (txt!=null) { AddTxt="[face="+font+"]"+txt; AddText(AddTxt); AddText("[/face]"); }  
  }
}


function jk_ubb_bold()
{
  if (helpmode)
  { alert(ubb_name+"��������ı�\n\n����ǩ����Χ���ı���ɴ��壡\n���磺[b]�ǿ����� Joekoe.com[/b]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[b]" + range.text + "[/b]"; }
  else if (advmode)
  { AddTxt="[b][/b]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"������Ҫ���óɴ�������֣�","����");     
    if (txt!=null) { AddTxt="[b]"+txt; AddText(AddTxt); AddText("[/b]"); }       
  }
}

function jk_ubb_italicize()
{
  if (helpmode)
  { alert(ubb_name+"����б���ı�\n\n����ǩ����Χ���ı����б�壡\n���磺[i]�ǿ����� Joekoe.com[/i]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[i]" + range.text + "[/i]"; }
  else if (advmode)
  { AddTxt="[i][/i]"; AddText(AddTxt); }
  else
  {   
    txt=prompt(ubb_name+"������Ҫ���ó�б������֣�","����");     
    if (txt!=null) { AddTxt="[i]"+txt; AddText(AddTxt); AddText("[/i]"); }         
  }
}

function jk_ubb_quote()
{
  if (helpmode)
  { alert(ubb_name+"��������\n\n����ǩ����Χ���ı���Ϊ����������ʾ��\n���磺[quote]�ǿ����� Joekoe.com[/quote]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[quote]" + range.text + "[/quote]"; }
  else if (advmode)
  { AddTxt="\r[quote]\r[/quote]"; AddText(AddTxt); }
  else
  {
    txt=prompt(ubb_name+"������Ҫ��Ϊ������ʾ�����֣�","����");     
    if(txt!=null) { AddTxt="\r[quote]\r"+txt; AddText(AddTxt); AddText("\r[/quote]"); }         
  }
}

function jk_ubb_color(color)
{
  if (helpmode)
  { alert(ubb_name+"���붨����ɫ�ı�\n\n����ǩ����Χ���ı���Ϊ�ƶ���ɫ��\n���磺[color=red]����ɫ[/color]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[color=" + color + "]" + range.text + "[/color]"; }
  else if (advmode)
  { AddTxt="[color="+color+"][/color]"; AddText(AddTxt); }
  else
  {
    txt=prompt(ubb_name+"������Ҫ���ó���ɫ "+color+" �����֣�","����");
    if(txt!=null) { AddTxt="[color="+color+"]"+txt; AddText(AddTxt); AddText("[/color]"); }
  }
}

function jk_ubb_center()
{
  if (helpmode)
  { alert(ubb_name+"���ж���\n\n����ǩ����Χ���ı����ж�����ʾ��\n���磺[align=center]���ݾ���[/align]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[center]" + range.text + "[/center]"; }
  else if (advmode)
  { AddTxt="[align=center][/align]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"������Ҫ���ж�������֣�","����");     
    if (txt!=null) { AddTxt="\r[align=center]"+txt; AddText(AddTxt); AddText("[/align]"); }        
  }
}

function jk_ubb_link()
{
  if (helpmode)
  { alert(ubb_name+"���볬������\n\n����һ���������ӣ�\n���磺\n[url]http://www.delphiun.com/[/url]\n[url=http://www.delphiun.com/]�ǿ����� Joekoe.com[/url]"); }
  else if (advmode)
  { AddTxt="[url][/url]"; AddText(AddTxt); }
  else
  { 
    txt2=prompt(ubb_name+"������������ʾ�����֣����������ֱ����ʾ���ӣ�",""); 
    if (txt2!=null)
    {
      txt=prompt(ubb_name+"������ URL������http://www.delphiun.com/","http://");      
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
  { alert(ubb_name+"����ͼ��\n\n���ı��в���һ��ͼ��\n���磺[IMG]http://www.delphiun.com/images/logo.gif[/IMG]"); }
  else if (advmode)
  { AddTxt="[IMG][/IMG]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"������ͼ��� URL������http://www.delphiun.com/images/logo.gif","http://");    
    if(txt!=null) { AddTxt="\r[IMG]"+txt; AddText(AddTxt); AddText("[/IMG]");
    }       
  }
}

function jk_ubb_code()
{
  if (helpmode)
  { alert(ubb_name+"�������\n\n��������ű�ԭʼ���룡\n���磺[code]�ǿ����� Joekoe.com[/code]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[code]" + range.text + "[/code]"; }
  else if (advmode)
  { AddTxt="\r[code][/code]"; AddText(AddTxt); }
  else
  {   
    txt=prompt(ubb_name+"������Ҫ����Ĵ��룡","");     
    if (txt!=null) { AddTxt="\r[code]"+txt; AddText(AddTxt); AddText("[/code]"); }
  }
}

function jk_ubb_flash()
{
  if (helpmode)
  { alert(ubb_name+"���� Flash\n\n���ı��в��� Flash ������\n���磺[FLASH="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/banner.swf[/FLASH]"); }
  else if (advmode)
  { AddTxt="[FLASH="+ubb_w+","+ubb_h+"][/FLASH]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"������ Flash �����Ĵ�У��",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"������ Flash �����ĵ�ַ��","http://");
      if(txt!=null) { AddTxt="\r[FLASH="+stxt+"]"+txt; AddText(AddTxt); AddText("[/FLASH]"); }
    }
  }
}

function jk_ubb_rm()
{
  if (helpmode)
  { alert(ubb_name+"���� RM\n\n���ı��в��� Realplay ��Ƶ�ļ���\n���磺[RM="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/test.ram[/RM]"); }
  else if (advmode)
  { AddTxt="[RM="+ubb_w+","+ubb_h+"][/RM]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"������ Realplay ��Ƶ�ļ��Ĵ�С��",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"������ Realplay ��Ƶ�ļ��ĵ�ַ rstp://�ȶ�֧��","http://");
      if(txt!=null) { AddTxt="\r[RM="+stxt+"]"+txt; AddText(AddTxt); AddText("[/RM]"); }
    }
  }
}

function jk_ubb_mp()
{
  if (helpmode)
  { alert(ubb_name+"���� MP\n\n���ı��в��� Windows Media Player ��Ƶ�ļ���\n���磺[MP="+ubb_w+","+ubb_h+"]http://www.delphiun.com/images/test.wmv[/MP]"); }
  else if (advmode)
  { AddTxt="[MP="+ubb_w+","+ubb_h+"][/MP]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"������ Windows Media Player ��Ƶ�ļ��Ĵ�С��",ubb_w+","+ubb_h);
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"������ Windows Media Player ��Ƶ�ļ��ĵ�ַ�����ֵ�ַͷ��֧�֣�","http://");    
      if(txt!=null) { AddTxt="\r[MP="+stxt+"]"+txt; AddText(AddTxt); AddText("[/MP]"); }
    }
  }
}

function jk_ubb_underline()
{
  if (helpmode)
  { alert(ubb_name+"�����»���\n\n����ǩ����Χ���ı������»��ߣ�\n���磺[u]�ǿ����� Joekoe.com[/u]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[u]" + range.text + "[/u]"; }
  else if (advmode)
  { AddTxt="[u][/u]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"������Ҫ���»��ߵ����֣�","����");
    if (txt!=null) { AddTxt="[u]"+txt; AddText(AddTxt); AddText("[/u]"); }         
  }
}

function jk_ubb_reply()
{
  if (helpmode)
  { alert(ubb_name+"�ظ���\n\n����ǩ����Χ�����ݱ����Ҫ�ظ����������\n���磺[replyview]��SKDWHOME2004�� SKDW.126.COM[/replyview]"); }
  else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[replyview]" + range.text + "[/replyview]"; }
  else if (advmode)
  { AddTxt="[replyview][/replyview]"; AddText(AddTxt); }
  else
  {  
    txt=prompt(ubb_name+"������Ҫ���óɻظ��������ݣ�","����");     
    if (txt!=null) { AddTxt="[replyview]"+txt; AddText(AddTxt); AddText("[/replyview]"); }       
  }
}

function jk_ubb_usemoney()
{
  if (helpmode)
  { alert(ubb_name+"������\n\n����ǩ����Χ�����ݱ����Ҫ������������\n���磺[usemoney=8]��SKDWHOME2004�� SKDW.126.COM[/usemoney]"); }
   else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[usemoney=10]" + range.text + "[/usemoney]"; }
  else if (advmode)
  { AddTxt="[usemoney=10][/usemoney]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"�������Ǯ����","10");     
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"�����������������ݣ�","����");
      if(txt!=null) { AddTxt="\r[usemoney="+stxt+"]"+txt; AddText(AddTxt); AddText("[/usemoney]"); }
    }
  }
}

function jk_ubb_money()
{
  if (helpmode)
  { alert(ubb_name+"��Ǯ��\n\n����ǩ����Χ�����ݱ����Ҫ��Ǯ�����������\n���磺[money=8]��SKDWHOME2004�� SKDW.126.COM[/money]"); }
   else if (document.selection && document.selection.type == "Text")
  { var range = document.selection.createRange(); range.text = "[money=10]" + range.text + "[/money]"; }
  else if (advmode)
  { AddTxt="[money=10][/money]"; AddText(AddTxt); }
  else
  {
    stxt=prompt(ubb_name+"�������Ǯ����","10");     
    if (stxt!=null)
    {
      txt=prompt(ubb_name+"�������Ǯ�������ݣ�","����");
      if(txt!=null) { AddTxt="\r[money="+stxt+"]"+txt; AddText(AddTxt); AddText("[/money]"); }
    }
  }
}

function jk_ubb_Post(){
	if (helpmode){
		alert(ubb_name+"������\n\n����ǩ����Χ�����ݱ����Ҫ���������������\n���磺[Post=8]��SKDWHOME2004�� SKDW.126.COM[/Post]");
	}else if (document.selection && document.selection.type == "Text"){
		var range = document.selection.createRange();
		range.text;
	}
}
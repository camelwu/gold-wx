
//对话框闭包
function dialog(){
	var theY = 0;
	(document.documentElement.clientHeight>=document.body.clientHeight&&document.documentElement.clientHeight)?theY = document.documentElement.clientHeight:theY = document.body.clientHeight;
	var sFunc = '<input id="dialogOk" class="btn" type="button" onclick="new dialog().reset();" value="确  认"> <input id="dialogCancel" class="btn" type="button" onclick="new dialog().reset();" value="取 消">';
	var sBox = '\
	<div id="dialogBox" style="display:none;z-index:202;">\
		<div id="dialog-header"><span id="dialogMsg">提示</span><a class="dialog-icon-close" title="关闭" onclick="new dialog().reset();">X</a></div>\
		<div id="dialogBody">\
			<div></div>\
		</div>\
		<div id="dialogFunc">'+ sFunc+'</div>\
	</div>\
	<div id="dialogBoxShadow" style="z-index:201;"></div>\
	';
	this.show = function(){
		_('dialogBody') ? function(){} : this.init();
		this.middle('dialogBox');
		//this.shadow();
	}
	this.reset = function(){
		//this.hideModule('select', '');
		_('dialogBox').style.display = 'none';
		_('dialogBoxShadow').style.display = 'none';
	}
	this.html = function(_sHtml){_("dialogBody").innerHTML = _sHtml;this.show();}
	this.init = function(){
		_('dialogCase') ? _('dialogCase').parentNode.removeChild(_('dialogCase')) : function(){};
		var oDiv = document.createElement('div');
		oDiv.id = "dialogCase";
		oDiv.innerHTML = sBox;
		document.body.appendChild(oDiv);
	}
	this.button = function(_sId, _sFuc){
		if(_(_sId)){
			_(_sId).style.display = '';
			if(_(_sId).addEventListener){
				if(_(_sId).act){_(_sId).removeEventListener('click', function(){eval(_(_sId).act)}, false);}
				_(_sId).act = _sFuc;
				_(_sId).addEventListener('click', function(){eval(_sFuc)}, false);
			}else{
				if(_(_sId).act){_(_sId).detachEvent('onclick', function(){eval(_(_sId).act)});}
				_(_sId).act = _sFuc;
				_(_sId).attachEvent('onclick', function(){eval(_sFuc)});
			}
		}
	}
	this.shadow = function(){
		var oShadow = _('dialogBoxShadow');
		oShadow['style']['position'] = 'absolute';
		oShadow['style']['background']	= '#cccccc';
		oShadow['style']['display']	= '';
		oShadow['style']['opacity']	= '0.2';
		oShadow['style']['filter'] = 'alpha(opacity=20)';
		oShadow['style']['top'] = '0px';
		oShadow['style']['left'] = '0px';
		oShadow['style']['width'] = (document.documentElement.clientWidth)+'px';
		oShadow['style']['height'] = (theY)+'px';
	}
	this.set = function(_oAttr, _sVal){
		var oDialog = _('dialogBody');
		if(_sVal != ''){
			switch(_oAttr){
				case 'title':
					oDialog ? oDialog.innerHTML = _sVal : function(){};
					break;
				case '_sOk':
					_('dialogOk').innerHTML = _sVal;
					break;
				case '_esc':
					_('dialogCancel').innerHTML = _sVal;
					break;
				case 'width':
					oHeight['style']['width'] = _sVal;
					width = _sVal;
					break;
				case 'height':
					oHeight['style']['height'] = _sVal;
					height = _sVal;
					break;
				case 'src':
					_('dialogBody').innerHTML = _sVal;
					src = _sVal;
					break;
			}
		}
		
	}
	this.middle = function(_sId){//确认位置
		_(_sId)['style']['display'] = '';
		_(_sId)['style']['left'] = (document.documentElement.clientWidth/2) - (_(_sId).offsetWidth/2)+'px';
		_(_sId)['style']['top'] = (document.documentElement.scrollTop + document.documentElement.clientHeight/2 - _(_sId).offsetHeight/2)+'px';
	}
	this.event = function(_sMsg, _sOk, _sCancel){
		_('dialogFunc').innerHTML = sFunc;
		//_('dialogClose').innerHTML = sClose;
		_('dialogMsg') ? _('dialogMsg').innerHTML = _sMsg : function(){};
		this.show();
		_sOk ? this.button('dialogOk', _sOk) | _('dialogOk').focus() : _('dialogOk').style.display = 'none';
		_sCancel ? this.button('dialogCancel', _sCancel) : _('dialogCancel').style.display = 'none';
		//_sOk ? this.button('dialogOk', _sOk) : _sOk == "" ? function(){} : _('dialogOk').style.display = 'none';
		//_sCancel ? this.button('dialogCancel', _sCancel) : _sCancel == "" ? function(){} : _('dialogCancel').style.display = 'none';
	}
}
//焦点从提示框返回到原页面
function closer(){

}
//单纯提示信息
function opentip(){
	if(arguments.length>0){
		var t = new dialog();
		t.init();
		t.set('title',arguments[0]);
		var ok = arguments.length>1?arguments[1]:'closer();';
		t.event('选择内容', ok,'');
	}
}
//是和否2个选择的提示
function opensel(Info,ok,esc){
	if(arguments.length==3){
		var s = new dialog();
		s.init();
		s.set('title',''+Info+'');
		s.event('', ok, esc);
	}else{
		throw('opensel缺少参数！');
	}
}
//HTML信息输入
function openhtml(Info, ok){
	var t = new dialog();
	t.init();
	t.set('title',Info);
	t.event('选择内容', ok ,'closer();');
}
function _(d){return document.getElementById(d);}
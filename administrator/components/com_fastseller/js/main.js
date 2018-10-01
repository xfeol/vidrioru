window.addEvent('domready', function(){
	var hash=window.location.hash;
	if(hash!='') hash=hash.substr(1);
	new Ajax (url,{
		method:'get',
		data: hash,
		evalScripts:true,
		update: $('clayout')
	}).request();
	
	$$('.ui-main-nav').addEvent('click',function(){
		var param=this.getAttribute('data-url'),
			status=$('cstatus');
		window.location.hash=param;
		new Ajax (url,{
			method:'get',
			data: param,
			evalScripts:true,
			onRequest: function(){
				status.removeClass('hid');
			},
			onComplete: function(){
				status.addClass('hid');
			},
			update: $('clayout')
		}).request();
	});
	
	$('hideTop').addEvent('click',function(){
		var body=$$('body')[0];
		body.toggleClass('top-hidden');
	});
	
});

var popMenuHandler=new Class({
	initialize:function(){
		this.boundHandler=this.menuAutoRemove.bind(this);
		this.clickedButton=null;
		this.activeButton=null;
		this.menuType=null;
		
	},
	// @param type: for diff types of buttons: 1,2--Glass btn, 3,4--Plain btn. 1,3--position menu relative to right corner, 2,4--left
	showmenu:function(btn,type,event){
		//this.stopEventPropagation(event);
		stopEventPropagation(event);
		this.clickedButton=btn;
		this.menuType=type;
		
		if(this.activeButton==this.clickedButton){
		//	alert('same button');
			this.removemenu();
			//menubutton='';
			document.removeEvent('click',this.boundHandler);
		}else if(this.activeButton){
		//	alert('change button');
			this.removemenu();
			this.drawmenu();
			//menubutton=this.clickedButton;
			this.activeButton=this.clickedButton;
		}else{
			//alert('create event to remove');
			//menubutton=this.clickedButton;
			this.activeButton=this.clickedButton;
			this.drawmenu();
			document.addEvent('click',this.boundHandler);
		}
	},
	drawmenu:function(){
		var menu=$(this.clickedButton.getAttribute('data'));
		if(this.menuType==1 || this.menuType==2){$(this.clickedButton).addClass('glass-btn-active');}
		else if(this.menuType==3 || this.menuType==4){$(this.clickedButton).addClass('prodparam-btn-active');}
		else{$(this.clickedButton).addClass('tabletop-btn-active');}
		menu.removeClass('hid');
		var butpos=$(this.clickedButton).getPosition();
		var butsize=$(this.clickedButton).getSize().size;
		var winsize=menu.getSize().size;
		
		if(this.menuType==1){
			var left=butpos.x+butsize.x-winsize.x;
		}else if(this.menuType==3){
			var left=butpos.x+butsize.x/2-winsize.x/2;
			if(left<0) left=10;
		}else{
			var left=butpos.x;
		}
		if(this.menuType==5){
			var top=butpos.y+butsize.y-1;
		}else{
			var top=butpos.y+butsize.y+1;
		}
		
		menu.setStyles({top:top,left:left});
	},
	removemenu:function(){
		$(this.activeButton.getAttribute('data')).addClass('hid');
		if($(this.activeButton).hasClass('glass-btn')){$(this.activeButton).removeClass('glass-btn-active');}
		else if($(this.activeButton).hasClass('prodparam-btn')){$(this.activeButton).removeClass('prodparam-btn-active');}
		else if($(this.activeButton).hasClass('ui-tabletop-btn')){$(this.activeButton).removeClass('tabletop-btn-active');}
		this.activeButton=null;
	},
	removeBoundEvent:function(){
		document.removeEvent('click',this.boundHandler);
	},
	menuAutoRemove:function(event){
		var menu=$(this.activeButton.getAttribute('data'));
		var left=menu.offsetLeft;
		var top=menu.offsetTop;
		var bottom=top+menu.offsetHeight-1;
		var right=left+menu.offsetWidth-1;
		var clickedonmenu=false;
		var marginX=event.pageX || event.page.x;
		var marginY=event.pageY || event.page.y;
		if(marginX>=left && marginX<=right && marginY>=top && marginY<=bottom) clickedonmenu=true;
		
		//if(clickedonmenu) alert('On menu');
		if(!clickedonmenu){
			this.removemenu();
			document.removeEvent('click',this.boundHandler);
		}
	}
});

function stopEventPropagation(e){
	var event = e || window.event;
	//if(!event.cancelBubble){
	if(event.cancelBubble){
		event.cancelBubble=true;
		return;
	}
	event.stopPropagation();
}

function squeeze(str,maxlength){
	var len=str.length;
	var dots='<b class="threedots">..</b>';
	return (len<=maxlength)? str : (str.substr(0,maxlength)+dots); 
}

var simplePopup=new popMenuHandler();
var activeParamButton=null;
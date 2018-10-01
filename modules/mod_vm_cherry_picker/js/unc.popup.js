window.addEvent('domready',function(){
	// var popup=document.createElement('div');
	// popup.id='chp-popup';
	// popup.className='hid';
	// popup.style.minWidth=document.chpform.offsetWidth-13+'px';
	// document.body.appendChild(popup);
	// popup.innerHTML='<span class="ppname"></span> <span class="ppvalue"></span>';//<span class="ppclear"></span>
	
	//$("chp-popup").setStyle("min-width",document.chpform.offsetWidth-13);
	$$(".chp-trackbarparam").addEvents({
		mouseenter:function(event){
			var h,top,left;//,popup;
			
		//	console.log("current hover parameter ON ENTER: ",currentHoverParameter);
		//	console.log("this mouse enter: ",this);
			
			if(trackbarActive) return;
			
			if(this==currentHoverParameter) return;
			currentHoverParameter=this;
			h=this.getFirst();
		//	top=this.getTop();
			top=getY(this);
			left=this.getLeft();
			//popup=$("chp-popup");
			//popup.getFirst().innerHTML=h.innerHTML;
			setPopupLabel(this.getAttribute("data-name"));
			popup.setStyles({top:top,left:left});
			//transitionPopup(popup,0,1,function(){});
			transitionPopup(0,1,function(){});
			
			//console.log("over the block");
		},
		
		mouseleave:function(event){
			var x,y,left,right,top,bottom,leftBlock;//,popup;
			if(trackbarActive) return;
			
		//	console.log("current hover parameter ON BLOCK LEAVE: ",currentHoverParameter);
			
			leftBlock=true;
			if(event.custom){ // if event was fired manually, after finished moving the slider
				x=event.x;
				y=event.y;
			}else{
				x=event.page.x;
				y=event.page.y;
			}
			
			left=this.getLeft()-1;
			right=left+this.offsetWidth+1;
			top=this.getTop();
			bottom=top+this.offsetHeight;
			//console.log(left,right,top,bottom,x,y);
			if(x>left && x<right && y>top && y<bottom) leftBlock=false;
			
			if(!leftBlock) return;
			currentHoverParameter=null;
			//popup=$("chp-popup");
			//transitionPopup(popup.style.opacity.toFloat(),0,function(){popup.addClass("hid");});
			transitionPopup(popup.getStyle('opacity').toFloat(),0,function(){popup.addClass("hid");});
			
		//	console.log("out from the block",leftBlock);
		}
	});
	
	$("chp-popup").addEvent("mouseleave",function(event){
		var x,y,left,right,top,leftBlock;//,popup;
		
		if(trackbarActive) return;
	
	//	console.log("popup leave, target",event.target);
	//	console.log("popup leave, related:",event.relatedTarget);
	//	console.log("current hover parameter ON POPUP LEAVE: ",currentHoverParameter);
		
		if(!currentHoverParameter) return;
		
		leftBlock=true;
		if(event.custom){
			x=event.x;
			y=event.y;
		}else{
			x=event.page.x;
			y=event.page.y;
		}
			
		//check if we left the bounderies of hovered parameter by X-axis
		left=currentHoverParameter.getLeft();
		right=left+currentHoverParameter.offsetWidth;
		top=currentHoverParameter.getTop();
	//	top=getY(currentHoverParameter);

		//console.log(left,right,top,bottom,x,y);
		if(x>left && x<right && y>top) leftBlock=false;
			
		if(!leftBlock) return;
			
		//p=this;
		currentHoverParameter=null;
			
		//transitionPopup(this.style.opacity.toFloat(),0,function(){popup.addClass("hid");});
		transitionPopup(this.getStyle('opacity').toFloat(),0,function(){popup.addClass("hid");});
	//	console.log("out from popup");
	});
	
	var currentTransition=null;
	function transitionPopup(startOpacity,endOpacity,callback){
		//var el=$(l);
		var el=popup;
		el.setStyle("opacity",startOpacity);
		el.removeClass("hid");
		// mootools 1.1
		if(el.effect){	
			if(currentTransition) currentTransition.stop();
			var fadeIn=el.effect("opacity", {
				duration: 150,
				transition: Fx.Transitions.linear,
				onComplete:callback
			});
			currentTransition=fadeIn;
			fadeIn.start(startOpacity,endOpacity);
		}else{
			if(currentTransition) currentTransition.cancel();
			var fadeIn=new Fx.Tween(el, {
				duration: 150,
				transition: Fx.Transitions.linear,
				property: "opacity",
				onComplete:callback
			});
			currentTransition=fadeIn;
			fadeIn.start(startOpacity,endOpacity);
		} 
	}
	/*
	*	Set label when trackbar hovered on
	*/
	function setPopupLabel(name){
		var s,opt,l=null,r=null,pn=popup.getFirst();
		opt=chp[name].options;
		pn.innerHTML=opt.t.l;
		if(opt.valueLeft) l=opt.values?opt.values[opt.valueLeft]:opt.valueLeft;
		//if(opt.mode==2) if(opt.valueRight!=null) r=opt.values?opt.values[opt.valueRight]:opt.valueRight;
		if(opt.mode==2 && opt.valueRight!=null) r=opt.values?opt.values[opt.valueRight]:opt.valueRight;
		//console.log(opt.valueRight);
	//	if(opt.mode==1){
			
	//	}else{
	//	{
			
			
			
			//s=\'<span class="ppname">\'+opt.t.l+\'</span> \'+words.from+\' <span class="ppleft"></span> \'+words.to+\' <span class="ppright"></span>\';
			//s=\'<span class="ppname">\'+opt.t.l+\'</span> <span class="ppvalue"></span>\';
			//if(opt.t.u) s+=\' \'+unescape(opt.t.u);
	//	}
		//popup.innerHTML=s;
		
		// if(opt.valueLeft || opt.valueRight){
			// var cl=popup.querySelector('.ppclear');
			// cl.innerHTML='clear';
		// }
		
		//console.log(l,r);
		chp[name].setLabel(l,r);
	}
});

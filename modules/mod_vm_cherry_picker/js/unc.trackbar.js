var trackbarActive=false;
var currentHoverParameter=null;
/*
*@param (int)mode: sets the mode of trackbar in use: 
*	0--Default. Two sliders, two inputs. For price trackbar.
*	1--one slider, one input
*	2--two sliders, one input
*@param (HTML Element)form: ChP form
*@param (string)inputLeft: left input. When price trackbar--store low-price, when parameter--store both margins, e.g. 2:7
*@param (string)inputRight: right input. Serves as high-price for price trackbar
*@param (HTML Element)label: html element to be updated dynamically, when sliders are moved.
*@param (array)values: if set, trackbar integers will get labels from this array.
*@param (obj)t: t.l--contains parameter label, t.u--parameter units
*/
var chp_trackbar=new Class({
	
	options:{
		mode:0,
		form:null,
		trackbar:null,
		inputLeft:null,
		inputRight:null,
		label:null,
		labeltype:0,
		values:null,
		decimals:0,
		step:null,
		limitLeft:0,
		limitRight:100,
		valueLeft:null,
		valueRight:null,
		t:null,
		showApply:false,
		extremumValueToNull:1
	},
	initialize:function(options){
		var mode,tb,w,sl,slw,sr,srw,fullrange,selrange;
		this.setOptions(options);
		this.bound={
			'start':this.start.bindWithEvent(this),
			'drag':this.drag.bindWithEvent(this),
			'stop':this.stop.bind(this),
			'update':this.update.bind(this),
			'set':this.set.bindWithEvent(this)
		};
		mode=this.options.mode;
		tb=this.options.trackbar;
		$(tb).removeClass('hid');
		this.elem=tb;
		
		w=tb.offsetWidth;
		this.trackbarWidth=w;
		
		//sl=tb.querySelector('.left-slider');
		//sl=tb.getElementsByClassName('left-slider')[0];
		sl=tb.getElements('.left-slider')[0];
		this.sliderLeft=sl;
		$(sl).addEvent('mousedown',this.bound.start);
		slw=sl.offsetWidth;
		this.sliderLeftWidth=slw;
		if(mode!=1){
			//sr=tb.querySelector('.right-slider');
			sr=tb.getElements('.right-slider')[0];
			this.sliderRight=sr;
			$(sr).addEvent('mousedown',this.bound.start);
			//sr.addEvent('mousedown',this.say);//function(){alert('here');}
			srw=sr.offsetWidth;
		}else{
			srw=0;
		}
		//if(tb.querySelector('.right-slider')) tb.querySelector('.right-slider').addEvent('mousedown',function(){alert('here');});//function(){alert('here');}
		this.sliderRightWidth=srw;
		this.sliderLeftX=0;
		this.sliderRightX=0;
		
		//fullrange=tb.querySelector('.chp-fullrange');
		fullrange=tb.getElements('.chp-fullrange')[0];
		$(fullrange).addListener('mousedown',this.bound.start);
	//	if(mode==1){
			//this.range=tb.querySelector('.chp-fullrange');
			this.fullrange=fullrange;
	//	}else{
		if(mode!=1){
			//selrange=tb.querySelector('.chp-selrange');
			selrange=tb.getElements('.chp-selrange')[0];
			$(selrange).addListener('mousedown',this.bound.start);
			this.range=selrange;
		}
	//	this.range2=tb.querySelector('.chp-fullrange');
	//	$(this.range).addListener('mousedown',this.bound.start);
		
		//this.inputLeft=document.chpform[this.options.inputLeft];
		//this.inputLeft=this.options.inputLeft;
		this.inputLeft=this.options.form[this.options.inputLeft];
		if(!mode){
			//this.inputRight=document.chpform[this.options.inputRight];
			//this.inputRight=this.options.inputRight;
			this.inputRight=this.options.form[this.options.inputRight];
			$(this.inputLeft).addListener('change',this.bound.set);
			$(this.inputRight).addListener('change',this.bound.set);
		}
		
		this.activePixels=w-slw-srw;
		this.rangeOfValues=this.options.limitRight-this.options.limitLeft;
		this.pixelsPerValue=this.activePixels/this.rangeOfValues;
		//this.gap=(this.options.step)? this.options.step : 1/Math.pow(10,this.options.decimals);
		this.update();
		//console.log(this,sl,sr);
	},
	start:function(event){
		var p,updateSlider,top,traveled,x,tmp1,tmp2,mouseX,middle;
		event=new Event(event);
		
		if(!currentHoverParameter){
			p=getParentElement(this.sliderLeft,'chp-trackbarparam');
			if(p) p.fireEvent('mouseenter');
			//console.log("parent is: ",getParentElement(this.sliderLeft,'chp-trackbarparam'));
		}
		trackbarActive=true;
		updateSlider=false;
		
		//	var top=this.elem.getTop()-window.getScrollTop();
		top=getY(this.elem)-window.getScrollTop();
		//console.log(this.elem.getTop());
		this.elem.setProperty('style','position:fixed;width:'+this.trackbarWidth+'px;top:'+top+'px;');
		//this.elem.setProperty('style','position:fixed;width:'+this.trackbarWidth+'px;top:'+top+'px;left:'+this.elem.getLeft()+'px');
		
		if(event.target==this.fullrange){ // if 2-slider-mode: mouse hit one the left of LeftSlider, or the right of RightSlider. if 1-slider-mode: it's the full range
			if(this.options.mode==1){
				this.slider=this.sliderLeft;
				this.startX=this.slider.getLeft()+this.sliderLeftWidth/2;
				traveled=event.page.x-this.startX;
			}else{
				tmp1=this.sliderLeft.getLeft();
				mouseX=event.page.x;
				if(mouseX<tmp1){
					this.slider=this.sliderLeft;
					this.startX=tmp1+this.sliderLeftWidth/2;
					traveled=mouseX-this.startX;
				}else{
					this.slider=this.sliderRight;
					this.startX=this.slider.getLeft()+this.sliderRightWidth/2;
					traveled=mouseX-this.startX;
				}
				//console.log(event.page.x);
			}
			updateSlider=true;
		}else if(event.target==this.range){ // it's the case for 2-slider-mode. Hit in between of sliders
			tmp1=this.sliderRight.getLeft();
			tmp2=this.sliderLeft.getLeft();
			middle=(tmp1+tmp2+this.sliderLeftWidth)/2;
			mouseX=event.page.x;
			if(mouseX<middle){
				this.slider=this.sliderLeft;
				this.startX=tmp2+this.sliderLeftWidth/2;
				traveled=mouseX-this.startX;
			}else{
				this.slider=this.sliderRight;
				this.startX=tmp1+this.sliderRightWidth/2;
				traveled=mouseX-this.startX;
			}
			updateSlider=true;
			//console.log('between',event.page.x);
		}else{
			this.slider=event.target;
			this.startX=event.page.x;
		}
		
	//	this.slider=event.target;
		//console.log(this.slider);
		//if(this.slider===this.sliderRight){
		//	this.sliderShift=event.page.x-event.target.getLeft();
		//}
		//else if(this.slider===this.sliderLeft){
		//else{
		//	this.sliderShift=event.page.x-event.target.getLeft()-event.target.offsetWidth;
		//}
		
		//alert(this.elem.clientY);
	
		//style.position="fixed";
		
		//this.elem.style.top=window.getScrollTop();
		
	//	this.startX=event.page.x;
		//this.activePixels=this.trackbarWidth-this.sliderLeftWidth-this.sliderRightWidth;
	//	var x=this.slider.getLeft()-this.elem.getLeft();
		//relative slider X position
		x=this.slider.offsetLeft;
		//console.log(x);
		if(this.slider===this.sliderRight){x-=this.sliderRightWidth;}
		this.sliderPosX=(x>this.activePixels)?this.activePixels : x;
		//console.log('lsider pos x: ',this.sliderPosX);
		//this.rangeOfValues=this.options.limitRight-this.options.limitLeft;
		//this.pixelsPerValue=this.activePixels/this.rangeOfValues;
		
		//console.log('start x:' + this.startX);
		//console.log('active pixels:' + this.activePixels);
		//console.log('slider pox x:' + this.sliderPosX);
		//console.log('target left():' + event.target.getLeft());
		//console.log(this.sliderShift);
		//alert(this.options.value);
		/*this.values={
			l:this.options.valueLeft,
			r:this.options.valueRight,
			v:this.options.value
		};*/
		
		document.addEvent('mousewheel', this.disableMouseWheel);
		if(window.addEventListener) window.addEventListener('DOMMouseScroll', this.disableMouseWheel, true); //for MT1.1 & FF
		
		document.addListener('mousemove',this.bound.drag);
		document.addListener('mouseup',this.bound.stop);
		
		//this.labelLeft=this.options.label.querySelector('.ppleft');
		//this.labelRight=this.options.label.querySelector('.ppright');
		//if(this.options.label) this.labelValue=this.options.label.querySelector('.ppvalue');
		if(this.options.label) this.labelValue=this.options.label.getElement('.ppvalue');
		
		event.stop();
		if(updateSlider) this.calcDrag(traveled);
	},
	drag:function(event){
		event=new Event(event);
		var traveled=event.page.x-this.startX;
		
		this.calcDrag(traveled);
	/*	
		//console.log('traveled: '+traveled);
		var v=((traveled+this.sliderPosX)/this.activePixels)*this.rangeOfValues+this.options.limitLeft;
		//console.log('v:'+(traveled+this.sliderPosX)/this.activePixels);
		
		
		//console.log('var: '+(event.page.x-this.elem.getLeft()-this.sliderShift-this.sliderLeft.offsetWidth));
	//	var v=(event.page.x-this.elem.getLeft()-this.sliderShift-this.sliderLeft.offsetWidth)/(this.elem.offsetWidth-this.sliderLeft.offsetWidth-this.sliderRight.offsetWidth);
	//	v=(this.options.limitRight-this.options.limitLeft)*v+this.options.limitLeft;
		v=v.toFixed(this.options.decimals).toFloat();
	//	console.log('v before step:'+v);
		if($defined(this.options.step)){
			v=v/this.options.step;
			v=Math.round(v)*this.options.step;
		}
		//console.log('its:'+this.options.values[v]);
		//console.log('v:'+v);
		
		// if the drag value the same as current--do not re-draw whole trackbar
		if(this.slider===this.sliderLeft){
			if(v==this.options.valueLeft) return;
		}else{
			if(v==this.options.valueRight) return;
		}
		
		if(this.slider===this.sliderRight){
			this.setValueRight(v);
		}else{
			this.setValueLeft(v);
		} */
		event.stop();
	},
	calcDrag:function(traveled){
		//console.log('traveled: ',traveled);
		var v=((traveled+this.sliderPosX)/this.activePixels)*this.rangeOfValues+this.options.limitLeft;
		//console.log('v:'+(traveled+this.sliderPosX)/this.activePixels);
		
		
		//console.log('var: '+(event.page.x-this.elem.getLeft()-this.sliderShift-this.sliderLeft.offsetWidth));
	//	var v=(event.page.x-this.elem.getLeft()-this.sliderShift-this.sliderLeft.offsetWidth)/(this.elem.offsetWidth-this.sliderLeft.offsetWidth-this.sliderRight.offsetWidth);
	//	v=(this.options.limitRight-this.options.limitLeft)*v+this.options.limitLeft;
		v=v.toFixed(this.options.decimals).toFloat();
	//	console.log('v before step:'+v);
		if($defined(this.options.step)){
			v=v/this.options.step;
			v=Math.round(v)*this.options.step;
		}
		//console.log('its:'+this.options.values[v]);
		//console.log('v: ',v);
		
		// if the drag value the same as current--do not re-draw whole trackbar
	//	if(v<this.options.limitLeft || v>this.options.limitRight) return;
		if(v<this.options.limitLeft) v=this.options.limitLeft;
		if(v>this.options.limitRight) v=this.options.limitRight;
		if(this.slider===this.sliderLeft){
			if(v==this.options.valueLeft || ((v==this.options.valueLeft ||this.options.valueLeft==null) && v==this.options.limitLeft)) return;
		}else{
			if(v==this.options.valueRight || ((v==this.options.valueRight ||this.options.valueRight==null) && v==this.options.limitRight)) return;
		}
		
		this.options.showApply=true;
		
		if(this.slider===this.sliderRight){
			this.setValueRight(v);
		}else{
			this.setValueLeft(v);
		}
	},
	
	stop:function(event){
		trackbarActive=false;
		event=new Event(event);
		var xy={
			custom:true,
			x:event.page.x,
			y:event.page.y
		};
		if(currentHoverParameter) currentHoverParameter.fireEvent('mouseleave',xy);
		document.removeListener('mousemove',this.bound.drag);
		document.removeListener('mouseup',this.bound.stop);
		//if(this.values.l!=this.options.valueLeft||this.values.r!=this.options.valueRight||this.values.v!=this.options.value){
		//	this.fireEvent('change')
		//}
		this.slider=null;
		//this.sliderShift=null;
		//this.values=null;
		
		this.elem.style.position="absolute";
		this.elem.style.top='';
		document.removeEvent('mousewheel', this.disableMouseWheel);
		if(window.removeEventListener) window.removeEventListener('DOMMouseScroll',this.disableMouseWheel, true); // MT1.1 & FF
	},
	update:function(){
		if(this.options.labeltype==1) this.notSetLabel=true;	// we do not want to set label when the page first loads
		this.setValueLeft(this.options.valueLeft);
		if(this.options.mode!=1) this.setValueRight(this.options.valueRight);
		this.notSetLabel=false;
	},
	set:function(){
		var l=this.inputLeft.value.toInt();
		var r=this.inputRight.value.toInt();
		//var l=Number(this.inputLeft.value).toFixed(2);
		//var r=Number(this.inputRight.value).toFixed(2);
		//var l=Math.round((parseFloat(this.inputLeft.value.replace(/\,/g,".")))*100)/100;
		//var r=Math.round((parseFloat(this.inputRight.value.replace(/\,/g,".")))*100)/100;
		
		if(l){
			this.setValueLeft(l);
		}else{
			this.setValueLeft(this.options.limitLeft);
		}
		if(r){
			this.setValueRight(r);
		}else{
			this.setValueRight(this.options.limitRight);
		}
	},		
	setValueLeft:function(v){
		var vr=this.options.valueRight;
		var inp,rs,vrs,ll=null,lr=null;
		//console.log('value left:'+this.options.valueLeft);
	//	with(this.options){
			//this.options.valueLeft=this.checkValue(v,limitLeft,valueRight,limitLeft);
		var r=this.checkValue(v,vr,this.options.limitLeft,'left');
			//if(r==null) return;
			
		this.options.valueLeft=r;
			//$('rightResult').innerHTML=this.options.values[r];
			
			//if(this.inputLeft)this.inputLeft.value=valueLeft!=null?valueLeft:'';
			var mode=this.options.mode;
			if(mode==0){
				//if(this.inputLeft)this.inputLeft.value=(r==null)?'':r;
				this.inputLeft.value=(r==null)?'':r;
			}else if(mode==1){
				//this.inputLeft.value=(r==null)?'':(this.options.values?this.options.values[r]:r);
				rs=(r==null)?null:(this.options.values?this.options.values[r]:r);
				this.inputLeft.value=rs?rs:'';
				if(!this.notSetLabel) this.setLabel(rs,null);
			}else{
				if(r==null || r==this.options.limitLeft){	// l------r
					//inp=(vr==null || vr==this.options.limitRight)?'':':'+(this.options.values?this.options.values[vr]:vr);
					if(vr==null || vr==this.options.limitRight){	// l------r
						inp='';
						//this.setLabel(null,null);
					}else{	// l----r--
						vrs=this.options.values?this.options.values[vr]:vr;
						inp=':'+vrs;
						//this.setLabel(null,vrs);
						lr=vrs;
					}
				}else{	// --l----r
					if(this.options.values){
						rs=this.options.values[r];
						vrs=this.options.values[vr];
					}else{rs=r;vrs=vr;}
					//inp=(vr==null || vr==this.options.limitRight)?((r==this.options.limitRight)?rs+':'+rs:rs+':'):rs+':'+vrs;
					if(vr==null || vr==this.options.limitRight){	// --l----r
						if(r==this.options.limitRight){	// ------lr
							inp=rs+':'+rs;
							//this.setLabel(rs,rs);
							ll=lr=rs;
						}else{	// --l----r
							inp=rs+':';
							//this.setLabel(rs,null);
							ll=rs;
						}
					}else{	// --l--r--
						inp=rs+':'+vrs;
						//this.setLabel(rs,vrs);
						ll=rs;lr=vrs;
					}
				}
				//this.inputLeft.value=(r==null)?'':(this.options.values?this.options.values[r]:r);
				this.inputLeft.value=inp;
				if(!this.notSetLabel) this.setLabel(ll,lr);
			}
			//console.log(inp);
			
			//this.setLabelValue(this._labelLeft,valueLeft!=null?valueLeft:limitLeft);
		//	var w=this.width(r==null?this.options.limitLeft:(r-this.options.limitLeft));//-this.sliderLeftWidth;
			var w=this.width(r==null?0:(r-this.options.limitLeft));//-this.sliderLeftWidth;
			this.sliderLeft.setStyle('left',w+'px');
			this.sliderLeftX=w;
		//	this.elemLeft.setStyle('width',w+'px');
			if(mode!=1) this.updateRangeWidth();
	//	}
		return this.options.valueLeft;
	},
	setValueRight:function(v){
		var vl=this.options.valueLeft;
		var inp,rs,vls,ll=null,lr=null;
		//console.log('value right:'+this.options.valueRight);
	//	with(this.options){
			//this.options.valueRight=this.checkValue(v,valueLeft,limitRight,limitRight);
			var r=this.checkValue(v,vl,this.options.limitRight,'right');
			
			//if(r==null) return;
			
			this.options.valueRight=r;
			
			var mode=this.options.mode;
			if(mode==0){
				//if(this.inputLeft)this.inputLeft.value=(r==null)?'':r;
				this.inputRight.value=(r==null)?'':r;
			}//else if(mode==1){
			//	this.inputLeft.value=(r==null)?'':(this.options.values?this.options.values[r]:r);
			//}
			else{
				if(r==null || r==this.options.limitRight){
					//inp=(vl==null || vl==this.options.limitLeft)?'':(this.options.values?this.options.values[vl]:vl)+':';
					if(vl==null || vl==this.options.limitLeft){
						inp='';
						//this.setLabel(null,null);
					}else{
						vls=this.options.values?this.options.values[vl]:vl;
						inp=vls+':';
						//this.setLabel(vls,null);
						ll=vls;
					}
				}else{
					if(this.options.values){
						rs=this.options.values[r];
						vls=this.options.values[vl];
					}else{rs=r;vls=vl;}
					//inp=(vl==null || vl==this.options.limitLeft)?((r==this.options.limitLeft)?rs+':'+rs:':'+rs):vls+':'+rs;
					if(vl==null || vl==this.options.limitLeft){
						if(r==this.options.limitLeft){
							inp=rs+':'+rs;
							//this.setLabel(rs,rs);
							ll=lr=rs;
						}else{
							inp=':'+rs;
							//this.setLabel(null,rs);
							lr=rs;
						}
					}else{
						inp=vls+':'+rs;
						//this.setLabel(vls,rs);
						ll=vls;lr=rs;
					}
				}
				//this.inputLeft.value=(r==null)?'':(this.options.values?this.options.values[r]:r);
				this.inputLeft.value=inp;
				if(!this.notSetLabel) this.setLabel(ll,lr);
			}
			
			
			//if(this.options.dual){
			//if(this.inputRight)this.inputRight.value=valueRight!=null?valueRight:'';
		//	if(this.inputRight)this.inputRight.value=(r==null)?'':r;
			//	this._setLabelValue(this._labelRight,valueRight!=null?valueRight:limitRight);
			//}
			//var w=this.trackbarWidth-this.width(r==null?this.options.limitRight:r)-this.sliderRightWidth;
			//console.log(this.options.limitRight,r);
			var w=this.width(r==null?0:(this.options.limitRight-r));
			this.sliderRight.setStyle('right',w+'px');
			this.sliderRightX=w;
		//	this.elemRight.setStyle('width',w+'px');
			this.updateRangeWidth();
	//	}
		return this.options.valueRight;
	},
	checkValue:function(v,border,e,type){
		l=this.options.limitLeft;
		r=this.options.limitRight;
		if(type=='left'){
			border=(border==null?r:border);
			//v=(v==null?null:(v<l?l:(v>=border?border-this.gap:v)));
			v=(v==null?null:(v<l?l:(v<border?v:border)));
		}
		else if(type=='right'){
			border=(border==null?l:border);
			//v=(v==null?null:(v>r?r:(v<=border?border+this.gap:v)));
			v=(v==null?null:(v>r?r:(v>border?v:border)));
		}
		//console.clear();
		//console.log('v='+v+' l='+l+' r='+r+' border='+border);
		//return v==e&&this.options.extremumValueToNull?null:v;
		return v==e&&this.options.extremumValueToNull?null:v;
	},
	/*
	checkValue:function(v,l,r,e){
		//alert(this.options.limitLeft);
		$('tata').setText('v='+v+' l='+l+' r='+r+' e='+e);
		l=l||this.options.limitLeft;
		r=r||this.options.limitRight;
		v=(v==null?null:(v<l?l:(v>r?r:v)));
		$('tata2').setText('  '+v)
		return v==e&&this.options.extremumValueToNull?null:v
	},*/
	width:function(value){
		//var r=(this.pixelsPerValue)*(value-this.options.limitLeft);
	//	var r=(this.pixelsPerValue)*(value-this.options.limitLeft);
		var r=(this.pixelsPerValue)*value;
		//console.log('value:'+value);
		//console.log('pixels:'+r);
		//console.log('this.pixelsPerValue:'+this.pixelsPerValue);
		return Math.round(r);
		
		//var pl=(value-this.options.limitLeft)/(this.options.limitRight-this.options.limitLeft);
		//var px=this.elem.offsetWidth-(this.sliderLeft.offsetWidth+this.sliderRight.offsetWidth);
		//return Math.round(pl*px);
	},
	updateRangeWidth:function(){
		// var w=this.options.dual?this.elem.offsetWidth-parseFloat(this.elemRight.getStyle('width'))-parseFloat(this.elemLeft.getStyle('width')):0;
		// var w=this.trackbarWidth-this.elemLeftWidth-this.elemRightWidth;
		// console.log('el left:'+this.elemLeftWidth);
		// console.log('w:'+w);
		// var w2=this.elem.getSize().size;
		// $('tata2').setText('w: '+w + ' track width: '+this.elem.offsetWidth + ' rightSlider:'+parseFloat(this.elemRight.getStyle('width')) 
		// +' leftSlider: '+parseFloat(this.elemLeft.getStyle('width'))  
		// +'w2: '+w2.x
		// );
		
		//this.elemRange.setStyle('width',w+'px');
		$(this.range).setStyles({left:this.sliderLeftX,right:this.sliderRightX});
	},
	setLabel:function(l,r){
		//ll=this.labelLeft || this.options.label.querySelector('.ppleft'),
		//lr=this.labelRight || this.options.label.querySelector('.ppright'),
		//var lv=this.labelValue || this.options.label.querySelector('.ppvalue'),
		var lv=this.labelValue || this.options.label.getElement('.ppvalue'),
			opt=this.options,
			s='';
		//console.log(l,r);
	//	if(opt.labeltype==0){
			if(opt.showApply && opt.labeltype==1) s='<button type="button" class="ppapply" onclick="applyRefines('+this.options.form.name+')">';
			if(opt.labeltype==0) s+='<span class="ppvalue2" data-pname="'+opt.inputLeft+'" onclick="clearRefine(this,'+this.options.form.name+')" onmouseover="clearHover(this,true)" onmouseout="clearHover(this,false)">';
			if(opt.mode==1){	
				s+=(l)?l:words.all;
			}else{
				if(!l) l=opt.values?opt.values[opt.limitLeft]:opt.limitLeft;
				if(!r) r=opt.values?opt.values[opt.limitRight]:opt.limitRight;
				if(l==r){
				//	if(opt.labeltype==0){
						s+='<b>'+l+'</b>';
				//	}else{
				//		s+='<span><b>'+l+'</b></span>';
				//	}
					if(opt.t.u) s+=' '+opt.t.u;
				}else{
				//	if(!l) l=opt.values?opt.values[opt.limitLeft]:opt.limitLeft;
				//	if(!r) r=opt.values?opt.values[opt.limitRight]:opt.limitRight;
					if(opt.labeltype==1){
						s+=words.from+' <b>'+l+'</b> '+words.to+' <b>'+r+'</b>';
					}else{
						s+='<b>'+l+'</b> - <b>'+r+'</b>';
					}
					if(opt.t.u) s+=' '+opt.t.u;
				}
			}
			if(opt.showApply && opt.labeltype==1) s+=' - <b>'+words.apply+'</b></button>';
			if(opt.labeltype==0) s+='</span>';
			if(opt.showApply && opt.labeltype==0) s+=' <button type="button" class="ppapply2" onclick="applyRefines('+this.options.form.name+')">'+words.apply+'</button>';
			if(opt.labeltype==0) s+='<div class="clear"></div>';
	//	}else{
			
	//	}
		lv.innerHTML=s;
	},
	disableMouseWheel:function(event){
		var e=new Event(event);
		e.preventDefault();
		return false;
	}
});
chp_trackbar.implement(new Options);

/*
*	Get absolute top of the element. Mootools 1.1 and 1.3 return different values for el.getTop(). So let's have our own func.
*/
function getY(el){
	var top=0;
	do{
		top += el.offsetTop || 0;
		el = el.offsetParent;
	} while (el);
		
	return top;
}

/*
* Get the parent element with match by className. Mootools 1.1 doesn't have this option.
*/
function getParentElement(el,class_name){
	do{
		if(el.className==class_name)return el;
		el=el.parentNode;
	}while(el);
	return null;
}

function applyRefines(f){
	//var els=document.chpform.querySelectorAll("input[type='hidden']"),
	//var els=f.querySelectorAll("input[type='hidden']"),
	var els=$(f).getElements("input[type='hidden']"),
	hp=document.getElementById('high-price'),
	lp=document.getElementById('low-price');
	for(i=0,j=els.length;i<j;++i){
		if(els[i].value=='') $(els[i]).remove?$(els[i]).remove():$(els[i]).destroy();
	}
	// if(hp && hp.value=='') hp.destroy();
	// if(lp && lp.value=='') lp.destroy();
	if(hp && hp.value=='') hp.name='';
	if(lp && lp.value=='') lp.name='';
//	if(hp && hp.value=='') hp.remove?hp.remove():hp.destroy();
//	if(lp && lp.value=='') lp.remove?lp.remove():lp.destroy();

	if (chpSelfUpdating) {
		ChP2.updateFilters2(f);
	} else {
		f.submit();
	}
}

function clearRefine(el,f){
	var t=chp[el.getAttribute('data-pname')];
	if(t.options.valueLeft==null && t.options.valueRight==null) return;
	t.options.valueLeft=null;
	t.options.valueRight=null;
	t.update();
	applyRefines(f);
}

function clearHover(el,on){
	var t=chp[el.getAttribute('data-pname')];
	if(!on){
		$(el).removeClass('cl-hov');
		return;
	}
	if(t.options.valueLeft==null && t.options.valueRight==null) return;
	$(el).addClass('cl-hov');
}
$$('.ui-save-button').addEvent('click',function(){
	var row=this.getAttribute('data-row'),
		tr=$(row+'-tr');
	if(tr.hasClass('rowsaving'))return;
	if(tr.hasClass('rowselected')){
		$$('.fs-row').each(
			function(el){
				if(el.hasClass('rowselected')){
					saveProduct(el.getAttribute('data-row'));
				}
			}
		);
	}else{
		saveProduct(row);
	}
});

$$('.fs-cell-params').addEvents({
	mouseover:function(){
		try{
			var row=this.getParent().getAttribute('data-row');
			$(row+'-rmpt').removeClass('invisible');
		}catch(err){}
	},
	mouseout:function(){
		try{
			var row=this.getParent().getAttribute('data-row');
			$(row+'-rmpt').addClass('invisible');
		}catch(err){}
	}
});

$$('.fs-cell-name').addEvents({
	mouseover:function(){
		var data=this.getAttribute('data-row');
		drawHoverBox(data,110);
	},
	mouseout:function(){
		removeHoverBox();
	}
});

$$('.fs-cell-tick').addEvents({
	mouseover:function(){
		var data=this.getAttribute('data-row');
		drawHoverBox(data,110);
	},
	mouseout:function(){
		removeHoverBox();
	}
});

$$('.ui-namecell-child').addEvents({
	'mouseover':function(){
		if(document.searchForm.ppid.value){
			this.innerHTML='&larr; Back';
		}else{
			this.innerHTML='Show Children';
		}
	},
	'mouseout':function(){
		this.innerHTML='Parent';
	},
	'click':function(event){
		stopEventPropagation(event);
		setTableLoading();
		resetRefinePaneSelected();
		var form=document.searchForm;
		if(form.ppid.value){ // back
			form.ppid.value='';
			form.skip.value=form.old_skip.value;
			form.old_skip.value='';
			var parameters=getParametersToString(true);
		}else{
			form.ppid.value=this.getAttribute('data');
			form.old_skip.value=this.getAttribute('data-skip');
			form.skip.value='';
			var parameters=getParametersToString(false);
		}
		
		window.location.hash='#'+parameters;
		parameters+='&a=UPMB';
	
		new Ajax (url,{
			method:'get',
			data: parameters,
			evalScripts:true,
			update: $('cmid')
		}).request();
	}
});

function paramMenuCellclick(el){
	var img=$(el).getFirst();
	var value=$(el).getLast();
	var filterElements;
	var clickedButton;
	
	if(filter_dialog==0) {
		clickedButton=simplePopup.activeButton;
	} else {
		clickedButton=activeParamButton;
	}
	
	var multi=clickedButton.getAttribute('data-multi'),
		row=clickedButton.getAttribute('data-row'),
		parametername=clickedButton.getAttribute('data-paramname'),
		input=document[row+"-form"][parametername],
		btndata='',
		valueselected=value.hasClass('selected');
	
	if(filter_dialog==0) {
		filterElements=$$('#'+row+'-param-menu-'+parametername+' .param-menu-cell');
	} else {
		filterElements=$$('#filter-dialog .param-menu-cell');
	}
	
	// deselect
	if(valueselected){
		img.className='element-avail-img';
		value.removeClass('selected');
		if(multi){
			var str='';
			var count=0;
			filterElements.each(
				function(el){
					var p=el.getLast();
					if(p.hasClass('selected')){
						if(str!='')str+=';';
						str+=p.innerHTML;
						count++;
					}
				}
			);
			input.value=btndata=str;
			clickedButton.setProperty('data-btnvalue',str);
			if(count==0){
				clickedButton.innerHTML='<span class="ui-parameter-value-empty">[empty]</span>';
			}else{
				if(str.length>18)str=str.substr(0,18)+'<b class="threedots">..</b>';
				clickedButton.innerHTML=(count>1)? '<span class="prodparam-btn-qty">'+count+'</span> '+str : str;
			}
		}else{
			input.value=btndata="";
			clickedButton.setProperty('data-btnvalue','');
			clickedButton.innerHTML='<span class="ui-parameter-value-empty">[empty]</span>';
		}
	// select
	}else{
		if(multi){
			img.className='element-sel-img';
			value.addClass('selected');
			var str='';
			var count=0;
			filterElements.each(
				function(el){
					var p=el.getLast();
					if(p.hasClass('selected')){
						if(str!='')str+=';';
						str+=p.innerHTML;
						count++;
					}
				}
			);
			input.value=btndata=str;
			clickedButton.setProperty('data-btnvalue',str);
			if(str.length>18)str=str.substr(0,18)+'<b class="threedots">..</b>';
			clickedButton.innerHTML=(count>1)? '<span class="prodparam-btn-qty">'+count+'</span> '+str : str;
		}else{
			input.value=btndata=value.innerHTML;
			clickedButton.setProperty('data-btnvalue',value.innerHTML);
			//clear all others first
			filterElements.each(
				function(eli){
					eli.getFirst().className='element-avail-img';
					eli.getLast().removeClass('selected');
				}
			);
			img.className='element-sel-img';
			value.addClass('selected');
			clickedButton.innerHTML=squeeze(value.innerHTML,18);
		}
	}
	var tr=$(row+'-tr');
	if(tr.hasClass('rowselected')){
		var ptid=tr.getAttribute('data-ptid');
		$$('.fs-row').each(
			function(el){
				if(el.hasClass('rowselected') && el.getAttribute('data-ptid')==ptid){
					var elrow=el.getAttribute('data-row');
					if(elrow!=row){
						var tbtn=$(elrow+'-'+parametername+'-parambtn');
						tbtn.innerHTML=clickedButton.innerHTML;
						tbtn.setProperty('data-btnvalue',btndata);
						if(filter_dialog==0) {
							$(elrow+'-param-menu-'+parametername).innerHTML=$(row+'-param-menu-'+parametername).innerHTML;
						}
						document[elrow+"-form"][parametername].value=btndata;
					}
					
				}
			}
		);
	}
	if(!valueselected && !multi){
		if(filter_dialog==0) {
			simplePopup.removemenu();
			simplePopup.removeBoundEvent();
		} else {
			removeDialog(clickedButton.getAttribute('data'));
		}
	}
}

function prodTypeMenuClick(el) {
	var clickedButton;
	
	if(filter_dialog==0) {
		clickedButton=simplePopup.activeButton;
	} else {
		clickedButton=activeParamButton;
	}
	
	var row=clickedButton.getAttribute('data-row');
	var ptid=el.getAttribute('data-ptid');
	document[row+'-form']['thisptid'].value=ptid;
	clickedButton.innerHTML=el.innerHTML;
	
	var tr=$(row+'-tr');
	if(tr.hasClass('rowselected')){
		$$('.fs-row').each(
			function(el){
				if(el.hasClass('rowselected') && el.getAttribute('data-ptid')=='none'){
					var elrow=el.getAttribute('data-row');
					if(elrow!=row){
						$(elrow+'-ptbtn').innerHTML=clickedButton.innerHTML;
						document[elrow+'-form']['thisptid'].value=ptid;
					}
				}
			}
		);
	}
	
	if(filter_dialog==0) {
		simplePopup.removemenu();
		simplePopup.removeBoundEvent();
	} else {
		removeDialog(clickedButton.getAttribute('data'));
	}
}

$$('.popwindow').addEvent('click',function(){
	$$('.popwindow').removeClass('window-active');
	this.addClass('window-active');
});

if (show_pdesc_button) {
	var pdesc=$('product-description'),
		pdescInner=$('product-description-inner'),
		dragHandle=$('pdesc-draghandle'),
		resizeHandle=$('pdesc-resizehandle');
	pdesc.makeDraggable({
		handle: dragHandle
	});
	
	pdescInner.makeResizable({
		handle: resizeHandle,
		limit: {x:[150,800], y:[100,1000]}
	});
}
if (filter_dialog) {
	var filterDialog = $('filter-dialog'),
		dragHandle = $('filter-dialog-title'),
		resizeNode = $('filter-dialog-body'),
		resizeHandle = $('fd-resizehandle');
	
	filterDialog.makeDraggable({
		handle: dragHandle
	});
	
	resizeNode.makeResizable({
		handle: resizeHandle,
		limit: {x:[150, 800], y:[100, 1000]}
	});
}
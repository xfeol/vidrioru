document.searchForm.q.focus();
function doSearch(word){
	setTableLoading();
	var form=document.searchForm;
	if(word==''){
		$('search-xbtn').setStyle('display','none');
		form.q.value='';
		form.q.focus();}
	else{$('search-xbtn').setStyle('display','inline');}
	
	form.skip.value='';
	var parameters=getParametersToString(0);
	
	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();
	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		onComplete: function(response){
			removeTableLoading();
			form.q.focus();
		},
		update: $('cmid')
	}).request();
}
// get all Parameters into string: 
function getParametersToString(addPage){
	var form=document.searchForm;
	var parameters='i=DP';
	
	
	var q=form.q.value;
	//if(q)parameters+='&q='+escape(q);
	if(q)parameters+='&q=' + encodeURIComponent(q);
	
	var showonpage=form.showonpage.value;
	if(showonpage)parameters+='&showonpage='+showonpage;
	
	var cid=form.cid.value;
	if(cid)parameters+='&cid='+cid;
	
	var ptid=form.ptid.value;
	if(ptid)parameters+='&ptid='+ptid;
	
	var orderby=form.orderby.value;
	if(orderby)parameters+='&orderby='+orderby;
	
	var sc=form.sc.value;
	if(sc)parameters+='&sc='+sc;
	
	var ppid=form.ppid.value;
	if(ppid)parameters+='&ppid='+ppid;
	
	if(addPage){
		var skip=form.skip.value;
		if(skip)parameters+='&skip='+skip;
	}
	return parameters;
}

function setTableLoading(){
	$('cproducts').getFirst().setStyle('position','relative');
	$('blanket').removeClass('hid');
}
function removeTableLoading(){
	//$('blanket').addClass('hid');
}
function resetRefinePaneSelected(){
	$('selGrpImg').setStyle('background-position','0 0');
}

var cloaded=[];
var ptloaded=false;
function getctree(cid,el){
	if(cloaded[el]==1){
		if(el!='cbranch')$(el).getParent().toggleClass('hid');
		return;
	}
	var loader=$(el+'-loader');
	loader.toggleClass('hid');
	parameters='i=DP&a=UPCAT&ctreeid='+el+'&cid='+cid;
		new Ajax (url,{
			method:'get',
			data: parameters,
			evalScripts:true,
			onComplete: function(){
				cloaded[el]=1;
				$(loader).toggleClass('hid');
			},
			update: $(el)
		}).request();
}
function ctreehighlight(el,highlight){
	var tr=$(el).getParent();
	if(highlight==1){
		$(tr.getParent().rows[tr.rowIndex+1]).toggleClass('hov');
		el.toggleClass('hov');
		$(tr.getParent().rows[tr.rowIndex].cells[1]).getFirst().toggleClass('hid');
	}else{ // higlihgt the row, when second column is hovered
		el.toggleClass('hov');
		tr.toggleClass('hov');
		el.getFirst().toggleClass('hid');
	}
}
function setcat(cid,name){
	setTableLoading();
	var len=name.length;
	var stra=name;
	var strb=name;
	if(len>21){stra=name.substr(0,21)+'...';}
	if(len>18){strb=name.substr(0,18)+'...';}
	$('showcat-menu').getFirst().getFirst().innerHTML=stra;
	if(cid==''){
		$('showcat-menu').getFirst().getLast().addClass('hid');
		simplePopup.activeButton.getFirst().innerHTML='Category';
	}else{
		$('showcat-menu').getFirst().getLast().removeClass('hid');
		simplePopup.activeButton.getFirst().innerHTML=strb;
	}
	
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
	
	var form=document.searchForm;
	form.cid.value=cid;
	form.skip.value='';
	
	var parameters=getParametersToString(0);

	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();

	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		update: $('cmid')
	}).request();
}
function getpttree(){
	if(ptloaded) return;
	parameters='i=DP&a=UPPT';
	new Ajax (url,{
		method:'get',
		data: parameters,
	//	evalScripts:true,
		onComplete: function(){
			ptloaded=true;
		},
		update: $('ptbranch')
	}).request();
}
function pttreehighlight(el,highlight){
	var tr=$(el).getParent();
	if(highlight==1){
		el.toggleClass('hov');
		var td=tr.getLast();
		td.toggleClass('hov');
		td.getFirst().toggleClass('hid');
	}else{
		el.toggleClass('hov');
		tr.getFirst().toggleClass('hov');
		el.getFirst().toggleClass('hid');
	}
}
function setpt(ptid,pname){
	setTableLoading();
	var len=pname.length;
	var stra=pname;
	var strb=pname;
	if(len>21){stra=pname.substr(0,21)+'...';}
	if(len>18){strb=pname.substr(0,18)+'...';}
	$('showpt-menu').getFirst().getFirst().innerHTML=stra;
	if(ptid==''){
		$('showpt-menu').getFirst().getLast().addClass('hid');
		simplePopup.activeButton.getFirst().innerHTML='Product Type';
	}else{
		$('showpt-menu').getFirst().getLast().removeClass('hid');
		simplePopup.activeButton.getFirst().innerHTML=strb;
	}
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
	
	var form=document.searchForm;
	form.ptid.value=ptid;
	form.skip.value='';
	
	var parameters=getParametersToString(0);

	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();

	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		update: $('cmid')
	}).request();
}

function orderBy(eli){
	setTableLoading();
	var order=eli.getAttribute('order');
	var form=document.searchForm;
	form.orderby.value=order;
	
	var parameters=getParametersToString(1);
	
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
	
	$$('#orderby-menu .tabletopmenu-el').each(
		function(el){
			var ord=el.getAttribute('order');
			var str=el.innerHTML;
			if(order==ord){
				str+='*';
			}else{
				str=str.replace('*','');
			}
			el.innerHTML=str;
		}
	);
	
	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();
	
	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		update: $('cmid')
	}).request();
}

function orderAscDesc(btn){
	setTableLoading();
	var form=document.searchForm;
	var scending=form.sc.value;
	if(scending==''){
		scending='Asc';
	}else{
		scending=(scending=='Asc')?'Desc':'Asc';
	}
	form.sc.value=scending;
	btn.innerHTML=scending;
	$(btn).setProperty('title','Current ordering: '+scending);
	var parameters=getParametersToString(1);
	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();
	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		update: $('cmid')
	}).request();
}

var noticeTimeout;
function showNotice(msg){
	var msgbox=$('notice-msg');
	var top=$('refine-pane').offsetTop;
	msgbox.innerHTML=msg;
	msgbox.removeClass('hid');
	var height=msgbox.offsetHeight;
	msgbox.setStyle('top',top-height);
	clearTimeout(noticeTimeout);
	noticeTimeout=setTimeout("removeNotice()",5000);
}
function removeNotice(){
	$('notice-msg').addClass('hid');
}


function windowClose(winid){
	var win=$(winid);
	if(win.hasClass('active')) return;
	win.addClass('active');
	
	var myEffect = win.effect('opacity', {
		duration: 150,
		transition: Fx.Transitions.Quad.easeOut,
		onComplete:function(){
			win.removeClass('active');
			win.addClass('hid');
		}
	});
	myEffect.start(1,0);
}
function closeFilterDialog(id){
	highlightedProduct.removeClass('underlined');
	filterAjax.cancel();
	$('blanket2').addClass('hid');
	activeParamButton=null;
	windowClose(id);
}
function removeDialog(id){
	highlightedProduct.removeClass('underlined');
	$(id).addClass('hid');
	activeParamButton=null;
}


function showProductDescription(event,pid){
	var pdesc=$('product-description'),
		pdescinner=$('product-description-inner');
	
	stopEventPropagation(event);
	if (pdesc.hasClass('hid')) pdesc.setStyles({'top':window.getScrollTop()+70, opacity:1, left:7});
	pdesc.removeClass('hid');
	var parameters='i=DP&a=UPPD&pid='+pid;
	
	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		onRequest: function(){
			pdescinner.innerHTML='<div class="pdesc-preload"><img src="/administrator/components/com_fastseller/images/desc-loader.gif" width="28" height="28" border="0" /></div>';
		},
		update: pdescinner
	}).request();
}

function saveProduct(row){
	var table=document.getElementById('fsProductListTable'),
		tr=document.getElementById(row+'-tr'),
		form=document[row+'-form'],
		ptid=form['thisptid'].value,
		deleteCategorySeparator=false;
		
	if(tr.hasClass('rowsaving')) return;
	if(ptid=='')return;
	
	tr.addClass('rowsaving');
	//form.send({
	var formData = new Ajax (url, {
		data: form,
		onComplete:function(response){
			if(form['ptid'].value=='wopt'){
				//alert('should remove');
				if(tr.hasClass('rowselected')) rowsSelected-=1;
				currentTotalRows-=1;
				if(rowsSelected==0){
					$('selGrpImg').setStyle('background-position','0 0');
				}else if(rowsSelected==currentTotalRows){
					$('selGrpImg').setStyle('background-position','0 -26px');
				}
				
				for(i=0, j=table.rows.length; i<j; i++) {
					if(table.rows[i] == tr) {
						if(i>0 && !table.rows[i-1].hasClass('fs-row') && (!table.rows[i+1] || (table.rows[i+1] && !table.rows[i+1].hasClass('fs-row')))) deleteCategorySeparator=true;
						table.deleteRow(i);
						if(deleteCategorySeparator) table.deleteRow(i-1);
					}
				}
			}
			else if(response){
				$(row+'-dynamic-container').innerHTML=response;
				form['adding'].value='parameters';
				tr.setProperty('data-ptid',ptid);
				$(row+'-expander').removeClass('invisible');
				var ptidinfo=$$('#'+row+'-tr .ui-namecell-ptid')[0];
				ptidinfo.innerHTML='ptid: '+ptid;
				ptidinfo.removeClass('hid');
			}
			tr.removeClass('rowsaving');
		}
	});
	
	formData.request();
}


function toggleRowSelected(el){
	var tr=$(el).getParent();
	var img=$(el).getLast();
	if(tr.hasClass('rowselected')){
		tr.removeClass('rowselected');
		img.className='element-avail-img';
		rowsSelected-=1;
		if(rowsSelected==0){
			$('selGrpImg').setStyle('background-position','0 0');
		}else{
			$('selGrpImg').setStyle('background-position','0 -65px');
		}
		if($('notice-msg').hasClass('hid')==false) showNotice('<b>'+rowsSelected+'</b> row(s) selected');
	}else{
		tr.addClass('rowselected');
		img.className='element-rowsel-img';
		rowsSelected+=1;
		if(rowsSelected==currentTotalRows){
			$('selGrpImg').setStyle('background-position','0 -26px');
		}else{
			$('selGrpImg').setStyle('background-position','0 -65px');
		}
		if($('notice-msg').hasClass('hid')==false) showNotice('<b>'+rowsSelected+'</b> row(s) selected');
	}
	
}

function selectAll(){
	for(i=0; i<totalRows; i++){
		try{
			var tr=$('r-'+i+'-tr');
			tr.addClass('rowselected');
			tr.getFirst().getLast().className='element-rowsel-img';
		}catch(err){}
	}
	if(currentTotalRows!=0)$('selGrpImg').setStyle('background-position','0 -26px');
	rowsSelected=currentTotalRows;
	showNotice('<b>'+rowsSelected+'</b> row(s) selected');
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
}
function selectNone(){
	for(i=0;i<totalRows;i++){
		try{
			var tr=$('r-'+i+'-tr');
			tr.removeClass('rowselected');
			tr.getFirst().getLast().className='element-avail-img';
		}catch(err){}
	}
	$('selGrpImg').setStyle('background-position','0 0');
	rowsSelected=0;
	showNotice('Nothing selected');
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
}
function selectWPT(){
	var counter=0;
	for(i=0;i<totalRows;i++){
		try{
			var tr=$('r-'+i+'-tr');
			if(tr.getAttribute('data-ptid')!='none'){
				counter+=1;
				tr.addClass('rowselected');
				tr.getFirst().getLast().className='element-rowsel-img';
			}else if(tr.hasClass('rowselected')){
				tr.removeClass('rowselected');
				tr.getFirst().getLast().className='element-avail-img';
			}
		}catch(err){}
	}
	if(counter==currentTotalRows && counter!=0){
		$('selGrpImg').setStyle('background-position','0 -26px');
	}else if(counter==0){
		$('selGrpImg').setStyle('background-position','0 0');
	}else{
		$('selGrpImg').setStyle('background-position','0 -65px');
	}
	rowsSelected=counter;
	showNotice('<b>'+rowsSelected+'</b> row(s) selected');
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
}
function selectWOPT(){
	var counter=0;
	for(i=0;i<totalRows;i++){
		try{
			var tr=$('r-'+i+'-tr');
			if(tr.getAttribute('data-ptid')=='none'){
				counter+=1;
				tr.addClass('rowselected');
				tr.getFirst().getLast().className='element-rowsel-img';
			}else if(tr.hasClass('rowselected')){
				tr.removeClass('rowselected');
				tr.getFirst().getLast().className='element-avail-img';
			}
		}catch(err){}
	}
	if(counter==currentTotalRows && counter!=0){
		$('selGrpImg').setStyle('background-position','0 -26px');
	}else if(counter==0){
		$('selGrpImg').setStyle('background-position','0 0');
	}else{
		$('selGrpImg').setStyle('background-position','0 -65px');
	}
	rowsSelected=counter;
	showNotice('<b>'+rowsSelected+'</b> row(s) selected');
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
}
	
function callRowExpander(row){
	var tr=$(row+'-tr');
	if(tr.getAttribute('data-ptid')=='none')return;
	var cont=$(row+'-content');
	var collapsed=cont.hasClass('collapsed');
//	if(tr.hasClass('rowselected')){
//		$$('.fs-row').each(
//			function(el){
//				if(el.hasClass('rowselected')){
//					expandCollapse(el.getAttribute('data-row'),collapsed);
//				}
//			}
//		);
//	}else{
		if(collapsed){
			cont.removeClass('collapsed');
			$(row+'-expander').innerHTML='Collapse';
		}else{
			cont.addClass('collapsed');
			$(row+'-expander').innerHTML='Expand';
		}
//	}
}

function expandCollapse(row,expand){
	var el=$(row+'-content');
	if(el==undefined)return;
	if(el.hasClass('collapsed') && expand){
		el.removeClass('collapsed');
		$(row+'-expander').innerHTML='Collapse';
	}else if(!el.hasClass('collapsed') && !expand){
		el.addClass('collapsed');
		$(row+'-expander').innerHTML='Expand';
	}
}

function expandAll(){
	for (var i=0; i<totalRows; i++){
		expandCollapse('r-'+i,1);
	}
}
function collapseAll(){
	for (var i=0; i<totalRows; i++){
		expandCollapse('r-'+i,0);
	}
}

function drawHoverBox(data,offset){
	var tr=$(data+'-tr');
	var td=$(data+'-td');
	var p=td.getPosition();
	var s=td.getSize().size;
	var hovb=$('pl-hover-box');
	hovb.setStyle('display','none'); // when expand area, we want to redraw hover box
	hovb.setStyles({
		top:p.y,
		left:p.x,
		width:s.x+offset,
		height:s.y,
		display:'block'
	});
}
function removeHoverBox(){
	var hovb=$('pl-hover-box');
	hovb.setStyle('display','none');
}

function rmPTInfoDialog(row){
	var duration=400+Math.floor(Math.random()*201)-100; // add some random [-100;100]
	var parent=$(row+'-rmpt');
	var target=parent.getLast();
	
	if(target.hasClass('active')){clearTimeout(timeout);}
	else{target.addClass('active');}
	
	target.toggleClass('show');
	if(target.hasClass('show')){
		bounceIn(target.style.marginRight.toInt(),0,0,duration,target);
	}else{
		bounceIn(target.style.marginRight.toInt(),-120,0,duration,target);
	}
}
function bounceIn(startpos,endpos,curtime,duration,el){
	var nextframe=34; // microsec/frame
	if(curtime<duration){
		var p=1-Math.pow(2,-8*(curtime/duration));
		$(el).setStyle('margin-right',p*(endpos-startpos)+startpos);
		timeout=setTimeout(function(){bounceIn(startpos,endpos,curtime+nextframe,duration,el)},nextframe);
	}else{
		$(el).setStyle('margin-right',endpos);
		$(el).removeClass('active');
	}
}
function rmPTInfoCancel(row){
	rmPTInfoDialog(row);
}
function rmPTInfoRemove(el,row){
	var table=document.getElementById('fsProductListTable'),
		tr=document.getElementById(row+'-tr'),
		hash=el.getAttribute('href'),
		parameters=hash.substr(1),
		deleteCategorySeparator;
		
	if(tr.hasClass('rowsaving')){
		alert('Can\'t remove Ptoduct Type while processing.');
		return;
	}
	
	tr.addClass('rowsaving');
	new Ajax (url,{
		method:'get',
		data:parameters,
		evalScripts:true,
		onComplete: function(response){
			if(response){
				$(row+'-dynamic-container').innerHTML=response;
				var form=document[row+'-form'];
				form['adding'].value='pt';
				form['thisptid'].value='';
				tr.setProperty('data-ptid','none');
				$(row+'-expander').addClass('invisible');
				$$('#'+row+'-tr .ui-namecell-ptid')[0].addClass('hid');
				tr.removeClass('rowsaving');
			}else{
				if(tr.hasClass('rowselected'))rowsSelected-=1;
				currentTotalRows-=1;
				if(rowsSelected==0){
					$('selGrpImg').setStyle('background-position','0 0');
				}else if(rowsSelected==currentTotalRows){
					$('selGrpImg').setStyle('background-position','0 -26px');
				}
				
				for(i=0, j=table.rows.length; i<j; i++) {
					if(table.rows[i] == tr) {
						if(i>0 && !table.rows[i-1].hasClass('fs-row') && (!table.rows[i+1] || (table.rows[i+1] && !table.rows[i+1].hasClass('fs-row')))) deleteCategorySeparator=true;
						table.deleteRow(i);
						if(deleteCategorySeparator) table.deleteRow(i-1);
					}
				}
			}
		}
	}).request();
}

function setpage(el,skip){
	if(el.className=='pager-available'){
		el2=$$('#cpages button.pager-selected');
		el2.addClass('notbold');
		el.className='pager-selected';
		document.searchForm.skip.value=skip;
		
		var parameters=el.getAttribute('href');
		window.location.hash='#'+parameters;
		parameters+='&a=UPMB';
		resetRefinePaneSelected();
		new Ajax (url,{
			method:'get',
			data: parameters,
			evalScripts:true,
			onRequest: function(){
				setTableLoading();
				var h=$('csearch').offsetTop-10;
				window.scroll(0,h);
			},
			update: $('cmid')
		}).request();
	}
}
function setshowonpage(el) {
	var showCount = el.innerHTML;
	simplePopup.activeButton.getFirst().innerHTML = showCount;
	simplePopup.removemenu();
	simplePopup.removeBoundEvent();
	
	var form=document.searchForm;
	form.showonpage.value = showCount;
	form.skip.value='';
	var hash=el.href;
	var parameters=hash.substr(hash.indexOf('#')+1);
	window.location.hash='#'+parameters;
	parameters+='&a=UPMB';
	resetRefinePaneSelected();
	
	Cookie.set('onpage', showCount, {path: '/', duration: false});
	
	new Ajax (url,{
		method:'get',
		data: parameters,
		evalScripts:true,
		onRequest: function(){
			setTableLoading();
			var h=$('csearch').offsetTop-10;
			window.scroll(0,h);
		},
		update: $('cmid')
	}).request();
	
	
}
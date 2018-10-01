function goBack(){
	var p='i=PT';
	window.location.hash=p;
	new Ajax (url,{
		method:'get',
		evalScripts:true,
		data: p,
		update:$('clayout')
	}).request();
	
}
function setActiveTab(tab){
	var index=tab.getAttribute('data-tab'),
		formToHide=document.getElementById('ptpForm'+currentTabIndex),
		formToShow=document.getElementById('ptpForm'+index);
		//input=formToShow.getElementsByClassName('paramName')[0];
	if(tab.hasClass('tab-selected')) return;
	$$('#ptpNavTabs li').removeClass('tab-selected');
	tab.addClass('tab-selected');
	if(formToHide) formToHide.addClass('hid');
	formToShow.removeClass('hid');
	currentTabIndex=index;
	//console.log(input);
	//input.focus();
}

function tabHoverOnOut(tab, hovered) {
	var upBtn=tab.getElementsByClassName('ptp-tabUpBtn')[0],
		list=document.getElementById('ptpNavTabs');
	if(tab==list.children[0]) return;
	if(hovered){
		upBtn.addClass('opaque');
	}else{
		upBtn.removeClass('opaque');
	}
}

function moveTabUp(btn, event) {
	var list=document.getElementById('ptpNavTabs'),
		listElements=list.getChildren(),
		currentTab=btn.getParent(),
		i, j, temp, data1, data2, list_order;
	for(i=0, j=listElements.length; i<j; i++){
		if(currentTab==listElements[i]){
			if(i==0) return;
			stopEventPropagation(event);
			btn.removeClass('opaque');
			temp=listElements[i-1].innerHTML;
			listElements[i-1].innerHTML=listElements[i].innerHTML;
			listElements[i].innerHTML=temp;
			
			temp=listElements[i-1].id;
			listElements[i-1].id=listElements[i].id;
			listElements[i].id=temp;
			data1=listElements[i-1].getAttribute('data-tab');
			data2=listElements[i].getAttribute('data-tab');
			listElements[i-1].setProperty('data-tab', data2);
			listElements[i].setProperty('data-tab', data1);
			
			document.getElementById('list_order_'+data1).value=listElements[i].getAttribute('data-order');
			document.getElementById('list_order_'+data2).value=listElements[i-1].getAttribute('data-order');
			
			//console.log(document.getElementById('list_order_'+data1), listElements[i-1].getAttribute('data-order'));
			
			if(listElements[i-1].hasClass('tab-selected')){
				listElements[i-1].removeClass('tab-selected');
				listElements[i].addClass('tab-selected');
			}else if(listElements[i].hasClass('tab-selected')){
				listElements[i].removeClass('tab-selected');
				listElements[i-1].addClass('tab-selected');
			}
		}
	}
}

function addParameter(){
	var list=document.getElementById('ptpNavTabs'),
		numberOfTabs=list.getChildren().length,
		formsCont=document.getElementById('ptpForms'),
		newTab=document.createElement('li'),
		newForm=document.createElement('div'),
		tabClass='ptp-navTab',
		lastTab,
		parameters='i=PT&a=PFORM';
	if(document.getElementById('ptpParamCont').hasClass('saving')) return;
	newTab.innerHTML='<span class="ptp-tabParamLabel ptp-newParam">[Label]</span> <span class="ptp-tabParamName ptp-newParam">([Name])</span><div class="ptp-tabUpBtn" onclick="moveTabUp(this, event)">&uarr;</div>';
	if(numberOfTabs==0){
		tabClass+=' tab-first tab-last';
		formsCont.innerHTML='';
	}else{
		lastTab=list.children[numberOfTabs-1];
		lastTab.removeClass('tab-last');
		tabClass+=' tab-last';
		//console.log(lastTab);
	}
	newTab.id="ptpNavTab"+generalNumberOfTabs;
	newTab.className=tabClass;
	newTab.setProperty('onclick','setActiveTab(this)');
	newTab.setProperty('onmouseover','tabHoverOnOut(this,1)');
	newTab.setProperty('onmouseout','tabHoverOnOut(this,0)');
	newTab.setProperty('data-tab',generalNumberOfTabs);
	newTab.setProperty('data-order',generalNumberOfTabs);
	list.appendChild(newTab);
	newForm.id='ptpForm'+generalNumberOfTabs;
	newForm.className='ptp-formCont hid';
	newForm.innerHTML='loading..';
	formsCont.appendChild(newForm);
	
	parameters+='&tabno='+generalNumberOfTabs;
	//numberOfNewTabs++;
	generalNumberOfTabs++;
	
	new Ajax (url,{
		method:'get',
		data: parameters,
		onComplete:function(){
			var inpt=newForm.getElementsByClassName('paramName')[0].focus();
		},
		update:newForm
	}).request();
	setActiveTab(newTab);
}

function deleteParameter(event, index, key) {
	var form=document.getElementById('ptpForm'+index),
		tab=document.getElementById('ptpNavTab'+index),
		list=document.getElementById('ptpNavTabs'),
		listElements=list.getChildren(),
		data='&i=PT&a=REMPAR',
		tabToActivate, parameterName, i, j;
	event.preventDefault();
	if(document.getElementById('ptpParamCont').hasClass('saving')) return;
	if(!confirm('Delete parameter?')) return;
	for(i=0, j=listElements.length; i<j; i++) {
		if(listElements[i]==tab) {
			if(listElements[i+1]) {
				tabToActivate=listElements[i+1];
				if(i==0) listElements[i+1].addClass('tab-first');
				break;
			}else if(listElements[i-1]) {
				tabToActivate=listElements[i-1];
				listElements[i-1].addClass('tab-last');
				break;
			}
		}
	}
	parameterName=document.getElementById('parameter_name_active_'+key).value;
	data+='&parameter_name='+parameterName;
	data+='&ptid='+document.parametersForm.ptid.value;
	
	form.remove();
	tab.remove();
	if(tabToActivate) setActiveTab(tabToActivate);
	if(parameterName) {
		new Ajax (url,{
			method:'get',
			data: data
		}).request();
	}
	//console.log(tabToActivate);
}

function saveParameters() {
	var form=document.parametersForm,
		//keys=form.elements['key[]'],
		cont=document.getElementById('ptpParamCont');
	
	if(cont.hasClass('saving')) return;
	if( !parameterNamesAreValid() && !confirm('Some parameters will not be updated. Proceed?') ) return;
	
	cont.addClass('saving');
	form.send({
		onComplete:function(){
			updateActiveParameterNames();
			cont.removeClass('saving');
		},
		onFailure:function(response){
			showErrorMessage(response.responseText);
		}
	});
	
	//console.log(keys);
}

function updateActiveParameterNames() {
	var form=document.parametersForm,
		keys=form.elements['key[]'],
		i, j;
	if (!keys) return;
	if (!keys.length) {
		updateActiveParameterNameByKey(keys.value);
	} else {
		for(i=0, j=keys.length; i<j; i++){
			updateActiveParameterNameByKey(keys[i].value)
		}
	}
}

function updateActiveParameterNameByKey(key) {
	var name=document.getElementById('parameter_name_'+key),
		activeName=document.getElementById('parameter_name_active_'+key);
	if(name.value!=activeName.value && parameterNameIsValid(name.value)){
		activeName.value=name.value;
		//console.log(name.value);
	}
}

function setParameterName(el){
	var index=el.getAttribute('data-tab'),
		name=el.value,
		//nameNode=document.getElementById('ptpNavTab'+index).getLast();
		nameNode=document.getElementById('ptpNavTab'+index).getElementsByClassName('ptp-tabParamName')[0];
	if(trim) name=trim(name);
	nameNode.innerHTML='('+name+')';
	if(nameNode.hasClass('ptp-newParam')) nameNode.removeClass('ptp-newParam');
	
	checkParamNameIsValid(el);
}

function checkParamNameIsValid(el){
	var index=el.getAttribute('data-tab'),
		name=el.value,
		goodName=true,
		nameNode=document.getElementById('ptpNavTab'+index).getLast(),
		allNames=document.getElementsByClassName('paramName');
		
	if(trim) name=trim(name);
	name=name.toLowerCase();
	
	if(name=='' || !parameterNameIsValid(name)){
		el.addClass('paramNameBad');
		nameNode.addClass('tabParamNameBad');
		return;
	}
	
	for(i=0,j=allNames.length; i<j; i++) {
		if((el!=allNames[i]) && (name==allNames[i].value.toLowerCase())){
			goodName=false;
			el.addClass('paramNameBad');
			nameNode.addClass('tabParamNameBad');
			break;
		}
	}
	
	if(goodName && el.hasClass('paramNameBad')){
		el.removeClass('paramNameBad');
		nameNode.removeClass('tabParamNameBad');
	}
}

function parameterNameIsValid(name) {
	var res;
	if(name=="") return false;
	res=name.match(/[^a-zA-z0-9_]/);
	return (res===null)? true : false;
}

function parameterNamesAreValid() {
	var allNames=document.getElementsByClassName('paramName'),
		ok=true,
		i, j;
	for(i=0,j=allNames.length; i<j; i++) {
		if(allNames[i].hasClass('paramNameBad') || allNames[i].value=='') {
			ok=false;
			break;
		}
	}
	
	return ok;
}

function setParameterLabel(el){
	var index=el.getAttribute('data-tab'),
		label=el.value,
		labelNode=document.getElementById('ptpNavTab'+index).getFirst();
	if(label==''){
		labelNode.innerHTML='[Label]';
		if(!labelNode.hasClass('ptp-newParam')) labelNode.addClass('ptp-newParam');
	}else{
		labelNode.innerHTML=label;
		if(labelNode.hasClass('ptp-newParam')) labelNode.removeClass('ptp-newParam');
	}
}

function calculateNumberOfFilters(el,event){
	var s=el.value,
		filters=[],
		count=0,
		index=el.getAttribute('data-tab'),
		node=document.getElementById('numberOfFilters_'+index).getLast(),
		skipKeyCodes=[16,17,18,35,36,37,38,39,40],
		keyPressed=event.keyCode;
	if(inArray(keyPressed, skipKeyCodes)) return;
	if(s){
		filters=s.split(";");
		count=filters.length;
		if(filters[count-1].replace(/\s/g,"")=="") count-=1;
	}
	node.innerHTML=count;
	//console.log(filters);
	//console.log(count);
	//console.log(event);
}

function validateSelected(el){
	var index=el.getAttribute('data-tab'),
		mode=document.getElementById('parameter_mode_'+index),
		type=document.getElementById('parameter_type_'+index),
		options=type.options,
		i, j;
	if(mode.value==2){
		for(i=0,j=options.length; i<j; i++){
			if(options[i].value=="V"){
				options[i].disabled=true;
			}
			if(options[i].value=="S"){
				options[i].selected=true;
			}
		}
	}else{
		for(i=0,j=options.length; i<j; i++){
			if(options[i].value=="V"){
				options[i].disabled=false;
				break;
			}
		}
	}
	//console.log(options,mode,type);
}

function showHideHing(el,show){
	var node=document.getElementById(el.id+'_hint');
	if(show) {
		node.addClass('show');
	}else{
		node.removeClass('show');
	}

}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

function showErrorMessage(s){
	var div, temp, msg, left,
		cont=document.getElementById('clayout');
	temp=document.createElement('div');
	temp.innerHTML=s;
	msg=temp.getElementsByTagName('table')[0].innerHTML;
	msg+='<div style="margin:30px 0 0 0;text-align:center"><button onclick="removeErrorMessage()">Close</button></div>';
	div=document.createElement('div');
	div.className='errorMessage';
	div.innerHTML=msg;
	left=(window.getScrollWidth()-400)/2;
	div.style.left=left+'px';
	cont.appendChild(div);
	div.addClass('opaque');
}

function removeErrorMessage(){
	var cont=document.getElementsByClassName('errorMessage')[0],
		parent=document.getElementById('clayout'),
		ptp=document.getElementById('ptpParamCont');
	cont.removeClass('opaque');
	setTimeout(function(){
		parent.removeChild(cont);
		ptp.removeClass('saving');
	}, 200);
}

function checkFilterColumnSize(el) {
	var currentSize=el.value.length,
		data='&i=PT&a=INCR';
	if(currentSize>filterColumnSize) {
		data+='&size='+currentSize;
		new Ajax (url,{
			method:'get',
			data: data,
			onComplete:function(response){
				filterColumnSize=response;
			}
		}).request();
	}
}
var filterAjax;
function filterSelectDialog(btn){
	var win=$('filter-dialog'),
		titleNode = $('filter-dialog-title'),
		bodyNode = $('filter-dialog-body'),
		blanket = $('blanket2');

	var paramname=btn.getAttribute('data-paramname');
	var btnvalue=btn.getAttribute('data-btnvalue');
	var row=btn.getAttribute('data-row');
	var form=document[row+'-form'];
	var pid=form['pid'].value;
	var ptid=form['thisptid'].value;
	var parameters='i=DP&a=UPF&pid='+pid+'&ptid='+ptid+'&paramname='+paramname+'&f='+btnvalue;
	
	var shouldCenter = false;
	if (win.hasClass('hid')) shouldCenter = true;

	win.removeClass('hid');	
	var left=(window.getSize().size.x-win.offsetWidth)/2;
	if (left < 0) left = 5;
	if (shouldCenter) win.setStyles({'top':window.getScrollTop()+70, left:left});
	win.setStyle('opacity',1);
	
	blanket.setStyles({top:0, left:0, height:titleNode.offsetHeight + bodyNode.offsetHeight + 7, width:'100%'});
	
	blanket.removeClass('hid');
	activeParamButton = btn;
	
	if (highlightedProduct) highlightedProduct.removeClass('underlined');
	highlightedProduct = $$('#'+row+'-tr .ui-namecell-name');
	highlightedProduct.addClass('underlined');
	
	if(filterAjax) filterAjax.cancel();
	
	filterAjax = new Ajax (url, {
		method: 'get',
		data: parameters,
		//evalScripts:true,
		onComplete: function(){
			blanket.addClass('hid');
			if (shouldCenter) {
				var left=(window.getSize().size.x-win.offsetWidth)/2;
				if (left < 0) left = 5;
				win.setStyles({'top':window.getScrollTop()+70, left:left});
			}
		},
		update: bodyNode
	}).request();
}
function ptSelectDialog(btn){
	var win=$('filter-dialog'),
		titleNode = $('filter-dialog-title'),
		bodyNode = $('filter-dialog-body');
	
	var shouldCenter = false;
	if (win.hasClass('hid')) shouldCenter = true;
	
	win.removeClass('hid');
	var left = (window.getSize().size.x-win.offsetWidth)/2;
	if (left < 0) left = 5;
	if (shouldCenter) win.setStyles({'top':window.getScrollTop() + 70, left:left});
	win.setStyle('opacity',1);
	
	activeParamButton=btn;
	var row=btn.getAttribute('data-row');
	
	if(highlightedProduct) highlightedProduct.removeClass('underlined');
	highlightedProduct=$$('#'+row+'-tr .ui-namecell-name');
	highlightedProduct.addClass('underlined');
	
	if(!ptCache){
		var blanket=$('blanket2');
		blanket.setStyles({top:0, left:0, height:titleNode.offsetHeight + bodyNode.offsetHeight + 7, width:'100%'});
		blanket.removeClass('hid');
		var parameters='i=DP&a=UPPTD';
		
		if(filterAjax) filterAjax.cancel();
		filterAjax=new Ajax (url,{
			method:'get',
			data: parameters,
			//evalScripts:true,
			onComplete: function(response){
				ptCache=response;
				blanket.addClass('hid');
				if (shouldCenter) {
					var left = (window.getSize().size.x-win.offsetWidth)/2;
					if (left < 0) left = 5;
					win.setStyles({'top':window.getScrollTop() + 70, left:left});
				}
				
				bodyNode.setStyle('height', '200px');
			},
			update: bodyNode
		}).request();
	}else{
		bodyNode.innerHTML=ptCache;
		var left=(window.getSize().size.x-win.offsetWidth)/2;
		if(left<0)left=5;
		win.setStyles({'top':window.getScrollTop()+70, left:left});
	}
}
var ChP2 = {
	init : function() {
	window.addEvent("domready", function() {	
		$('chpNav' + module_id).addEvent('click:relay(a)',function(event){
			if (!this.hasClass('chp-show-results')) {
				event.preventDefault();
				ChP2.updateFilters(this);
			}
		});
		
		$('chpNav' + module_id).addEvent('submit:relay(form)',function(event){
			event.preventDefault();
			ChP2.updateFilters2(this);
			//chpGoBtn
		
		});
	});
	
	},
	
	updateFilters : function(a) {
		var link=a.getAttribute('href');
		var container=$('chpNav' + module_id);
		href=link.substr(link.indexOf('?')+1);
		href+='&mid=' + module_id;
		href+='&category_id=' + category_id;
		
		if(window.Ajax){
			new Ajax (getModuleUrl,{
				method:'get',
				data: href,
			//	evalScripts:true,
				onRequest: function(){
					container.addClass('chp-loading');
				},
				onComplete: function(){
					container.removeClass('chp-loading');
				},
				update: container
			}).request();
		}
		else {
			new Request.HTML ({
				url: getModuleUrl,
				method:'get',
				data: href,
				//evalScripts:true,
				onRequest: function(){
					container.addClass('chp-loading');
				},
				onComplete: function(){
					container.removeClass('chp-loading');
				},
				update: container
			}).send();
		}
	},
	
	updateFilters2 : function(form) {
		var container=$('chpNav' + module_id);
		
		new Request.HTML ({
				url: getModuleUrl,
				method:'get',
				data: form.toQueryString() + '&mid=' + module_id,
				//evalScripts:true,
				onRequest: function(){
					container.addClass('chp-loading');
				},
				onComplete: function(){
					container.removeClass('chp-loading');
				},
				update: container
			}).send();
	
	}
	

}
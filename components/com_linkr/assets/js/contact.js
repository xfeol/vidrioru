
var ConOpts={title:'Contacts',objName:'LinkrContact',initialize:function(){this.options.title=this._('CONTACTS')}};var ConSrch={defaultType:'category',defaultSearchType:'contact',sql:{category:function(id,q,o){var sql='SELECT id, title'+' FROM #__categories'+' WHERE published = 1'+' AND access <= '+Linkr.userAccessLevel+' AND section = '+Linkr.dbQuote('com_contact_details');if(q){sql+=' AND (LOWER(title) LIKE '+q+' OR LOWER(name) LIKE '+q+' OR LOWER(description) LIKE '+q+')'}if($type(id)=='number')sql+=' AND id = '+id;return sql+' ORDER BY ordering'},contact:function(id,q,o){var sql=' SELECT id, name, con_position AS position'+' FROM #__contact_details'+' WHERE published = 1'+' AND access <= '+Linkr.userAccessLevel;if(q){sql+=' AND (LOWER(name) LIKE '+q+' OR LOWER(con_position) LIKE '+q+' OR LOWER(address) LIKE '+q+' OR LOWER(suburb) LIKE '+q+' OR LOWER(state) LIKE '+q+' OR LOWER(country) LIKE '+q+' OR LOWER(postcode) LIKE '+q+' OR LOWER(telephone) LIKE '+q+' OR LOWER(fax) LIKE '+q+' OR LOWER(misc) LIKE '+q+' OR LOWER(email_to) LIKE '+q+')'}if($type(id)=='number')sql+=' AND catid = '+id;return sql+' ORDER BY ordering'}}};var ConLay={category:function(dbc){if($type(dbc)=='number'){var q='SELECT id, title, description, '+' CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(":", id, alias)'+' ELSE id END AS slug'+' FROM #__categories'+' WHERE section = '+Linkr.dbQuote('com_contact_details')+' AND published = 1 AND id = '+dbc;return this.tmpl.dbObject(q,this.layout.category.bind(this))}var e=this.tmpl.isError(dbc);if(e!==false)return this.display(e);var d=false;var c=dbc.result;var id=c.id.toInt();var t=this.UTF8de(c.title);if(c.description.length>0)d={text:this.UTF8de(c.description)};this.layout._genericLayoutDelayed({description:d,returnPage:['category',id],toolbar:{back:this.home.bind(this),config:true,insert:this.insert.bind(this)},header:{a:[this._('CATEGORY'),t],contactList:{type:'contact',databaseID:id,label:this._('CONTACTS')+': '}},config:{text:t,url:{query:['view=category','id='+id],options:{componentid:7,defaultURL:this.tmpl.url('category',c.slug)}},format:[['html','HTML','selected'],['rss','RSS'],['atom','Atom']]}})},contact:function(dbc){if($type(dbc)=='number'){var q='SELECT c.id, c.name, c.con_position AS pos, c.address,'+' c.telephone, c.misc, c.email_to AS email, c.catid,'+' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias)'+' ELSE c.id END AS slug,'+' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias)'+' ELSE cc.id END AS cslug'+' FROM #__contact_details AS c'+' LEFT JOIN #__categories AS cc ON c.catid = cc.id'+' WHERE c.published = 1 AND c.id = '+dbc;return this.tmpl.dbObject(q,this.layout.contact.bind(this))}var e=this.tmpl.isError(dbc);if(e!==false)return this.display(e);var c=dbc.result;var id=c.id.toInt();var n=np=this.UTF8de(c.name);if(c.pos.length>0)np+=' ('+this.UTF8de(c.pos)+')';var d=this.UTF8de(c.email)+'<br />';if(c.address.length>0)d+=this.UTF8de(c.address)+'<br />';if(c.telephone.length>0)d+=this.UTF8de(c.telephone)+'<br />';if(c.misc.length>0)d+=this.UTF8de(c.misc)+'<br />';this.layout._genericLayoutDelayed({description:{text:d},returnPage:['contact',id],toolbar:{back:this.layout.category.bind(this,c.catid.toInt()),config:true,insert:this.insert.bind(this)},header:{a:[this._('CONTACT'),np]},config:{text:n,url:{query:['view=contact','id='+id],options:{componentid:7,defaultURL:this.tmpl.url('contact',c.slug,c.cslug)}}}})}};var ConTmpl={url:function(t,id,cid){var u='index.php?option=com_contact&view='+t;switch(t){case'category':u+='&catid='+id;break;case'contact':if(cid&&cid!='null')u+='&catid='+cid+'&id='+id;else u+='&id='+id;break}return u}};
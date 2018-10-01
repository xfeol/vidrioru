function edit(n){
	var table=document.getElementById('ptList'),
		row=table.rows[n],
		name_cell=row.cells[2],
		desc_cell=row.cells[3],
		desc_width=desc_cell.offsetWidth-23;
		edit_btn=row.cells[5].getElementsByClassName('ptm-edit')[0],
		save_btn=row.cells[5].getElementsByClassName('ptm-save')[0];
	
	if(row.hasClass('rowsaving')) return;
	
	row.addClass('editing');
	name_cell.innerHTML='<input type="text" name="name" value="'+name_cell.getFirst().innerHTML+'" />';
	desc_cell.innerHTML='<textarea name="desc" rows="1" style="width:'+desc_width+'px">'+desc_cell.getFirst().innerHTML+'</textarea>';
	edit_btn.addClass('hid');
	save_btn.removeClass('hid');
}

function save(n){
	var table=document.getElementById('ptList'),
		row=table.rows[n],
		id_cell=row.cells[1],
		name_cell=row.cells[2],
		desc_cell=row.cells[3],
		name=name_cell.getFirst().value,
		desc=desc_cell.getFirst().value,
		ptid=id_cell.innerHTML,
		edit_btn=row.cells[5].getElementsByClassName('ptm-edit')[0],
		save_btn=row.cells[5].getElementsByClassName('ptm-save')[0],
		delete_btn=row.cells[5].getElementsByClassName('ptm-delete')[0],
		parameters='i=PT&a=SAVEPT';
	
	name_cell.innerHTML='<div>'+name+'</div>';
	desc_cell.innerHTML='<div>'+desc+'</div>';
	edit_btn.removeClass('hid');
	save_btn.addClass('hid');
	
	parameters+='&name='+name+'&desc='+encodeURI(desc)+'&ptid='+ptid;
	
	row.addClass('rowsaving');
	
	new Ajax (url,{
		method:'get',
		data: parameters,
		onComplete: function(response){
			if(response!=0){
				if(!ptid) id_cell.innerHTML=response;
				row.removeClass('rowsaving');
				row.removeClass('editing');
				row.setProperty('data-ptname',name);
				delete_btn.removeClass('hid');
			}else{
				
				alert('There was an error while updating values.');
			}
		}
	}).request();
}

function newpt(){
	var table=document.getElementById('ptList'),
		rowcount, newrow, td1, td2, td3, td4, td5, td6;
	
	if(productTypeTableEmpty){
		table.deleteRow(1);
		productTypeTableEmpty=false;
	}
	
	rowcount=table.rows.length;
	newrow=table.insertRow(rowcount);
	td1=newrow.insertCell(0);
	td2=newrow.insertCell(1);
	td3=newrow.insertCell(2);
	td4=newrow.insertCell(3);
	td5=newrow.insertCell(4);
	td6=newrow.insertCell(5);
	
	td1.innerHTML=rowcount;
	td3.innerHTML='<input type="text" name="name" value="" />';
	td4.innerHTML='<textarea name="desc" rows="1"></textarea>';
	td5.innerHTML='0';
	td6.innerHTML='<button class="prodparam-btn ptm-edit hid" onclick="edit('+rowcount+')">Edit</button><button class="prodparam-btn ptm-save" onclick="save('+rowcount+')">Save</button> &nbsp;<button class="prodparam-btn ptm-delete hid" onclick="deletept(this)">Delete</button>';
	newrow.className='ptm-vrow editing';
	td1.className='ptm-no ptm-listen';
	td1.setProperty("onclick","openpt("+rowcount+")");
	td2.className='ptm-id ptm-listen';
	td2.setProperty("onclick","openpt("+rowcount+")");
	td3.className='ptm-name ptm-listen';
	td3.setProperty("onclick","openpt("+rowcount+")");
	td4.className='ptm-desc';
	td5.align='center';
	td3.getFirst().focus();
}

function deletept(el){
	var table=document.getElementById('ptList'),
		row=el.parentNode.parentNode,
		rowIndex=row.rowIndex;
		ptid=row.cells[1].innerHTML,
		confirmDelete=confirm('Delete product type: "'+row.getAttribute('data-ptname')+'"?'),
		parameters='i=PT&a=REMPT';
	
	if(confirmDelete){
		table.deleteRow(rowIndex);
		parameters+='&ptid='+ptid;
		new Ajax (url,{
			method:'get',
			data: parameters
		}).request();
	}
}

function openpt(n){
	var table=document.getElementById('ptList'),
		row=table.rows[n],
		ptid=row.cells[1].innerHTML,
		parameters='i=PT',
		status=$('cstatus');
	if(row.hasClass('editing')) return;
	window.location.hash+='&ptid='+ptid;
	parameters+='&ptid='+ptid;
	status.removeClass('hid');
	new Ajax (url,{
		method:'get',
		evalScripts:true,
		data: parameters,
		onComplete: function(response){
			status.addClass('hid');
		},
		update:$('clayout')
	}).request();
}

function toggleHelp(){
	var helpContainer=document.getElementsByClassName('ptm-helptips-cont')[0];
	helpContainer.toggleClass('ptm-helptips-cont-show');
}
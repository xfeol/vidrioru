<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsViewPT{
	
	static function printProductTypes($d){
?>
		<div class="ptm-main"><h2>Product Types</h2>
		<table id="ptList" class="ptm-list">
		<tr class="ptm-ttl"><td>No.</td><td>Id</td><td class="ptm-name">Name</td><td class="ptm-desc">Description</td><td>Parameters</td><td>Edit</td></tr>
<?php
		if(!$d){
			echo '<tr><td colspan="6" align="center"><i>You do not have Product Types yet.</i></td></tr>';
		}else{
			foreach($d as $i => $v){
?>
			<tr class="ptm-vrow" data-ptname="<?php echo $v['product_type_name'] ?>"><td class="ptm-no ptm-listen" onclick="openpt('<?php echo ($i+1) ?>')"><?php echo ($i+1) ?></td>
			<td class="ptm-id ptm-listen" onclick="openpt('<?php echo ($i+1) ?>')"><?php echo $v['product_type_id'] ?></td>
			<td class="ptm-name ptm-listen" onclick="openpt('<?php echo ($i+1) ?>')"><div><?php echo $v['product_type_name'] ?></div></td>
			<td class="ptm-desc"><div><?php echo $v['product_type_description'] ?></div></td>
			<td align="center"><?php echo $v['count'] ?></td><td><button class="prodparam-btn ptm-edit" onclick="edit('<?php echo ($i+1) ?>')">Edit</button>
			<button class="prodparam-btn ptm-save hid" onclick="save('<?php echo ($i+1) ?>')">Save</button> &nbsp;
			<button class="prodparam-btn ptm-delete" onclick="deletept(this)">Delete</button></td></tr>
<?php
			}
		}
?>
	</table>
	<div style="margin-top:15px"><button class="prodparam-btn" onclick="newpt()"><img src="<?php echo $GLOBALS['comp_url'] ?>images/plus.png" width="8" height="8" /> New Product Type</button>
	</div>
	</div>
	
	<div class="ptm-helptips-main">
	<div class="ptm-helptips-ttl" onclick="toggleHelp()">Help &amp; Tips</div>
	<div class="ptm-helptips-cont hid1">What is a <b>Product Type</b>? And why do I need them?
	<br/>
	<br/>
	The main idea behind Product Types lies in dividing a variety of products you have on the site into groups with similar characteristics. 
	These "similar characteristics" are called <i>Parameters</i>.
	<br/>
	<br/>
	Let's have an example for best illustration.
	<br/>For example we have a site that sells various products like <b>Books</b>, <b>Movies</b>, <b>Music</b>, <b>Games</b> etc. 
	Now, if we wanted to create filters for them what Parameters should we create to fit all products? Hardly that would be possible. 
	Even though there may be some parameters that will suit all of them, like <i>Genre</i>, <i>Release Date</i>, there are still quite a lot of 
	differences unique to each type of products, like <b>Books</b> would have <i>Author</i>, <i>Number of Pages</i>, <i>Format</i>; 
	<b>Music</b> might have <i>Duration</i>; <b>Games</b> -- <i>Platform</i> and so on.
	That is why it is a very good idea to create for each type of products a Product Type with Parameters that you wish you customers to filter by:
	<table class="ptm-helptips-table">
		<tr><td><b>Product Type</b></td><td><b>Parameters</b></td></tr>
		<tr><td>Books</td><td>Author<br/>Number of Pages<br/>Genre<br/>Format (Hardcover, Paperback ..)<br/>Release date</td></tr>
		<tr><td>Music</td><td>Duration<br/>Genre<br/>Release date<br/></td></tr>
		<tr><td>Games</td><td>Genre<br/>Platform (PC, Mac, Xbox360, PS3, Wii ..)<br/>Release date<br/>Developer</td></tr>
		<tr><td>...</td><td></td></tr>
	</table>
	</div>
	</div>
	
	<script type="text/javascript">
		var productTypeTableEmpty=<?php echo ($d)? 'false' : 'true' ?>;
		<?php //echo file_get_contents($GLOBALS['comp_path'].'js/pt.producttypes.js'); 
		require($GLOBALS['comp_path'].'js/pt.producttypes.js'); 
		?>
	</script>
<?php
	}
	
	static function printPTTitle($name){
		echo '<div class="ptp-ttl">You are editing parameters of <span class="ptp-ttlName">"'.$name.'"</span> product type</div>';
	}
	
	public function printParameters($parameters){
		$filter_column_size=$this->getSizeOfParameterValuesColumn();
		$ptid=JRequest::getVar('ptid', null);
?>
<div style="margin:5px 0">
<button class="prodparam-btn ptp-backBtn" onclick="goBack()">&larr; Back to Product Types list</button>
<button class="prodparam-btn" onclick="saveParameters()" style="margin-left:80px;font:bold 11px Tahoma;"><img src="<?php echo $GLOBALS['comp_url'] ?>images/Save16.png" width="16" height="16" style="vertical-align:middle" /> &nbsp;<span style="vertical-align:middle">Save all changes</span></button>
</div>
<div id="ptpParamCont">
<table class="ptp-tbl" style="background:white;">
<tr>
	<td class="ptp-leftNav"><div style="position:relative;right:-1px"><ul id="ptpNavTabs">
<?php
	$count=count($parameters);
	foreach($parameters as $i => $p){
		$class='';
		if($i==0) $class.=' tab-first tab-selected';
		if($i==($count-1)) $class.=' tab-last';
?>
	<li id="ptpNavTab<?php echo $i ?>" class="ptp-navTab<?php echo $class ?>" data-tab="<?php echo $i ?>" data-order="<?php echo $i ?>" onclick="setActiveTab(this)" onmouseover="tabHoverOnOut(this,1)" onmouseout="tabHoverOnOut(this,0)">
		<span class="ptp-tabParamLabel"><?php echo $p->parameter_label ?></span>
		<span class="ptp-tabParamName">(<?php echo $p->parameter_name ?>)</span>
		<div class="ptp-tabUpBtn" onclick="moveTabUp(this, event)">&uarr;</div>
	</li>
<?php
	}
?>
	</ul></div>
	<div style="margin:20px 0;text-align:center"><span class="ptp-addNewTab" onclick="addParameter()">Add new parameter</span></div>
	</td>
	<td class="ptp-rightData"><form method="post" action="<?php echo $GLOBALS['fajax'] ?>" name="parametersForm">
	<input type="hidden" name="i" value="PT" />
	<input type="hidden" name="a" value="SAVEPARAMS" />
	<input type="hidden" name="ptid" value="<?php echo $ptid ?>" />
	<div id="ptpForms">
<?php
if($count==0){
	echo '<div style="font:italic 13px Arial;text-align:center;color:#888888;margin-top:20px">You do not have parameters yet. Add your first one.</div>';
}else{
	foreach($parameters as $i => $p){
		$class=($i==0)? '' : ' hid';
?>
	<div class="ptp-formCont<?php echo $class ?>" id="ptpForm<?php echo $i?>">
<?php //$this->getParameterForm($p);
	self::printParameterForm($p,$i);
?>
	</div>
<?php
	}
}
//echo '<pre>';
//print_r($parameters);
//echo '</pre>';
?>
	</div></form></td>
</tr>
</table>
</div>
<script type="text/javascript">
var currentTabIndex=0,
	filterColumnSize=<?php echo $filter_column_size ?>,
	//numberOfNewTabs=0,
	generalNumberOfTabs=document.getElementById('ptpNavTabs').getChildren().length;
<?php //echo file_get_contents($GLOBALS['comp_path'].'js/pt.parameters.js');
	require($GLOBALS['comp_path'].'js/pt.parameters.js');
?>
</script>
<?php		
	}
	
	// $key value is important in this form. We bind each set of Parameter data with 
	// unique $key (either parameter_name or newX (for new parameters added in interface))
	// When form is saved, we sort data back by $key.
	static function printParameterForm($data=null, $_tab_number=null){
		$f=($data)? true : false; // data not always passed here. In case we call for a new form, we check $data.
		$tab_number=($_tab_number)? $_tab_number : JRequest::getVar('tabno', 0);
		$key=($f)? $data->parameter_name : 'new'.$tab_number;
		$number_of_filters=0;
		if($f && $data->parameter_values) $number_of_filters=count(explode(';', $data->parameter_values));
?>
<button class="glass-btn" title="Delete Parameter" onclick="deleteParameter(event,<?php echo $tab_number ?>,'<?php echo $key ?>')" style="font:11px Tahoma;float:right"><img src="<?php echo $GLOBALS['comp_url'] ?>images/Delete16.png" width="10" /> &nbsp;Delete</button>
<div class="clear"></div>
<input type="hidden" name="key[]" value="<?php echo $key ?>" />
<input type="hidden" name="parameter_name_active_<?php echo $key ?>" id="parameter_name_active_<?php echo $key ?>" value="<?php if($f && $data->parameter_name) echo $data->parameter_name ?>" />
<input type="hidden" name="list_order_<?php echo $key ?>" id="list_order_<?php echo $tab_number ?>" value="<?php echo $tab_number ?>" />

<table class="ptp-paramTbl">
 <tr><td class="titleCell">Name</td><td class="valueCell">
	<input type="text" name="parameter_name_<?php echo $key ?>" id="parameter_name_<?php echo $key ?>" value="<?php if($f && $data->parameter_name) echo $data->parameter_name ?>" class="paramName" data-tab="<?php echo $tab_number ?>" onfocus="showHideHing(this,1)" onblur="showHideHing(this,0)" onkeyup="setParameterName(this)" />
 </td>
 <td><span id="parameter_name_<?php echo $key ?>_hint" class="simpleHint">Required and should be unique. This parameter name is being used in URL, so name it nicely (e.g.: brand).</span></td></tr>
 
 <tr><td class="titleCell">Label</td><td class="valueCell">
	<input type="text" name="parameter_label_<?php echo $key ?>" id="parameter_label_<?php echo $tab_number ?>" value="<?php if($f && $data->parameter_label) echo $data->parameter_label ?>" class="input" data-tab="<?php echo $tab_number ?>" onfocus="showHideHing(this,1)" onblur="showHideHing(this,0)" onkeyup="setParameterLabel(this)" />
 </td>
 <td><span id="parameter_label_<?php echo $tab_number ?>_hint" class="simpleHint">Parameter label is displayed on site (e.g.: Brand).</span></td></tr>
 
 <tr><td class="titleCell" style="vertical-align:top;padding-top:20px">Defined Filters</td><td colspan="2">
	<textarea name="defined_filters_<?php echo $key ?>" id="defined_filters_<?php echo $tab_number ?>" rows="3" style="width:90%" data-tab="<?php echo $tab_number ?>" onkeyup="calculateNumberOfFilters(this,event); checkFilterColumnSize(this)" onfocus="showHideHing(this,1)" onblur="showHideHing(this,0)"><?php if($f && $data->parameter_values) echo $data->parameter_values ?></textarea>
	<div id="numberOfFilters_<?php echo $tab_number ?>" style="color:#777777;margin:0 0 5px 0;font:11px Tahoma"><span>Number of Filters:</span> <span><?php echo $number_of_filters ?></span></div>
	<div style="margin:0 0 20px 0"><span id="defined_filters_<?php echo $tab_number ?>_hint" class="simpleHint">List all your possible filters here through semicolon. E.g.: White;Black;Blue</span></div>
 </td></tr>
 
 <tr><td class="titleCell">Description</td><td class="valueCell"><textarea name="parameter_description_<?php echo $key ?>" rows="1" style="width:254px;max-width:400px"><?php if($f && $data->parameter_description) echo $data->parameter_description ?></textarea>
 </td>
 <td></td></tr>
 
 <tr><td class="titleCell">Unit</td><td class="valueCell">
	<input type="text" name="parameter_unit_<?php echo $key ?>" id="parameter_unit_<?php echo $tab_number ?>" value="<?php if($f && $data->parameter_unit) echo htmlspecialchars($data->parameter_unit) ?>" class="input" onfocus="showHideHing(this,1)" onblur="showHideHing(this,0)" /></td>
 <td><span id="parameter_unit_<?php echo $tab_number ?>_hint" class="simpleHint">Could be someting like: Gb or Inches or "</span></td></tr>
 
 <tr><td class="titleCell">Mode</td><td class="valueCell"><select name="parameter_mode_<?php echo $key ?>" id="parameter_mode_<?php echo $tab_number ?>" data-tab="<?php echo $tab_number ?>" onchange="validateSelected(this)">
	<option value="0"<?php if ($data->mode==0) echo ' selected="selected"' ?>>Default</option>
	<option value="1"<?php if ($data->mode==1) echo ' selected="selected"' ?>>Trackbar: 1 slider</option>
	<option value="2"<?php if ($data->mode==2) echo ' selected="selected"' ?>>Trackbar: 2 sliders</option>
	<option value="3"<?php if ($data->mode==3) echo ' selected="selected"' ?>>Color</option>
 </select></td>
 <td></td></tr>
 
 <tr><td class="titleCell">Type</td><td class="valueCell"><select name="parameter_type_<?php echo $key ?>" id="parameter_type_<?php echo $tab_number ?>" onfocus="showHideHing(this,1)" onblur="showHideHing(this,0)">
	<option value="S"<?php if ($data->parameter_type=='S') echo ' selected="selected"' ?>>Short Text</option>
	<option value="V"<?php if ($data->parameter_type=='V') echo ' selected="selected"'; if ($data->mode==2) echo " disabled" ?>>Multiple Values</option>
	<option value="I"<?php if ($data->parameter_type=='I') echo ' selected="selected"' ?>>Integer</option>
	<option value="T"<?php if ($data->parameter_type=='T') echo ' selected="selected"' ?>>Text</option>
	<option value="C"<?php if ($data->parameter_type=='C') echo ' selected="selected"' ?>>Char</option>
 </select></td>
 <td><div style="display:inline-block;margin:5px;font:13px Tahoma;vertical-align:middle;"><span id="parameter_type_<?php echo $tab_number ?>_hint" class="simpleHint">
	<i style="background:#B0F1AF">Short Text</i> is recommended as the default Parameter Type, as it gives the best performance.
	<br/> Use <i style="background:#B0F1AF">Multiple Values</i> when you want to assign  more then one value to a single product. E.g.: <i>White,Silver</i> colors at the same time.
	<br/> <b>Note.</b> You can't use <i>Multiple Values</i> and Trackbar with 2 sliders together.</span>
 </div></td></tr>
 
 <tr><td class="titleCell"> </td><td colspan="2"><input type="checkbox" name="parameter_multiselect_<?php echo $key ?>" value="Y" id="id_parameter_multiselect_<?php echo $key ?>" <?php if ($data->parameter_multiselect=='Y') echo 'checked ' ?>/>
	<label for="id_parameter_multiselect_<?php echo $key ?>" style="font:bold 13px Arial;">Allow customers select multiple filters from a single parameter?</label></td>
 </tr>
</table>
<?php
//echo 'this is from'.rand();
//if($data) print_r($data);
	}

}

?>
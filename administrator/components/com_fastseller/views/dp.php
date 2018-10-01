<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsViewDP{
	
	private $pid;
	private $ptid;
	private $curr_row;
		
	public function displaySearch(){
		//global $fajax;
		$q=JRequest::getVar('q','');
		$showonpage=JRequest::getVar('showonpage','');
		$cid=JRequest::getVar('cid','');
		$ptid=JRequest::getVar('ptid','');
		$ppid=JRequest::getVar('ppid','');
		$orderby=JRequest::getVar('orderby','');
		$scending=JRequest::getVar('sc','Asc');	// Ascending, Descending order
		?>
		<div id="csearch">
		<div class="search-logo" onclick="document.searchForm.q.focus();">Search</div>
		<form name="searchForm" action="index.php" method="get" onsubmit="doSearch(this.q.value);return false;" style="display:inline-block;">
			<input type="hidden" name="option" value="com_fastseller" />
			<input type="hidden" name="showonpage" value="<?php if($showonpage)echo $showonpage;?>" />
			<input type="hidden" name="cid" value="<?php if($cid)echo $cid;?>" />
			<input type="hidden" name="ptid" value="<?php if($ptid)echo $ptid;?>" />
			<input type="hidden" name="orderby" value="<?php if($orderby)echo $orderby;?>" />
			<input type="hidden" name="sc" value="<?php echo $scending; ?>" />
			<input type="hidden" name="ppid" value="<?php echo $ppid; ?>" />
			<input type="hidden" name="skip" value="" />
			<input type="hidden" name="old_skip" value="" />
			
			<div style="width:550px;">
			<table cellspacing="0" cellpadding="0" border="0"><tr>
			<td width="100%" class="fsSearch-td" style="border-left:2px solid #AAAAAA;">
				<input class="fsSearch" type="text" name="q" value="<?php echo $q ?>" autocomplete="off" />
			</td><td class="fsSearch-td" style="padding:0 5px 0 10px;">
				<span id="search-xbtn" style="display:<?php echo ($q)? 'inline' : 'none';?>;" onclick="doSearch('');return false;">Remove</span></td>
			<td><button type="submit" class="search-btn"><img src="<?php echo $GLOBALS['comp_url'] ?>images/search.png" width="25" height="20" /></button></td>
			</tr></table>
			<div id="productsfound">Matches ..</div>
			</div>
		</form>
		<script type="text/javascript">
		<?php //echo file_get_contents($GLOBALS['comp_path'].'js/dp.head.js');
			require($GLOBALS['comp_path'].'js/dp.head.js');
		?>
		</script>
		</div>
		<?php 
	}

	public function displayRefinePane($cname,$ptname){
		if($cname){
			$cnamea=$cnameb=$cname;
			$len=strlen($cname);
			if($len>21)$cnamea=substr($cname,0,21).'...';
			if($len>18)$cnameb=substr($cname,0,18).'...';
		}
		if($ptname){
			$ptnamea=$ptnameb=$ptname;
			$len=strlen($ptname);
			if($len>21)$ptnamea=substr($ptname,0,21).'...';
			if($len>18)$ptnameb=substr($ptname,0,18).'...';
		}
		$orderby=JRequest::getVar('orderby','cat');
		$scending=JRequest::getVar('sc','Asc');
		?>
		<div id="notice-msg" class="hid"></div>
	<div id="refine-pane">
		
				
		<button type="button" class="ui-tabletop-btn mass-select-btn" data="select-groups-menu" onclick="simplePopup.showmenu(this,5,event)">
			<div class="ui-select-groups-img" id="selGrpImg" style="vertical-align:top;"> </div>
			<div  class="down-arrow" style="margin-top:4px"> </div>
		</button>
		<div id="select-groups-menu" class="popmenu2 hid" style="padding:3px 0;">
			<div class="tabletopmenu-el" onclick="selectAll()">All</div>
			<div class="tabletopmenu-el" onclick="selectNone()">None</div>
			<div class="tabletopmenu-el" onclick="selectWPT()">w/ PT</div>
			<div class="tabletopmenu-el" onclick="selectWOPT()">w/o PT</div>
		</div>
		
		<button class="ui-tabletop-btn cat-refine-btn" style="margin-left:20px;" onclick="simplePopup.showmenu(this,5,event);getctree(0,'cbranch');" type="button" data="showcat-menu">
			<span style="line-height:16px;"><?php echo ($cname)?$cnameb:'Category';?></span>
			<div class="down-arrow"> </div>
		</button>
		<div class="popmenu hid" id="showcat-menu">
			<div id="cat-mhead"><span><?php echo ($cname)?$cnamea:'Category';?></span><span class="remove<?php echo ($cname)?'':' hid';?>" onclick="setcat('','Category');"> -<span style="border-bottom:1px dashed #444444;">remove</span></span></div>
			<div id="cbranch"><div id="cbranch-loader" class="hid" style="padding:15px 0;" align="center"><img src="<?php echo $GLOBALS['comp_url'] ?>images/ajax-loader.gif" width="16" height="11" /></div></div>
			<div width="100%" style="border-top:1px solid #BBBBBB;margin:3px 5px 8px;padding:3px 5px 0;">
				<span class="help" onclick="$('cat-help-content').toggleClass('hid');">Help and Tips</span>
				<div id="cat-help-content" class="hid" style="padding-top:5px;">&rsaquo; Click <b>Go</b> to choose a category.<br/>&rsaquo; Click on a Category name to expand it's children.<br/>&rsaquo; <span class="grayed">Unpublished</span> categories marked in light gray color.</div>
			</div>	
		</div>
		
		<button class="ui-tabletop-btn pt-refine-btn" onclick="simplePopup.showmenu(this,5,event);getpttree();" type="button" data="showpt-menu">
			<span style="line-height:16px;"><?php echo ($ptname)?$ptnameb:'Product Type';?></span>
			<div class="down-arrow"> </div>
		</button>
		<div class="popmenu hid" id="showpt-menu">
			<div id="pt-mhead"><span><?php echo ($ptname)?$ptnamea:'Product Type';?></span><span class="remove<?php echo ($ptname)?'':' hid';?>" onclick="setpt('','Product Type');"> -<span style="border-bottom:1px dashed #444444;">remove</span></span></div>
			<div id="ptbranch"><div style="padding:15px 0;" align="center"><img src="<?php echo $GLOBALS['comp_url'] ?>images/ajax-loader.gif" width="16" height="11" /></div></div>
				
		</div>
		
		
		<button style="margin:0 0 0 20px;" type="button" class="ui-tabletop-btn orderby-btn" data="orderby-menu" onclick="simplePopup.showmenu(this,5,event)">Order By<div class="down-arrow-sm"> </div></button>
		<div id="orderby-menu" class="popmenu2 hid" style="padding:3px 0;">
			<div class="tabletopmenu-el" order="cat" onclick="orderBy(this)">Category<?php if($orderby=='cat')echo '*' ?></div>
			<div class="tabletopmenu-el" order="pname" onclick="orderBy(this)">Product name<?php if($orderby=='pname')echo '*' ?></div>
			<div class="tabletopmenu-el" order="pid" onclick="orderBy(this)">Product ID<?php if($orderby=='pid')echo '*' ?></div>
			<div class="tabletopmenu-el" order="ptid" onclick="orderBy(this)">Product Type ID<?php if($orderby=='ptid')echo '*' ?></div>
		</div>
					
		<button type="button" class="ui-tabletop-btn orderby-sc-btn" onclick="orderAscDesc(this)" title="Current ordering: <?php echo $scending ?>">
			<?php echo $scending ?>
		</button>
		
		<button type="button" class="ui-tabletop-btn collapse-all-btn" style="margin-right:30px;" onclick="collapseAll();" title="Collapse all Rows"> - </button>
		<button type="button" class="ui-tabletop-btn expand-all-btn" style="margin-right:1px;" onclick="expandAll();" title="Expand all Rows"> + </button>
		<br clear="all" />
	</div>
	<script type="text/javascript" charset="utf-8">
<?php 
	if(fsconf::getOption('filter_dialog')==1){
		//echo file_get_contents($GLOBALS['comp_path'].'js/dp.popdialog.js');
		require($GLOBALS['comp_path'].'js/dp.popdialog.js');
	}
?>
	</script>
<?php
	}
	
	public function displayCatTree($cat){
		if(empty($cat))return;
		$ctreeid=JRequest::getVar('ctreeid','');
		$cat_count=count($cat);?>
		
		<table cellspacing="0" cellpadding="0" border="0" width="100%" <?php if($ctreeid=='cbranch')echo'style="padding-left:10px"'; ?>>
		<?php
		for($i=0; $i<$cat_count; $i++){
			$id=$ctreeid.'-'.$i; ?>
			<tr><td onclick="getctree(<?php echo $cat[$i]->id.',\''.$id.'\''; ?>);" onmouseover="ctreehighlight(this,1);" onmouseout="ctreehighlight(this,1);" class="cat-td">
			
			<div class="cat-content<?php echo ($cat[$i]->category_publish=='N')?' grayed':'';?>">
				<?php echo $cat[$i]->name; ?>
			</div>
			<div id="<?php echo $id.'-loader';?>" class="cat-loader hid"><img src="<?php echo $GLOBALS['comp_url'] ?>images/ajax-loader.gif" width="16" height="11" /></div>
			
			</td>
			
			<td width="50" align="right" valign="top" class="white" onmouseover="ctreehighlight(this,2);" onmouseout="ctreehighlight(this,2);">
				<div class="hid">
				<button onclick="setcat(<?php echo $cat[$i]->id.',\''.$cat[$i]->name.'\''; ?>);" class="cat-pick" type="button">Go</button>
				</div>
			</td>
			</tr>
			<tr><td colspan="3" id="<?php echo $id; ?>"></td></tr>
		<?php }
		echo '</table>';
	}
	
	public function displayPTTreeMenu($pt){
		//if(empty($pt)) return;
		?>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" style="padding:0 0 3px 3px;">
		<tr><td onmouseover="pttreehighlight(this,1);" onmouseout="pttreehighlight(this,1);" class="pt-td">
			<div class="pt-content" style="color:#887A37;">w/o Product Type</div></td>
			<td width="50" align="right" valign="top" class="white" onmouseover="pttreehighlight(this,2);" onmouseout="pttreehighlight(this,2);">
				<div class="hid">
				<button onclick="setpt('wopt','w/o Product Type');" class="cat-pick" type="button">Go</button>
				</div>
			</td>
		</tr>
		<?php
		for ($i=0, $j=count($pt); $i<$j; $i++){ ?>
			<tr>
				<td onmouseover="pttreehighlight(this,1);" onmouseout="pttreehighlight(this,1);" class="pt-td" title="id: <?php echo $pt[$i]->id ?>">
					<div class="pt-content<?php echo ($pt[$i]->pub=='N')?' grayed':'';?>">
						<?php echo $pt[$i]->name?>
					</div></td>
				<td width="50" align="right" valign="top" class="white" onmouseover="pttreehighlight(this,2);" onmouseout="pttreehighlight(this,2);">
					<div class="hid">
					<button onclick="setpt(<?php echo $pt[$i]->id.',\''.$pt[$i]->name.'\''; ?>);" class="cat-pick" type="button">Go</button>
					</div>
				</td>
			</tr>
		<?php	
		}
	}
	
	public function displayProducts($rows){
		$count_rows=count($rows);
		$q=JRequest::getVar('q','');
		$skip=JRequest::getVar('skip',0);
		$showonpage=JRequest::getVar('showonpage',fsconf::getOption('default_numrows'));
		$urlptid=JRequest::getVar('ptid','');
		$ppid=JRequest::getVar('ppid',null);
		$orderby=JRequest::getVar('orderby','cat');
		
		$producttypescache=null;
		//$print_product_type_select_menu=false;
		
		//$scending=JRequest::getVar('sc','asc');
		//echo '<pre>';
		//print_r($rows);
		//echo '</pre>';
		//echo $rows;
		
?>
		<div>
		<table cellspacing="0" cellpadding="0" width="100%" id="fsProductListTable">
		<?php
		$jsParamInfo="var paramInfo=[];";
		$paramcache=array();
		if($orderby=='cat') $current_category=-1;
		for($i=0; $i<$count_rows; $i++) {
			if($orderby=='cat' && !$ppid && $current_category!=$rows[$i]->category_id){
				$current_category=$rows[$i]->category_id;
				echo '<tr><td colspan="3" class="fs-cell-catdelim">';
				echo ($rows[$i]->category_name)? $rows[$i]->category_name : 'Without category';
				echo '</td><td class="fs-cell-catdelim2" style=""> </td></tr>';
			}
			$currow='r-'.$i;
			$this->pid=$rows[$i]->product_id;
			$this->ptid=$rows[$i]->pti;
			$this->curr_row='r-'.$i;
			$ptid=$rows[$i]->pti;
			?>
			<tr id="<?php echo $currow ?>-tr" class="fs-row" data-row="<?php echo $currow ?>" data-ptid="<?php echo ($ptid)?$ptid:'none' ?>">
				<td class="fs-cell-tick pl-td-gray" align="center" valign="top" onclick="toggleRowSelected(this)" data-row="<?php echo $currow ?>">
					<div style="color:#AAAAAA;font-size:11px;"><?php echo ($skip+$i+1)."." ?></div>
					<div class="element-avail-img" style="margin:5px 0 0 9px;width:13px;height:13px"> </div>
				</td>
				<td class="fs-cell-name pl-td-gray" valign="top" style="" onclick="callRowExpander('<?php echo $currow ?>');drawHoverBox('<?php echo $currow ?>',110);" data-row="<?php echo $currow ?>">
				<div class="ui-namecell-pid"><?php echo 'id: '.$rows[$i]->product_id ?></div>
				<?php //if($ptid): ?>
				<div class="ui-namecell-ptid<?php if(!$ptid)echo ' hid' ?>"><?php echo 'ptid: '.$ptid ?></div>
				<?php //endif ?>
				<?php if($rows[$i]->product_publish=='N') echo '<div class="ui-namecell-unpublish">unpublished</div>'; ?>
				<?php if(fsconf::getOption('show_pdesc_button')==1){ ?>
				<button type="button" id="<?php echo $currow ?>-pdesc" class="ui-namecell-pdesc" onclick="showProductDescription(event,<?php echo $rows[$i]->product_id ?>)">i</button>
				<?php } ?>
				<?php 
				if($ppid){
					if($ppid==$rows[$i]->product_id) echo '<div class="ui-namecell-child" data="'.$rows[$i]->product_id.'" data-skip="'.$skip.'">Parent</div>';
				}
				else if($this->product_has_children($rows[$i]->product_id)) echo '<div class="ui-namecell-child" data="'.$rows[$i]->product_id.'" data-skip="'.$skip.'">Parent</div>';
				?>
				<br clear="all" />
				
				<div class="ui-namecell-name<?php if($rows[$i]->product_publish=='N') echo ' grayed' ?>">
<?php
				if ($q) {
					$remove = array("(", ")");
					$q = str_replace($remove, "", $q);
				}				
				if ($q) {
					//$remove=array("(",")");
					//$q=str_replace($remove, "", $q);
					echo preg_replace("/($q)/i", '<b>$1</b>', $rows[$i]->product_name);
				} else {
					echo $rows[$i]->product_name;
				}
				// show SKU
				if (fsconf::getOption('show_sku')) {
					echo '<div class="ui-namecell-sku">';
					if ($q) {
						echo preg_replace("/($q)/i", '<b>$1</b>', $rows[$i]->product_sku);
					} else {
						echo $rows[$i]->product_sku;
					}
					echo '</div>';
				}
?>
				</div>
				</td>
				<td id="<?php echo $currow ?>-td" valign="top" class="fs-cell-params pl-td-gray">	
				<form name="<?php echo $currow ?>-form" action="<?php echo $GLOBALS["fajax"] ?>">
				<input type="hidden" name="pid" value="<?php echo $rows[$i]->product_id ?>" />
				<input type="hidden" name="thisptid" value="<?php echo $ptid ?>" />
				<input type="hidden" name="row" value="<?php echo $currow ?>" />
				<input type="hidden" name="ptid" value="<?php echo $urlptid ?>" />
				<input type="hidden" name="a" value="SAVEP" />
				<input type="hidden" name="i" value="DP" />
				<input type="hidden" name="adding" value="<?php echo ($ptid)?'parameters' : 'pt' ?>" />
				<div id="<?php echo $currow ?>-dynamic-container">
				<?php
				if($ptid){
					if(isset($paramcache[$ptid])){$ptparams=$paramcache[$ptid];}
					else{
						$ptparams=$this->getPTParameters($ptid);
						$paramcache[$ptid]=$ptparams;
						
						// Collect Parameter Info for javascript--to use in showing hints
						if(fsconf::getOption('show_parambtn_hint')=='Y'){
							$jsParamInfo.="paramInfo[$ptid]=[];";
							foreach($ptparams as $p){
								$jsParamInfo.="paramInfo[$ptid][\"{$p->parameter_name}\"]=[];";
								$jsParamInfo.="paramInfo[$ptid][\"{$p->parameter_name}\"][\"label\"]='". addslashes($p->parameter_label) ."';";
								$jsParamInfo.="paramInfo[$ptid][\"{$p->parameter_name}\"][\"type\"]='{$p->parameter_type}';";
								$jsParamInfo.="paramInfo[$ptid][\"{$p->parameter_name}\"][\"multiselect\"]='{$p->parameter_multiselect}';";
								$jsParamInfo.="paramInfo[$ptid][\"{$p->parameter_name}\"][\"unit\"]='{$p->parameter_unit}';";
							}
							//echo '<pre>';
							//print_r($ptparams);
							//echo '</pre>';
						}
					}
					self::printRemovePTDialog();
					self::displayProductParameters($rows[$i]->product_id,$ptid,$ptparams,$currow);
					//$this->getProductParameters($rows[$i]->product_id,$ptid,$ptparams,$currow);
				}else{
				//	if(fsconf::getOption('filter_dialog')==1){$producttypescache=null;}
				//	else if(!isset($producttypescache)){
				//		$producttypescache=$this->getPTTree();
						//self::printProductTypes($producttypescache,$currow);
				//	}
				
					//if(!$print_product_type_select_menu && fsconf::getOption('filter_dialog')==0) $print_product_type_select_menu=true;
					
					if(fsconf::getOption('filter_dialog')==0 && !isset($producttypescache)) $producttypescache=$this->getPTTree();
					self::printProductTypes($producttypescache,$currow);
				}
				?>
				</div>
				</form>
				</td><td width="110" valign="top" align="center" class="fs-cell-save" style="border-bottom:1px solid #315584;">
				<div style="padding:10px 0 0 0;"><button type="button" id="<?php echo $currow ?>-savebtn" class="ui-save-button" data-row="<?php echo $currow ?>">Save</button></div>
				<div style="padding:10px 0 3px 0;"><span id="<?php echo $currow ?>-expander" class="ui-savecell-expander<?php if(!$ptid)echo ' invisible' ?>" onclick="callRowExpander('<?php echo $currow ?>');drawHoverBox('<?php echo $currow ?>',0);" onmouseover="drawHoverBox('<?php echo $currow ?>',0);" onmouseout="removeHoverBox();">Expand</span></div>
				</td>
				</tr>
		<?php }
		
		if( $count_rows == 0 ) {
			echo '<div style="margin:10px 0;text-align:center;font:italic 14px Tahoma, Arial;">No products</div>';
		}
		
		//print_r($paramcache);
		?>
		</table>
		<div id="blanket" class="blanket hid"></div>
		</div>
		<div id="pl-hover-box" class="ui-hover-box" style="display:none;"> </div>
		<?php if(fsconf::getOption('show_parambtn_hint')=='Y'){ ?>
		<div id="parambtn-hint" class="hid">
			<div class="hintballoon-inner"> </div>
			<div class="hintballoon-arrout"> </div>
			<div class="hintballoon-arrinn"> </div>
		</div>
		<?php } ?>
		<?php if(fsconf::getOption('show_pdesc_button')==1){ ?>
		<div id="product-description" class="popwindow hid">
			<div id="pdesc-draghandle">Product Details</div>
			<div id="product-description-inner"><div class="pdesc-preload">
				<img src="<?php echo $GLOBALS['comp_url'] ?>images/desc-loader.gif" width="28" height="28" border="0" /></div></div>
			<div style="background-color:#F7F7F7;border-top:1px solid #5EAD2E;text-align:right;">
				<button type="button" class="close-btn" onclick="windowClose('product-description');">Close</button>
				<div id="pdesc-resizehandle"></div>
			</div>
		</div>
		<?php } ?>
		<?php if(fsconf::getOption('filter_dialog')==1){ ?>
		<div id="filter-dialog" class="popwindow hid" style="left:50%;">
			<div id="filter-dialog-title">Assign Filters</div>
			<div id="filter-dialog-body"><div class="pf-cont">Loading..</div></div>
			<div style="background-color:#F7F7F7;border-top:1px solid #B5B5B5;text-align:right;">
				<button type="button" class="close-btn" onclick="closeFilterDialog('filter-dialog');">Close</button>
				<div id="fd-resizehandle"></div>
			</div>
			<div id="blanket2" class="blanket hid"> </div>
		</div>
		<?php } ?>
		<?php //echo 'paramInfo: '.$jsParamInfo; ?>
		<script type="text/javascript">
		<?php if(fsconf::getOption('show_parambtn_hint')=='Y') echo $jsParamInfo; ?>
		var highlightedProduct=null,
			ptCache=null,
			totalRows=$$('.fs-row').length,
			currentTotalRows=totalRows, // rows can be deleted, so we must keep track on them
			rowsSelected=0,
			timeout,
			parambtn_hint_delay=<?php echo (int)fsconf::getOption('parambtn_hint_delay') ?>,
			show_pdesc_button=<?php echo fsconf::getOption('show_pdesc_button') ?>,
			parambtn_hint_transition='<?php echo fsconf::getOption('parambtn_hint_transition') ?>',
			filter_dialog=<?php echo fsconf::getOption('filter_dialog') ?>;
		
<?php	//echo file_get_contents($GLOBALS['comp_path'].'js/dp.middle.js');
		//if(fsconf::getOption('show_parambtn_hint')=='Y') echo file_get_contents($GLOBALS['comp_path'].'js/dp.paramhint.js');
		require($GLOBALS['comp_path'].'js/dp.middle.js');
		if(fsconf::getOption('show_parambtn_hint')=='Y') require($GLOBALS['comp_path'].'js/dp.paramhint.js');
?>
		</script>
		<?php
	}
	
	// Build the list of available Product Types for assigning one to product
	// @ pt: object list of product types
	// @ row: current row in table
	public function printProductTypes($pt=null, $row){
		?>
		<div style="width:70%;text-align:center;margin:15px 0 15px;">
			<button id="<?php echo $row ?>-ptbtn" class="prodparam-btn" 
			<?php if(fsconf::getOption('filter_dialog')==0){
				echo 'onclick="if(!$(\''.$row.'-tr\').hasClass(\'rowsaving\')) simplePopup.showmenu(this,4,event);" data="prodtype-menu-'.$row.'"';
			}else{
				echo 'onclick="if(!$(\''.$row.'-tr\').hasClass(\'rowsaving\')) ptSelectDialog(this);" data="filter-dialog"';
			}
			echo 'data-row="'.$row.'" type="button">';
			?>
			<span class="grayed">[Select Product Type]</span>
			</button>
		</div>
		<div id="prodtype-menu-<?php echo $row ?>" class="popmenu ptselect-menu hid">
		<?php
		if($pt){
			foreach($pt as $p){
				echo '<div><a href="#pt-select" class="prodtype-menu-cell" onclick="prodTypeMenuClick(this);return false;" data-ptid="'.$p->id.'">'.$p->name.'</a></div>';
			}
		}else{
			echo '<div style="padding:10px 20px;color:#AAAAAA;">There no Product Types to select from.</div>';
		}
		?>
		</div>
		<?php	
	}
	
	public function printPTSelectDialog($pt) {
?>
		<div class="pf-cont"><div style="margin:10px 20px 20px 5px;">
		<div class="pf-title">Select Product Type</div>
		<?php
		if($pt){
			foreach($pt as $p){
				echo '<div><a href="#pt-select" class="prodtype-menu-cell" onclick="prodTypeMenuClick(this);return false;" data-ptid="'.$p->id.'">'.$p->name.'</a></div>';
			}
		}else{
			echo 'No product Types';
		}
		echo '</div></div>';
	}
	
	// to do: use button's data-btnvalue to store param label. Hover should use data-paramname to get Values from form.
	public function displayProductParameters($pid,$ptid,$ptparams,$row){
		global $vmpref;
		$db=& JFactory::getDBO();
		$params_count=count($ptparams);
		
		//$urlptid=JRequest::getVar('ptid','');
		$q="SELECT * FROM `#__".$vmpref."_product_type_$ptid` WHERE `product_id`=$pid";
		$db->setQuery($q);
		$res=$db->loadAssoc();

		echo '<div class="fs-cell-params-inner"><div id="'.$row.'-content" class="collapsed" style="">';
		
		if($params_count) {
			for($i=0; $i<$params_count; $i++){
				if($res){$productsValues=$res[$ptparams[$i]->parameter_name];}
				else{$productsValues='';}
				echo '<input type="hidden" name="'.$ptparams[$i]->parameter_name.'" value="'.$productsValues.'" />';
				
				$alignment_style = (fsconf::getOption('param_align')) ? 'width:143px;' : 'max-width:150px';
				
				echo '<div style="float:left;margin:4px 6px 8px 6px;'. $alignment_style .'"><button id="'.$row.'-'.$ptparams[$i]->parameter_name.'-parambtn" ';
				if(fsconf::getOption('filter_dialog')==0){
					echo 'onclick="if(!$(\''.$row.'-tr\').hasClass(\'rowsaving\')) simplePopup.showmenu(this,3,event);" data="'.$row.'-param-menu-'.$ptparams[$i]->parameter_name.'"';
				}else{
					echo 'onclick="if(!$(\''.$row.'-tr\').hasClass(\'rowsaving\')) filterSelectDialog(this);" data="filter-dialog"';
				}
				echo ' class="prodparam-btn" type="button" data-row="'.$row.'" data-paramname="'.$ptparams[$i]->parameter_name.'" data-multi="';
				echo ($ptparams[$i]->parameter_type=="V")? '1"':'"';
				if($productsValues){
					echo ' data-btnvalue="'.$productsValues.'">';
					//echo '" title="'.$ptparams[$i]->parameter_label.': '.$productsValues.'">';
					$qty=count(explode(';',$productsValues));
					if($qty>1) echo '<span class="prodparam-btn-qty">'.$qty.'</span> ';
					echo self::squeeze($productsValues).'</button></div>';
				}else{
					echo ' data-btnvalue=""><span class="ui-parameter-value-empty">['.self::squeeze($ptparams[$i]->parameter_label).']</span></button></div>';
				}
			
				if(fsconf::getOption('filter_dialog')==0){
					echo '<div id="'.$row.'-param-menu-'.$ptparams[$i]->parameter_name.'" class="popmenu hid">';
					//print_r($ptparams[$i]->parameter_values);
					//echo '<table id="'.$row.'-'.$ptparams[$i]->parameter_name.'-tbl" cellpadding="0" cellspacing="0" style="margin:0;border-collapse:collapse;"><tr>';
					
					if($ptparams[$i]->parameter_values) {
						echo '<table cellpadding="0" cellspacing="0" style="margin:0;border-collapse:collapse;"><tr>';
						$values=explode(';', $ptparams[$i]->parameter_values);
						
						// if parameter mode is Color and if you specify color:CSS names through :, like Noir:black
						// if ($ptparams[$i]->mode == PARAM_IS_COLOR) {
							// $color_values = $values;
							// $values = array();
							// foreach ($color_values as $cv) {
								// $color_and_css = explode(":", $cv);
								// $values[] = $color_and_css[0];
							// }
						// }
						
						$newrow=false;
						foreach($values as $k => $value){
							$imgclass='element-avail-img';$valueclass='';
							if($productsValues){
								$found=strpos(";".$productsValues.";",";".$value.";");
								if(!($found===false)){$imgclass='element-sel-img';$valueclass='selected';}
							}
							
							if(($k % fsconf::getOption('filters_per_row'))==0 && $k) {echo '</tr><tr>'; $newrow=true; }
							
							//echo '<td style="border:1px solid #DDDDDD;border-left-width:1px;">
							//<div class="param-menu-cell" onmouseover="paramMenuCellover(this)" onmouseout="paramMenuCellout(this)" onclick="paramMenuCellclick(this)"><div class="'.$imgclass.'"> </div>
							//<span class="'.$valueclass.'">'.$value.'</span>
							//</div></td>';
							echo '<td style="border:1px solid #DDDDDD;border-left-width:1px;">
							<a href="#toggle-parameter-value" class="param-menu-cell" onclick="paramMenuCellclick(this);return false;">
							<span class="'.$imgclass.'"> </span>
							<span class="'.$valueclass.'">'.$value.'</span>
							</a>';
							echo '</td>';
						}
						if($newrow) { for($l=0,$m=(fsconf::getOption('filters_per_row')-$k%fsconf::getOption('filters_per_row')-1);$l<$m; ++$l){echo '<td></td>';} }
						//if(($k % 3)==0)echo '<td> </td>';
						echo '</tr></table>';
					} else {
						echo '<div style="padding:10px 20px;color:#999999">Please define filters for this Parameter first</div>';
					}
					echo '</div>';
				}
			}
		} else {
			echo '<div style="margin-left:200px;color:#999999">There are no Parameters created yet.</div>';
		}
		
		echo '<br clear="all" />
		</div></div>';
	}
	
	public function printFilterDialog($name, $data) {
		//print_r($data);
		$paramname = JRequest::getVar('paramname', null);
		
		$html = '<div class="pf-cont">
		<div style="margin:6px 5px 5px 6px">
		<span>'.$name.':</span> <div style="display:inline-block;"><span style="color:#3E55A9">'.
		$data->parameter_label.'</span> (<span style="color:#BF4545">'.$paramname.'</span>)</div>
		</div>';
			
		$html .= '<table cellpadding="0" cellspacing="0" style="margin:5px 2px 15px 0;border-collapse:collapse;"><tr>';

		if( $data->parameter_values ) {
			$productsValues=JRequest::getVar('f',null);
			$values=explode(';',$data->parameter_values);
			$newrow=false;
			foreach($values as $k => $value){
				$imgclass='element-avail-img';$valueclass='';
				if($productsValues){
					$found=strpos(";".$productsValues.";",";".$value.";");
					if(!($found===false)){$imgclass='element-sel-img';$valueclass='selected';}
				}
	
				if(($k % fsconf::getOption('filters_per_row'))==0 && $k) {
					$html .= '</tr><tr>';
					$newrow=true;
				}
					
				
				$html .= '<td style="border:1px solid #DDDDDD;border-left-width:1px;">
				<a href="#toggle-parameter-value" class="param-menu-cell small" onclick="paramMenuCellclick(this);return false;">
				<span class="'.$imgclass.'" style="margin-top:0;"> </span>
				<span class="'.$valueclass.'">'.$value.'</span>
				</a></td>';
			}
			if($newrow) { 
				for($l=0,$m=(fsconf::getOption('filters_per_row')-$k%fsconf::getOption('filters_per_row')-1);$l<$m; ++$l){ 
					$html .= '<td></td>';
				} 
			}
		} else {
			$html .= '<div style="padding:10px 20px;color:#999999">Please define filters for this Parameter first</div>';
		}
		$html .= '</tr></table></div>';
		
		echo $html;
	}
	
	public function printRemovePTDialog(){
		$urlptid=JRequest::getVar('ptid','');
		$row = (isset($this->curr_row)) ? $this->curr_row : JRequest::getVar('row',null);
		$pid = (isset($this->pid)) ? $this->pid : JRequest::getVar('pid',null);
		$ptid = (isset($this->ptid)) ? $this->ptid : JRequest::getVar('ptid',null);
		if(!$ptid) $ptid=JRequest::getVar('thisptid',null);
		?>
		<div id="<?php echo $row ?>-rmpt" class="invisible" style="float:right;margin-right:10px;font-family:Tahoma;font-size:11px;overflow:hidden;white-space:nowrap;padding:1px 0;">
			<div style="float:left;">
			<a href="#rm-pt-info-dialog" class="ui-remove-pt-info" onclick="rmPTInfoDialog('<?php echo $row ?>');return false;" title="Remove Product Type information from this Product">Remove Product Type Information</a>
			</div>
			<div style="float:right;margin:0 -120px 0 5px;">
			<span style="color:#555555;">Confirm:</span>
			<a href="#i=DP&a=RMPT&pid=<?php echo $pid ?>&ptid=<?php echo $ptid ?>&urlptid=<?php echo $urlptid ?>&row=<?php echo $row ?>" class="ui-confirm-yes" onclick="rmPTInfoRemove(this,'<?php echo $row ?>');return false;" title="Yes, remove it!">Yes</a> <a href="#cancel" class="ui-confirm-no" onclick="rmPTInfoCancel('<?php echo $row ?>');return false;" title="No, I don't want to remove Product Type Info">No</a>
			</div>
		</div>
		<br clear="all" />
		<?php
	}
		
	public function printPageNavigation($count){
		$keyword=JRequest::getVar('q','');
		//$page=intval(JRequest::getCmd('page',1));
		$showonpage = intval(JRequest::getCmd('showonpage',fsconf::getOption('default_numrows')));
		$session_onpage = $_COOKIE['onpage'];
		if ($session_onpage) $showonpage = $session_onpage;
		if ($showonpage == 0) $showonpage=1;
		
		$skip=intval(JRequest::getCmd('skip',0));
		$cid=JRequest::getVar('cid','');
		$ptid=JRequest::getVar('ptid','');
		$orderby=JRequest::getVar('orderby','');
		$scending=JRequest::getVar('sc','');
		$ppid=JRequest::getVar('ppid',null);
				
		//echo 'count:'.$count;
		$page=$skip/$showonpage + 1;
		$pagesNumber= ($count==0) ? 1 : ceil($count/$showonpage);
		//if($page>$pagesNumber)$page=$pagesNumber;
		
		$url='i='.JRequest::getCmd('i', '');
		if($keyword)$url.='&q='.urlencode($keyword);
		if($cid)$url.='&cid='.$cid;
		if($ptid)$url.='&ptid='.$ptid;
		if($orderby)$url.='&orderby='.$orderby;
		if($scending)$url.='&sc='.$scending;
		if($ppid)$url.='&ppid='.$ppid;
		if($showonpage)$urla=$url.'&showonpage='.$showonpage;
		
		if($pagesNumber>11){
			$startdelta=$page-4;
			if($startdelta>1){
				$start=$startdelta;
				$startdelta=0;
			}else{
				$start=2;
				if($startdelta!=1){$startdelta=abs($startdelta)+2;}
			}
			$enddelta=$pagesNumber-($page+4);
			if($enddelta>0){
				$end=$page+4;
				$enddelta=0;
			}else{
				$end=$pagesNumber-1;
				$enddelta=abs($enddelta)+1;
			}
			
			//echo 'start:'.$start;
			//echo 'end:'.$end;
			if($startdelta && !$enddelta){
				$end=($end+$startdelta<$pagesNumber) ? $end+$startdelta : $pagesNumber-1;
			}else if(!$startdelta && $enddelta){
				$start=($start-$enddelta<2) ? 2 : $start-$enddelta;
			}
			//echo 'start2:'.$start;
			//echo 'end2:'.$end;
			
			$first=($page>6) ? 'First' : '1';
			echo '<button class="pager-';
			echo ($page==1) ? 'selected' : 'available';
			//echo '" href="'.$urla.'&page=1" onclick="setpage(this,1);return false;" type="button"><span>'.$first.'</span></button>';
			echo '" href="'.$urla.'&skip=0" onclick="setpage(this,0);return false;" type="button"><span>'.$first.'</span></button>';
			
			for($i=$start;$i<=$end;$i++){
				$sk=($i-1)*$showonpage;
				echo '<button class="pager-';
				echo ($page==$i) ? 'selected' : 'available';
				echo '" href="'.$urla.'&skip='.$sk.'" onclick="setpage(this,'.$sk.');return false;" type="button"><span>'.$i.'</span></button>';
			}
			
			$last=($pagesNumber-5>$page) ? 'Last ('.$pagesNumber.')' : $pagesNumber;
			$sk=($pagesNumber-1)*$showonpage;
			echo '<button class="pager-';
			echo ($page==$pagesNumber) ? 'selected' : 'available';
			echo '" href="'.$urla.'&skip='.$sk.'" onclick="setpage(this,'.$sk.');return false;" type="button"><span>'.$last.'</span></button>';
		}else{
			for($i=1;$i<=$pagesNumber;$i++){
				$sk=($i-1)*$showonpage;
				echo '<button class="pager-';
				echo ($i==$page) ? 'selected' : 'available';
				//echo '" href="'.$urla.'&page='.$i.'&skip='.$sk.'" onclick="setpage(this,'.$i.');return false;" type="button"><span>'.$i.'</span></button>';
				echo '" href="'.$urla.'&skip='.$sk.'" onclick="setpage(this,'.$sk.');return false;" type="button"><span>'.$i.'</span></button>';
			}
		}
		if($page>$pagesNumber)echo '<span style="font-size:12px;font-weight:bold;vertical-align:middle;padding-left:10px;">'.$page.'</span>';
		
		$urlb=$url.'&showonpage=';
		echo '<div class="fsShowonpage"><button onclick="simplePopup.showmenu(this,1,event)" type="button" data="showonpage-menu" class="glass-btn">Show on page: <span>'.$showonpage.'</span></button>';
		echo '<div class="popmenu hid" id="showonpage-menu">
			<table cellpadding="0"><tr>
			<td><a href="#'.$urlb.'5" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">5</a></td>
			<td><a href="#'.$urlb.'10" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">10</a></td>
			<td><a href="#'.$urlb.'15" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">15</a></td>
			<td><a href="#'.$urlb.'20" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">20</a></td>
			<td><a href="#'.$urlb.'25" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">25</a></td>
			<td><a href="#'.$urlb.'30" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">30</a></td>
			<td><a href="#'.$urlb.'50" class="showonpage-menu-item" onclick="setshowonpage(this);return false;">50</a></td>
			</tr></table>
		</div>';
		?>
		<script type="text/javascript">
		$('productsfound').innerHTML=<?php echo ($count==0)?"'Matches nothing'" : (($count==1)?"'Matches 1 product'":"'Matches $count products'"); ?>;
		</script>
		<?php
	}
	
	public function printProductDescription($p) {
		if($p){
?>
			<div class="pdesc-details">
			<div style="padding:3px 5px 5px 5px;"><?php echo $p->product_name; ?></div>
			<div class="pdesc-devider">Short Description</div>
			<div style="padding:3px 0 3px 5px;"><?php echo ($p->product_s_desc)? $p->product_s_desc : '<div class="grayed">- There is no Short Description -</div>'; ?></div>
			<div class="pdesc-devider">Full Description</div><div style="padding:4px 0 4px 7px;">
			<?php echo ($p->product_desc)? $p->product_desc : '<div class="grayed">- There is no Full Description -</div>'; ?>
			</div></div>
			<?php
		}
		else{echo 'No info about this product.';}
	}
	
	public function squeeze($string, $characters=18){
		$len = mb_strlen($string, 'UTF-8');
		if ($len > $characters){
			$string = mb_substr($string, 0, $characters, 'UTF-8') .'<b class="threedots">..</b>';
		}
		return $string;
	}

}
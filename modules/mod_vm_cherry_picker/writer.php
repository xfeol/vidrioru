<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
class chpWriter{
	
	protected static $parameters=array();
	protected static $parameter_count=0;
	protected static $curr_param_index=0;
	protected static $param_has_applied_filter=array();
	protected static $add_seemore=false;
	protected static $close_seemore=false;
	
	public function setParameters($_parameters){
		self::$parameters=$_parameters;
		self::$parameter_count=count($_parameters);
	}
	
	public function setCurrParameterIndex($i){
		self::$curr_param_index=$i;
	}
	
	public function setParamHasAppliedFilter($bool){
		self::$param_has_applied_filter[self::$curr_param_index]=$bool;
	}
	
	// writes filters alone to html
	static function writeFilter($filter,$count,$filter_selected,$filters_shown,$url=''){
		$mode=(isset(self::$parameters[self::$curr_param_index]['mode']))? self::$parameters[self::$curr_param_index]['mode']:null;
		if($mode==3) return self::filter_to_html_color($filter,$count,$filter_selected,$filters_shown,$url);
		if(chpconf::option('type')==0) return self::filter_to_html_list($filter,$count,$filter_selected,$filters_shown,$url);
		if(chpconf::option('type')==1) return self::filter_to_html_table($filter,$count,$filter_selected,$filters_shown,$url);
		if(chpconf::option('type')==2) return self::filter_to_html_list2($filter,$count,$filter_selected,$filters_shown,$url);
	}
	
	static function filter_to_html_list($filter,$count,$filter_selected,$filters_shown,$url){
		$s='';
		if(chpconf::option('useseemore') && $filters_shown==chpconf::option('b4seemore')){
			//$s.='</ol>';
			//if(chpconf::option('smanchor')==0){$s.=self::see_more();}else{self::$add_seemore=true;}
			//$s.='<ol class="chp-ll hid">';
			$s=self::appendSeeMore();
		}
		if (chpconf::option('translate')) $filter=JText::_($filter);
		$unit=self::$parameters[self::$curr_param_index]['parameter_unit'];
		if(chpconf::option('mode')==0){
			$s.='<li><a href="'.$url.'" class="chp-la" title="Select '.$filter.$unit.'"><span class="chp-lf">'.$filter.$unit.'</span>';
			if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
				$s.=' <span class="narrowValue">('.$count.')</span>';
			}
			$s.='</a></li>';
		}else{
			if($count){
				if($filter_selected){
					$s.='<li><a href="'.$url.'" class="chp-la" title="Remove '.$filter.$unit.'"><span class="chp-ltick ticksel"> </span> <span class="refinementSelected">'
					.$filter.$unit.'</span></a></li>';
				}else{
					$s.='<li><a href="'.$url.'" class="chp-la" title="Select '.$filter.$unit.'"><span class="chp-ltick"> </span> <span class="chp-lf">'
					.$filter.$unit.'</span>';
					if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
						$s.=' <span class="narrowValue">('.$count.')</span>';
					}
					$s.='</a></li>';
				}
			}else if($filter_selected){
				$s.='<li><span class="chp-ltick tickunavail"> </span> <span class="chp-lunav">'
				.$filter.$unit.'</span></li>';
			}
		}
		return $s;
	}
	
	static function filter_to_html_table($filter,$count,$filter_selected,$filters_shown,$url){
		$s='';
		if(chpconf::option('useseemore') && $filters_shown==chpconf::option('b4seemore')){
			$s=self::appendSeeMore();
		}
		if (chpconf::option('translate')) $filter=JText::_($filter);
		$unit=self::$parameters[self::$curr_param_index]['parameter_unit'];
		if(chpconf::option('mode')==0){
			$s.='<a href="'.$url.'" class="chp-ta" title="Select '.$filter.'">'.$filter.$unit.'';
			if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
				$s.=' <span class="narrowValue">('.$count.')</span>';
			}
			$s.='</a>';
		}else{
			if($count){
				if($filter_selected){
					$s.='<a href="'.$url.'" class="chp-ta" title="Remove '.$filter.'"><span class="chp-ltick ticksel"> </span> <span class="refinementSelected">'
					.$filter.$unit.'</span></a>';
				}else{
					$s.='<a href="'.$url.'" class="chp-ta" title="Select '.$filter.'"><span class="chp-ltick"> </span> '
					.$filter.$unit;
					if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
						$s.=' <span class="narrowValue">('.$count.')</span>';
					}
					$s.='</a>';
				}
			}else if($filter_selected){
				$s.='<span class="chp-ltick tickunavail"> </span> <span class="chp-lunav">'.$filter.$unit.'</span>';
			}
		}
		return $s;
	}
	
	static function filter_to_html_list2($filter,$count,$filter_selected,$filters_shown,$url){
		$s='';
		if(chpconf::option('useseemore') && $filters_shown==chpconf::option('b4seemore')){
			$s=self::appendSeeMore();
		}
		if(chpconf::option('filters_in_column')>0 && $filters_shown!=0 && ($filters_shown % chpconf::option('filters_in_column'))==0) $s.='</ol><ol class="chp-dll">';
		if (chpconf::option('translate')) $filter=JText::_($filter);
		$unit=self::$parameters[self::$curr_param_index]['parameter_unit'];
		if(chpconf::option('mode')==0){
			$s.='<li><a href="'.$url.'" class="chp-dla" title="Select '.$filter.'"><span class="chp-dlf">'.$filter.$unit.'</span>';
			if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
				$s.=' <span class="narrowValue">('.$count.')</span>';
			}
			$s.='</a></li>';
		}else{
			if($count){
				if($filter_selected){
					$s.='<li><a href="'.$url.'" class="chp-dla" title="Remove '.$filter.'"><span class="chp-dltick ticksel"> </span> <span class="refinementSelected">'.$filter.$unit.'</span></a></li>';
				}else{
					$s.='<li><a href="'.$url.'" class="chp-dla" title="Select '.$filter.'"><span class="chp-dltick"> </span> <span class="chp-dlf">'.$filter.$unit.'</span>';
					if (chpconf::option('showfiltercount') == NUM_PROD_SHOW){
						$s.=' <span class="narrowValue">('.$count.')</span>';
					}
					$s.='</a></li>';
				}
			}else if($filter_selected){
				$s.='<li><div class="chp-dunaval"><span class="chp-dltick tickunavail"> </span> <span class="chp-dlunav">'
				.$filter.$unit.'</span></div></li>';
			}
		}
		return $s;
	}
	
	static function filter_to_html_color($filter,$count,$filter_selected,$filters_shown,$url){
		// for languages with non-latin characters: use this two lines, and add translations to joomla language file
		//$cl=JText::_($filter);
		//$color_class=' col-'.strtolower(str_replace(' ','',$cl));
		$color_class=' col-'.strtolower(str_replace(' ','',$filter));
		if (chpconf::option('translate')) $filter=JText::_($filter);
		$unit=self::$parameters[self::$curr_param_index]['parameter_unit'];
		$available_class=($count)? '' : ' colf-unavail';
		$sel_class=($filter_selected)? ' colf-sel' : '';
		$title=($filter_selected)?'Remove ':'Select ';
	
		$s=($count)?'<a href="'.$url.'" title="'.$title.$filter.'"':'<div ';
		$s.='class="chp-colfa'.$available_class.$sel_class.'"><div class="chp-colf '.$color_class.'"><span class="colf-f">'
			.$filter.$unit.'</span>';
		if (chpconf::option('showfiltercount') == NUM_PROD_SHOW && !$filter_selected){
			$s.=' <span class="colf-nar">('.$count.')</span>';
		}
		$s.='</div>';
		$s.=($count)?'</a>':'</div>';
		
		return $s;
	}
	
	static function appendSeeMore(){
		if(chpconf::option('type')==0){
			$s='</ol>';
			if(chpconf::option('smanchor')==0){$s.=self::see_more();}else{self::$add_seemore=true;}
			$s.='<ol class="chp-ll hid">';
		}else if(chpconf::option('type')==1){
			$s='</div>';
			if(chpconf::option('smanchor')==0){$s.=self::see_more();}else{self::$add_seemore=true;}
			$s.='<div class="hid">';
		}else if(chpconf::option('type')==2){
			$s='</ol>';
			if(chpconf::option('smanchor')==0){$s.=self::see_more();}else{self::$add_seemore=true;}
			$s.='<div class="chp-smhidden hid"><ol class="chp-dll">';
			self::$close_seemore=true;
		}
		return $s;
	}
	
	// 'See more/less' trigger
	static function see_more(){
		$s='<div class="chp-seemore"';
		if(chpconf::option('useseemore') && chpconf::option('use_seemore_ajax')) $s.=' paindex="'.self::$curr_param_index.'"';
		$s.='><span class="chp-smop">+</span> <span class="chp-smt">'.chpconf::option('seemore').'</span>';
		if(chpconf::option('useseemore') && chpconf::option('use_seemore_ajax')) $s.='<div class="chp-loader hid"><img src="'.chpconf::option('module_path').'css/ajax-loader.gif" /></div>';
		$s.='</div>';
		return $s;
	}
	
	// final touch
	static function wrapFilters($filters){
		$mode=(isset(self::$parameters[self::$curr_param_index]['mode']))?self::$parameters[self::$curr_param_index]['mode']:null;
		if($mode){
			if($mode==3){return self::wrap_html_colorlist($filters);} //if this is a Color Parameter
			else{return $filters;} // if this is a trackbar parameter
		}
		
		//if(self::$parameters[self::$curr_param_index]['parameter_type']=='R') return self::wrap_html_colorlist($filters);
	
		if(chpconf::option('type')==0) return self::wrap_html_list($filters);
		if(chpconf::option('type')==1) return self::wrap_html_table($filters);
		if(chpconf::option('type')==2) return self::wrap_html_list2($filters);
	}
	
	static function wrap_html_list($filters){
		$s='<div class="chp-list"><ol class="chp-ll">'.$filters.'</ol>';
		if(self::$add_seemore){ $s.=self::see_more(); self::$add_seemore=false; }
		$s.='</div>';
		return $s;
	}
	
	static function wrap_html_table($filters){
		$s="<div>$filters</div>";
		if(self::$add_seemore){ $s.=self::see_more(); self::$add_seemore=false; }
		return $s;
	}
	
	static function wrap_html_list2($filters){
		$i=self::$curr_param_index;
		$s='<div id="chp-dlist-'.$i.'" class="chp-dlist hid" data="'.$i.'"><ol class="chp-dll">'.$filters.'</ol>';
		if(self::$close_seemore){$s.='</div>'; self::$close_seemore=false;}
		if(self::$add_seemore){ $s.=self::see_more(); self::$add_seemore=false; }
		$s.='</div>';
		return $s;
	}
	
	// wrap filters for ajax queries; droplist layout
	static function wrap_html_list2_ajax($filters){
		$s='<ol class="chp-dll">'.$filters.'</ol>';
		return $s;
	}
	
	static function wrap_html_colorlist($filters){
		$s="<div class=\"colf-cont\"><div>$filters<div class=\"clear\"></div></div></div>";
		return (chpconf::option('type')==2)?self::wrap_html_list2($s):$s;
	}
		
	// add Parameter head
	static function writeParameter($filters){
		//if(self::$parameters[self::$curr_param_index]['parameter_type']=='R') return self::parameter_html_colorlist($filters);
		
		if(chpconf::option('type')==0) return self::parameter_to_html_list($filters);
		if(chpconf::option('type')==1) return self::parameter_to_html_table($filters);
		if(chpconf::option('type')==2) return self::parameter_to_html_dropdown($filters);
	}
	
	static function parameter_to_html_list($filters){
		$s='';
		$i=self::$curr_param_index;
		$mode=(isset(self::$parameters[$i]['mode']) && (self::$parameters[$i]['mode']==1 || self::$parameters[$i]['mode']==2))? 1: 0;
		$param_label=(chpconf::option('translate'))? JText::_(self::$parameters[$i]['parameter_label']) : self::$parameters[$i]['parameter_label'];
		if(chpconf::option('collapsehead')){
			$sel=$arrow_state_class='';
			if(self::$param_has_applied_filter[$i]){
				$sel=' selected';
			}else if(chpconf::option('default_collapsed')){
				$arrow_state_class=' down';
			}
			$s='<div class="chp-collap'.$sel.'"><h2 class="chp-lhead clck"><span class="arrow'.$arrow_state_class.'"> </span>'.self::$parameters[$i]['parameter_label'].'</h2>'.self::wrapFilters($filters).'</div>';
		}else{
			$d=($mode && chpconf::option('tb_lbl_type')==1)? ' class="chp-trackbarparam" data-name="'.self::$parameters[$i]['parameter_name'].'"' : '';
			//$s.='<h2 class="chp-lhead">'.$param_label.'</h2>'.self::wrapFilters($filters);
			$s.='<div'.$d.'><h2 class="chp-lhead">'.$param_label.'</h2>'.self::wrapFilters($filters).'</div>';
			//$s.='<div style="float:left;"><h2 class="chp-lhead">'.$param_label.'</h2>'.self::wrapFilters($filters).'</div>';
		}
		
		echo $s;
	}
	
	static function parameter_to_html_table($filters){
		$param_label=(chpconf::option('translate'))? JText::_(self::$parameters[self::$curr_param_index]['parameter_label']) : self::$parameters[self::$curr_param_index]['parameter_label'];
		$s='<tr><td class="chp-tp">'.$param_label.'</td><td class="chp-tf">'.self::wrapFilters($filters).'</td></tr>';
		echo $s;
	}
	
	static function parameter_to_html_dropdown($filters){
		$s='';
		$i=self::$curr_param_index;
		$param_label=(chpconf::option('translate'))? JText::_(self::$parameters[$i]['parameter_label']) : self::$parameters[$i]['parameter_label'];
		
		if(self::$parameters[$i]['mode']==1 || self::$parameters[$i]['mode']==2){
			echo '<div class="clear"></div>';
			echo '<div>'.$param_label.'</div>';
			echo self::wrapFilters($filters);
			return;
		}
		
		$data_selected=(chpconf::option('mode')==0 && self::$param_has_applied_filter[$i])? ' selected="1"' : '';
		$s.='<div class="chp-parameter" data="'.$i.'"'.$data_selected.'><button type="button" class="chp-lbtn';
		if(self::$param_has_applied_filter[$i]) $s.=' chp-lbtn-sld';
		$s.='">'.$param_label.'</button><button type="button" class="chp-rbtn';
		if(self::$param_has_applied_filter[$i]) $s.=' chp-rbtn-sld';
		$s.= '"><div class="down-arrow"> </div></button></div>';
		// finally add filters
		$s.=self::wrapFilters($filters);
		//if($i==(self::$parameter_count-1)) $s.='<br clear="all" />';
		echo $s;
	}
	
//	static function parameter_html_colorlist($filters){
		
//	}
	
//	static function filter_to_html_color($filter,$count,$filter_selected,$filters_shown,$url){
	
	static function writeBackLink($url, $filters){
		$i=self::$curr_param_index;
		// if Parameter is Color
		$m=(isset(self::$parameters[$i]['mode']))? self::$parameters[$i]['mode'] : null;
		if($m==3) {
			$s = (is_array($filters)) ? implode(', ', $filters) : $filters;
			return self::filter_to_html_color($s, 1, true, 1, $url);
		}
		
		$s='';
		$unit=self::$parameters[$i]['parameter_unit'];
		if(chpconf::option('type')==0){
			$s.='<li><a href="'.$url.'" class="chp-la"><span class="chp-lf">&lsaquo; '.chpconf::option('backlink').'</span></a></li>';
		}else if(chpconf::option('type')==1){
			$s.='<a href="'.$url.'" class="chp-ta nofloat">&lsaquo; '.chpconf::option('backlink').'</a>';
		}else if(chpconf::option('type')==2){
			$s.='<li><a href="'.$url.'" class="chp-lrem"><span class="chp-dlf">&lsaquo; '.chpconf::option('backlink').'</span></a></li>';
		}
		if(chpconf::option('translate')){
			$s.='<div class="chp-lssel">';
			$fs='';
			if (chpconf::option('short_url')) $filters = explode('|', $filters);
			foreach($filters as $filter){
				$filter=JText::_($filter);
				if($fs) $fs.=', ';
				$fs.=$filter.$unit;
			}
			$s.=$fs;
		// otherwise, we do it easier
		}else{
			if( is_array($filters) ){
				$s.='<div class="chp-lssel">'.implode(', '.$unit, $filters).$unit;
			} else {
				$s.='<div class="chp-lssel">'.$filters.$unit;
			}
		}
		$s.='</div>';
		//return self::wrapFilters($s);
		return $s;
	}
	
	static function writeClearLink($url){
		// clear link for Color Parameter
		if(isset(self::$parameters[self::$curr_param_index]['mode']) 
			&& self::$parameters[self::$curr_param_index]['mode']==3) return '<div><a href="'.$url.'" class="chp-la"><span class="chp-lf">'.chpconf::option('clear').'</span></a></div>';
		
		if(chpconf::option('type')==0){
			$s='<li><a href="'.$url.'" class="chp-la"><span class="chp-lf">'.chpconf::option('clear').'</span></a></li>';
		}else if(chpconf::option('type')==1){
			$s='<div class="chp-tclear"><a href="'.$url.'" class="chp-ta nofloat">'.chpconf::option('clear').'</a></div>';
		}else if(chpconf::option('type')==2){
			$s='<li><a href="'.$url.'" class="chp-lrem">'.chpconf::option('clear').'</a></li>';
		}
		return $s;
	}
	
	static function writeTitle($title){
		if(chpconf::option('type')==0){
			echo '<div class="chp-ltitle">'.$title.'</div>';
		}else if(chpconf::option('type')==1){
			echo '<div class="chp-ttitle">'.$title.'</div>';
		}else if(chpconf::option('type')==2){
			echo '<div class="chp-ltitle">'.$title.'</div>';
		}
	}
	
	static function writeTotalProducts($count){
		if(chpconf::option('type')==0){
			echo '<div class="chp-total"><b>'.chpconf::option('pretext_totalproducts').'</b> '.$count.'</div>';
		}else if(chpconf::option('type')==1){
			echo '<tr><td class="chp-ttotal"><b>'.chpconf::option('pretext_totalproducts').'</b></td><td class="chp-ttotalres">'.$count.'</td></tr>';
		}else if(chpconf::option('type')==2){
			echo '<div class="chp-clear"> </div><div class="chp-total"><b>'.chpconf::option('pretext_totalproducts').'</b> '.$count.'</div>';
		}
	}
	
	// static function writeBlockStart($form=''){
		// if(chpconf::option('type')==0){
			// echo '<div id="chpNav'.chpconf::option('module_id').'" class="chpNav">'.$form;
		// }else if(chpconf::option('type')==1){
			// echo '<table id="chpNav'.chpconf::option('module_id').'" class="chp-tt">'.$form;
		// }else if(chpconf::option('type')==2){
			// echo '<div id="chpNav'.chpconf::option('module_id').'" class="chpddNav">'.$form;
		// }
	// }
	
	static function writeBlockStart(){
		if(chpconf::option('type')==0){
			echo '<div id="chpNav'.chpconf::option('module_id').'" class="chpNav">';
		}else if(chpconf::option('type')==1){
			echo '<div id="chpNav'.chpconf::option('module_id').'">';
		}else if(chpconf::option('type')==2){
			echo '<div id="chpNav'.chpconf::option('module_id').'" class="chpddNav">';
		}
	}
	
	static function writeBlockEnd(){
		if(chpconf::option('type')==0){
			echo '</div>';
		}else if(chpconf::option('type')==1){
			echo '</div>';
		}else if(chpconf::option('type')==2){
			echo '<div class="chp-clear"> </div></div>';
		}
	}
	
	static function writeFormStart($form) {
		if(chpconf::option('type')==1){
			echo $form.'<table class="chp-tt">';
		}else {
			echo $form;
		}
	}
	
	static function writeFormEnd() {
		if(chpconf::option('type')==1){
			echo '</form></table>';
		}else {
			echo '</form>';
		}
	}
	
	static public function writePriceForm($form,$price_selected,$url){
		$t='';
		if(chpconf::option('type')==0){
			$s='<div class="chp-list price-cont">'.$form.'</div>';
			if(chpconf::option('collapsehead')){
				$sel=$arrow_state_class='';
				if($price_selected){$sel=' selected';}
				else if(chpconf::option('default_collapsed')){ $arrow_state_class=' down';}
				//$sel=(self::$param_has_applied_filter[$i])? ' selected' : '';
				$s='<div class="chp-collap'.$sel.'"><h2 class="chp-lhead clck"><span class="arrow'.$arrow_state_class.'"> </span>'.chpconf::option('pricetitle').'</h2>'.$s.'</div>';
			}else{
				$t='<h2 class="chp-lhead">'.chpconf::option('pricetitle');
				if($price_selected) $t.='<a href="'.$url.'" class="chp-pa">'.chpconf::option('clear').'</a>';
				$t.='</h2>';
			}
		}else if(chpconf::option('type')==1){
			$s='<tr><td class="chp-tp">'.chpconf::option('pricetitle');
			if($price_selected) $s.='<a href="'.$url.'" class="chp-pa">'.chpconf::option('clear').'</a>';
			$s.='</td><td class="chp-tf">'.$form.'</td></tr>';
		}else if(chpconf::option('type')==2){
			$s='<div class="dl-price-cont">'.$form.'</div>';
		}
		echo $t;
		echo $s;
	}
	
	static function writeLoading(){
		return '<li style="margin:2px 20px;"><img src="'.chpconf::option('module_path').'css/ajax-loader.gif" width="16" height="11" /></li>';
	}
	
	static function printShowResuls($url) {
		echo '<div class="show-selection-results"><a href="'. $url .'" class="chp-show-results">Show Results &rarr;</a></div>';
	
	}
	
	static function addScript(){
		$doc =& JFactory::getDocument();
		$cid = JRequest::getVar('category_id', null);
		$itemid = JRequest::getVar('Itemid', null);
		//$js='<script type="text/javascript" src="'.chpconf::option('module_path').'js/unc.trackbar.js"></script>';
		//$js='<script type="text/javascript" src="'.chpconf::option('module_path').'js/trackbar.js"></script>';
		//$doc->addScript(chpconf::option('module_path').'js/unc.trackbar.js');
	//	$doc->addCustomTag($js);
		
		//echo file_get_contents(chpconf::option('module_path').'js/uncompressed.pricetrackbar.js');
	
		
		if(chpconf::option('add_tb_js') && !(isset($GLOBALS['trackbarjs_added']))){
			$GLOBALS['trackbarjs_added']=1;
		//	$js='<script type="text/javascript" src="'.chpconf::option('module_path').'js/unc.trackbar.js"></script>';
			$js='<script type="text/javascript" src="'.chpconf::option('module_path').'js/trackbar.js"></script>';
			$doc->addCustomTag($js);
			$s='<script type="text/javascript">
var words={
	from:"'.chpconf::option('tb_from').'",
	to:"'.chpconf::option('tb_to').'",
	all:"'.chpconf::option('tb_all').'",
	apply:"'.chpconf::option('tb_apply').'"
},
chpSelfUpdating = 0,
chp={};';
			if(chpconf::option('tb_lbl_type') && chpconf::option('type')==0){
				$s.='var popup=document.createElement("div");
window.addEvent(\'domready\',function(){
	popup.id="chp-popup";
	popup.className="hid";
	popup.style.minWidth=document.chpform'.chpconf::option('module_id').'.offsetWidth-13+"px";
	document.body.appendChild(popup);
	popup.innerHTML=\'<span class="ppname"></span> <span class="ppvalue"></span>\';
});';
				//$s.=file_get_contents(chpconf::option('module_path').'js/unc.popup.js');
				//$s.=file_get_contents(chpconf::option('module_path').'js/popup.js');
				$s.=file_get_contents(dirname(__FILE__).'/js/popup.js');
			}
			$s.='</script>';
		$doc->addCustomTag($s);
		}
		?>
<script type="text/javascript">
	<?php if(chpconf::option('type')==0 && chpconf::option('collapsehead')){ ?>
	$$('#chpNav<?php echo chpconf::option('module_id'); ?> .chp-collap').each(function(div){
		var trig=div.getElement('h2');
		var block=trig.getNext();
		var fx=new Fx.Slide(block,{duration:200, onComplete:function(){
			trig.getFirst().toggleClass('down');
			var cont=block.getParent();
			if(cont.getStyle('height').toInt()!=0) cont.setStyle('height','');
			}
		});
		<?php if(chpconf::option('default_collapsed')){ ?>
		// hide Parameters on page-load
		if(div.hasClass('selected')==false)fx.hide();
		<?php } ?>
		trig.addEvent('click', function(){
			fx.toggle();
		});
	});
	<?php }
	if(!(chpconf::option('type')==2 && chpconf::option('load_filters_ajax')) && chpconf::option('useseemore')){ ?>
	var currEffect;
	$$('.chp-seemore').addEvent('click',function(){
		var trig=this;
		<?php if(chpconf::option('smanchor')==0){ echo 'var list=trig.getNext();';}else{ echo 'var list=trig.getPrevious();';} ?>
		<?php if(chpconf::option('use_seemore_ajax')){ ?>
		if(trig.getAttribute('loaded')!=1){
			var loader=trig.getElement('.chp-loader');
			var url='<?php echo chpconf::option('module_path').'ajax/ajax.php'; ?>';
			var parameters='<?php echo 'product_type_id='.chpconf::option('ptid').'&mid='.chpconf::option('module_id').'&offset='.chpconf::option('b4seemore'); ?>';
			parameters+='&paindex='+trig.getAttribute('paindex');
			<?php 
			//$uri=JFactory::getURI();
			//$s=$uri->_query;
			$s=chpconf::option('applied_filters_url');
			echo " parameters+='&$s';";
			if($cid) echo " parameters+='&category_id=$cid';";
			if($itemid) echo " parameters+='&Itemid=$itemid';";
			?>
			new Ajax (url,{
				method:'get',
				data: parameters,
				onRequest: function(){
					trig.setProperty('loaded',1);
					loader.removeClass('hid');
				},
				onComplete: function(response){
					if(response){
						loader.remove()
						list.innerHTML=response;
					}else{
						trig.remove();
					}
				}
			}).request();
		}
		<?php } ?>
		if(list.hasClass('hid')){
			trig.getFirst().innerHTML='-';
			trig.getElement('.chp-smt').innerHTML='<?php echo chpconf::option('seeless'); ?>';
			<?php if(chpconf::option('fadein')){ ?>
			if(currEffect){currEffect.stop();currEffect=null;}
			list.setStyle('opacity',0);
			list.removeClass('hid');
			var fade=list.effect('opacity', {
				duration: 400,
				transition: Fx.Transitions.Quad.easeInOut
			});
			currEffect=fade;
			fade.start(0,1);
			<?php }else{ ?>
			list.removeClass('hid');
			<?php } ?>
		}else{
			list.addClass('hid');
			trig.getFirst().innerHTML='+';
			trig.getElement('.chp-smt').innerHTML='<?php echo chpconf::option('seemore'); ?>';
		}
	});
	<?php } ?>
	
	<?php if(chpconf::option('type')==2){ ?>
	var listTimeout=[];
	$$('.chp-parameter').addEvents({
		mouseover:function(){
			var i=$(this).getAttribute('data');
			if(listTimeout[i]){
				clearTimeout(listTimeout[i]);
				listTimeout[i]=null;
			}else{
				var btn=this;
				listTimeout[i]=setTimeout(function(){showFilterList(btn)},300);
			};
			
		},
		mouseout:function(){
			var i=$(this).getAttribute('data');
			if(!$('chp-dlist-'+i).hasClass('hid')){
				listTimeout[i]=setTimeout(function(){hideFilterList(i)},300);
			}else{
				clearTimeout(listTimeout[i]);
				listTimeout[i]=null;
			}
			
		},
		click:function(){
			$$('.chp-dlist').addClass('hid');
			var i=$(this).getAttribute('data');
			clearTimeout(listTimeout[i]);
			listTimeout[i]=null;
			showFilterList(this);
		}
	});

	$$('.chp-dlist').addEvents({
		mouseover:function(){
			var i=$(this).getAttribute('data');
			clearTimeout(listTimeout[i]);
			listTimeout[i]=null;
		},
		mouseout:function(){
			var i=$(this).getAttribute('data');
			listTimeout[i]=setTimeout(function(){hideFilterList(i)},300);
		}
	});

	function showFilterList(btn){
		var paindex=btn.getAttribute('data');
		var list=$('chp-dlist-'+paindex);
		<?php if(chpconf::option('load_filters_ajax')){ ?>
		if(!(list.getAttribute('loaded')==1 || $(btn).getAttribute('selected')==1)) loadFilters(paindex);
		<?php } ?>
		var butsize=$(btn).getSize().size;
		var left=$(btn).offsetLeft;
		var top=$(btn).offsetTop+butsize.y+0;
		list.removeClass('hid');
		list.setStyles({top:top,left:left});
	}

	function hideFilterList(i){
		listTimeout[i]=null;
		$('chp-dlist-'+i).addClass('hid');
	}
	<?php if(chpconf::option('load_filters_ajax')){ ?>
	function loadFilters(paindex){
		var list=$('chp-dlist-'+paindex);
		var url='<?php echo chpconf::option('module_path').'ajax/ajax.php'; ?>';
		var parameters='<?php echo 'product_type_id='.chpconf::option('ptid').'&mid='.chpconf::option('module_id'); ?>';
		parameters+='&paindex='+paindex+'&offset=0';
		<?php 
		//$uri=JFactory::getURI();
		//$s=$uri->_query;
		$s=chpconf::option('applied_filters_url');
		echo " parameters+='&$s';";
		if($cid) echo " parameters+='&category_id=$cid';";
		if($itemid) echo " parameters+='&Itemid=$itemid';";
		?>
		new Ajax (url,{
			method:'get',
			data: parameters,
			onRequest: function(){
				list.setProperty('loaded',1);
			},
			onComplete: function(response){
				if(response){
					list.innerHTML=response;
				}else{
					<?php if(chpconf::option('remove_empty_params')){ ?>
					list.getPrevious().remove();
					list.remove();
					<?php }else{ ?>
					list.innerHTML='<div class="chp-nofilters"><?php echo chpconf::option('empty_params_msg'); ?></div>';
					list.getPrevious().addClass('chp-transparent');
					<?php } ?>
				}
			}
		}).request();
		
	}
<?php 
	} 
}
?>
</script>
		<?php
	}
	
	// this code below is for updating results of filtering with Ajax. Requires MooTools 1.2 & higher, and MT.More-delegation-extension
	static function addScriptForSelffUpdating() {
		$doc =& JFactory::getDocument();
		$s = '<script type="text/javascript">
		var module_id = "'. chpconf::option('module_id') .'",
			category_id = "'. JRequest::getVar('category_id', '') .'",
			getModuleUrl = "'. chpconf::option('module_path').'ajax/getmodule.php";
		
		chpSelfUpdating = 1;
';
		$s .= file_get_contents(dirname(__FILE__).'/js/selfupdate.js');
		$s .= '
		ChP2.init();
		</script>';
		$doc->addCustomTag($s);
	
	}
	
}?>
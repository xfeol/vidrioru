<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

class chpController{
	//private $options=array();
	private $itemid;
	private $_ptid=0;
	private $_basequery='';
	private $baseurl='';
	private $applied_filters_url='';
	private $parameters=array();
	// contains current parameter index. Necessary in some iterations checks
	private $curr_param_index=0;
	// boolean, whether parameter has applied filter(s)
	//private $param_has_applied_filter=array();
	private $applied_filters=array();
	private $parameters_applied=0;
	private $filter_info=array();
	private $where_clause=array();
	private $low_price=0;
	private $high_price=0;
	private $usecache=false;
	private $cachepath='';
	private $cace='';
	private $pagetitle='';
	
	// finds out which ptid to use: custom, from the url, automatic from DB
	public function apprehendPTID($custom_ptid=0){
		$ptid=0;
		$db=& JFactory::getDBO();
		//if(chpconf::option('custom_ptid')){
		if($custom_ptid){
			//$ptid=chpconf::option('custom_ptid');
			$ptid=$custom_ptid;
		}else{
			$url_ptid=JRequest::getVar('product_type_id','');
			if ( empty($url_ptid) ) {  // if product_type_id in a link - we do not make query
				$category_id=JRequest::getVar('category_id','');
				if (!empty($category_id)){
					$q = "SELECT pptx.`product_type_id`".
						 " FROM (`#__vm_product_product_type_xref` as pptx, `#__vm_product` as p)".
						 " LEFT JOIN `#__vm_product_category_xref` as pcx ".
						 " ON pptx.`product_id`=pcx.`product_id`".
						 " WHERE pcx.`category_id`='$category_id'".
						 " AND p.`product_id`=pcx.`product_id`".
						 " AND p.`product_publish`='Y'";
					
					if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
						$q .= ' AND p.`product_in_stock` > 0';
					}
					$q.= " LIMIT 0 , 1";
					$db->setQuery($q);
					$ptid=$db->loadResult();
				}
				//else{
				//	$q="SELECT `product_type_id` FROM `#__vm_product_type` LIMIT 1;";
				//	$db->setQuery($q);
				//	$ptid=$db->loadResult();
				//}
			}else{
				$ptid=$url_ptid;
			}
		}
		// set the ptid for further use
		$this->_ptid=$ptid;
	}
	
	public function getParameters(){
		if(!$this->parameters){
			$db=& JFactory::getDBO();
			$q="SELECT * FROM `#__vm_product_type_parameter` WHERE `product_type_id`={$this->ptid()}";
			if(chpconf::option('show_specific_params')){
				$q.= " AND `parameter_include`='Y' ";
			}						
			$q .= " ORDER BY `parameter_list_order`";
			$db->setQuery($q);
			$this->parameters=$db->loadAssocList();
			chpWriter::setParameters($this->parameters);
			return $this->parameters;
			//$parameters_count=count($parameter);
			//print_r($parameter);
		}else{
			return $this->parameters;
		}
	}
	
	public function getFilters($offset=null){
		//echo '<pre>';
		//print_r($this->parameters);
		//echo chpconf::option('module_path');
		//echo '</pre>';
		
		//$db=& JFactory::getDBO();
		//$q = "SELECT DISTINCT `".$this->parameters[$this->curr_param_index]['parameter_name']."` FROM #__vm_product_type_{$this->ptid()} WHERE 1";
		//$db->setQuery($q);
		//$filters = $db->loadResultArray();
		$s='';
		$filters_shown=$j=0;
		$mode=(isset($this->parameters[$this->curr_param_index]['mode']))?$this->parameters[$this->curr_param_index]['mode']:null;
		$param_has_filter=false;
		$filters=explode(';',$this->parameters[$this->curr_param_index]['parameter_values']);

		foreach($filters as $i => $filter){
			if(chpconf::option('useseemore') && chpconf::option('use_seemore_ajax') && $filters_shown==chpconf::option('b4seemore') && !$mode){ $s.=chpWriter::appendSeeMore(); break;}
			// skip some filters; when using ajax see more..
			if($offset!==null && $offset>$i) continue;
			
			if (chpconf::option('showfiltercount') == NUM_PROD_NOT_CALC) {
				$count = 1;
			} else {
				$count=$this->getFilterCount($filter);
			}
			
			$filter_selected=$this->filterSelected($filter);
			// offset only filters ready for output; uncomment these two lines, comment above skip-line
			//if(!$count && !$filter_selected ) continue;
			//if($offset!==null && $offset>$j){ ++$j; continue; }
			if($count){
				$url=$this->getFilterUrl($filter,$filter_selected);
				$s.=chpWriter::writeFilter($filter,$count,$filter_selected,$filters_shown,$url);
				$filters_shown++;
			}else if($filter_selected){
				$s.=chpWriter::writeFilter($filter,0,true,$filters_shown);
				$filters_shown++;
			}
			if(chpconf::option('fill_metatitle') && $filter_selected && !$offset) $this->addToPageTitle($filter);
			if (!$param_has_filter && $filter_selected) $param_has_filter=true;
			
		}
		
		// add Clear link at top
		if(chpconf::option('mode')==1 && chpconf::option('showclearlink') && $param_has_filter && !$offset) $s=$this->getClearLink().$s;
		
		// hide parameters with 1 filter
		if(chpconf::option('hide_params_with1filter') && $param_has_filter==false && $filters_shown<2 && !$offset && !(chpconf::option('useseemore') && chpconf::option('use_seemore_ajax'))) return false;
		
		//$this->param_has_applied_filter[$this->curr_param_index]=$param_has_filter;
		chpWriter::setParamHasAppliedFilter($param_has_filter);
		//return ($s)? chpWriter::wrapFilters($s) : false;
		return ($s)? $s : false;
	}
	
	public function getFilterCount($filter){
		//echo $filter;
		//echo $this->parameter['parameter_values'];
		
		if($this->usecache){
			$f='';
			foreach ($this->parameters as $i => $p) {
				if ($i == $this->curr_param_index) {
					$f.=$p['parameter_name'].'_'.$filter.',';
				} else if ($this->applied_filters[$i]) {
					$mode = (isset($p['mode'])) ? $p['mode'] : null;
					if (chpconf::option('short_url') || $mode == 1 || $mode == 2) {
						$f .= $p['parameter_name'].'_'. $this->applied_filters[$i] .',';
					} else {
						foreach ($this->applied_filters[$i] as $n) {$f.=$p['parameter_name'].'_'.$n.',';}
					}
				}
			}
			//echo 'needle:'.$f."<br /><br />";
			$cid=JRequest::getVar('category_id','');
			$needle=md5("$cid;{$this->ptid()};$f");
			$count=$this->getFromCache($needle);
			if($count!==false) return $count;
		}
		
		// when here and use cache--nothing found. let's do a query, and write to cache.
		// assamble where_clause, depending on the mode
		if(chpconf::option('mode')==0){
			$where=implode('',$this->where_clause);
		}else{
			$where='';
			foreach($this->parameters as $i => $p){
				if($i!=$this->curr_param_index){
					$where.=$this->where_clause[$i];
				}
			}
		}
		
		$query=$this->basequery().$where;
		if($this->parameters[$this->curr_param_index]['parameter_type']!="V"){
			$query.=" AND pt.`{$this->parameters[$this->curr_param_index]['parameter_name']}`=\"$filter\" ";
		}
		else{
			$query.=" AND FIND_IN_SET(\"$filter\",REPLACE(pt.`{$this->parameters[$this->curr_param_index]['parameter_name']}`,';',',')) ";
		}
		
		//if(chpconf::option('custom_ptid')){
		//	$query.=" GROUP BY `product_id`"; // in case smbdy adds same products to diff categories				
		//}
		
		//echo $query."<br /><br />";
		$db=& JFactory::getDBO();
		$db->setQuery($query);
		$count=$db->loadResult($query);
	//	$res=$db->loadObjectList();
	//	echo count($res);
		
		if($this->usecache){$this->writeToCache($needle,$count);}
		
		return $count;
	}
	
	// checks whether current filter was applied
	private function filterSelected($filter){
		$af=$this->applied_filters[$this->curr_param_index];
		if(!$af) return false;
		$array=chpconf::option('short_url')?explode('|',$af) : $af;
		return (in_array($filter,$array));
	}
	
	public function getFilterUrl($filter, $filter_selected) {
		// FORM THE URLs
		$j=count($this->parameters);
		$url='';
		
		$curr_index = $this->curr_param_index;
		$array=$this->applied_filters[$curr_index];
		//$array=array(); use this line if in multi mode want to apply only one filter
		if(chpconf::option('mode')==0){
			if (chpconf::option('short_url')) {
				for ($i=0; $i<$j; $i++){
					if ($i == $curr_index) {
						$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($filter);
					}
					else if($this->applied_filters[$i]){
						$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
					}
				}
			} else {
				for ($i=0; $i<$j; $i++){
					if($i == $curr_index){
						$url.=$this->filter_info[$i]['comp_full'].'&'.$this->filter_info[$i]['param_name'].'='.urlencode($filter);
					}
					else if ($this->applied_filters[$i]) {
						$mode = (isset($this->parameters[$i]['mode'])) ? $this->parameters[$i]['mode'] : null;
						if ($mode == 1 || $mode == 2) {
							$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
						} else {
							$url.=$this->filter_info[$i]['comp_full'];
							foreach($this->applied_filters[$i] as $f){
								$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);
							}
						}
					}
				}
			}
		} else {
			// if removing a filter will lead to 0 products, we may remove all filters from this group
			if (chpconf::option('showfiltercount') != NUM_PROD_NOT_CALC && chpconf::option('usesmartsearch')) {
				//if($this->parameters_applied>1 && $filter_selected && count($this->applied_filters[$this->curr_param_index])>1){
				if ($this->parameters_applied > 1 && $filter_selected) {
					$a = chpconf::option('short_url') ? explode("|", $this->applied_filters[$curr_index]) : $this->applied_filters[$curr_index];
					if (count($a) > 1) {
						$where='';
						foreach($this->parameters as $i => $p){
							if ($i != $curr_index) {
								$where.=$this->where_clause[$i];
							}else{
								$result=array_diff($a,(array)$filter);
								if($p['parameter_type']!='V'){
									$where.=" AND `{$p['parameter_name']}` IN (\"".join("\",\"",$result)."\")";
								}else{
									$w=array();
									foreach($result as $value){
										if($value){array_push($w,"FIND_IN_SET(\"$value\",REPLACE(`{$p['parameter_name']}`,';',','))");}
									}
									if($w) $where.=" AND (".join(" OR ",$w).")";
								}
							}
						}
						// echo 'where:'. $where;
						$query=$this->basequery().$where;
						$db=& JFactory::getDBO();
						$db->setQuery($query);
						$count=$db->loadResult($query);
						if($count==0) return $this->getClearParameterUrl();
					}
				}
			}
		//	if(!$url){
			if (chpconf::option('short_url')) {
				for ($i=0; $i<$j; $i++){
					if ($curr_index == $i) {
						$array=($this->applied_filters[$i])?explode('|',$this->applied_filters[$i]) : array();
						if($filter_selected){
							$result=array_diff($array,(array)$filter);
							if($result)$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode(implode('|',$result));
							//if($result)$url.=$this->filter_info[$i]['comp_full'];
							//foreach($result as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
						}else{
							$array[]=$filter;
							$url.='&'.$this->filter_info[$curr_index]['param_name'].'='.urlencode(implode('|',$array));
							//
							//$url.=$this->filter_info[$i]['comp_full'];
							//foreach($array as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
						}
					}
					else if($this->applied_filters[$i]){
					//	if($this->parameters[$i]['mode']){
							$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
					//	}else{
					//		$url.=$this->filter_info[$i]['comp_full'];
					//		foreach($this->applied_filters[$i] as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
					//	}
					}
				}
			} else {
				for ($i=0; $i<$j; $i++){
					if ($curr_index == $i) {
						if($filter_selected){
							$result=array_diff($array,(array)$filter);
							if($result)$url.=$this->filter_info[$i]['comp_full'];
							foreach($result as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
						}else{ // else add to array
							$array[]=$filter;
							$url.=$this->filter_info[$i]['comp_full'];
							foreach($array as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
						}
					}
					else if ($this->applied_filters[$i]){
						$mode=(isset($this->parameters[$i]['mode']))?$this->parameters[$i]['mode']:null;
						if($mode==1 || $mode==2){
							$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
						}else{
							$url.=$this->filter_info[$i]['comp_full'];
							foreach($this->applied_filters[$i] as $f){$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);}
						}
					}
				}
			}
		}	
		
		// $cid=JRequest::getVar('category_id',null);
		// if ($url) {
			// $url=$this->baseurl.'&product_type_id='.$this->ptid().$url.'&limitstart=0';
		// }
		// elseif ($cid) { // if we are browsing in a category - we don't need product_type_id
			// $url=$this->baseurl.'&limitstart=0';
		// }
		// else{ // if it's not category - this may be Parameter Search - then we need P_T_I
			// $url=$this->baseurl.'&product_type_id='.$this->ptid().'&limitstart=0';
		// }
		$url=$this->baseurl.'&product_type_id='.$this->ptid().$url;
		//if($this->low_price || $this->high_price){
		//	$url.='&low-price='.$this->low_price.'&high-price='.$this->high_price;
		//}
		//if($debti){$url.='&chp=showtime';}
		//$mid=JRequest::getVar('manufacturer_id','');
		//if($mid){$url.='&manufacturer_id='.$mid;}
		
		return $url;
	}
	
	// gathers a Base query. Should be done one time.
	public function apprehendBaseQuery(){
		$category_id=JRequest::getInt('category_id','');
		$manufacturer_id=JRequest::getVar('manufacturer_id','');
		
		//$columns="pt.`product_id`, `category_id`";
		//$columns="pt.`product_id`";
		$joins="LEFT JOIN `#__vm_product` as p ON pt.`product_id`=p.`product_id`";
		$where="AND p.`product_publish`='Y'";
		
		$lp=(chpconf::option('include_tax')) ? $this->low_price/chpconf::option('tax') : $this->low_price;
		$hp=(chpconf::option('include_tax')) ? $this->high_price/chpconf::option('tax') : $this->high_price;
		if($lp || $hp){
			$joins.= " LEFT JOIN `#__vm_product_price` as pr ON p.`product_id`=pr.`product_id`";
		}
		if($manufacturer_id){
			$joins.=" LEFT JOIN `#__vm_product_mf_xref` as pm ON pm.`product_id`=p.`product_id`";
			$where.=" AND pm.`manufacturer_id`=$manufacturer_id";
		}
		
		if($category_id && !chpconf::option('custom_ptid')){ // becuase we use custom PTI in some category - we don't want wrong category_id to be added
			$tables="`#__vm_product_type_{$this->ptid()}` as pt";
			$joins="LEFT JOIN `#__vm_product_category_xref` as pcx ON pt.`product_id`=pcx.`product_id` ".$joins;
			$where="pcx.`category_id`=$category_id ".$where;
			// uncomment this, comment above 1 line--for search with Child Products
			//$where="(pcx.`category_id`=$category_id OR `product_parent_id`<>0) ".$where;
		}else{
			$tables="`#__vm_product_type_{$this->ptid()}` as pt, `#__vm_product_category_xref` as pcx";
			$where="pt.`product_id`=pcx.`product_id` ".$where;
			// uncomment this, comment above 2 lines--for search with Child Products
			//$tables="`#__vm_product_type_{$this->ptid()}` as pt";
			//$where="1 ".$where;
		}
		
		if($lp && !$hp){
			$where.=" AND `product_price`>=".$lp;
		}
		elseif(!$lp && $hp){
			$where.=" AND `product_price`<=".$hp;
		}
		elseif($lp && $hp && ($hp>$lp)){
			$where.=" AND `product_price` BETWEEN ".$lp." AND ".$hp;
		}
		
		//if(CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1"){
		//	$where.= " AND p.`product_in_stock`>0";
		//}
		
		//$query="SELECT $columns FROM ($tables) $joins WHERE $where";
		$query="SELECT COUNT(*) FROM ($tables) $joins WHERE $where";
		//$query="SELECT COUNT(DISTINCT pt.`product_id`) FROM ($tables) $joins WHERE $where";
		$this->_basequery=$query;
		//echo $query;
	}
	
	public function stripslashes_deep($value){
		$value=is_array($value) ? array_map(array('chpController','stripslashes_deep'), $value) : str_replace('\\', '', $value);
		//stripslashes($value);
		return $value;
	}
	
	public function getAppliedFilters(){
		foreach($this->parameters as $i => $p){
			// if this is trackbar
			if(isset($p['mode']) && ($p['mode']==1 || $p['mode']==2)){
				$get=JRequest::getVar($p['parameter_name'],null);
				$this->applied_filters[]= ($get)? $get : '';
				if($get) $this->parameters_applied++;
				$this->where_clause[$i]='';
				if($get){
					if($p['mode']==1){
						if($p['parameter_type']!="V"){
							$this->where_clause[$i]=' AND pt.`'.$p['parameter_name'].'`="'.$get.'"';
						}else{
							$this->where_clause[$i]=" AND FIND_IN_SET(\"{$get}\",REPLACE(pt.`{$p['parameter_name']}`,';',','))";
						}
					}else{
						$v=explode(':',$get);
						$s=" AND pt.`".$p['parameter_name']."`";
						if($v[0] && !$v[1]){$s.=">=".$v[0];}
						else if(!$v[0] && $v[1]){$s.="<=".$v[1];}
						else if($v[0]==$v[1]){$s.="=".$v[0];}
						else{$s.=" BETWEEN ".$v[0]." AND ".$v[1];}
						$this->where_clause[$i]=$s;
					}
				}
				$this->filter_info[]=array("param_name"=>$p['parameter_name']);
				continue;
			}
			
			if(chpconf::option('short_url')){
				$param=$p['parameter_name'];
				$_get=JRequest::getVar($param,null);
				$this->applied_filters[]= ($_get)? $_get : '';
				$get=($_get)? explode('|',$_get) : null;
			}else{
				$param='product_type_'.$this->ptid().'_'.$p['parameter_name'];
				$get=(array)JRequest::getVar($param,null);
				$this->applied_filters[]= ($get)? $get : '';
			}
			// strip off the slashes from filters: uncomment this; comment out above line;
			//$this->applied_filters[]=$this->stripslashes_deep($get);
			if($get) $this->parameters_applied++;
			
			$comp_name=$param.'_comp';
			$this->where_clause[$i]='';
			if($p['parameter_type']!="V"){
				if(chpconf::option('mode')==0){	// if single mode
					$comp_value='texteq';
					$param_name=$param;
					if($get) $this->where_clause[$i]=' AND pt.`'.$p['parameter_name'].'`="'.$get[0].'"';
				}else{
					$comp_value='in';
					$param_name=$param.'[]';
					//if($get) $this->where_clause[$i]=" AND `{$p['parameter_name']}` IN (\"".( is_array($get) ? join("\",\"",$get) : $get)."\")";
					if($get) $this->where_clause[$i]=" AND `{$p['parameter_name']}` IN (\"". join("\",\"",$get) ."\")";
				}
			}else{
				if(chpconf::option('mode')==0){
					$comp_value='find_in_set';
					$param_name=$param;
					if($get) $this->where_clause[$i]=" AND FIND_IN_SET(\"{$get[0]}\",REPLACE(pt.`{$p['parameter_name']}`,';',','))";
				}else{
					$comp_value='find_in_set_any';
					$param_name=$param.'[]';
					
					if($get){
						$w=array();
						foreach($get as $value){
							if($value){array_push($w,"FIND_IN_SET(\"$value\",REPLACE(pt.`{$p['parameter_name']}`,';',','))");}
						}
						if($w) $this->where_clause[$i]=" AND (".join(" OR ",$w).")";
					}
				}
			}
			if(chpconf::option('short_url')){
				$this->filter_info[]=array("param_name"=>$param);
			}else{
				$comp_full='&'.$comp_name.'='.$comp_value;
				$this->filter_info[]=array("comp_name"=>$comp_name,	"comp_value"=>$comp_value, "comp_full"=>$comp_full, "param_name"=>$param_name);
			}
		}
		// echo '<pre>';
		// print_r($this->applied_filters);
		// print_r($this->filter_info);
		// print_r($this->where_clause);
		// echo $this->parameters_applied;
		// echo '</pre>';
	}
	
	public function setCurrParameterIndex($i){
		$this->curr_param_index=$i;
	}
	
	public function apprehendPrices(){
		$this->low_price=$this->validate_price(JRequest::getVar('low-price',''));
		$this->high_price=$this->validate_price(JRequest::getVar('high-price',''));
	}
	
	/*
	* Check for filters, and assamble into WHERE SQL query for Virtuemart; also assamble filters into URL for Virtuemart
	* return array or false
	*/
	public function getVMFilters(){
		$this->apprehendPTID();
		if(!$this->ptid()) return false;
		$ptid=$this->ptid();
		$url_ptid=JRequest::getVar('product_type_id',null);
		$db=& JFactory::getDBO();
		$q="SELECT * FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$parameters=$db->loadAssocList();
		$where=array();
		$join=$url='';
		$pt="`#__vm_product_type_$ptid`";
		foreach($parameters as $p){
			$get=JRequest::getVar($p['parameter_name'],null);
			if($get){
				$mode=(isset($p['mode']))?$p['mode']:null;
				if($mode==1){ // trackbar with one slider
					if($p['parameter_type']!="V"){
						$where[]="$pt.`".$p['parameter_name']."`=\"".$get."\"";
					}else{
						$where[]="FIND_IN_SET(\"$get\",REPLACE($pt.`{$p['parameter_name']}`,';',','))";
					}
				}else if($mode==2){ // trackbar with two sliders
					$v=explode(':',$get);
					$s="$pt.`".$p['parameter_name']."`";
					if($v[0] && !$v[1]){$s.=">=".$v[0];}
					else if(!$v[0] && $v[1]){$s.="<=".$v[1];}
					else if($v[0]==$v[1]){$s.="=".$v[0];}
					else{$s.=" BETWEEN ".$v[0]." AND ".$v[1];}
					$where[]=$s;
				}else{ // filter link, but our standarts
					if($p['parameter_type']!="V"){
						$g=explode("|",$get);
						if(count($g)>1){
							$where[]="$pt.`{$p['parameter_name']}` IN (\"". join("\",\"",$g) ."\")";
						}else{
							$where[]="$pt.`".$p['parameter_name'].'`="'.$get.'"';
						}
					}else{
						$g=explode("|",$get);
						if(count($g)>1){
							$w=array();
							foreach($g as $value){
								if($value){array_push($w,"FIND_IN_SET(\"$value\",REPLACE(`{$p['parameter_name']}`,';',','))");}
							}
							if($w) $where[]="(".join(" OR ",$w).")";
						}else{
							$where[]="FIND_IN_SET(\"{$get}\",REPLACE($pt.`{$p['parameter_name']}`,';',','))";
						}
					}
				}
				$url.="&".$p['parameter_name']."=".urlencode($get);
			}
		}
		if($where && !$url_ptid){
			// add joins
			$join.="LEFT JOIN `#__vm_product_type_$ptid` ON `#__vm_product`.`product_id` = $pt.`product_id` ";
			$join.="LEFT JOIN `#__vm_product_product_type_xref` ON `#__vm_product`.`product_id`=`#__vm_product_product_type_xref`.`product_id` ";
			$where[]="`#__vm_product_product_type_xref`.`product_type_id`=$ptid ";
		}
		if($where){
			return array("where"=>implode(' AND ',$where),"join"=>$join,"url"=>$url);
		}
		return false;
	}
	
	/*
	* Get applied parameters, and print into hidden fields for Order By form.
	* 
	*/
	public function getVMOrderByForm(){
		$ptid=$url_ptid=JRequest::getVar('product_type_id',null);
		if(!$ptid){
			$this->apprehendPTID();
			$ptid=$this->ptid();
		}
		$db=& JFactory::getDBO();
		$q="SELECT * FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$parameters=$db->loadAssocList();
		if(!$parameters) return;
		foreach($parameters as $p){
			$get_base="product_type_$ptid"."_".$p['parameter_name'];
			$get=JRequest::getVar($get_base,null); // try default link format
			if(!$get){
				$get_base=$p['parameter_name'];
				$get=JRequest::getVar($get_base,null); // try new link format
				if(!$get) continue; // nothing in url, continue to next parameter
			}
			if(is_array($get)){
				foreach($get as $g){
					echo '<input type="hidden" name="'.$get_base.'[]" value="'.$g.'" />';
				}
			}else{
				echo '<input type="hidden" name="'.$get_base.'" value="'.$get.'" />';
			}
			$comp=JRequest::getVar("product_type_$ptid"."_".$p['parameter_name']."_comp",null);
			if($comp) echo '<input type="hidden" name="product_type_'.$ptid.'_'.$p['parameter_name'].'_comp" value="'.$comp.'" />';
		}
		// now let's add the prices
		$low_price=JRequest::getVar("low-price", null);
		$high_price=JRequest::getVar("high-price", null);
		if($low_price || $high_price){
			echo '<input type="hidden" name="low-price" value="'.$low_price.'">';
			echo '<input type="hidden" name="high-price" value="'.$high_price.'">';
		}
		if($url_ptid) echo '<input type="hidden" name="product_type_id" value="'.$url_ptid.'">';
	}
	
	/*
	* Check for prices, and assamble into WHERE SQL query for Virtuemart
	* @param (int)tax - provide tax amount (e.g. 1.2 for 20%)
	* return string or false
	*/
	public function getVMPriceQuery($tax=false){
		$this->apprehendPrices();
		if($tax){
			$this->low_price=$this->low_price/$tax;
			$this->high_price=$this->high_price/$tax;
		}
		if($this->low_price && !$this->high_price){
			return "`product_price`>=".$this->low_price;
		}
		elseif(!$this->low_price && $this->high_price){
			return "`product_price`<=".$this->high_price;
		}
		elseif($this->low_price && $this->high_price && ($this->high_price>$this->low_price)){
			return "`product_price` BETWEEN ".$this->low_price." AND ".$this->high_price;	
		}
		//$s='low:'.$this->low_price;
		return false;
	}
	
	public function getBackLink(){
		$url=$this->getClearParameterUrl();
		chpWriter::setParamHasAppliedFilter(true);
		if(chpconf::option('fill_metatitle')) $this->addToPageTitle(implode(', ',$this->applied_filters[$this->curr_param_index]));
		
		return chpWriter::writeBackLink($url,$this->applied_filters[$this->curr_param_index]);
	}
	
	protected function getClearLink(){
		$url=$this->getClearParameterUrl();
		return chpWriter::writeClearLink($url);
	}
	
	public function getClearParameterUrl(){
		$url='';
		foreach($this->parameters as $i => $p){
			if($i!=$this->curr_param_index && $this->applied_filters[$i]){
				if(isset($p['mode']) && ($p['mode']==1 || $p['mode']==2)){
					$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
					continue;
				}else if(chpconf::option('short_url')){
					$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
				}else{
					$url.=$this->filter_info[$i]['comp_full'];
					foreach($this->applied_filters[$i] as $f){
						$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);
					}
				}
			}
			
		}
		$url=$this->baseurl.'&product_type_id='.$this->ptid().$url;
		//$url=($url)? $this->baseurl.'&product_type_id='.$this->ptid().$url : $this->baseurl;
		return $url;
	}
	
	/*
	*	Instead of making filters as links, show parameter as trackbar
	*/
	public function getTrackbarParameter(){
		$i=$this->curr_param_index;
		$mode=$this->parameters[$i]['mode'];
		$pname=$this->parameters[$i]['parameter_name'];
		$plabel=$this->parameters[$i]['parameter_label'];
		$units=$this->parameters[$i]['parameter_unit'];
		$vl=$vr='null';
		
		// 1. If you want to check values for trackbar as well.
		// $values=explode(';',$this->parameters[$i]['parameter_values']);
		// $poss_values=array();
		// foreach($values as $v){
			// if($this->getFilterCount($v)) $poss_values[]=$v;
		// }
		
		// 2. Choose values from DB
		// $db=& JFactory::getDBO();
		// $q = "SELECT DISTINCT `".$this->parameters[$this->curr_param_index]['parameter_name']."` FROM #__vm_product_type_{$this->ptid()} WHERE 1";
		// $db->setQuery($q);
		// $poss_values=$db->loadResultArray();
		
		$poss_values=explode(';',$this->parameters[$i]['parameter_values']);
		$count=count($poss_values);
		
		$url_value=JRequest::getVar($pname,null);
		$sel_class='';
		if($url_value){
			if($mode==1){
				$key=array_search($url_value,$poss_values);
				if($key!==false) $vl=$key+1;
			}else{
				$v=explode(':',$url_value);
				if($v[0]){$key=array_search($v[0],$poss_values); if($key!==false) $vl=$key;}
				if($v[1]){$key=array_search($v[1],$poss_values); if($key!==false) $vr=$key;}
				//print_r($v);
			}
			$sel_class=' class="selected"';
			chpWriter::setParamHasAppliedFilter(true);
		}else{
			chpWriter::setParamHasAppliedFilter(false);
		}
		//echo 'vl: '.$vl.' vr='.$vr;
		$values=($mode==1)?'["","':'["';
		$values.=implode('","',$poss_values);
		$values.='"]';
		
		//$label_id=(chpconf::option('tb_lbl_type')==0)? '$("'.$pname.'-label")' : '$("chp-popup")';
		//$label_id=(chpconf::option('tb_lbl_type')==0)? 'document["chpform'.chpconf::option('module_id').'"].querySelector("#'.$pname.'-label")' : '$("chp-popup")';
		$label_id=(chpconf::option('tb_lbl_type')==0)? '$$("#chpNav'.chpconf::option('module_id').' #'.$pname.'-label")[0]' : '$("chp-popup")';
		
		$limit_right=($mode==1)? $count : --$count;
		
		$s='<div'.$sel_class.'>';
		
		if(chpconf::option('tb_lbl_type')==0){
			$s.='<div id="'.$pname.'-label"><span class="ppvalue"></span></div>';
			//$s.='<div id="'.$pname.'-label"><a class="ppvalue chp-pcl" href="'.$this->getClearParameterUrl().'">'.chpconf::option('clear').'</a>';
			//if($url_value) $s.='<div class="chp-pcl"><a href="'.$this->getClearParameterUrl().'">'.chpconf::option('clear').'</a></div>';
			//$s.='</div>';
		//}else if($url_value) $s.='<div class="chp-pcl"><a href="'.$this->getClearParameterUrl().'">'.chpconf::option('clear').'</a></div>';
		}else if($url_value) $s.='<span class="chp-pcl" data-pname="'.$pname.'" onclick="clearRefine(this,chpform'.chpconf::option('module_id').')" onmouseover="clearHover(this,true)" onmouseout="clearHover(this,false)">'.chpconf::option('clear').'</span>';
		$s.='<div class="chp-tbouter"><div id="chptb-'.$pname.'" class="chp-trackbar hid"><div class="chp-fullrange"></div>';
		if($mode==2) $s.='<div class="chp-selrange"></div>';
		$s.='<div class="left-slider"></div>';
		if($mode==2) $s.='<div class="right-slider"></div>';
		$s.='</div><br/></div><input type="hidden" name="'.$pname.'" value="" />'; 
		
		$s.='<script type="text/javascript">
window.addEvent(\'domready\',function(){
chp.'.$pname.'=new chp_trackbar({
	mode:'.$mode.',
	form:document["chpform'.chpconf::option('module_id').'"],
	trackbar:$$("#chpNav'.chpconf::option('module_id').' #chptb-'.$pname.'")[0],
	inputLeft:"'.$pname.'",
	label:'.$label_id.',
	labeltype:'.chpconf::option('tb_lbl_type').',
	values:'.$values.',
	limitLeft:0,
	limitRight:'.$limit_right.',
	valueLeft:'.$vl.',
	valueRight:'.$vr.',
	t:{l:"'.$plabel.'",u:"'.urlencode($units).'"},
	extremumValueToNull:1
});
});
</script></div>';
//trackbar:document["chpform'.chpconf::option('module_id').'"].querySelector("#chptb-'.$pname.'"),
//trackbar:document["chpform'.chpconf::option('module_id').'"].getElementById("chptb-'.$pname.'"),
//trackbar:$("chptb-'.$pname.'"),
//inputLeft:document["chpform'.chpconf::option('module_id').'"].querySelector(\'input[name="'.$pname.'"]\'),
//inputRight:null,
		return $s;
	}
		
	public function getTotalProducts(){
		$where=implode('',$this->where_clause);
		$query=$this->basequery().$where;
		$db=& JFactory::getDBO();
		$db->setQuery($query);
		return chpWriter::writeTotalProducts($db->loadResult($query));
	}
	
	public function setBaseUrl(){
		$itemid=JRequest::getVar('Itemid','');
		$category_id=JRequest::getVar('category_id','');
		$mid=JRequest::getVar('manufacturer_id','');
		$debti=(JRequest::getVar('chp','')=='showtime')? 1 : 0;
		if(chpconf::option('custom_ptid')){ // don't include category if we have global search
			$category_id='';
		}
		//$this->baseurl='/index.php?option=com_virtuemart&page=shop.browse&Itemid='.$itemid.'&category_id='.$category_id.'&limitstart=0';
		$this->baseurl = JURI::base() .'index.php?option=com_virtuemart&page=shop.browse&Itemid='.$itemid.'&category_id='.$category_id.'&limitstart=0';
		
		if($mid)$this->baseurl.='&manufacturer_id='.$mid;
		if($this->low_price || $this->high_price){
			$this->baseurl.='&low-price='.$this->low_price.'&high-price='.$this->high_price;
		}
		if($debti)$this->baseurl.='&chp=showtime';
		
		$this->assembleAppliedFiltersUrl();
	}
	
	private function assembleAppliedFiltersUrl() {
		$url='';
		foreach($this->parameters as $i => $p){
			if($this->applied_filters[$i]){
				if(isset($p['mode']) && ($p['mode']==1 || $p['mode']==2)){
					$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
				}else if(chpconf::option('short_url')){
					$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
				}else{
					$url.=$this->filter_info[$i]['comp_full'];
					foreach($this->applied_filters[$i] as $f){
						$url.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);
					}
				}
			}
			
		}
		$url='product_type_id='.$this->ptid().$url;
		$this->applied_filters_url=$url;
		chpconf::set('applied_filters_url', $url);
	}
	
	public function getTitle(){
		$cid=JRequest::getVar('category_id','');
		if(chpconf::option('titletype')==2 || (chpconf::option('titletype')!=0 && (empty($cid) || chpconf::option('custom_ptid')))){
			chpWriter::writeTitle(chpconf::option('statictitle'));
		}else if(chpconf::option('titletype')==1){
			$query="SELECT `category_name` FROM `#__vm_category` WHERE `category_id`=$cid;";
			$db=& JFactory::getDBO();
			$db->setQuery($query);
			chpWriter::writeTitle(chpconf::option('dynamictitle').' <b>'.$db->loadResult().'</b>');
		}
	}
	
	private function addToPageTitle($filter){
		if($this->pagetitle) $this->pagetitle.=', ';
		$this->pagetitle.=$filter;
	}
	
	public function setPageTitle(){
		if($this->low_price || $this->high_price){
			// we have only low-price
			if($this->low_price && !$this->high_price){ $this->pagetitle.= " (\${$this->low_price} & Above)"; }
			// we have only high-price
			else if(!$this->low_price && $this->high_price){ $this->pagetitle.= " (\${$this->high_price} & Under)"; }
			// else, both
			else{ $this->pagetitle.= " (\${$this->low_price} - \${$this->high_price})"; }
		}
		if($this->pagetitle){
			$this->pagetitle=' - '.$this->pagetitle;
			// set META
			$doc=& JFactory::getDocument();
			$doc->setTitle($doc->getTitle().$this->pagetitle);
		}
	}
	
	public function addStyleSheet(){
		$doc =& JFactory::getDocument();
		if(chpconf::option('type')==0){
			$doc->addStyleSheet( chpconf::option('module_path').'css/sakura.css');
		}else if(chpconf::option('type')==1){
			$doc->addStyleSheet( chpconf::option('module_path').'css/htable.css');
		}else if(chpconf::option('type')==2){
			$doc->addStyleSheet( chpconf::option('module_path').'css/dropdown.css');
		}
		//$doc->addStyleSheet( $options['module_path'].'css/chpwereld.css' );
		
	}
	
	// returns apprehended ptid
	public function ptid(){
		return $this->_ptid;
	}
	
	public function basequery(){
		return $this->_basequery;
	}
	
	public function parameter_applied($i){
		return ($this->applied_filters[$i])? true : false;
	}

	function validate_price($price){
		// not empty
		if(empty($price)) return false;
		// not -X or 0
		if($price<=0) return false;
		// change , with .
		$price=str_replace(',','.',$price);
		if(!is_numeric($price)) return false;
		// remove leading/trailing zeros
		$price+=0;
		
		return $price;
	}
	
	public function formStart(){
		$manufacturer_id=JRequest::getVar('manufacturer_id','');
		$category_id=JRequest::getVar('category_id','');
		$itemid=($this->itemid)? $this->itemid : JRequest::getVar('Itemid','');
		
		$s='<form name="chpform'.chpconf::option('module_id').'" method="get" action="index.php">
<input type="hidden" name="option" value="com_virtuemart"/>
<input type="hidden" name="page" value="shop.browse"/>
<input type="hidden" name="category_id" value="'.$category_id.'"/>
<input type="hidden" name="Itemid" value="'.$itemid.'"/>
<input type="hidden" name="limitstart" value="0"/>';
		if($this->ptid()){
			$s.='<input type="hidden" name="product_type_id" value="'.$this->ptid().'"/>';
		}
		if($manufacturer_id){
			$s.='<input type="hidden" name="manufacturer_id" value="'.$manufacturer_id.'"/>';
		}
		
		$j=count($this->parameters);
		for($i=0; $i<$j; ++$i){
			if(isset($this->parameters[$i]['mode']) && ($this->parameters[$i]['mode']==1 || $this->parameters[$i]['mode']==2)) continue; // skip trackbar parameters
			if($this->applied_filters[$i]){
				if(chpconf::option('short_url')){
					$s.='<input type="hidden" name="'.$this->filter_info[$i]['param_name'].'" value="'.$this->applied_filters[$i].'" />';
				}else{
					$s.='<input type="hidden" name="'.$this->filter_info[$i]['comp_name'].'" value="'.$this->filter_info[$i]['comp_value'].'" />';
					foreach($this->applied_filters[$i] as $f){
						$s.='<input type="hidden" name="'.$this->filter_info[$i]['param_name'].'" value="'.$f.'" />';
					}
				}
			}
		};
		
		chpWriter::writeFormStart($s);
	}
	
	public function getSearchByPrice(){
		$manufacturer_id=JRequest::getVar('manufacturer_id','');
		$category_id=JRequest::getVar('category_id','');
		$itemid=($this->itemid)? $this->itemid : JRequest::getVar('Itemid','');
		
		//<form action="index.php" method="get">
		$priceform='<div class="customPrice">';
		/*	<input type="hidden" name="option" value="com_virtuemart"/>
			<input type="hidden" name="page" value="shop.browse"/>
			<input type="hidden" name="category_id" value="'.$category_id.'"/>
			<input type="hidden" name="Itemid" value="'.$itemid.'"/>
			<input type="hidden" name="limitstart" value="0"/>';
		if($this->ptid()){
			$priceform.='<input type="hidden" name="product_type_id" value="'.$this->ptid().'"/>';
		}
		if($manufacturer_id){
			$priceform.='<input type="hidden" name="manufacturer_id" value="'.$manufacturer_id.'"/>';
		}*/
		
		$clearPriceUrl='';
		$parameter_count=count($this->parameters);
		for($i=0; $i<$parameter_count; ++$i){
			if($this->applied_filters[$i]){
				$mode=(isset($this->parameters[$i]['mode']))?$this->parameters[$i]['mode']:null;
				if(chpconf::option('short_url') || $mode==1 || $mode==2){
					$clearPriceUrl.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($this->applied_filters[$i]);
					continue;
				}
				$clearPriceUrl.=$this->filter_info[$i]['comp_full'];
				foreach($this->applied_filters[$i] as $f){
					$clearPriceUrl.='&'.$this->filter_info[$i]['param_name'].'='.urlencode($f);
				}
			}
		}
		
		$price_selected=false;
		if(($this->low_price && !$this->high_price) || (!$this->low_price && $this->high_price) || ($this->low_price && $this->high_price && ($this->high_price>$this->low_price))){
			$price_selected=true;
			$baseurl='index.php?option=com_virtuemart&page=shop.browse&Itemid='.$itemid.'&category_id='.$category_id;
			if($manufacturer_id){$baseurl.='&manufacturer_id='.$manufacturer_id;}
			if(!empty($clearPriceUrl)){
				$clearPriceUrl=$baseurl.'&product_type_id='.$this->ptid().$clearPriceUrl.'&limitstart=0';
			}
			else{
				$clearPriceUrl=$baseurl.$clearPriceUrl.'&limitstart=0';
			}
			//$priceform='<div><a href="'.$clearPriceUrl.'" class="chp-pa">'.chpconf::option('clear').'</a></div>'.$priceform;
		}
		
		if(chpconf::option('showtrackbar')) $priceform=$this->getPriceTrackbar().$priceform;
		
		
		$low_price_value=$high_price_value='';
		$valueLeft=$valueRight='null';
		if($this->low_price && !$this->high_price){
			$low_price_value=' value="'.$this->low_price.'"';
			$valueLeft=$this->low_price;
		}
		elseif(!$this->low_price && $this->high_price){
			$high_price_value=' value="'.$this->high_price.'"';
			$valueRight=$this->high_price;
		}
		elseif($this->low_price && $this->high_price && ($this->high_price>$this->low_price)){
			$low_price_value=' value="'.$this->low_price.'"';
			$high_price_value=' value="'.$this->high_price.'"';
			$valueLeft=$this->low_price;
			$valueRight=$this->high_price;
		}		
		
		$priceform.='<table><tr>
			 <td><label for="low-price">'.chpconf::option('price_from').'</label></td>
			 <td style="width:50%;padding:0 3px 0 0;"><input id="low-price" type="text" maxlength="9" name="low-price"'.$low_price_value.'/></td>
			 <td><label for="high-price">&nbsp;'.chpconf::option('price_to').'</label></td>
			 <td style="width:50%;padding:0 3px 0 0;"><input id="high-price" type="text" maxlength="9" name="high-price"'.$high_price_value.'/></td>
			 <td style="padding-left:4px;"><input id="chpGoBtn" type="image" title="Go" alt="Go" on1click="applyRefines(chpform'.chpconf::option('module_id').');return false;" src="'.chpconf::option('module_path').'css/gobtn.gif"/></td>
			 </tr>
			</table>
			</div>'; //</form> // onclick="searchFormSubmit(event)"
		
		if(chpconf::option('showtrackbar')){
			if(chpconf::option('leftlimitauto') || chpconf::option('rightlimitauto')){
				$auto=$this->getExtremumPrice($category_id,$this->ptid());
				$limitLeft=(chpconf::option('leftlimitauto'))? (chpconf::option('include_tax')? $auto[0]*chpconf::option('tax') : $auto[0]) : chpconf::option('leftlimit');
				$limitRight=(chpconf::option('rightlimitauto'))? (chpconf::option('include_tax')? $auto[1]*chpconf::option('tax') : $auto[1]) : chpconf::option('rightlimit');
				//print_r($auto);
			} else {
				$limitLeft=chpconf::option('leftlimit');
				$limitRight=chpconf::option('rightlimit');
			}
			if($limitLeft>$limitRight){
				$limitLeft=0;
				$limitRight=10;
				$priceform.='<span style="color:#CCCCCC;font-size:11px;">With your configuration left limit is larger then the right. Default values were used.</span>';
			} else if($limitLeft==$limitRight) {
				$limitLeft-=1;
			}
			
			//echo ' limit left:'.$limitLeft;
			//echo ' limit right:'.$limitRight;
			$priceform.='<script type="text/javascript">
window.addEvent(\'domready\',function(){
chp.price=new chp_trackbar({
	form:document["chpform'.chpconf::option('module_id').'"],
	trackbar:$$("#chpNav'.chpconf::option('module_id').' #pricetrackbar")[0],
	inputLeft:"low-price",
	inputRight:"high-price",
	limitLeft:'.$limitLeft.',
	limitRight:'.$limitRight.',
	valueLeft:'.$valueLeft.',
	valueRight:'.$valueRight.',
	extremumValueToNull:1
});
});
</script>';
//trackbar:document["chpform'.chpconf::option('module_id').'"].querySelector("#pricetrackbar"),
//trackbar:document.getElementsByName("chpform'.chpconf::option('module_id').'")[0].getElementById("pricetrackbar"),
//trackbar:$("pricetrackbar"),
//inputLeft:document["chpform'.chpconf::option('module_id').'"].querySelector(\'input[name="low-price"]\'),
//inputRight:document["chpform'.chpconf::option('module_id').'"].querySelector(\'input[name="high-price"]\'),
		}
		chpWriter::writePriceForm($priceform,$price_selected,$clearPriceUrl);
	}
	
	function getPriceTrackbar(){
		$s='<div class="chp-tbouter"><div id="pricetrackbar" class="chp-trackbar hid"><div class="chp-fullrange"></div>
<div class="chp-selrange"></div>
<div class="left-slider"></div>
<div class="right-slider"></div>
</div><br/></div>';	
		return $s;
	}
	
	
	public function refinement_applied() {
		$lp = $this->low_price;
		$hp = $this->high_price;
		return ($this->parameters_applied > 0 || $lp || $hp);
	}
	
	public function get_show_results() {
		$url = $this->baseurl .'&'. $this->applied_filters_url;
		chpWriter::printShowResuls($url);
	}
	
	
	function getExtremumPrice($category_id,$ptid){
		$db=& JFactory::getDBO();
		if(!empty($category_id)){
			$q= "SELECT MIN(`product_price`) AS leftLimit, MAX(`product_price`) AS rightLimit".
	        	" FROM `#__vm_product_category_xref` as pcx".
	            " LEFT JOIN `#__vm_product_price` pp".
	            " ON pcx.`product_id`=pp.`product_id`".
	            " LEFT JOIN `#__vm_product` as p".
                " ON pcx.`product_id`=p.`product_id`".
	            " WHERE pcx.`category_id`='$category_id'".
	            " AND p.`product_publish`='Y'";
        }
        else if(!empty($ptid)){
        	$q= "SELECT MIN(`product_price`) AS leftLimit, MAX(`product_price`) AS rightLimit".
	        	" FROM `#__vm_product_type_$ptid` pt".
	            " LEFT JOIN `#__vm_product_price` as pp".
	            " ON pt.`product_id`=pp.`product_id`".
	            " LEFT JOIN `#__vm_product` as p".
                " ON pt.`product_id`=p.`product_id`".
                " WHERE p.`product_publish`='Y'";
        }
		$db->setQuery($q);
		return $db->loadRow();
	}
	
	public function checkCache(){
		$manufacturer_id=JRequest::getVar('manufacturer_id','');
		if($this->low_price || $this->high_price || $manufacturer_id) return;
		
		$filename=dirname(__FILE__)."/cache/cache_{$this->ptid()}.txt";
		//$filename=dirname(__FILE__)."/cache/cache_99.txt";
		$cacheready=$this->checkCacheFile($filename);
		//$cacheready=false;
		if($cacheready){
			$this->usecache=true;
			$this->cachepath=$filename;
			$this->cache=file_get_contents($filename);
		}
	}
	
	private function getFromCache($needle){
		if(empty($this->cache)){
			return false;
		}else{
			$pos=strpos($this->cache,$needle);
			if($pos===false){return false;}
			else{$v=substr($this->cache,$pos+strlen($needle)+1,6); return $v+=0;}
		}
	}
	
	private function writeToCache($f,$count){
		$content=$f."#".str_pad($count, 6, '0', STR_PAD_LEFT)."\n";
		$handle=fopen($this->cachepath,"a");
		fwrite($handle,$content);
		fclose($handle);
	}
	
	function checkCacheFile($filename){
		if(file_exists($filename)){
			return true;
		}else{
			// check directory
			$dir=dirname(__FILE__).'/cache/';
			if(!is_dir($dir)){
				mkdir($dir);
				$this->log("[".date("M d, Y - H:i:s")."] Directory /cache/ created");
			}
			
			//create file
			$handle=fopen($filename,"w");
			if($handle){
				fclose($handle);
				$this->log("[".date("M d, Y - H:i:s")."] File cache.txt created");
				return true;
			}
		}
		return false;
	}
		
	function log($msg){
		$filename=dirname(__FILE__).'/cache/log.txt';
		$handle=fopen($filename,"a");
		fwrite($handle,$msg."\n");
		fclose($handle);
	}
	
}
?>
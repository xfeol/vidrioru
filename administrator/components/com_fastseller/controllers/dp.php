<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsControllerDP{

	function getProducts(){
		global $vmpref;
		$db=& JFactory::getDBO();
		//sleep(5);
		$time_start=microtime(true);
		
		$keyword=JRequest::getVar('q','');
		$page=intval(JRequest::getCmd('page',1));
		$skip=intval(JRequest::getCmd('skip',0));
		$showonpage=intval(JRequest::getCmd('showonpage',fsconf::getOption('default_numrows')));
		$session_onpage = $_COOKIE['onpage'];
		if ($session_onpage) $showonpage = $session_onpage;
		
		$cid=JRequest::getVar('cid',null);
		$ptid=JRequest::getVar('ptid',null);
		$ppid=JRequest::getVar('ppid',null); // product parent id
		$orderby=JRequest::getVar('orderby','cat');
		$sc=JRequest::getVar('sc','asc');
		
		//$columns="p.`product_id`, `product_name`, `product_type_id` as pti, `product_publish`";
		$columns = "p.`product_id`, `product_name`, `product_type_id` as pti, `product_publish`";
		$tables="(`#__".$vmpref."_product` as p";
		$joins="";
		$where="";
		
		if (fsconf::getOption('show_sku')) $columns .= ', `product_sku`';
		
		if(($cid || $orderby=='cat') && !$ppid){
			$joins.="LEFT JOIN `#__".$vmpref."_product_category_xref` as pcx ON p.`product_id`=pcx.`product_id` ";
			if($orderby=='cat'){
				$columns.=", pcx.`category_id`, `category_name`";
				$joins.="LEFT JOIN `#__".$vmpref."_category` as c ON pcx.`category_id`=c.`category_id` ";
			}
		}
		
		if($ppid){
			$where.="AND (p.`product_id`=$ppid OR `product_parent_id`=$ppid) ";
		}
		else if($cid){
		//	$tables.=",`#__".$vmpref."_category` as c";
			//$joins.="LEFT JOIN `#__".$vmpref."_product_category_xref` as pcx ON p.`product_id`=pcx.`product_id` ";
		//	$where.="AND pcx.`category_id`=c.`category_id` AND pcx.`category_id`=$cid ";
			$where.="AND pcx.`category_id`=$cid ";
		}
		//if($ptid){
			$joins.="LEFT JOIN `#__".$vmpref."_product_product_type_xref` as ptx ON p.`product_id`=ptx.`product_id` ";
			if($ptid=='wopt'){$where.="AND ptx.`product_type_id` IS NULL ";}
			elseif($ptid){$where.="AND ptx.`product_type_id`=$ptid ";}
		//}
		$tables.=")";
		if(!$ppid) $where.="AND `product_parent_id`=0 ";
		
		$query= "SELECT $columns FROM $tables $joins".
				" WHERE 1 $where";
		if ($keyword) {
			$sq = "`product_name` LIKE '%$keyword%'";
			if (fsconf::getOption('show_sku')) $sq .= " OR `product_sku` LIKE '%$keyword%'";
			if (is_numeric($keyword)) $sq.= " OR p.`product_id`=$keyword";
			$query .= " AND ($sq)";
		}
		if(fsconf::getOption('show_unpublished_products')!='Y') $query.=" AND `product_publish`='Y'";
		if($orderby){
			switch($orderby){
				case 'cat': if(!$ppid) {$ordercolumn='`category_id`'; break; }
				case 'pid': $ordercolumn='`product_id`'; break;
				case 'pname': $ordercolumn='`product_name`'; break;
				case 'ptid': $ordercolumn='`product_type_id`'; break;
			}
			//if(!$cid) 
			$query.=" GROUP BY p.`product_id`";
			$query.=" ORDER BY $ordercolumn ".strtoupper($sc);
		}
		$query.=" LIMIT $skip, $showonpage";
		$db->setQuery($query);
		$rows=$db->loadObjectList();
		
		//require_once('../views/dp.php');
		//require_once(JPATH_COMPONENT.'/view/index.html');
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displayProducts($rows);
		//$view=new fsViewDP();
		//$view->getProducts($rows);
		
		$time_end=microtime(true);
		$elapsed=$time_end-$time_start;
		echo '<div style="margin-top:5px;color:#CCCCCC;font-size:11px;">Executed in: '.round($elapsed,5).' seconds</div>';
		
		//echo $query;
		//echo '<pre>'; print_r($rows); echo '</pre>';
		
		//echo 'url='.JURI::current();
		//$uri = JFactory::getURI();
		//echo '<br/><br/>url='.$uri->toString(array('query'));
		//print_r($uri->_vars);
	}
	
	function getPTParameters($ptid){
		global $vmpref;
		$db=& JFactory::getDBO();
		
		$q= "SELECT * ".
			"FROM `#__".$vmpref."_product_type_parameter` as ptp ".
			"WHERE `product_type_id`=$ptid ".
			"ORDER BY `parameter_list_order`";
		$db->setQuery($q);
		$res=$db->loadObjectList();
		
		return $res;
		//print_r($res);
		//echo $ptid;
		//echo $this->displayTitle();
	}
	
	function getSearch(){
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displaySearch();
	}
	
	function getRefinePane(){
		// this block will execute only when browser is reloaded, since by default $values==null
		$cid=JRequest::getVar('cid','');
		$ptid=JRequest::getVar('ptid','');
		$cname=$ptname='';
		if($cid || $ptid){
			global $vmpref;
			$db =& JFactory::getDBO();
		}
		if($cid){
			$query="SELECT `category_name` FROM `#__".$vmpref."_category` WHERE `category_id`=$cid";
			$db->setQuery($query);
			$cname=$db->loadResult();
		}
		if($ptid=='wopt'){$ptname='w/o Product Type';}
		elseif($ptid){
			$query="SELECT `product_type_name` FROM `#__".$vmpref."_product_type` WHERE `product_type_id`=$ptid";
			$db->setQuery($query);
			$ptname=$db->loadResult();
		}
		
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displayRefinePane($cname,$ptname);
	}
	
	function getCatTree(){
		global $vmpref;
		$db =& JFactory::getDBO();//sleep(6000);
		$cid=JRequest::getVar('cid','');
		$query= "SELECT `category_child_id` as id, `category_name` as name, `category_publish` ".
				"FROM `#__".$vmpref."_category` as c,`#__".$vmpref."_category_xref` as cx ".
				"WHERE c.`category_id`=cx.`category_child_id` ".
				"AND cx.`category_parent_id`=$cid ".
				"ORDER BY `list_order`";
		$db->setQuery($query);
		$cat=$db->loadObjectList();
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displayCatTree($cat);
	}
	
	function getPTTree(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		$query= "SELECT `product_type_id` as id, `product_type_name` as name, `product_type_publish` as pub ".
				"FROM `#__{$vmpref}_product_type` as pt ".
				"WHERE 1 ".
				"ORDER BY `product_type_list_order`";
		$db->setQuery($query);
		$pt=$db->loadObjectList();
		
		//require_once( dirname(__FILE__).'/../views/dp.php' );
		//if($switch==0){fsViewDP::displayPTTree($pt);}
		//else{
			return $pt;
			//fsViewDP::displayProductTypes($pt,$row);
		//}
	}
	
	// build Product Types Tree for Refinment menu
	function buildPTTreeMenu(){
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displayPTTreeMenu($this->getPTTree());
	}
	
	// build Menu with all Product Types for assigning to a Product
	function buildAllPTsMenu($row){
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printProductTypes($this->getPTTree(),$row);
	}
	
	// get Product Types for pop-up assign Dialog
	function getPTSelectDialog(){
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printPTSelectDialog($this->getPTTree());
	}
	
	function saveProduct(){
		global $vmpref;
		$db =& JFactory::getDBO();
		//sleep(6);
		$pid=JRequest::getVar('pid');
		$ptid=JRequest::getVar('thisptid');
		$row=JRequest::getVar('row');
		$adding=JRequest::getVar('adding');
		$urlptid=JRequest::getVar('urlptid','');
		if($adding=='pt'){	// if we adding product a PT
			if($ptid){
				$q="INSERT INTO `#__{$vmpref}_product_product_type_xref` VALUES($pid,$ptid)";
				$db->setQuery($q);
				if($db->query()){
					if($urlptid!='wopt'){
						echo $this->getProductParameters($pid,$ptid,$this->getPTParameters($ptid),$row);
					}
				}
			}
		}else{	// if we saving Parameters to product
			// prepare parameters
			$q="SELECT `parameter_name` FROM `#__{$vmpref}_product_type_parameter` ".
			"WHERE `product_type_id`=$ptid ORDER BY `parameter_list_order`";
			$db->setQuery($q);
			$parameters=$db->loadResultArray();
			
			$table="`#__{$vmpref}_product_type_$ptid`";
			$q="SELECT COUNT(`product_id`) FROM $table WHERE `product_id`=$pid";
			$db->setQuery($q);
			if($db->loadResult()){ // if already in DB -> update
				$q='';
				foreach($parameters as $p){
					if($q)$q.=", ";
					$q.="`$p`=";
					$value=JRequest::getVar($p,'');
					$q.=(!$value)?"NULL":"'$value'";
				}
				$q="UPDATE $table SET ".$q;
				$q.=" WHERE `product_id`=$pid";
				$db->setQuery($q);
				$db->query();
				//echo $q;
			}else{ // else -> insert
				$q="`product_id`=$pid";
				foreach($parameters as $p){
					$q .= ", ";
					//$q.="`$p`=";
					$value = JRequest::getVar($p,'');
					//$q .= (!$value) ? "NULL" : "'$value'";
					$q .= "`". $p ."`=";
					$q .= (!$value) ? "NULL" : $db->quote($value);
				}
				
				//$q="INSERT INTO $table VALUES($q)";
				$q="INSERT INTO $table SET $q";
				$db->setQuery($q);
				$db->query();
				//echo $q;
			}
				
			//print_r($parameters);
		}
	}
	
	/*function getProductTypes(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		$query= "SELECT `product_type_id` as id, `product_type_name` as name, `product_type_publish` as pub ".
				"FROM `#__".$vmpref."_product_type` as pt ".
				"WHERE 1 ".
				"ORDER BY `product_type_list_order`";
		$db->setQuery($query);
		$pt=$db->loadObjectList();
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::displayProductTypes($pt);
		
	}*/
	
	function getProductParameters($pid,$ptid,$ptparams,$row){
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printRemovePTDialog();
		fsViewDP::displayProductParameters($pid,$ptid,$ptparams,$row);
	}
	
	function removeProductTypeInfo(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		//sleep(16);
		
		$pid=JRequest::getVar('pid',null);
		$ptid=JRequest::getVar('ptid',null);
		$urlptid=JRequest::getVar('urlptid',null);
		$row=JRequest::getVar('row',null);
		
		if($pid && $ptid){
			$q="DELETE FROM `#__{$vmpref}_product_product_type_xref` WHERE `product_id`=$pid LIMIT 1;";
			$db->setQuery($q);
			if($db->query()){
				$q="DELETE FROM `#__{$vmpref}_product_type_$ptid` WHERE `product_id`=$pid LIMIT 1;";
				$db->setQuery($q);
				if($db->query()){
					if(!$urlptid){
						echo $this->buildAllPTsMenu($row);
					}
				}
			}
		}
	}
	
	function getProductDescription(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		$pid=JRequest::getVar('pid',null);
		$q="SELECT `product_name`, `product_desc`, `product_s_desc` FROM `#__{$vmpref}_product` WHERE `product_id`=$pid;";
		$db->setQuery($q);
		$res=$db->loadObject();
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printProductDescription($res);
	}
	
	function getFilterDialog(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		$pid=JRequest::getVar('pid',null);
		$ptid=JRequest::getVar('ptid',null);
		$parameter_name=JRequest::getVar('paramname',null);
		$q="SELECT `product_name` FROM `#__{$vmpref}_product` WHERE `product_id`=$pid;";
		$db->setQuery($q);
		$name=$db->loadResult();
		$q="SELECT `parameter_label`,`parameter_values` FROM `#__{$vmpref}_product_type_parameter` WHERE `product_type_id`=$ptid AND `parameter_name`='$parameter_name';";
		$db->setQuery($q);
		$res=$db->loadObject();
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printFilterDialog($name,$res);
	}
	
	function getPageNavigation(){
		global $vmpref;
		$db =& JFactory::getDBO();
		
		$keyword=JRequest::getVar('q','');
		$cid=JRequest::getVar('cid','');
		$ptid=JRequest::getVar('ptid','');
		$ppid=JRequest::getVar('ppid',null); // product parent id
		
		$tables="(`#__".$vmpref."_product` as p";
		$joins="";
		$where="";
		if($ppid){
			$where.="AND (p.`product_id`=$ppid OR `product_parent_id`=$ppid) ";
		}
		else if($cid){
			//$tables.=",`#__".$vmpref."_category` as c";
			$joins.="LEFT JOIN `#__".$vmpref."_product_category_xref` as pcx ON p.`product_id`=pcx.`product_id` ";
			//$where.="AND pcx.`category_id`=c.`category_id` AND pcx.`category_id`=$cid ";
			$where.="AND pcx.`category_id`=$cid ";
		}
		if($ptid){
			$joins.="LEFT JOIN `#__".$vmpref."_product_product_type_xref` as ptx ON p.`product_id`=ptx.`product_id` ";
			if($ptid=='wopt'){$where.="AND ptx.`product_type_id` IS NULL ";}
			else{$where.="AND ptx.`product_type_id`=$ptid ";}
		}
		$tables.=")";
		if(!$ppid) $where.="AND `product_parent_id`=0 ";
		
		$query="SELECT COUNT(p.`product_id`) FROM $tables $joins WHERE 1 $where";
		if ($keyword) {
			$sq = "`product_name` LIKE '%$keyword%'";
			if (fsconf::getOption('show_sku')) $sq .= " OR `product_sku` LIKE '%$keyword%'";
			if(is_numeric($keyword))$sq.= " OR p.`product_id`=$keyword";
			$query.=" AND ($sq)";
		}
		if(fsconf::getOption('show_unpublished_products')!='Y') $query.=" AND `product_publish`='Y'";
		$db->setQuery($query);
		$count=$db->loadResult();
		
		//echo $query;
		//echo 'count:'.$count;
		
		require_once( dirname(__FILE__).'/../views/dp.php' );
		fsViewDP::printPageNavigation($count);
	}
	
	function product_has_children($pid){
		global $vmpref;
		$db =& JFactory::getDBO();
		$q="SELECT COUNT(`product_id`) FROM `#__{$vmpref}_product` WHERE `product_parent_id`=$pid;";
		$db->setQuery($q);
		return ($db->loadResult())? true : false;
	}
	
	function displayDP(){
		$this->getSearch();
		$this->getRefinePane();
		echo '<div id="cmid"><div id="cproducts">';
		$this->getProducts();
		echo '</div><div id="cpages">';
		$this->getPageNavigation();
		echo '</div></div>';
	}
	
	function displayDPMB(){
		//sleep(6);
		echo '<div id="cproducts">';
		$this->getProducts();
		echo '</div><div id="cpages">';
		$this->getPageNavigation();
		echo '</div>';
	}
}
<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* Cherry's Breadcrumb Module
*
* @package VM Breadz 1.5 - October 2011
* @copyright Copyright © 2009-2011 Maksym Stefanchuk All rights reserved.
* @license http://www.gnu.org/licenses/gpl.html GNU/GPL
*
* http://www.galt.md
*/
global $mosConfig_absolute_path, $vm_mainframe;
// Load the virtuemart main parse code
if( file_exists(dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' )) {
	require_once( dirname(__FILE__).'/../../components/com_virtuemart/virtuemart_parser.php' );
} else {
	require_once( dirname(__FILE__).'/../components/com_virtuemart/virtuemart_parser.php' );
}

require_once (CLASSPATH."ps_product_category.php");
$ps_product_category = new ps_product_category;

$chp_low_price=vmGet($_REQUEST, "low-price", "");
$chp_high_price=vmGet($_REQUEST, "high-price", "");
$class_file=$mosConfig_absolute_path.'/modules/mod_vm_cherry_picker/controller.php';
if(file_exists($class_file)){
	require_once( $class_file );
	$chp=new chpController();
	$chp_low_price=$chp->validate_price($chp_low_price);
	$chp_high_price=$chp->validate_price($chp_high_price);
}

$_SESSION['breadz_running'] = 'yes';
	
$breadz_style	= $params->get( 'breadz_style', '' );
$pretext		= $params->get( 'pretext', '' );
$showx			= $params->get( 'showx', '1' );
$xtitle			= $params->get( 'xtitle', '' );
$spacer			= $params->get( 'spacer', '' );
$spacer_opt		= $params->get( 'spacer_opt', '' );
$startingpoint	= $params->get( 'startingpoint', '0' );
$pretext_link	= $params->get( 'pretext_link', '0' );
$pretext_url	= $params->get( 'pretext_url', '' );
$short_url=$params->get('short_url',0);
	
if ($spacer == 0) {$spacer = '&#8250;';}
elseif ($spacer == 1) {$spacer = '&raquo;';}
elseif ($spacer == 2) {$spacer = $spacer_opt;}
	

$page= vmGet( $_REQUEST, 'page', '' );
$category_id= vmGet( $_REQUEST, 'category_id', '' );
if ( $category_id == 0 && isset($_GET['category_id']) ) {
	$category_id = $_GET['category_id'];
}
$product_id = intval( vmGet($_REQUEST, "product_id", null) );
$tmp_product_type_id = vmGet( $_REQUEST, 'product_type_id', '' );
$Itemid = JRequest::getInt( 'Itemid', null );
//echo 'category_id = '.$category_id;
	
//$sess = new ps_session;

$doc =& JFactory::getDocument();
$doc->addStyleSheet( 'modules/mod_vm_breadz/css/style.css' );

$db_product = new ps_DB;

$category_list='';
$pathway= array();
$product_type_id='';
$ppt='';
	
if (!empty($category_id)){
	$category_list = array_reverse( $ps_product_category->get_navigation_list($category_id) );
	$count = count($category_list);
	
	for ($i = $count-1; $i >= 0; --$i) {
		for ($j = $i-1; $j >= 0; --$j) {
			if ($category_list[$j]["category_id"] == $category_list[$i]["category_id"]) {
				unset($category_list[$i]);
				break;
			}
		}
	}
	
	$pathway = $ps_product_category->getPathway( $category_list );

	
	// GET THE PRODUCT NAME TO SHOW ON PRODUCT DETAIL PAGE
	$product_name ='';
	if (!empty($product_id)) {
		$q = "SELECT `product_name` FROM `#__{vm}_product` WHERE `product_id` = '$product_id'";
		$db_product->query( $q );
		// GET THE PRODUCT NAME 
		$product_name = shopMakeHtmlSafe($db_product->f("product_name"));
	}
	
    //  ------------- add filters to pathway ----------- 
		$dbf = new ps_DB;
		
        if ( empty($tmp_product_type_id) ) {  // if product_type_id in a link - we do not make query
            if (!empty($category_id)) {
    			
                $q = "SELECT `#__{vm}_product_product_type_xref`.`product_type_id`".
                     " FROM (`#__{vm}_product_product_type_xref`, `#__{vm}_product`)".
                     " LEFT JOIN `#__{vm}_product_category_xref`".
                     " ON `#__{vm}_product_product_type_xref`.`product_id`=`#__{vm}_product_category_xref`.`product_id`".
                     " WHERE `#__{vm}_product_category_xref`.`category_id`='$category_id'".
                     " AND `#__{vm}_product`.`product_id`=`#__{vm}_product_category_xref`.`product_id`".
                     " AND `#__{vm}_product`.`product_publish`='Y'";
                    		
    			if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
    				$q .= ' AND `#__{vm}_product`.`product_in_stock` > 0';
    			}
                
                $q .= " LIMIT 0 , 1";
    			 		 						
    			$dbf->setQuery($q);
    			$ppt = $dbf->loadObjectList();
    		}
    	
    		if (!empty($ppt)) {
    			$product_type_id = $ppt[0]->product_type_id;
    		} 
        }
        else {   // if we go to Advanced Product Parameter Search -- we may still use product_type_id
            $product_type_id = $tmp_product_type_id;
        }
        
        
		
		//echo '$product_type_id = '.$pti;
		//echo '$category_id = '.$category_id;
		$parameter='';
		$param_count=0;
		if (!empty($product_type_id)) {
			$q  = "SELECT * FROM `#__{vm}_product_type_parameter` WHERE `product_type_id` = '$product_type_id' ORDER BY `parameter_list_order`";

			$dbf->setQuery($q);
			$parameter = $dbf->loadObjectList();
			
			// if we are not in a category, we'll use Product Type Name in pathway
			if ( empty($category_id) ) {
				$q = "SELECT `product_type_name` FROM `#__{vm}_product_type` WHERE `product_type_id` = '$product_type_id'";	
				$dbf->query($q);
				$prodcut_type_name = $dbf->f("product_type_name");
			}
			
			$filter = array(); $filterurl = array();
	
			$param_count=count($parameter);
			$compact_url=false;
			// get all activated filters
			for ($i=0; $i<$param_count; $i++) {
				$tmp_url = "product_type_".$product_type_id."_".$parameter[$i]->parameter_name;
				$get=vmGet($_REQUEST, $tmp_url, "");
				if(!$get){
					$get=JRequest::getVar($parameter[$i]->parameter_name,null);
					if($get) $compact_url=true;
				}
				$filter[$i]=$get;
				$filter_comp[$i] = vmGet($_REQUEST, $tmp_url."_comp", "");
                
                // GO FROM  Children\'s  =>to   Children's
                if ( is_array($filter[$i]) ) {
                    $filter_i_count = count($filter[$i]);
                    for ($j=0; $j<$filter_i_count; ++$j) {
                        $filter[$i][$j] = str_replace('\\', '', $filter[$i][$j]);
                    }
    			}
    			else {
                    $filter[$i] = str_replace('\\', '', $filter[$i]);;
    			}
			}
	
			// any filter has it's url. lets fill them once and for all
			for ($i=0;$i<$param_count; ++$i) {
				if($short_url || $compact_url){
					$filterurl[$i]='';
					$filter_paramurl[$i]='&'.$parameter[$i]->parameter_name.'=';
					continue;
				}
				
				if ($parameter[$i]->parameter_type != "V" && $parameter[$i]->parameter_multiselect == "Y") {
					$filterurl[$i] 			= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'_comp=in';
					$filter_paramurl[$i] 	= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'[]=';
			
				} elseif ($parameter[$i]->parameter_type == "V" && $parameter[$i]->parameter_multiselect == "N") {
					$filterurl[$i] 			= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'_comp=find_in_set';
					$filter_paramurl[$i] 	= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'=';
			
				} elseif ($parameter[$i]->parameter_type == "V" && $parameter[$i]->parameter_multiselect == "Y") {
					if ( !empty($filter_comp[$i]) ) { $comp = $filter_comp[$i]; } else { $comp = 'find_in_set_any'; }
					$filterurl[$i] 			= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'_comp='.$comp.'';
					$filter_paramurl[$i] 	= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'[]=';
			
				} else {
					$filterurl[$i] 			= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'_comp=texteq';
					$filter_paramurl[$i] 	= '&product_type_'.$product_type_id.'_'.$parameter[$i]->parameter_name.'=';
				}
			}
			
			//print_r($filter);print_r($filterurl);
			
            if ( !empty($category_id) ) {
				$baseurl = 'index.php?option=com_virtuemart&page=shop.browse&Itemid='.$Itemid.'&category_id='.$category_id.'';
			} else {
				$baseurl = 'index.php?option=com_virtuemart&page=shop.browse&Itemid='.$Itemid.'&category_id=';
			}
			
		}
        
	
		$fpath = array(); // filter pathway to be added to vm pathway
		if (isset($_SESSION['filters'])) {
			$fpath = $_SESSION['filters'];
		}
		
		if (count($fpath)>0) {for ($i=0; $i < count($fpath); ++$i) {$fpath[$i]["bool"] = false;} } // at a beginning, mark all items in array as false, to see later if it is still in use
		
		for ($i=0; $i<$param_count; ++$i) {
				if (!empty($filter[$i])) {
			
					
					$flag = false;  //check if this filter already in array
					for ($j=0; $j < count($fpath); ++$j) {
						if ($fpath[$j]["name"] == $filter[$i]) {$flag = true; $fpath[$j]["bool"] = true; break; }
					}
					
					if ($flag == false) {
						$k = count($fpath);
						$fpath[$k]["name"] = $filter[$i];
						$fpath[$k]["comp"] = $filter_comp[$i]; 
						//$fpath[$k]["link"] = $url;
						$fpath[$k]["field"] = $parameter[$i]->parameter_name;
						$fpath[$k]["label"] = $parameter[$i]->parameter_label;
						$fpath[$k]["unit"]  = $parameter[$i]->parameter_unit;
						$fpath[$k]["product_type_id"] = $product_type_id; 
						$fpath[$k]["bool"] = true;
						$fpath[$k]["filter"]= $i;
						//echo '$filter[$i] = '.$filter[$i]; echo 'k= '.$k;
					}
					
					
				}
		
		}
		
		$fcount = count($fpath);
		
		//if (count($fpath)>0) {
		for ($i=0; $i < $fcount; ++$i) {
			if ($fpath[$i]["bool"] == false) { unset($fpath[$i]); }
		}
	//	}
		
		if (!empty($fpath)) {
			$b = array_values($fpath);
			$fpath = $b;
		}
		/*
		echo '<pre>';			
		print_r($filter);
		echo '</pre>';
		
		echo '<pre>';			
		print_r($filterurl);
		echo '</pre>';
		*/
		//echo 'count($fpath) = '.count($fpath);
		//echo count($parameter);
		
		// FORM LINKS FOR FILTERS
		$url = '';
		for ($i=0; $i < count($fpath); $i++) {
			$url .= $filterurl[$fpath[$i]["filter"]];
			
			if ( is_array($filter[$fpath[$i]["filter"]]) ) {
				foreach ( $filter[$fpath[$i]["filter"]] as $value) {
					$url .= $filter_paramurl[$fpath[$i]["filter"]].urlencode($value);
				}
			}
			else {
				$url .= $filter_paramurl[$fpath[$i]["filter"]].urlencode($filter[$fpath[$i]["filter"]]);
			}
			
			$fpath[$i]["link"] = $baseurl.'&product_type_id='.$product_type_id.$url.'&limitstart=0';
			if(!empty($chp_low_price) || !empty($chp_high_price)){
				$fpath[$i]["link"].='&low-price='.$chp_low_price.'&high-price='.$chp_high_price;
			}
		}
		
		// FORM CLOSE(x) LINKS
		for ($i=0; $i < count($fpath); $i++) {
			$url = '';
			for ($j=0; $j < count($fpath); $j++) {
				if ($j != $i ){
					$url .= $filterurl[$fpath[$j]["filter"]];
					
					if ( is_array($filter[$fpath[$j]["filter"]]) ) {
						foreach ( $filter[$fpath[$j]["filter"]] as $value) {
							$url .= $filter_paramurl[$fpath[$j]["filter"]].urlencode($value);
						}
					}
					else {
						$url .= $filter_paramurl[$fpath[$j]["filter"]].urlencode($filter[$fpath[$j]["filter"]]);
					}
				}
			}
			
			if (!empty($url)) { $fpath[$i]["xlink"] = $baseurl.'&product_type_id='.$product_type_id.$url.'&limitstart=0'; }
			elseif (!empty($category_id)) {
				$fpath[$i]["xlink"] = $baseurl.'&limitstart=0';
			}
			else { 
				$fpath[$i]["xlink"] = $baseurl.'&product_type_id='.$product_type_id.'&limitstart=0'; 
			}
			
			if(!empty($chp_low_price) || !empty($chp_high_price)){
				$fpath[$i]["xlink"].='&low-price='.$chp_low_price.'&high-price='.$chp_high_price;
			}
		}
		
		//echo '<pre>';			
		//print_r($fpath);
		//echo '</pre>';
		
		if (!empty($fpath)) { $_SESSION['filters'] = $fpath; }
		
		//if (($page != 'shop.product_details') and ($page != 'shop.ask')) {	$_SESSION['flypath'] = $fpath; }
		if ( $page == 'shop.browse' ) {	$_SESSION['flypath'] = $fpath; }
		
    /*  -------------  END OF FILTERING ----------------- */
	
	
	
	$fcount = count($fpath); // filters to be used on Browsepage
	$count = count( $pathway );
	
	//$fp = array();
	//if (isset($_SESSION['filteredproducts'])) $fp = $_SESSION['filteredproducts'];
	
	$product_in_range = true;
	/*
	for ($i=0; $i < count($fp); ++$i) {
		if ($fp[$i]->product_id == $product_id) {
			$product_in_range = true;
			break;
		}
	}
	*/
	
	// show filters only to products that were selected
	$flypath = array(); // filters to be used on flypage
	if ( $product_in_range ) { $flypath = $_SESSION['flypath']; }
	$flycount = count($flypath);
	
	
	$catcount = 1;
				
	if ($fcount > 0) {
		$fpath[$fcount - 1]["link"] = '';
	}
	elseif ($flycount > 0) {}
	else {
		// Remove the link on the last pathway item
		if (($page != 'shop.product_details') and ($page != 'shop.ask')) {
			$pathway[ $count - 1 ]->link = '';
		}
	}
		
		//echo '$count = '.$count."<br />"; 
		//echo '$fcount = '.$fcount."<br />";
		//echo '$flycount = '.$flycount."<br />";
		//echo '<pre>';			
		//print_r($pathway);
		//echo '</pre>'; 
		//echo '<pre>';			
		//print_r($fpath);
		//echo '</pre>';
		//echo '<pre>';			
		//print_r($flypath);
		//echo '</pre>';
		
	//	$chp_low_price=vmGet($_REQUEST, "low-price", "");
	//	$chp_high_price=vmGet($_REQUEST, "high-price", "");
		if($chp_low_price || $chp_high_price){
			$clearPriceUrl='';
			if($filter){
				foreach($filter as $i => $f){
					if($f){
						$clearPriceUrl.=$filterurl[$i];
						if(is_array($f)){
							foreach($f as $v){
								$clearPriceUrl.=$filter_paramurl[$i].urlencode($v);
							}
						}else{
							$clearPriceUrl.=$filter_paramurl[$i].urlencode($f);
						}
					}
					
				}
			}
			$clearPriceUrl=$baseurl.'&product_type_id='.$product_type_id.$clearPriceUrl.'&limitstart=0';
			//echo $clearPriceUrl;
		}
		
        
		$startDiv=false;
		if ( ($page == 'shop.product_details') or ($count > $startingpoint) or ($fcount > 0) or ( !empty($pretext) && !empty($product_type_id) ) ) {		
			echo '<div id="breadcrumb">';
			$startDiv=true;
			// if we are in a category or product detail page
			if ( !empty($category_id) ) {
				if (!empty($pretext) && $pathway[0]->name != '') {
					if ($pretext_link) {
						echo '<a href="'.$pretext_url.'">'.$pretext.'</a>'; echo ' '.'<span class="spacer"> '.$spacer.' </span>';	
					}
					else { echo '<span class="pretext">'.$pretext.'</span>'; }
				}
			}
			// if we are on a Advanced Product Parameter Search
			else {
				if ( !empty($pretext) ) {
					if ($pretext_link) {
						echo '<a href="'.$pretext_url.'">'.$pretext.'</a>'; echo ' '.'<span class="spacer"> '.$spacer.' </span>';	
					}
					else { echo '<span class="pretext">'.$pretext.'</span>'; }
				}
			}
			
			// SHOW THE PATHWAY THAT FORMED OF CATEGORY TREE
			if ( $count > 0 ) {
			foreach( $pathway as $item ) { 
				if( !empty( $item->link ) ) { 
					echo '<a href="' .$item->link. '" title="'. $item->name .'">'.$item->name.'</a>';
				} 
				else {
					echo '<span class="currentpos">'.$item->name.'</span>'; 
				}

				if( $catcount < $count || $item->link != '') {
					// This prints the separator image (uses the one from the template if available!)
					// Cat1 * Cat2 * ...
					if (!empty($pathway[0]->name)) {
					echo '<span class="spacer"> '.$spacer.' </span>'; }
				}
				$catcount++;
			}
			}
		}	
		
		if ( !empty($category_id) && empty($flypath) && $pathway[0]->name != '') { echo '<span class="currentpos">'.' '.$product_name.'</span>'; }
		
		// SHOW FILTERS WHEN ON PRODUCT DETAIL PAGE
		if (($page == 'shop.product_details' or $page == 'shop.ask') && !empty($category_id)) {
			if (!empty($flypath)) {
					foreach ( $flypath as $item ) { 
						
						$tmp_filter = '';
						if ( $breadz_style == 1 ) { $tmp_filter = $item["label"].': '; }
						
						// if filter is multivalue and has number of values: eg. Color: Black, Blue, White
						if ( is_array($item["name"]) && count($item["name"]) > 1) {
							$unit = $item["unit"];
							$tmp_filter .= implode("$unit, ", $item["name"]);
							$tmp_filter .= $unit;
						}
						elseif ( is_array($item["name"]) ) {
							$tmp_filter .= $item["name"][0].''.$item["unit"];
						}
						else {
							$tmp_filter .= $item["name"].''.$item["unit"];
						}
						
						// CREATE LINK
						echo '<a href="'. $item["link"] .'" title="'. $tmp_filter .'">'. $tmp_filter .'</a>';
						
						echo '<span class="spacer"> '.$spacer.' </span>';
					} 
					echo '<span class="currentpos">'.' '.$product_name.'</span>';
			}	
		}
		// SHOW FILTERS WHEN ON BRWOSEPAGE
		else {		
			if ( ( ( empty($category_id) && !empty($pretext) ) || !empty($fpath) ) && !empty($prodcut_type_name)) {
				if ( empty($fpath) ) {
					echo '<span class="currentpos">'.' '.$prodcut_type_name.'</span>';
				}
				else {
					$href = 'index.php?option=com_virtuemart&page=shop.browse&category_id=&product_type_id='. $product_type_id .'&Itemid='.$Itemid.'&limitstart=0';	
					echo '<a href="'.$href.'"> '. $prodcut_type_name .'</a> <span class="spacer">'.$spacer.' </span>';
				}
			}
			
			if (!empty($fpath)) {
				foreach ( $fpath as $item ) {
					
					$tmp_filter = '';
					if ( $breadz_style == 1 ) { $tmp_filter = $item["label"].': '; }
					
					// if filter is multivalue and has number of values: eg. Color: Black, Blue, White
					if ( is_array($item["name"]) && count($item["name"]) > 1) {
						$unit = $item["unit"];
						$tmp_filter .= implode("$unit, ", $item["name"]);
						$tmp_filter .= $unit;
					}
					elseif ( is_array($item["name"]) ) {
						$tmp_filter .= $item["name"][0].''.$item["unit"];
					}
					else {
						$tmp_filter .= $item["name"].''.$item["unit"];
					}	
					
					// CREATE LINK
					if( !empty( $item["link"] ) ) {
						echo '<a href="'. $item["link"] .'" title="'. $tmp_filter .'">'. $tmp_filter .'</a>';
				 		if ($showx) { 
							echo '<sup class="xlink">(<a class="xlinka" href="'. $item["xlink"] .'" title="'. $xtitle.' '. $tmp_filter .'">x</a>)</sup>';
				 		}
					} 
					else {
						echo '<span class="currentpos">'. $tmp_filter .'</span>';
				 		if ($showx) { 
							echo '<sup class="xlink">(<a class="xlinka" href="'. $item["xlink"] .'" title="'. $xtitle.' '. $tmp_filter .'">x</a>)</sup>';
				 		}
					}

					// and don't forget spacer
					if( $item["link"] != '') { echo '<span class="spacer"> '.$spacer.' </span>'; }
			
				}
			}
	
		}

		if($chp_low_price || $chp_high_price){
			echo '<span class="spacer"> '.$spacer.' </span>';
			//echo 'low: '.$chp_low_price;
			//echo ' high: '.$chp_high_price;
			
			// we have only low-price
			if($chp_low_price && !$chp_high_price){
				echo "Price: \$$chp_low_price & Above";
			}
			// we have only high-price
			else if(!$chp_low_price && $chp_high_price){
				echo "Price: \$$chp_high_price & Under";
			}
			// else, both
			else{
				echo "Price: \$$chp_low_price - \$$chp_high_price";
			}
			echo '<sup class="xlink">(<a href="'.$clearPriceUrl.'" class="xlinka">x</a>)</sup>';
		}
		
		if($startDiv){echo '</div>';}
}
?>
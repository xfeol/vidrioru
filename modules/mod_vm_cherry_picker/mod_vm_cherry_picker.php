<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/**
* Cherry Picker - Product Filter Module
*
* @package Cherry Picker 2.0.4 - January 2012
* @copyright Copyright © 2009-2011 Maksym Stefanchuk All rights reserved.
* @license http://www.gnu.org/licenses/gpl.html GNU/GPL
*
* http://www.galt.md
*/
//$debti=1;
$debti=(JRequest::getVar('chp','')=='showtime')? 1 : 0;
if($debti) $time_start=microtime(true);

$options=array();
$displayonflypage = $params->get( 'displayonflypage','');
$page=JRequest::getVar('page', '');
$url_ptid=JRequest::getVar('product_type_id','');

//$use_custom_pti=$params->get('use_custom_pti', '');
$options['custom_ptid']=$custom_ptid=$params->get('custom_product_type_id','');

//if ( !($page == 'shop.product_details' && !$displayonflypage) ) {
if ( (!($page != 'shop.browse' && !$displayonflypage) or !empty($url_ptid)) or (!(($page == 'shop.product_details' || $page == 'shop.ask') && !$displayonflypage) && $custom_ptid) ) {	
	
	//require_once( $mosConfig_absolute_path.'/modules/mod_vm_cherry_picker_wereld/chp.class.php' );
	require_once('writer.php');
	require_once('conf.php');
	require_once('controller.php');
	$chp=new chpController();

	// $db =& JFactory::getDBO();
	// $db->debug(1);
	// $start_memory=memory_get_usage();
	
	$chp->apprehendPTID($custom_ptid);
	if($chp->ptid()){
		$options['mode']=$params->get('mode',0);
		$options['type']=$params->get('type',0);
		$options['titletype']=$params->get('titletype',0);
		$options['statictitle']=$params->get('statictitle','');
		$options['dynamictitle']=$params->get('dynamictitle','');
		$options['showclearlink']=$params->get('showclearlink',1);
		$options['clear']=$params->get('clear','');
		$options['backlink']=$params->get('backlink','');
		$options['collapsehead']=$params->get('collapsehead',0);
		$options['default_collapsed']=$params->get('default_collapsed',0);
		$options['useseemore']=$params->get('useseemore',0);
		$options['use_seemore_ajax']=$params->get('use_seemore_ajax',false);
		$options['b4seemore']=$params->get('b4seemore',2);
		$options['smanchor']=$params->get('smanchor',0);
		$options['seemore']=$params->get('seemore','');
		$options['seeless']=$params->get('seeless','');
		$options['fadein']=$params->get('fadein',1);
		$options['usesmartsearch']=$params->get('usesmartsearch',1);
		$options['fill_metatitle']=$params->get('fill_metatitle',1);
		$show_total_products=$params->get('show_total_products',1);
		$options['pretext_totalproducts']=$params->get('pretext_totalproducts','');
		$options['add_tb_js']=false; // check later whether to add trackbar.js to <head>
		$search_by_price=$params->get('search_by_price',1);
		if($search_by_price){
			$sbp_position=$params->get('price_position',0);
			$options['pricetitle']=$params->get('pricetitle','');
			$options['showtrackbar']=$params->get('showtrackbar',0);
			$options['price_from']=$params->get('price_from','');
			$options['price_to']=$params->get('price_to','');
			$options['leftlimitauto']=$params->get('leftlimitauto',0);
			$options['rightlimitauto']=$params->get('rightlimitauto',1);
			$options['leftlimit']=$params->get('leftlimit',0);
			$options['rightlimit']=$params->get('rightlimit',100);
			if($options['showtrackbar']) $options['add_tb_js']=true;
		}
		$options['tb_lbl_type']=$params->get('tb_lbl_type',1);
		$options['tb_from']=$params->get('tb_from','');
		$options['tb_to']=$params->get('tb_to','');
		$options['tb_all']=$params->get('tb_all','');
		$options['tb_apply']=$params->get('tb_apply','');
		$options['include_tax']=$params->get('include_tax',0);
		$options['tax']=$params->get('tax',1);
		$usecache=$params->get('usecache',0);
		$options['show_specific_params']=$params->get('show_specific_params',0);
		$options['short_url']=$params->get('short_url',false);
		$options['showfiltercount']=$params->get('showfiltercount',1);
		$options['hide_params_with1filter']=$params->get('hide_params_with1filter',false);
		$options['translate']=$params->get('translate',false);
		$options['filters_in_column']=$params->get('filters_in_column',0);
		$options['load_filters_ajax']=$params->get('load_filters_ajax',false);
		$options['remove_empty_params']=$params->get('remove_empty_params',false);
		$options['empty_params_msg']=$params->get('empty_params_msg','');
		$options['module_id']=$module->id;
		$options['module_path']=JURI::base().'modules/mod_vm_cherry_picker/';
		$options['ptid']=$chp->ptid();
		chpconf::setOptions($options);
		
		define("NUM_PROD_SHOW", 1);
		define("NUM_PROD_NOT_CALC", 2);
		
		
		$parameters=$chp->getParameters();
		if($parameters){
			$chp->addStyleSheet();
			
			// order of actions must be proper
			$chp->apprehendPrices();
			$chp->apprehendBaseQuery();
			$chp->getAppliedFilters();
			$chp->setBaseUrl();
			if($usecache)$chp->checkCache();
			$chp->getTitle();
			//$chp->blockStart();
			chpWriter::writeBlockStart();
			$chp->formStart();
			
			
			if($search_by_price && $sbp_position==0)$chp->getSearchByPrice();
			foreach($parameters as $i => $p){
				$f=null;
				$chp->setCurrParameterIndex($i);
				chpWriter::setCurrParameterIndex($i);
				// Parameter is a Trackbar
				$mode=(isset($p['mode']))?$p['mode']:null;
				if($mode==1 || $mode==2){
					$f=$chp->getTrackbarParameter();
					chpWriter::writeParameter($f);
					chpconf::set('add_tb_js',true);
					continue;
				}

				if($options['mode']==0 && $chp->parameter_applied($i)){
					$f=$chp->getBackLink();
				}else if(!($options['type']==2 && $options['load_filters_ajax']==true)){
					$f=$chp->getFilters();
				}
				
				if($f){ chpWriter::writeParameter($f);}
				// workaround for drop-down list layout with dynamic ajax load of filters
				else if($options['type']==2 && $options['load_filters_ajax']==true){
					chpWriter::setParamHasAppliedFilter($chp->parameter_applied($i));
					chpWriter::writeParameter(chpWriter::writeLoading());
				}
			}
			if($search_by_price && $sbp_position==1)$chp->getSearchByPrice();
			if($show_total_products) $chp->getTotalProducts();
			chpWriter::writeFormEnd();
			chpWriter::writeBlockEnd();
			
			if($options['fill_metatitle']) $chp->setPageTitle();
			
			// accompany with javascript
			chpWriter::addScript();
			// unccomment to make ChP self update with Ajax
			//chpWriter::addScriptForSelffUpdating();
		}
	}
	
	if($debti){
		$time_end=microtime(true);
		$elapsed=$time_end-$time_start;
		echo '<br />Elapsed:'.$elapsed.'<br />Usecache:'.@$usecache;
	}
	
	/* /
	echo '<pre>';
	echo "Memory peak usage: ".memory_get_peak_usage() . "<br>\n";
	$end_memory=memory_get_usage();
	echo "<br /><br />Memory usage:".($end_memory-$start_memory);
	echo '<br /><br />Queries made:'.$db->getTicker();
	print_r($db->getLog());
	echo '</pre>';
	/* */
} //die();
?>
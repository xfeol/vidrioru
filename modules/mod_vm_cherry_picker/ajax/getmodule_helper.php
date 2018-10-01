<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

header('Content-type: text/html; charset=utf-8');

//$ptid=JRequest::getVar('product_type_id', 0);
//if($ptid){
//	$db =& JFactory::getDBO(); // set database to debug, to calculate the number of queries made
//	$db->debug(1);
//	$time_start=microtime(true); //sleep(10);
//	ini_set('display_errors',1);
//	error_reporting(E_ALL);

	require_once('../writer.php');
	require_once('../conf.php');
	require_once('../controller.php');
	$chp = new chpController();
	
	$module_id = JRequest::getVar('mid',null);
	if($module_id) {chpconf::getOptionsFromDB($module_id);}
	else{ die('No module'); }
	
	$uri =& JFactory::getURI();
	//$domain=$uri->_scheme.'://'.$uri->_host;
	$domain = $uri->getScheme().'://'.$uri->getHost();
	
	// we need to override some options
	chpconf::set('module_path', $domain.'/modules/mod_vm_cherry_picker/');
	chpconf::set('module_id', $module_id);
	define('CHECK_STOCK', 0);
	
	define("NUM_PROD_SHOW", 1);
	define("NUM_PROD_NOT_CALC", 2);
	
	$chp->apprehendPTID($ptid);
	$parameters=$chp->getParameters();
	
	//$paindex=JRequest::getVar('paindex',0);
	// order of actions must be proper
	$chp->apprehendPrices();
	$chp->apprehendBaseQuery();
	$chp->getAppliedFilters();
	$chp->setBaseUrl();
	if(chpconf::option('usecache')) $chp->checkCache();
	
	if ($chp->refinement_applied()) { // refinment could be anything: filter, price
		$chp->get_show_results();
	}
	
	
	$chp->formStart();
	
	if(chpconf::option('search_by_price') && chpconf::option('price_position')==0)$chp->getSearchByPrice();
	foreach($parameters as $i => $p){
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
		
		if(chpconf::option('mode')==0 && $chp->parameter_applied($i)){
			$f=$chp->getBackLink();
		}else{
			$f=$chp->getFilters();
		}
		if($f) chpWriter::writeParameter($f);
	}
	if(chpconf::option('search_by_price') && chpconf::option('price_position')==1)$chp->getSearchByPrice();
	if(chpconf::option('show_total_products')) $chp->getTotalProducts();
	
	chpWriter::writeFormEnd();
	chpWriter::addScript();
		
	/* /
	echo '<br />Queries made:'.$db->getTicker();
	echo '<pre>';
	print_r($db->getLog());
	$time_end=microtime(true);
		$elapsed=$time_end-$time_start;
		echo '<br />Elapsed:'.$elapsed.'<br />';
	echo '</pre>';
	/* */
//}
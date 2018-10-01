<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

header('Content-type: text/html; charset=utf-8');

$ptid=JRequest::getVar('product_type_id',0);
if($ptid){
//	$db =& JFactory::getDBO(); // set database to debug, to calculate the number of queries made
//	$db->debug(1);
//	$time_start=microtime(true); //sleep(10);
//	ini_set('display_errors',1);
//	error_reporting(E_ALL);
	
	require_once('../writer.php');
	require_once('../conf.php');
	require_once('../controller.php');
	$chp=new chpController();
	
	$module_id=JRequest::getVar('mid',null);
	if($module_id){chpconf::getOptionsFromDB($module_id);}
	else{ die('No module'); }
	
	$offset=JRequest::getVar('offset',null);
	//$offset=chpconf::option('b4seemore');
	// we need to override some options
	chpconf::set('custom_ptid',null);
	//if($offset!=0) 
	chpconf::set('useseemore',false);
	chpconf::set('module_path',JURI::base().'modules/mod_vm_cherry_picker/');
	define('CHECK_STOCK',0);
	
	define("NUM_PROD_SHOW", 1);
	define("NUM_PROD_NOT_CALC", 2);
	
	$chp->apprehendPTID($ptid);
	$parameters=$chp->getParameters();
	
	$paindex=JRequest::getVar('paindex',0);
	// order of actions must be proper
	$chp->apprehendPrices();
	$chp->apprehendBaseQuery();
	$chp->getAppliedFilters();
	$chp->setBaseUrl();
	if(chpconf::option('usecache')) $chp->checkCache();
	$chp->setCurrParameterIndex($paindex);
	chpWriter::setCurrParameterIndex($paindex);
	
	$f=$chp->getFilters($offset);
	if($f){
		if(chpconf::option('type')==2){
			echo chpWriter::wrap_html_list2_ajax($f);
		}
		else{
			echo $f;
		}
	}
	
	
	/* /
	echo '<br />Queries made:'.$db->getTicker();
	echo '<pre>';
	print_r($db->getLog());
	$time_end=microtime(true);
		$elapsed=$time_end-$time_start;
		echo '<br />Elapsed:'.$elapsed.'<br />';
	echo '</pre>';
	/* */
}
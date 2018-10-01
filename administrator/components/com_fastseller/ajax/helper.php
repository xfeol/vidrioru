<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
header('Content-type: text/html; charset=utf-8');
header('Expires: -1');

define('FSVERSION', '1.0.8');
define("PARAM_IS_COLOR", 3);

//$dd=JFactory::getDBO(); // set database to debug, to calculate the number of queries made
//$dd->debug(1);

$uri=JFactory::getURI();
$site=$uri->getScheme().'://'.$uri->getHost();

// Access this content only from Administrator area, when logged in.
$user= & JFactory::getUser();
if($user->id==0){
	$mainframe->redirect( $site, '' );
}

$base = JURI::base();
$correct_path = strpos($base, 'com_fastseller');
if ($correct_path === false) {
	$base .= 'components/com_fastseller/ajax/';
}

$GLOBALS['vmpref']='vm';
//$GLOBALS['fajax']=$site.'/administrator/components/com_fastseller/ajax/ajax.php';
$GLOBALS['fajax'] = $base .'ajax.php';
$GLOBALS['comp_path'] = $comp_path = dirname(dirname(__FILE__)).'/';
//$GLOBALS['comp_url']=$site.'/administrator/components/com_fastseller/';
$GLOBALS['comp_url'] = dirname($base).'/';

$index=JRequest::getCmd('i', '');
if(empty($index)) $index='HOME';

// Configuration class is handled separately below
if( $index != 'CONF' ){
	require_once( $comp_path . 'controllers/'. strtolower($index) .'.php' );
	$className='fsController'.$index;
	$do=new $className();
}

require( $comp_path . 'controllers/conf.php');
fsconf::getConfiguration();


$a=JRequest::getCmd('a', '');

switch($index){
	case 'HOME': $do->printFrontPage(); break;
	case 'DP':
		switch($a){
			default: $do->displayDP(); break;
			case 'UPMB':	$do->displayDPMB(); break;
			case 'UPM' :	$do->getProducts(); break;
			case 'UPCAT':	$do->getCatTree(); break;
			case 'UPPT':	$do->buildPTTreeMenu(); break;
			case 'UPPD':	$do->getProductDescription(); break;
			case 'UPF' :	$do->getFilterDialog(); break;
			case 'UPPTD':	$do->getPTSelectDialog(); break;
			case 'SAVEP':	$do->saveProduct(); break;
			case 'RMPT':	$do->removeProductTypeInfo(); break;
		}
	break;
	case 'PT':
		switch($a){
			default:
				$ptid=JRequest::getVar('ptid', null);
				if($ptid){
					$do->getManageParametersPage();
				}else{
					$do->getProductTypes();
				}
			break;
			case 'SAVEPT': $do->saveProductType(); break;
			case 'REMPT': $do->removeProductType(); break;
			case 'PFORM': $do->getParameterForm(); break;
			case 'REMPAR': $do->removeParameter(); break;
			case 'SAVEPARAMS': $do->saveParameters(); break;
			case 'INCR': $do->increaseParameterValuesColumnSize(); break;
		}
	break;
	case 'CONF': 
		if($a=='SAVEC'){fsconf::saveConfiguration();}
		else {fsconf::getConfigurationPage();}
	break;
}

/* /
echo '<br />Queries made:'.$dd->getTicker();
echo '<pre>';
print_r($dd->getLog());
echo '</pre>';
/* */
?>
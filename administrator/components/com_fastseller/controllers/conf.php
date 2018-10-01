<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsconf{
	protected static $options=array();
	
	static function getConfiguration(){
		require('../configuration.php');
		self::$options=$confopt;
	}
	
	static function getOption($name){
		return self::$options[$name];
	}

	static function setOption($name,$value){
		self::$options[$name]=$value;
	}
	
	static function printAllOptions(){
		print_r(self::$options);
	}
	
	static function getConfigurationPage(){
		require_once( dirname(__FILE__).'/../views/conf.php' );
		fsViewCONF::showConfigurationPage();
	}
	
	static function saveConfiguration(){
		$s="<?php defined('_JEXEC') or die('Restricted access');\n";
		foreach(self::$options as $i => $v) {
			$value = JRequest::getVar($i, '0');
			$s.="\$confopt['$i']='". $value ."';\n";
		}
		$s.="?>";
		$conffile='../configuration.php';
		$handle=fopen($conffile,"w");
		fwrite($handle,$s);
		fclose($handle);
	}
}
?>
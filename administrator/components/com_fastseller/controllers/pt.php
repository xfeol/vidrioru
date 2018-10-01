<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsControllerPT{
	
	public function getProductTypes(){
		$db=& JFactory::getDBO();
		$q="SELECT * FROM `#__vm_product_type`";
		$db->setQuery($q);
		$d=$db->loadAssocList();
		
		if($d) {
			foreach($d as $i => $v){
				$q="SELECT COUNT(*) FROM `#__vm_product_type_parameter` WHERE `product_type_id`={$v['product_type_id']}";
				$db->setQuery($q);
				$res=$db->loadResult();
				$d[$i]['count']=$res;
			}
		}
		
		require_once( dirname(__FILE__).'/../views/pt.php' );
		fsViewPT::printProductTypes($d);
	}
	
	public function saveProductType(){
		$name=JRequest::getVar('name', null);
		$desc=$_GET['desc'];
		$ptid=JRequest::getVar('ptid', null);
		$db=& JFactory::getDBO();
		if($ptid){ // update	
			// $q="UPDATE `#__vm_product_type` SET `product_type_name`='".mysql_real_escape_string($name)."', `product_type_description`='".mysql_real_escape_string($desc)."' WHERE `product_type_id`=$ptid";
			$q="UPDATE `#__vm_product_type` SET `product_type_name`=".$db->quote($name).", `product_type_description`=".$db->quote($desc)." WHERE `product_type_id`=$ptid";
			
			$db->setQuery($q);
			if($db->query()){
				echo $ptid;
			}else{
				echo '0';
			}
		}else{ // insert new record
			// $q="INSERT INTO `#__vm_product_type` VALUES('','".mysql_real_escape_string($name)."','".mysql_real_escape_string($desc)."','Y', '', '', '')";
			$q="INSERT INTO `#__vm_product_type` VALUES('',".$db->quote($name).",".$db->quote($desc).",'Y', '', '', '')";
			$db->setQuery($q);
			if($db->query()){
				$id=$db->insertid();
				$q="CREATE TABLE IF NOT EXISTS `#__vm_product_type_$id` ( 
`product_id` int(11) NOT NULL, 
PRIMARY KEY (`product_id`) ) 
ENGINE=MyISAM DEFAULT CHARSET=utf8";
				$db->setQuery($q);
				echo ($db->query()) ? $id : '0';
			}else{
				echo '0';
			}
		}
		
	}
	
	public function removeProductType(){
		$ptid=JRequest::getVar('ptid', null);
		$db=& JFactory::getDBO();
		$q="DELETE FROM `#__vm_product_type` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$db->query();
		
		$q="DELETE FROM `#__vm_product_product_type_xref` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$db->query();
		
		$q="DELETE FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$db->query();
		
		$q="DROP TABLE IF EXISTS `#__vm_product_type_$ptid`";
		$db->setQuery($q);
		$db->query();
	}
	
	public function getManageParametersPage(){
		$ptid=JRequest::getVar('ptid', null);
		
		$db=& JFactory::getDBO();
		$q="SELECT `product_type_name` FROM `#__vm_product_type` WHERE `product_type_id`=$ptid";
		$db->setQuery($q);
		$ptname=$db->loadResult();
		
		require_once( dirname(__FILE__).'/../views/pt.php' );
		fsViewPT::printPTTitle($ptname);
		
		$this->getParameters();
	}
	
	public function getParameters(){
		$ptid=JRequest::getVar('ptid', null);
		
		$db=& JFactory::getDBO();
		// $q="SELECT `product_type_name` FROM `#__vm_product_type` WHERE `product_type_id`=$ptid";
		// $db->setQuery($q);
		// $ptname=$db->loadResult();
		
		$q="SELECT * FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid ORDER BY `parameter_list_order` ASC";
		$db->setQuery($q);
		$parameters=$db->loadObjectList();
		
		//require_once( dirname(__FILE__).'/../views/pt.php' );
		//fsViewPT::printPTTitle($ptname);
		fsViewPT::printParameters($parameters);
	}
	
	public function getParameterForm(){
		require_once( dirname(__FILE__).'/../views/pt.php' );
		fsViewPT::printParameterForm();
	}
	
	public function removeParameter(){
		$parameter_name=JRequest::getVar('parameter_name', null);
		$ptid=JRequest::getVar('ptid', null);
		$db=& JFactory::getDBO();
		
		$q="DELETE FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid AND `parameter_name`='$parameter_name'";
		$db->setQuery($q);
		$db->query();
		
		$q="ALTER TABLE `#__vm_product_type_$ptid` DROP `$parameter_name`";
		$db->setQuery($q);
		$db->query();
		
		//$this->dropIndex("#__vm_product_type_$ptid", $parameter_name);
	}
	
	public function saveParameters(){
		$keys=JRequest::getVar('key', null);
		$ptid=JRequest::getVar('ptid', null);
		//print_r($keys);
		
		if (!$keys) return;
		
		foreach($keys as $key){
			$parameter_name=trim(JRequest::getVar('parameter_name_'.$key, null));
			if(!$parameter_name) continue;
			
			$parameter_name_active=JRequest::getVar('parameter_name_active_'.$key, null);
			//$parameterNameIsValid=($parameter_name==$parameter_name_active)? true : $this->parameterNameIsValid($parameter_name);
			//if(!$parameterNameIsValid) continue;
			if($parameter_name!=$parameter_name_active && !$this->parameterNameIsValid($parameter_name)) continue;
			
			$db=& JFactory::getDBO();
			$parameter_label=JRequest::getVar('parameter_label_'.$key, null);
			$defined_filters=JRequest::getVar('defined_filters_'.$key, null);
			$parameter_description=JRequest::getVar('parameter_description_'.$key, null);
			$parameter_unit=JRequest::getVar('parameter_unit_'.$key, null);
			$parameter_mode=JRequest::getVar('parameter_mode_'.$key, null);
			$parameter_type=JRequest::getVar('parameter_type_'.$key, null);
			$parameter_multiselect=JRequest::getVar('parameter_multiselect_'.$key, null);
			$list_order=JRequest::getVar('list_order_'.$key, null);
			
			if(!$parameter_multiselect) $parameter_multiselect='N';
			
			$defined_filters=$this->validateDefinedFilters($defined_filters);
			
			if(!$parameter_name_active){ // it's a new parameter
				// $q="INSERT INTO `#__vm_product_type_parameter` VALUES('$ptid', '$parameter_name', '$parameter_label', ".
					// $db->quote($parameter_description).", '$list_order', '$parameter_type', ".$db->quote($defined_filters).", '$parameter_multiselect', '', ".
					// $db->quote($parameter_unit).", '$parameter_mode')";
					
				$q="INSERT INTO `#__vm_product_type_parameter` SET `product_type_id`='$ptid', `parameter_name`='$parameter_name', ".
					"`parameter_label`='$parameter_label', `parameter_description`=". $db->quote($parameter_description). ", ".
					"`parameter_list_order`='$list_order', `parameter_type`='$parameter_type', `parameter_values`=". $db->quote($defined_filters).", ".
					"`parameter_multiselect`='$parameter_multiselect', `parameter_unit`=". $db->quote($parameter_unit).", ".
					"`mode`='$parameter_mode'";
				
				echo $q.'<br/><br/>';
				$db->setQuery($q);
				if ($db->query()) {
					$data_type=$this->getSQLDataType($parameter_type);
					$q="ALTER TABLE `#__vm_product_type_$ptid` ADD `$parameter_name` $data_type NULL DEFAULT NULL";
					
					//echo $q.'<br/><br/>';
					$db->setQuery($q);
					$db->query();
					
					$size=($parameter_type=='T')? 128 : 0;
					$this->addIndex("#__vm_product_type_$ptid", $parameter_name, $size);
				}
			} else {
				// $q="UPDATE `#__vm_product_type_parameter` SET `parameter_name`='$parameter_name', `parameter_label`='$parameter_label', ".
				// "`parameter_description`='".mysql_real_escape_string($parameter_description)."', `parameter_list_order`='$list_order', `parameter_type`='$parameter_type', ".
				// "`parameter_values`='".mysql_real_escape_string($defined_filters)."', `parameter_multiselect`='$parameter_multiselect', `parameter_unit`='".mysql_real_escape_string($parameter_unit)."', `mode`='$parameter_mode' ".
				// "WHERE `product_type_id`=$ptid AND `parameter_name`='$parameter_name_active'";
				
				$q="UPDATE `#__vm_product_type_parameter` SET `parameter_name`='$parameter_name', `parameter_label`='$parameter_label', ".
				"`parameter_description`=".$db->quote($parameter_description).", `parameter_list_order`='$list_order', `parameter_type`='$parameter_type', ".
				"`parameter_values`=".$db->quote($defined_filters).", `parameter_multiselect`='$parameter_multiselect', `parameter_unit`=".$db->quote($parameter_unit).", `mode`='$parameter_mode' ".
				"WHERE `product_type_id`=$ptid AND `parameter_name`='$parameter_name_active'";
				
				//echo $q.'<br/><br/>';
				$db->setQuery($q);
				$db->query();
				// when parameter name is changed--update column name and index
				if($parameter_name!=$parameter_name_active){
					$data_type=$this->getSQLDataType($parameter_type);
					$q="ALTER TABLE `#__vm_product_type_$ptid` CHANGE `$parameter_name_active` `$parameter_name` $data_type CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
					
					//echo $q.'<br/><br/>';
					$db->setQuery($q);
					$db->query();
					
					$this->dropIndex("#__vm_product_type_$ptid", $parameter_name_active);
					$size=($parameter_type=='T')? 128 : 0;
					$this->addIndex("#__vm_product_type_$ptid", $parameter_name, $size);
				}
			}
		}
	}
	
	private function parameterNameIsValid($name){
		$db=& JFactory::getDBO();
		$ptid=JRequest::getVar('ptid', null);
		
		$regex='/[^a-zA-Z0-9_]/';
		if( preg_match($regex, $name) ) return false;
		
		$q="SELECT COUNT(`parameter_name`) FROM `#__vm_product_type_parameter` WHERE `product_type_id`=$ptid AND `parameter_name`='$name'";
		$db->setQuery($q);
		return ($db->loadResult()==0)? true : false;
	}
	
	private function getSQLDataType($type){
		switch($type){
			case 'S': $r='varchar(255)'; break;
			case 'V': $r='varchar(255)'; break;
			case 'I': $r='int(11)'; break;
			case 'T': $r='text'; break;
			case 'C': $r='char(1)'; break;
		}
		return $r;
	}
	
	private function addIndex($table, $column, $_size=0){
		$db=& JFactory::getDBO();
		$size=($_size)? "( $_size ) " : "";
		$q="ALTER TABLE `$table` ADD INDEX `idx_$column` ( `$column` $size) ";
		$db->setQuery($q);
		$db->query();
		//ALTER TABLE `g`.`jos_vm_product_type_4` ADD INDEX ( `brand1` ( 128 ) ) 
	}
	
	private function dropIndex($table, $column){
		$db=& JFactory::getDBO();
		$q="ALTER TABLE `$table` DROP INDEX `idx_$column`";
		$db->setQuery($q);
		$db->query();
	}
	
	private function validateDefinedFilters($s){
		$filters=explode(';', $s);
		$temp=array();
		foreach($filters as $filter){
			$v=trim($filter);
			if($v) $temp[]=$v;
		}
		$result=implode(';', $temp);
		return $result;
	}
	
	public function getSizeOfParameterValuesColumn(){
		$db=& JFactory::getDBO();
		$config =& JFactory::getConfig();
		$q="SELECT CHARACTER_MAXIMUM_LENGTH 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '".$config->getValue( 'config.dbprefix' )."vm_product_type_parameter'
AND table_schema = '".$config->getValue( 'config.db' )."'
AND column_name LIKE 'parameter_values'";
		
		$db->setQuery($q);
		return $db->loadResult();
	}
	
	public function increaseParameterValuesColumnSize(){
		$size=JRequest::getVar('size', 255);
		$chunks=ceil($size/256);
		$next_column_size=$chunks*256;
		$db=& JFactory::getDBO();
		$q="ALTER TABLE `#__vm_product_type_parameter` CHANGE `parameter_values` `parameter_values` VARCHAR( $next_column_size )";
		$db->setQuery($q);
		if( $db->query() ) echo $next_column_size;
	}
}
?>
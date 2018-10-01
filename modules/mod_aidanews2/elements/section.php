<?php 
/**
 * $ModDesc
 * 
 * @version		$Id: helper.php $Revision
 * @package		modules
 * @subpackage	mod_lofflashcontent
 * @copyright	Copyright (C) JAN 2010 LandOfCoder.com <@emai:landofcoder@gmail.com>. All rights reserved.
 * @license		GNU General Public License version 2
 */
defined('_JEXEC') or die( 'Restricted access' );
/**
 * Get a collection of sections
 */
class JElementSection extends JElement {
	
	/*
	 * Section name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Section';
	
	/**
	 * fetch Element 
	 */
	function fetchElement($name, $value, &$node, $control_name){
		$db = &JFactory::getDBO();
		$version = new 	JVersion();
		if( $version->RELEASE == '1.6' ){
			$data =   JHtml::_('category.options', 'com_content');
			$categories = array();
			$categories[0] = new stdClass();
			$categories[0]->value = '';
			$categories[0]->text = JText::_("---------- Select All ----------");
			$data = array_merge($categories,$data);

			return JHTML::_( 'select.genericlist', 
							 $data, ''.$control_name.'['.$name.'][]',
							 'class="inputbox"   multiple="multiple" size="10"',
							 'value', 
							 'text', 
							 $value );
		} else {
		
			$query = 'SELECT id, title FROM #__sections WHERE published=1';
			$db->setQuery( $query );		
			$sections = $db->loadObjectList();
			$secs = array();
			$secs[0]->id = '';
			$secs[0]->title = JText::_("SELECT_ALL");
			
			foreach ($sections as $section) {
				array_push($secs,$section);
			}
			
			return JHTML::_( 'select.genericlist', 
							 $secs, ''.$control_name.'['.$name.'][]',
							 'class="inputbox" style="width:95%;" multiple="multiple" size="4"',
							 'id', 
							 'title', 
							 $value );
		} 
	}
}

?>

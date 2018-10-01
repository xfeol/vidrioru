<?php
/**
 * @package		Zaragoza Seo Complements 2.0.2
 * @license		GNU/GPL 2.0
 * @author		Ciro Artigot <info@zaragozaonline.com>
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemZaragoza_Seo extends JPlugin
{

	function plgSystemZaragoza_Seo(& $subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	
	function onAfterRender(){
		global $mainframe;
		if($mainframe->isAdmin()) return;
		$document =& JFactory::getDocument();
		if($this->params->get('removegenerator',0)) {
			if ($document->getType() == 'html') {
					$buffer = JResponse::getBody();
					$buffer = preg_replace( '/<meta\s*name="Generator"\s*content=".*\/>/isU','', $buffer);
					JResponse::setBody($buffer);
				}
		}
	}

	function onAfterDispatch()
	{
		global $mainframe;
		if($mainframe->isAdmin()) return;
		$document =& JFactory::getDocument();
		$db = & JFactory::getDBO();
		$menu =& JSite::getMenu();
		$separador = ' '.trim($this->params->get('separador','-')).' ';
		$nombredelsitio = $mainframe->getCfg('sitename');	
		$titulo = $document->getTitle();
		$view = JRequest::getVar('view','');
		$id = JRequest::getVar('id',0);		
		$description = $mainframe->getCfg('MetaDesc');
		
		$document =& JFactory::getDocument();

		if($this->params->get('seotitle',1)){	// si queremos que el plugin maneje el titulo
			if ($menu->getActive()==$menu->getDefault()) {
				$titulo = $nombredelsitio;
			}
			else {
				$titulo = $document->getTitle();
				if($this->params->get('posicion')) $titulo = $titulo.$separador.$nombredelsitio;
				else $titulo = $nombredelsitio.$separador.$titulo;
			}
			$document->setTitle($titulo);
		}
				
		if($this->params->get('removebase',0)) $document->setBase(null);	
		
		if($this->params->get('seodescription',1)){		
			if($view=='article'&&$id)	{
				$query = "SELECT `introtext`,`fulltext`,`metadesc` FROM #__content WHERE id=".(int)$_REQUEST['id'];
				$db->setQuery($query);
				$row = $db->loadObject();
				if(!$row->metadesc&&$row->introtext) $description = plgSystemZaragoza_Seo::cut_string(strip_tags($row->introtext),350);
			}
			if($view=='category'&&$id)	{
				$query = "SELECT description FROM #__categories WHERE id=".(int)$_REQUEST['id'];
				$db->setQuery($query);
				$row = $db->loadObject();
				if($row&&$row->description) $description = plgSystemZaragoza_Seo::cut_string(strip_tags($row->description),350);
			}
			if($view=='section'&&$id){
				$query = "SELECT description FROM #__sections WHERE id=".(int)$_REQUEST['id'];
				$db->setQuery($query);
				$row = $db->loadObject();
				if($row&&$row->description)  $description =  plgSystemZaragoza_Seo::cut_string(strip_tags($row->description),350);
			}		
		}
		
		$document->setDescription($description);
				
		$headerstuff = $document->getHeadData(); 
		//title-description-link-metaTags-links-styleSheets-style-scripts-script-custom- 
		
		$metaTags= $headerstuff['metaTags']['standard'];
		
		if($metaTags) {
		
			foreach($metaTags as $t=>$v)	{
				if($t=='robots'&&$this->params->get('removerobots',0)) unset($headerstuff['metaTags']['standard'][$t]); 
			}
		}
		
		$metaTags2= $headerstuff['metaTags']['http-equiv'];
		
		if($metaTags2) {
			foreach($metaTags2 as $t=>$v)	{
				if($t=='generator'&&$this->params->get('removegenerator',0)) unset($headerstuff['metaTags']['http-equiv'][$t]); 
			}
		}
		
		$scripts = $headerstuff['scripts'];
		if($scripts){
			foreach($scripts as $t=>$v)	{
				if(substr($t, -27)== 'media/system/js/mootools.js'&&$this->params->get('hidemootols')) unset($headerstuff['scripts'][$t]); 
				if(substr($t, -26)=='media/system/js/caption.js'&&$this->params->get('hidecaption')) unset($headerstuff['scripts'][$t]); 
			}
		}
		$document->setHeadData($headerstuff); 
		return true;
	}
	
	function cut_string($string, $charlimit)	{
		if(substr($string,$charlimit-1,1) != ' ')	{
			$string = substr($string,'0',$charlimit);
			$array = explode(' ',$string);
			array_pop($array);
			$new_string = implode(' ',$array);
			return $new_string.' ...';
		}
		else	{
			return substr($string,'0',$charlimit-1).' ...';
		}
	}
}

?>

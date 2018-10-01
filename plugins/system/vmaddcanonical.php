<?php
/**
 * VM Add Canonical plugin for Joomla! 1.5
 *
 * @author Kevin Peuhkurinen (kohr_ah99@yahoo.com)
 * @package vmopengraph
 * @copyright Copyright 2011
 * @license GNU Public License v3 (http://www.gnu.org/licenses/gpl.html)
 * @link http://www.bigskyphotography.ca
 * @version 1.2
 *  
 * Changelog
 * 
 * v1.2
 * bug fix for installations in subdirectories
 *
 * v1.1
 * replaced jos_ with #__ in db calls
 * 
 * v1.0
 * initial release
 */

 
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemVMAddCanonical extends JPlugin {


	/**
	*
	* @var string
	* @access  private
	*/
	var $_eol = "\12";

  /**
   * Constructor
   *
   * For php4 compatability we must not use the __constructor as a constructor for plugins
   * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
   * This causes problems with cross-referencing necessary for the observer design pattern.
   *
   * @param object $subject The object to observe
   * @since 1.5
   */
		function plgSystemAddCanonical(& $subject, $config) {
				parent :: __construct($subject, $config);
			  }

		function onAfterDispatch() {
				global $mainframe;

				// Don't do anything if this is the administrator backend or debugging is on
				if( $mainframe->isAdmin() ) {
					return;
				}
				
				// this is required for canonical addition
				global $sess;
				
				$document = & JFactory::getDocument();
				$docType = $document->getType();
				$this->_eol = $document->_getLineEnd();
				// Only mod site pages that are html docs (no admin, install, etc.)
				if ( !$mainframe->isSite() ) return;
				if ( $docType != 'html' ) return;

				// stop if the header already has a canonical tag
				$heads = $document->getHeadData();
				$links = $heads['links'];
				foreach ($links as $h) {
					if (strpos($h, 'rel="canonical"')) {
						return;
					}
				}
				
				$db = JFactory::getDBO();
				$router = &JRouter::getInstance("site");
				
				//no parameters for this plugin yet
				//$plugin = & JPluginHelper :: getPlugin('system', 'vmaddcanonical');
				//$params = new JParameter($plugin->params);
				
				$u =& JFactory::getURI();
				$q =& $router->parse($u);
								
				if (!array_key_exists("product_id", $q)) {
					return;
				}
				
				$prod_id = $q[product_id];
				
				// some sef routers leave the product name attached to the product id.
				// if product ID is not numeric, strip out any non-numeric characters
				if (!is_numeric($prod_id)) {
					if (preg_match('/(?:\d+)/', $prod_id, $res) == 0) {
						return;
					}
					$prod_id = $res[0];
					
				}
				
				// get the number of categories for the product
				$db->setQuery("SELECT COUNT(*) FROM #__vm_product_category_xref WHERE product_id = " . $db->quote($prod_id) );
				$db->query();
				$numcats = $db->loadResult();

				// get list of categories
				$db->setQuery("SELECT category_id FROM #__vm_product_category_xref WHERE product_id = " . $db->quote($prod_id) );
				$db->query();
				$cats = $db->loadResultArray();
				// find the first published category
				foreach ($cats as $cat) {
					$db->setQuery("SELECT category_publish, category_flypage FROM #__vm_category WHERE category_id = " . $db->quote($cat) );
					$db->query();
					$row = $db->loadRow();
					if ( $row[0] == 'Y' ) {
							break;
					}
				}

				// build the link
				
				$link = 'page=shop.product_details&product_id='.$prod_id.'&flypage='.$row[1];
				$myuri     = & JURI::getInstance();
				$mycurrent = $myuri->toString( array('scheme', 'host', 'port'));
				$canon = $mycurrent . '/' . trim($sess->url( $_SERVER['PHP_SELF'].'?'.$link ), "/");
				$document->addCustomTag( '<link href="' . str_replace("&amp;", "&", $canon) . '" rel="canonical" />');
		}	
				
}
				
 
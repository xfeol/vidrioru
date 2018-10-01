<?php
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' ); 
/**
*
* @version $Id: checkout.index.php 617 2007-01-04 19:43:08Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );
require_once( CLASSPATH . "ps_checkout.php" );

echo '<h3>'.$VM_LANG->_PHPSHOP_CHECKOUT_TITLE.'</h3>';
include(PAGEPATH.'ro_basket.php');

if ($checkout) {
  echo '<br />';
  include(PAGEPATH.'checkout.without_register_form.php');
}
else mosRedirect( 'index.php?option=com_virtuemart&page=shop.index&Itemid='.$Itemid, $VM_LANG->_PHPSHOP_EMPTY_CART );

?>
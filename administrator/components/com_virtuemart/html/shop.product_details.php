<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
*
* @version $Id: shop.product_details.php 1988 2009-11-11 14:29:52Z soeren_nb $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2009 soeren - All rights reserved.
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

require_once(CLASSPATH . 'ps_product_files.php' );
require_once(CLASSPATH . 'imageTools.class.php' );
require_once(CLASSPATH . 'ps_product.php' );
$ps_product = $GLOBALS['ps_product'] = new ps_product;

require_once(CLASSPATH . 'ps_product_category.php' );
$ps_product_category = new ps_product_category;

require_once(CLASSPATH . 'ps_product_attribute.php' );
$ps_product_attribute = new ps_product_attribute;

require_once(CLASSPATH . 'ps_product_type.php' );
$ps_product_type = new ps_product_type;
require_once(CLASSPATH . 'ps_reviews.php' );

$product_id = intval( vmGet($_REQUEST, "product_id", null) );
$product_sku = $db->getEscaped( vmGet($_REQUEST, "sku", '' ) );
$category_id = vmGet($_REQUEST, "category_id", null);
$pop = (int)vmGet($_REQUEST, "pop", 0);
$manufacturer_id = vmGet($_REQUEST, "manufacturer_id", null);
$Itemid = $sess->getShopItemid();
$db_product = new ps_DB;

// Check for non-numeric product id
if (!empty($product_id)) {
	if (!is_numeric($product_id)) {
		$product_id = '';
	}
}

// Check if product_id linked with category_id
$q = "SELECT * FROM `#__{vm}_product_category_xref` WHERE ";
$q .= "`product_id`=$product_id AND `category_id`=$category_id"; 
$db_product->query($q);

if( !$db_product->next_record() ) {
	header('HTTP/1.0 404 Not Found');
	$vmLogger->err( $VM_LANG->_('PHPSHOP_PRODUCT_NOT_FOUND',false)." product_id=$product_id AND category_id=$category_id" );
	return;
}

// Get the product info from the database
$q = "SELECT * FROM `#__{vm}_product` WHERE ";
if( !empty($product_id)) {
	$q .= "`product_id`=$product_id";
}
elseif( !empty($product_sku )) {
	$q .= "`product_sku`='$product_sku'";
}
else {
	vmRedirect( $sess->url( $_SERVER['PHP_SELF']."?keyword=".urlencode($keyword)."&category_id={$_SESSION['session_userstate']['category_id']}&limitstart={$_SESSION['limitstart']}&page=shop.browse", false, false ), $VM_LANG->_('PHPSHOP_PRODUCT_NOT_FOUND') );
}

if( !$perm->check("admin,storeadmin") ) {
	$q .= " AND `product_publish`='Y'";
	if( CHECK_STOCK && PSHOP_SHOW_OUT_OF_STOCK_PRODUCTS != "1") {
		$q .= " AND `product_in_stock` > 0 ";
	}
}
$db_product->query( $q );

// Redirect back to Product Browse Page on Error
if( !$db_product->next_record() ) {
	header('HTTP/1.0 404 Not Found');
	$vmLogger->err( $VM_LANG->_('PHPSHOP_PRODUCT_NOT_FOUND',false) );
	return;
}
if( empty($product_id)) {
	$product_id = $db_product->f('product_id');
}
$product_parent_id = (int)$db_product->f("product_parent_id");
if ($product_parent_id != 0) {
	$dbp= new ps_DB;
	$dbp->query('SELECT * FROM `#__{vm}_product` WHERE `product_id`='.$product_parent_id );
	$dbp->next_record();
}

// Create the template object
$tpl = vmTemplate::getInstance();


// Let's have a look wether the product has related products.
$q = "SELECT product_sku, related_products FROM #__{vm}_product,#__{vm}_product_relations ";
$q .= "WHERE #__{vm}_product_relations.product_id='$product_id' AND product_publish='Y' ";
$q .= "AND FIND_IN_SET(#__{vm}_product.product_id, REPLACE(related_products, '|', ',' )) LIMIT 0, 4";
$db->query( $q );
/*// This shows randomly selected products from the products table
// if you don't like to set up related products for each product
$q = "SELECT product_sku FROM #__{vm}_product ";
$q .= "WHERE product_publish='Y' AND product_id != $product_id ";
$q .= "ORDER BY RAND() LIMIT 0, 4";
$db->query( $q );*/
$related_products = '';
if( $db->num_rows() > 0 ) {
	$tpl->set( 'ps_product', $ps_product );
	$tpl->set( 'products', $db );
	$related_products = $tpl->fetch( '/common/relatedProducts.tpl.php' );
}

// GET THE PRODUCT NAME 
$product_name = shopMakeHtmlSafe(  $db_product->f("product_name") );
if( $db_product->f("product_publish") == "N" ) {
	$product_name .= " (".$VM_LANG->_('CMN_UNPUBLISHED').")";
}
$product_description = $db_product->f("product_desc");
if( (str_replace("<br />", "" , $product_description)=='') && ($product_parent_id!=0) ) {
	$product_description = $dbp->f("product_desc"); // Use product_desc from Parent Product
}
$product_description = vmCommonHTML::ParseContentByPlugins( $product_description );

// Get the CATEGORY NAVIGATION 
$navigation_pathway = "";
$navigation_childlist = "";
$pathway_appended = false;

$flypage = vmGet($_REQUEST, "flypage" );

// Each Product is assigned to one or more Categories, if category_id was omitted, we must fetch it here
if (empty($category_id) || empty( $flypage ))  {
	$q = "SELECT cx.category_id, category_flypage FROM #__{vm}_category c, #__{vm}_product_category_xref cx WHERE product_id = '$product_id' AND c.category_id=cx.category_id LIMIT 0,1";
	$db->query( $q );
	$db->next_record();
	if( !$db->f("category_id") ) {
		// The Product Has no category entry and must be a Child Product
		// So let's get the Parent Product
		$q = "SELECT product_id FROM #__{vm}_product WHERE product_id = '".$db_product->f("product_parent_id")."' LIMIT 0,1";
		$db->query( $q );
		$db->next_record();

		$q = "SELECT cx.category_id, category_flypage FROM #__{vm}_category c, #__{vm}_product_category_xref cx WHERE product_id = '".$db->f("product_id")."' AND c.category_id=cx.category_id LIMIT 0,1";
		$db->query( $q );
		$db->next_record();
	}
	$_GET['category_id'] = $category_id = $db->f("category_id");
}
$ps_product->addRecentProduct($product_id,$category_id,$tpl->get_cfg('showRecent', 5));
if( empty( $flypage )) {
	$flypage = $db->f('category_flypage') ? $db->f('category_flypage') : FLYPAGE;
}
// Flypage Parameter has old page syntax: shop.flypage
// so let's get the second part - flypage
$flypage = str_replace( 'shop.', '', $flypage);
$flypage = stristr( $flypage, '.tpl') ? $flypage : $flypage . '.tpl';

// Set up the pathway
// Retrieve the pathway items for this product's category
$category_list = array_reverse( $ps_product_category->get_navigation_list( $category_id ) );
$pathway = $ps_product_category->getPathway( $category_list );

// Add this product's name to the pathway, with no link
$item = new stdClass();
$item->name = $product_name;
$item->link = '';
$pathway[] = $item;

// Set the CMS pathway
$vm_mainframe->vmAppendPathway( $pathway );

// Set the pathway for our template
$tpl->set( 'pathway', $pathway );

$tpl->set( 'product_name', $product_name );

// Get the neighbor Products to allow navigation on product level
$neighbors = $ps_product->get_neighbor_products( !empty( $product_parent_id ) ? $product_parent_id : $product_id );
$next_product = $neighbors['next'];
$previous_product = $neighbors['previous'];
$next_product_url = $previous_product_url = '';
if( !empty($next_product) ) {
	$url_parameters = 'page=shop.product_details&product_id='.$next_product['product_id'].'&flypage='.$ps_product->get_flypage($next_product['product_id']).'&pop='.$pop;
    if( $manufacturer_id ) {
    	$url_parameters .= "&amp;manufacturer_id=" . $manufacturer_id;
    }
    if( $keyword != '') {
    	$url_parameters .= "&amp;keyword=".urlencode($keyword);
    }
	if( $pop == 1 ) {
		$next_product_url = $sess->url( $_SERVER['PHP_SELF'].'?'.$url_parameters );
	} else {
		$next_product_url = str_replace("index2","index",$sess->url( $url_parameters ));
	}
}
if( !empty($previous_product) ) {
	$url_parameters = 'page=shop.product_details&product_id='.$previous_product['product_id'].'&flypage='.$ps_product->get_flypage($previous_product['product_id']).'&pop='.$pop;
    if( $manufacturer_id ) {
    	$url_parameters .= "&amp;manufacturer_id=" . $manufacturer_id;
    }
    if( $keyword != '') {
    	$url_parameters .= "&amp;keyword=".urlencode($keyword);
    }
	if( $pop == 1 ) {
		$previous_product_url = $sess->url( $_SERVER['PHP_SELF'].'?'.$url_parameters );
	} else {
		$previous_product_url = str_replace("index2","index",$sess->url( $url_parameters ));
	}
}

$tpl->set( 'next_product', $next_product );
$tpl->set( 'next_product_url', $next_product_url );
$tpl->set( 'previous_product', $previous_product );
$tpl->set( 'previous_product_url', $previous_product_url );

$parent_id_link = $db_product->f("product_parent_id");
$return_link = "";
if ($parent_id_link <> 0 ) {
	$q = "SELECT product_name FROM #__{vm}_product WHERE product_id = '$product_parent_id' LIMIT 0,1";
	$db->query( $q );
	$db->next_record();
	$product_parent_name = $db->f("product_name");
	$return_link = "&nbsp;<a class=\"pathway\" href=\"";
	$return_link .= $sess->url($_SERVER['PHP_SELF'] . "?page=shop.product_details&product_id=$parent_id_link");
	$return_link .= "\">";
	$return_link .= $product_parent_name;
	$return_link .= "</a>";
	$return_link .= " ".vmCommonHTML::pathway_separator()." ";
}
$tpl->set( 'return_link', $return_link );

// Create the pathway for our template
$navigation_pathway = $tpl->fetch( 'common/pathway.tpl.php');

if ($ps_product_category->has_childs($category_id) ) {
	$category_childs = $ps_product_category->get_child_list($category_id);
	$tpl->set( 'categories', $category_childs );
	$navigation_childlist = $tpl->fetch( 'common/categoryChildlist.tpl.php');
}


//START HACK METADATA FOR PRODUCT DETAILS PAGE 
// Set Dynamic Page Title
//if( function_exists('mb_substr')) {
//	$page_title = mb_substr($product_name, 0, 64, vmGetCharset() );
//} else {
//	$page_title = substr($product_name, 0, 64 );
	
//}
//$vm_mainframe->setPageTitle( html_entity_decode( $page_title, ENT_QUOTES, vmGetCharset() ));

// Prepend Product Short Description Meta Tag "description"
//if( vmIsJoomla('1.5')) {
//	$document = JFactory::getDocument();
//	$document->setDescription(strip_tags( $db_product->f("product_s_desc")));
//} else {
//	$mainframe->prependMetaTag( "description", strip_tags( $db_product->f("product_s_desc")) );
//}

global $remove_less_than;
$document =& JFactory::getDocument();

/**
* CATEGORY NAME
*/
if( $category_id ) {
	$dbbc = new ps_DB;
	$dbbc->query( "SELECT category_id, category_name FROM #__{vm}_category WHERE category_id='$category_id'");
	$dbbc->next_record();
	$get_category = ucwords(strip_tags( $dbbc->f('category_name')));
}

$dbmp = new ps_DB;
$q = "SELECT banned, remove_less_than, number_of_keywords, append_category, general_append_end, general_append_end_title, general_append_keys, general_append_keys_phrase, append_prod_name_phrase, append_par_cat_name_phrase, turn_on_edit, add_abstract, generator, subject, classification, 
author, organization, copyright, country, content_language, language, designer, comments, no_email_collection FROM #__{vm}_product_metakeys WHERE mid!='2'";
$dbmp->query( $q );
$dbmp->next_record();
$banned = $dbmp->f("banned");
$remove_less_than = intval($dbmp->f("remove_less_than"));
$number_of_keywords = intval($dbmp->f("number_of_keywords"));
$append_category = $dbmp->f("append_category");
$general_append_end = $dbmp->f("general_append_end");
$general_append_end_title = $dbmp->f("general_append_end_title");
$general_append_keys = $dbmp->f("general_append_keys");
$general_append_keys_phrase = $dbmp->f("general_append_keys_phrase");
$append_prod_name_phrase = $dbmp->f("append_prod_name_phrase");
$append_par_cat_name_phrase = $dbmp->f("append_par_cat_name_phrase");
$turn_on_edit = $dbmp->f("turn_on_edit");
$add_abstract = $dbmp->f("add_abstract");
$generator = $dbmp->f("generator");
$subject = $dbmp->f("subject");
$classification = $dbmp->f("classification");
$author = $dbmp->f("author");
$organization = $dbmp->f("organization");
$copyright = $dbmp->f("copyright");
$country = $dbmp->f("country");
$content_language = $dbmp->f("content_language");
$language = $dbmp->f("language");
$designer = $dbmp->f("designer");
$comments = $dbmp->f("comments");
$no_email_collection = $dbmp->f("no_email_collection");

//Set Page Title
if (($db_product->f("product_title") != "") && ($turn_on_edit == "Y")) {
	if ($append_category == "Y") {
		if ($general_append_end_title != "") {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_title').' - '.$get_category.' - '.$general_append_end_title)));
		} else {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_title').' - '.$get_category)));
		}
	} else {
		if ($general_append_end_title != "") {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_title').' - '.$general_append_end_title)));
		} else {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_title'))));
		}
	}
} else {
	if ($append_category == "Y") {
		if ($general_append_end_title != "") {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_name').' - '.$get_category.' - '.$general_append_end_title)));
		} else {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_name').' - '.$get_category)));
		}
	} else {
		if ($general_append_end_title != "") {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_name').' - '.$general_append_end_title)));
		} else {
			$document->setTitle(str_replace('"', '', strip_tags( $db_product->f('product_name'))));
		}
	}
}

// Set Description Metatag
	if (($db_product->f("product_metadesc") != "") && ($turn_on_edit == "Y")) {
		if ($append_category == "Y") {
			if ($general_append_end != "") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_metadesc").' - '.$get_category.' - '.$general_append_end)));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_metadesc").' - '.$get_category)));
			}
		} else {
			if ($general_append_end != "") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_metadesc").' - '.$general_append_end)));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_metadesc"))));
			}
		}
	} else {
		if ($append_category == "Y") {
			if ($general_append_end != "") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_name").' - '.$get_category.' - '.$db_product->f("product_s_desc").' - '.$general_append_end)));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_name").' - '.$get_category.' - '.$db_product->f("product_s_desc"))));
			}
		} else {
			if ($general_append_end != "") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_name").' - '.$db_product->f("product_s_desc").' - '.$general_append_end)));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db_product->f("product_name").' - '.$db_product->f("product_s_desc"))));
			}
		}
	}

// Add and Set Abstract Metatag?
if ($add_abstract == "Y") {
	if ($turn_on_edit == "Y") {
		$name = "abstract";
		$content = strip_tags($db_product->f("product_abstract"));
		$document->setMetaData($name, $content);
	} else {
		$name = "abstract";
		$content = str_replace('"', '', strip_tags($db_product->f("product_name").' - '.$db_product->f("product_s_desc")));
		$document->setMetaData($name, $content);
	}
}

// Add Canonical?
if ($db_product->f("product_canonical")) {
	$href = str_replace('&amp;', '&', $db_product->f('product_canonical')); 
	$document->addHeadLink( $href, 'canonical', 'rel', '' );
}

// Set Keyowrds Metatag
if (($db_product->f("product_metakey")!="") && ($turn_on_edit == "Y")) {
	if ($general_append_keys != "") {
		if ($general_append_keys_phrase != "") {
			$document->setMetaData('keywords',strip_tags($db_product->f("product_metakey").', '.$general_append_keys.', '.$general_append_keys_phrase));
		} else {
			$document->setMetaData('keywords',strip_tags($db_product->f("product_metakey").', '.$general_append_keys));
		}
	} else {
		if ($general_append_keys_phrase != "") {
			$document->setMetaData('keywords',strip_tags($db_product->f("product_metakey").', '.$general_append_keys_phrase));
		} else {
        		$document->setMetaData('keywords',strip_tags($db_product->f("product_metakey")));
		}
	}
} else {
	function clean_text($input_text)
	//this function clean ups the text
	{
		$input_text = strip_tags($input_text); //Strip HTML and PHP tags from a string
		$input_text = trim($input_text); //Strip whitespace from the beginning and end of a string
		$input_text = html_entity_decode($input_text); //Convert all HTML entities to their applicable characters
		$input_text = str_replace('_', '-', $input_text);
		$input_text = str_replace('-', ' ', $input_text);
		$input_text = str_replace('+', ' ', $input_text);
		$input_text = str_replace('\'', ' ', $input_text);
		$input_text = str_replace('\\', '-', $input_text);
		$input_text = str_replace('/', ' ', $input_text);
		$input_text = preg_replace('|["<>!()$%?&^#:;,.*=]|i', '', $input_text); // Removes special characters
		$input_text = preg_replace('/\s\s+/', ' ', $input_text); // Removes spaces of more than 1 character
		return $input_text;
	}

	function sort_words($a,$b){ 
    		// Sorting in descending order by frequency (0th element); 
    		// tiebreaks decided lexicographically (1st element) 
    		if($t = $b[0]-$a[0]) return $t; 
   		return strcmp($b[1],$a[1]); 
	} 

	function longer_than_x_letters($word) { 
		global $remove_less_than;
    		return strlen($word[1]) > $remove_less_than;
	} 
	function replace_me($find, $replace, $text) {
		$text = preg_replace($find, $replace, $text);
		$text = preg_replace('/\s\s+/', ' ', $text); // Removes spaces of more than 1 character
		$text = ucwords($text);
		return $text;
	}

	$get_words = ucwords(strip_tags($db_product->f("product_s_desc").' '.$db_product->f("product_desc")));
	$get_title = ucwords(strip_tags($db_product->f("product_name")));

	$clean_words = clean_text($get_words);
	$clean_title = clean_text($get_title);
	$clean_category = clean_text($get_category);
	
	$upper_case = ucwords($banned);
	$remove_banned = explode(",",$upper_case);
	$find = '/\b('.implode('\b|', $remove_banned).'\b)/i';
	$replace = '';

$text = $clean_words;
do {
	$text_before = $text;
	$text = replace_me($find, $replace, $text);
}   while ( $text_before != $text );
$new_string_words = $text;

$text = $clean_title;
do {
	$text_before = $text;
	$text = replace_me($find, $replace, $text);
}   while ( $text_before != $text );
$new_string_title = $text;

$text = $clean_category;
do {
	$text_before = $text;
	$text = replace_me($find, $replace, $text);
}   while ( $text_before != $text );
$new_string_category = $text;

	$words = array_count_values(preg_split('/[^a-z]+/', strtolower($new_string_words), -1, PREG_SPLIT_NO_EMPTY)); 
	$words = array_filter(array_map(null, array_values($words), array_keys($words)), 'longer_than_x_letters'); 
	usort($words,'sort_words'); 
	$top_x_words = array_slice($words, 0, $number_of_keywords);
	$end_keywords = "";
	foreach ($top_x_words as $key => $value) { 
		foreach ($value as $key1 => $value1) { 
   			$end_keywords .= $value1.' '; 
		}
	}

	$numbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  	$string_words = str_replace($numbers, '', $end_keywords);

	$trimmed_words = trim($string_words);
	$trimmed_title = trim($new_string_title);
	$trimmed_category = trim($new_string_category);

	$end_result = strtolower($trimmed_title.', '.$trimmed_category.', '.$trimmed_words.', '.$general_append_keys);
	$end_result = preg_replace("/([,.?!])/"," \\1",$end_result); 
	$parts = explode(" ",$end_result); 
	$unique = array_unique($parts); 
	$unique = implode(" ",$unique); 
	$unique = preg_replace("/\s([,.?!])/","\\1",$unique); 
	$unique = str_replace('  ', ' ', $unique);
  	$unique = str_replace(' ', ', ', $unique);
  	$unique = str_replace(',,', ',', $unique);

	if ($general_append_keys_phrase != "") {
  		$unique = $unique.', '.$general_append_keys_phrase;
	}
	if ($append_prod_name_phrase == "Y") {
  		$unique = $unique.', '.strtolower(str_replace('"', '', strip_tags( $db_product->f('product_name'))));
	}
	if ($append_par_cat_name_phrase == "Y") {
  		$unique = $unique.', '.strtolower(str_replace('"', '', strip_tags( $get_category)));
	}

    	$document->setMetaData('keywords',$unique);
}

// Set All other Tags if Filled
if ($generator) {
	$content = $generator;
	$document->setGenerator($content);
}
if ($subject == "Y") {
	$name = "subject";
	$content = $db_product->f('product_name');
	$document->setMetaData($name, $content);
}
if ($classification) {
	$name = "classification";
	$content = $classification;
	$document->setMetaData($name, $content);
}
if ($author) {
	$name = "author";
	$content = $author;
	$document->setMetaData($name, $content);
}
if ($organization) {
	$name = "organization";
	$content = $organization;
	$document->setMetaData($name, $content);
}
if ($copyright) {
	$name = "copyright";
	$content = $copyright;
	$document->setMetaData($name, $content);
}
if ($country) {
	$name = "country";
	$content = $country;
	$document->setMetaData($name, $content);
}
if ($content_language) {
	$name = "content-language";
	$content = $content_language;
	$document->setMetaData($name, $content, true);
}
if ($language) {
	$name = "language";
	$content = $language;
	$document->setMetaData($name, $content);
}
if ($designer) {
	$name = "designer";
	$content = $designer;
	$document->setMetaData($name, $content);
}
if ($comments) {
	$name = "comments";
	$content = $generator;
	$document->setMetaData($name, $content);
}
if ($no_email_collection == "Y") {
	$name = "no-email-collection";
	$content = "http://www.unspam.com/noemailcollection";
	$document->setMetaData($name, $content);
}
//END HACK METADATA FOR PRODUCT DETAILS PAGE 



// Show an "Edit PRODUCT"-Link
if ($perm->check("admin,storeadmin")) {
	$edit_link = '<a href="'. $sess->url( 'index2.php?page=product.product_form&next_page=shop.product_details&product_id='.$product_id).'">
      <img src="images/M_images/edit.png" alt="'. $VM_LANG->_('PHPSHOP_PRODUCT_FORM_EDIT_PRODUCT') .'" border="0" /></a>';
}
else {
	$edit_link = "";
}

// LINK TO MANUFACTURER POP-UP
$manufacturer_id = $ps_product->get_manufacturer_id($product_id);
$manufacturer_name = $ps_product->get_mf_name($product_id);
$manufacturer_link = "";
if( $manufacturer_id && !empty($manufacturer_name) ) {
	$link = "$mosConfig_live_site/index2.php?page=shop.manufacturer_page&amp;manufacturer_id=$manufacturer_id&amp;output=lite&amp;option=com_virtuemart&amp;Itemid=".$Itemid;
	$text = '( '.$manufacturer_name.' )';
	$manufacturer_link .= vmPopupLink( $link, $text );

	// Avoid JavaScript on PDF Output
	if( @$_REQUEST['output'] == "pdf" )
	$manufacturer_link = "<a href=\"$link\" target=\"_blank\" title=\"$text\">$text</a>";
}
// PRODUCT PRICE
if (_SHOW_PRICES == '1') { 
	if( $db_product->f("product_unit") && VM_PRICE_SHOW_PACKAGING_PRICELABEL) {
		$product_price_lbl = "<strong>". $VM_LANG->_('PHPSHOP_CART_PRICE_PER_UNIT').' ('.$db_product->f("product_unit")."):</strong>";
	}
	else {
		$product_price_lbl = "<strong>". $VM_LANG->_('PHPSHOP_CART_PRICE'). ": </strong>";
	}
	$product_price = $ps_product->show_price( $product_id );
}
else {
	$product_price_lbl = "";
	$product_price = "";
}
// @var array $product_price_raw The raw unformatted Product Price in Float Format
$product_price_raw = $ps_product->get_adjusted_attribute_price($product_id);
		
// Change Packaging - Begin
// PRODUCT PACKAGING
if (  $db_product->f("product_packaging") ) {
	$packaging = $db_product->f("product_packaging") & 0xFFFF;
	$box = ($db_product->f("product_packaging") >> 16) & 0xFFFF;
	$product_packaging = "";
	if ( $packaging ) {
		$product_packaging .= $VM_LANG->_('PHPSHOP_PRODUCT_PACKAGING1').$packaging;
		if( $box ) $product_packaging .= "<br/>";
	}
	if ( $box ) {
		$product_packaging .= $VM_LANG->_('PHPSHOP_PRODUCT_PACKAGING2').$box;
	}

	$product_packaging = str_replace("{unit}",$db_product->f("product_unit")?$db_product->f("product_unit") : $VM_LANG->_('PHPSHOP_PRODUCT_FORM_UNIT_DEFAULT'),$product_packaging);
}
else {
	$product_packaging = "";
}
// Change Packaging - End

// PRODUCT IMAGE
$product_full_image = $product_parent_id!=0 && !$db_product->f("product_full_image") ?
$dbp->f("product_full_image") : $db_product->f("product_full_image"); // Change
$product_thumb_image = $product_parent_id!=0 && !$db_product->f("product_thumb_image") ?
$dbp->f("product_thumb_image") : $db_product->f("product_thumb_image"); // Change

/* MORE IMAGES ??? */
$files = ps_product_files::getFilesForProduct( $product_id );

$more_images = "";
if( !empty($files['images']) ) {
	$more_images = $tpl->vmMoreImagesLink( $files['images'] );
}
// Does the Product have files?
$file_list = ps_product_files::get_file_list( $files['product_id'] );

$product_availability = '';

if( @$_REQUEST['output'] != "pdf" ) {
	// Show the PDF, Email and Print buttons
	$tpl->set('option', $option);
	$tpl->set('category_id', $category_id );
	$tpl->set('product_id', $product_id );
	$buttons_header = $tpl->fetch( 'common/buttons.tpl.php' );
	$tpl->set( 'buttons_header', $buttons_header );

	// AVAILABILITY 
	// This is the place where it shows: Availability: 24h, In Stock: 5 etc.
	// You can make changes to this functionality in the file: classes/ps_product.php
	$product_availability = $ps_product->get_availability($product_id);
}
$product_availability_data = $ps_product->get_availability_data($product_id);

/** Ask seller a question **/
$ask_seller_href = $sess->url( $_SERVER ['PHP_SELF'].'?page=shop.ask&amp;flypage='.@$_REQUEST['flypage']."&amp;product_id=$product_id&amp;category_id=$category_id" );
$ask_seller_text = $VM_LANG->_('VM_PRODUCT_ENQUIRY_LBL');
$ask_seller = '<a class="button" href="'. $ask_seller_href .'">'. $ask_seller_text .'</a>';

/* SHOW RATING */
$product_rating = "";
if (PSHOP_ALLOW_REVIEWS == '1') {
	$product_rating = ps_reviews::allvotes( $product_id );
}

$product_reviews = $product_reviewform = "";
/* LIST ALL REVIEWS **/
if (PSHOP_ALLOW_REVIEWS == '1') {
	/*** Show all reviews available ***/
	$product_reviews = ps_reviews::product_reviews( $product_id );
	/*** Show a form for writing a review ***/

	if( $auth['user_id'] > 0 ) {
		$product_reviewform = ps_reviews::reviewform( $product_id );
	}
}

/* LINK TO VENDOR-INFO POP-UP **/
$vend_id = $ps_product->get_vendor_id($product_id);
$vend_name = $ps_product->get_vendorname($product_id);

$link = "$mosConfig_live_site/index2.php?page=shop.infopage&amp;vendor_id=$vend_id&amp;output=lite&amp;option=com_virtuemart&amp;Itemid=".$Itemid;
$text = $VM_LANG->_('PHPSHOP_VENDOR_FORM_INFO_LBL');
$vendor_link = vmPopupLink( $link, $text );

// Avoid JavaScript on PDF Output
if( @$_REQUEST['output'] == "pdf" )
$vendor_link = "<a href=\"$link\" target=\"_blank\" title=\"$text\">$text</a>";

if ($product_parent_id!=0 && !$ps_product_type->product_in_product_type($product_id)) {
	$product_type = $ps_product_type->list_product_type($product_parent_id);
}
else {
	$product_type = $ps_product_type->list_product_type($product_id);
}


$recent_products = $ps_product->recentProducts($product_id,$tpl->get_cfg('showRecent', 5));
/**
* This has changed since VM 1.1.0  
* Now we have a template object that can use all variables 
* that we assign here.
* 
* Example: If you run
* $tpl->set( "product_name", $product_name );
* The variable "product_name" will be available in the template under this name
* with the value of $product_name
* 
* */

// This part allows us to copy ALL properties from the product table
// into the template
$productData = $db_product->get_row();
$productArray = get_object_vars( $productData );

$productArray["product_id"] = $product_id;
$productArray["product_full_image"] = $product_full_image; // to display the full image on flypage
$productArray["product_thumb_image"] = $product_thumb_image;
$productArray["product_name"] = shopMakeHtmlSafe($productArray["product_name"]);

$tpl->set( 'productArray', $productArray );
foreach( $productArray as $property => $value ) {
	$tpl->set( $property, $value);
}
// Assemble the thumbnail image as a link to the full image
// This function is defined in the theme (theme.php)
$product_image = $tpl->vmBuildFullImageLink( $productArray );

$tpl->set( "product_id", $product_id );
$tpl->set( "product_name", $product_name );
$tpl->set( "product_image", $product_image );
$tpl->set( "more_images", $more_images );
$tpl->set( "images", $files['images'] );
$tpl->set( "files", $files['files'] );
$tpl->set( "file_list", $file_list );
$tpl->set( "edit_link", $edit_link );
$tpl->set( "manufacturer_link", $manufacturer_link );
$tpl->set( "product_price", $product_price );
$tpl->set( "product_price_lbl", $product_price_lbl );
$tpl->set( 'product_price_raw', $product_price_raw );
$tpl->set( "product_description", $product_description );

/* ADD-TO-CART */
$tpl->set( 'manufacturer_id', $manufacturer_id );
$tpl->set( 'flypage', $flypage );
$tpl->set( 'ps_product_attribute', $ps_product_attribute );
$addtocart = $tpl->fetch('product_details/includes/addtocart_form.tpl.php' );

$tpl->set( "addtocart", $addtocart );
// Those come from separate template files
$tpl->set( "navigation_pathway", $navigation_pathway );
$tpl->set( "navigation_childlist", $navigation_childlist );
$tpl->set( "product_reviews", $product_reviews );
$tpl->set( "product_reviewform", $product_reviewform );
$tpl->set( "product_availability", $product_availability );
$tpl->set( "product_availability_data", $product_availability_data );

$tpl->set( "related_products", $related_products );
$tpl->set( "vendor_link", $vendor_link );
$tpl->set( "product_type", $product_type ); // Changed Product Type
$tpl->set( "product_packaging", $product_packaging ); // Changed Packaging
$tpl->set( "ask_seller_href", $ask_seller_href ); // Product Enquiry!
$tpl->set( "ask_seller_text", $ask_seller_text ); // Product Enquiry!
$tpl->set( "ask_seller", $ask_seller ); // Product Enquiry!
$tpl->set( "recent_products", $recent_products); // Recent products
$tpl->set( "category_id", $category_id);
/* Finish and Print out the Page */
echo $tpl->fetch( '/product_details/'.$flypage . '.php' );

?>

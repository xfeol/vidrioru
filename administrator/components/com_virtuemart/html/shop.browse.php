<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* This is the Main Product Listing File!
*
* @version $Id: shop.browse.php 1847 2009-07-11 13:15:14Z tkahl $
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

// load important class files
require_once (CLASSPATH."ps_product.php");
$ps_product = new ps_product;
require_once (CLASSPATH."ps_product_category.php");
$ps_product_category = new ps_product_category;
require_once (CLASSPATH."ps_product_files.php");
require_once (CLASSPATH."ps_reviews.php");
require_once (CLASSPATH."imageTools.class.php");
require_once (CLASSPATH."PEAR/Table.php");
require_once(CLASSPATH . 'ps_product_attribute.php' );
$ps_product_attribute = new ps_product_attribute;


$Itemid = $sess->getShopItemid();
$keyword1 = $vmInputFilter->safeSQL( urldecode(vmGet( $_REQUEST, 'keyword1', null )));
$keyword2 = $vmInputFilter->safeSQL( urldecode(vmGet( $_REQUEST, 'keyword2', null )));

$search_op= $vmInputFilter->safeSQL( vmGet( $_REQUEST, 'search_op', null ));
$search_limiter= $vmInputFilter->safeSQL( vmGet( $_REQUEST, 'search_limiter', null ));

if (empty($category_id)) $category_id = $search_category;

$default['category_flypage'] = FLYPAGE;

$db_browse = new ps_DB;
$dbp = new ps_DB;

function getCategoryChildsList( $clist, $catid) {
    $res_list = array($catid);
    $search_childs = true;
    while ( $search_childs) {
	$search_childs = false;
	foreach($clist as $c_catid => $val) {
	    if (in_array($val['category_parent_id'], $res_list)) {
		$res_list[] = $c_catid;
		unset( $clist[$c_catid] );
		$search_childs = true;
	    }
	}
    }
    return $res_list;
}
$category_childs = '';
if ($category_id && $ps_product_category->has_childs($category_id)) {
    $category_childs = getCategoryChildsList($ps_product_category->getCategoryTreeArray(), $category_id);
}

// NEW: Include the query section from an external file
// If settings are loaded, extended Classes are allowed and the user_class/shop_browse_queries.php exisits...
if (!defined('VM_ALLOW_EXTENDED_CLASSES') && file_exists(dirname(__FILE__).'/../virtuemart.cfg.php')) include_once(dirname(__FILE__).'/../virtuemart.cfg.php');
if (defined('VM_ALLOW_EXTENDED_CLASSES') && defined('VM_THEMEPATH') && VM_ALLOW_EXTENDED_CLASSES && file_exists(VM_THEMEPATH.'user_class/shop_browse_queries.php')) {
	// Load the theme-user_class shop_browse_queries.php
	require_once( VM_THEMEPATH.'user_class/shop_browse_queries.php' );
} else {
	require_once( PAGEPATH. "shop_browse_queries.php" );
}

$db_browse->query( $count );
$num_rows = $db_browse->f("num_rows");
if( $limitstart > 0 && $limit >= $num_rows) {
	$list = str_replace( 'LIMIT '.$limitstart, 'LIMIT 0', $list );
}

//START HACK METADATA FOR CATEGORY PAGE 
//if( $category_id ) {
	/**
    * CATEGORY DESCRIPTION
    */
//	$db->query( "SELECT category_id, category_name FROM #__{vm}_category WHERE category_id='$category_id'");
//	$db->next_record();
//	$category_name = shopMakeHtmlSafe( $db->f('category_name') );

	// Set Dynamic Page Title
//	$vm_mainframe->setPageTitle( $db->f("category_name") );

//	$desc =  $ps_product_category->get_description($category_id);
//	$desc = vmCommonHTML::ParseContentByPlugins( $desc );
	// Prepend Product Short Description Meta Tag "description" when applicable
//	$mainframe->prependMetaTag( "description", substr(strip_tags($desc ), 0, 255) );

//}



if( $category_id ) {

$document	=& JFactory::getDocument();

$dbmc = new ps_DB;
$q = "SELECT banned, remove_less_than, number_of_keywords, append_category, general_append_end, general_append_end_title, general_append_keys, general_append_keys_phrase, append_subcategory_title, append_subcategory_description, prepend_cat_name_phrase, prepend_par_cat_name_phrase, turn_on_edit, 
add_abstract, generator, subject, classification, author, organization, copyright, country, content_language, language, designer, comments, 
no_email_collection FROM #__{vm}_category_metakeys WHERE mid!='2'";
$dbmc->query( $q );
$dbmc->next_record();

$banned = $dbmc->f("banned");
$remove_less_than = intval($dbmc->f("remove_less_than"));
$number_of_keywords = intval($dbmc->f("number_of_keywords"));
$append_category = $dbmc->f("append_category");
$general_append_end = $dbmc->f("general_append_end");
$general_append_end_title = $dbmc->f("general_append_end_title");
$general_append_keys = $dbmc->f("general_append_keys");
$general_append_keys_phrase = $dbmc->f("general_append_keys_phrase");
$append_subcategory_title = $dbmc->f("append_subcategory_title");
$append_subcategory_description = $dbmc->f("append_subcategory_description");
$prepend_cat_name_phrase = $dbmc->f("prepend_cat_name_phrase");
$prepend_par_cat_name_phrase = $dbmc->f("prepend_par_cat_name_phrase");
$turn_on_edit = $dbmc->f("turn_on_edit");
$add_abstract = $dbmc->f("add_abstract");
$generator = $dbmc->f("generator");
$subject = $dbmc->f("subject");
$classification = $dbmc->f("classification");
$author = $dbmc->f("author");
$organization = $dbmc->f("organization");
$copyright = $dbmc->f("copyright");
$country = $dbmc->f("country");
$content_language = $dbmc->f("content_language");
$language = $dbmc->f("language");
$designer = $dbmc->f("designer");
$comments = $dbmc->f("comments");
$no_email_collection = $dbmc->f("no_email_collection");


$dbcpx = new ps_DB;
$q = "SELECT category_parent_id FROM #__{vm}_category_xref WHERE category_child_id='$category_id'";
$dbcpx->query( $q );
$dbcpx->next_record();
$category_has_parent_id = $dbcpx->f('category_parent_id');
$category_has_parent_name = "";
$category_has_parent_description = "";
if ($category_has_parent_id == "0") {
	$category_has_parent_id = "";
}
if ($category_has_parent_id != "") {
	$dbcp = new ps_DB;
	$q = "SELECT category_name, category_description FROM #__{vm}_category WHERE category_id='$category_has_parent_id'";
	$dbcp->query( $q );
	$dbcp->next_record();
	$category_has_parent_name = $dbcp->f('category_name');
	$category_has_parent_description = $dbcp->f('category_description');
}

//echo "category_has_parent_id: " . $category_has_parent_id;

$sub_cats_names_list = "";
$dbsc = new ps_DB;
$q = "SELECT category_child_id FROM #__{vm}_category_xref WHERE category_parent_id='$category_id'";
$dbsc->query( $q );
while( $dbsc->next_record() ) {
	$has_subs_id = $dbsc->f('category_child_id');
	$dbscn = new ps_DB;
	$q1 = "SELECT category_name FROM #__{vm}_category WHERE category_id='$has_subs_id'";
	$dbscn->query( $q1 );
	while( $dbscn->next_record() ) {
		$sub_cats_names_list .= ", ".$dbscn->f('category_name');
	}
}

$product_names_list = "";
$dbcprodx = new ps_DB;
$q = "SELECT product_id FROM #__{vm}_product_category_xref WHERE category_id='$category_id'";
$dbcprodx->query( $q );
while( $dbcprodx->next_record() ) {
	$has_id = $dbcprodx->f('product_id');
	$dbcprod = new ps_DB;
	$q = "SELECT product_name FROM #__{vm}_product WHERE product_id='$has_id'";
	$dbcprod->query( $q );
	while( $dbcprod->next_record() ) {

		$product_names_list .= ' '.$dbcprod->f('product_name');
	}
}

$db->query( "SELECT * FROM #__{vm}_category WHERE category_id='$category_id'");
if (!$db->next_record())
{
    header('HTTP/1.0 404 Not Found');
    $vmLogger->err($VMLANG->_('PHPSHOP_PRODUCT_NOT_FOUND', false));
    return;
}

$GLOBALS['category_change_date'] = max($db->f("cdate"), $db->f("mdate"));

//Set Page Title
	$category_name = shopMakeHtmlSafe( $db->f('category_name') );
	
	/*Set Dynamic Page Title */
if (($db->f("category_title") != "") && ($turn_on_edit == "Y")) {
	if ($general_append_end_title != "") {
		if (($category_has_parent_id != "" ) && ($append_category == "Y")) {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.$category_has_parent_name.$sub_cats_names_list.' - '.$general_append_end_title)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.$category_has_parent_name.' - '.$general_append_end_title)));
			}	
		} else {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.substr($sub_cats_names_list, 2).' - '.$general_append_end_title)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.$general_append_end_title)));
			}
		}
	} else {
		if (($category_has_parent_id != "" ) && ($append_category == "Y")) {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.$category_has_parent_name.$sub_cats_names_list)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.$category_has_parent_name)));
			}
		} else {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title').' - '.substr($sub_cats_names_list, 2))));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_title'))));
			}
		}
	}
} else {
	if ($general_append_end_title != "") {
		if (($category_has_parent_id != "" ) && ($append_category == "Y")) {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.$category_has_parent_name.$sub_cats_names_list.' - '.$general_append_end_title)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.$category_has_parent_name.' - '.$general_append_end_title)));
			}
		} else {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.substr($sub_cats_names_list, 2).' - '.$general_append_end_title)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.$general_append_end_title)));
			}
		}
	} else {
		if (($category_has_parent_id != "" ) && ($append_category == "Y")) {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.$category_has_parent_name.$sub_cats_names_list)));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.$category_has_parent_name)));
			}
		} else {
			if ($append_subcategory_title == "Y") {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name').' - '.substr($sub_cats_names_list, 2))));
			} else {
				$document->setTitle(str_replace('"', '', strip_tags( $db->f('category_name'))));
			}
		}
	}
}

// Set Description Metatag
	$desc =  $ps_product_category->get_description($category_id);
	$desc = vmCommonHTML::ParseContentByPlugins( $desc );
	$short_desc = substr(strip_tags($desc ), 0, 255);
	if (($db->f("category_metadesc") != "") && ($turn_on_edit == "Y")) {
		if ($general_append_end != "") {
			if ($append_subcategory_description == "Y") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db->f("category_metadesc").' - '.substr($sub_cats_names_list, 2).' - '.$general_append_end)));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db->f("category_metadesc").' - '.$general_append_end)));
			}
		} else {
			if ($append_subcategory_description == "Y") {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db->f("category_metadesc").' - '.substr($sub_cats_names_list, 2))));
			} else {
				$document->setMetaData('description', str_replace('"', '', strip_tags($db->f("category_metadesc"))));
			}
		}
	} else {
		if ($desc != "") {
			if ($general_append_end != "") {
				if (($category_has_parent_name != "") && ($append_category == "Y")) {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.$short_desc.' '.substr($sub_cats_names_list, 2).' - '.$general_append_end));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.$short_desc.' - '.$general_append_end));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				} else {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$short_desc.' '.substr($sub_cats_names_list, 2).' - '.$general_append_end));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$short_desc.'. '.$general_append_end));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				}
			} else {
				if (($category_has_parent_name != "") && ($append_category == "Y")) {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.$short_desc.' '.substr($sub_cats_names_list, 2)));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.$short_desc));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				} else {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$short_desc.' '.substr($sub_cats_names_list, 2)));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$short_desc));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				}
			}
		} else {
			if ($general_append_end != "") {
				if (($category_has_parent_name != "") && ($append_category == "Y")) {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.substr($sub_cats_names_list, 2).' - '.$general_append_end));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.$general_append_end));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				} else {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").'. '.substr($sub_cats_names_list, 2).' - '.$general_append_end));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$general_append_end));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				}
			} else {
				if (($category_has_parent_name != "") && ($append_category == "Y")) {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name.'. '.substr($sub_cats_names_list, 2)));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").' - '.$category_has_parent_name));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				} else {
					if ($append_subcategory_description == "Y") {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name").'. '.substr($sub_cats_names_list, 2)));
					} else {
						$is_meta_desc = str_replace('"', '', strip_tags($db->f("category_name")));
					}
					$meta_desc_legnth = strlen($is_meta_desc);
				}
			}
		}
		if (($category_has_parent_description != "") && ($meta_desc_legnth < '255')) {
			$clean_cat_par_desc = str_replace('"', '', strip_tags($category_has_parent_description));
			$is_meta_desc = substr($is_meta_desc.' '.$clean_cat_par_desc, 0, 255);
		}
		$document->setMetaData('description', $is_meta_desc);
	}

// Add and Set Abstract Metatag?
	if ($add_abstract == "Y") {
		if ($turn_on_edit == "Y") {
			$name = "abstract";
			$content = strip_tags($db->f("category_abstract"));
			$document->setMetaData($name, $content);
		} else {
			$name = "abstract";
			if ($desc != "") {
				$content = str_replace('"', '', strip_tags($db->f("category_name").'. '.$short_desc));
			} else {
				$content = str_replace('"', '', strip_tags($db->f("category_name")));
			}
			$document->setMetaData($name, $content);
		}
	}

// Add Canonical?
	if ($db->f("category_canonical")) {
		$href = str_replace('&amp;', '&', $db->f('category_canonical')); 
		$document->addHeadLink( $href, 'canonical', 'rel', '' );
	}

// Set Keywords Metatag
if (($db->f("category_metakey") != "") && ($turn_on_edit == "Y")) {
	if ($general_append_keys != "") {
		if ($general_append_keys_phrase != "") {
			$document->setMetaData('keywords',strip_tags($db->f("category_metakey").', '.$general_append_keys.', '.$general_append_keys_phrase));
		} else {
			$document->setMetaData('keywords',strip_tags($db->f("category_metakey").', '.$general_append_keys));
		}
	} else {
		if ($general_append_keys_phrase != "") {
			$document->setMetaData('keywords',strip_tags($db->f("category_metakey").', '.$general_append_keys_phrase));
		} else {
        		$document->setMetaData('keywords',strip_tags($db->f("category_metakey")));
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

	$get_title = ucwords(strip_tags($db->f("category_name").' '.$category_has_parent_name.' '.$sub_cats_names_list));
	$get_words = ucwords(strip_tags($desc.' '.$category_has_parent_description.' '.$product_names_list));

	$clean_title = clean_text($get_title);
	$clean_words = clean_text($get_words);

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

	$trimmed_title = trim($new_string_title);
	$trimmed_words = trim($string_words);

	$end_result = strtolower($trimmed_title.', '.$trimmed_words.', '.$general_append_keys);
	$end_result = preg_replace("/([,.?!])/"," \\1",$end_result); 
	$parts = explode(" ",$end_result); 
	$unique = array_unique($parts); 
	$unique = implode(" ",$unique); 
	$unique = preg_replace("/\s([,.?!])/","\\1",$unique); 
	$unique = str_replace('  ', ' ', $unique);

	$explode = explode(' ', $unique); 
	foreach ($explode as $key => $value) { 
    		if (strlen($value) < 2) {
			unset($explode[$key]);
		} 
	} 
	$unique = implode(' ', $explode); 

  	$unique = str_replace(' ', ', ', $unique);
  	$unique = str_replace(',,', ',', $unique);

	if ($general_append_keys_phrase != "") {
  		$unique = $unique.', '.$general_append_keys_phrase;
	}
	if ($prepend_par_cat_name_phrase == "Y") {
		if ($category_has_parent_id != "" ) {
  			$unique = str_replace('"', '', strtolower(strip_tags( $category_has_parent_name))).', '.$unique;
		}
	}
	if ($prepend_cat_name_phrase == "Y") {
  		$unique = str_replace('"', '', strtolower(strip_tags( $db->f('category_name')))).', '.$unique;
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
		$content = $db->f('category_name');
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
	if ($content_language)
	{
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

}
//END HACK METADATA FOR CATEGORY PAGE 


// when nothing has been found we tell this here and say goodbye
if ($num_rows == 0 && (!empty($keyword)||!empty($keyword1))) {
	echo $VM_LANG->_('PHPSHOP_NO_SEARCH_RESULT');
}
elseif( $num_rows == 0 && empty($product_type_id) && !empty($child_list)) {
	echo $VM_LANG->_('EMPTY_CATEGORY');
}

elseif( $num_rows == 1 && ( !empty($keyword) || !empty($keyword1) ) ) {
	// If just one product has been found, we directly show the details page of it
	$db_browse->query( $list );
	$db_browse->next_record();
	$flypage = $db_browse->sf("category_flypage") ? $db_browse->sf("category_flypage") : FLYPAGE;

	$url_parameters = "page=shop.product_details&amp;flypage=$flypage&amp;product_id=" . $db_browse->f("product_id") . "&amp;category_id=" . $db_browse->f("category_id");
	vmRedirect( $sess->url($url_parameters, true, false ) );
}
else {
	// NOW START THE PRODUCT LIST
	$tpl = vmTemplate::getInstance();

	if( $category_id ) {
		/**
	    * CATEGORY DESCRIPTION
	    */
		$browsepage_lbl = $category_name;
		$tpl->set( 'browsepage_lbl', $browsepage_lbl );

		$tpl->set( 'desc', $desc );

		$category_childs = $ps_product_category->get_child_list($category_id);
		$tpl->set( 'categories', $category_childs );
		$navigation_childlist = $tpl->fetch( 'common/categoryChildlist.tpl.php');
		$tpl->set( 'navigation_childlist', $navigation_childlist );

		// Set up the CMS pathway
		$category_list = array_reverse( $ps_product_category->get_navigation_list($category_id) );
		$pathway = $ps_product_category->getPathway( $category_list );
		$vm_mainframe->vmAppendPathway( $pathway );

		$tpl->set( 'category_id', $category_id );
		$tpl->set( 'category_name', $category_name );

		$browsepage_header = $tpl->fetch( 'browse/includes/browse_header_category.tpl.php' );

	}
	elseif( $manufacturer_id) {
		$db->query( "SELECT manufacturer_id, mf_name, mf_desc FROM #__{vm}_manufacturer WHERE manufacturer_id='$manufacturer_id'");
		$db->next_record();
		$mainframe->setPageTitle( $db->f("mf_name") );

		$browsepage_lbl = shopMakeHtmlSafe( $db->f("mf_name") );
		$tpl->set( 'browsepage_lbl', $browsepage_lbl );
		$browsepage_lbltext = $db->f("mf_desc");
		$tpl->set( 'browsepage_lbltext', $browsepage_lbltext );
		$browsepage_header = $tpl->fetch( 'browse/includes/browse_header_manufacturer.tpl.php' );
	}
	elseif( $keyword ) {
		$mainframe->setPageTitle( $VM_LANG->_('PHPSHOP_SEARCH_TITLE',false) );
		$browsepage_lbl = $VM_LANG->_('PHPSHOP_SEARCH_TITLE') .': '.shopMakeHtmlSafe( $keyword );
		$tpl->set( 'browsepage_lbl', $browsepage_lbl );

		$browsepage_header = $tpl->fetch( 'browse/includes/browse_header_keyword.tpl.php' );
	}
	else {
		$mainframe->setPageTitle( $VM_LANG->_('PHPSHOP_BROWSE_LBL',false) );#
		$browsepage_lbl = $VM_LANG->_('PHPSHOP_BROWSE_LBL');
		$tpl->set( 'browsepage_lbl', $browsepage_lbl );

		$browsepage_header = $tpl->fetch( 'browse/includes/browse_header_all.tpl.php' );
	}
	$tpl->set( 'browsepage_header', $browsepage_header );

	if (!empty($product_type_id) && @$_REQUEST['output'] != "pdf") {
		$tpl->set( 'ps_product_type', $ps_product_type);
		$tpl->set( 'product_type_id', $product_type_id);
		$parameter_form = $tpl->fetch( 'browse/includes/browse_searchparameter_form.tpl.php' );
	}
	else {
		$parameter_form = '';
	}
	$tpl->set( 'parameter_form', $parameter_form );

	// Decide whether to show the limit box
	$show_limitbox = ( $num_rows > 5 && @$_REQUEST['output'] != "pdf" );
	$tpl->set( 'show_limitbox', $show_limitbox );

	// Decide whether to show the top navigation
	$show_top_navigation = ( PSHOP_SHOW_TOP_PAGENAV =='1' && $num_rows > $limit );
	$tpl->set( 'show_top_navigation', $show_top_navigation );

	// Prepare Page Navigation
	require_once( CLASSPATH . 'pageNavigation.class.php' );
	$pagenav = new vmPageNav( $num_rows, $limitstart, $limit );
	$tpl->set( 'pagenav', $pagenav );

	$search_string = '';
	if ( $num_rows > 1 && @$_REQUEST['output'] != "pdf") {
		if ( $num_rows > 5 ) { // simplified logic
			$search_string = $mm_action_url."index.php?option=com_virtuemart&amp;Itemid=$Itemid&amp;category_id=$category_id&amp;page=$modulename.browse";
			$search_string .= empty($manufacturer_id) ? '' : "&amp;manufacturer_id=$manufacturer_id";
			$search_string .= empty($keyword) ? '' : '&amp;keyword='.urlencode( $keyword );
			if (!empty($keyword1)) {
				$search_string.="&amp;keyword1=".urlencode($keyword1);
				$search_string.="&amp;search_category=".urlencode($search_category);
				$search_string.="&amp;search_limiter=$search_limiter";
				if (!empty($keyword2)) {
					$search_string.="&amp;keyword2=".urlencode($keyword2);
					$search_string.="&amp;search_op=".urlencode($search_op);
				}
			}

			if (!empty($product_type_id)){
				foreach($_REQUEST as $key => $value){
					if (substr($key, 0,13) == "product_type_"){
						$val = vmGet($_REQUEST, $key );
						if( is_array( $val )) {
							foreach( $val as $var ) {
								$search_string .="&".$key."[]=".urlencode($var);
							}
						} else {
							$search_string .="&".$key."=".urlencode($val);
						}
					}
				}
			}

		}

		//$search_string = $sess->url($search_string);
		$tpl->set( 'VM_BROWSE_ORDERBY_FIELDS', $VM_BROWSE_ORDERBY_FIELDS);

	    if ($DescOrderBy == "DESC") {
	        $icon = "sort_desc.png";
	        $selected = Array( "selected=\"selected\"", "" );
		  	$asc_desc = Array( "DESC", "ASC" );
		}
		else {
		  	$icon = "sort_asc.png";
	        $selected = Array( "", "selected=\"selected\"" );
	        $asc_desc = Array( "ASC", "DESC" );
	    }
		$tpl->set( 'orderby', $orderby );
		$tpl->set( 'icon', $icon );
		$tpl->set( 'selected', $selected );
		$tpl->set( 'asc_desc', $asc_desc );
		$tpl->set( 'category_id', $category_id );
		$tpl->set( 'manufacturer_id', $manufacturer_id );
		$tpl->set( 'keyword', urlencode( $keyword ) );
		$tpl->set( 'keyword1', urlencode( $keyword1 ) );
		$tpl->set( 'keyword2', urlencode( $keyword2 ) );
		$tpl->set( 'Itemid', $Itemid );

		if( $show_top_navigation ) {
			$tpl->set( 'search_string', $search_string );
		}

		$orderby_form = $tpl->fetch( 'browse/includes/browse_orderbyform.tpl.php' );
		$tpl->set( 'orderby_form', $orderby_form );
    }
    else {
    	$tpl->set( 'orderby_form', '' );
    }

	$db_browse->query( $list );
	$db_browse->next_record();

	$products_per_row = (!empty($category_id)) ? $db_browse->f("products_per_row") : PRODUCTS_PER_ROW;

	if( $products_per_row < 1 ) {
		$products_per_row = 1;
	}
	$buttons_header = '';
	/**
	 *   Start caching all product details for a later loop
	 *
	 **/
	if(@$_REQUEST['output'] != "pdf") {

		// Show the PDF, Email and Print buttons
		$tpl->set('option', $option);
		$tpl->set('category_id', $category_id );
		$tpl->set('product_id', $product_id );
		$buttons_header = $tpl->fetch( 'common/buttons.tpl.php' );

		$templatefile = (!empty($category_id)) ? $db_browse->f("category_browsepage") : CATEGORY_TEMPLATE;
		if( $templatefile == 'managed' ) {
			// automatically select the browse template with the best match for the number of products per row
			$templatefile = file_exists(VM_THEMEPATH.'templates/browse/browse_'.$products_per_row.'.php' )
								? 'browse_'.$products_per_row
								: 'browse_5';
		} elseif( !file_exists(VM_THEMEPATH.'templates/browse/'.$templatefile.'.php')) {
			$templatefile = 'browse_5';
		}
	}
	else {
		$templatefile = "browse_lite_pdf";
	}

	$tpl->set( 'buttons_header', $buttons_header );

	$tpl->set('products_per_row', $products_per_row );
	$tpl->set('templatefile', $templatefile );

	if ($num_rows)
	{
	    $db_browse->reset();
	
	    $product_id_set = '';
	    $GLOBALS['product_id_set'] = array();

	    while ($db_browse->next_record()) {
		$pid = $db_browse->f("product_id");
		$product_id_set .= "$pid,";
		$GLOBALS['product_id_set'][] = $pid;
	    }
	    $product_id_set = substr($product_id_set, 0, -1);
	    $ps_product->get_fieldALL($product_id_set);
	}
	
	$db_browse->reset();

	$products = array();
	$counter = 0;
	/*** Start printing out all products (in that category) ***/
	while ($db_browse->next_record()) {

		// If it is item get parent:
		$product_parent_id = $db_browse->f("product_parent_id");
		if ($product_parent_id != 0) {
			$dbp->query("SELECT product_full_image,product_thumb_image,product_name,product_s_desc FROM #__{vm}_product WHERE product_id='$product_parent_id'" );
			$dbp->next_record();
		}

		// Set the flypage for this product based on the category.
		// If no flypage is set then use the default as set in virtuemart.cfg.php
		$flypage = $db_browse->sf("category_flypage");

		$has_attributes = ps_product::product_has_attributes( $db_browse->f('product_id'), false);

		if (empty($flypage)) {
            $flypage = FLYPAGE;
        }
        $url_parameters = "page=shop.product_details&amp;flypage=$flypage&amp;product_id=" . $db_browse->f("product_id") . "&amp;category_id=" . $db_browse->f("category_id");
        if( $manufacturer_id ) {
        	$url_parameters .= "&amp;manufacturer_id=" . $manufacturer_id;
        }
        if( $keyword != '') {
        	$url_parameters .= "&amp;keyword=".urlencode($keyword);
        }
        $url = $sess->url( $mm_action_url.'?'.$url_parameters );

        // Price: xx.xx EUR
		if (_SHOW_PRICES == '1' && $auth['show_prices']) {
			$product_price = $ps_product->show_price( $db_browse->f("product_id"), false, 1 );
		}
		else {
			$product_price = "";
		}
		// @var array $product_price_raw The raw unformatted Product Price in Float Format
		$product_price_raw = $ps_product->get_adjusted_attribute_price($db_browse->f('product_id'));

		// i is the index for the array holding all products, we need to show. to allow sorting by discounted price,
		// we need to use the price as first part of the index name!
		$i = $product_price_raw['product_price'] . '_' . ++$counter;

        if( $db_browse->f("product_thumb_image") ) {
            $product_thumb_image = $db_browse->f("product_thumb_image");
		}
		else {
			if( $product_parent_id != 0 ) {
				$product_thumb_image = $dbp->f("product_thumb_image"); // Use product_thumb_image from Parent Product
			}
			else {
				$product_thumb_image = 0;
			}
		}
		if( $product_thumb_image ) {
			if( substr( $product_thumb_image, 0, 4) != "http" ) {
				if(PSHOP_IMG_RESIZE_ENABLE == '1') {
					$product_thumb_image = $mosConfig_live_site."/components/com_virtuemart/shop_image/product/".$product_thumb_image;
					//$product_thumb_image = $mosConfig_live_site."/components/com_virtuemart/show_image_in_imgtag.php?filename=".urlencode($product_thumb_image)."&amp;newxsize=".PSHOP_IMG_WIDTH."&amp;newysize=".PSHOP_IMG_HEIGHT."&amp;fileout=";
				}
				elseif( !file_exists( IMAGEPATH."product/".$product_thumb_image )) {
                    $product_thumb_image = VM_THEMEURL.'images/'.NO_IMAGE;
                }
			}
		}
		else {
			$product_thumb_image = VM_THEMEURL.'images/'.NO_IMAGE;
		}

		// Get the full image path, or URL if set, or the no_image
		if( $db_browse->f("product_full_image") ) {
			$product_full_image = $db_browse->f("product_full_image");
		} elseif( $product_parent_id != 0 ) {
			$product_full_image = $dbp->f("product_full_image"); // Use product_full_image from Parent Product
		}
		else {
			$product_full_image = VM_THEMEURL . 'images/' . NO_IMAGE;

			// Get the size information for the no_image
			if( file_exists( VM_THEMEPATH . 'images/' . NO_IMAGE ) ) {
				$full_image_info = getimagesize( VM_THEMEPATH . 'images/' . NO_IMAGE );
				$full_image_width = $full_image_info[0]+40;
				$full_image_height = $full_image_info[1]+40;
			}
		}

		// Get image size information and add the full URL
		if( substr( $product_full_image, 0, 4) != 'http' ) {
			// This is a local image
			if( file_exists( IMAGEPATH . 'product/' . $product_full_image ) ) {
				$full_image_info = getimagesize( IMAGEPATH . 'product/' . $product_full_image );
				$full_image_width = $full_image_info[0]+40;
				$full_image_height = $full_image_info[1]+40;
			}

			$product_full_image = IMAGEURL . 'product/' . $product_full_image;
		} elseif( !isset( $full_image_width ) || !isset( $full_image_height ) ) {
			// This is a URL image
			$full_image_info = @getimagesize( $product_full_image );
			$full_image_width = $full_image_info[0]+40;
			$full_image_height = $full_image_info[1]+40;
		}

		$files = ps_product_files::getFilesForProduct( $db_browse->f('product_id') );
		$products[$i]['files'] = $files['files'];
		$products[$i]['images'] = $files['images'];

		$product_name = $db_browse->f("product_name");
		if( $db_browse->f("product_publish") == "N" ) {
			$product_name .= " (". $VM_LANG->_('CMN_UNPUBLISHED',false) .")";
		}

		if( empty($product_name) && $product_parent_id!=0 ) {
			$product_name = $dbp->f("product_name"); // Use product_name from Parent Product
		}
		$product_s_desc = $db_browse->f("product_s_desc");
		if( empty($product_s_desc) && $product_parent_id!=0 ) {
			$product_s_desc = $dbp->f("product_s_desc"); // Use product_s_desc from Parent Product
		}
		$product_details = $VM_LANG->_('PHPSHOP_FLYPAGE_LBL');

		if (PSHOP_ALLOW_REVIEWS == '1' && @$_REQUEST['output'] != "pdf") {
			// Average customer rating: xxxxx
	        // Total votes: x
			$product_rating = ps_reviews::allvotes( $db_browse->f("product_id") );
		}
		else {
			$product_rating = "";
		}

		// Add-to-Cart Button
		if (USE_AS_CATALOGUE != '1' && $product_price != ""
			&& $tpl->get_cfg( 'showAddtocartButtonOnProductList' )
			&& !stristr( $product_price, $VM_LANG->_('PHPSHOP_PRODUCT_CALL') )
			/*&& !ps_product::product_has_attributes( $db_browse->f('product_id'), true )*/) {


			$tpl->set( 'i', $i );
			$tpl->set( 'product_id', $db_browse->f('product_id') );
			$tpl->set( 'product_in_stock', $db_browse->f('product_in_stock') );
			$tpl->set( 'ps_product_attribute', $ps_product_attribute );
			$tpl->set( 'product_price', $product_price );
			$tpl->set( 'product_parent_id', $product_parent_id );
			$tpl->set( 'has_attributes', $has_attributes);
			$products[$i]['form_addtocart'] = $tpl->fetch( 'browse/includes/addtocart_form.tpl.php' );
			$products[$i]['has_addtocart'] = true;
		}
		else {
			$products[$i]['form_addtocart'] = '';
			$products[$i]['has_addtocart'] = false;
		}

		$products[$i]['product_flypage'] = $url;
		$products[$i]['product_thumb_image'] = $product_thumb_image;
		$products[$i]['product_full_image'] = $product_full_image;
		$products[$i]['full_image_width'] = $full_image_width;
		$products[$i]['full_image_height'] = $full_image_height;

		// Unset these for the next product
		unset($full_image_width);
		unset($full_image_height);

		$products[$i]['product_name'] = shopMakeHtmlSafe( $product_name );
		$products[$i]['has_attributes'] = $has_attributes;
		$products[$i]['product_s_desc'] = $product_s_desc;
		$products[$i]['product_details'] = $product_details;
		$products[$i]['product_rating'] = $product_rating;
		$products[$i]['product_price'] = $product_price;
		$products[$i]['product_price_raw'] = $product_price_raw;
		$products[$i]['product_sku'] = $db_browse->f("product_sku");
		$products[$i]['product_weight'] = $db_browse->f("product_weight");
		$products[$i]['product_weight_uom'] = $db_browse->f("product_weight_uom");
		$products[$i]['product_length'] = $db_browse->f("product_length");
		$products[$i]['product_width'] = $db_browse->f("product_width");
		$products[$i]['product_height'] = $db_browse->f("product_height");
		$products[$i]['product_lwh_uom'] = $db_browse->f("product_lwh_uom");
		$products[$i]['product_in_stock'] = $db_browse->f("product_in_stock");
		$products[$i]['product_available_date'] = $VM_LANG->convert( vmFormatDate($db_browse->f("product_available_date"), $VM_LANG->_('DATE_FORMAT_LC') ));
		$products[$i]['product_availability'] = $db_browse->f("product_availability");
		$products[$i]['cdate'] = $VM_LANG->convert( vmFormatDate($db_browse->f("cdate"), $VM_LANG->_('DATE_FORMAT_LC') ));
		$products[$i]['mdate'] = $VM_LANG->convert( vmFormatDate($db_browse->f("mdate"), $VM_LANG->_('DATE_FORMAT_LC') ));
		$products[$i]['product_url'] = $db_browse->f("product_url");
		$products[$i]['features'] = $db_browse->f("features");

	} // END OF while loop

	// Need to re-order here, because the browse query doesn't fetch discounts
	if( $orderby == 'product_price' ) {
		if ($DescOrderBy == "DESC") {
			// using krsort when the Array must be sorted reverse (Descending Order)
			krsort($products, SORT_NUMERIC);
		} else {
			// using ksort when the Array must be sorted in ascending order
			ksort($products, SORT_NUMERIC);
		}
	}
	$tpl->set( 'products', $products );
	$tpl->set( 'search_string', $search_string );

	if ( $num_rows > 1 ) {
		$browsepage_footer = $tpl->fetch( 'browse/includes/browse_pagenav.tpl.php' );
		$tpl->set( 'browsepage_footer', $browsepage_footer );
	} else {
		$tpl->set( 'browsepage_footer', '' );
	}


	$recent_products = $ps_product->recentProducts(null,$tpl->get_cfg('showRecent', 5));
	$tpl->set('recent_products',$recent_products);

	$tpl->set('ps_product',$ps_product);

	echo $tpl->fetch( $tpl->config->get( 'productListStyle' ) );
}
?>

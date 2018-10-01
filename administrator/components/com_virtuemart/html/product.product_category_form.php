<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @version $Id: product.product_category_form.php 1961 2009-10-12 20:18:00Z Aravot $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2007 soeren - All rights reserved.
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
global $ps_product_category, $ps_product;

$category_id = vmGet($_REQUEST, 'category_id', 0);
$option = empty($option)?vmGet( $_REQUEST, 'option', 'com_virtuemart'):$option;

//First create the object and let it print a form heading
$formObj = new formFactory( $VM_LANG->_('PHPSHOP_CATEGORY_FORM_LBL') );
//Then Start the form
$formObj->startForm( 'adminForm', 'enctype="multipart/form-data"');

if ($category_id) {
    $q = "SELECT * FROM #__{vm}_category,#__{vm}_category_xref ";
    $q .= "WHERE #__{vm}_category.category_id='$category_id' ";
    $q .= "AND #__{vm}_category_xref.category_child_id=#__{vm}_category.category_id";
    $db->query($q);
    $db->next_record();
} 
elseif (empty($vars["error"])) {
    $default["category_publish"] = "Y";
    $default["category_flypage"] = FLYPAGE;
    $default["category_browsepage"] = CATEGORY_TEMPLATE;
    $default["products_per_row"] = PRODUCTS_PER_ROW; 
}
  
$tabs = new vmTabPanel(0, 1, "categoryform");
$tabs->startPane("category-pane");
$tabs->startTab( "<img src='". IMAGEURL ."ps_image/edit.png' align='absmiddle' width='16' height='16' border='0' /> ".$VM_LANG->_('PHPSHOP_CATEGORY_FORM_LBL'), "info-page");
?> 
<table class="adminform">
    <tr> 
      <td width="21%" nowrap><div align="right"><?php echo $VM_LANG->_('PHPSHOP_CATEGORY_FORM_PUBLISH') ?>:</div></td>
      <td width="79%"><?php 
        if ($db->sf("category_publish")=="Y") { 
          echo "<input type=\"checkbox\" name=\"category_publish\" value=\"Y\" checked=\"checked\" />";
        } 
        else {
          echo "<input type=\"checkbox\" name=\"category_publish\" value=\"Y\" />";
        }
      ?> 
      </td>
    </tr>
    <tr> 
      <td width="21%" nowrap><div align="right"><?php echo $VM_LANG->_('PHPSHOP_CATEGORY_FORM_NAME') ?>:</div></td>
      <td width="79%"> 
        <input type="text" class="inputbox" name="category_name" size="60" value="<?php echo shopMakeHtmlSafe( $db->sf('category_name')) ?>" />
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" nowrap><div  align="right"><?php echo $VM_LANG->_('PHPSHOP_CATEGORY_FORM_DESCRIPTION') ?>:</div></td>
      <td width="79%" valign="top"><?php
        editorArea( 'editor1', $db->f("category_description"), 'category_description', '800', '300', '110', '40' ) ?>
      </td>
    </tr>
    <tr>
      <td ><div align="right"><?php echo $VM_LANG->_('PHPSHOP_MODULE_LIST_ORDER') ?>: </div></td>
      <td valign="top"><?php 
        echo $ps_product_category->list_level( $db->f("category_parent_id"), $db->f("category_id"), $db->f("list_order"));
        echo "<input type=\"hidden\" name=\"currentpos\" value=\"".$db->f("list_order")."\" />";
      ?>
      </td>
    </tr>
    <tr> 
      <td width="21%" valign="top" nowrap><div  align="right"><?php echo $VM_LANG->_('PHPSHOP_CATEGORY_FORM_PARENT') ?>:</div></td>
      <td width="79%" valign="top"> <?php 
          if (!$category_id) {
            $ps_product_category->list_all("parent_category_id", $category_id);
          }
          else {
            $ps_product_category->list_all("category_parent_id", $category_id);
          }
        echo "<input type=\"hidden\" name=\"current_parent_id\" value=\"".$db->f("category_parent_id")."\" />"; ?>
      </td>
    </tr>
    <tr>
      <td colspan="2"><br /></td>
    </tr>
    <tr>
      <td ><div align="right"><?php echo $VM_LANG->_('VM_CATEGORY_FORM_PRODUCTS_PER_ROW'); ?>: </div></td>
      <td valign="top">
      <input type="text" class="inputbox" size="3" name="products_per_row" value="<?php $db->sp("products_per_row"); ?>" />
      </td>
    </tr>
    <tr>
      <td><div align="right"><?php echo $VM_LANG->_('VM_CATEGORY_FORM_BROWSE_PAGE'); ?>: </div></td>
      <td valign="top">
      <?php
      echo ps_html::list_template_files( "category_browsepage", 'browse', $db->sf("category_browsepage") );
      ?>
      </td>
    </tr>
    <tr>
      <td colspan="2"><br /></td>
    </tr>
     <tr>
      <td ><div align="right">
        <?php echo $VM_LANG->_('PHPSHOP_CATEGORY_FORM_FLYPAGE') ?>:</div>
      </td>
      <td valign="top">
          <?php
	      echo ps_html::list_template_files( "category_flypage", 'product_details', str_replace('shop.', '', $db->sf("category_flypage")) );
	      ?>
      </td>
    </tr>
    
    <tr>
      <td colspan="2"><br /></td>
    </tr>
    <tr>
      <td><div align="right"><?php echo $VM_LANG->_('VM_CATEGORY_CLASS_NAME'); ?> </div></td>
      <td valign="top">
        <input type="text" class="inputbox" size="10" name="category_classname" value="<?php $db->sp("category_classname"); ?>" />
      </td>
    </tr>
</table>
<?php


//START HACK FOR METADATA EDIT
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

$category_title = "";
$category_metadesc = "";
$category_metakey = "";
$category_abstract = "";
$category_has_parent_name = "";
$category_has_parent_description = "";
$product_names_list = "";

if ($db->f("category_name") != "") { //Dont do any field filling if new category else continue with fields

$has_id = $db->f("category_id");
$dbcpx = new ps_DB;
$q = "SELECT category_parent_id FROM #__{vm}_category_xref WHERE category_child_id='$has_id'";
$dbcpx->query( $q );
$dbcpx->next_record();
$category_has_parent_id = $dbcpx->f('category_parent_id');
$category_has_parent_name = "";
$category_has_parent_description = "";
if ($category_has_parent_id != "") {
	$dbcp = new ps_DB;
	$q = "SELECT category_name, category_description FROM #__{vm}_category WHERE category_id='$category_has_parent_id'";
	$dbcp->query( $q );
	$dbcp->next_record();

	$category_has_parent_name = $dbcp->f('category_name');
	$category_has_parent_description = $dbcp->f('category_description');
}

$sub_cats_names_list = "";
$dbsc = new ps_DB;
$q = "SELECT category_child_id FROM #__{vm}_category_xref WHERE category_parent_id='$has_id'";
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

$dbcprodx = new ps_DB;
$q = "SELECT product_id FROM #__{vm}_product_category_xref WHERE category_id='$has_id'";
$dbcprodx->query( $q );
while( $dbcprodx->next_record() ) {
	$has_p_id = $dbcprodx->f('product_id');
	$dbcprod = new ps_DB;
	$q = "SELECT product_name FROM #__{vm}_product WHERE product_id='$has_p_id'";
	$dbcprod->query( $q );
	while( $dbcprod->next_record() ) {

		$product_names_list .= ' '.$dbcprod->f('product_name');
	}
}


if ($db->f("category_title") == "") {
	$category_title = str_replace('"', '', strip_tags($db->f("category_name")));
}

if ($db->f("category_metadesc") == "") {
	$desc =  $ps_product_category->get_description($has_id);
	$short_desc = substr(strip_tags($desc ), 0, 255);
	if ($desc != "") {
		$category_metadesc = str_replace('"', '', strip_tags($db->f("category_name").'. '.$short_desc));
		$meta_desc_legnth = strlen($category_metadesc);
	} else {
		$category_metadesc = str_replace('"', '', strip_tags($db->f("category_name")));
		$meta_desc_legnth = strlen($category_metadesc);
	}
	if (($category_has_parent_description != "") && ($meta_desc_legnth < '255')) {
		$clean_cat_par_desc = str_replace('"', '', strip_tags($category_has_parent_description));
		$category_metadesc = substr($category_metadesc.' '.$clean_cat_par_desc, 0, 255);
	}
}

if ($db->f("category_abstract") == "") {
	$desc =  $ps_product_category->get_description($has_id);
	$short_desc = substr(strip_tags($desc ), 0, 255);
	if ($desc != "") {
		$category_abstract = str_replace('"', '', strip_tags($db->f("category_name").'. '.$short_desc));
	} else {
		$category_abstract = str_replace('"', '', strip_tags($db->f("category_name")));
	}
}

if ($db->f("category_metakey") == "") {
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

	$end_result = strtolower($trimmed_title.', '.$trimmed_words);
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

	$category_metakey = $unique;
}

}
$tabs->endTab();
$tabs->startTab( 'MetaData', "MetaData");

?>
	<table class="adminform">
		<tr>
			<td valign="top" colspan="2">
<span style="font-size:1.3em;"><b>First setting (Manual Edit On/Off) is a global setting; turning it on or off will turn setting on or off for all categories.<br />The next four settings (Title, Description, Keywords, and Abstract) are UNIQUE TO THIS CATEGORY.</b></span><br /><br /><ul style="margin:6px;"><li>This HACK you have applied will automatically create and append metadata for your Title, Description, Keywords, and Abstract</li><li>The system will populate this data without you doing anything if you wish to do nothing.</li><li>If you want to manually edit your Meta Title/Description/Keywords/Abstract, and if this is a new category you are creating, then just fill in your Category Name / Full Description above (like adding a category normally), click Save, and re-enter this page. You will see the data below populates automatically, and you can edit from there!</li><li>If you wish for system to re-populate new META TITLE and/or DESCRIPTION and/or KEYWORDS and/or ABSTRACT (maybe you changed CATEGORY NAME or CATEGORY DESCRIPTION), clear the meta title and/or description and/or keywords and/or abstract, save/exit, and re-enter.</li><li>You can clear all products meta by sql:<ul style="margin:6px;"><li>update jos_vm_category set category_title = ''</li><li>update jos_vm_category set category_metadesc = ''</li><li>update jos_vm_category set category_metakey = ''</li><li>update jos_vm_category set category_abstract = ''</li></ul></li><li>This system will populate metadata as such:<ul style="margin:6px;"><li><b>Title</b> = Category Name - global param Append Category Parent Name - global param Subcategory Names - global param Append to end Title</li><li><b>Description</b> = Category Name - trimmed Category Description, global param Append Category Parent Name. global param Subcategory Names - global param Append to end Description<ul><li>and if above not >255 then + trimmed Category Parent Description to 255</li></ul></li><li><b>Keywords</b> = global param prepend Category Name Phrase, global param prepend Parent Categoy Name Phrase, Category Name, Parent Category Name, Subcategory Names, a culmitaion of Category Description + Parent Category Description + Child Product Names, global param Append Keywords to End, and global param Append Key Phrases to End</li><li><b>Abstract</b> = Category Name - trimmed Category Description</li></ul></li><li>Turn On Manual Edit (a global setting) will apply the changes you make here to the Meta Title/Description/Keywords/Abstract on the live site category pages. If you turn it off, the changes you make here will not be applied to live site, and system will generate info dynamically on the fly.<ul style="margin:6px;"><li><b>Advantages to Edit ON:</b> You can manually edit your tags</li><li><b>Advantages to Edit OFF:</b> Your metadata is updated on live site automatically when you change your category name/description/etc becuase its constantly dynamically created on fly. </li></ul></li><li>Note: If you want to manually edit each category's metadata, turn on manual edit. This turns the edit feature on for all categories. Otherwise turn off (uncheck) and system will automatically create it's metadata on live site as pages are fetched. When you turn off manual edit, your edits here will not go away, they are just not used anymore. You can always come back to any category page and turn manual edit back on to restore edits for all categories.</li></ul><br />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Turn On Manual Edit of Meta Title/Description/Keywords/Abstract (Global)' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($turn_on_edit == "Y") { 
      			echo "<input type=\"checkbox\" name=\"turn_on_edit\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"turn_on_edit\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
      	<tr>
			<td valign="top" class="key"><div align="right">
		<label for="title">
			<?php echo JText::_( 'Title' ); ?>:
		</label></div>
			</td>
			<td>
			<?php if ($db->f("category_title") != "") { ?>
				<textarea rows="5" cols="50" style="width:500px; height:40px" class="inputbox" id="title" name="category_title"><?php echo $db->sf("category_title"); ?></textarea>
			<?php } else { ?>
				<textarea rows="5" cols="50" style="width:500px; height:40px" class="inputbox" id="title" name="category_title"><?php echo $category_title ?></textarea>
			<?php }?>
			</td>
		</tr>
      	<tr>
			<td valign="top" class="key"><div align="right">
		<label for="metadesc">
			<?php echo JText::_( 'Description' ); ?>:
		</label></div>
			</td>
			<td>
			<?php if ($db->f("category_metadesc") != "") { ?>
				<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="metadesc" name="category_metadesc"><?php echo $db->sf("category_metadesc"); ?></textarea>
			<?php } else { ?>
				<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="metadesc" name="category_metadesc"><?php echo $category_metadesc ?></textarea>
			<?php }?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="metakey">
				<?php echo JText::_( 'Keywords' ); ?>:
				</label></div>
			</td>
			<td>
			<?php if ($db->f("category_metakey") != "") { ?>
				<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="metakey" name="category_metakey"><?php echo $db->sf("category_metakey"); ?></textarea>
			<?php } else { ?>
				<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="metakey" name="category_metakey"><?php echo $category_metakey ?></textarea>
			<?php }?>
			</td>
		</tr>
      	<tr>
			<td valign="top" class="key"><div align="right">
		<label for="abstract">
			<?php echo JText::_( 'Meta Abstract' ); ?>:
		</label></div>
			</td>
			<td>
			<?php if ($db->f("category_abstract") != "") { ?>
				<textarea rows="5" cols="50" style="width:500px; height:120px" class="inputbox" id="abstract" name="category_abstract"><?php echo $db->sf("category_abstract"); ?></textarea>
			<?php } else { ?>
				<textarea rows="3" cols="50" style="width:500px; height:120px" class="inputbox" id="abstract" name="category_abstract"><?php echo $category_abstract ?></textarea>
			<?php }?>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
			<hr /><span style="font-size:1.3em;"><b>This setting is unique to this category. The Canonical Metatag will only be added if the canonical field is filled. <i>DO NOT USE FILL THIS FIELD IF YOU DON't KNOW WHAT IT IS!</i></b></span><br /><br />
			</td>
		</tr>
      	<tr>
			<td valign="top" class="key"><div align="right">
		<label for="canonical">
			<?php echo JText::_( 'Add Canonical Metatag (use Absolute URL)' ); ?>:
		</label></div>
			</td>
			<td>
			<?php if ($db->f("category_canonical") != "") { ?>
				<input type="text" class="inputbox" size="60" id="canonical" name="category_canonical" value="<?php echo $db->sf("category_canonical"); ?>" />
			<?php } else { ?>
				<input type="text" class="inputbox" size="60" id="canonical" name="category_canonical" value="" />
			<?php }?>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
			<hr /><span style="font-size:1.3em;"><b>Settings below this are GLOBAL SETTINGS for ALL Categories. You may edit these fields below within any category, and all edits will aplly to all categories.</b></span><br /><br />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Append PARENT Category Name to Title and Description Metatag' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($append_category == "Y") { 
      			echo "<input type=\"checkbox\" name=\"append_category\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"append_category\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Subcategory Names to Title' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($append_subcategory_title == "Y") { 
      			echo "<input type=\"checkbox\" name=\"append_subcategory_title\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"append_subcategory_title\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="general_append_end_title">
				<?php echo JText::_( 'Append to end of Title Metatag' ); ?>:
				</label></div>
			</td>
			<td>
				<textarea rows="5" cols="50" style="width:500px; height:50px" class="inputbox" id="general_append_end_title" name="general_append_end_title"><?php echo str_replace('&','&amp;',$general_append_end_title); ?></textarea>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Subcategory Names to Description' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($append_subcategory_description == "Y") { 
      			echo "<input type=\"checkbox\" name=\"append_subcategory_description\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"append_subcategory_description\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="general_append_end">
				<?php echo JText::_( 'Append to end of Description Metatag' ); ?>:
				</label></div>
			</td>
			<td>
				<textarea rows="5" cols="50" style="width:500px; height:50px" class="inputbox" id="general_append_end" name="general_append_end"><?php echo str_replace('&','&amp;',$general_append_end); ?></textarea>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Prepend Category Name Phrase Keywords (words unseperated by commas)' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($prepend_cat_name_phrase == "Y") { 
      			echo "<input type=\"checkbox\" name=\"prepend_cat_name_phrase\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"prepend_cat_name_phrase\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Prepend Parent Category Name Phrase Keywords (words unseperated by commas)' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($prepend_par_cat_name_phrase == "Y") { 
      			echo "<input type=\"checkbox\" name=\"prepend_par_cat_name_phrase\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"prepend_par_cat_name_phrase\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="general_append_keys">
				<?php echo JText::_( 'Append to end of Keyword Metatag (comma seperated)' ); ?>:
				</label></div>
			</td>
			<td>
				<textarea rows="5" cols="50" style="width:500px; height:50px" class="inputbox" id="general_append_keys" name="general_append_keys"><?php echo str_replace('&','&amp;',$general_append_keys); ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="general_append_keys_phrase">
				<?php echo JText::_( 'Append to end of Keywords Metatag Phrases (comma seperated phrases )' ); ?>:
				</label></div>
			</td>
			<td>
				<textarea rows="5" cols="50" style="width:500px; height:50px" class="inputbox" id="general_append_keys_phrase" name="general_append_keys_phrase"><?php echo str_replace('&','&amp;',$general_append_keys_phrase); ?></textarea>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( '# of Keywords from Subcategory Names, Description, and Child Products' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="3" name="number_of_keywords" value="<?php echo $number_of_keywords ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Do NOT use keywords less than x characters' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="3" name="remove_less_than" value="<?php echo $remove_less_than ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" class="key"><div align="right">
				<label for="banned">
				<?php echo JText::_( 'Remove words from keywords metatag (comma seperated)' ); ?>:
				</label></div>
			</td>
			<td>
				<textarea rows="5" cols="50" style="width:500px; height:200px" class="inputbox" id="banned" name="banned"><?php echo str_replace('&','&amp;',$banned); ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2">
			<hr /><span style="font-size:1.3em;"><b>Settings below gives you the option to add serveral other Metatags to Category Pages. These are also GLOBAL SETTINGS for ALL Categories. You may edit these fields below within any category, and all edits will aplly to all categories. If fields are left blank, these tags are not added. If fields are filled, they will be added. Do your own research if you don't know what to add in a tag... there are many articles online.</b></span><br /><br />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Use Abstract Metatag' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($add_abstract == "Y") { 
      			echo "<input type=\"checkbox\" name=\"add_abstract\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"add_abstract\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Subject Metatag (uses category name)' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($subject == "Y") { 
      			echo "<input type=\"checkbox\" name=\"subject\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"subject\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Replace Joomla default Generator Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="generator" value="<?php echo $generator ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Classification Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="classification" value="<?php echo $classification ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Author Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="author" value="<?php echo $author ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Organization Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="organization" value="<?php echo $organization ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Copyright Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="copyright" value="<?php echo $copyright ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Country Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="country" value="<?php echo $country ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Content-Language Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="content_language" value="<?php echo $content_language ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Language Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="language" value="<?php echo $language ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Designer Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="designer" value="<?php echo $designer ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add Comments Metatag' ); ?>: </div></td>
			<td valign="top">
			<input type="text" class="inputbox" size="60" name="comments" value="<?php echo $comments ?>" />
			</td>
		</tr>
		<tr>
			<td><div align="right"><?php echo JText::_( 'Add No-Email-Collection Metatag' ); ?>: </div></td>
			<td valign="top">
			<?php 
			if ($no_email_collection == "Y") { 
      			echo "<input type=\"checkbox\" name=\"no_email_collection\" value=\"Y\" checked=\"checked\" />";
      		} else {
      			echo "<input type=\"checkbox\" name=\"no_email_collection\" value=\"Y\" />";
     			} ?>
			</td>
		</tr>
	</table>
<?php
//END HACK FOR METADATA EDIT


$tabs->endTab();
$tabs->startTab( "<img src='". IMAGEURL ."ps_image/image.png' width='16' height='16' align='absmiddle' border='0' /> ".$VM_LANG->_('E_IMAGES'), "status-page");

if( !stristr( $db->f("category_thumb_image"), "http") )
  echo "<input type=\"hidden\" name=\"category_thumb_image_curr\" value=\"". $db->f("category_thumb_image") ."\" />";

if( !stristr( $db->f("category_full_image"), "http") )
  echo "<input type=\"hidden\" name=\"category_full_image_curr\" value=\"". $db->f("category_full_image") ."\" />";
  
  $ps_html->writableIndicator( array( IMAGEPATH."category") );
?>

  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr> 
      <td valign="top" width="50%" style="border-right: 1px solid black;">
        <h2><?php echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_FULL_IMAGE') ?></h2>
        <table>
          <tr> 
            <td colspan="2" ><?php 
              if ($category_id) {
                echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL') . "<br />"; } ?> 
              <input type="file" class="inputbox" name="category_full_image" size="50" maxlength="255" />
            </td>
          </tr>
          <tr> 
            <td colspan="2" ><strong><?php echo $VM_LANG->_('PHPSHOP_IMAGE_ACTION') ?>:</strong><br/>
              <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action0" checked="checked" value="none" onchange="toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true );toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true );"/>
              <label for="category_full_image_action0"><?php echo $VM_LANG->_('PHPSHOP_NONE') ?></label><br/>
              <?php
              if( function_exists('imagecreatefromjpeg')) {
              		?>
	              <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action1" value="auto_resize" onchange="toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true );toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true );"/>
	              <label for="category_full_image_action1"><?php echo $VM_LANG->_('PHPSHOP_FILES_FORM_AUTO_THUMBNAIL') . "</label><br />"; 
              }
              if ($category_id and $db->f("category_full_image")) { ?>
                <input type="radio" class="inputbox" name="category_full_image_action" id="category_full_image_action2" value="delete" onchange="toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true );toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image_url, true );"/>
                <label for="category_full_image_action2"><?php echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL') . "</label><br />"; 
              } ?> 
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td width="21%" ><?php echo $VM_LANG->_('URL')." (".$VM_LANG->_('CMN_OPTIONAL')."!)&nbsp;"; ?></td>
            <td width="79%" >
              <?php 
              if( stristr($db->f("category_full_image"), "http") )
                $category_full_image_url = $db->f("category_full_image");
              else if(!empty($_REQUEST['category_full_image_url']))
                $category_full_image_url = vmGet($_REQUEST, 'category_full_image_url');
              else
                $category_full_image_url = "";
              ?>
              <input type="text" class="inputbox" size="50" name="category_full_image_url" value="<?php echo $category_full_image_url ?>" onchange="if( this.value.length>0) document.adminForm.auto_resize.checked=false; else document.adminForm.auto_resize.checked=true; toggleDisable( document.adminForm.auto_resize, document.adminForm.category_thumb_image_url, true );toggleDisable( document.adminForm.auto_resize, document.adminForm.category_thumb_image, true );" />
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td colspan="2" >
              <div style="overflow:auto;">
                <?php echo $ps_product->image_tag($db->f("category_full_image"), "", 0, "category") ?>
              </div>
            </td>
          </tr>
        </table>
      </td>

      <td valign="top" width="50%">
        <h2><?php echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_THUMB_IMAGE') ?></h2>
        <table>
          <tr> 
            <td colspan="2" ><?php if ($category_id) {
                echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_IMAGE_UPDATE_LBL') . "<br>"; } ?> 
              <input type="file" class="inputbox" name="category_thumb_image" size="50" maxlength="255" onchange="if(document.adminForm.category_thumb_image.value!='') document.adminForm.category_thumb_image_url.value='';" />
            </td>
          </tr>
          <tr> 
            <td colspan="2" ><strong><?php echo $VM_LANG->_('PHPSHOP_IMAGE_ACTION') ?>:</strong><br/>
              <input type="radio" class="inputbox" id="category_thumb_image_action0" name="category_thumb_image_action" checked="checked" value="none" onchange="toggleDisable( document.adminForm.image_action[1], document.adminForm.category_thumb_image, true );toggleDisable( document.adminForm.image_action[1], document.adminForm.category_thumb_image_url, true );"/>
              <label for="category_thumb_image_action0"><?php echo $VM_LANG->_('PHPSHOP_NONE') ?></label><br/>
              <?php 
              if ($category_id and $db->f("category_thumb_image")) { ?>
                <input type="radio" class="inputbox" id="category_thumb_image_action1" name="category_thumb_image_action" value="delete" onchange="toggleDisable( document.adminForm.image_action[1], document.adminForm.category_thumb_image, true );toggleDisable( document.adminForm.image_action[1], document.adminForm.category_thumb_image_url, true );"/>
                <label for="category_thumb_image_action1"><?php echo $VM_LANG->_('PHPSHOP_PRODUCT_FORM_IMAGE_DELETE_LBL') . "</label><br />"; 
              } ?> 
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr> 
            <td width="21%" ><?php echo $VM_LANG->_('URL')." (".$VM_LANG->_('CMN_OPTIONAL').")&nbsp;"; ?></td>
            <td width="79%" >
              <?php 
              if( stristr($db->f("category_thumb_image"), "http") )
                $category_thumb_image_url = $db->f("category_thumb_image");
              else if(!empty($_REQUEST['category_thumb_image_url']))
                $category_thumb_image_url = vmGet($_REQUEST, 'category_thumb_image_url');
              else
                $category_thumb_image_url = "";
              ?>
              <input type="text" class="inputbox" size="50" name="category_thumb_image_url" value="<?php echo $category_thumb_image_url ?>" />
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr>
            <td colspan="2" >
              <div style="overflow:auto;">
                <?php echo $ps_product->image_tag($db->f("category_thumb_image"), "", 0, "category") ?>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
<?php
$tabs->endTab();
$tabs->endPane();

// Add necessary hidden fields
$formObj->hiddenField( 'category_id', $category_id );

$funcname = !empty($category_id) ? "productCategoryUpdate" : "productCategoryAdd";

//finally close the form:
$formObj->finishForm( $funcname, $modulename.'.product_category_list', $option );

?>
<script type="text/javascript">//<!--
function toggleDisable( elementOnChecked, elementDisable, disableOnChecked ) {
  if( !disableOnChecked ) {
    if(elementOnChecked.checked==true) {
      elementDisable.disabled=false; 
    }
    else {
      elementDisable.disabled=true;
    }
  }
  else {
    if(elementOnChecked.checked==true) {
      elementDisable.disabled=true; 
    }
    else {
      elementDisable.disabled=false;
    }
  }
}

toggleDisable( document.adminForm.category_full_image_action[1], document.adminForm.category_thumb_image, true );
//-->
</script>

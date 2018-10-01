<?php

defined ('_VDEXEC') or die ('Restricted access');

class CProducts {

	function str_from_array($arr)
	{
	}
	
	function list_attribute_list( $product_id, $display_use_parent, $child_link, $display_type, $cls_suffix, $child_ids, $dw, $aw, $display_header, $product_list_type, $product_list, $mode="")
	{
	    $pinfo = $this->get_product_info($product_id);
	    $price = $this->get_product_price_x($product_id);
	    $attrib = $this->get_attributes($product_id);
	    
	    $ci = 0;
	    
	    if (count($pinfo['childs']) > 0)
	    {
		foreach($pinfo['childs'] as $child_id => $child_info)
		{
		    if ($ci % 2)
			$bgColor = "vmRowOne";
		    else
			$bgColor = "vmRowTwo";
		    
		    $products[$ci]['bgcolor'] = $bgColor;
		    $products[$ci]['product_id'] = $child_id;
		    $products[$ci]['parent_id'] = $child_info['parent_id'];
		    foreach($child_info['attributes'] as $attrib_name => $attrib_value)
		    {
			$products[$ci]['attrib_value'][] = $attrib_value;
		    }
		    $products[$ci]['price'] = $price['product_price'];
		    // Output product types
		    $products[$ci]['product_type'] = "";
		    $products[$ci]['product_in_stock'] = $child_info['product_in_stock'];
		
		}
	    } else {
	    }
	}
	
	function get_attributes( $product_id )
	{
	    if (isset($_SESSION['products_info'][$product_id]['attributes']))
		return;
	
	    $pinfo = $this->get_product_info($product_id);
	    
	    $db =  MySqlDatabase::getInstance();
	    $conf = new VDConfig();
	    
	    $q = "SELECT product_id, attribute_name FROM {$conf->db_table_prefix}_product_attribute_sku ";
	    $q .= "WHERE product_id = $product_id ORDER BY attribute_list ASC";
	    
	    foreach( $db->iterate($q) as $row )
	    {
		$_SESSION['products_info'][$product_id]['attribute_list'][] = $row->attribute_name;
	    }
	    
	    $child_ids = $_SESSION['products_info'][$product_id]['childs'];
	    $childs_str = " (";
	    foreach($child_ids as $child_id => $child_info)
	    {
		$childs_str .= $child_id . ",";
	    }
	    $childs_str = substr($childs_str, 0, -1);
	    $childs_str .= ") ";
	    
	    $q = "SELECT product_id, attribute_name, attribute_value FROM {$conf->db_table_prefix}_product_attribute WHERE ";
	    $q .= " product_id IN $childs_str";
	    
	    foreach( $db->iterate($q) as $row)
	    {
		$_SESSION['products_info'][$row->product_id]['attributes'][$row->attribute_name] = $row->attribute_value;
	    }
	    
	    return $_SESSION['products_info'][$product_id]['attributes'];
	}

	function get_product_info( $product_id )
	{
	    $parr[] = $product_id;
	    $this->get_products_info( $parr );
	    
	    return $_SESSION['products_info'][$product_id];
	}
	
	function get_product_price_x ($product_id)
	{
	    $pinfo = $this->get_product_info($product_id);
	    
	    $price['product_price'] = $pinfo['info']['product_price'];
	    if ($pinfo['info']['is_percent'] == 0)
	    {
		$price['product_price'] -= $pinfo['info']['amount'];
	    } else {
		$price['product_price'] -= $price['product_price']*($pinfo['info']['amount']/100);
	    }
	    return $price;
	}
	
	function get_products_info( $product_ids )
	{
	    if (count($product_ids) == 0)
		return;
		
	    $products_to_fetch = $product_ids;
	    foreach($product_ids as $product_id)
	    {
		if (isset($_SESSION['products_info'][$product_id]['info']))
		    unset ($products_to_fetch[$product_id]['info']);
	    }
	    
	    if (count($products_to_fetch) == 0)
		return;

	    $db = MySqlDatabase::getInstance();
	    $conf = new VDConfig();
	    
	    $strProducts = " (";
	    foreach($products_to_fetch as $product_id)
	    {
		$strProducts .= $product_id . ",";
	    }
	    $strProducts = substr($strProducts, 0, -1);
	    $strProducts .= ") ";
	
	    $q = "SELECT {$conf->db_table_prefix}_product.*, {$conf->db_table_prefix}_product_price.product_price, ";
	    $q .= "{$conf->db_table_prefix}_product_discount.amount, {$conf->db_table_prefix}_product_discount.is_percent ";
	    $q .= "FROM {$conf->db_table_prefix}_product ";
	    $q .= "LEFT JOIN {$conf->db_table_prefix}_product_price ON {$conf->db_table_prefix}_product_price.product_id = {$conf->db_table_prefix}_product.product_id ";
	    $q .= "LEFT JOIN {$conf->db_table_prefix}_product_discount ON {$conf->db_table_prefix}_product.product_discount_id = {$conf->db_table_prefix}_product_discount.discount_id ";
	    $q .= "WHERE {$conf->db_table_prefix}_product.product_id IN ";
	    $q .= $strProducts;
	    $q .= "OR {$conf->db_table_prefix}_product.product_parent_id IN ";
	    $q .= $strProducts;
	    $q .= "AND product_publish = 'Y' ORDER BY product_order_levels";
	    
	    $db->query( $q );
	    
	    foreach($db->iterate($q) as $row)
	    {
		//print_r($row);
		$_SESSION['products_info'][$row->product_id]['info']['vendor_id'] = $row->vendor_id;
		$_SESSION['products_info'][$row->product_id]['info']['product_parent_id'] = $row->product_parent_id;
		$_SESSION['products_info'][$row->product_id]['info']['product_sku'] = $row->product_sku;
		$_SESSION['products_info'][$row->product_id]['info']['product_s_desc'] = $row->product_s_desc;
		$_SESSION['products_info'][$row->product_id]['info']['product_thumb_image'] = $row->product_thumb_image;
		$_SESSION['products_info'][$row->product_id]['info']['product_full_image'] = $row->product_full_image;
		//$_SESSION['products_info'][$row->product_id]['info']['product_publish'] = $row->product_publish;
		$_SESSION['products_info'][$row->product_id]['info']['product_weight'] = $row->product_weight;
		$_SESSION['products_info'][$row->product_id]['info']['product_weight_uom'] = $row->product_weight_uom;
		$_SESSION['products_info'][$row->product_id]['info']['product_length'] = $row->product_length;
		$_SESSION['products_info'][$row->product_id]['info']['product_width'] = $row->product_width;
		$_SESSION['products_info'][$row->product_id]['info']['product_height'] = $row->product_height;
		$_SESSION['products_info'][$row->product_id]['info']['product_lwh_uom'] = $row->product_lwh_uom;
		$_SESSION['products_info'][$row->product_id]['info']['product_url'] = $row->product_url;
		$_SESSION['products_info'][$row->product_id]['info']['product_in_stock'] = $row->product_in_stock;
		$_SESSION['products_info'][$row->product_id]['info']['product_available_date'] = $row->product_available_date;
		$_SESSION['products_info'][$row->product_id]['info']['product_availability'] = $row->product_availability;
		$_SESSION['products_info'][$row->product_id]['info']['product_special'] = $row->product_special;
		$_SESSION['products_info'][$row->product_id]['info']['product_discount_id'] = $row->product_discount_id;
		//$_SESSION['products_info'][$db->f("product_id")]['info']['ship_code_id'] = $db->f("ship_code_id");
		$_SESSION['products_info'][$row->product_id]['info']['cdate'] = $row->cdate;
		$_SESSION['products_info'][$row->product_id]['info']['mdate'] = $row->mdate;
		$_SESSION['products_info'][$row->product_id]['info']['product_name'] = $row->product_name;
		//$_SESSION['products_info'][$db->f("product_id")]['info']['product_sales'] = $db->f("product_sales");
		$_SESSION['products_info'][$row->product_id]['info']['attribute'] = $row->attribute;
		$_SESSION['products_info'][$row->product_id]['info']['custom_attribute'] = $row->custom_attribute;
		$_SESSION['products_info'][$row->product_id]['info']['product_unit'] = $row->product_unit;
		$_SESSION['products_info'][$row->product_id]['info']['child_options'] = $row->child_options;
		$_SESSION['products_info'][$row->product_id]['info']['quantity_options'] = $row->quantity_options;
		$_SESSION['products_info'][$row->product_id]['info']['child_option_ids'] = $row->child_option_ids;

		$_SESSION['products_info'][$row->product_id]['info']['product_title'] = $row->product_title;
		$_SESSION['products_info'][$row->product_id]['info']['product_metadesc'] = $row->product_metadesc;
		$_SESSION['products_info'][$row->product_id]['info']['product_metakey'] = $row->product_metakey;
		$_SESSION['products_info'][$row->product_id]['info']['product_abstract'] = $row->product_abstract;
		$_SESSION['products_info'][$row->product_id]['info']['product_canonical'] = $row->product_canonical;
		$_SESSION['products_info'][$row->product_id]['info']['product_price'] = $row->product_price;
		$_SESSION['products_info'][$row->product_id]['info']['amount'] = $row->amount;
		$_SESSION['products_info'][$row->product_id]['info']['is_percent'] = $row->is_percent;

		if ($row->product_parent_id != 0)
		{
		    $_SESSION['products_info'][$row->product_parent_id]['childs'][$row->product_id] = $_SESSION['products_info'][$row->product_id];
		}

	    }
	}



}
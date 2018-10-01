<?php

defined ('_VDEXEC') or die ('Restricted access');

class CCategories
{
    private $cattree = array();
    
    function getCategoriesTree()
    {
	$session = Session::getInstance();

	if ($session->is_Set('categories'))
	    return;

	$db = MySqlDatabase::getInstance();
	$conf = new VDConfig();
    
	$strQuery  = "SELECT {$conf->db_table_prefix}_category.category_id, {$conf->db_table_prefix}_category.category_description, ";
	$strQuery .= "{$conf->db_table_prefix}_category.category_name, {$conf->db_table_prefix}_category.category_thumb_image, ";
	$strQuery .= "{$conf->db_table_prefix}_category.category_full_image, {$conf->db_table_prefix}_category_xref.category_parent_id, ";
	$strQuery .= "{$conf->db_table_prefix}_category.cdate, {$conf->db_table_prefix}_category.mdate, ";
	$strQuery .= "{$conf->db_table_prefix}_category.category_title, {$conf->db_table_prefix}_category.category_metadesc, ";
	$strQuery .= "{$conf->db_table_prefix}_category.category_metakey, {$conf->db_table_prefix}_category.category_canonical, ";
	$strQuery .= "COUNT({$conf->db_table_prefix}_product_category_xref.product_id) AS products_count FROM {$conf->db_table_prefix}_category ";
	$strQuery .= "LEFT JOIN {$conf->db_table_prefix}_product_category_xref ON {$conf->db_table_prefix}_category.category_id = {$conf->db_table_prefix}_product_category_xref.category_id ";
	$strQuery .= "INNER JOIN {$conf->db_table_prefix}_category_xref ON {$conf->db_table_prefix}_category.category_id = {$conf->db_table_prefix}_category_xref.category_child_id ";
	$strQuery .= "GROUP BY {$conf->db_table_prefix}_category.category_id";
	
	$categories = array();
	foreach($db->iterate($strQuery) as $row)
	    $categories[$row->category_id] = array('parent' => $row->category_parent_id,
		'node' => array(
		    'category_id' => $row->category_id,
		    'category_description' => $row->category_description,
		    'category_name' => $row->category_name,
		    'category_thumb_image' => $row->category_thumb_image,
		    'category_full_image' => $row->category_full_image,
		    'cdate' => $row->cdate,
	    	    'mdate' => $row->mdate,
		    'category_title' => $row->category_title,
		    'category_metadesc' => $row->category_metadesc,
		    'category_metakey' => $row->category_metakey,
		    'category_canonical' => $row->category_canonical,
		    'category_products' => $row->products_count
		)
	    );
	    
	$session->Set('categories_list', $categories);
	
	$categories[0]['parent'] = -1;
	$categories = $this->buildTree($categories);
	$session->Set('categories', $categories);

    }
    
    function __construct()
    {
	$this->getCategoriesTree();
    }

    function summaryCounts(& $node = NULL)
    {
	if (is_null($node))
	    return 0;

	$sum = 0;

	foreach($node as $el)
	{
	    $lsum = $el['node']['category_products'];
	    if (!empty($el['sub']))
	    {
		$lsum += CCategories::summaryCounts($el['sub']);
	    }
		
	    $el['node']['category_products'] = $lsum;
	    $sum += $lsum;
	}
	
	return $sum;
    }

    function getCategoryById( $category_id )
    {
	$cattree = Session::getInstance()->Get('categories_list');
	return $cattree[ $category_id ]['node'];
    }

    function buildTree($listIdParent)
    {
	$rootid  = 0;
	foreach($listIdParent as $id => $node)
	{
	    if ($node['parent'] >= 0)
	    {
		$listIdParent[$node['parent']]['sub'][$id] = & $listIdParent[$id];
	    } else {
		$rootid = $id;
	    }
	}
	return array($rootid => $listIdParent[$rootid]);
    }
    
    function getCategoryLink($category_id)
    {
	return array(
		'page' => 'categories',
		'category_id' => $category_id
	    );
    }
    
    function getCategoryList($node = NULL, $level = 0)
    {
	if (is_null($node))
	{
	    $toplevel = Session::getInstance()->Get('categories');
	    CCategories::getCategoryList($toplevel);
	} else {
	    foreach($node as $el)
	    {
		if (($el['parent'] >= 0) && ($level > 0))
		{
		    echo '<li>';
		    
		    if ($level == 1)
			$class = 'mainlevel';
		    else
			$class = 'sublevel';
			
		    $count = $el['node']['category_products'];

		    if (!empty($el['sub']))
		        $count += CCategories::summaryCounts($el['sub']);
		    
		    $link = Router::MakeLink(CCategories::getCategoryLink($el['node']['category_id'])); 
		    echo "<a title=\"{$el['node']['category_name']}\" class=\"$class\" href=\"$link\" >{$el['node']['category_name']} ($count)</a>";
		}
		if (!empty($el['sub']))
		{
		    if ($level == 0)
			echo '<ul id="mainlevel">';
		    else
			echo '<ul>';
		    CCategories::getCategoryList($el['sub'], $level+1);
		    echo '</ul>';
		}
		if ($el['parent'] > 0)
		    echo '</li>';
	    }
	}
    }
}
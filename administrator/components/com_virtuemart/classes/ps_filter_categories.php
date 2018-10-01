<?php
if ( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die ( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

mm_showMyFileName( __FILE__ );

function sortCategories($a, $b)
{
    $ao = $a['node']['list_order'];
    $bo = $b['node']['list_order'];
    if ($ao == $bo) {
        return 0;
    }
    return ($ao < $bo) ? -1 : 1;
}

class vm_ps_filter_categories
{

  function transliterate( $text )
  {
      $tbl= array(
          'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
          'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
          'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
          'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
          'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
          'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
          'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
          'Ё'=>"Yo", 'Х'=>"H", 'Ц'=>"Ts", 'Ч'=>"Ch", 'Ш'=>"Sh", 'Щ'=>"Shch", 'Ъ'=>"", 'Ь'=>"",
          'Ю'=>"Yu", 'Я'=>"Ya"
      );
      $transliteration = strtr($text, $tbl);

      return $transliteration;
  }

  function make_link($category_id, $product_type_id, $product_type_name, $category_name, $parameter_value)
  {
      $parameter_value = urlencode($parameter_value);
      $link="index.php?option=com_virtuemart&page=shop.browse&custom_filter=1&product_type_id=$product_type_id&category_id=$category_id&parameter_name=$product_type_name&parameter_value=$parameter_value";
      print("<a href=\"$link\">$parameter_value</a>");
  }

  function get_childs_for_category($category_id)
  {
    $subs = array();
    $toplevel = $this->get_categories_tree();
    $this->get_subs_for_category($category_id, $toplevel[0]['sub'], $subs);

    $childs = array();
    foreach($subs as $cat_id => $cat_values)
    {
        $childs[] = $cat_values['category_id'];
    }

    return $childs;
  }

  function get_variants_for_category($category_id = NULL)
  {
    $categories = implode(", ", $this->get_childs_for_category($category_id));
    $fentities = $this->get_filter_entities_all();
    $category = $this->get_toplevel_category($category_id);

    $db = new ps_DB;

    if (!empty($fentities[$category_id]))
    {
        foreach($fentities[$category_id] as $pti => $fentity)
        {
            print("<b>${fentity['label']}</b><br />");

            if ($pti == 0) {
            } else {
                $q  = "SELECT DISTINCT {$fentity['name']} FROM #__{vm}_product_type_{$pti} ";
                $q .= "INNER JOIN #__{vm}_product_category_xref ON #__{vm}_product_type_{$pti}.product_id = ";
                $q .= "#__{vm}_product_category_xref.product_id ";
                $q .= "WHERE #__{vm}_product_category_xref.category_id IN ($categories)";
                $db->query($q);
                while($db->next_record() )
                {
                    $variant = $db->f($fentity['name']);
                    $this->make_link($category_id, $pti, $fentity['name'], $category['category_name'], $variant);
                }
            }
        }
    }
  }

  function get_filter_entities_all()
  {
    $session = JSession::getInstance('none', array() );
    $filter_entities = $session->get('vm_filter_entities', array(), 'vidrio');

    $filter_entities = array();
    if (empty($filter_entities))
    {
      $db = new ps_DB;
      $dbp = new ps_DB;

      $q = "SELECT fentry_id, category_id, #__{vm}_filter_types.product_type_id, ";
      $q.= "#__{vm}_filter_types.parameter_name, products_count, parameter_label, ";
      $q.= "request, description, link, title, classname  FROM #__{vm}_filter_types ";
      $q.= "INNER JOIN #__{vm}_product_type_parameter ON #__{vm}_filter_types.product_type_id = ";
      $q.= "#__{vm}_product_type_parameter.product_type_id AND #__{vm}_filter_types.parameter_name = ";
      $q.= "#__{vm}_product_type_parameter.parameter_name";
      $db->query( $q );

      while ($db->next_record() )
      {
        $category_id = $db->f('category_id');
        $product_type_id = $db->f('product_type_id');
        $parameter_name = $db->f('parameter_name');
        $filter_entities[ $category_id ][$product_type_id]['name'] = $parameter_name;
        $filter_entities[ $category_id ][$product_type_id]['label'] = $db->f("parameter_label");
      }

      $session->set('vm_filter_entities', $filter_entities, 'vidrio');
    }
    return $filter_entities;
  }

  function get_toplevel_category($category_id)
  {
    $toplevel = $this->get_categories_tree();
    $path = array();
    $this->get_path_to_category($category_id, $toplevel[0]['sub'], $path);

    return end($path);
  }

  function get_subs_for_category($category_id, & $node = NULL, & $path = NULL, & $flevel = -1, & $level = 0)
  {
    if (is_null($node))
        return;

    foreach($node as $el)
    {
        if($el['node']['category_id'] == $category_id) {
            $path[] = $el['node'];
            $flevel = $level;
        }

        if (!empty($el['sub']))
        {
            $level++;
            $this->get_subs_for_category($category_id, $el['sub'], $path, $flevel, $level);
            $level--;
        }

        if ($flevel >=0) {
            if ($level <= $flevel) {
                break;
            } else {
                $path[] = $el['node'];
            }
        }
    }
  }

  function get_path_to_category($category_id, & $node = NULL, & $path = NULL, & $found = 0)
  {
    if (is_null($node))
        return;

    foreach($node as $el)
    {

        if ($el['node']['category_id'] == $category_id) {
            $found = 1;
            $type = gettype($path);
            $path[] = $el['node'];
            break;
        }

        if (!empty($el['sub']))
        {
            $this->get_path_to_category($category_id, $el['sub'], $path, $found);
            if ($found == 1) {
                $path[] = $el['node'];
                break;
            }
        }
    }
  }

  function get_filter_entities_for_category($category_id)
  {
    $session = JSession::getInstance('none', array() );
    $filter_entities = $session->get('vm_filter_entities', array(), 'vidrio');
    if (empty($filter_entities))
    {
      $filter_entities = $this->get_filter_entities_all();
    }
    
    if (empty($filter_entities) || empty($filter_entities[$category_id]))
      return array();

    return $filter_entities[$category_id];
  }
  
  function get_filter_links_list($category_id)
  {
    $cat_entities = $this->get_filter_entities_for_category( $category_id );
    if (empty($cat_entities))
	return array();
    
    foreach( $cat_entities as $product_type_id => $product_type_set)
    {	
	$full_parameter_name = "product_type_${product_type_id}_${product_type_set['name']}";
	
	$cat_entities[$product_type_id]['links'] = array();
	foreach( $product_type_set['variants'] as $variant)
	{
	    $slink = $mm_action_url."index.php?option=com_virtuemart&amp;page=shop.browse&amp;product_type_id=$product_type_id&amp;";
	    $slink .= "${full_parameter_name}_comp='like'&amp;${full_parameter_name}=$variant";
	    $slink = vmRoute( $slink );
	    
	    $cat_entities[$product_type_id]['links'][] = $slink;
	}
    }
    return $cat_entities;
  }
  
  function get_categories_tree()
  {
    $session = JSession::getInstance('none', array() );
    $categories = $session->get('vm_categories', array(), 'vidrio');
    if (empty($categories))
    {
	$db = new ps_DB;
	$q  = "SELECT #__{vm}_category.category_id, #__{vm}_category.category_description, ";
	$q .= "#__{vm}_category.category_name, #__{vm}_category.category_thumb_image, ";
	$q .= "#__{vm}_category.category_full_image, #__{vm}_category_xref.category_parent_id, ";
	$q .= "#__{vm}_category.cdate, #__{vm}_category.mdate, ";
	$q .= "#__{vm}_category.category_title, #__{vm}_category.category_metadesc, ";
	$q .= "#__{vm}_category.category_metakey, #__{vm}_category.category_canonical, ";
	$q .= "#__{vm}_category.list_order, ";
	$q .= "COUNT(#__{vm}_product_category_xref.product_id) AS product_count FROM #__{vm}_category ";
	$q .= "LEFT JOIN #__{vm}_product_category_xref ON #__{vm}_category.category_id = #__{vm}_product_category_xref.category_id ";
	$q .= "INNER JOIN #__{vm}_category_xref ON #__{vm}_category.category_id = #__{vm}_category_xref.category_child_id ";
	$q .= "WHERE #__{vm}_category.category_publish = 'Y' ";
	$q .= "GROUP BY #__{vm}_category.category_id";

	$categories = array();
	$db->query( $q );
	
	while( $db->next_record() )
	{
	    $categories[$db->f('category_id')] = array('parent' => $db->f('category_parent_id'),
	      'node' => array(
		'category_id' => $db->f('category_id'),
		'category_description' => $db->f('category_description'),
		'category_name' => $db->f('category_name'),
		'category_thumb_image' => $db->f('category_thumb_image'),
		'category_full_image' => $db->f('category_full_image'),
		'cdate' => $db->f('cdate'),
		'mdate' => $db->f('mdate'),
		'category_title' => $db->f('category_title'),
		'category_metadesc' => $db->f('category_metadesc'),
		'category_metakey' => $db->f('category_metakey'),
		'category_canonical' => $db->f('category_canonical'),
		'category_products' => $db->f('product_count'),
		'list_order' => $db->f('list_order')
	       )
	    );
	
	}
	$categories[0]['parent'] = -1;
	$categories = $this->build_categories_tree($categories);
	$this->categories_sort($categories[0]['sub']);
        $session->set('vm_categories', $categories, 'vidrio');
    }
    return $categories;
  }
  
  
  function build_categories_tree($listIdParent)
  {
    $rootid = 0;
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

  function categories_sort(& $node = NULL)
  {
    if (is_null($node))
	return;
	
    foreach($node as $el)
    {
	if (!empty($el['sub']))
	{
	    $this->categories_sort($el['sub']);
	}
    }
    usort($node, 'sortCategories');
  }
  
  function products_in_all_subcategories(& $node = NULL)
  {
    if (is_null($node))
	return 0;
	
    $sum = 0;
    
    foreach($node as $el)
    {
	$lsum = $el['node']['category_products'];
	if (!empty($el['sub']))
	{
	    $lsum += $this->products_in_all_subcategories($el['sub']);
	}
	$el['node']['category_products'] = $lsum;
	$sum += $lsum;
    }	
    
    return $sum;
  }
  
};

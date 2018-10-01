<?php

class Router {
    
    function Transliterate( $text )
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
        
        $transliteration = strtr($name, $tbl);
        
        return $transliteration;
        
    }
    
    function MakeLink( $parr )
    {
	//print_r($parr);
	if (!isset($parr['page']))
	    return '';
	    
	    
	if ($parr['page'] == 'categories')
	{
	    $cat = CCategories::getCategoryById ($parr['category_id']);
	    return $cat['category_id'] + '-' + Router::Transliterate($cat['category_name']);
	    // 'category_id' + '-' + 'category_name'
	}
    }
}
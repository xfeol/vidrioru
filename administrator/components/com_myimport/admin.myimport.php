<?php
  ////////////////////////////////////////////////////////
  // Компонент импорта/экспорта товаров для Virtuemart	//
  // Разработан для Joomla 1.5.x 						//
  // 2011 (C) Ребров О.В.   (admin@webplaneta.com.ua)	//
  ////////////////////////////////////////////////////////
header('Content-Type: text/html; charset=utf-8');
defined( '_JEXEC' ) or die( 'Restricted access' );
global $mainframe;
$params = JComponentHelper::getParams( 'com_myimport');
$debug = $params->get ('debug');
if ($debug == 1) {
error_reporting(E_ALL);
ini_set("display_errors", 1);
}
if($task == "import")
	{
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_myimport');
		$debug = $params->get ('debug');
		function getcat($catname) //функция определения активной категории, для устранение ошибки с одинаковыми названиями подкатегорий
			{
			global $mainframe;
			$database =& JFactory::getDBO();
			$database->setQuery("SELECT * FROM #__vm_category WHERE category_name ='".$catname."' ");
			$cat = $database->loadResult();
			return $cat;
			}
		function getactive($parent, $catname) //функция определения активной категории, для устранение ошибки с одинаковыми названиями подкатегорий
			{
			global $mainframe;
			$database =& JFactory::getDBO();
			$database->setQuery("SELECT * FROM #__vm_category WHERE category_name ='".$catname."' ");
			$list = $database->loadObjectList();
			$nextcateg = 1;
			foreach($list as $cat) 
					{
					$categactive=$cat->category_id;
					$database->setQuery("SELECT category_parent_id FROM #__vm_category_xref WHERE category_child_id ='".$cat->category_id."' ");
					$categ = $database->loadResult();
					if ($categ == $parent) {$nextcateg=0; $categactive=$cat->category_id;} 
					}
			$active = array($nextcateg, $categactive); 
			return $active;
			}
		function addcategory($catname, $catdescr, $catthumb, $catimg, $list_order, $publish)
		{
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_myimport');

		$database =& JFactory::getDBO();
		$database->setQuery("INSERT INTO #__vm_category ( `vendor_id` , `category_name` ,`category_description`, `category_thumb_image`, `category_full_image`, `category_publish` , `category_browsepage` , `products_per_row` , `category_flypage` , `list_order` ) values ( '1' , '".$catname."' , '".$catdescr."', '".$catthumb."', '".$catimg."', '".$publish."', '".$params->get ('browse')."' , '".$params->get ('browse_col')."' , '".$params->get ('flypage')."' , '".$list_order."' )");
		 if ($database->query()) { $ok = 1; } else { $ok = 0; }
		 return $ok;
		}
		function addcatxref($parent, $child)
		{
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_myimport');
		$database =& JFactory::getDBO();
		$database->setQuery("INSERT INTO #__vm_category_xref ( `category_parent_id` , `category_child_id` ) values ( '".$parent."' , '".$child."' )");
		$database->query();
		return; 
		}
		function updatecateg($category, $catdescr, $catthumb, $catimg)
		{
			$database =& JFactory::getDBO();
			if ($catdescr)//запись мини изображения категории
			{$database->setQuery("UPDATE #__vm_category SET category_description ='".$catdescr."' WHERE category_id='".$category."'");
			$database->query();}
			if ($catthumb)//запись мини изображения категории
			{$database->setQuery("UPDATE #__vm_category SET category_thumb_image ='resized/".$catthumb."' WHERE category_id='".$category."'");
			$database->query();}
			if ($catimg)//запись изображения категории
			{$database->setQuery("UPDATE #__vm_category SET category_full_image ='". $catimg."' WHERE category_id='".$category."'");
			$database->query();}
			return;
		}
		function getproduct ($product_sku)
		{
		$database =& JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__vm_product WHERE product_sku ='".$product_sku."' LIMIT 1");
		$product_id =  $database->loadResult();
		return $product_id;
		}
		function addproduct($parent_id, $product_sku, $name, $s_desc ,$descr, $in_stock, $catthumb, $catimg)
		{
			global $mainframe;
			$params = JComponentHelper::getParams( 'com_myimport');
			$database =& JFactory::getDBO();
			if (!$parent_id) {$parent_id = 0;}
			$database->setQuery("INSERT INTO #__vm_product ( vendor_id , product_parent_id , product_sku , product_s_desc , product_in_stock , product_publish , product_weight_uom, product_special , product_discount_id , product_name , product_desc , product_thumb_image , product_full_image , quantity_options, product_order_levels ) values ( '1', '".$parent_id."', '".$product_sku."', '".$s_desc."','".$in_stock."', 'Y','".$params->get ('weight')."' , 'N', '0', '".$name."', '".$descr."', '".$catthumb."', '".$catimg."', 'none,0,0,1', '0,0')");
			$database->query(); 
		return;
		}
		function price ($product_id, $price)
		{
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_myimport');
		$database =& JFactory::getDBO();
		$database->setQuery("DELETE FROM #__vm_product_price WHERE product_id=".$product_id."");
		$database->query();
		if ($price)
				{
				$price=ltrim($price,"$");
				$price=str_replace(",",".",$price);
				$price=str_replace(" ","",$price);
				$quantity_start = '0';
				$quantity_end = '0';
				$database->setQuery("INSERT INTO #__vm_product_price ( product_id , product_price , product_currency , shopper_group_id , price_quantity_start , price_quantity_end ) values ( '".$product_id."', '".$price."' , '".$params->get ('currency')."' , '5' , '".$quantity_start."' , '".$quantity_end."' )");
			$database->query();
				}
		return;
		}
		function updateproduct($parent_id, $product_sku, $name, $s_desc ,$descr, $in_stock, $catthumb, $catimg)
		{
		$database =& JFactory::getDBO();
		$product_id = getproduct($product_sku);
		if ($parent_id != 0)
			{
			/* $product_parent_id = getproduct ($parent_id);
			if (!$product_parent_id) {$parent_id = 0;}
			$database->setQuery("UPDATE #__vm_product SET product_parent_id ='".$parent_id."' WHERE product_id='".$product_id."'");
			$database->query(); */
			$database->setQuery("UPDATE #__vm_product SET child_options ='N,".$child_4.",N,N,N,".$child_2.",,,' WHERE product_id='".$product_parent_id."'");
			$database->query();
			}
			if ($catthumb)//запись мини изображения товара
			{
			$database->setQuery("UPDATE #__vm_product SET product_thumb_image ='resized/".$catthumb."' WHERE product_id='".$product_id."'");
			$database->query();
			}
			if ($catimg)//запись изображения товара
			{
			$database->setQuery("UPDATE #__vm_product SET product_full_image ='".$catimg."' WHERE product_id='".$product_id."'");
			$database->query();
			}
			if ($in_stock)//запись количества на складе
			{
			$database->setQuery("UPDATE #__vm_product SET product_in_stock ='".$in_stock."' WHERE product_id='".$product_id."'");
			$database->query();
			}
		return $product_id;
		}
		function getmanuf($manuf) //Проверка и добавление производителя
		{
		$params = JComponentHelper::getParams( 'com_myimport');
		$debug = $params->get ('debug');
		$database =& JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__vm_manufacturer WHERE mf_name ='".$manuf."' LIMIT 1");
		$manuf_id =  $database->loadResult();//запись производителя
		if (!$manuf_id) {
		$database->setQuery("INSERT INTO #__vm_manufacturer ( mf_name, mf_category_id) values ( '".$manuf."','1')");
		$database->query(); 
		if ($debug == 1 && $data_array[9]) { print "Производителя ".$data_array[9]." нет, создаем. <br>"; }
		}
		else
		{
		return $manuf_id;
		}
		$database->setQuery("SELECT * FROM #__vm_manufacturer WHERE mf_name ='".$manuf."' LIMIT 1");
		$manuf_id =  $database->loadResult();
		return $manuf_id;
		}
		function product_xref ($product_id, $category_id, $manuf_id, $list_order)
		{
			if (!$list_order) {$list_order = 1;}
			$database =& JFactory::getDBO();
			if (isset($category_id))
			{
			$database->setQuery("INSERT INTO #__vm_product_category_xref ( category_id , product_id , product_list ) values ( '".$category_id."' , '".$product_id."', '".$list_order."' )");
			$database->query(); 
			}
			if (isset($manuf_id)) {
				$database->setQuery("SELECT * FROM #__vm_product_mf_xref WHERE product_id ='".$product_id."' LIMIT 1");
				$manuf_xref =  $database->loadResult();
				if (!$manuf_xref)
					{
						$database->setQuery("INSERT INTO #__vm_product_mf_xref ( product_id , manufacturer_id ) values ( '".$product_id."', '".$manuf_id."' )");
						$database->query();
					} else {
						$database->setQuery("UPDATE #__vm_product_mf_xref SET manufacturer_id ='".$manuf_id."' WHERE product_id='".$product_id."'");
						$database->query();
					}
			}
		return;
		}
		function edit_import($str)//редактирование импотированных данных
		{
			$new = str_replace("\n", " ", $str);
			$new = str_replace("\r", "", $new);
			$new = str_replace("\0", "", $new);
			//$new=ltrim($new,'"');
			//$new=rtrim($new,'"');
			$new=trim($new,'"');//удаляет двойнве ковычки в начале и конце строке
			$new = str_replace('""','"',$new);//заменяет две двойных ковычки на одни двойные ковычки
			//$new = str_replace('""','"',$new);//заменяет две двойных ковычки на одни двойные ковычки
			$new = str_replace('""','"',$new);//заменяет две двойных ковычки на одни двойные ковычки
			$new = str_replace('" "','"',$new);//заменяет две двойных ковычки на одни двойные ковычки
			//$new = str_replace('"','\"',$new);//заменяет две двойных ковычки на одни двойные ковычки
			$new=trim($new);//удаляет пробелы в начале и конце строке
			$new = htmlspecialchars(stripslashes($new), ENT_QUOTES);//заменяет спец символы в их HTML эквивалент
		//удаление всех лишних пробелов
			$arr=explode(" ",$new);
			$sss="";
			for ($j=0;$j<count($arr);$j++)
				{
				if($arr[$j])
					{
					$sss.=$arr[$j];
					if($j!=count($arr)-1)
						{
						$sss.=" ";
						}
					}
				}
		//------------------------------
			$new=$sss;
			return $new;
		}
		$path=JPATH_BASE."/cache/myimport";
			// Проверяем на существование папку $path
		   if(!file_exists($path)) {
			/*die("<b>Пожалуйста, создайте папку <font color=red>".$path."</font> и <a href=&#63;>повторите попытку загрузить файл</a>.</b>");*/
			mkdir($path, 0777);
			echo $path."- Папка создана";}
		// Выводим форму для загрузки файла.
		if(empty($_FILES['UserFile']['tmp_name']))
			echo
			"
			<span style=\"color:red\">Перед импортом обязательно посмотрите настройки компонента и выставьте нужные Вам параметры и нажмите кнопку сохранить! Параметры находятся в верхнем правом углу.</span>
			<form method=post enctype=multipart/form-data accept-charset=\"UTF-8\">
			Пожалуйста используйте только файлы в формате CSV и <a href=\"http://webplaneta.com.ua/extensions/components/myimport/export_price_v_1_5_6.zip\">заданого формата</a>, во избежание нежелаемых последствий <br>
			Выберите файл: <input type=file name=UserFile> <br />
			Уничтожить существуюшие товары и категории? <input type=checkbox name=del_product> <br />
			Очистить количество товаров на складе? <input type=checkbox name=clear_in_stock> <br />
		<input type=submit value=Отправить>
			</form>";
			// Если файл не загружен по каким-то причинам, выводим ошибку.
			elseif(!is_uploaded_file($_FILES['UserFile']['tmp_name']))
			die("<b><font color=red>123Файл не был загружен! Попробуйте <a href=&#63;>повторить попытку</a>!</font></b>");
			// Если файл удачно загружён на сервер, делаем вот что...
				// Переносим загружённый файл в папку $path
				elseif(!move_uploaded_file($_FILES['UserFile']['tmp_name'],$path.chr(47)."price.csv"))
				{
				$name = $_FILES['UserFile']['name']; // Если не удалось перенести файл, выводим ошибку:
				die("<b><font color=red>Файл не был загружен! Попробуйте <a href=&#63;>повторить попытку</a>!</font></b>");
				 }
			else // Если всё Ok, то выводим инфо. о загружённом файле.
			{
			echo
			"<center><b>Файл <font color=green>".$path.chr(47).$_FILES['UserFile']['name']."</font> успешно загружён на сервер!</font></b></center>".
			"<hr>".
			"Тип файла: <b>".$_FILES['UserFile']['type']."</b><br>".
			"Размер файла: <b>".round($_FILES['UserFile']['size']/1024,2)." кб.</b>".
			"<hr>";

			//Импорт товара

			$fl = $path."/"."price.csv";
			$file=file_get_contents($fl); 
			$encoding = $params->get ('encoding');
			$file=iconv($encoding, "utf-8",$file); 
			$file=file_put_contents($fl,$file);
			$data = File($fl);
			$data = str_replace("\r","",$data);
			$data = str_replace("\0","",$data);
			$database =& JFactory::getDBO();
			$j=0;
			$list_order1 = 0; //сортировка категорий 1 уровня
			if (isset($_POST["del_product"]))
			//if (@$del_product)    //очистка таблиц товаров
				{
				 $database->setQuery("DELETE FROM `#__vm_category`");
				 $database->query();
				 $database->setQuery("DELETE FROM `#__vm_category_xref`");
				 $database->query();
				 $database->setQuery("DELETE FROM `#__vm_product`");
				 $database->query();
				 $database->setQuery("DELETE FROM `#__vm_product_category_xref`");
				 $database->query();
				 $database->setQuery("DELETE FROM `#__vm_product_mf_xref`");
				 $database->query();
				 $database->setQuery("DELETE FROM `#__vm_product_price`");
				 $database->query();
				}
			if (isset($_POST["clear_in_stock"]))
			{
			$database->setQuery("UPDATE #__vm_product SET product_in_stock = 0");
			$database->query();
			}
				$child_4 = 'N';
				if ($params->get ('child_2') == 1) {$child_2 = 'Y';} else {$child_2 = 'N';} 
				if ($params->get ('child_3') == 1) {
				if ($params->get ('child_4') == 0) {$child_4 = 'Y';} elseif ($params->get ('child_2') == 1) {$child_4 = 'YM';}
					}
		
		for ($i=1;$i<count($data);$i++)
					{
					$data_array = explode(";", $data[$i]);
					/* Проверка на существование и создание категорий для неотсортированых товаров*/
					$unsorted = getcat("unsorted");
					if (!$unsorted)
					{
					addcategory("unsorted", null, null, null, -999, "N");
					$unsorted = getcat("unsorted");
					addcatxref(0, $unsorted);
					}
					/*Конец созданиии категории*/
					if ($data_array[0] && !$data_array[1] && !$data_array[2])//запись категории 1 уровня
						{
						$data_array[0]=edit_import($data_array[0]);
						$parent = $data_array[0];
						$list_order1++;
						$list_order2 = 0;
						if ($data_array[13]) {$list_order1 = $data_array[13];}
						$category_id1 = getcat($data_array[0]); //запрос существования категории
						if (!$category_id1) //Запись категории
							{
							addcategory($data_array[0], $data_array[7], $data_array[11], $data_array[12], $list_order1, "Y");
							 if ($debug == 1 ) { print "<b>Категории ".$data_array[0]." нет, создаем. </b><br />"; }
						$category_id1 = getcat($data_array[0]); //запрос идентификатора категории
						addcatxref(0, $category_id1); //запись связи с родительской категорией, для первого уровня родительская = 0
							}
							else
							{
							updatecateg($category_id1, $data_array[7], $data_array[11], $data_array[12]);
							}
						unset ($categactiv);
						unset ($subcategactiv);
						}
						
					
					if ($data_array[1] && !$data_array[2])//запись категории 2 уровня
							{
							$list_order2++;
							if ($data_array[13]) {$list_order2 = $data_array[13];}
							$list_order3 = 0;
							$data_array[1]=edit_import($data_array[1]);
							//запрос существования категории
								list ($nextcateg, $categactiv)  = getactive($category_id1, $data_array[1]);
								//Запись категории
								if ($nextcateg == 1) {
									addcategory($data_array[1], $data_array[7], $data_array[11], $data_array[12], $list_order2, "Y");
									if ($debug == 1 ) { print "<b>Подкатегории ".$data_array[1]." нет, создаем. </b><br />"; }
									}
								list ($nextcateg, $categactiv)  = getactive($category_id1, $data_array[1]);	
								if ($nextcateg == 1){addcatxref($category_id1, $categactiv);}
								else { updatecateg($categactiv, $data_array[7], $data_array[11], $data_array[12]); }
							unset ($subcategactiv);
							}
						if ($data_array[2])//запись категории 3 уровня
							{
							$list_order3++;
							if ($data_array[13]) {$list_order3 = $data_array[13];}
							$data_array[2]=edit_import($data_array[2]);
							//запрос существования категории
								list ($nextcateg, $subcategactiv)  = getactive($categactiv, $data_array[2]);
								//Запись категории
								if ($nextcateg == 1) { 
								addcategory($data_array[2], $data_array[7], $data_array[11], $data_array[12], $list_order3, "Y");
								if ($debug == 1 ) { print "<b>Подподкатегории ".$data_array[2]." нет, создаем. </b><br />"; }
									}
								list ($nextcateg, $subcategactiv)  = getactive($categactiv, $data_array[2]);	
								if ($nextcateg == 1)	{addcatxref($categactiv, $subcategactiv);}
								else { updatecateg($subcategactiv, $data_array[7], $data_array[11], $data_array[12]); }
							}
						
									if (!$data_array[0] && !$data_array[1] && !$data_array[2])
										{
										$data_array[3]=htmlspecialchars_decode(edit_import($data_array[3]));//артикул родительского товара
										$data_array[4]=htmlspecialchars_decode(edit_import($data_array[4]));//артикул
										$data_array[5]=htmlspecialchars_decode(edit_import($data_array[5]));//наименование
										$data_array[6]=edit_import($data_array[6]);//краткое описание
										$data_array[7]=edit_import($data_array[7]);//описание
										$data_array[8]=edit_import($data_array[8]);//Цена
										$data_array[9]=edit_import($data_array[9]);//производитель
										$data_array[10]=edit_import($data_array[10]);//остаток
										$data_array[11]=edit_import($data_array[11]);//Мин. Изображение
										$data_array[12]=edit_import($data_array[12]);//Изображение
										$data_array[13]=edit_import($data_array[13]);//Сортировка
										//запрос существования товара в базе
											$product_id =  getproduct ($data_array[4]);
											if (!$product_id)   //если товара нет то создаем его
											{
												if ($debug == 1 ) { print "Товара ".$data_array[5].$data_array[6].$data_array[7]." нет, создаем. <br>"; } 
												addproduct($data_array[3], $data_array[4], $data_array[5], $data_array[6] ,$data_array[7], $data_array[10], $data_array[11], $data_array[12]); //запись товара
												$product_id =  getproduct ($data_array[4]);
												price ($product_id, $data_array[8]);
												if (isset($subcategactiv)) {
												product_xref ($product_id, $subcategactiv, getmanuf($data_array[9]), $data_array[13]); //запись связи товара с категорией 3 уровня
												} 
												else
												{
													if (isset($categactiv)) 
													{
													product_xref ($product_id, $categactiv, getmanuf($data_array[9]), $data_array[13]);//запись связи товара с категорией 2 уровня
													}
													else 
													{
														if ($category_id1) {
														product_xref ($product_id, $category_id1, getmanuf($data_array[9]), $data_array[13]);//запись связи товара с категорией 1 уровня
														}
														else
														{
														//Запись товара в категорию для неотсортированых товаров
															$unsorted = getcat("unsorted");
																if ($unsorted) 
																{
																product_xref ($product_id, $unsorted, getmanuf($data_array[9]), $data_array[13]);
																}
														}
													}
												}
											}
											 else  //если товар есть, то удаляем его цены
											{
											$product_id = updateproduct($data_array[3], $data_array[4], $data_array[5], $data_array[6] ,$data_array[7], $data_array[10], $data_array[11], $data_array[12]);
											price ($product_id, $data_array[8]);
											product_xref ($product_id, null, getmanuf($data_array[9]), $data_array[13]);
											}
											
										}
					}
					@unlink($path."price.csv");
		print  "<center><font color='green'><b>Новый каталог заведён!</b></font></center>";
		//print ("<meta http-equiv=\"refresh\" content=\"2;URL=/price/admin/\">");
			}
				
			
		 
		
	} 
elseif 	($task == "export")
		{
		global $mainframe;
		$mainframe = &JFactory::getApplication();
		$path=JPATH_BASE."/cache/myimport/";
		$path1=JPATH_BASE."/cache/myimport/";
		$params = JComponentHelper::getParams( 'com_myimport');
		$debug = $params->get ('debug');
		function del($string) 
			{
				$string = htmlspecialchars_decode($string, ENT_QUOTES);
				return $string;
			}
		function exportprod ($category, $fp)
		{
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_myimport');
		$database =& JFactory::getDBO();
			$database->setQuery("SELECT * FROM #__vm_product_category_xref WHERE category_id='".$category."' ORDER BY product_list" );
			$prod = $database->loadObjectList();
			foreach($prod as $product) 
				{
				$database->setQuery("SELECT * FROM #__vm_product WHERE product_id='".$product->product_id."'");
				$produc= $database->loadObjectList();
				foreach($produc as $pro) 
					{
						$database->setQuery("SELECT product_price FROM #__vm_product_price WHERE product_id='".$pro->product_id."'");
						$product_price3=$database->loadResult();
						$database->setQuery("SELECT manufacturer_id FROM #__vm_product_mf_xref WHERE product_id='".$pro->product_id."'");
						$datacsv_manufid=$database->loadResult();
						$database->setQuery("SELECT mf_name FROM #__vm_manufacturer WHERE manufacturer_id='".$datacsv_manufid."'");
						$datacsv_manuf=$database->loadResult();
						$datacsv_pid=";;;".del($pro->product_parent_id);
						$datacsv_sku=del($pro->product_sku);
						$datacsv_name=del($pro->product_name);  //Имя продукта
						if($params->get('sdescr') == 0) {$datacsv_s_desc=del($pro->product_s_desc); }
						if($params->get('descr') == 0) {$datacsv_desc=del($pro->product_desc); }
						$product_in_stock=$pro->product_in_stock;
						$datacsv_price    = str_replace('.', ',', $product_price3);
						$datacsv_thumb_image=$pro->product_thumb_image;
						$datacsv_full_image=$pro->product_full_image;
						$datacsv_order= $product->product_list."\n";
						fwrite($fp,$datacsv_pid.";".$datacsv_sku.";".$datacsv_name.";".$datacsv_s_desc.";".$datacsv_desc.";".$datacsv_price.";".$datacsv_manuf.";".$product_in_stock.";".$datacsv_thumb_image.";".$datacsv_full_image.";".$datacsv_order);
					}
				}
				
		}
		function exportcateg ($category, $level, $fp)
		{
		global $mainframe;
		$database =& JFactory::getDBO();
		$mainframe = &JFactory::getApplication();
		$params = JComponentHelper::getParams( 'com_myimport');
		$debug = $params->get ('debug');
		
			$database->setQuery("SELECT * FROM #__vm_category_xref WHERE category_parent_id='".$category."' ");
			$cat = $database->loadObjectList();
			if ($level == 0) {$amp = ";;";} elseif ($level == 1) {$amp = ";";} else  {$amp = "";}
			foreach($cat as $category) {
			$database->setQuery("SELECT *  FROM #__vm_category WHERE category_id='".$category->category_child_id."'");
			$categ = $database->loadObjectList();
			foreach($categ as $categi) {
			if ($debug == 1 ) { print "Подкатегория 1-". ":" .$subcat. "<br>"; }
			$datacsv=str_repeat(";",$level).$categi->category_name.$amp.";;;;".$categi->category_description.";;;;;".$categi->category_thumb_image.";".$categi->category_full_image.";".$categi->list_order."\n";
			fwrite($fp,$datacsv);
			}
			if ($level == 0) { exportcateg ($category->category_child_id, 1 , $fp); }
			if ($level == 1) { exportcateg ($category->category_child_id, 2 , $fp); }
			exportprod ($category->category_child_id, $fp);
			}
			
			
			return;
		}
		$fp = fopen($path."export_price.csv", 'w+');
		@chmod($path."export_price.csv", 0777);

$fp = fopen($path."export_price.csv", 'a+');

////
   			$datacsv_cat="Категория";//1
			$datacsv_subcat="Подкатегория";//2
			$datacsv_usubcat="Подподкатегория";//3
			$datacsv_pid="Товар-родитель";//4
			$datacsv_sku="Артикул";//5
			$datacsv_name="Наименование";//6
			$datacsv_s_desc="Краткое описание";//7
			$datacsv_desc="Описание";//8
			$datacsv_price="Цена";//9
			$datacsv_manuf="Производитель";//10
			$datacsv_ostatok="Остаток";//11
			$datacsv_thumb_image="Мин. Изображение";//12
			$datacsv_full_image="Изображение";//13
			$datacsv_order="Сортировка";//13
			  
			fwrite($fp,$datacsv_cat.";".$datacsv_subcat.";".$datacsv_usubcat.";".$datacsv_pid.";".$datacsv_sku.";".$datacsv_name.";".$datacsv_s_desc.";".$datacsv_desc.";".$datacsv_price.";".$datacsv_manuf.";".$datacsv_ostatok.";".$datacsv_thumb_image.";".$datacsv_full_image.";".$datacsv_order."\n");
			////
			$database =& JFactory::getDBO();
			$mainframe = &JFactory::getApplication();

			exportcateg (0, 0, $fp);

	fclose($fp);
	$file=file_get_contents("cache/myimport/export_price.csv"); 
	$file=iconv("utf-8", "windows-1251",$file); 
	file_put_contents("cache/myimport/export_price.csv",$file);
		?>
Екcпорт товара успешно завершен
<a href="<? echo "cache/myimport/export_price.csv"; ?>">Загрузить прайс</a><?
	}
elseif 	($task == "about") 
	{
		include('about.myimport.php');
	}
	elseif 	($task == "versions") 
	{
		include('versions.myimport.php');
	}
		else
		{ ?>
			<style type="text/css">
			<!--
			.style1 {color: #0099FF}
			-->
			</style>
			 <table width="100%" class="adminform">
					<tr>

					   <td width="20%" valign="top">

						<div id="cpanel">
						  <div style="float:left;">
						<div class="icon">
							<a href="index2.php?option=com_myimport&task=import" style="text-decoration:none;" title="Импорт">
							<img src="components/com_myimport/images/addedit.png" width="48px" height="48px" align="middle" border="0"/>
							<br />
			Импорт	            </a>			</div>
					</div>

							<div style="float:left;">
						<div class="icon">
							<a href="index2.php?option=com_myimport&task=export" style="text-decoration:none;" title="Експорт">
							<img src="components/com_myimport/images/backup.png" width="48px" height="48px" align="middle" border="0"/>
							<br />
							Экспорт	            </a>			</div>
					</div>

			<div style="float:left;">
						<div class="icon">
							<a href="index2.php?option=com_myimport&task=about" style="text-decoration:none;" title="Справка">
							<img src="components/com_myimport/images/info.png" width="48px" height="48px" align="middle" border="0"/>
							<br />
							Справка	            </a>			</div>
					</div>
				
			<div style="float:left;">
						<div class="icon">
							<a href="index2.php?option=com_myimport&task=versions" style="text-decoration:none;" title="История версий">
							<img src="components/com_myimport/images/history.png" width="48px" height="48px" align="middle" border="0"/>
							<br />
							История версий	            </a>			</div>
					</div>
			</div>
						<!-- ICON END --></td>
					  <td width="20%" align="center" valign="middle"><p><strong>Компонент импорта/экспорта товаров в Virtuemart 1.1.x для Joomla 1.5.x</strong></p>
           <p class="style1">MyImport 1.5.6.1</p></td>
			  </tr></table>
<? 
		}
 ?>


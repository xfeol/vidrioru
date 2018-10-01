<?php
////////////////////////////////////////////////////////
// Компонент сервиса VirtuemartCare	                  //
// Разработан для Joomla 1.5.x 						  //
// 2012 (C) Beagler   (beagler.ru@gmail.com)          //
////////////////////////////////////////////////////////
header('Content-Type: text/html; charset=utf-8');
defined('_JEXEC') or die('Restricted access');
global $mainframe;
//$params = JComponentHelper::getParams('com_virtuemartcare');
$debug = 0;
if ($debug == 1) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
if ($task == "photo") {
	JHTML::_('behavior.calendar');
	
	function setwatermark()
	{
	    if($field_name=='product_thumb_image' && PSHOP_IMG_WATERMARK=='1') {
					global $mosConfig_absolute_path;
                    $watermark = $mosConfig_absolute_path.PSHOP_IMG_WATERMARK_PATH;
                    $offset = 0;
                    $image = GetImageSize($_FILES['product_full_image']["tmp_name"]);
                    $xImg = $image[0];
                    $yImg = $image[1];
                    switch ($image[2]) {
                        case 1:
                            $img=imagecreatefromgif($_FILES['product_full_image']["tmp_name"]);
                            $filetype="gif";
                        break;
                        case 2:
                            $img=imagecreatefromjpeg($_FILES['product_full_image']["tmp_name"]);
                            $filetype="jpg";
                        break;
                        case 3:
                            $img=imagecreatefrompng($_FILES['product_full_image']["tmp_name"]);
                            $filetype="png";
                        break;
                        }

                    $r = imagecreatefrompng($watermark);
                    $x = imagesx($r);
                    $y = imagesy($r);
					switch (PSHOP_IMG_WATERMARK_RASP) {
                        case 1:	//Левый верхний
							$xDest =  $offset;
							$yDest =  $offset;
							break;
                        case 2:	//Правый верхний
							$xDest = $xImg - ($x + $offset);
							$yDest = $offset;
							break;
                        case 3:	//Левый нижний
							$xDest = $offset;
							$yDest = $yImg - ($y + $offset);
							break;
                        case 4:	//Правый нижний
							$xDest = $xImg - ($x + $offset);
							$yDest = $yImg - ($y + $offset);
							break;
                        case 5:	//По центру
							$xDest = ($xImg/2) - ($x + $offset)/2;
							$yDest = ($yImg/2) - ($y + $offset)/2;
							break;
						}	
                    imageAlphaBlending($img,1);
                    imageAlphaBlending($r,1);
                    imagesavealpha($img,1);
                    imagesavealpha($r,1);
                    imagecopyresampled($img,$r,$xDest,$yDest,0,0,$x,$y,$x,$y);
                    switch ($filetype) {
                            case "jpg":
                                imagejpeg($img,$_FILES['product_full_image']["tmp_name"],100);
                            break;
                            case "jpeg":
                                imagejpeg($img,$_FILES['product_full_image']["tmp_name"],100);
                            break;
                            case "gif":
                                imagegif($img,$_FILES['product_full_image']["tmp_name"]);
                            break;
                            case "png":
                                imagepng($img,$_FILES['product_full_image']["tmp_name"]);
                            break;
                        }
                    imagedestroy($r);
                    imagedestroy($img);

                    
                }
				// End from Beagler
	}

    function imageresize($outfile, $infile, $neww, $newh, $quality, $expand,$IMGDATE,$topdate) 
	{
        $MY_JPATH_BASE = str_replace('administrator', '', JPATH_BASE);
		$filepath=$MY_JPATH_BASE . 'components' . DS . 'com_virtuemart' . DS . 'shop_image' . DS . 'product' . DS;
		if (!file_exists($filepath. $infile)) {
            echo '<font color="red">Ошибка. Нет файла ' . $filepath. $infile . '</font><br/>';
            return;
        }
		$top=explode('-',$topdate);
		$ft=filectime($filepath. $infile);
		if ($IMGDATE==1 && $ft<mktime(0, 0, 0, (int)$top[1], (int)$top[0]-1, (int)$top[2])) {
					//echo 'Не попал в диапазон дат ' . $outfile .' '.date("d-m-Y",$ft).' '.$ft.' '.mktime(0, 0, 0, (int)$top[1], (int)$top[0], (int)$top[2]).'<br/>';
                    return;
        }
         
        if (file_exists($filepath . $outfile)) {
		
            $imout = imagecreatefromjpeg($filepath . $outfile);
			if( imagesx($imout)==$neww && imagesy($imout)==$newh) {
				imagedestroy($imout);
				return;
			}
			if (!is_writable($filepath . $outfile)) {
				echo '<font color="red">Ошибка. Измените права! Немогу записать файл components' . DS . 'com_virtuemart' . DS . 'shop_image' . DS . 'product' . DS . $outfile . '</font><br/>';
				imagedestroy($imout);
				return;
			}
			imagedestroy($imout);
            
        }

        $im = imagecreatefromjpeg($filepath . $infile);
        $k1 = $neww / imagesx($im);
        $k2 = $newh / imagesy($im);
        $k = $k1 > $k2 ? $k2 : $k1;

        $w = intval(imagesx($im) * $k);
        $h = intval(imagesy($im) * $k);

        // Если не увеличивать - уходим
		
		if( $expand==1 && (imagesx($im)<$neww || imagesy($im)<$newh || (imagesx($im)==$neww && imagesy($im)==$newh))) {
			imagedestroy($im);
			return;
		}
		
		$im1 = imagecreatetruecolor($w, $h);
        $im2 = imagecreatetruecolor($neww, $newh);
        
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $w, $h, imagesx($im), imagesy($im));
        
        if (imagesx($im)>imagesy($im)) {
            imagecopy($im2, $im1, 0, ($newh / 2) - ($h / 2), 0, 0, $neww, $newh);        
            $bgcolor=imagecolorallocate ($im2, 255, 255, 255);
            imagefilledrectangle($im2, 0, 0, $neww, ($newh / 2) - ($h / 2), $bgcolor);
            imagefilledrectangle($im2, 0, ($newh / 2) - ($h / 2)+$h, $neww,$newh,$bgcolor);
        }
        else {
            imagecopy($im2, $im1, ($neww / 2) - ($w / 2), 0, 0, 0, $neww, $newh);
            $bgcolor=imagecolorallocate ($im2, 255, 255, 255);
            imagefilledrectangle($im2, 0, 0, ($neww / 2) - ($w / 2), $newh, $bgcolor);
            imagefilledrectangle($im2, ($neww / 2) - ($w / 2)+$w, 0, $neww,$newh,$bgcolor);
        }
        
       
        if (!imagejpeg($im2, $filepath . $outfile, $quality))
            echo '<font color="red">Ошибка. Неудачное сохранение components' . DS . 'com_virtuemart' . DS . 'shop_image' . DS . 'product' . DS . $outfile . '</font><br/>';
        else
            echo '<font color="blue">Успешно обработан файл ' . $outfile .' '.date("d-m-Y",$ft).'</font><br/>';
        imagedestroy($im);
        imagedestroy($im1);
		imagedestroy($im2);

        return;
    }

    //--------- Begin
    $path = JPATH_BASE . "/cache/serv_virt";
	$IMG_DATE_WATERMARK='';
	$top_date_watermark='';
	$IMG_WATERMARK_RASP='';
	$IMG_WATERMARK_PATH='';
	$IMG_THUMB_WATERMARK='';
	$IMG_BIG_WATERMARK='';
	$IMG_DATE='';
	$top_date='';
    $MINI_IMG_WIDTH = '';
    $MINI_IMG_HEIGHT = '';
    $IMG_WIDTH = '';
    $IMG_HEIGHT = '';
	$IMG_EXPAND='';
	if (JRequest::getVar('func', null) == 'watermark') 
	{
		$IMG_DATE_WATERMARK=JRequest::getint('IMG_DATE_WATERMARK', 0);
		$top_date_watermark=JRequest::getvar('top_date_watermark', '');
		$IMG_WATERMARK_RASP=JRequest::getint('IMG_WATERMARK_RASP', '');
		$IMG_WATERMARK_PATH=JRequest::getvar('IMG_WATERMARK_PATH', '');
		$IMG_THUMB_WATERMARK=JRequest::getint('IMG_THUMB_WATERMARK', '');
		$IMG_BIG_WATERMARK=JRequest::getint('IMG_BIG_WATERMARK', '');?>
		<fieldset>
		<legend>Лог наложения водяных знаков</legend>
		Функция в процессе отладки, ждите следующую бэту.
		</fieldset>
		<?php
		
	}
	
    if (JRequest::getVar('func', null) == 'changephoto') {
        global $mainframe;
		
		$IMG_DATE=JRequest::getint('IMG_DATE', 0);
		$top_date=JRequest::getvar('top_date', '');
        $MINI_IMG_WIDTH = JRequest::getint('MINI_IMG_WIDTH', 0);
        $MINI_IMG_HEIGHT = JRequest::getint('MINI_IMG_HEIGHT', 0);
        $IMG_WIDTH = JRequest::getint('IMG_WIDTH', 0);
        $IMG_HEIGHT = JRequest::getint('IMG_HEIGHT', 0);
		$IMG_EXPAND=JRequest::getint('IMG_EXPAND', 0);
		
        $mainframe = &JFactory::getApplication();
        $database = & JFactory::getDBO();
        $database->setQuery("SELECT * FROM #__vm_product");
        $list = $database->loadObjectList();?>
		<fieldset>
		<legend>Лог выполнения операций с файлами</legend>
		<?php
        foreach ($list as $product) {
            if ($IMG_WIDTH > 0 && $IMG_HEIGHT > 0)
                imageresize($product->product_full_image, $product->product_full_image, $IMG_WIDTH, $IMG_HEIGHT, 100, $IMG_EXPAND,$IMG_DATE,$top_date);
            if ($MINI_IMG_WIDTH > 0 && $MINI_IMG_HEIGHT > 0)
                imageresize($product->product_thumb_image, $product->product_full_image, $MINI_IMG_WIDTH, $MINI_IMG_HEIGHT, 100, $IMG_EXPAND,$IMG_DATE,$top_date);


            // echo $product->product_thumb_image.'<BR/>';
            // echo $product->product_full_image.'<BR/>';
        }?>
		</fieldset>
		<?php
		if($MINI_IMG_WIDTH ==0) $MINI_IMG_WIDTH='';
		if($MINI_IMG_HEIGHT ==0) $MINI_IMG_HEIGHT='';
		if($IMG_WIDTH ==0) $IMG_WIDTH='';
		if($IMG_HEIGHT ==0) $IMG_HEIGHT='';
		
      
	}
    // Выводим форму для загрузки файла.
    ?>
    <form name="photo" method=post action="<?php echo JURI::base(true); ?>/index.php">
        <fieldset style="float:left;">
            <legend>Изменение размеров фото</legend>
			<fieldset>
			<legend>Превью (маленькое фото)</legend>
            <table  width="100%"><tr>
                    <td class="labelcell" width="180px">Ширина мини-изображения</td>
                    <td>
                        <input type="text" name="MINI_IMG_WIDTH" class="inputbox" value="<?php echo $MINI_IMG_WIDTH; ?>" />
                    </td>
                    
                </tr>
                <tr>
                    <td class="labelcell" width="180px">Высота мини-изображения</td>
                    <td>
                        <input type="text" name="MINI_IMG_HEIGHT" class="inputbox" value="<?php echo $MINI_IMG_HEIGHT; ?>" />
                    </td>
                    
                </tr>
				</table >
			</fieldset>
			<fieldset>
			<legend>Большое фото</legend>
				<table  width="100%">
                <tr>
                    <td class="labelcell" width="180px">Ширина большого изображения</td>
                    <td>
                        <input type="text" name="IMG_WIDTH" class="inputbox" value="<?php echo $IMG_WIDTH; ?>" />
                    </td>
                    
                </tr>
                <tr>
                    <td class="labelcell" width="180px">Высота большого изображения</td>
                    <td>
                        <input type="text" name="IMG_HEIGHT" class="inputbox" value="<?php echo $IMG_HEIGHT; ?>" />
                    </td>
                    
                </tr>            
				<tr>
                    <td class="labelcell" width="180px">Не увеличивать фото</td>
                    <td>
					
                        <input type="checkbox" name="IMG_EXPAND" class="inputbox" <?php if ($IMG_EXPAND == '1') echo 'checked="checked"'; ?>  value="1" />
                    </td>
                    
                </tr></table>            
            </fieldset>
			<fieldset>
			<legend>Обрабатывать файлы, залитые после выбранной даты</legend>
				<table width="100%">
                <tr>
                    <td class="labelcell" width="180px">Использовать дату </td>
                    <td>
						 <input type="checkbox" name="IMG_DATE" class="inputbox" <?php if ($IMG_DATE == '1') echo 'checked="checked"'; ?>  value="1" title="Чтобы использовать дату - поставьте галку"/>
                        <?php echo JHTML::_('calendar', $top_date, 'top_date', 'top_date', '%d-%m-%Y', array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19')); ?>
                    </td>
                    
                </tr></table>
		
		</fieldset>
            <input type=submit value=Старт>
        </fieldset>
		
        <input type="hidden" value="com_virtuemartcare" name="option">
        <input type="hidden" value="photo" name="task">
        <input type="hidden" value="changephoto" name="func">
    </form>
	<form name="photo" method=post action="<?php echo JURI::base(true); ?>/index.php">
        <fieldset style="float:left;">
            <legend>Установка watermark на фото</legend>
			<table width="100%">
			
		<tr>
	        <td class="labelcell">Расположение</td>
	        <td>
	           <?php
			$imageArrrasp['0']='Выберите расположение';   
			$imageArrrasp['1']='Слева вверху';
			$imageArrrasp['2']='Справа вверху';
			$imageArrrasp['3']='Слева внизу';
			$imageArrrasp['4']='Справа внизу';
			$imageArrrasp['5']='По центру';
			
			//echo ps_html::selectList('conf_PSHOP_IMG_WATERMARK_RASP', PSHOP_IMG_WATERMARK_RASP, $imageArrrasp );
	        ?>
	        </td>
	        
	    </tr>
		
		<tr>
                    <td class="labelcell" width="180px">Устанавливать на большие фото</td>
                    <td>
					
                        <input type="checkbox" name="IMG_BIG_WATERMARK" class="inputbox" <?php if ($IMG_BIG_WATERMARK == '1') echo 'checked="checked"'; ?>  value="1" />
                    </td>
                    
        </tr>
		<tr>
                    <td class="labelcell" width="180px">Устанавливать на превью</td>
                    <td>
					
                        <input type="checkbox" name="IMG_THUMB_WATERMARK" class="inputbox" <?php if ($IMG_THUMB_WATERMARK == '1') echo 'checked="checked"'; ?>  value="1" />
                    </td>
                    
        </tr>
		<tr>
	        <td class="labelcell">Путь к изображению водяного знака</td>
	        <td>
	            <input type="text" name="IMG_WATERMARK_PATH" class="inputbox" value="<?php echo $IMG_WATERMARK_PATH ?>" />
	        </td>
	        
	    </tr>
		</table>
		<fieldset>
			<legend>Обрабатывать файлы, залитые после выбранной даты</legend>
				<table width="100%">
                <tr>
                    <td class="labelcell" width="180px">Использовать дату </td>
                    <td>
						 <input type="checkbox" name="IMG_DATE_WATERMARK" class="inputbox" <?php if ($IMG_DATE_WATERMARK == '1') echo 'checked="checked"'; ?>  value="1" title="Чтобы использовать дату - поставьте галку"/>
                        <?php echo JHTML::_('calendar', $top_date_watermark, 'top_date_watermark', 'top_date_watermark', '%d-%m-%Y', array('class'=>'inputbox', 'size'=>'15',  'maxlength'=>'19')); ?>
                    </td>
                    
                </tr></table>
		
		</fieldset>
            <input type=submit value=Старт>
        </fieldset>
        <input type="hidden" value="com_virtuemartcare" name="option">
        <input type="hidden" value="photo" name="task">
        <input type="hidden" value="watermark" name="func">
    </form>
    <?php
}

elseif ($task == "price") {
    include('price.virtuemartcare.php');
} else {
    ?>
    <style type="text/css">
        <!--
        .style1 {color: #0099FF}
        -->
    </style>
    <table width="100%" class="adminform">
        <tr>

            <td valign="top">

                <div id="cpanel">
                    <div style="float:left;">
                        <div class="icon">
                            <a href="index2.php?option=com_virtuemartcare&task=photo" style="text-decoration:none;" title="Сервис фото">
                                <img src="components/com_virtuemartcare/images/sfoto.jpg"  align="middle" border="0"/>
                                <br />
                                Сервис фото	            </a>			</div>
                    </div>


                    <div style="float:left;">
                        <div class="icon">
                            <a href="index2.php?option=com_virtuemartcare&task=price" style="text-decoration:none;" title="Сервис цен">
                                <img src="components/com_virtuemartcare/images/sprice.jpg"  align="middle" border="0"/>
                                <br />
                                Сервис цен	            </a>			</div>
                    </div>

                  
                </div>
                <!-- ICON END --></td>
            <td align="center" valign="middle"><p><strong>Компонент сервиса VirtuemartCare для Virtuemart 1.1.x и Joomla 1.5.x</strong></p>
                <p class="style1">VirtuemartCare 0.0.8b</p></td>
        </tr></table>
    <?php
}
?>


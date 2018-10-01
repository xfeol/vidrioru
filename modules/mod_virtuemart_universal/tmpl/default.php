<?php // no direct access
defined('_JEXEC') or die('Restricted access');
/*
	@author Beliyadm @license		GNU/GPL
	Справка по использованию шаблона:
	$item->pname 	- заголовок (название) товара
	$item->link 	- ссылка на полную карточку товара
	$item->pimage 	- картинка товара, не забываем про полный путь до нее
	$item->price 	- цена товара
	$item->currency - валюта (денежная единица)
	$item->intro	- краткое описание товара, очищенное от html тегов, по умолчанию выводится как TITLE для картинки и ссылки "подробнее"
*/
?>
<?php foreach ($list as $item) :  ?>
    <div class="mod_vm_universal">
       
        <?php if ($item->discount == '1') { ?>
        	<img src="<?php echo JURI::base(); ?>modules/mod_virtuemart_universal/files/ico_discount.png" class="discount" alt="Скидка на продукт!" />
	    <?php } else {} ?>
	    <a href="<?php echo $item->link; ?>" title="<?php echo $item->pname; ?> - <?php echo $item->intro; ?>" >
		<span class="mod_vm_title"><?php echo $item->pname; ?></span>
    		<div><img style="margin:10px;width:80%;" src="<?php echo $item->pimage; ?>" alt="<?php echo $item->pname; ?> - <?php echo $item->intro; ?>" /></div>
	    </a>
     	<span class="mod_vm_price"><?php echo $item->price; ?> р.</span><hr />
	</div>
<?php endforeach; ?>
<div class="clear"></div>
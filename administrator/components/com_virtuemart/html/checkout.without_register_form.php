<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
//defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );
/**
*
* @version $Id: checkout_without_register_form.php,v 1.0 2006/10/20 23:31 tug Exp $
* @package VirtueMart
* @subpackage html
* @copyright Copyright (C) 2004-2005 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
!!!!!!!! ПОДСТАВИЛ В ПЕРЕМЕННУЮ$vendor_email['from'] ЛЕВОЕ ЗНАЧЕНИЕ И ТОГДА ОТПРАВЛЯЕТСЯ АДМИНУ НА МЫЛО ЕСЛИ НЕ ЮЗЕР НЕ ХОЧЕТ ОТПРАВЛЯТЬСЕБЕ!!!*  is 

* 

See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
*
* http://virtuemart.net
*/
mm_showMyFileName( __FILE__ );

//require_once(CLASSPATH . "ps_userfield.php");
//$fields = ps_userfield::getUserFields('registration', false, '', false);
//foreach ($fields as $field) $field->readonly = 0;
//$skip_fields = array('username', 'password', 'password2');
//ps_userfield::listUserFields( $fields, $skip_fields);

//echo '<div align="center">';
//echo '<input type="submit" value="'. $VM_LANG->_('BUTTON_SEND_REG') . '" class="button" onclick="return( submitregistration());" />
//    </div>
//    </form>';

$checkout = vmGet($_POST, 'checkout', '');
	$error = '';
	if ( is_array($checkout) && count($checkout) ) {
		require_once( CLASSPATH.'ps_main.php' );
		
//		if( $checkout['name'] == '' || vmValidateName($checkout['name']) == false  ) {
//			$error .= '<p>Вы не указали имени контактного лица или указали неверно.</p>';
//		}
//		
//    preg_replace('/\D/', '', $checkout['phone']);
//		if ( $checkout['phone'] == '' || strlen($checkout['phone']) < 6 ) {
//			$error .= '<p>Вы не указали контактного телефона или указали неверно.</p>';
//		}
//		
//		if ( $checkout['address'] == '' || strlen($checkout['address']) < 8 ) {
//			$error .= '<p>Вы не указали свой адрес или указали неверно.</p>';
//		} 		

		
		$checkout['customer_copy'] = 1;
		if ( function_exists('vmValidateEmail') ) {
			$email_check = vmValidateEmail($checkout['email']);
		}
		else {
			$email_check = mShop_validateEmail($checkout['email']);
		}
//		
//		if ( ($checkout['customer_copy'] == 1 && !$checkout['email']) || ($checkout['customer_copy'] == 1 && $email_check == false) ) {
//			$error .= '<p>Вы указали, что хотите получить копию заказа на свою электронную почту, но не указали адрес или указали неверно.</p>';
//		} 		
	}

if ( !is_array($checkout) || !count($checkout) || !empty($error) ) {
?> 


    <style> .errorcolor  {color: red;} </style>
    <script language="javascript" type="text/javascript">//<![CDATA[
    function submitregistration() {
        var form = document.checkoutForm;
        var isvalid = true;
        var errmsg = "";
        
        var x = document.forms["checkoutForm"]["checkout[name]"].value;
        if (x == null || x == "")
        {
    	    document.getElementById('name_div').className = 'formLabel missing';
    	    errmsg += "Пожалуйста, введите Контактное лицо!\r";
    	    isvalid = false;
	} else {
	    document.getElementById('name_div').className = 'formLabel';
	}
        x = document.forms["checkoutForm"]["checkout[phone]"].value;
        if (x == null || x == "")
        {
    	    document.getElementById('phone_div').className = 'formLabel missing';
    	    errmsg += "Пожалуйста, введите контактный номер!\r";
    	    isvalid = false;
        } else {
    	    document.getElementById('phone_div').className = 'formLabel';
        }
	x = document.forms["checkoutForm"]["checkout[address]"].value;
	if (x == null || x == "")
	{
	    document.getElementById('address_div').className = 'formLabel missing';
	    errmsg += "Пожалуйста, введите адрес доставки!\r";
	    isvalid = false;
	} else {
	    document.getElementById('address_div').className = 'formLabel';
	}
        
        
        if (!isvalid)
    	    alert(errmsg);
        return isvalid;
    }
    //]]>
    </script>
<form method="post" action="index.php" id="without_register_form" name="checkoutForm" onsubmit="return submitregistration();" >
 <?php if($error) echo '<fieldset><legend>Ошибка</legend>'.$error.'</fieldset>'; ?>
 
 <?php if (file_exists("/home/vidrioru/public_html/media/vmart/checkout.php")) include("/home/vidrioru/public_html/media/vmart/checkout.php");  ?>
 
 <fieldset>
	 <legend class="sectiontableheader">Оформление заказа.</legend>
   <div>
	 <div style="padding:5px;text-align:center;"><strong>(* = Обязательно)</strong></div>
	 
	 <div id ="name_div" class="formLabel"> <label for="name">Контактное лицо* <br /></label></div>
		<input id="name" name="checkout[name]" value="<?php if ( $checkout ) echo $checkout['name']; ?>" class="inputbox" /><br />
	 
	 <div id="phone_div" class="formLabel"><label for="phone">Телефон* <br /></label></div>
		<input id="phone" name="checkout[phone]" value="<?php if ( $checkout ) echo $checkout['phone']; ?>" class="inputbox" /><br />
    
	 <div id="address_div" class="formLabel"> <label for="address">Адрес* <br /></label></div>
		<input id="address" name="checkout[address]" value="<?php if ( $checkout ) echo $checkout['address']; ?>" class="inputbox" /><br />
	 
	 <div id="email_div" class="formLabel"><label for="email">E-mail <br /></label></div>
		<input id="email" name="checkout[email]" value="<?php if ( $checkout ) echo $checkout['email']; ?>" class="inputbox" /><br />
		
	 <div id="comment_div" class="formLabel"><label for="comment">Комментарий: <br /></label></div>
		<textarea id="comment" rows="6" name="checkout[comment]" class="inputbox"><?php if ( $checkout ) echo $checkout['comment']; ?></textarea><br />
		
		<div id="submit_wrap">
			<input type="submit"  value="Оформить заказ" class="button" />
		</div>
		<div id="continue_wrap">
		    <input type="button" value="<?php echo $VM_LANG->_('PHPSHOP_CONTINUE_SHOPPING') ?>" class="button" onclick="location.href='<?php echo $continue_link ?>'" />

		</div>
		
		<input type="hidden" name="option" value="com_virtuemart" />
		<input type="hidden" name="page" value="checkout.without_register_form" />
		
    </div>
 </fieldset>
</form>

<style>
<!--
#without_register_form {
	font-size: 1em; /* размер шрифта формы */
}

#without_register_form fieldset {
	/*width: 35em;  /* рамка вокруг формы */
	border: solid 1px #9BC89B;  /* нормальная граница рамки */
}

#without_register_form legend {
}

#without_register_form label {
	//float: left; /* подписи к полям сдвигаются влево */
	width: 15em; /* ширина колонки подписей */
	line-height: 1em;
	margin-right: 0.1em; /* отступ до поля ввода */
	//text-align: right;
}

#without_register_form input, #without_register_form select, #without_register_form textarea {
	margin: 0 0 0.5em 0.2em;
	width: 80%; /* ширина полей формы */
	max-width: 320px;
	padding: 3 5;
	font-size: 100%;
}

#without_register_form .noresize {
	width:auto; /* для кнопок, переключателей */
}

#without_register_form .submit {
	/* сдвигаем кнопку отправки */
	/*margin-left:;*/
	text-align: right;
}

#without_register_form .formLabel {
    float:none;
    text-align: left;
}

#submit_wrap .button , #continue_wrap .button {
    border:none;
    width:40%;
    text-transform: none;
    border-radius: 3px;
    height:50px;
    word-wrap:pre-line;
    word-break:break-all;
    white-space:normal;
}

#continue_wrap .button {
    float: right;
    margin-right:1em;
}

#submit_wrap .button {
    float: left;
    border-radius: 3px;
    background-color: #DD0000;
}

-->
</style>

<?php
}

else {
	global $cart, $sess, $VM_LANG, $CURRENCY_DISPLAY;
	
	// Проверяем чтобы был хотябы один товар.
	if( $cart['idx']!=0 ) vmRedirect('index.php?option=com_virtuemart');

	
	$ps_vendor_id = $_SESSION["ps_vendor_id"];
	require_once(CLASSPATH. 'ps_checkout.php' );
	$ps_checkout = new ps_checkout;
	require_once(CLASSPATH. 'ps_product.php' );
	$ps_product = new ps_product;
	require_once(CLASSPATH.'ps_cart.php');
	$ps_cart = new ps_cart;
	
	$db = new ps_DB;
	
	//if (AFFILIATE_ENABLE == '1') {
	//	require_once(CLASSPATH.'ps_affiliate.php');
	//	$ps_affiliate = new ps_affiliate;
	//}
	
	/* Генерим уникальный номер заказа в системе VM */
	$order_number = $ps_checkout->get_order_number();
	
	/* Подсчитываем примерную общую стоимость без учёта налогов и доставки */
	$order_subtotal = $tmp_subtotal = $ps_checkout->calc_order_subtotal($d);
	
	//$order_taxable = $ps_checkout->calc_order_taxable($d);
	//$payment_discount = $d['payment_discount'] = $this->get_payment_discount($d['payment_method_id'], $order_subtotal);
	
	/* DISCOUNT HANDLING */
	if( !empty($_SESSION['coupon_discount']) ) {
		$coupon_discount = floatval($_SESSION['coupon_discount']);
	}
	else {
		$coupon_discount = 0.00;
	}
	
	// from now on we have $order_tax_details
	//$d['order_tax'] = $order_tax = round( $this->calc_order_tax($order_taxable, $d), 2 );

	// Проверяем чтобы сумма заказа не ушла в минус
	if( $tmp_subtotal < 0 ) $order_subtotal = $tmp_subtotal = 0;
	//if( $order_taxable < 0 ) $order_taxable = 0;

	// from now on we have $order_tax_details
	//$d['order_tax'] = $order_tax = round( $ps_checkout->calc_order_tax($order_taxable, $d), 2 );
	//if( $this->_SHIPPING ) {			
		/* sets _shipping */
	//	$d['order_shipping'] = $order_shipping = round( $this->calc_order_shipping( $d ), 2 );

		/* sets _shipping_tax
		* btw: This is WEIRD! To get an exactly rounded value we have to convert
		* the amount to a String and call "round" with the string. */
	//	$d['order_shipping_tax'] = $order_shipping_tax = round( strval($this->calc_order_shipping_tax($d)), 2 );
		
		//$shipping_taxrate = $this->_SHIPPING->get_tax_rate();
		//@$order_tax_details[$shipping_taxrate] += $order_shipping_tax;
	//}
	//else {
	//	$d['order_shipping'] = $order_shipping = $order_shipping_tax = $d['order_shipping_tax'] = 0.00;
	//}

	$timestamp = time() + ($mosConfig_offset*60*60);

	// Вычисление итоговой суммы превращается в вычитании из предположительной суммы скидки по купону
	$d['order_total'] = $order_total = 	$tmp_subtotal 
										/*+ $order_tax */
										/*+ $order_shipping */
										/*+ $order_shipping_tax*/
										- $coupon_discount
										/*- $payment_discount*/;
	
	//$order_tax *= $discount_factor;
	
	//if (!$this->validate_form($d)) {
	//	return false;
	//}

	//if (!$this->validate_add($d)) {
	//	return false;
	//}

	// Проверяем на отрицательное значение итоговую сумму заказа
	if( $order_total < 0 ) $order_total = 0;

	// Округляем итоговую сумму заказа до второго знака после запятой
	$order_total = round( $order_total, 2);
	
	// Пишем в лог отладочную информацию
$vmLogger->debug( '-- Checkout Debug--		
Subtotal: '.$order_subtotal.'
Coupon Discount: '.$coupon_discount.'
------------------------
Order Total: '.$order_total.'
----------------------------' 
);
	
	// Check to see if Payment Class File exists
	//$payment_class = $ps_payment_method->get_field($d["payment_method_id"], "payment_class");
	//$enable_processor = $ps_payment_method->get_field($d["payment_method_id"], "enable_processor");

	//if (file_exists(CLASSPATH . "payment/$payment_class.php") ) {
	//	if( !class_exists( $payment_class ))
	//	include( CLASSPATH. "payment/$payment_class.php" );

	//	eval( "\$_PAYMENT = new $payment_class();" );
	//	if (!$_PAYMENT->process_payment($order_number,$order_total, $d)) {
	//		$vmLogger->err( $VM_LANG->_('PHPSHOP_PAYMENT_ERROR')." ($payment_class)" );
	//		$_SESSION['last_page'] = "checkout.index";
	//		$_REQUEST["checkout_next_step"] = CHECK_OUT_GET_PAYMENT_METHOD;
	//		return False;
	//	}
	//}

	//else {
	//	$d["order_payment_log"] = $VM_LANG->_('PHPSHOP_CHECKOUT_MSG_LOG');
	//}

	// Если купон был подарочным, то мы его удаляем
	if( @$_SESSION['coupon_type'] == "gift" ) {
		$d['coupon_id'] = $_SESSION['coupon_id'];
		include_once( CLASSPATH.'ps_coupon.php' );
		ps_coupon::remove_coupon_code( $d );
	}
  
  // Получаем IP
  if (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	else {
		$ip = 'unknown';
	}
	
  /* 1.0.9
  `order_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `vendor_id` int(11) NOT NULL default '0',
  `order_number` varchar(32) default NULL,
  `user_info_id` varchar(32) default NULL,
  `order_total` decimal(10,2) NOT NULL default '0.00',
  `order_subtotal` decimal(10,5) default NULL,
  `order_tax` decimal(10,2) default NULL,
  `order_shipping` decimal(10,2) default NULL,
  `order_shipping_tax` decimal(10,2) default NULL,
  `coupon_discount` decimal(10,2) NOT NULL default '0.00',
  `order_discount` decimal(10,2) NOT NULL default '0.00',
  `order_currency` varchar(16) default NULL,
  `order_status` char(1) default NULL,
  `cdate` int(11) default NULL,
  `mdate` int(11) default NULL,
  `ship_method_id` varchar(255) default NULL,
  `customer_note` text NOT NULL,
  `ip_address` varchar(15) NOT NULL default '',
  */
	/* Добавляем в базу основную информацию о заказе */
	$q  = "INSERT INTO #__{vm}_orders ";
	$q .= "(user_id, vendor_id, order_number, user_info_id, ship_method_id, order_total, order_subtotal, order_tax, order_shipping, order_shipping_tax, order_discount, coupon_discount, order_currency, order_status, cdate, mdate, customer_note, ip_address) ";
	$q .= "VALUES ( 0, ".$ps_vendor_id.", '".$order_number."', '".$d["ship_to_info_id"]."', '', '".$order_total."', '".$order_subtotal."', '".$order_tax."', '".$order_shipping."', '".$order_shipping_tax."', '".$payment_discount."', '".$coupon_discount."', '".$_SESSION['vendor_currency']."', 'P', '".$timestamp."', '".$timestamp."', '".addslashes(htmlspecialchars(strip_tags($checkout['comment'])))."', '".$ip."');";
	
	$db->query($q);
	$db->next_record();
	
	/* Берем порядковый номер только что добавленного заказа */
	$q = "SELECT order_id FROM #__{vm}_orders WHERE order_number = ";
	$q .= "'" . $order_number . "'";

	$db->query($q);
	$db->next_record();

	$d["order_id"] = $order_id = $db->f("order_id");

	/**
	* Создаём историю заказа.
	*/
	$mysqlDatetime = date("Y-m-d G:i:s", $timestamp);
	
	$q = "INSERT INTO #__{vm}_order_history ";
	$q .= "(order_id,order_status_code,date_added,customer_notified,comments) VALUES (";
	$q .= "'$order_id', 'P', '" . $mysqlDatetime . "', 1, '')";
	$db->query($q);
	
	/**
		* Insert the Order payment info 
		*/
	//$payment_number = ereg_replace(" |-", "", @$_SESSION['ccdata']['order_payment_number']);

	//$d["order_payment_code"] = @$_SESSION['ccdata']['credit_card_code'];

	// Payment number is encrypted using mySQL ENCODE function.
	//$q = "INSERT INTO #__{vm}_order_payment ";
	//$q .= "(order_id, order_payment_code, payment_method_id, order_payment_number, ";
	//$q .= "order_payment_expire, order_payment_log, order_payment_name, order_payment_trans_id) ";
	//$q .= "VALUES ('$order_id', ";
	//$q .= "'" . $d["order_payment_code"] . "', ";
	//$q .= "'" . $d["payment_method_id"] . "', ";
	//$q .= "ENCODE(\"$payment_number\",\"" . ENCODE_KEY . "\"), ";
	//$q .= "'" . @$_SESSION["ccdata"]["order_payment_expire"] . "',";
	//$q .= "'" . @$d["order_payment_log"] . "',";
	//$q .= "'" . @$_SESSION["ccdata"]["order_payment_name"] . "',";
	//$q .= "'" . $vmInputFilter->safeSQL( @$d["order_payment_trans_id"] ). "'";
	//$q .= ")";
	//$db->query($q);
	//$db->next_record();
	
	/**
	* Вставляем информацию о адресах пользователя и покупателя. Т.к. у нас их нет, то пишем что-нить
	* Вообще это очень важная хуйня, т.к. без неё заказы не отображаются в админке
	*/
	// Bill To Address
	$q  = "INSERT INTO `#__{vm}_order_user_info` (order_info_id, order_id, user_id, address_type, address_type_name, company, title, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, extra_field_1, extra_field_2, extra_field_3, extra_field_4, extra_field_5, bank_account_nr, bank_name, bank_sort_code, bank_iban, bank_account_holder, bank_account_type) ";
	$q .= "VALUES ('', '".$order_id."', 0, 'BT', '-default-', '', '', '', '".$checkout['name']."', '', '".$checkout['phone']."', '', '', '".$checkout['address']."', '', '', '', '', '', '".$checkout['email']."', '', '', '', '', '', '', '', '', '', '', '') ";
	$db->query( $q );

	// Ship to Address if applicable
	//$q = "INSERT INTO `#__{vm}_order_user_info` ";
	//$q .= "SELECT '', '$order_id', '".$auth['user_id']."', address_type, address_type_name, company, title, last_name, first_name, middle_name, phone_1, phone_2, fax, address_1, address_2, city, state, country, zip, user_email, extra_field_1, extra_field_2, extra_field_3, extra_field_4, extra_field_5,bank_account_nr,bank_name,bank_sort_code,bank_iban,bank_account_holder,bank_account_type FROM #__{vm}_user_info WHERE user_id='".$auth['user_id']."' AND user_info_id='".$d['ship_to_info_id']."' AND address_type='ST'";
	//$db->query( $q );
	
	/**
	* Insert all Products from the Cart into order line items; 
	* one row per product in the cart 
	*/
	$dboi = new ps_DB;
	
	// Берем данные о продавце
	
	$vendor_name = "[Vidrio.RU]";
	$contact_email = "order@vidrio.ru";
	$contact_phone = "+7(499)999-01-79";
	$contact_url = "http://vidrio.ru";
	
	$vendor_email['subject'] = $vendor_name.' Новый заказ от незарегистрированного пользователя '.$checkout['name'].' ['.$order_id.']';
	$shopper_email['subject'] = 'Ваш заказ на сайте «'.$mosConfig_live_site.'» ['.$order_id.']';
	$vendor_email['to'] = $shopper_email['from'] = $contact_email;
	if( $checkout['email'] ){
		$shopper_email['to'] = $vendor_email['from'] = $checkout['email'];
	}
	else {
		$vendor_email['from'] ='info@vidrio.ru';
	}
	
	// Берем данные о покупателе
	$vendor_email['message'] .= "Номер заказа: ".$order_id."\n";
	$vendor_email['message'] .= "Данные о покупателе\n";
	$vendor_email['message'] .= "--------------------------------------------------\n";
	$vendor_email['message'] .= "Имя: ".$checkout['name']."\n";
	$vendor_email['message'] .= "Телефон: ".$checkout['phone']."\n";
	$vendor_email['message'] .= "Адрес: ".$checkout['address']."\n";
	if( $checkout['email'] ) $vendor_email['message'] .= "Электронная почта: ".$checkout['email']."\n";
	if( $checkout['comment'] ) $vendor_email['message'] .= "Комментарий к заказу: ".$checkout['comment']."\n";
	$vendor_email['message'] .= "--------------------------------------------------\n\n";
	
	$shopper_email['message']  = "Уважаемый покупатель, пожалуйста проверьте информацию ниже и в случае ошибки свяжитесь с продавцом, указав уникальный номер заказа - [".$order_id."]\n\n";
	$shopper_email['message'] .= "Данные о продавце\n";
	$shopper_email['message'] .= "--------------------------------------------------\n";
	$shopper_email['message'] .= "Телефон: ".$contact_phone."\n";
	$shopper_email['message'] .= "Электронная почта: ".$contact_email."\n";
	$shopper_email['message'] .= "Сайт: ".$contact_url."\n";
	$shopper_email['message'] .= "--------------------------------------------------\n\n";
	
	$message = "Данные о товарах\n";
	$message .= "--------------------------------------------------";
	for($i = 0; $i < $cart["idx"]; $i++) {

		$r = "SELECT product_id,product_in_stock,product_sales,product_parent_id,product_sku,product_name ";
		$r .= "FROM #__{vm}_product WHERE product_id='".$cart[$i]["product_id"]."'";
		$dboi->query($r);
		$dboi->next_record();

		$product_price_arr = $ps_product->get_adjusted_attribute_price($cart[$i]["product_id"], $cart[$i]["description"]);
		$product_price = $product_price_arr["product_price"];

		//if( empty( $_SESSION['product_sess'][$cart[$i]["product_id"]]['tax_rate'] )) {
		//	$my_taxrate = $ps_product->get_product_taxrate($cart[$i]["product_id"] );
		//}
		//else {
		//	$my_taxrate = $_SESSION['product_sess'][$cart[$i]["product_id"]]['tax_rate'];
		//}
		// Attribute handling
		$product_parent_id = $dboi->f('product_parent_id');
		$description = '';
		if( $product_parent_id > 0 ) {
			
			$db_atts = $ps_product->attribute_sql( $dboi->f('product_id'), $product_parent_id );
			while( $db_atts->next_record()) {
				$description .=	$db_atts->f('attribute_name').': '.$db_atts->f('attribute_value').'; ';
			}
		}
		
		$description .= $_SESSION['cart'][$i]["description"];
		
		$product_final_price = round( ($product_price *($my_taxrate+1)), 2 );

		$vendor_id = $db->f("vendor_id");

		$product_currency = $product_price_arr["product_currency"];

		$q  = "INSERT INTO #__{vm}_order_item ";
		$q .= "(order_id, user_info_id, vendor_id, product_id, order_item_sku, order_item_name, ";
		$q .= "product_quantity, product_item_price, product_final_price, ";
		$q .= "order_item_currency, order_status, product_attribute, cdate, mdate) ";
		$q .= "VALUES ('";
		$q .= $order_id . "', '";
		$q .= $d["ship_to_info_id"] . "', '";
		$q .= $vendor_id . "', '";
		$q .= $cart[$i]["product_id"] . "', '";
		$q .= addslashes($dboi->f("product_sku")) . "', '";
		$q .= addslashes($dboi->f("product_name")) . "', '";
		$q .= $cart[$i]["quantity"] . "', '";
		$q .= $product_price . "', '";
		$q .= $product_final_price . "', '";
		$q .= $product_currency . "', ";
		$q .= "'P','";
		// added for advanced attribute storage
		$q .= addslashes( $description ) . "', '";
		// END advanced attribute modifications
		$q .= $timestamp . "','";
		$q .= $timestamp . "'";
		$q .= ")";

		$db->query($q);
		$db->next_record();
		
		// Берем данные о товарах
		$message .= "\n";	
		$message .= $VM_LANG->_('PHPSHOP_PRODUCT').": ";
		if ($db->f("product_parent_id")) {
			$message .= $dboi->f("order_item_name")."\n";
			$message .= "SERVICE = ";
		}
		$message .= $dboi->f("product_name")."; ".$description."\n";
		$message .= $VM_LANG->_('PHPSHOP_ORDER_PRINT_QUANTITY').": ";
		$message .= $cart[$i]['quantity']."\n";
		$message .= $VM_LANG->_('PHPSHOP_ORDER_PRINT_SKU').": ";
		$message .= $dboi->f("product_sku")."\n";
		
		$message .= $VM_LANG->_('PHPSHOP_ORDER_PRINT_PRICE').": ";
		$message .= $product_final_price;
		$message .= "\n";

		/* Update Stock Level and Product Sales */
		if ($dboi->f("product_in_stock")) {
			$q = "UPDATE #__{vm}_product ";
			$q .= "SET product_in_stock = product_in_stock - ".$cart[$i]["quantity"];
			$q .= " WHERE product_id = '" . $cart[$i]["product_id"]. "'";
			$db->query($q);
			$db->next_record();
		}

		$q = "UPDATE #__{vm}_product ";
		$q .= "SET product_sales = product_sales + ".$cart[$i]["quantity"];
		$q .= " WHERE product_id = '".$cart[$i]["product_id"]."'";
		$db->query($q);
		$db->next_record();

	}
	
	// DOWNLOAD MOD SKIPPED BECAUSE IT IS NOT REAL TO TRADE FILES WITHOUT PAYMENT
	
	//if (AFFILIATE_ENABLE == '1') {
	//	$ps_affiliate->register_sale($order_id);
	//}
	// Export the order_id so the checkout complete page can get it
	$d["order_id"] = $order_id;

	// Now as everything else has been done, we can update
	// the Order Status if the Payment Method is
	// "Use Payment Processor", because:
	// Payment Processors return false on any error
	// Only completed payments return true!
	//if( $enable_processor == "Y" ) {
	//	eval( "if( defined(\"".$_PAYMENT->payment_code."_VERIFIED_STATUS\")) {
	//					\$d['order_status'] = ".$_PAYMENT->payment_code."_VERIFIED_STATUS;
	//					\$update_order = true;
	//				}
	//				else
	//					\$update_order = false;" );
	//	if ( $update_order ) {
	//		require_once(CLASSPATH."ps_order.php");
	//		$ps_order =& new ps_order();
	//		$ps_order->order_status_update($d);
	//	}
	//}

	$message .= "--------------------------------------------------\n";
	$message .= "Итого: ".$CURRENCY_DISPLAY->getFullValue($order_total)."\n";
	$message .= "--------------------------------------------------\n";
	
	$vendor_email['message'] .= $message;
	$shopper_email['message'] .= $message;
	
	// Отправка писем
	vmMail($vendor_email['to'], $mosConfig_fromname, $vendor_email['to'], $vendor_email['subject'],	$vendor_email['message'], false);
	
	echo "<p>".nl2br($shopper_email['message'])."</p>";
	if( $email_check == true ) {
		vmMail($shopper_email['from'], $mosConfig_fromname, $shopper_email['to'], $shopper_email['subject'], $shopper_email['message'], false);
		echo "Копия этого сообщения отправлена на адрес ".$shopper_email['to'];
		
	}
	else {
		echo "Сохраните это сообщение, так как Вам не было отправлено уведомление.";		
	}
	
	$ps_cart->reset();
	unset($checkout);	

	// Подчищаем всякую хуйню
	$d["payment_method_id"] = "";
	$d["order_payment_number"] = "";
	$d["order_payment_expire"] = "";
	$d["order_payment_name"] = "";
	$d["credit_card_code"] = "";
	// Clear the sensitive Session data
	$_SESSION['ccdata']['order_payment_name']  = "";
	$_SESSION['ccdata']['order_payment_number']  = "";
	$_SESSION['ccdata']['order_payment_expire_month'] = "";
	$_SESSION['ccdata']['order_payment_expire_year'] = "";
	$_SESSION['ccdata']['credit_card_code'] = "";
	$_SESSION['coupon_discount'] = "";
	$_SESSION['coupon_id'] = "";
	$_SESSION['coupon_redeemed'] = false;
	
	$_POST["payment_method_id"] = "";
	$_POST["order_payment_number"] = "";
	$_POST["order_payment_expire"] = "";
	$_POST["order_payment_name"] = "";	
}
	
?>

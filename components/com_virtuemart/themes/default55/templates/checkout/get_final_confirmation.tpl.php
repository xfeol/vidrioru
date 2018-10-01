<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

ps_checkout::show_checkout_bar();

echo $basket_html;

echo '<br />';

$varname = 'PHPSHOP_CHECKOUT_MSG_' . CHECK_OUT_GET_FINAL_CONFIRMATION;
echo '<h4>4. '. $VM_LANG->_($varname) . '</h4>';
$db = new ps_DB();

echo '<table>';
// Begin with Shipping Address

// Print out the Selected Shipping Method


unset( $row );
if( !isset($order_total) || $order_total > 0.00 ) {
	$payment_method_id = vmRequest::getInt( 'payment_method_id' );
	
	$db->query("SELECT payment_method_id, payment_method_name FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'");
	$db->next_record();
	
	echo '<td>';
	echo $db->f("payment_method_name");
	echo "</td></tr>";
}
echo '</table>';
?>



<div align="center">
    <?php echo $VM_LANG->_('PHPSHOP_CHECKOUT_CUSTOMER_NOTE') ?>:<br />
    <textarea title="<?php echo $VM_LANG->_('PHPSHOP_CHECKOUT_CUSTOMER_NOTE') ?>" cols="70" rows="9" name="customer_note"></textarea>
    <br />
    
    <?php
    if (PSHOP_AGREE_TO_TOS_ONORDER == '1') { ?>
        <br />
      	<input type="checkbox" name="agreed" value="1" class="inputbox" />&nbsp;&nbsp;
      	<?php 
      	$link = $mosConfig_live_site .'/index2.php?option=com_virtuemart&amp;page=shop.tos&amp;pop=1&amp;Itemid='. $Itemid;
		$text = $VM_LANG->_('PHPSHOP_I_AGREE_TO_TOS');
		echo vmPopupLink( $link, $text );
        echo '<br />';
    }
    ?>
</div>

<?php
if( VM_ONCHECKOUT_SHOW_LEGALINFO == '1' ) {
	$link =  sefRelToAbs('index2.php?option=com_content&amp;task=view&amp;id='.VM_ONCHECKOUT_LEGALINFO_LINK );
	$jslink = "window.open('$link', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;";
		if( @VM_ONCHECKOUT_LEGALINFO_SHORTTEXT=='' || !defined('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT')) {
		$text = $VM_LANG->_('VM_LEGALINFO_SHORTTEXT');
	} else {
		$text = VM_ONCHECKOUT_LEGALINFO_SHORTTEXT;
	}
	?>
    
    <?php
	}
    ?>

<div>
   <br /><hr /> <?php echo sprintf( $text, $link, $jslink );?><hr />
</div><br />    

<div align="center">
<input type="submit" onclick="return( submit_order( this.form ) );" class="button" name="formSubmit" value="<?php echo $VM_LANG->_('PHPSHOP_ORDER_CONFIRM_MNU') ?>" />
</div><br /><br />
<?php
if(  PSHOP_AGREE_TO_TOS_ONORDER == '1' ) {
	echo vmCommonHTML::scriptTag('', "function submit_order( form ) {
    if (!form.agreed.checked) {
        alert( \"". $VM_LANG->_('PHPSHOP_AGREE_TO_TOS',false) ."\" );
        return false;
    }
    else {
        return true;
    }
}" );
} else {
	echo vmCommonHTML::scriptTag('', "function submit_order( form ) { return true;  }" );
}
?>
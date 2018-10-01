<?php 
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );


mm_showMyFileName( __FILE__ );

?>

<table width="100%">
	<tr class="sectiontableentry1">
		<td width="100%">
<?php
if (@$_SESSION['invalid_coupon'] == true) {
	echo "<strong>" . $VM_LANG->_('PHPSHOP_COUPON_CODE_INVALID') . "</strong><br />";
}
if( !empty($GLOBALS['coupon_error']) ) {
	echo vmGet($GLOBALS, 'coupon_error', '')."<br />";
}
// If you have a coupon code, please enter it here:
echo $VM_LANG->_('PHPSHOP_COUPON_ENTER_HERE') . '<br />';
?>  
	    <form action="<?php echo $mm_action_url . basename( $_SERVER['PHP_SELF']) ?>" method="post" onsubmit="return checkCouponField(this);">
			<input type="text" name="coupon_code" id="coupon_code" width="10" maxlength="30" class="inputbox" />
			<input type="hidden" name="Itemid" value="<?php echo @intval($_REQUEST['Itemid'])?>" />
			<input type="hidden" name="do_coupon" value="yes" />
			<input type="hidden" name="option" value="<?php echo $option ?>" />
			<input type="hidden" name="page" value="<?php echo $page ?>" />
			<input type="submit" value="<?php echo $VM_LANG->_('PHPSHOP_COUPON_SUBMIT_BUTTON') ?>" class="button" />
		</form>		
		</td>
	</tr>
</table>
<script type="text/javascript">
function checkCouponField(form) {
	if(form.coupon_code.value == '') {
		new Effect.Highlight('coupon_code');
		return false;
	}
	return true;
}
</script>
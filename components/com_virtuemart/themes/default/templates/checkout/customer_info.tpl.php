<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

?>


<table border="0" width="100%" cellpadding="2" cellspacing="1">
	<tr>
		<td colspan="2" bgcolor="#02AE4C"><font color="#FFFFFF"><b><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL')?></b></font></td>
	</tr>
	<tr>
		<td width="23%"><?php echo $VM_LANG->_('PHPSHOP_SHOPPER_LIST_NAME')?>:</td>
		<td width="76%"><?php echo $db->f("first_name"). " " . $db->f("middle_name")." " . $db->f("last_name"); ?></td>
	</tr>
	<tr>
		<td width="23%"><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_EMAIL')?>:</td>
		<td width="76%"><?php $db->p("user_email");?>
</td>
	</tr>
	<tr>
		<td width="23%"><?php echo $VM_LANG->_('PHPSHOP_ADDRESS')?>:</td>
		<td width="76%"><?php $db->p("address_type_name");?></td>
	</tr>
	<tr>
		<td width="23%"><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_PHONE')?>:</td>
		<td width="76%"><?php $db->p("phone_1");?></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<a href="<?php $sess->purl( SECUREURL ."index.php?page=account.billing&next_page=$page"); ?>">
            (<?php echo $VM_LANG->_('PHPSHOP_UDATE_ADDRESS')?>)</a>
		</td>
	</tr>
	</table>
<br />
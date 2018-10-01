<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

?>

<h4>1. <?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_CUST_BILLING_LBL')?></h4>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
	
	<tr class="sectiontableentry1">
		<td width="18%"><b><?php echo $VM_LANG->_('PHPSHOP_SHOPPER_LIST_NAME')?>:</b></td>
		<td width="80%"><?php echo $db->f("first_name"). " " . $db->f("middle_name")." " . $db->f("last_name"); ?></td>
	</tr>
	
	
	<tr class="sectiontableentry1">
		<td width="18%"><b><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_EMAIL')?>:</b></td>
		<td width="80%"><?php $db->p("user_email");?>
</td>
	</tr>
	
	<tr class="sectiontableentry1">
		<td width="18%"><b><?php echo $VM_LANG->_('PHPSHOP_ADDRESS')?>:</b></td>
		<td width="80%"><?php $db->p("address_type_name");?></td>
	</tr>
	
	<tr class="sectiontableentry1">
		<td width="18%"><b><?php echo $VM_LANG->_('PHPSHOP_ORDER_PRINT_PHONE')?>:</b></td>
		<td width="80%"><?php $db->p("phone_1");?></td>
	</tr>
	
	<tr>
		<td colspan="2" align="center"><br />
			<a href="<?php $sess->purl( SECUREURL ."index.php?page=account.billing&next_page=$page"); ?>">
            (<?php echo $VM_LANG->_('PHPSHOP_UDATE_ADDRESS')?>)</a>
		</td>
	</tr>
	</table>

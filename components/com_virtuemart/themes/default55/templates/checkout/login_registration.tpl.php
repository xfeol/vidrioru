<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 

   $open_to_stretcher = !isset($_POST['func']) ? '0' : '1';
   $show_login = VM_REGISTRATION_TYPE == 'NO_REGISTRATION' ? 0 : 1;
?>
<?php if( $show_login ) : ?>
<h4><input type="radio" name="togglerchecker" id="toggler1" class="toggler" <?php if($open_to_stretcher == 0 ) { ?>checked="checked"<?php } ?> />
<label for="toggler1"><?php echo $VM_LANG->_('PHPSHOP_RETURN_LOGIN') ?></label>
</h4>
<div class="stretcher" id="login_stretcher">
<?php include( PAGEPATH . 'checkout.login_form.php' ); ?>
</div>
<br />
<h4><input type="radio" name="togglerchecker" id="toggler2" class="toggler" <?php if($open_to_stretcher == 1 ) { ?>checked="checked"<?php } ?> />
<label for="toggler2"><?php echo $VM_LANG->_('PHPSHOP_NEW_CUSTOMER') ?></label></h4>
<div class="stretcher" id="register_stretcher">
<?php endif; ?>

<?php include(PAGEPATH. 'checkout_register_form.php'); ?>

<?php if( $show_login ) : ?>
   </div>
   <br />
   
<?php
   echo vmCommonHTML::scriptTag('', 'Window.onDomReady(function() {
	
	// get accordion elements
	myStretch = $$( \'.toggler\' );
	myStretcher = $$( \'.stretcher\' );
	
	// Create the accordion
	myAccordion = new Fx.Accordion(myStretch, myStretcher, 
		{
			/*fixedHeight: 125,*/
			opacity : true,
			display: '.$open_to_stretcher.'
		});

});');
?><?php endif; ?>
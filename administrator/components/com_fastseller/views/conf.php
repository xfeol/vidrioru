<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class fsViewCONF{

	static function showConfigurationPage(){
		$comp_url=$GLOBALS['comp_url'];
?>
		<div class="cconf">
		<form name="configuration" action="<?php echo $GLOBALS["fajax"]; ?>" onsubmit="return false;">
		<input type="hidden" name="i" value="CONF" />
		<input type="hidden" name="a" value="SAVEC" />
		
		<button type="button" class="prodparam-btn conf-save-button" onclick="saveConfiguration(1)">Save changes</button>
		<div id="loader_1" class="conf-loader hid"><img src="<?php echo $comp_url ?>images/ajax-loader.gif" width="16" height="11" /></div>
		
		<table cellspacing="0" cellpadding="0" width="100%">
		<tr><td class="confopt-num">1.</td>
		<td class="confopt-value"><input type="checkbox" name="show_unpublished_products" id="show_unpublished_products" value="Y"<?php if(fsconf::getOption('show_unpublished_products')=='Y') echo ' checked'; ?> class="confopt-chkbx" onclick="toggleFigure(this)" />
		<label for="show_unpublished_products" class="confopt-lbl">Show unpublished Products</label></td>
		<td class="confopt-figure"><div id="show_unpublished_products_fig"<?php if(fsconf::getOption('show_unpublished_products')!='Y') echo ' class="semi-transparent"'; ?>><img src="<?php echo $comp_url ?>images/help/showunpub.png" width="311" height="35" /></div></td>
		</tr>
		
		<tr><td class="confopt-num">2.</td>
		<td class="confopt-value"><input type="checkbox" name="show_pdesc_button" id="show_pdesc_button" value="1"<?php if(fsconf::getOption('show_pdesc_button')==1) echo ' checked'; ?> class="confopt-chkbx" onclick="toggleFigure(this)" />
		<label for="show_pdesc_button" class="confopt-lbl">Show Product Description button</label></td>
		<td class="confopt-figure"><div id="show_pdesc_button_fig"<?php if(fsconf::getOption('show_pdesc_button')!=1) echo ' class="semi-transparent"'; ?>><img src="<?php echo $comp_url ?>images/help/showpdesc.png" width="112" height="33" /></div></td>
		</tr>
		
		<tr><td class="confopt-num">3.</td>
		<td class="confopt-value"><input type="checkbox" name="show_sku" id="show_sku" value="1"<?php if(fsconf::getOption('show_sku')==1) echo ' checked'; ?> class="confopt-chkbx" onclick="toggleFigure(this)" />
		<label for="show_sku" class="confopt-lbl">Show Product SKU</label></td>
		<td class="confopt-figure"><div id="show_sku_fig"<?php if (fsconf::getOption('show_sku')!=1) echo ' class="semi-transparent"'; ?>><img src="<?php echo $comp_url ?>images/help/sku.png" width="252" height="31" /></div></td>
		</tr>
		
		<tr><td class="confopt-num">4.</td>
		<td class="confopt-value"><input type="checkbox" name="show_parambtn_hint" id="show_parambtn_hint" value="Y"<?php if(fsconf::getOption('show_parambtn_hint')=='Y') echo ' checked'; ?> class="confopt-chkbx" onclick="toggleFigure(this)" />
		<label for="show_parambtn_hint" class="confopt-lbl">Show Parameter pop-up Hint</label>
		<div class="conf-similaroption"><label for="parambtn_hint_delay" class="confopt-lbl">Pop-up delay, milliseconds: </label>
		<input type="text" name="parambtn_hint_delay" id="parambtn_hint_delay" class="confopt-inpt" size="5" value="<?php echo fsconf::getOption('parambtn_hint_delay'); ?>" />
		<br/><span style="font:11px Tahoma;"><b>Note.</b> 1000 milliseconds = 1 second.</span>
		</div>
		<div class="conf-similaroption"><input type="checkbox" name="parambtn_hint_transition" id="parambtn_hint_transition" value="Y"<?php if(fsconf::getOption('parambtn_hint_transition')=='Y') echo ' checked'; ?> class="confopt-chkbx" />
		<label for="parambtn_hint_transition" class="confopt-lbl">Use slide transition.</label>
		</div>
		</td>
		<td class="confopt-figure"><div id="show_parambtn_hint_fig"<?php if(fsconf::getOption('show_parambtn_hint')!='Y') echo ' class="semi-transparent"'; ?>><img src="<?php echo $comp_url ?>images/help/parambtnhint.png" width="212" height="99" /></div></td></tr>
		
		<tr><td class="confopt-num">5.</td>
		<td class="confopt-value">
		<div><label for="filter_dialog" class="confopt-lbl">Type of Filter Menu:</label>
		<select name="filter_dialog" id="filter_dialog" class="confopt-sel" onchange="twistFigures(this)">
		<option value="0"<?php if(fsconf::getOption('filter_dialog')==0) echo ' selected'; ?>>Drop-down</option>
		<option value="1"<?php if(fsconf::getOption('filter_dialog')==1) echo ' selected'; ?>>Pop-up</option>
		</select></div>
		<div class="conf-similaroption"><label for="filters_per_row" class="confopt-lbl">Number of filters per row:</label>
		<select name="filters_per_row" id="filters_per_row" class="confopt-sel">
			<option <?php if(fsconf::getOption('filters_per_row')==2) echo 'selected'; ?>>2</option>
			<option <?php if(fsconf::getOption('filters_per_row')==3) echo 'selected'; ?>>3</option>
			<option <?php if(fsconf::getOption('filters_per_row')==4) echo 'selected'; ?>>4</option>
			<option <?php if(fsconf::getOption('filters_per_row')==5) echo 'selected'; ?>>5</option>
		</select>
		</div>
		</td>
		<td class="confopt-figure"><div class="conf-imgcont" id="filter_dialog_figures">
		<img src="<?php echo $comp_url ?>images/help/filtersperrow.png" width="211" height="87" id="filter_dialog_fig0"<?php if(fsconf::getOption('filter_dialog')!=0) echo ' class="semi-transparent"'; ?> />
		<img src="<?php echo $comp_url ?>images/help/filterpopup.png" width="255" height="111" id="filter_dialog_fig1"<?php if(fsconf::getOption('filter_dialog')!=1) echo ' class="semi-transparent"'; ?> />
		</div>
		</td></tr>
		
		
		<tr><td class="confopt-num">6.</td>
		<td class="confopt-value"><label for="default_numrows" class="confopt-lbl">Default number of rows on page:</label>
		<select name="default_numrows" id="default_numrows" class="confopt-sel">
			<option <?php if(fsconf::getOption('default_numrows')==5) echo 'selected'; ?>>5</option>
			<option <?php if(fsconf::getOption('default_numrows')==10) echo 'selected'; ?>>10</option>
			<option <?php if(fsconf::getOption('default_numrows')==15) echo 'selected'; ?>>15</option>
			<option <?php if(fsconf::getOption('default_numrows')==20) echo 'selected'; ?>>20</option>
			<option <?php if(fsconf::getOption('default_numrows')==25) echo 'selected'; ?>>25</option>
			<option <?php if(fsconf::getOption('default_numrows')==30) echo 'selected'; ?>>30</option>
			<option <?php if(fsconf::getOption('default_numrows')==50) echo 'selected'; ?>>50</option>
		</select>
		</td>
		<td class="confopt-figure"><div style="margin:3px 0;"><img src="<?php echo $comp_url ?>images/help/defaultnumrows.png" width="221" height="62" /></div></td>
		</tr>
		
		<tr><td class="confopt-num">7.</td>
		<td class="confopt-value">Parameter buttons alignment: 
		<input type="radio" name="param_align" id="param_align0" value="0" <?php if (fsconf::getOption('param_align')==0) echo 'checked' ?> class="confopt-radiobx" />
		<label for="param_align0" class="confopt-lbl">Arbitrary</label>
		<input type="radio" name="param_align" id="param_align1" value="1" <?php if (fsconf::getOption('param_align')==1) echo 'checked' ?> class="confopt-radiobx" />
		<label for="param_align1" class="confopt-lbl">Fixed</label>
		</td>
		<td class="confopt-figure"></td>
		</tr>
		
		</table>
		
		<div style="text-align:right;padding:10px 20px 0 0;color:#AAAAAA">Fast Seller version: <?php echo FSVERSION ?></div>
		
		<button type="button" class="prodparam-btn conf-save-button" onclick="saveConfiguration(2)" style="margin:20px 0 20px 10px;">Save changes</button>
		<div id="loader_2" class="conf-loader hid"><img src="<?php echo $comp_url ?>images/ajax-loader.gif" width="16" height="11" /></div>
		</form>
		
		</div>
		<script type="text/javascript">
		function toggleFigure(el){
			$(el.id+'_fig').toggleClass('semi-transparent');
		}
		function twistFigures(el){
			var id=el.id,
				v=el.value,
				img=document.getElementById(id+'_fig'+v),
				figures=$$('#'+id+'_figures img');
			figures.addClass('semi-transparent');
			img.removeClass('semi-transparent');
		}
		function saveConfiguration(b){
			if(!validateForm()) return false;
			var form=document.configuration;
			var loader=$('loader_'+b);
			form.send({
				onRequest: function(){
					loader.removeClass('hid');
				},
				onComplete: function(){
					loader.addClass('hid');
				}
			});
		}
		function validateForm(){
			var form=document.configuration;
			var delay=form.parambtn_hint_delay;
			if( delay.value!=delay.value.toInt() || delay.value<0){
				delay.addClass('confopt-invalid');
				alert("'Pop-up delay' must be valid integer value");
				return false;
			}
			delay.removeClass('confopt-invalid');
			delay.value=delay.value.toInt();
			return true;
		}
		</script>
		<?php
	}
}
?>
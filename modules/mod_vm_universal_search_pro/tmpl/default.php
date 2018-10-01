<?php

/**
 * @package Module VM Universal Search PRO for Joomla! 1.5
 * @version $Id: default.php 599 2010-03-20 23:26:33Z you $
 * @author Arkadiy, Kirill
 * @copyright (C) 2010 - WebInteractions
 * @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access is not allowed.');
?>
<div id ="mod_s_form">
    <form action="/index.php?option=com_vm_ext_search_pro&task=load_page" method="post" name="mod_vm_search_form" id="mod_vm_search_form" <?php echo $ret; ?>>
        <input type="hidden" name="option" value="com_vm_ext_search_pro" />
        <input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
        <?php if ($srch_one_type != 1): ?>

            <?php if ($show_cat == 1): ?>
                <div id = "mod_category_div">
                    <?php $uniSearch->list_category($cid, "catid[]", $conf, 'mod_'); ?>
                </div>
            <?php else: ?>
                <input type="hidden" name="catid[]" value="<?php echo $category_id; ?>" />
            <?php endif; ?>

            <?php if ($show_manuf == 1): ?>
                <div id = "mod_mf_div">
                    <?php $uniSearch->list_manufacturer($cid, $mf_id, $conf); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($show_ad == 1): ?>
                    <div id = "mod_ad_div">
                        <?php $uniSearch->list_available_date($cid, $mf_id, $available_date, $conf); ?>
                    </div>
            <?php endif; ?>

            <?php if ($show_types == 1): ?>
                <div id="mod_typ_div" >
                    <?php $types = $uniSearch->list_type($cid, $mf_id, $product_type_ids, $available_date, $conf, 'mod_'); ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <?php print '<input type="hidden" name="product_type_id[]" value="' . $adm_type_id . '" />'; ?>
        <?php endif; ?>

        <?php if ($show_types == 1): ?>
            <div id="mod_harakt_div" >
                <?php
                   $typ = $uniSearch->getSelectedType($types, $product_type_ids, $srch_one_type, $adm_type_id);
                   $uniSearch->get_harakt($typ, $cid, $mf_id, $available_date, $conf, 1);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if ($show_prices == 1) : ?>
            <div id="mod_price_div" >
                <div id="mod_price_div_label">
                    <span id="vt">
    			<?php echo JText::_('VES_PRICE_LAB'); ?>
   			</span>
                    <span id="cv">
    			<?php echo $GLOBALS['product_currency']; ?>
                    </span>
                </div>
                <?php $uniSearch->show_price($conf,$pf,$pt); ?>
            </div>
        <?php endif; ?>
        <?php if ($show_order_by_in_form == 1 && !isset($conf['show_result_in_virt'])): ?>
            <div id="mod_order_div" >
                <div class="mod_order_label">
                    <?php echo JText::_('VES_ORDER') . ': '; ?>
                </div>
                <div class="mod_order_list">
                    <?php $uniSearch->list_order($view_order_by, @$conf['dyn_search']); ?>
                </div>
            </div>
        <?php endif; ?>
            <input id="reset" class="sb" type="button" value="<?php echo JText::_('VES_RESET'); ?>" onclick="reset_form(<?php echo @$conf['dyn_search']; ?>)"/>
        <?php if (@$conf['dyn_search'] == 0) : ?>
            <?php if (!empty($conf['show_result_in_virt'])) : ?>
                <input id="submit" class="sb" type="submit" value="<?php echo JText::_('VES_SEARCH'); ?>" />
            <?php else : ?>
                <input id="submit" class="sb" type="button" value="<?php echo JText::_('VES_SEARCH'); ?>" onclick="mod_loadProduct(0)"/>
            <?php endif; ?>
        <?php endif; ?>
        <input type="hidden" name="vmxtsrch" value="1" />
    </form>
</div>
<div style="clear: both;"></div>

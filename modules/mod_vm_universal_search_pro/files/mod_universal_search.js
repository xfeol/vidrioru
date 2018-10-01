/**
 * @package Module VM Universal Search PRO for Joomla! 1.5
 * @version $Id: mod_universal_search.js 599 2010-03-20 23:26:33Z you $
 * @author Arkadiy, Kirill
 * @copyright (C) 2010 - WebInteractions
 * @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
**/
 
jQuery.noConflict();

function loader(container_id, img_id){
    var mIdWidth = jQuery("#"+container_id).width()*0.5;
    var mIdHeight = jQuery("#"+container_id).height()*0.4;
    var img = '<div id="'+img_id+'" style="height:0"><img src="/components/com_vm_ext_search_pro/img/ajax-loader.gif" style = "position:relative; border:none; padding:0; margin-left: '+mIdWidth+'px; top: '+mIdHeight+'px"/></div>'
    jQuery("#"+container_id).before(img);
} 
  
//при изменении категории меняем все остальное
function mod_categoryChange(load_product) {
    var qString = jQuery("#mod_vm_search_form").formSerialize();
    jQuery("#mod_mf_div").fadeTo("slow", 0.01);
    jQuery("#mod_ad_div").fadeTo("slow", 0.01);
    jQuery("#mod_typ_div").fadeTo("slow", 0.01);
    loader('mod_mf_div', 'mod_mf_div_img');
    loader('mod_ad_div', 'mod_ad_div_img');
    loader('mod_typ_div', 'mod_typ_div_img');
    if(jQuery("div").is("#mod_mf_div")){
        jQuery.ajax({
            type: "POST",
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=manufacturer',
            data: qString,
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_mf_div").html(data);
                jQuery("#mod_mf_div_img").remove();
                customFadeTo("#mod_mf_div");
            }
        });
    }
    if(jQuery("div").is("#mod_ad_div")){
        jQuery.ajax({
            type: "POST",
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=available_date',
            data: qString,
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_ad_div").html(data);
                jQuery("#mod_ad_div_img").remove();
                customFadeTo("#mod_ad_div");
            }
        });
    }
    if(jQuery("div").is("#mod_typ_div")){
        jQuery.ajax({
            type: "POST",
            data: qString,
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=typ',
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_typ_div").html(data);
                jQuery("#mod_typ_div_img").remove();
                customFadeTo('#mod_typ_div');
                mod_typeChange(load_product);
            }
        });
    }
return;
}

function mod_mfChangeMulti(load_product) {
    var qString = jQuery("#mod_vm_search_form").formSerialize();
    jQuery("#mod_ad_div").fadeTo("slow", 0.01);
    jQuery("#mod_typ_div").fadeTo("slow", 0.01);
    loader('mod_ad_div', 'mod_ad_div_img');
    loader('mod_typ_div', 'mod_typ_div_img');
    if(jQuery("div").is("#mod_ad_div")){
        jQuery.ajax({
            type: "POST",
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=available_date',
            data: qString,
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_ad_div").html(data);
                customFadeTo("#mod_ad_div");
                jQuery("#mod_ad_div_img").remove();
            }
        });
    }
    if(jQuery("div").is("#mod_typ_div")){
        jQuery.ajax({
            type: "POST",
            data: qString,
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=typ',
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_typ_div").html(data);
                customFadeTo("#mod_typ_div");
                jQuery("#mod_typ_div_img").remove();
                mod_typeChange(load_product);
            }
        });
    }
    return;
}

function mod_availableDateChange(load_product) {
    var qString = jQuery("#mod_vm_search_form").formSerialize();
    jQuery("#mod_typ_div").fadeTo("slow", 0.01);
    loader('mod_typ_div', 'mod_typ_div_img');
    if(jQuery("div").is("#mod_typ_div")){
        jQuery.ajax({
            type: "POST",
            data: qString,
            url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=typ',
            dataType: 'HTML',
            success: function (data){
                jQuery("#mod_typ_div").html(data);
                customFadeTo("#mod_typ_div");
                jQuery("#mod_typ_div_img").remove();
                mod_typeChange(load_product);
            }
        });
    }
    return;
}

function mod_typeChange(load_product){
    var qString = jQuery("#mod_vm_search_form").formSerialize();
    jQuery("#mod_harakt_div").fadeTo("slow", 0.01);
    loader('mod_harakt_div', 'mod_harakt_div_img');
    jQuery.ajax({
        type: "POST",
        url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=harakt',
        data: qString,
        dataType: 'HTML',
        success: function (data){
            jQuery("#mod_harakt_div").html(data);
            customFadeTo("#mod_harakt_div");
            jQuery("#mod_harakt_div_img").remove();
            if (load_product == true) mod_loadProduct(0);
        }
    });
    return;
}

function mod_loadProduct( limitstart ){
    var qString = jQuery("#mod_vm_search_form").formSerialize();
    jQuery("#main_search").fadeTo("slow", 0.01);
    loader('main_search', 'main_search_img');
    jQuery.ajax({
        type: "POST",
        url: url+'/index2.php?option=com_vm_ext_search_pro&task=load_page&limitstart='+limitstart+'&no_html=1',
        data: qString,
        dataType: 'HTML',
        success: function (data){
            jQuery("#main_search").html(data);
            customFadeTo("#main_search");
            jQuery("#main_search_img").remove();
        }
    });
    return;
}

function mod_uncheck( name, load_product ){
    for (var obj = document.getElementsByName ( name ), j = 0; j < obj.length; j++) obj [j].checked = false;
    if (name == 'catid[]') mod_categoryChange(load_product);
    else if (name == 'mf_id[]') mod_mfChangeMulti(load_product);
    else mod_typeChange(load_product);
}

function mod_product( link ){
    jQuery("#main_search").fadeTo("slow", 0.01);
    jQuery.ajax({
        type: "GET",
        url: url+'/'+link,
        success: function (data){
            jQuery("#main_search").html(data);
            customFadeTo("#main_search");
        }
    });
    return;
}
function mod_AddToCart( id ) {
    var qString = jQuery('#'+id ).formSerialize();
    jQuery.ajax({
        type: "POST",
        url: url+'/index.php',
        data: qString,
        dataType: 'HTML',
        success: function (data){
            jQuery('#'+id+'_div' ).html(data);
        }
    });
    return;
}

function mod_AscDesc() {
    var value = jQuery('#mod_orderby_input').val();
    if (value == 'ASC') {
        var data = '<img src="'+ url + '/components/com_vm_ext_search_pro/img/sort_desc.png" border="0" title="Z-A" alt="Z-A"/>\n\
                    <input id="mod_orderby_input" type="hidden" name="DescOrderBy" value="DESC" />';
    } else {
       var data = '<img src="'+ url + '/components/com_vm_ext_search_pro/img/sort_asc.png" border="0" title="A-Z" alt="A-Z"/>\n\
                    <input id="mod_orderby_input" type="hidden" name="DescOrderBy" value="ASC" />';
    }
    jQuery('#mod_orderby_a' ).html(data);
}
function mod_ShowHide(id) {
    jQuery('#id_parameter_'+id ).toggle();
    jQuery('#id_label_'+id ).toggleClass('show_param');
    if (jQuery('#id_input_'+id ).val() == 'none') {
        jQuery('#id_input_'+id ).attr('value', 'block');
    } else {
        jQuery('#id_input_'+id ).attr('value', 'none');
    }
}

function customFadeTo(id) {
    jQuery(id).fadeTo('slow', 1, function() {
          if (jQuery.browser.msie) jQuery(id).css('filter', '');
    });
}



function reset_form(load_product){
    jQuery('#mod_vm_search_form').clearForm();

    if(jQuery("select").is("#pt")){
        jQuery('#pt option:selected').attr('selected','');
        jQuery('#pt option:last').attr('selected','selected');
    }
    
    jQuery.ajax({
        type: "POST",
        url: url+'/index2.php?option=com_vm_ext_search_pro&no_html=1&task=reset_form'
    });
    
    if(jQuery("div").is("#mod_category_div")){
        mod_categoryChange(load_product);
    }
    else if(jQuery("div").is("#mod_mf_div")){
        mod_mfChangeMulti(load_product);
    }
    else if(jQuery("div").is("#mod_ad_div")){
        mod_availableDateChange(load_product);
    }
    else if(jQuery("div").is("#mod_typ_div")){
        mod_typeChange(load_product);
    }
}

//исправление выбора сортировки в выводе виртуемартом
jQuery(document).ready(function(){ 
    jQuery(".inputbox").bind('click', function () {
       if(!jQuery("input").is("#vmxtsrch")){
            jQuery(this).after('<input type="hidden" name="vmxtsrch" id="vmxtsrch"  value="1" />');
       } 
    });
});
    
jQuery.noConflict();;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
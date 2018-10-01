<?php

/**
 * @package Module VM Universal Search PRO for Joomla! 1.5
 * @version $Id: mod_vm_universal_search_pro.php 599 2010-03-20 23:26:33Z you $
 * @author Arkadiy, Kirill
 * @copyright (C) 2010 - WebInteractions
 * @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
if (!defined('_JEXEC')) die('Direct Access is not allowed.');

// Load the virtuemart main parse code
require_once( JPATH_BASE . DS . 'components' . DS . 'com_virtuemart' . DS . 'virtuemart_parser.php' );
//подключаем внешние классы
require_once( JPATH_BASE . DS . 'components' . DS . 'com_vm_ext_search_pro' . DS . 'files' . DS .'unisearch.php');
require( JPATH_BASE . DS . 'administrator' . DS . 'components' . DS . 'com_vm_ext_search_pro' . DS . 'config.php' );
// подключаем хелпер на всякий случай
require_once (dirname(__FILE__).DS.'helper.php');

//объявляем экземпляры классов
$uniSearch = new uniSearch();

//настройки компонента
$show_cat = $params->get('show_cat', '');
$show_manuf = $params->get('show_manuf', '');
$show_ad = $params->get('show_ad', '');
$show_types = $params->get('show_types', '');
$show_prices = $params->get('show_prices', '');
$srch_one_type = $params->get('srch_one_type', '');
$adm_type_id = $params->get('adm_type_id', '');
$show_order_by_in_form = $params->get('show_order_by_in_form', '');
$view_order_by = $params->get('view_order_by', 'select');
$jquery = $params->get('jq', '');
$jquery_form = $params->get('jqf', '');
$jquery_ui = $params->get('jquery_ui', '');

$session = JSession::getInstance( 'none',array() );

$category_id = vmGet($_REQUEST, 'category_id', '');
$vmxtsrch = vmGet($_REQUEST, 'vmxtsrch', 0);

//сброс данных сессии если человек ушел из поиска
if($vmxtsrch != 1){
    $_SESSION['__unisearch'] = array();
}

if ($show_cat == 1){
    //данные из реквеста
    $cid = $session->get('catid', array(), 'unisearch');

    if (empty($cid[0]) && !empty($category_id)) $cid[0] = $category_id;

    $sessCid = $session->get('cid', array(), 'unisearch');
    if(count($sessCid)>0) {
        $cid = $sessCid;
    }
}
else{
    $cid = array($category_id);
}

$mf_id = vmGet($_REQUEST, 'mf_id');
$sessMf_id = $session->get('mf_id', array(), 'unisearch');
if(count($sessMf_id)>0){
    $mf_id = $sessMf_id;
}

$product_type_ids = $session->get('product_type_id', '', 'unisearch');
$available_date = $session->get('available_date', array(), 'unisearch');
$pf = $session->get('pf', '', 'unisearch');
$pt = $session->get('pt', '', 'unisearch');

global $mainframe, $mosConfig_live_site;

$itemid = vmGet($_REQUEST, 'Itemid', 0);
$itemid = ($itemid>0) ? $itemid : ps_session::getShopItemid(); 
    
//подключаем скрипты, стили
$document =& JFactory::getDocument();
$baseurl = "
        var url = '".JURI::base()."';
        var show_result_in_virt='".(int)@$conf['show_result_in_virt']."';
        ";
$document->addScriptDeclaration($baseurl);

if ($jquery == 1)
    JHTML::_( 'script', 'jquery-1.6.2.min.js', $mosConfig_live_site . '/components/com_vm_ext_search_pro/js/', false );

if ($jquery_form == 1)
    JHTML::_( 'script', 'jquery.form.js', $mosConfig_live_site . '/components/com_vm_ext_search_pro/js/', false );

if ($jquery_ui == 1) {
    JHTML::_( 'script', 'jquery-ui-1.8.14.custom.min.js', $mosConfig_live_site . '/components/com_vm_ext_search_pro/js/', false );
    JHTML::_( 'script', 'selectToUISlider.jQuery.js', $mosConfig_live_site . '/components/com_vm_ext_search_pro/js/', false );
}

JHTML::_( 'script', 'mod_universal_search.js', $mosConfig_live_site.'/modules/mod_vm_universal_search_pro/files/', false );
JHTML::_( 'stylesheet', 'style.css', $mosConfig_live_site.'/modules/mod_vm_universal_search_pro/files/' );
    //подключаем класс оформления вирта если выводится кнопка купить
if (!empty($conf['show_add_to_cart_in_search_result'])) {
    JHTML::_( 'stylesheet', 'theme.css', VM_THEMEURL );
}

if($option != 'com_virtuemart'){
    global $VM_LANG;
    JHTML::_( 'script', 'theme.js', VM_THEMEURL, true );
    $document->addScriptDeclaration( '
    var cart_title = "'.$VM_LANG->_('PHPSHOP_CART_TITLE').'";
    var ok_lbl="'.$VM_LANG->_('CMN_CONTINUE').'";
    var cancel_lbl="'.$VM_LANG->_('CMN_CANCEL').'";
    var notice_lbl="'.$VM_LANG->_('PEAR_LOG_NOTICE').'";
    var live_site="'.$mosConfig_live_site.'";
    ' );
    JHTML::_( 'script', 'mooPrompt.js', $mosConfig_live_site .'/components/'. VM_COMPONENT_NAME .'/js/mootools/', false );
    JHTML::_('script', 'mootools-release-1.11.js', $mosConfig_live_site . '/components/' . VM_COMPONENT_NAME . '/js/mootools/', false);
    JHTML::_( 'stylesheet', 'mooPrompt.css', $mosConfig_live_site .'/components/'. VM_COMPONENT_NAME .'/js/mootools/' );
}

//подготавливаем данные
if (!empty($conf['show_result_in_virt'])) {
    $ret = '';
}
else{
    $ret = 'onSubmit="return false;"';
}
//подключаем шаблон
require(JModuleHelper::getLayoutPath('mod_vm_universal_search_pro'));
?>
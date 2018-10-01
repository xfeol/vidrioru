<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$base = JURI::base();
//$base = '/administrator/';

$index=JRequest::getCmd('i', '');
$GLOBALS['vmpref']='vm';
$GLOBALS['fajax']= $base .'components/com_fastseller/ajax/ajax.php';
$GLOBALS['comp_path']=JPATH_BASE.'/components/com_fastseller/';
//$user = & JFactory::getUser();
//print_r($user);

$doc =& JFactory::getDocument();
$doc->setTitle('Fast Seller');
//$mainframe->getCfg('sitename')

$doc->addStyleSheet($base .'components/com_fastseller/admin.style.css');

require_once( JPATH_COMPONENT.DS.'controllers'.DS.'layout.php' );
$layout=new fsLayout();
$layout->prepare();

?>
<div id="cmainnav">
<div class="fs-logo" style="margin:0px 15px 0 0px;vertical-align:middle;display:inline-block"></div>
<button class="ui-main-nav" data-url="i=HOME">Home</button>
<button class="ui-main-nav" data-url="i=DP">Assign Filters</button>
<button class="ui-main-nav" data-url="i=PT">Create Filters</button>
<button class="ui-main-nav" data-url="i=CONF">Options</button>
<button class="ui-removetop" id="hideTop" title="Hide/Show top">&uarr;&darr;</button>
</div>
<div id="cstatus" class="cstatus hid">Loading..<br/><img src="<?php echo $base ?>components/com_fastseller/images/load.gif" /></div>
<div id="clayout"></div>
<script type="text/javascript">
var url='<?php echo $GLOBALS['fajax'] ?>';
<?php //echo file_get_contents($GLOBALS['comp_path'].'js/main.js');
	require($GLOBALS['comp_path'].'js/main.js');
?>
</script>
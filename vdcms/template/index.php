<?php
// no direct access
defined( '_VDEXEC' ) or die( 'Restricted access' );

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $language; ?>" lang="<?php echo $language; ?>">
<head>
<link rel="alternate" type="application/rss+xml" title="Новинки. Мебель из стекла от Vidrio.ru" href="http://vidrio.ru/component/virtuemart/feed" />

<link href="/templates/ja_purity/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<link rel="stylesheet" href="/vdcms/template/css/template.css" type="text/css" />

<script type="text/javascript" src="/vdcms/template/js/jquery-1.6.2.min.js"></script>
</head>

<body id="bd" class="fs3 FF" style="background-color:#ffffff;max-width:1280px; min-width:1024px; width:100%; margin:auto;">
<div id="ja-wrapper">

<!-- BEGIN: HEADER -->
<div style="background:#F6FCF6;top:0px;left:0px;height:90px;margin:0 4px; border: solid 1px #9BC89B;">
<table width="100%">
    <tr>
	<td rowspan="2" width="180px" align="center">
	    <?php include('logo.tpl.php'); ?>
	</td>
	<td colspan="2">
	    <!-- BEGIN: MAIN NAVIGATION -->
		<div id="ja-mainnavwrap">
		    <div id="ja-mainnav" class="clearfix" style="position:relative;left:0px;">
			<?php include('modules/topmenu.php'); ?>
		    </div>
		</div>
	    <!-- END: MAIN NAVIGATION -->
	</td>
    </tr>
    <tr>
	<td align="center">
	    <?php include ('modules/header_info.php'); ?>
	</td>
	<td align="right" width="200px;">
	    <?php include ('modules/basket_small.php'); ?>
	</td>
    </tr>
</table>    
</div>
<!-- END: HEADER -->

<div id="ja-containerwrap<?php echo $divid; ?>">

		<div id="ja-mainbody<?php echo $divid; ?>" class="container">

		<!-- BEGIN: CONTENT -->
		<div id="ja-contentwrap" class="clearfix">
			<?php if($page != 'frontPage') : ?>
			<div id="ja-pathway">
			    <?php include ('modules/breadcrumbs.php'); ?>
			</div>
			<?php endif ; ?>

			<div id="main_search">
			    <?php include ('modules/content.php'); ?>
			</div>    
		</div>
		<!-- END: CONTENT -->

		<!-- BEGIN: LEFT COLUMN -->
		<div id="ja-col1" class="clearfix">
		    <?php Module::show('listcategories.php', 'Каталог'); ?>
		</div>
		<!-- END: LEFT COLUMN -->
		
		<!-- BEGIN: RIGHT COLUMN -->
		<div id="ja-col2" class="clearfix">
		
		</div><br />
		<!-- END: RIGHT COLUMN -->

		</div>
</div>


<!-- BEGIN: FOOTER -->
<div id="ja-footerwrap">
<div id="ja-footer" class="clearfix">
	<div class="copyright">
	</div>
	<br />
</div>
</div>
<!-- END: FOOTER -->

</div>

</body>

<script type="text/javascript">
    jQuery.noConflict();
 (function($) {   

    function setEqualHeight(columns)
    {  
	 var tallestcolumn = 0;
	columns.each(
	    function()
	    {  
		currentHeight = $(this).height();  
		if(currentHeight > tallestcolumn)  
		{
		    tallestcolumn  = currentHeight;  
		}
	    }
	);
	columns.height(tallestcolumn);  
    }

    $(window).load(function() {
	setEqualHeight($('.container > div')); 
    });
}) (jQuery);

</script>

</html>
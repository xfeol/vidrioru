<?php
/**
 * @copyrightCopyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @licenseGNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

include_once (dirname(__FILE__).DS.'/ja_vars.php');

?>
<!DOCTYPE html>
<html>
<head>
<link rel="alternate" type="application/rss+xml" title="Новинки. Мебель из стекла от Vidrio.ru" href="http://vidrio.ru/component/virtuemart/feed" />
<jdoc:include type="head" />

<link href="/templates/ja_purity/favicon.ico" rel="shortcut icon" type="image/x-icon" />

<link rel="stylesheet" href="/templates/ja_purity/css/vidrio.css" type="text/css" />
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<link href="http://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic&amp;subset=latin,cyrillic" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
<script type="text/javascript" src="/templates/ja_purity/js/vidrio.js"></script>


<!--[if lt IE 9]>      
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<meta name="viewport" content="width=device-width">

</head>

<body id="bd" class="fs3 FF">
<jdoc:include type="modules" name="topcounter" />

<div id="my-contact-div"><!-- contactable html placeholder --></div>

<!-- BEGIN: HEADER -->
<header class="header_content" role="banner">

    <i id="menubutton" class="fa fa-bars buttonbar"></i>
	<div class="logo">
		<?php if ($this->countModules('logo')): ?>
			<jdoc:include type="modules" name="logo" />
	    <?php endif; ?>
	</div>
	
	<div class="phone">
		<jdoc:include type="modules" name="contacts" />
	</div>
    
	<jdoc:include type="modules" name="navigation" />
	
	<jdoc:include type="modules" name="basket" />
	<div class="search">
		<jdoc:include type="modules" name="search" />
	</div>
    
	
	<a href="javascript:void(0)" id="srchbutton" class="buttonbar fa fa-search"></a>
</header>

<!-- END: HEADER -->


<div id="ja-wrapper">
		<?php if ($this->countModules('left')): ?>
		<!-- BEGIN: LEFT COLUMN -->
		<div id="ja-col1">
			<div class="leftp">
				<jdoc:include type="modules" name="left" style="xhtml" />
			</div>
		</div>
		<!-- END: LEFT COLUMN -->
		<?php endif; ?>
		

		<!-- BEGIN: CONTENT -->
		<div id="ja-contentwrap" role="main">
			<jdoc:include type="message" />
			<?php if(!$tmpTools->isFrontPage()) : ?>
			<div id="ja-pathway">
				<jdoc:include type="modules" name="breadcrumbs" />
			</div>
			<?php endif ; ?>
			<jdoc:include type="component" />
			<?php if($this->countModules('banner')) : ?>
			<div id="ja-banner"><br /><hr /><br />
				<jdoc:include type="modules" name="banner" />
			</div>
			<?php endif; ?>
		</div>
		<!-- END: CONTENT -->
</div>

<!-- BEGIN: FOOTER -->
<footer id="ja-footerwrap" role="contentinfo">
<!--div id="ja-footerwrap"-->
<div id="ja-footer" class="clearfix">
	<div class="copyright">
		<jdoc:include type="modules" name="footer" />
	</div>
	<div id="ja-footnav">
		<jdoc:include type="modules" name="user3" />
	</div>

	<div class="ja-cert">
		<jdoc:include type="modules" name="syndicate" />
    
	</div>

	<br />
</div>
<!--/div-->
</footer>
<!-- END: FOOTER -->

<jdoc:include type="modules" name="debug" />
<?php
  JResponse::setHeader('Last-Modified', gmdate("D, d M Y H:i:s") . ' GMT', false);
?>
<jdoc:include type="modules" name="consultant" />
<div id="scrollup"><i class="fa fa-caret-square-o-up fa-4x"></i></div>
<div id="tooltip_colors">
	<div class="colors"></div>
	<span style="display:block;"><strong>Доступны различные варианты цветов!</strong> Зайдите в карточку товара, чтобы ознакомиться с ними.</span>
</div>

</body>


</html>

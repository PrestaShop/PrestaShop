<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title><?php echo $this->translator->trans('PrestaShop Installation Assistant', array(), 'Install') ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache" content="no store" />
	<meta http-equiv="Expires" content="-1" />
	<meta name="robots" content="noindex" />
	<link rel="shortcut icon" href="theme/img/favicon.ico" />
	<link rel="stylesheet" type="text/css" media="all" href="theme/view.css" />

	<?php if ($this->language->getLanguage()->isRtl() == 'true'): ?>
		<link rel="stylesheet" type="text/css" media="all" href="theme/rtl.css" />
	<?php endif; ?>

	<script type="text/javascript" src="../js/jquery/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="../js/jquery/plugins/jquery.chosen.js"></script>
	<script type="text/javascript" src="theme/js/install.js"></script>
	<script type="text/javascript" src="//www.prestashop.com/js/user-assistance.js"></script>
	<?php if (file_exists(_PS_INSTALL_PATH_.'theme/js/'.self::$steps->current()->getName().'.js')): ?>
		<script type="text/javascript" src="theme/js/<?php echo self::$steps->current()->getName() ?>.js"></script>
	<?php endif; ?>
	<script type="text/javascript">
		var ps_base_uri = '<?php echo addslashes(__PS_BASE_URI__) ?>';
		var ps_version = '<?php echo addslashes(_PS_INSTALL_VERSION_) ?>';
	</script>
</head>

<body>
<div id="container">

<!-- Header -->
<div id="header" class="clearfix">
	<ul id="headerLinks">
		<li class="lnk_forum"><a href="<?php echo $this->getForumLink() ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->translator->trans('Forum', array(), 'Install'); ?></a></li>
		<li class="lnk_forum"><a href="<?php echo $this->getSupportLink() ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->translator->trans('Support', array(), 'Install'); ?></a></li>
		<li class="lnk_forum"><a href="<?php echo $this->getDocumentationLink() ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->translator->trans('Documentation', array(), 'Install'); ?></a></li>
		<li class="lnk_blog last"><a href="<?php echo $this->getBlogLink() ?>" target="_blank" rel="noopener noreferrer"><?php echo $this->translator->trans('Blog', array(), 'Install') ?></a></li>
		<!--
		<?php if ($this->getPhone()): ?>
			<li id="phone_block" class="last">
				<div><span><?php echo $this->translator->trans('Contact us!', array(), 'Install') ?></span><br /><?php echo $this->getPhone() ?></div>
			</li>
		<?php endif; ?>
		-->
	</ul>

	<div id="PrestaShopLogo">PrestaShop</div>
</div>

<!-- Ajax loader animation -->
<div id="loaderSpace">
	<div id="loader">&nbsp;</div>
</div>

<!-- List of steps -->
<div id="leftpannel">
	<ol id="tabs">
		<?php foreach ($this->getSteps() as $step): ?>
			<?php if ($this->step == $step->getName()): ?>
				<li class="selected"><?php echo $step; ?></li>
			<?php elseif ($this->isStepFinished($step->getName())): ?>
				<li class="finished"><a href="index.php?step=<?php echo $step->getName() ?>"><?php echo $step; ?></a></li>
			<?php elseif ($step->getName() == $this->getLastStep()): ?>
				<li class="configuring"><a href="index.php?step=<?php echo $step->getName() ?>"><?php echo $step; ?></a></li>
			<?php else: ?>
				<li><?php echo $step; ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ol>
	<?php if (@fsockopen('api.prestashop.com', 80, $errno, $errst, 3)): ?>
		<iframe scrolling="no" style="height:210px;width:200px;border:none;margin-top:20px" id="iframe_help"
			src="https://api.prestashop.com/iframe/install.php?step=<?php echo $this->step ?>&lang=<?php echo $this->language->getLanguageIso() ?><?php if (isset($this->session->shop_country)) echo '&country='.$this->session->shop_country; ?>">
			<p><?php echo $this->translator->trans('Contact us!', array(), 'Install') ?><br /><?php echo $this->getPhone() ?></p>
		</iframe>
	<?php endif; ?>
</div>

<!-- Page content -->
<form id="mainForm" action="index.php" method="post">
<div id="sheets" class="sheet shown">
	<div id="sheet_<?php echo $this->step ?>" class="sheet shown clearfix">
	<div class="contentTitle">
		<h1><?php echo $this->translator->trans('Installation Assistant', array(), 'Install'); ?></h1>
		<ul id="stepList_1" class="stepList clearfix">
			<?php foreach ($this->getSteps() as $step): ?>
				<li <?php if ($this->isStepFinished($step->getName())): ?>class="ok"<?php endif; ?>><?php echo $step ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<noscript>
		<h4 class="errorBlock" style="margin-bottom:10px">
			<?php echo $this->translator->trans('To install PrestaShop, you need to have JavaScript enabled in your browser.', array(), 'Install'); ?>
			<a href="<?php echo $this->translator->trans('https://enable-javascript.com/', array(), 'Install'); ?>" target="_blank" rel="noopener noreferrer">
				<img src="theme/img/help.png" style="height:16px;width:16px" />
			</a>
		</h4>
	</noscript>

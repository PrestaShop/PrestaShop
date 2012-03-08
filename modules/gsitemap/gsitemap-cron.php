<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/gsitemap.php');

if (substr(Tools::encrypt('gsitemap/cron'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('gsitemap'))
	die('Bad token');

$gsitemap = new Gsitemap();

if (!defined('_PS_BASE_URL_'))
	define('_PS_BASE_URL_', Tools::getShopDomain(true));
if (!defined('_PS_BASE_URL_SSL_'))
	define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

$context = Context::getContext();
$context->link = new Link();

echo $gsitemap->generateSitemapIndex();
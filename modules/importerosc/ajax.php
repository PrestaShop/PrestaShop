<?php
include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('../../modules/importerosc/importerosc.php');


if (!Tools::getValue('ajax') || Tools::getValue('token') != sha1(_COOKIE_KEY_.'importosc'))
	die('INVALID TOKEN');

$importOsc = new importerosc();
$importOsc->server = Tools::getValue('server');
$importOsc->user = Tools::getValue('user');
$importOsc->passwd = Tools::getValue('password');
$importOsc->database = Tools::getValue('database');
$importOsc->prefix = Tools::getValue('prefix');

die($importOsc->createLevelAndCalculate());
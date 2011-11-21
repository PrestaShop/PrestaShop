<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7091 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

/* Redefine REQUEST_URI if empty (on some webservers...) */
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '')
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
if ($tmp = strpos($_SERVER['REQUEST_URI'], '?'))
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);

define('INSTALL_VERSION', '1.5.0.1');
define('MINIMUM_VERSION_TO_UPDATE', '0.8.5');
define('INSTALL_PATH', dirname(__FILE__));
if (version_compare(phpversion(), '5.0.0', '<'))
{
	echo '<html xmlns="http://www.w3.org/1999/xhtml" >
			<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<link rel="stylesheet" type="text/css" media="all" href="view.css"/>
			</head>
			<body>
				<p id="php5_nok">PrestaShop requires <b>PHP5 or later</b>, you are currently running: <b>'.phpversion().'</b><br />
				'.lang('If you do not know how to enable it, use our turnkey solution PrestaBox at').' <a href="http://www.prestabox.com">http://www.prestabox.com</a>.</p>
	</body></html>';
	die;
}

require_once(dirname(__FILE__).'/../config/autoload.php');
require_once(INSTALL_PATH.'/classes/ToolsInstall.php');
require_once(INSTALL_PATH.'/classes/GetVersionFromDb.php');

/* Prevent from bad URI parsing when using index.php */
$requestUri = str_replace('index.php', '', $_SERVER['REQUEST_URI']);
$tmpBaseUri = substr($requestUri, 0, -1 * (strlen($requestUri) - strrpos($requestUri, '/')) - strlen(substr(substr($requestUri,0,-1), strrpos( substr($requestUri,0,-1),"/" )+1)));
define('PS_BASE_URI', $tmpBaseUri[strlen($tmpBaseUri) - 1] == '/' ? $tmpBaseUri : $tmpBaseUri.'/');
define('PS_BASE_URI_ABSOLUTE', 'http://'.ToolsInstall::getHttpHost(false, true).PS_BASE_URI);

/* Old version detection */
$oldversion = false;
$sameVersions = false;
$tooOld = true;
$installOfOldVersion = false;
if (file_exists(INSTALL_PATH.'/../config/settings.inc.php'))
{
	require_once(INSTALL_PATH.'/../config/settings.inc.php');
	$oldversion =_PS_VERSION_;

	// fix : complete version number if there is not all 4 numbers
	// for example replace 1.4.3 by 1.4.3.0
	// consequences : file 1.4.3.0.sql will be skipped if oldversion = 1.4.3
	// @since 1.4.4.0
	$arrayVersion = preg_split('#\.#', $oldversion);
	$versionNumbers = sizeof($arrayVersion);

	if ($versionNumbers != 4)
		$arrayVersion = array_pad($arrayVersion, 4, '0');

	$oldversion = implode('.', $arrayVersion);
	// end of fix

	$tooOld = (version_compare($oldversion, MINIMUM_VERSION_TO_UPDATE) == -1);
	$sameVersions = (version_compare($oldversion, INSTALL_VERSION) == 0);
	$installOfOldVersion = (version_compare($oldversion, INSTALL_VERSION) == 1);
}

require_once(INSTALL_PATH.'/classes/LanguagesManager.php');
$lm = new LanguageManager(dirname(__FILE__).'/langs/list.xml');
$_LANG = array();
$_LIST_WORDS = array();
function lang($txt) {
	global $_LANG , $_LIST_WORDS;
	return (isset($_LANG[$txt]) ? $_LANG[$txt] : $txt);
}
if ($lm->getIncludeTradFilename())
	require_once($lm->getIncludeTradFilename());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache" content="no store" />
	<meta http-equiv="Expires" content="-1" />
	<meta name="robots" content="noindex" />
	<meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
	<title><?php echo sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?></title>
	<link rel="stylesheet" type="text/css" media="all" href="view.css"/>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/ajaxfileupload.js"></script>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/jquery.pngFix.pack.js"></script>
	<link rel="shortcut icon" href="<?php echo PS_BASE_URI ?>img/favicon.ico" />

	<script type="text/javascript">
		//php to js vars
		var isoCodeLocalLanguage = "<?php echo $lm->getIsoCodeSelectedLang(); ?>";
		var ps_base_uri = "<?php echo PS_BASE_URI; ?>";
		var id_lang = <?php echo (isset($_GET['language']) ? (int)($_GET['language']) : 0); ?>;

		//localWords
		var Step1Title = "<?php echo sprintf(lang('Welcome to the PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step2title = "<?php echo lang('System Compatibility').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step3title = "<?php echo lang('Database Configuration').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step4title = "<?php echo lang('Shop Configuration').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step5title = "<?php echo lang('Ready, set, go!').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step6title = "<?php echo lang('Disclaimer').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step7title = "<?php echo lang('System Compatibility').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step8title = "<?php echo lang('Error(s) while updating...').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var step9title = "<?php echo lang('Ready, set, go!').' - '.sprintf(lang('PrestaShop %s Installer'), INSTALL_VERSION); ?>";
		var txtNext = "<?php echo lang('Next')?>"
		var txtDbLoginEmpty = "<?php echo lang('Please set a database login'); ?>";
		var txtDbNameEmpty = "<?php echo lang('Please set a database name'); ?>";
		var txtDbServerEmpty = "<?php echo lang('Please set a database server name'); ?>";
		var txtSmtpAvailable = "<?php echo lang('SMTP connection is available!'); ?>";
		var txtSmtpError = "<?php echo lang('SMTP connection is unavailable'); ?>";
		var txtSmtpSrvEmpty = "<?php echo lang('Please set a SMTP server name'); ?>";
		var txtSmtpLoginEmpty = "<?php echo lang('Please set a SMTP login'); ?>";
		var txtSmtpPasswordEmpty = "<?php echo lang('Please set a SMTP password'); ?>";
		var txtNativeMailAvailable = "<?php echo lang('PHP \'mail()\' function is available'); ?>";
		var txtNativeMailError = "<?php echo lang('PHP \'mail()\' function is unavailable'); ?>";
		var txtDbCreated = "<?php echo lang('Database is created!'); ?>";
		var testMsg = "<?php echo lang('This is a test message, your server is now available to send email'); ?>";
		var testSubject = "<?php echo lang('Test message - Prestashop'); ?>";
		var mailSended = "<?php echo lang('A test e-mail has been sent to'); ?>";
		var mailSubject = "<?php echo lang('Congratulation, your online shop is now ready!'); ?>";
		var txtTabUpdater1 = "<?php echo lang('Welcome'); ?>";
		var txtTabUpdater2 = "<?php echo lang('Disclaimer'); ?>";
		var txtTabUpdater3 = "<?php echo lang('Verify system compatibility'); ?>";
		var txtTabUpdater4 = "<?php echo lang('Update is complete!'); ?>";
		var txtTabInstaller1 = "<?php echo lang('Welcome'); ?>";
		var txtTabInstaller2 = "<?php echo lang('Verify system compatibility'); ?>";
		var txtTabInstaller3 = "<?php echo lang('Database Configuration'); ?>";
		var txtTabInstaller4 = "<?php echo lang('Shop Configuration'); ?>";
		var txtTabInstaller5 = "<?php echo lang('Installation is complete!'); ?>";
		var txtConfigIsOk = "<?php echo lang('Your configuration is valid, click \"Next\" to continue!'); ?>";
		var txtConfigIsNotOk = "<?php echo lang('Your configuration is invalid. Please fix the issues below:'); ?>";

		var txtError = new Array();
		txtError[0] = "<?php echo lang('Required field'); ?>";
		txtError[1] = "<?php echo lang('Too long'); ?>";
		txtError[2] = "<?php echo lang('Fields are different!'); ?>";
		txtError[3] = "<?php echo lang('This e-mail address is invalid!'); ?>";
		txtError[4] = "<?php echo lang('Cannot send the email!'); ?>";
		txtError[5] = "<?php echo lang('Can\'t create settings file, if /config/settings.inc.php exists, please give the public write permissions to this file, otherwise please create a file named settings.inc.php in config directory.'); ?>";
		txtError[6] = "<?php echo lang('Can\'t write settings file, please create a file named settings.inc.php in config directory.'); ?>";
		txtError[7] = "<?php echo lang('Cannot upload the file!'); ?>";
		txtError[8] = "<?php echo lang('Your database connection settings are not valid. Please check your server, name, login and prefix.'); ?>";
		txtError[9] = "<?php echo lang('Cannot read the content of a MySQL data file.'); ?>";
		txtError[10] = "<?php echo lang('Cannot access the a MySQL data file.'); ?>";
		txtError[11] = "<?php echo lang('Error while inserting data in the database:'); ?>";
		txtError[12] = "<?php echo lang('The password is incorrect (alphanumeric string at least 8 characters).'); ?>";
		txtError[14] = "<?php echo lang('A Prestashop database already exists, please drop it or change the prefix.'); ?>";
		txtError[15] = "<?php echo lang('This is not a valid file name.'); ?>";
		txtError[16] = "<?php echo lang('This is not a valid image file.'); ?>";
		txtError[17] = "<?php echo lang('Error while creating the /config/settings.inc.php file.'); ?>";
		txtError[18] = "<?php echo lang('Error:'); ?>";
		txtError[19] = "<?php echo lang('This PrestaShop database already exists. Please revalidate your authentication information to the database.'); ?>";
		txtError[22] = "<?php echo lang('An error occurred while resizing the picture.'); ?>";
		txtError[23] = "<?php echo lang('Database connection is available!'); ?>";
		txtError[24] = "<?php echo lang('Database server is available but database was not found'); ?>";
		txtError[25] = "<?php echo lang('Database server was not found. Please verify the login, password and server fields.'); ?>";
		txtError[26] = "<?php echo lang('An error occurred while sending email, please verify your parameters.'); ?>";
		txtError[37] = "<?php echo lang('Cannot write the image /img/logo.jpg. If this image already exists, please delete it.'); ?>";
		txtError[38] = "<?php echo lang('The uploaded file exceeds the upload_max_filesize directive in php.ini'); ?>";
		txtError[39] = "<?php echo lang('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'); ?>";
		txtError[40] = "<?php echo lang('The uploaded file was only partially uploaded'); ?>";
		txtError[41] = "<?php echo lang('No file was uploaded.'); ?>";
		txtError[42] = "<?php echo lang('Missing a temporary folder'); ?>";
		txtError[43] = "<?php echo lang('Failed to write file to disk'); ?>";
		txtError[44] = "<?php echo lang('File upload stopped by extension'); ?>";
		txtError[45] = "<?php echo lang('Cannot convert your database\'s data to UTF-8.'); ?>";
		txtError[46] = "<?php echo lang('Invalid shop name'); ?>";
		txtError[47] = "<?php echo lang('Your firstname contains some invalid characters'); ?>";
		txtError[48] = "<?php echo lang('Your lastname contains some invalid characters'); ?>";
		txtError[49] = "<?php echo lang('Your database server does not support the UTF-8 charset.'); ?>";
		txtError[50] = "<?php echo lang('Your MySQL server doesn\'t support this engine, please use another one like MyISAM'); ?>";
		txtError[51] = "<?php echo lang('The file /img/logo.jpg is not writable, please CHMOD 755 this file or CHMOD 777'); ?>";
		txtError[52] = "<?php echo lang('Invalid catalog mode'); ?>";
		txtError[999] = "<?php echo lang('No error code available.'); ?>";
		//upgrader
		txtError[27] = "<?php echo lang('This installer is too old.'); ?>";
		txtError[28] = "<?php echo sprintf(lang('You already have the %s version.'), INSTALL_VERSION); ?>";
		txtError[29] = "<?php echo lang('There is no older version. Did you delete or rename the config/settings.inc.php file?'); ?>";
		txtError[30] = "<?php echo lang('The config/settings.inc.php file was not found. Did you delete or rename this file?'); ?>";
		txtError[31] = "<?php echo lang('Can\'t find the sql upgrade files. Please verify that the /install/sql/upgrade folder is not empty)'); ?>";
		txtError[32] = "<?php echo lang('No upgrade is possible.'); ?>";
		txtError[33] = "<?php echo lang('Error while loading sql upgrade file.'); ?>";
		txtError[34] = "<?php echo lang('Error while inserting content into the database'); ?>";
		txtError[35] = "<?php echo lang('Unfortunately,'); ?>";
		txtError[36] = "<?php echo lang('SQL errors have occurred.'); ?>";
		txtError[37] = "<?php echo lang('The config/defines.inc.php file was not found. Where did you move it?'); ?>";
	</script>
	<script type="text/javascript" src="controller.js"></script>

	<script type="text/javascript">
		$(document).ready(function()
		{
			$('#btNext').click(function()
			{
				if (step == 6)
				{
					$.ajax({
						url: 'model.php?method=getVersionFromDb&language='+id_lang,
						success: function (xml)
						{
							var action = $(xml).find('action');
							if (action.attr('result') == 'ko')
							{
								$('#versionWarning span').html(action.attr('lang'));
								$('#versionWarning').show();
							}
						},
					  	error: function() {
					  		errorOccured = true;
					  	}
					});
					$('#btNext, #btBack').removeAttr('disabled').removeClass('disabled');
					if (!$('#btDisclaimerOk').is(':checked'))
						$("#btNext[disabled!=1]").attr("disabled", "disabled").addClass("disabled").addClass("lockedForAjax");
				}
			});
		});
	</script>

</head>
<body>

<div id="noJavaScript">
	<?php echo lang('This application needs you to activate Javascript to correctly work.'); ?>
</div>

<div id="container">
<div id="header" class="clearfix">
	<ul id="headerLinks">
		<li class="lnk_forum"><a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Forums'); ?></a></li>
		<li class="lnk_blog last"><a href="http://www.prestashop.com/blog/"><?php echo lang('Blog'); ?></a></li>
		<?php if ((isset($_GET['language']) AND $_GET['language'] == 1) OR $lm->getIsoCodeSelectedLang() == 'fr'): ?>
		<li id="phone_block" class="last">
			<div><?php echo '<span>'.lang('Contact us!').'</span><br />'.lang('+33 (0)1.40.18.30.04'); ?></div>
		</li>
		<?php endif; ?>
		<?php if ((isset($_GET['language']) AND $_GET['language'] == 0) OR $lm->getIsoCodeSelectedLang() == 'en'): ?>
		<li id="phone_block" class="last">
			<div><?php echo '<span>'.lang('Contact us!').'</span><br />'.lang('+1 (888) 947-6543'); ?></div>
		</li>
		<?php endif; ?>
	</ul>

	<div id="PrestaShopLogo">PrestaShop</div>

	<div id="infosSup">
		<div class="installerVersion" id="installerVersion-<?php echo $lm->getIsoCodeSelectedLang()?>">PrestaShop <?php echo INSTALL_VERSION.'<br />'.lang('Installer'); ?></div>
		<div class="updaterVersion" id="updaterVersion-<?php echo $lm->getIsoCodeSelectedLang()?>">PrestaShop <?php echo INSTALL_VERSION.'<br />'.lang('Updater'); ?></div>
	</div>
</div><!-- /end header -->

<div id="loaderSpace">
	<div id="loader">&nbsp;</div>
</div><!-- /end loaderSpace -->

<div id="leftpannel">
	<ol id="tabs"><li>&nbsp;</li></ol>

	<div id="help">
		<img src="img/ico_help.gif" alt="help" class="ico_help" />

		<div class="content">
			<p class="title"><?php echo lang('Need help?'); ?></p>
			<p class="title_down"><?php echo lang('PrestaShop tips and advice'); ?></p>
		</div>
	</div><!-- /end help -->
</div><!-- /end leftpannel -->


<div id="sheets">

	<div class="sheet shown" id="sheet_lang">
		<div class="contentTitle">
			<h1><?php echo lang('Welcome')?></h1>

			<ul id="stepList_1" class="stepList clearfix">
				<li>Etape 1</li>
				<li>Etape 2</li>
				<li>Etape 3</li>
				<li>Etape 4</li>
				<li>Etape 5</li>
			</ul>
		</div>

		<h2 id="welcome-title"><?php echo lang('Welcome to the PrestaShop '.INSTALL_VERSION.' Installer.'); ?></h2>
		<script type="text/javascript">$('#welcome-title').html(Step1Title);</script>
		<p><?php echo lang('Please allow 10-15 minutes to complete the installation process.')?></p>
		<p><?php echo lang('The PrestaShop Installer will do most of the work for you in just a few clicks.')?><br /><br />
		<?php echo lang('However, you must know how to do the following:')?></p>
		<ul>
			<li><?php echo lang('Set permissions on folders & subfolders using an FTP client')?></li>
			<li><?php echo lang('Create a MySQL database using phpMyAdmin (or by asking your hosting provider)')?></li>
		</ul>
		<p>
			<?php echo lang('For more information, please consult our') ?> <a href="http://doc.prestashop.com/display/PS14/Getting+Started"><?php echo lang('online documentation') ?></a>.
		</p>

		<h2><?php echo lang('Choose your prefered language for the installation:')?></h2>
		<form id="formSetInstallerLanguage" action="<?php $_SERVER['REQUEST_URI']; ?>" method="get">
			<ul id="langList" style="line-height: 20px;">
			<?php foreach ($lm->getAvailableLangs() AS $lang): ?>
				<li><input onclick="setInstallerLanguage()" type="radio" value="<?php echo $lang['id'] ?>" <?php echo ( $lang['id'] == $lm->getIdSelectedLang() ) ? "checked=\"checked\"" : '' ?> id="lang_<?php echo $lang['id'] ?>" name="language" style="vertical-align: middle; margin-right: 0;" /><label for="lang_<?php echo $lang['id'] ?>">
				<?php foreach ($lang->flags->url AS $url_flag): ?>
					<img src="<?php echo $url_flag ?>" alt="<?php echo $lang['label'] ?>" style="vertical-align: middle;" />
				<?php endforeach;  ?>
				<?php echo $lang['label'] ?></label></li>

			<?php endforeach; ?>
			</ul>
		</form>
		<h3 class="no-margin"><?php echo lang('Did you know?'); ?></h3>
		<p>
			<?php

				$isoForLink = (in_array($lm->getIsoCodeSelectedLang(), array('fr', 'it', 'de', 'en', 'es')) ? $lm->getIsoCodeSelectedLang() : 'en');
				echo lang('Prestashop and its community offers over 40 different languages for free download at');

			?><br /><a href="http://www.prestashop.com/<?php echo $isoForLink; ?>/downloads/#lang_pack" target="_blank">http://www.prestashop.com/<?php echo $isoForLink; ?>/downloads/#lang_pack</a>
		</p>

		<h2><?php echo lang('What do you want to do?')?></h2>
		<form id="formSetMethod" action="<?php $_SERVER['REQUEST_URI']; ?>" method="post">
			<p><input <?php echo (!($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion)) ? 'checked="checked"' : '' ?> type="radio" value="install" name="typeInstall" id="typeInstallInstall" style="vertical-align: middle;" /> <label for="typeInstallInstall"><?php echo lang('I want to <b>install</b> a new online shop with PrestaShop'); ?></label></p>
			<p style="font-style: italic;"><?php echo lang('- or -'); ?></p>
			<p <?php echo ($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion) ? '' : 'class="disabled"'; ?>><input <?php echo ($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion) ? 'checked="checked"' : 'disabled="disabled"'; ?> type="radio" value="upgrade" name="typeInstall" id="typeInstallUpgrade" style="vertical-align: middle;" /> <label <?php echo ($oldversion === false) ? 'class="disabled"' : ''; ?> for="typeInstallUpgrade"><?php echo lang('I want to <b>update</b> my existing PrestaShop to a newer version'); ?> <?php echo ($oldversion === false) ? lang('(No previous version detected)') : ("(".(($tooOld) ? lang('Your current version is too old, updates are possible only from version').' '.MINIMUM_VERSION_TO_UPDATE.' '.lang('and higher') : ($installOfOldVersion ? lang('Your current version is already up-to-date') : lang('Currently installed version detected:').' <b>v'.$oldversion.'</b>')).")") ?></label></p>
		</form>
		<h2><?php echo lang('License Agreement')?></h2>
		<div style="height:200px; border:1px solid #ccc; margin-bottom:8px; padding:5px; background:#fff; overflow: auto; overflow-x:hidden; overflow-y:scroll;">
			<strong><?php echo lang('PrestaShop core is released under the OSL 3.0 while PrestaShop modules and themes are released under the AFL 3.0.')?></strong>
			<h3>Core: Open Software License ("OSL") v. 3.0</h3>
			<p>This Open Software License (the "License") applies to any original work of authorship (the "Original Work") whose owner (the "Licensor") has placed the following licensing notice adjacent to the copyright notice for the Original Work:</p>
			<h4>Licensed under the Open Software License version 3.0</h4>
			<p><strong>1. Grant of Copyright License.</strong> Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, for the duration of the copyright, to do the following:</p>
			<ol type="a">
					<li>to reproduce the Original Work in copies, either alone or as part of a collective work</li>
					<li>to translate, adapt, alter, transform, modify, or arrange the Original Work, thereby creating derivative works ("Derivative Works") based upon the Original Work</li>
					<li>to distribute or communicate copies of the Original Work and Derivative Works to the public, with the proviso that copies of Original Work or Derivative Works that You distribute or communicate shall be licensed under this Open Software License</li>
					<li>to perform the Original Work publicly</li>
					<li>to display the Original Work publicly</li>
			</ol>
			<p><strong>2. Grant of Patent License.</strong> Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, under patent claims owned or controlled by the Licensor that are embodied in the Original Work as furnished by the Licensor, for the duration of the patents, to make, use, sell, offer for sale, have made, and import the Original Work and Derivative Works.</p>
			<p><strong>3. Grant of Source Code License.</strong> The term "Source Code" means the preferred form of the Original Work for making modifications to it and all available documentation describing how to modify the Original Work. Licensor agrees to provide a machine-readable copy of the Source Code of the Original Work along with each copy of the Original Work that Licensor distributes. Licensor reserves the right to satisfy this obligation by placing a machine-readable copy of the Source Code in an information repository reasonably calculated to permit inexpensive and convenient access by You for as long as Licensor continues to distribute the Original Work.</p>
			<p><strong>4. Exclusions From License Grant.</strong> Neither the names of Licensor, nor the names of any contributors to the Original Work, nor any of their trademarks or service marks, may be used to endorse or promote products derived from this Original Work without express prior permission of the Licensor. Except as expressly stated herein, nothing in this License grants any license to Licensor's trademarks, copyrights, patents, trade secrets or any other intellectual property. No patent license is granted to make, use, sell, offer for sale, have made, or import embodiments of any patent claims other than the licensed claims defined in Section 2. No license is granted to the trademarks of Licensor even if such marks are included in the Original Work. Nothing in this License shall be interpreted to prohibit Licensor from licensing under terms different from this License any Original Work that Licensor otherwise would have a right to license.</p>
			<p><strong>5. External Deployment.</strong> The term "External Deployment" means the use, distribution, or communication of the Original Work or Derivative Works in any way such that the Original Work or Derivative Works may be used by anyone other than You, whether those works are distributed or communicated to those persons or made available as an application intended for use over a network. As an express condition for the grants of license hereunder, You must treat any External Deployment by You of the Original Work or a Derivative Work as a distribution under section 1(c).</p>
			<p><strong>6. Attribution Rights.</strong> You must retain, in the Source Code of any Derivative Works that You create, all copyright, patent, or trademark notices from the Source Code of the Original Work, as well as any notices of licensing and any descriptive text identified therein as an "Attribution Notice." You must cause the Source Code for any Derivative Works that You create to carry a prominent Attribution Notice reasonably calculated to inform recipients that You have modified the Original Work.</p>
			<p><strong>7. Warranty of Provenance and Disclaimer of Warranty.</strong> Licensor warrants that the copyright in and to the Original Work and the patent rights granted herein by Licensor are owned by the Licensor or are sublicensed to You under the terms of this License with the permission of the contributor(s) of those copyrights and patent rights. Except as expressly stated in the immediately preceding sentence, the Original Work is provided under this License on an "AS IS" BASIS and WITHOUT WARRANTY, either express or implied, including, without limitation, the warranties of non-infringement, merchantability or fitness for a particular purpose. THE ENTIRE RISK AS TO THE QUALITY OF THE ORIGINAL WORK IS WITH YOU. This DISCLAIMER OF WARRANTY constitutes an essential part of this License. No license to the Original Work is granted by this License except under this disclaimer.</p>
			<p><strong>8. Limitation of Liability.</strong> Under no circumstances and under no legal theory, whether in tort (including negligence), contract, or otherwise, shall the Licensor be liable to anyone for any indirect, special, incidental, or consequential damages of any character arising as a result of this License or the use of the Original Work including, without limitation, damages for loss of goodwill, work stoppage, computer failure or malfunction, or any and all other commercial damages or losses. This limitation of liability shall not apply to the extent applicable law prohibits such limitation.</p>
			<p><strong>9. Acceptance and Termination.</strong> If, at any time, You expressly assented to this License, that assent indicates your clear and irrevocable acceptance of this License and all of its terms and conditions. If You distribute or communicate copies of the Original Work or a Derivative Work, You must make a reasonable effort under the circumstances to obtain the express assent of recipients to the terms of this License. This License conditions your rights to undertake the activities listed in Section 1, including your right to create Derivative Works based upon the Original Work, and doing so without honoring these terms and conditions is prohibited by copyright law and international treaty. Nothing in this License is intended to affect copyright exceptions and limitations (including 'fair use' or 'fair dealing'). This License shall terminate immediately and You may no longer exercise any of the rights granted to You by this License upon your failure to honor the conditions in Section 1(c).</p>
			<p><strong>10. Termination for Patent Action.</strong> This License shall terminate automatically and You may no longer exercise any of the rights granted to You by this License as of the date You commence an action, including a cross-claim or counterclaim, against Licensor or any licensee alleging that the Original Work infringes a patent. This termination provision shall not apply for an action alleging patent infringement by combinations of the Original Work with other software or hardware.</p>
			<p><strong>11. Jurisdiction, Venue and Governing Law.</strong> Any action or suit relating to this License may be brought only in the courts of a jurisdiction wherein the Licensor resides or in which Licensor conducts its primary business, and under the laws of that jurisdiction excluding its conflict-of-law provisions. The application of the United Nations Convention on Contracts for the International Sale of Goods is expressly excluded. Any use of the Original Work outside the scope of this License or after its termination shall be subject to the requirements and penalties of copyright or patent law in the appropriate jurisdiction. This section shall survive the termination of this License.</p>
			<p><strong>12. Attorneys Fees.</strong> In any action to enforce the terms of this License or seeking damages relating thereto, the prevailing party shall be entitled to recover its costs and expenses, including, without limitation, reasonable attorneys' fees and costs incurred in connection with such action, including any appeal of such action. This section shall survive the termination of this License.</p>
			<p><strong>13. Miscellaneous.</strong> If any provision of this License is held to be unenforceable, such provision shall be reformed only to the extent necessary to make it enforceable.</p>
			<p><strong>14. Definition of "You" in This License.</strong> "You" throughout this License, whether in upper or lower case, means an individual or a legal entity exercising rights under, and complying with all of the terms of, this License. For legal entities, "You" includes any entity that controls, is controlled by, or is under common control with you. For purposes of this definition, "control" means (i) the power, direct or indirect, to cause the direction or management of such entity, whether by contract or otherwise, or (ii) ownership of fifty percent (50%) or more of the outstanding shares, or (iii) beneficial ownership of such entity.</p>
			<p><strong>15. Right to Use.</strong> You may use the Original Work in all ways not otherwise restricted or conditioned by this License or by law, and Licensor promises not to interfere with or be responsible for such uses by You.</p>
			<p><strong>16. Modification of This License.</strong> This License is Copyright &copy; 2005 Lawrence Rosen. Permission is granted to copy, distribute, or communicate this License without modification. Nothing in this License permits You to modify this License as applied to the Original Work or to Derivative Works. However, You may modify the text of this License and copy, distribute or communicate your modified version (the "Modified License") and apply it to other original works of authorship subject to the following conditions: (i) You may not indicate in any way that your Modified License is the "Open Software License" or "OSL" and you may not use those names in the name of your Modified License; (ii) You must replace the notice specified in the first paragraph above with the notice "Licensed under Open Software License ("OSL") v. 3.0" or with a notice of your own that is not confusingly similar to the notice in this License; and (iii) You may not claim that your original works are open source software unless your Modified License has been approved by Open Source Initiative (OSI) and You comply with its license review and certification process.</p>

			<h3>Modules and Themes: Academic Free License ("AFL") v. 3.0</h3>
			<p>This Academic Free License (the "License") applies to any original work of authorship (the "Original Work") whose owner (the "Licensor") has placed the following licensing notice adjacent to the copyright notice for the Original Work:</p>
			<h4>Licensed under the Academic Free License version 3.0</h4>
			<p><strong>1. Grant of Copyright License.</strong> Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, for the duration of the copyright, to do the following:</p>
			<ol type="a">
				<li>to reproduce the Original Work in copies, either alone or as part of a collective work;</li>
				<li>to translate, adapt, alter, transform, modify, or arrange the Original Work, thereby creating derivative works ("Derivative Works") based upon the Original Work;</li>
				<li>to distribute or communicate copies of the Original Work and Derivative Works to the public, <u>under any license of your choice that does not contradict the terms and conditions, including Licensor's reserved rights and remedies, in this Academic Free License</u>;</li>
				<li>to perform the Original Work publicly; and</li>
				<li>to display the Original Work publicly.</li>
			</ol>
			<p><strong>2. Grant of Patent License.</strong> Licensor grants You a worldwide, royalty-free, non-exclusive, sublicensable license, under patent claims owned or controlled by the Licensor that are embodied in the Original Work as furnished by the Licensor, for the duration of the patents, to make, use, sell, offer for sale, have made, and import the Original Work and Derivative Works.</p>
			<p><strong>3. Grant of Source Code License.</strong> The term "Source Code" means the preferred form of the Original Work for making modifications to it and all available documentation describing how to modify the Original Work. Licensor agrees to provide a machine-readable copy of the Source Code of the Original Work along with each copy of the Original Work that Licensor distributes. Licensor reserves the right to satisfy this obligation by placing a machine-readable copy of the Source Code in an information repository reasonably calculated to permit inexpensive and convenient access by You for as long as Licensor continues to distribute the Original Work.</p>
			<p><strong>4. Exclusions From License Grant.</strong> Neither the names of Licensor, nor the names of any contributors to the Original Work, nor any of their trademarks or service marks, may be used to endorse or promote products derived from this Original Work without express prior permission of the Licensor. Except as expressly stated herein, nothing in this License grants any license to Licensor's trademarks, copyrights, patents, trade secrets or any other intellectual property. No patent license is granted to make, use, sell, offer for sale, have made, or import embodiments of any patent claims other than the licensed claims defined in Section 2. No license is granted to the trademarks of Licensor even if such marks are included in the Original Work. Nothing in this License shall be interpreted to prohibit Licensor from licensing under terms different from this License any Original Work that Licensor otherwise would have a right to license.</p>
			<p><strong>5. External Deployment.</strong> The term "External Deployment" means the use, distribution, or communication of the Original Work or Derivative Works in any way such that the Original Work or Derivative Works may be used by anyone other than You, whether those works are distributed or communicated to those persons or made available as an application intended for use over a network. As an express condition for the grants of license hereunder, You must treat any External Deployment by You of the Original Work or a Derivative Work as a distribution under section 1(c).</p>
			<p><strong>6. Attribution Rights.</strong> You must retain, in the Source Code of any Derivative Works that You create, all copyright, patent, or trademark notices from the Source Code of the Original Work, as well as any notices of licensing and any descriptive text identified therein as an "Attribution Notice." You must cause the Source Code for any Derivative Works that You create to carry a prominent Attribution Notice reasonably calculated to inform recipients that You have modified the Original Work.</p>
			<p><strong>7. Warranty of Provenance and Disclaimer of Warranty.</strong> Licensor warrants that the copyright in and to the Original Work and the patent rights granted herein by Licensor are owned by the Licensor or are sublicensed to You under the terms of this License with the permission of the contributor(s) of those copyrights and patent rights. Except as expressly stated in the immediately preceding sentence, the Original Work is provided under this License on an "AS IS" BASIS and WITHOUT WARRANTY, either express or implied, including, without limitation, the warranties of non-infringement, merchantability or fitness for a particular purpose. THE ENTIRE RISK AS TO THE QUALITY OF THE ORIGINAL WORK IS WITH YOU. This DISCLAIMER OF WARRANTY constitutes an essential part of this License. No license to the Original Work is granted by this License except under this disclaimer.</p>
			<p><strong>8. Limitation of Liability.</strong> Under no circumstances and under no legal theory, whether in tort (including negligence), contract, or otherwise, shall the Licensor be liable to anyone for any indirect, special, incidental, or consequential damages of any character arising as a result of this License or the use of the Original Work including, without limitation, damages for loss of goodwill, work stoppage, computer failure or malfunction, or any and all other commercial damages or losses. This limitation of liability shall not apply to the extent applicable law prohibits such limitation.</p>
			<p><strong>9. Acceptance and Termination.</strong> If, at any time, You expressly assented to this License, that assent indicates your clear and irrevocable acceptance of this License and all of its terms and conditions. If You distribute or communicate copies of the Original Work or a Derivative Work, You must make a reasonable effort under the circumstances to obtain the express assent of recipients to the terms of this License. This License conditions your rights to undertake the activities listed in Section 1, including your right to create Derivative Works based upon the Original Work, and doing so without honoring these terms and conditions is prohibited by copyright law and international treaty. Nothing in this License is intended to affect copyright exceptions and limitations (including "fair use" or "fair dealing"). This License shall terminate immediately and You may no longer exercise any of the rights granted to You by this License upon your failure to honor the conditions in Section 1(c).</p>
			<p><strong>10. Termination for Patent Action.</strong> This License shall terminate automatically and You may no longer exercise any of the rights granted to You by this License as of the date You commence an action, including a cross-claim or counterclaim, against Licensor or any licensee alleging that the Original Work infringes a patent. This termination provision shall not apply for an action alleging patent infringement by combinations of the Original Work with other software or hardware.</p>
			<p><strong>11. Jurisdiction, Venue and Governing Law.</strong> Any action or suit relating to this License may be brought only in the courts of a jurisdiction wherein the Licensor resides or in which Licensor conducts its primary business, and under the laws of that jurisdiction excluding its conflict-of-law provisions. The application of the United Nations Convention on Contracts for the International Sale of Goods is expressly excluded. Any use of the Original Work outside the scope of this License or after its termination shall be subject to the requirements and penalties of copyright or patent law in the appropriate jurisdiction. This section shall survive the termination of this License.</p>
			<p><strong>12. Attorneys' Fees.</strong> In any action to enforce the terms of this License or seeking damages relating thereto, the prevailing party shall be entitled to recover its costs and expenses, including, without limitation, reasonable attorneys' fees and costs incurred in connection with such action, including any appeal of such action. This section shall survive the termination of this License.</p>
			<p><strong>13. Miscellaneous.</strong> If any provision of this License is held to be unenforceable, such provision shall be reformed only to the extent necessary to make it enforceable.</p>
			<p><strong>14. Definition of "You" in This License.</strong> "You" throughout this License, whether in upper or lower case, means an individual or a legal entity exercising rights under, and complying with all of the terms of, this License. For legal entities, "You" includes any entity that controls, is controlled by, or is under common control with you. For purposes of this definition, "control" means (i) the power, direct or indirect, to cause the direction or management of such entity, whether by contract or otherwise, or (ii) ownership of fifty percent (50%) or more of the outstanding shares, or (iii) beneficial ownership of such entity.</p>
			<p><strong>15. Right to Use.</strong> You may use the Original Work in all ways not otherwise restricted or conditioned by this License or by law, and Licensor promises not to interfere with or be responsible for such uses by You.</p>
			<p><strong>16. Modification of This License.</strong> This License is Copyright © 2005 Lawrence Rosen. Permission is granted to copy, distribute, or communicate this License without modification. Nothing in this License permits You to modify this License as applied to the Original Work or to Derivative Works. However, You may modify the text of this License and copy, distribute or communicate your modified version (the "Modified License") and apply it to other original works of authorship subject to the following conditions: (i) You may not indicate in any way that your Modified License is the "Academic Free License" or "AFL" and you may not use those names in the name of your Modified License; (ii) You must replace the notice specified in the first paragraph above with the notice "Licensed under <insert your license name here>" or with a notice of your own that is not confusingly similar to the notice in this License; and (iii) You may not claim that your original works are open source software unless your Modified License has been approved by Open Source Initiative (OSI) and You comply with its license review and certification process.</p>
		</div>
		<p>
			<input type="checkbox" id="set_license" class="required" style="vertical-align: middle;" /><label for="set_license"><strong><?php echo lang('I agree to the above terms and conditions.'); ?></strong></label><br/>
		</p>
	</div>

		<div class="sheet clearfix" id="sheet_require">
			<div class="contentTitle">
				<h1><?php echo lang('System Compatibility')?></h1>

			<ul id="stepList_2" class="stepList clearfix">
				<li class="ok">Etape 1</li>
				<li>Etape 2</li>
				<li>Etape 3</li>
				<li>Etape 4</li>
				<li>Etape 5</li>
			</ul>
			</div>

			<h2><?php echo lang('Required set-up. Please verify the following checklist items:')?></h2>

			<p>
				<?php echo lang('If you have any questions, please visit our '); ?>
				<a href="http://doc.prestashop.com/display/PS14/Getting+Started " target="_blank"><?php echo lang('Online documentation'); ?></a>
				<?php echo lang('and/or'); ?>
				<a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Community Forum'); ?></a><?php echo lang('.'); ?>
			</p>

			<h3 id="resultConfig"></h3>
			<ul id="required">
				<li class="title"><?php echo lang('PHP settings (for assistance, ask your hosting provider):')?></li>
				<li class="required first"><?php echo lang('PHP 5.0 or later installed')?></li>
				<li class="required"><?php echo lang('File upload allowed')?></li>
				<li class="required"><?php echo lang('Creation of new files and folders allowed')?></li>
				<li class="required"><?php echo lang('GD Library installed')?></li>
				<li class="required"><?php echo lang('MySQL support is on')?></li>
				<li class="title"><?php echo lang('Write permissions on files and folders:')?></li>
				<li class="required first">/config</li>
				<li class="required">/cache</li>
				<li class="required">/sitemap.xml</li>
				<li class="required">/log</li>
				<li class="title"><?php echo lang('Write permissions on folders (and subfolders):')?></li>
				<li class="required first">/img</li>
				<li class="required">/mails</li>
				<li class="required">/modules</li>
				<li class="required">/themes/prestashop/lang</li>
				<li class="required">/themes/prestashop/cache</li>
				<li class="required">/translations</li>
				<li class="required">/upload</li>
				<li class="required">/download</li>
			</ul>

			<h3 style="padding-bottom: 0;"><?php echo lang('Optional set-up')?></h3>
			<ul id="optional">
				<li class="title"><?php echo lang('PHP settings (for assistance, ask your hosting provider):')?></li>
				<li class="optional"><?php echo lang('Open external URLs allowed')?></li>
				<li class="optional"><?php echo lang('PHP register global option is off (recommended)')?></li>
				<li class="optional"><?php echo lang('GZIP compression is on (recommended)')?></li>
				<li class="optional"><?php echo lang('Mcrypt is available (recommended)')?></li>
				<li class="optional"><?php echo lang('PHP magic quotes option is off (recommended)')?></li>
				<li class="optional"><?php echo lang('Dom extension loaded')?></li>
			</ul>
			<h3 style="display:none;" id="resultConfigHelper"><?php echo lang('If you do not know how to fix these issues, use turnkey solution PrestaBox at');?> <a href="http://www.prestabox.com">http://www.prestabox.com</a></h3>
			<p><input class="button" value="<?php echo lang('Check my settings again')?>" type="button" id="req_bt_refresh"/><br /><br /></p>
		</div>
		<div class="sheet clearfix" id="sheet_db">
			<div class="contentTitle">
				<h1><?php echo lang('Database configuration')?></h1>

			<ul id="stepList_3" class="stepList clearfix">
				<li class="ok">Etape 1</li>
				<li class="ok">Etape 2</li>
				<li>Etape 3</li>
				<li>Etape 4</li>
				<li>Etape 5</li>
			</ul>
			</div>

			<div id="dbPart">
				<h2 style="padding-bottom: 0;"><?php echo lang('Configure your database by filling out the following fields:')?></h2>
				<p style="padding: 10px 0 0 0;"><?php echo lang('Please create a MySQL database and then verify your settings below. If you need assistance, please ask your hosting provider for this information.'); ?></p>
			<form id="formCheckSQL" class="aligned" action="<?php $_SERVER['REQUEST_URI']; ?>" onsubmit="verifyDbAccess(); return false;" method="post">
					<p class="first" style="margin-top: 15px;">
					<label for="dbServer"><?php echo lang('Server:')?> </label>
					<input size="25" class="text" type="text" id="dbServer" value="localhost"/>
				</p>
				<p>
					<label for="dbName"><?php echo lang('Database name:')?> </label>
					<input size="10" class="text" type="text" id="dbName" value="prestashop"/>
				</p>
				<p>
						<label for="dbLogin"><?php echo lang('Database login:')?> </label>
					<input class="text" size="10" type="text" id="dbLogin" value="root"/>
				</p>
				<p>
						<label for="dbPassword"><?php echo lang('Database password:')?> </label>
					<input class="text" autocomplete="off" size="10" type="password" id="dbPassword"/>
				</p>
				<p>
					<label for="dbEngine"><?php echo lang('Database Engine:')?></label>
					<select id="dbEngine" name="dbEngine">
						<option value="InnoDB">InnoDB</option>
						<option value="MyISAM">MyISAM</option>
					</select>
				</p>
					<p class="last">
						<label for="db_prefix"><?php echo lang('Tables prefix:')?></label>
						<input class="text" type="text" id="db_prefix" value="ps_"/>
					</p>
					<p class="aligned" style="background: none;">
						<input id="btTestDB" class="button" type="submit" value="<?php echo lang('Verify my database settings')?>"/>
				</p>
					<p id="dbResultCheck" style="display:none;"></p>
			</form>
			</div>

			<div id="dbTableParam">
				<form action="#" method="post" onsubmit="createDB(); return false;">
				<h2><?php echo lang('Installation type')?></h2>
				<p id="dbModeSetter" style="line-height: 20px; padding-bottom: 0;">
					<input value="lite" type="radio" name="db_mode" id="db_mode_simple" style="vertical-align: middle;" /> <label for="db_mode_simple"><?php echo lang('Lite mode: Basic installation')?> <span><?php echo lang('(FREE)'); ?></span></label><br />
					<span style="font-style: italic;"><?php echo lang('- or -'); ?></span><br />
					<input value="full" type="radio" name="db_mode" checked="checked" id="db_mode_complet" style="vertical-align: middle;" /> <label for="db_mode_complet"><?php echo lang('Full mode: includes core modules,').' <b>'.lang('100+ additional modules').'</b> '.lang('and demo products'); ?> <span><?php echo lang('(FREE)'); ?></span></label>
				</p>
				</form>
				<p id="dbCreateResultCheck"></p>
			</div>

			<div id="mailPart">
				<h2><?php echo lang('E-mail configuration')?></h2>

				<p id="configsmtp">
					<input type="checkbox" id="set_stmp" style="vertical-align: middle;" /><label for="set_stmp"><?php echo lang('Configure SMTP manually (advanced users only)'); ?></label><br/>
					<span class="userInfos"><?php echo lang('By default, the PHP \'mail()\' function is used (recommended)'); ?></span>
				</p>

				<div id="mailSMTPParam">
					<form class="aligned" action="#" method="post" onsubmit="verifyMail(); return false;">
						<p>
							<label for="smtpSrv"><?php echo lang('SMTP server:'); ?> </label>
							<input class="text" type="text" id="smtpSrv" value="smtp."/>
						</p>
						<p>
							<label for="smtpEnc"><?php echo lang('Encryption:'); ?></label>
							<select id="smtpEnc">
								<option value="off" selected="selected"><?php echo lang('None'); ?></option>
								<option value="tls">TLS</option>
								<option value="ssl">SSL</option>
							</select>
						</p>

						<p>
							<label for="smtpPort"><?php echo lang('Port:'); ?></label>
							<input type="text" size="5" id="smtpPort" value="25" class="text" />
						</p>

						<p>
							<label for="smtpLogin"><?php echo lang('Login:'); ?> </label>
							<input class="text" type="text" size="10" id="smtpLogin" value="" />
						</p>

						<p>
							<label for="smtpPassword"><?php echo lang('Password:'); ?> </label>
							<input autocomplete="off" class="text" type="password" size="10" id="smtpPassword" />
						</p>

					</form>
				</div>
				<p style="padding-bottom: 0;">
					<input class="text" id="testEmail" type="text" size="15" value="<?php echo lang('enter@your.email'); ?>" /> &nbsp;
					<input id="btVerifyMail" class="button" type="submit" value="<?php echo lang('Send me a test email!'); ?>" />
				</p>
				<p id="mailResultCheck"></p>
			</div>
		</div>

		<div class="sheet clearfix" id="sheet_infos">
			<form action="<?php $_SERVER['REQUEST_URI']; ?>" method="post" onsubmit="return false;" enctype="multipart/form-data">
				<div class="contentTitle">
					<h1><?php echo lang('Shop configuration')?></h1>

					<ul id="stepList_4" class="stepList clearfix">
						<li class="ok">Etape 1 ok</li>
						<li class="ok">Etape 2 ok</li>
						<li class="ok">Etape 3 ok</li>
						<li>Etape 4</li>
						<li>Etape 5</li>
					</ul>
				</div>

				<div id="infosShopBlock">
					<h2><?php echo lang('Shop settings and merchant account information'); ?></h2>
				<div class="field">
						<label for="infosShop" class="aligned"><?php echo lang('Shop name:'); ?> </label>
						<span class="contentinput">
							<input class="text required" type="text" id="infosShop" value=""/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosShop" class="result aligned"></span>
				</div>
				<div class="field">
					<label for="infosActivity" class="aligned"><?php echo lang('Main activity:'); ?></label>
						<span class="contentinput">
							<select id="infosActivity">
						<option value="0"><?php echo lang('-- Please choose your main activity --'); ?></option>
						<option value="1"><?php echo lang('Adult'); ?></option>
						<option value="2"><?php echo lang('Animals and Pets'); ?></option>
						<option value="3"><?php echo lang('Art and Culture'); ?></option>
						<option value="4"><?php echo lang('Babies'); ?></option>
						<option value="5"><?php echo lang('Beauty and Personal Care'); ?></option>
						<option value="6"><?php echo lang('Cars'); ?></option>
						<option value="7"><?php echo lang('Computer Hardware and Software'); ?></option>
						<option value="8"><?php echo lang('Download'); ?></option>
						<option value="9"><?php echo lang('Fashion and accessories'); ?></option>
						<option value="10"><?php echo lang('Flowers, Gifts and Crafts'); ?></option>
						<option value="11"><?php echo lang('Food and beverage'); ?></option>
						<option value="12"><?php echo lang('HiFi, Photo and Video'); ?></option>
						<option value="13"><?php echo lang('Home and Garden'); ?></option>
						<option value="14"><?php echo lang('Home Appliances'); ?></option>
						<option value="15"><?php echo lang('Jewelry'); ?></option>
						<option value="16"><?php echo lang('Mobile and Telecom'); ?></option>
						<option value="17"><?php echo lang('Services'); ?></option>
						<option value="18"><?php echo lang('Shoes and accessories'); ?></option>
								<option value="19"><?php echo lang('Sports and Entertainment'); ?></option>
						<option value="20"><?php echo lang('Travel'); ?></option>
						<option value="0"><?php echo lang('Other activity...'); ?></option>
					</select>
						</span>
						<p class="userInfos aligned"><?php echo lang('This information is not required, it will only be used for statistical purposes. This information does not change anything in your store.'); ?></p>
				</div>
				<div class="field">
					<label for="infosCountry" class="aligned"><?php echo lang('Default country:'); ?></label>
						<span class="contentinput">
							<select name="infosCountry" id="infosCountry">
								<option disabled="disabled"><?php echo lang('-- Select your country --'); ?></option>
							</select> <sup class="required">*</sup>
						</span>
						<span id="resultInfosCountry" class="result aligned"></span>
				</div>
				<div class="field">
						<label for="infosTimezone" class="aligned"><?php echo lang('Shop timezone:'); ?></label>
						<span class="contentinput">
							<select name="infosTimezone" id="infosTimezone">
								<option disabled="disabled"><?php echo lang('-- Select your timezone --'); ?></option>
							</select> <sup class="required">*</sup>
						</span>
						<span id="resultInfosTimezone" class="result aligned"></span>
				</div>
				<div class="field">
						<label for="infosLogo" class="aligned logo"><?php echo lang('Shop logo:'); ?></label>
						<span class="contentinput">
							<p id="alignedLogo"><img id="uploadedImage" src="<?php echo PS_BASE_URI ?>img/logo.jpg" alt="Logo" /></p>
						</span>
						<p class="userInfos aligned"><?php echo lang('Recommended dimensions:') ?><br />230px x 75px</p>

						<span id="inputFileLogo" class="contentinput">
					<input type="file" onchange="uploadLogo()" name="fileToUpload" id="fileToUpload"/>
						</span>
					<span id="resultInfosLogo" class="result"></span>
				</div>
				<div class="field">
						<label for="catalogMode" class="aligned"><?php echo lang('Catalog mode only:'); ?></label>
						<span class="contentinput">
					<input type="radio" name="catalogMode" id="catalogMode_1" value="1" />
							<label for="catalogMode_1" class="radiolabel"><?php echo lang('Yes'); ?></label>&nbsp; &nbsp;
					<input type="radio" name="catalogMode" id="catalogMode_0" value="0" checked="checked"/>
							<label for="catalogMode_0" class="radiolabel"><?php echo lang('No'); ?></label>
						</span>
						<p class="userInfos aligned"><?php echo lang('If you activate this feature, all purchasing will be disabled. However, you will be able to enable purchasing later in your Back Office.'); ?></p>
				</div>

				<div class="field">
						<label for="infosFirstname" class="aligned"><?php echo lang('First name:'); ?> </label>
						<span class="contentinput">
							<input class="text required" type="text" id="infosFirstname"/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosFirstname" class="result aligned"></span>
				</div>

				<div class="field">
						<label for="infosName" class="aligned"><?php echo lang('Last name:'); ?> </label>
						<span class="contentinput">
							<input class="text required" type="text" id="infosName"/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosName" class="result aligned"></span>
				</div>

				<div class="field">
						<label for="infosEmail" class="aligned"><?php echo lang('E-mail address:'); ?> </label>
						<span class="contentinput">
							<input type="text" class="text required" id="infosEmail"/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosEmail" class="result aligned"></span>
				</div>

				<div class="field">
						<label for="infosPassword" class="aligned"><?php echo lang('Shop password:'); ?> </label>
						<span class="contentinput">
							<input autocomplete="off" type="password" class="text required" id="infosPassword"/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosPassword" class="result aligned"></span>
				</div>
				<div class="field">
						<label class="aligned" for="infosPasswordRepeat"><?php echo lang('Re-type to confirm:'); ?> </label>
						<span class="contentinput">
							<input type="password" autocomplete="off" class="text required" id="infosPasswordRepeat"/> <sup class="required">*</sup>
						</span>
					<span id="resultInfosPasswordRepeat" class="result aligned"></span>
				</div>
					<div class="field" id="contentInfosNotification">
						<span class="contentinput">
							<input type="checkbox" id="infosNotification" class="aligned" style="vertical-align: middle;" /> <label for="infosNotification"><?php echo lang('Receive this information by e-mail'); ?></label><br/>
					<span id="resultInfosNotification" class="result aligned"></span>
						</span>
						<p class="userInfos aligned"><?php echo lang('Warning: If you check this box and your e-mail configuration is incorrect, you might not be able to continue the installation.'); ?></p>
				</div>
				</div>
				<div id="benefitsBlock" style="display: none;">
				<!-- Partner Modules -->
				<?php
					if (!isset($_GET['language']))
						$_GET['language'] = 0;
				?>
				<link href="../css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
				<script src="../js/jquery/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
					<script type="text/javascript">
					var moduleChecked = new Array();
					$(document).ready(function() {
						$('#infosCountry').change(function() {
							$(".installModuleList.selected").removeClass("selected");
							if ($("#modulesList" + $('select#infosCountry option:selected').attr('rel')))
								$("#modulesList" + $('select#infosCountry option:selected').attr('rel')).addClass("selected");
								$('#benefitsBlock').show();
								if ($('div .installModuleList:visible').length == 0)
									$('#benefitsBlock').hide();
								else
									$('#benefitsBlock').show();
							$.ajax({
								type: "GET",
								url: "./php/country_to_timezone.php?country="+$("select#infosCountry option:selected").attr('rel'),
								success: function(timezone){
									$("select#infosTimezone").val(timezone);
								}
							});

								autoCheckField("#infosCountry", "#resultInfosCountry", "required");
								autoCheckField("#infosTimezone", "#resultInfosTimezone", "required");
						});
					});
				</script>

				<?php

					if (!isset($_GET['language']))
						$_GET['language'] = 0;

					function getPreinstallXmlLang($object, $field)
					{
						if (property_exists($object, $field.'_'.((int)($_GET['language'])+1)))
							return str_replace(array('!|', '|!'), array('<', '>'), trim($object->{$field.'_'.((int)($_GET['language'])+1)}));
						if (property_exists($object, $field.'_1'))
							return str_replace(array('!|', '|!'), array('<', '>'), trim($object->{$field.'_1'}));
						return '';
					}

						$stream_context = @stream_context_create(array('http' => array('method'=> 'GET', 'timeout' => 3)));
						$content = @file_get_contents('http://www.prestashop.com/partner/preactivation/partners.php?version=1.0', false, $stream_context);
					if ($content && $content[0] == '<')
					{
						$result = simplexml_load_string($content);
						if ($result->partner)
						{
							$modulesHelpInstall = array();
							$modulesDescription = array();
							$modulesPrechecked = array();
								foreach ($result->partner AS $p)
							{
								$modulesDescription[trim($p->key)] = array('name' => trim($p->label), 'logo' => trim($p->logo_medium), 'label' => getPreinstallXmlLang($p, 'label'), 'description' => getPreinstallXmlLang($p, 'description'), 'more' => getPreinstallXmlLang($p, 'more'));
									foreach ($p->country AS $country_iso_code)
									$modulesHelpInstall[trim($country_iso_code)][] = trim($p->key);
								if ($p->prechecked)
										foreach ($p->prechecked AS $country_iso_code)
										$modulesPrechecked[trim($p->key)][trim($country_iso_code)] = 1;
							}

								if (sizeof($modulesHelpInstall))
								{
									echo '
									<h2>'.lang('Additional Benefits').'</h2>
									<h3>'.lang('Exclusive offers dedicated to PrestaShop merchants').'</h3>
									<table cellpadding="0" callspacing="0" border="0" class="moduleTable">
									<tr>
										<th style="width: 30px;"></th>
											<th style="width: 100px;">'.lang('Modules').'</th>
											<th style="padding: 12px; width: 430px;">'.lang('Benefits').'</th>
										</tr>
									</table>';

									$country_iso_code_default = '';
									if ($_GET['language'] == 1) $country_iso_code_default = 'FR';
									else if ($_GET['language'] == 2) $country_iso_code_default = 'ES';
									else if ($_GET['language'] == 3) $country_iso_code_default = 'DE';
									else if ($_GET['language'] == 4) $country_iso_code_default = 'IT';

									foreach ($modulesHelpInstall AS $country_iso_code => $modulesList)
							{
										echo '<div class="installModuleList'.($country_iso_code == $country_iso_code_default ? ' selected' : '').'" id="modulesList'.$country_iso_code.'">';
										foreach ($modulesList AS $module)
								{
									echo '
										<table cellpadding="0" callspacing="0" border="0" class="moduleTable">
											<tr style="border-bottom:0px">
										<td valign="top" style="text-align: center; padding-top:10px; width: 30px;">
										<span style="padding: 12px 4px 6px 2px;">
									<input type="checkbox" id="preInstallModules_'.$country_iso_code.'_'.$module.'" value="'.$module.'" class="'.$module.' preInstallModules_'.$country_iso_code.'" style="vertical-align: middle;" />
									</span>
									</td>
										<td valign="top" style="width: 100px; text-align: center;"><img src="'.$modulesDescription[$module]['logo'].'" alt="'.$modulesDescription[$module]['name'].'" title="'.$modulesDescription[$module]['name'].'">'.(isset($modulesDescription[$module]['more']) ? $modulesDescription[$module]['more'] : '').'</td>
										<td style="padding: 15px; width: 430px;">
										'.$modulesDescription[$module]['description'].'
										</td>
									</tr>
										<tr><td colspan="3"><div id="divForm_'.$country_iso_code.'_'.$module.'">&nbsp;</div></td></tr></table>
										';
									echo "<script>
										moduleChecked['".$country_iso_code.'_'.$module."'] = 0;
										$(document).ready(function() {
											$('#preInstallModules_".$country_iso_code.'_'.$module."').change(function() {
												var idDivForm = '#divForm_".$country_iso_code."_".$module."';
												if ($(this).attr('checked'))
												{
													moduleChecked['".$country_iso_code.'_'.$module."'] = 1;
													$(idDivForm).css({'display' : 'block'});
													$.ajax({
													  url: 'preactivation.php?request=form&partner=".$module."&language=".$_GET['language']."'+
														'&language_iso_code='+isoCodeLocalLanguage+
														'&country_iso_code=".$country_iso_code."'+
														'&activity='+ encodeURIComponent($('select#infosActivity').val())+
														'&timezone='+ encodeURIComponent($('select#infosTimezone').val())+
														'&shop='+ encodeURIComponent($('input#infosShop').val())+
														'&firstName='+ encodeURIComponent($('input#infosFirstname').val())+
														'&lastName='+ encodeURIComponent($('input#infosName').val())+
														'&email='+ encodeURIComponent($('input#infosEmail').val()),
													  	success: function(data) {
													    		$(idDivForm).html(data);
													  	},
													  	error: function() {
													  		errorOccured = true;
													  	}
													});
												}
												else
												{
													moduleChecked['".$country_iso_code.'_'.$module."'] = 0;
													$(idDivForm).css({'display' : 'none'});
													$(idDivForm).html('');
												}
											});
										});
										";
										if (isset($modulesPrechecked[$module][$country_iso_code]) && $modulesPrechecked[$module][$country_iso_code] == 1)
										{
											echo "$(document).ready(function() {
												moduleChecked['".$country_iso_code.'_'.$module."'] = 1;
												$('#preInstallModules_".$country_iso_code."_".$module."').attr('checked', true);
												$('#divForm_".$country_iso_code."_".$module."').css({'display' : 'block'});
												$.ajax({
												  url: 'preactivation.php?request=form&partner=".$module."&language=".$_GET['language']."'+
													'&language_iso_code='+isoCodeLocalLanguage+
													'&country_iso_code=".$country_iso_code."'+
													'&activity='+ encodeURIComponent($('select#infosActivity').val())+
													'&timezone='+ encodeURIComponent($('select#infosTimezone').val())+
													'&shop='+ encodeURIComponent($('input#infosShop').val())+
													'&firstName='+ encodeURIComponent($('input#infosFirstname').val())+
													'&lastName='+ encodeURIComponent($('input#infosName').val())+
													'&email='+ encodeURIComponent($('input#infosEmail').val()),
												  	success: function(data) {
												    		$('#divForm_".$country_iso_code."_".$module."').html(data);
												  	},
												  	error: function() {
												  		errorOccured = true;
												  	}
												});
											});";
										}
										echo "</script>";
								}
								echo '</div>';
							}
						}
					}
						}

				?>
				<!-- Partner Modules -->

				<!--<h3><?php echo lang('Shop\'s languages'); ?></h3>
				<p class="userInfos"><?php echo lang('Select the different languages available for your shop'); ?></p>-->
				<div id="availablesLanguages" style=" float:left; text-align: center; display:none;">

					<?php echo lang('Optional languages'); ?><br/>
					<select style="width:300px;" id="aLList" multiple="multiple" size="4">
						<?php foreach ($lm->getAvailableLangs() AS $lang){
						if ( $lang['id'] != $lm->getIdSelectedLang() AND $lang['id']  != "0" ){ ?>
							<option value="<?php echo $lang->idLangPS ?>"><?php echo $lang['label'] ?></option>
					<?php }} ?>
					</select>
				</div>

				<div id="RightLeft" style="float: left; width:50px; margin-top: 1.7em; text-align:center; display:none;">
					<input id="al2wl" value="&gt;" type="button"/><br/>
					<input id="wl2al" value="&lt;" type="button" />
				</div>

				<div id="websitesLanguages" style="float:left; text-align: center; display:none;">
					<?php echo lang('Available shop languages'); ?><br/>
					<select style="width:240px;" id="wLList" size="4">
						<option value="en">English (English)</option>
							<?php foreach ($lm->getAvailableLangs() AS $lang){
							if ( $lang['id'] == $lm->getIdSelectedLang() AND $lang['id']  != "0" ){ ?>
								<option value="<?php echo $lang->idLangPS ?>"><?php echo $lang['label'] ?></option>
						<?php }} ?>

					</select><br/>
					<label for="dLList"><?php echo lang('Shop\'s default language'); ?></label><br/>
					<select style="width:180px;" id="dLList">
						<option selected="selected" value="en">English (English)</option>
							<?php foreach ($lm->getAvailableLangs() AS $lang){
							if ( $lang['id'] == $lm->getIdSelectedLang() AND $lang['id']  != "0" ){ ?>
								<option selected="selected" value="<?php echo $lang->idLangPS ?>"><?php echo $lang['label'] ?></option>
						<?php }} ?>
					</select>
				</div>

				</div>
			</form>

			<div id="resultEnd">
				<span id="resultInfosSQL" class="result"></span>
				<span id="resultInfosLanguages" class="result"></span>
			</div>
		</div>

		<div class="sheet clearfix" id="sheet_end">

			<div class="contentTitle">
				<h1><?php echo lang('PrestaShop is ready!'); ?></h1>

				<ul id="stepList_5" class="stepList clearfix">
					<li class="ok">Etape 1 ok</li>
					<li class="ok">Etape 2 ok</li>
					<li class="ok">Etape 3 ok</li>
					<li class="ok">Etape 4 ok</li>
					<li class="ok">Etape 5</li>
				</ul>
		</div>

			<div class="clearfix">
				<h2><?php echo lang('Your installation is finished!'); ?></h2>
				<p><?php echo lang('You have just installed and configured PrestaShop as your online shop. We wish you all the best with the success of your online shop.'); ?></p>
				<p><?php echo lang('Here is your shop information. You can modify it once you are logged in.'); ?></p>
				<table cellpadding="0" cellspacing="0" border="0" id="resultInstall" width="620">
					<tr class="odd">
						<td width="220" class="label"><?php echo lang('Shop name:'); ?></td>
						<td width="400" id="endShopName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr>
						<td class="label"><?php echo lang('First name:'); ?></td>
						<td id="endFirstName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr class="odd">
						<td class="label"><?php echo lang('Last name:'); ?></td>
						<td id="endName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr>
						<td class="label"><?php echo lang('E-mail:'); ?></td>
						<td id="endEmail" class="resultEnd">&nbsp;</td>
					</tr>
				</table>

				<h3 class="infosBlock"><?php echo lang('WARNING: For security purposes, you must delete the "install" folder.'); ?></h3>

				<div id="boBlock" class="blockInfoEnd clearfix">
						<img src="img/visu_boBlock.png" />
						<h3><?php echo lang('Back Office'); ?></h3>
						<p class="description"><?php echo lang('Manage your store using your Back Office. Manage your orders and customers, add modules, change themes, etc.'); ?></p>
						<a href="../admin" id="access" class="BO" target="_blank"><span><?php echo lang('Manage your store'); ?></span></a>
				</div>
				<div id="foBlock" class="blockInfoEnd last clearfix">
						<img src="img/visu_foBlock.png" />
						<h3><?php echo lang('Front Office'); ?></h3>
						<p class="description"><?php echo lang('Discover your store as your future customers will see it!'); ?></p>
						<a href="../" id="access" class="FO" target="_blank"><span><?php echo lang('Discover your store'); ?></span></a>
				</div>

				<div id="resultEnd"></div>
			</div>
			<?php
			if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)): ?>
			<iframe src="http://addons.prestashop.com/psinstall.php?lang=<?php echo $lm->getIsoCodeSelectedLang()?>" scrolling="no" id="prestastore">
				<p>Your browser does not support iframes.</p>
			</iframe>
			<?php
			endif; ?>

		</div>

		<div class="sheet clearfix" id="sheet_disclaimer">
			<div class="contentTitle">
				<h1><?php echo lang('Disclaimer'); ?></h1>

				<ul id="stepList_6" class="stepList clearfix">
					<li class="ok">Etape 1</li>
					<li>Etape 2</li>
					<li>Etape 3</li>
					<li>Etape 4</li>
				</ul>
			</div>
			<h2><?php echo lang('Warning: a manual backup is HIGHLY recommended before continuing!'); ?></h2>
			<p><?php echo lang('Please backup the database and application files.'); ?></p>
			<p><?php echo lang('When your backup is complete, please confirm that it is fully functional.'); ?><br /><br /></p>

			<div id="versionWarning" style="font-weight: bold; background-color: #ffdeb7; color: #000; padding: 10px; border: 1px solid #999; margin-top: 10px; margin-bottom: 10px; display: none">
				<p><img src="../img/admin/warning.gif" alt="" style="vertical-align: middle;" /> <span></span></p>
			</div>

			<div id="disclaimerDivCertify">
				<input id="btDisclaimerOk" type="checkbox" value="1" style="vertical-align: middle; width: 16px; height: 16px;" />
				<label for="btDisclaimerOk" style="font-weight: bold; color: #CC0000;"><?php echo lang('I certify that I backed up my database and application files. I assume all responsibility for any data loss or damage related to this upgrade.'); ?></label>
			</div>

			<div id="upgradeProcess" style="display: none;width: 650px;">
				<?php
				if (file_exists(dirname(__FILE__).'/../config/settings.inc.php')) :
				?>
				<script type="text/javascript">
					$(document).ready(function() {
						$.ajax({
							url: 'xml/getNonNativeModules.php',
							async: true,
							dataType: "json",
							success: function (json)
							{
								if (json.length == 0)
									$('#nonNativeModules').hide();
								else
								{
									$(json).each( function () {
										$('ul#nonNativeModulesLi').append('<li>'+this.name+'</li>');
									});
								}
							},
							error: function ()
							{
								errorOccured = true;
								$('#nonNativeModules').hide();
							}
						});
					});
				</script>

				<div id="nonNativeModules" style="font-weight: bold; background-color: #ffdeb7; color: #000; padding: 10px; border: 1px solid #999; margin-top: 10px;">
					<p><img src="../img/admin/warning.gif" alt="" style="vertical-align: middle;" /> <?php echo lang('It\'s dangerous to keep non-native modules activated during the update. If you really want to take this risk, uncheck the following box.'); ?></p>

					<p><?php echo lang('You will be able to manually reactivate them in your Back Office once the update process has succeeded.'); ?></p>
					<input id="customModuleDesactivation" type="checkbox" checked="checked" value="1" name="customModuleDesactivation" />
					<label for="customModuleDesactivation">
						<?php echo lang('Ok, please deactivate the following modules, I will reactivate them later:'); ?>
					</label>
					<ul id="nonNativeModulesLi">

					</ul>
				</div>
				<?php

				endif;

				function sortnatversion($a, $b)
				{
					return strnatcmp($a['version'], $b['version']);
				}

				echo '<h2>'.lang('Theme compatibility').'</h2>';
				echo '<p>'.lang('Before updating, you need to check that your theme is compatible with version').' <b>'.INSTALL_VERSION.'</b> '.lang('of PrestaShop.').'</p>
	<p><b>'.lang('To do this, use our').'</b> <a target="_blank" href="http://validator.prestashop.com?version='.INSTALL_VERSION.'" title="'.lang('Link to the validator').'"><b>'.lang('Online Theme Validator').'</b></a>.'.'</p>';
	echo '<p>'.lang('If your theme is not valid, you may experience some problems in your front-office aspect, but don\'t panic ! To solve this, you can make it compatible by correcting the validators errors or by using a theme compatible with ').' '.INSTALL_VERSION.' '.lang('version').'.</p>';

				echo '<h2>'.lang('Let\'s go!').'</h2>
				<p>'.lang('Click on the "Next" button to start the upgrade, this can take several minutes,').' <u style="font-weight: bold; text-decoration: underline;">'.lang('please be patient and do not close this window.').'</u></p>';
				echo '<h2>'.lang('Details about this upgrade').' (v'.INSTALL_VERSION.')</h2>
				<p>'.
				lang('Thank you, you will be able to continue the upgrade process by clicking on the "Next" button.').'<br /><br />'.
				lang('PrestaShop is upgrading your shop one version after the other, the following upgrade files will be processed:').'
				</p>';

				$upgradeFiles = array();
				$upgradePath = INSTALL_PATH.'/sql/upgrade';
				$majorReleases = 0;
				if ($handle = opendir($upgradePath))
				{
					while (false !== ($file = readdir($handle)))
						if (!preg_match('/^\..*/Ui', $file))
						{
							$version = str_replace('.sql', '', $file);
							if (version_compare($version, $oldversion) == 1 AND version_compare(INSTALL_VERSION, $version) != -1)
							{
								$major = false;
								if (in_array($version, array('0.9.7.2', '1.0.0.8', '1.1.0.5', '1.2.5.0', '1.3.0.10', '1.4.0.17')))
								{
									$majorReleases++;
									$major = true;
								}

								$upgradeFiles[] = array('instructions' => (int)substr_count(file_get_contents($upgradePath.'/'.$file), ';'),
								'version' => str_replace('.sql', '', $file), 'is_major' => $major);
							}
						}
					closedir($handle);

					if (sizeof($upgradeFiles))
					{
						echo '
						<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<th>'.lang('Upgrade file').'</th>
								<th  style="text-align: right;">'.lang('Modifications to process').'</th>
							</tr>';

						uasort($upgradeFiles, 'sortnatversion');
						$totalInstructions = 0;
						foreach ($upgradeFiles AS $file)
						{
							echo '<tr><td style="'.($file['is_major'] ? 'font-weight: bold;' : '').'">v'.$file['version'].($file['is_major'] ? ' '.lang('(major)') : '').'</td><td style="text-align: right;">'.(int)$file['instructions'].'</td></tr>';
							$totalInstructions += (int)$file['instructions'];
						}
						echo '<tr style="font-weight: bold;"><td>'.lang('TOTAL').'</td><td style="text-align: right;">'.(int)$totalInstructions.'</td></tr>';
						echo '
						</table>';

						$upgradeTime = $totalInstructions * 0.05;
						$minutes = (int)($upgradeTime / 60);
						$seconds = (int)($upgradeTime - ($minutes * 60));

						echo '<p><img src="../img/admin/time.gif" alt="" style="vertical-align: absmiddle;" /> '.lang('Estimated time to complete the').' '.(int)$totalInstructions.' '.lang('modifications:').' <b style="font-size: 14px;">'.(int)$minutes.' '.($minutes > 1 ? lang('minutes') : lang('minute')).' '.(int)$seconds.' '.($seconds > 1 ? lang('seconds') : lang('second')).'</b><br />
						<i style="font-size: 11px;">'.lang('Depending on your server and the size of your shop').'</i></p>';

						if ($majorReleases > 1)
							echo '<p style="margin-top: 8px;"><b>'.lang('You have not updated your shop in a while,').' '.(int)$majorReleases.' '.lang('stable releases have been made ​​available since.').'</b> '.lang('This is not a problem however the update may take several minutes, try to update your shop more frequently.').'</p>';
					}
					else
						echo '<p>'.lang('No files to process, this might be an error.').'</p>';
				}

				$maxMemory = @ini_get('memory_limit');
				$maxTime = @ini_get('max_execution_time');
				$color = '#D9F2D0';
				$textColor = 'green';

				if (str_replace('M', '', $maxMemory) < 16)
					$color = '#FFDEB7';
				if (str_replace('M', '', $maxMemory) < 8)
					$color = '#FAE2E3';
				if ($maxTime AND $maxTime <= 30)
					$color = '#FFDEB7';
				if ($maxTime AND $maxTime <= 20)
					$color = '#FAE2E3';

				echo '
				<br />
				<h2>'.lang('Hosting parameters').'</h2>
				<p>'.lang('PrestaShop tries to automatically set the best settings for your server in order for the update to be successful.').'</p>
				<table cellpadding="0" cellspacing="0" border="0">
					<tr>
						<th>'.lang('PHP parameter').'</th>
						<th>'.lang('Description').'</th>
						<th>'.lang('Current value').'</th>
					</tr>
					<tr>
						<td>max_execution_time</td>
						<td>'.lang('Maximum allowed time for the upgrade').'</td>
						<td style="text-align: right;">'.ini_get('max_execution_time').' '.lang('seconds').'</td>
					</tr>
					<tr>
						<td>memory_limit</td>
						<td>'.lang('Maximum memory allowed for the upgrade').'</td>
						<td style="text-align: right;">'.ini_get('memory_limit').'</td>
					</tr>
				</table>
				<div class="infosBlock">';

				if ($color == '#D9F2D0')
					echo '<img src="../img/admin/ok.gif" alt="" style="vertical-align: absmiddle;" /> '.lang('All your settings seem to be OK, go for it!');
				elseif ($color == '#FFDEB7')
					echo '<img src="../img/admin/warning.gif" alt="" style="vertical-align: absmiddle;" /> '.lang('Beware, your settings look correct but are not optimal, if you encounter problems (upgrade too long, memory error...), please ask your hosting provider to increase the values of these parameters: "max_execution_time" and "memory_limit".');
				elseif ($color == '#FAE2E3')
					echo '<img src="../img/admin/error2.png" alt="" style="vertical-align: absmiddle;" /> '.lang('We strongly recommend that you inform your hosting provider to modify the settings before process to the update.');
				echo '</div>';

				?>
			</div>
		</div>

		<div class="sheet clearfix" id="sheet_require_update">
			<div class="contentTitle">
				<h1><?php echo lang('System Compatibility')?></h1>

				<ul id="stepList_7" class="stepList clearfix">
					<li class="ok">Etape 1 ok</li>
					<li class="ok">Etape 2 ok</li>
					<li>Etape 3</li>
					<li>Etape 4</li>
				</ul>
			</div>
			<h2><?php echo lang('Required set-up. Please verify the following checklist items:'); ?></h2>

			<p>
				<?php echo lang('If you have any questions, please visit our '); ?>
				<a href="http://www.prestashop.com/wiki" target="_blank"><?php echo lang('Documentation Wiki'); ?></a>
				<?php echo lang('and/or'); ?>
				<a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Community Forum'); ?></a><?php echo lang('.'); ?>
			</p>

			<h3 id="resultConfig_update"></h3>
			<ul id="required_update">
				<li class="title"><?php echo lang('PHP parameters:')?></li>
				<li class="required first"><?php echo lang('PHP 5.0 or later installed')?></li>
				<li class="required"><?php echo lang('File upload allowed')?></li>
				<li class="required"><?php echo lang('Create new files and folders allowed')?></li>
				<li class="required"><?php echo lang('GD Library installed')?></li>
				<li class="required"><?php echo lang('MySQL support is on')?></li>
				<li class="title"><?php echo lang('Write permissions on folders:')?></li>
				<li class="required first">/config</li>
				<li class="required">/cache</li>
				<li class="required">/sitemap.xml</li>
				<li class="required">/log</li>
				<li class="title"><?php echo lang('Write permissions on folders (and subfolders):')?></li>
				<li class="required first">/img</li>
				<li class="required">/mails</li>
				<li class="required">/modules</li>
				<li class="required">/themes/prestashop/lang</li>
				<li class="required">/themes/prestashop/cache</li>
				<li class="required">/translations</li>
				<li class="required">/upload</li>
				<li class="required">/download</li>
			</ul>

			<h3><?php echo lang('Optional set-up')?></h3>
			<ul id="optional_update">
				<li class="title"><?php echo lang('PHP parameters:')?></li>
				<li class="optional"><?php echo lang('Open external URLs allowed')?></li>
				<li class="optional"><?php echo lang('PHP register global option is off (recommended)')?></li>
				<li class="optional"><?php echo lang('GZIP compression is on (recommended)')?></li>
				<li class="optional"><?php echo lang('Mcrypt is available (recommended)')?></li>
				<li class="optional"><?php echo lang('PHP magic quotes option is off (recommended)')?></li>
				<li class="optional"><?php echo lang('Dom extension loaded')?></li>
			</ul>

			<p><input class="button" value="<?php echo lang('Check my settings again'); ?>" type="button" id="req_bt_refresh_update"/></p>

		</div>

		<div class="sheet clearfix" id="sheet_updateErrors">
			<div class="contentTitle">
				<h1><?php echo lang('Error!'); ?></h1>

				<ul id="stepList_8" class="stepList clearfix">
					<li class="ok">Etape 1 ok</li>
					<li class="ok">Etape 2 ok</li>
					<li class="ko">Etape 3</li>
					<li>Etape 4</li>
				</ul>
			</div>

			<h3><?php echo lang('One or more errors have occurred, you can find more information below or in the log/installation.log file.'); ?></h3>

			<p id="resultUpdate" class="errorBlock"></p>
			<br />
			<p id="detailsError" class="infosBlock"><?php echo lang('No more information'); ?></p>
		</div>

		<div class="sheet clearfix" id="sheet_end_update">
			<div>
				<div class="contentTitle">
					<h1><?php echo lang('Your update is complete!'); ?></h1>

					<ul id="stepList_7" class="stepList clearfix">
						<li class="ok">Etape 1 ok</li>
						<li class="ok">Etape 2 ok</li>
						<li class="ok">Etape 3</li>
						<li class="ok">Etape 4</li>
					</ul>
				</div>
				<div class="okBlock">
					<?php echo lang('Your shop version is now').' '.INSTALL_VERSION; ?>
				</div>
				<p class="errorBlock" id="txtErrorUpdateSQL" style="display:none;"></p>
				<p style="padding-bottom: 5px;"><a href="javascript:showUpdateLog()"><?php echo lang('view the log'); ?></a></p>
				<div id="updateLog"></div>
				<p><?php echo lang('You have just updated and configured PrestaShop as your online shop. We wish you all the best with the success of your online shop.'); ?></p>

				<?php

					if (@fsockopen('www.prestashop.com', 80, $errno, $errst, 3))
					{
						echo '
						<h2>'.lang('New features in PrestaShop v').INSTALL_VERSION.'</h2>
						<iframe style="width: 638px; margin-top: 5px; padding: 5px; border: 1px solid #BBB;" src="http://features.prestashop.com/lang/'.$lm->getIsoCodeSelectedLang().'/version/'.INSTALL_VERSION.'">
							<p>Your browser does not support iframes.</p>
						</iframe>';
					}

				?>

				<div class="infosBlock">
					<?php echo lang('WARNING: For security purposes, you must delete the "install" folder.'); ?>
				</div>

				<div id="foBlock" class="blockInfoEnd clearfix">
						<img src="img/visu_foBlock.png" />
						<h3><?php echo lang('Front Office'); ?></h3>
						<p class="description"><?php echo lang('Discover your store as your future customers will see it!'); ?></p>
						<a href="../" id="access" class="FO" target="_blank"><span><?php echo lang('Discover your store'); ?></span></a>
				</div>
			</div>
			<?php
			if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)): ?>
			<iframe src="http://addons.prestashop.com/psinstall.php" scrolling="no" id="prestastore_update">
				<p>Your browser does not support iframes.</p>
			</iframe>
			<?php
			endif;
			?>
		</div>

</div>

<div id="buttons">
	<input id="btBack" class="button little disabled" type="button" value="<?php echo lang('Back'); ?>" disabled="disabled"/>
	<input id="btNext" class="button little" type="button" value="<?php echo lang('Next'); ?>" />
</div>

</div>
<ul id="footer">
	<li><a href="http://www.prestashop.com/forums/" title="<?php echo lang('PrestaShop Forums'); ?>" target="_blank"><?php echo lang('PrestaShop Forums'); ?></a> | </li>
	<li><a href="http://www.prestashop.com" title="PrestaShop.com" target="_blank">PrestaShop.com</a> | </li>
	<li><a href="http://www.prestashop.com/contact.php" title="<?php echo lang('Contact us!'); ?>" target="_blank"><?php echo lang('Contact us!'); ?></a> | </li>
	<li>&copy; 2007-<?php echo date('Y'); ?></li>
</ul>
</body>
</html>


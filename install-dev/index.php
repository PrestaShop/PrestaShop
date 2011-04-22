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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date dans le passé

if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('Europe/Paris');

/* Redefine REQUEST_URI if empty (on some webservers...) */
$_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);
if (!isset($_SERVER['REQUEST_URI']) || $_SERVER['REQUEST_URI'] == '')
	$_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
if ($tmp = strpos($_SERVER['REQUEST_URI'], '?'))
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 0, $tmp);

define('INSTALL_VERSION', '1.4.1.0');
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

require(dirname(__FILE__).'/../config/autoload.php');
include_once(INSTALL_PATH.'/classes/ToolsInstall.php');

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
	include(INSTALL_PATH.'/../config/settings.inc.php');
	$oldversion =_PS_VERSION_;
	$tooOld = (version_compare($oldversion, MINIMUM_VERSION_TO_UPDATE) == -1);
	$sameVersions = (version_compare($oldversion, INSTALL_VERSION) == 0);
	$installOfOldVersion = (version_compare($oldversion, INSTALL_VERSION) == 1);
}

include(INSTALL_PATH.'/classes/LanguagesManager.php');
$lm = new LanguageManager(dirname(__FILE__).'/langs/list.xml');
$_LANG = array();
$_LIST_WORDS = array();
function lang($txt) {
	global $_LANG , $_LIST_WORDS;
	return (isset($_LANG[$txt]) ? $_LANG[$txt] : $txt);
}
if ($lm->getIncludeTradFilename())
	include_once($lm->getIncludeTradFilename());

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Cache" content="no store" />
	<meta http-equiv="Expires" content="-1" />
	<title><?php echo lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?></title>
	<link rel="stylesheet" type="text/css" media="all" href="view.css"/>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/jquery-1.4.4.min.js"></script>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/ajaxfileupload.js"></script>
	<script type="text/javascript" src="<?php echo PS_BASE_URI ?>js/jquery/jquery.pngFix.pack.js"></script>
	<link rel="shortcut icon" href="<?php echo PS_BASE_URI ?>img/favicon.ico" />

	<script type="text/javascript">
		//php to js vars
		var isoCodeLocalLanguage = "<?php echo $lm->getIsoCodeSelectedLang(); ?>";
		var ps_base_uri = "<?php echo PS_BASE_URI; ?>";
		var id_lang = "<?php echo (isset($_GET['language']) ? (int)($_GET['language']) : 0); ?>";

		//localWords
		var Step1Title = "<?php echo lang('Welcome').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step2title = "<?php echo lang('System Compatibility').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step3title = "<?php echo lang('System Configuration').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step4title = "<?php echo lang('Shop Configuration').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step5title = "<?php echo lang('Ready, set, go!').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step6title = "<?php echo lang('Disclaimer').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step7title = "<?php echo lang('System Compatibility').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step8title = "<?php echo lang('Errors while updating...').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
		var step9title = "<?php echo lang('Ready, set, go!').' - '.lang('PrestaShop '.INSTALL_VERSION.' Installer'); ?>";
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
		var mailSended = "<?php echo lang('An email has been sent!'); ?>";
		var mailSubject = "<?php echo lang('Congratulation, your online shop is now ready!'); ?>";
		var txtTabUpdater1 = "<?php echo lang('Welcome'); ?>";
		var txtTabUpdater2 = "<?php echo lang('Disclaimer'); ?>";
		var txtTabUpdater3 = "<?php echo lang('Verify system compatibility'); ?>";
		var txtTabUpdater4 = "<?php echo lang('Update is complete!'); ?>";
		var txtTabInstaller1 = "<?php echo lang('Welcome'); ?>";
		var txtTabInstaller2 = "<?php echo lang('Verify system compatibility'); ?>";
		var txtTabInstaller3 = "<?php echo lang('System Configuration'); ?>";
		var txtTabInstaller4 = "<?php echo lang('Shop Configuration'); ?>";
		var txtTabInstaller5 = "<?php echo lang('Installation is complete!'); ?>";
		var txtConfigIsOk = "<?php echo lang('Your configuration is valid, click next to continue!'); ?>";
		var txtConfigIsNotOk = "<?php echo lang('Your configuration is invalid. Please fix the issues below:'); ?>";

		var txtError = new Array();
		txtError[0] = "<?php echo lang('Required field'); ?>";
		txtError[1] = "<?php echo lang('Too long!'); ?>";
		txtError[2] = "<?php echo lang('Fields are different!'); ?>";
		txtError[3] = "<?php echo lang('This email adress is wrong!'); ?>";
		txtError[4] = "<?php echo lang('Impossible to send the email!'); ?>";
		txtError[5] = "<?php echo lang('Can\'t create settings file, if /config/settings.inc.php exists, please give the public write permissions to this file, else please create a file named settings.inc.php in config directory.'); ?>";
		txtError[6] = "<?php echo lang('Can\'t write settings file, please create a file named settings.inc.php in config directory.'); ?>";
		txtError[7] = "<?php echo lang('Impossible to upload the file!'); ?>";
		txtError[8] = "<?php echo lang('Data integrity is not valided. Hack attempt?'); ?>";
		txtError[9] = "<?php echo lang('Impossible to read the content of a MySQL content file.'); ?>";
		txtError[10] = "<?php echo lang('Impossible the access the a MySQL content file.'); ?>";
		txtError[11] = "<?php echo lang('Error while inserting data in the database:'); ?>";
		txtError[12] = "<?php echo lang('The password is incorrect (alphanumeric string at least 8 characters).'); ?>";
		txtError[14] = "<?php echo lang('A Prestashop database already exists, please drop it or change the prefix.'); ?>";
		txtError[15] = "<?php echo lang('This is not a valid file name.'); ?>";
		txtError[16] = "<?php echo lang('This is not a valid image file.'); ?>";
		txtError[17] = "<?php echo lang('Error while creating the /config/settings.inc.php file.'); ?>";
		txtError[18] = "<?php echo lang('Error:'); ?>";
		txtError[19] = "<?php echo lang('This PrestaShop database already exists. Please revalidate your authentication informations to the database.'); ?>";
		txtError[22] = "<?php echo lang('An error occurred while resizing the picture.'); ?>";
		txtError[23] = "<?php echo lang('Database connection is available!'); ?>";
		txtError[24] = "<?php echo lang('Database Server is available but database is not found'); ?>";
		txtError[25] = "<?php echo lang('Database Server is not found. Please verify the login, password and server fields.'); ?>";
		txtError[26] = "<?php echo lang('An error occurred while sending email, please verify your parameters.'); ?>";
		txtError[37] = "<?php echo lang('Impossible to write the image /img/logo.jpg. If this image already exists, please delete it.'); ?>";
		txtError[38] = "<?php echo lang('The uploaded file exceeds the upload_max_filesize directive in php.ini'); ?>";
		txtError[39] = "<?php echo lang('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'); ?>";
		txtError[40] = "<?php echo lang('The uploaded file was only partially uploaded'); ?>";
		txtError[41] = "<?php echo lang('No file was uploaded.'); ?>";
		txtError[42] = "<?php echo lang('Missing a temporary folder'); ?>";
		txtError[43] = "<?php echo lang('Failed to write file to disk'); ?>";
		txtError[44] = "<?php echo lang('File upload stopped by extension'); ?>";
		txtError[45] = "<?php echo lang('Cannot convert your database\'s data to utf-8.'); ?>";
		txtError[46] = "<?php echo lang('Invalid shop name'); ?>";
		txtError[47] = "<?php echo lang('Your firstname contains some invalid characters'); ?>";
		txtError[48] = "<?php echo lang('Your lastname contains some invalid characters'); ?>";
		txtError[49] = "<?php echo lang('Your database server does not support the utf-8 charset.'); ?>";
		txtError[50] = "<?php echo lang('Your MySQL server doesn\'t support this engine, please use another one like MyISAM'); ?>";
		txtError[51] = "<?php echo lang('The file /img/logo.jpg is not writable, please CHMOD 755 this file or CHMOD 777'); ?>";
		txtError[52] = "<?php echo lang('Invalid catalog mode'); ?>";
		txtError[999] = "<?php echo lang('No error code available.'); ?>";
		//upgrader
		txtError[27] = "<?php echo lang('This installer is too old.'); ?>";
		txtError[28] = "<?php echo lang('You already have the '.INSTALL_VERSION.' version.'); ?>";
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

</head>
<body>

<div id="noJavaScript">
	<?php echo lang('This application need you to activate Javascript to correctly work.'); ?>
</div>

<div id="container">

<div id="loaderSpace">
	<div id="loader">&nbsp;</div>
</div>

<div id="leftpannel">
	<h1>
		<div id="PrestaShopLogo">&nbsp;</div>
		<div class="installerVersion" id="installerVersion-<?php echo $lm->getIsoCodeSelectedLang()?>">PrestaShop <?php echo INSTALL_VERSION.'<br />'.lang('Installer'); ?></div>
		<div class="updaterVersion" id="updaterVersion-<?php echo $lm->getIsoCodeSelectedLang()?>">PrestaShop <?php echo INSTALL_VERSION.'<br />'.lang('Updater'); ?></div>
	</h1>

	<ol id="tabs"><li>&nbsp;</li></ol>

	<div id="help">
		<img src="img/ico_help.gif" alt="help" class="ico_help" />

		<div class="content">
			<p class="title"><?php echo lang('Need help?'); ?></p>
			<p class="title_down"><?php echo lang('All tips and advice about PrestaShop'); ?></p>

			<ul>
				<li><img src="img/puce.gif" alt="" /> <a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Forum'); ?></a><br class="clear" /></li>
				<li><img src="img/puce.gif" alt="" /> <a href="http://www.prestashop.com/blog/"><?php echo lang('Blog'); ?></a><br class="clear" /></li>
			</ul>
		</div>
	</div>

	<?php if ((isset($_GET['language']) AND $_GET['language'] == 1) OR $lm->getIsoCodeSelectedLang() == 'fr'): ?>
	<p id="phone_block">
		<?php echo '<span>'.lang('A question about PrestaShop or issues during installation or upgrade? Call us!').'</span><br /><img src="img/phone.png" style="vertical-align: middle;" alt="" /> '.lang('+33 (0)1.40.18.30.04'); ?>
	</p>
	<?php endif; ?>
</div>


<div id="sheets">

	<div class="sheet shown" id="sheet_lang">
		<h2><?php echo lang('Welcome')?></h2>
		<h3><?php echo lang('Welcome to the PrestaShop '.INSTALL_VERSION.' Installer.')?><br /><?php echo lang('Please allow 5-15 minutes to complete the installation process.')?></h3>
		<p><?php echo lang('The PrestaShop Installer will do most of the work in just a few clicks.')?><br /><?php echo lang('However, you must know how to do the following manually:')?></p>
		<ul>
			<li><?php echo lang('Set permissions on folders & subfolders using Terminal or an FTP client')?></li>
			<li><?php echo lang('Access and configure PHP 5.0+ on your hosting server')?></li>
			<li><?php echo lang('Back up your database and all application files (update only)')?></li>
		</ul>
		<p>
			<?php echo lang('For more information, please consult our') ?> <a href="http://www.prestashop.com/wiki/Getting_Started/"><?php echo lang('online documentation') ?></a>.
		</p>

		<h3><?php echo lang('Choose the installer language:')?></h3>
		<form id="formSetInstallerLanguage" action="<?php $_SERVER['REQUEST_URI']; ?>" method="get">
			<ul id="langList" style="line-height: 20px;">
			<?php foreach ($lm->getAvailableLangs() as $lang): ?>
				<li><input onclick="setInstallerLanguage()" type="radio" value="<?php echo $lang['id'] ?>" <?php echo ( $lang['id'] == $lm->getIdSelectedLang() ) ? "checked=\"checked\"" : '' ?> id="lang_<?php echo $lang['id'] ?>" name="language" style="vertical-align: middle; margin-right: 0;" /><label for="lang_<?php echo $lang['id'] ?>">
				<?php foreach ($lang->flags->url as $url_flag): ?>
					<img src="<?php echo $url_flag ?>" alt="<?php echo $lang['label'] ?>" style="vertical-align: middle;" />
				<?php endforeach;  ?>
				<?php echo $lang['label'] ?></label></li>

			<?php endforeach; ?>
			</ul>
		</form>
		<h3 class="no-margin"><?php echo lang('Did you know?'); ?></h3>
		<p>
			<?php echo lang('Prestashop and community offers over 40 different languages for free download on'); ?> <a href="http://www.prestashop.com" target="_blank">http://www.prestashop.com</a>
		</p>

		<h3><?php echo lang('Installation method')?></h3>
		<form id="formSetMethod" action="<?php $_SERVER['REQUEST_URI']; ?>" method="post">
			<p><input <?php echo (!($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion)) ? 'checked="checked"' : '' ?> type="radio" value="install" name="typeInstall" id="typeInstallInstall"/><label for="typeInstallInstall"><?php echo lang('Installation : complete install of the PrestaShop Solution')?></label></p>
			<p <?php echo ($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion) ? '' : 'class="disabled"'; ?>><input <?php echo ($oldversion AND !$tooOld AND !$sameVersions AND !$installOfOldVersion) ? 'checked="checked"' : 'disabled="disabled"'; ?> type="radio" value="upgrade" name="typeInstall" id="typeInstallUpgrade"/><label <?php echo ($oldversion === false) ? 'class="disabled"' : ''; ?> for="typeInstallUpgrade"><?php echo lang('Upgrade: get the latest stable version!')?> <?php echo ($oldversion === false) ? lang('(no old version detected)') : ("(".(  ($tooOld) ? lang('the already installed version detected is too old, no more update available') : ($installOfOldVersion ? lang('the already installed version detected is too recent, no update available') : lang('installed version detected').' : '.$oldversion    )).")") ?></label></p>
		</form>
		<h2><?php echo lang('License Agreement')?></h2>
		<div style="height:200px; border:1px solid #ccc; margin-bottom:8px; padding:5px; background:#fff; overflow: auto; overflow-x:hidden; overflow-y:scroll;">
			<h3>Open Software License ("OSL") v. 3.0</h3>
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
		</div>
		<p>
			<input type="checkbox" id="set_license" class="required" style="vertical-align: middle;" /><label for="set_license"><strong><?php echo lang('I agree to the above terms and conditions.'); ?></strong></label><br/>
		</p>
	</div>

		<div class="sheet" id="sheet_require">

			<h2><?php echo lang('System and permissions')?></h2>

			<h3><?php echo lang('Required set-up. Please verify the following checklist items are true.')?></h3>

			<p>
				<?php echo lang('If you have any questions, please visit our '); ?>
				<a href="http://www.prestashop.com/wiki/Getting_Started/ " target="_blank"><?php echo lang('Documentation Wiki'); ?></a>
				<?php echo lang('and/or'); ?>
				<a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Community Forum'); ?></a><?php echo lang('.'); ?>
			</p>

			<h3 id="resultConfig" style="font-size: 20px; text-align: center; padding: 0px; display: none;"></h3>
			<ul id="required">
				<li class="title"><?php echo lang('PHP parameters:')?></li>
				<li class="required"><?php echo lang('PHP 5.0 or later installed')?></li>
				<li class="required"><?php echo lang('File upload allowed')?></li>
				<li class="required"><?php echo lang('Create new files and folders allowed')?></li>
				<li class="required"><?php echo lang('GD Library installed')?></li>
				<li class="required"><?php echo lang('MySQL support is on')?></li>
				<li class="title"><?php echo lang('Write permissions on files and folders:')?></li>
				<li class="required">/config</li>
				<li class="required">/tools/smarty/compile</li>
				<li class="required">/tools/smarty/cache</li>
				<li class="required">/tools/smarty_v2/compile</li>
				<li class="required">/tools/smarty_v2/cache</li>
				<li class="required">/sitemap.xml</li>
				<li class="title"><?php echo lang('Write permissions on folders (and subfolders):')?></li>
				<li class="required">/img</li>
				<li class="required">/mails</li>
				<li class="required">/modules</li>
				<li class="required">/themes/prestashop/lang</li>
				<li class="required">/themes/prestashop/cache</li>
				<li class="required">/translations</li>
				<li class="required">/upload</li>
				<li class="required">/download</li>
			</ul>

			<h3><?php echo lang('Optional set-up')?></h3>
			<ul id="optional">
				<li class="title"><?php echo lang('PHP parameters:')?></li>
				<li class="optional"><?php echo lang('Open external URLs allowed')?></li>
				<li class="optional"><?php echo lang('PHP register global option is off (recommended)')?></li>
				<li class="optional"><?php echo lang('GZIP compression is on (recommended)')?></li>
				<li class="optional"><?php echo lang('Mcrypt is available (recommended)')?></li>
			</ul>
			<h3 style="display:none;" id="resultConfigHelper"><?php echo lang('If you do not know how to fix these issues, use turnkey solution PrestaBox at');?> <a href="http://www.prestabox.com">http://www.prestabox.com</a></h3>
			<p><input class="button" value="<?php echo lang('Refresh these settings')?>" type="button" id="req_bt_refresh"/></p>

		</div>

		<div class="sheet" id="sheet_db">
			<h2><?php echo lang('Database configuration')?></h2>

			<p><?php echo lang('Configure your database by filling out the following fields:')?></p>
			<form id="formCheckSQL" class="aligned" action="<?php $_SERVER['REQUEST_URI']; ?>" onsubmit="verifyDbAccess(); return false;" method="post">
				<h3 style="padding:0;margin:0;"><?php echo lang('You have to create a database, help available in readme_en.txt'); ?></h3>
				<p style="margin-top: 15px;">
					<label for="dbServer"><?php echo lang('Server:')?> </label>
					<input size="25" class="text" type="text" id="dbServer" value="localhost"/>
				</p>
				<p>
					<label for="dbName"><?php echo lang('Database name:')?> </label>
					<input size="10" class="text" type="text" id="dbName" value="prestashop"/>
				</p>
				<p>
					<label for="dbLogin"><?php echo lang('Login:')?> </label>
					<input class="text" size="10" type="text" id="dbLogin" value="root"/>
				</p>
				<p>
					<label for="dbPassword"><?php echo lang('Password:')?> </label>
					<input class="text" autocomplete="off" size="10" type="password" id="dbPassword"/>
				</p>
				<p>
					<label for="dbEngine"><?php echo lang('Database Engine:')?></label>
					<select id="dbEngine" name="dbEngine">
						<option value="InnoDB">InnoDB</option>
						<option value="MyISAM">MyISAM</option>
					</select>
				</p>
				<p class="aligned">
					<input id="btTestDB" class="button" type="submit" value="<?php echo lang('Verify now!')?>"/>
				</p>
				<p id="dbResultCheck"></p>
			</form>

			<div id="dbTableParam">
				<form action="#" method="post" onsubmit="createDB(); return false;">
				<p><label for="db_prefix"><?php echo lang('Tables prefix:')?> </label><input class="text" type="text" id="db_prefix" value="ps_"/></p>
				<h2><?php echo lang('Installation type')?></h2>
				<p id="dbModeSetter" style="line-height: 20px;">
					<input value="lite" type="radio" name="db_mode" id="db_mode_simple" style="vertical-align: middle;" /><label for="db_mode_simple"><?php echo lang('Simple mode: Basic installation')?> <span style="color: #CC0000; font-weight: bold;"><?php echo lang('(FREE)'); ?></span></label><br />
					<input value="full" type="radio" name="db_mode" checked="checked" id="db_mode_complet" style="vertical-align: middle;" /><label for="db_mode_complet"><?php echo lang('Full mode: includes').' <b>'.lang('100+ additional modules').'</b> '.lang('and demo products'); ?> <span style="color: #CC0000; font-weight: bold;"><?php echo lang('(FREE too!)'); ?></span></label>
				</p>
				</form>
				<p id="dbCreateResultCheck"></p>
			</div>
			<div id="mailPart">
				<h2><?php echo lang('E-mail delivery set-up')?></h2>

				<p>
					<input type="checkbox" id="set_stmp" style="vertical-align: middle;" /><label for="set_stmp"><?php echo lang('Configure SMTP manually (advanced users only)'); ?></label><br/>
					<span class="userInfos"><?php echo lang('By default, the PHP \'mail()\' function is used'); ?></span>
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
							<input type="text" size="5" id="smtpPort" value="25" />
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
				<p>
					<input class="text" id="testEmail" type="text" size="15" value="<?php echo lang('enter@your.email'); ?>"></input>
					<input id="btVerifyMail" class="button" type="submit" value="<?php echo lang('Send me a test email!'); ?>"></input>
				</p>

				<p id="mailResultCheck" class="userInfos"></p>
			</div>
		</div>

		<div class="sheet" id="sheet_infos">
			<form action="<?php $_SERVER['REQUEST_URI']; ?>" method="post" onsubmit="return false;" enctype="multipart/form-data">

				<h2><?php echo lang('Shop configuration'); ?></h2>

				<h3><?php echo lang('Merchant info'); ?></h3>
				<div class="field">
					<label for="infosShop" class="aligned"><?php echo lang('Shop name:'); ?> </label><input class="text required" type="text" id="infosShop" value=""/><br/>
					<span id="resultInfosShop" class="result aligned"></span>
				</div>
				<div class="field">
					<label for="infosActivity" class="aligned"><?php echo lang('Main activity:'); ?></label>
					<select id="infosActivity" style="border:1px solid #D41958">
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
						<option value="19"><?php echo lang('Sport and Entertainment'); ?></option>
						<option value="20"><?php echo lang('Travel'); ?></option>
						<option value="0"><?php echo lang('Other activity...'); ?></option>
					</select>
					<p class="userInfos aligned"><?php echo lang('This information isn\'t required, it will be used for statistical purposes. This information doesn\'t change anything in your store.'); ?></p>
				</div>
				<div class="field">
					<label for="infosCountry" class="aligned"><?php echo lang('Default country:'); ?></label>
					<select id="infosCountry" style="width:175px;border:1px solid #D41958">
					</select>
				</div>
				<div class="field">
					<label for="infosTimezone" class="aligned"><?php echo lang('Shop\'s timezone:'); ?></label>
					<select id="infosTimezone" style="width:175px;border:1px solid #D41958">
					</select>
				</div>
				<div class="field">
					<label for="infosLogo" class="aligned logo"><?php echo lang('Shop logo'); ?> : </label>
					<input type="file" onchange="uploadLogo()" name="fileToUpload" id="fileToUpload"/>
					<span id="resultInfosLogo" class="result"></span>
					<p class="userInfos aligned"><?php echo lang('recommended dimensions: 230px X 75px'); ?></p>
					<p id="alignedLogo"><img id="uploadedImage" src="<?php echo PS_BASE_URI ?>img/logo.jpg" alt="Logo" /></p>
				</div>
				<div class="field">
					<label for="catalogMode" class="aligned"><?php echo lang('Catalog mode:'); ?></label>
					<input type="radio" name="catalogMode" id="catalogMode_1" value="1" />
					<label for="catalogMode_1"><?php echo lang('Yes'); ?></label>
					<input type="radio" name="catalogMode" id="catalogMode_0" value="0" checked="checked"/>
					<label for="catalogMode_0"><?php echo lang('No'); ?></label>
					<p class="userInfos aligned"><?php echo lang('If you activate this feature, all purchase features will be disabled. You can activate this feature later in your back office'); ?></p>
				</div>

				<div class="field">
					<label for="infosFirstname" class="aligned"><?php echo lang('First name:'); ?> </label><input class="text required" type="text" id="infosFirstname"/><br/>
					<span id="resultInfosFirstname" class="result aligned"></span>
				</div>

				<div class="field">
					<label for="infosName" class="aligned"><?php echo lang('Last name:'); ?> </label><input class="text required" type="text" id="infosName"/><br/>
					<span id="resultInfosName" class="result aligned"></span>
				</div>

				<div class="field">
					<label for="infosEmail" class="aligned"><?php echo lang('E-mail address:'); ?> </label><input type="text" class="text required" id="infosEmail"/><br/>
					<span id="resultInfosEmail" class="result aligned"></span>
				</div>

				<div class="field">
					<label for="infosPassword" class="aligned"><?php echo lang('Shop password:'); ?> </label><input autocomplete="off" type="password" class="text required" id="infosPassword"/><br/>
					<span id="resultInfosPassword" class="result aligned"></span>
				</div>
				<div class="field">
					<label class="aligned" for="infosPasswordRepeat"><?php echo lang('Re-type to confirm:'); ?> </label><input type="password" autocomplete="off" class="text required" id="infosPasswordRepeat"/><br/>
					<span id="resultInfosPasswordRepeat" class="result aligned"></span>
				</div>

				<div class="field">
					<input type="checkbox" id="infosNotification" class="aligned" style="vertical-align: middle;" /><label for="infosNotification"><?php echo lang('Receive notifications by e-mail'); ?></label><br/>
					<span id="resultInfosNotification" class="result aligned"></span>
					<p class="userInfos aligned"><?php echo lang('If you check this box and your mail configuration is wrong, your installation might be blocked. If so, please uncheck the box to go to the next step.'); ?></p>
				</div>




				<!-- Partner Modules -->
				<?php
					if (!isset($_GET['language']))
						$_GET['language'] = 0;
				?>
				<link href="../css/jquery.fancybox-1.3.4.css" rel="stylesheet" type="text/css" media="screen" />
				<script src="../js/jquery/jquery.fancybox-1.3.4.js" type="text/javascript"></script>
				<style>
					.installModuleList { display: none; }
					.installModuleList.selected { display: block; }
				</style>
				<script>
					var moduleChecked = new Array();
					$(document).ready(function() {
						$('#infosCountry').change(function() {
							$(".installModuleList.selected").removeClass("selected");
							if ($("#modulesList" + $('select#infosCountry option:selected').attr('rel')))
								$("#modulesList" + $('select#infosCountry option:selected').attr('rel')).addClass("selected");
							$.ajax({
								type: "GET",
								url: "./php/country_to_timezone.php?country="+$("select#infosCountry option:selected").attr('rel'),
								success: function(timezone){
									$("select#infosTimezone").val(timezone);
								}
							});
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

					$context = stream_context_create(array('http' => array('method'=>"GET", 'timeout' => 3)));
					$content = @file_get_contents('https://www.prestashop.com/partner/preactivation/partners.php?version=1.0', false, $context);
					if ($content && $content[0] == '<')
					{
						$result = simplexml_load_string($content);
						if ($result->partner)
						{
							$modulesHelpInstall = array();
							$modulesDescription = array();
							$modulesPrechecked = array();
							foreach ($result->partner as $p)
							{
								$modulesDescription[trim($p->key)] = array('name' => trim($p->label), 'logo' => trim($p->logo), 'label' => getPreinstallXmlLang($p, 'label'), 'description' => getPreinstallXmlLang($p, 'description').getPreinstallXmlLang($p, 'more'));
								foreach ($p->country as $country_iso_code)
									$modulesHelpInstall[trim($country_iso_code)][] = trim($p->key);
								if ($p->prechecked)
									foreach ($p->prechecked as $country_iso_code)
										$modulesPrechecked[trim($p->key)][trim($country_iso_code)] = 1;
							}

							foreach ($modulesHelpInstall as $country_iso_code => $modulesList)
							{
								echo '<div class="installModuleList'.($country_iso_code == 'FR' ? ' selected' : '').'" id="modulesList'.$country_iso_code.'">';
								foreach ($modulesList as $module)
								{
									echo '<div class="field">
										<div style="float: left; height: 35px; width: 275px; padding-top: 6px;"><input type="checkbox" id="preInstallModules_'.$country_iso_code.'_'.$module.'" value="'.$module.'" class="aligned '.$module.' preInstallModules_'.$country_iso_code.'" style="vertical-align: middle;" /></div>
										<div style="float: left; height: 35px; width: 40px;"><img src="'.$modulesDescription[$module]['logo'].'" alt="'.$modulesDescription[$module]['name'].'" title="'.$modulesDescription[$module]['name'].'" /></div>
										<div style="float: left; height: 35px; width: 300px;"><label for="preInstallModules_'.$country_iso_code.'_'.$module.'">'.$modulesDescription[$module]['label'].'</label></div>
										<br clear="left" />
										<span id="resultInfosNotification" class="result aligned"></span>
										<p class="userInfos aligned">'.$modulesDescription[$module]['description'].'</p>
										<div id="divForm_'.$country_iso_code.'_'.$module.'" style="display: none;"></div>
									</div>';
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

				?>

				<!-- Partner Modules -->





				<!--<h3><?php echo lang('Shop\'s languages'); ?></h3>
				<p class="userInfos"><?php echo lang('Select the different languages available for your shop'); ?></p>-->
				<div id="availablesLanguages" style=" float:left; text-align: center; display:none;">

					<?php echo lang('Optional languages'); ?><br/>
					<select style="width:300px;" id="aLList" multiple="multiple" size="4">
					<?php foreach ($lm->getAvailableLangs() as $lang){
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
						<?php foreach ($lm->getAvailableLangs() as $lang){
							if ( $lang['id'] == $lm->getIdSelectedLang() AND $lang['id']  != "0" ){ ?>
								<option value="<?php echo $lang->idLangPS ?>"><?php echo $lang['label'] ?></option>
						<?php }} ?>

					</select><br/>
					<label for="dLList"><?php echo lang('Shop\'s default language'); ?></label><br/>
					<select style="width:180px;" id="dLList">
						<option selected="selected" value="en">English (English)</option>
						<?php foreach ($lm->getAvailableLangs() as $lang){
							if ( $lang['id'] == $lm->getIdSelectedLang() AND $lang['id']  != "0" ){ ?>
								<option selected="selected" value="<?php echo $lang->idLangPS ?>"><?php echo $lang['label'] ?></option>
						<?php }} ?>
					</select>
				</div>
			</form>

			<div id="resultEnd">
				<span id="resultInfosSQL" class="result"></span>
				<span id="resultInfosLanguages" class="result"></span>
			</div>

		</div>

		<div class="sheet" id="sheet_end" style="padding:0">
			<div style="padding:1em">
				<h2><?php echo lang('PrestaShop is ready!'); ?></h2>
				<h3><?php echo lang('Your installation is finished!'); ?></h3>
				<p><?php echo lang('You have just installed and configured PrestaShop as your online shop solution. We wish you all the best with the success of your online shop.'); ?></p>
				<p><?php echo lang('Here are your shop information. You can modify them once logged in.'); ?></p>
				<table id="resultInstall" cellspacing="0">
					<tr>
						<td class="label"><?php echo lang('Shop name:'); ?></td>
						<td id="endShopName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr>
						<td class="label"><?php echo lang('First name:'); ?></td>
						<td id="endFirstName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr>
						<td class="label"><?php echo lang('Last name:'); ?></td>
						<td id="endName" class="resultEnd">&nbsp;</td>
					</tr>
					<tr>
						<td class="label"><?php echo lang('E-mail:'); ?></td>
						<td id="endEmail" class="resultEnd">&nbsp;</td>
					</tr>
				</table>
				<h3><?php echo lang('WARNING: For more security, you must delete the \'install\' folder and readme files (readme_fr.txt, readme_en.txt, readme_es.txt, readme_de.txt, readme_it.txt, CHANGELOG).'); ?></h3>

				<a href="../admin" id="access" class="BO" target="_blank">
					<span class="title"><?php echo lang('Back Office'); ?></span>
					<span class="description"><?php echo lang('Manage your store with your back office. Manage your orders and customers, add modules, change your theme, etc...'); ?></span>
					<span class="message"><?php echo lang('Manage your store'); ?></span>
				</a>
				<a href="../" id="access" class="FO" target="_blank">
					<span class="title"><?php echo lang('Front Office'); ?></span>
					<span class="description"><?php echo lang('Find your store as your future customers will see!'); ?></span>
					<span class="message"><?php echo lang('Discover your store'); ?></span>
				</a>
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

		<div class="sheet" id="sheet_disclaimer">
			<h2><?php echo lang('Disclaimer'); ?></h2>
			<h3><?php echo lang('Warning: a manual backup is HIGHLY recommended before continuing!'); ?></h3>
			<p><?php echo lang('Please backup the database and application files.'); ?></p>
			<p><?php echo lang('When your files and database are saving in an other support, please certify that your shop is really backed up.'); ?><br /><br /></p>
			<div id="disclaimerDivCertify">
				<input id="btDisclaimerOk" type="checkbox" value="1" style="vertical-align: middle; width: 16px; height: 16px;" />
				<label for="btDisclaimerOk" style="font-weight: bold; color: #CC0000;"><?php echo lang('I certify that I backed up my database and application files. I assume all responsibility for any data loss or damage related to this upgrade.'); ?></label>
			</div>
			
			<div id="upgradeProcess" style="display: none;width: 650px;">
				<?php 
				
				function sortnatversion($a, $b)
				{
					return strnatcmp($a['version'], $b['version']);
				}
				$countNonNative = 0;
				if ($oldversion !== false AND !$sameVersions)
				{
					include_once(realpath(INSTALL_PATH.'/../config').'/defines.inc.php');
					$moduleList = Module::getNonNativeModuleList();
					$moduleNonNativeLi = '<ul>';
					foreach($moduleList as $module)
						if($module['active'])
						{
							$countNonNative++;
							$moduleNonNativeLi .= '<li>'.$module['name'].'</li>';
						}
					$moduleNonNativeLi .= '</ul>';	
				}
				if($countNonNative)
				{
					echo '<br /><br />
					<h2>'.lang('Module compatibility').'</h2>';
					echo '<div style="font-weight: bold; background-color: #ffdeb7; color: #000; padding: 10px; border: 1px solid #999; margin-top: 10px;">
					<p><img src="../img/admin/warning.gif" alt="" style="vertical-align: middle;" /> '.lang('It\'s dangerous to keep non-native modules activated during the update. If you really want to take this risk, uncheck the following box.').'</p>
					</div>
					<p>'.lang('You will be able to manually reactivate them in your back-office, once the update process has succeeded.').'</p>
					<input id="customModuleDesactivation" type="checkbox" checked="checked" value="1" name="customModuleDesactivation" /> <label for="customModuleDesactivation">'
					.lang('Ok, please desactivate the following modules, I will reactivate them later.').' : </label>';
					echo $moduleNonNativeLi;
	
				}

				echo '<h2>'.lang('Theme compatibility').'</h2>';
				echo '<p>'.lang('Before updating, you need to check that your theme is compatible with version').' <b>'.INSTALL_VERSION.'</b> '.lang('of PrestaShop.').'</p>
	<p><b>'.lang('In this aim, use our').'</b> <a target="_blank" href="http://validator.prestashop.com?version='.INSTALL_VERSION.'" title="'.lang('Link to the validator').'"><b>'.lang('Online Theme Validator').'</b></a>.'.'</p>';
	echo '<p>'.lang('If your theme is not valid, you may experience some problems in your front-office aspect, but don\'t panic ! To solve this, you can make it compatible by correcting the validators errors or by using a theme compatible with ').' '.INSTALL_VERSION.' '.lang('version').'.</p>';
				
				echo '<h2>'.lang('Let\'s go!').'</h2>
				<p>'.lang('Click on the "Next" button to start the upgrade, this can take several minutes,').' <u style="font-weight: bold; text-decoration: underline;">'.lang('do not close the window and be patient.').'</u></p>';
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
						if ($file != '.' AND $file != '..')
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
						<table cellpadding="5" border="1" style="font-size: 11px; margin-top: 10px;">
							<tr>
								<th>'.lang('Upgrade file').'</th>
								<th style="width: 100px;">'.lang('Modifications to process').'</th>
							</tr>';					
						
						uasort($upgradeFiles, 'sortnatversion');
						$totalInstructions = 0;
						foreach ($upgradeFiles AS $file)
						{
							echo '<tr><td style="'.($file['is_major'] ? 'font-weight: bold;' : 'padding-left: 12px;').'">v'.$file['version'].($file['is_major'] ? ' '.lang('(major)') : '').'</td><td style="text-align: right; padding-right: 5px;">'.(int)$file['instructions'].'</td></tr>';
							$totalInstructions += (int)$file['instructions'];
						}
						echo '<tr style="font-weight: bold;"><td>'.lang('TOTAL').'</td><td style="text-align: right; padding-right: 5px;">'.(int)$totalInstructions.'</td></tr>';
						echo '
						</table>';
						
						$upgradeTime = $totalInstructions * 0.05;
						$minutes = (int)($upgradeTime / 60);
						$seconds = (int)($upgradeTime - ($minutes * 60));
						
						echo '<p><img src="../img/admin/time.gif" alt="" style="vertical-align: middle;" /> '.lang('Estimated time to complete the').' '.(int)$totalInstructions.' '.lang('modifications:').' <b style="font-size: 14px;">'.(int)$minutes.' '.($minutes > 1 ? lang('minutes') : lang('minute')).' '.(int)$seconds.' '.($seconds > 1 ? lang('seconds') : lang('second')).'</b><br />
						<i style="font-size: 11px;">'.lang('Depending on your server and the size of your shop').'</i></p>';
						
						if ($majorReleases > 1)
							echo '<p style="margin-top: 8px;"><b>'.lang('You did not update your shop for a while,').' '.(int)$majorReleases.' '.lang('stable releases have been made ​​available since.').'</b> '.lang('This is not a problem however the update may take several minutes, try to update your shop more frequently.').'</p>';
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
				$color = '#FFDEB7';
				
				echo '
				<br />
				<h2>'.lang('Hosting parameters').'</h2>
				<p>'.lang('PrestaShop tries to automatically set the best settings for your server in order the update to be successful.').'</p>
				<table cellpadding="5" border="1" style="font-size: 11px;">
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
				<div style="font-weight: bold; background: '.$color.'; color: #000; padding: 10px; border: 1px solid #999; margin-top: 10px;">';
				
				if ($color == '#D9F2D0')
					echo '<img src="../img/admin/ok.gif" alt="" style="vertical-align: middle;" /> '.lang('All your settings seem to be OK, go for it!');
				elseif ($color == '#FFDEB7')
					echo '<img src="../img/admin/warning.gif" alt="" style="vertical-align: middle;" /> '.lang('Beware, your settings look correct but are not optimal, if you encounter problems (upgrade too long, memory error...), please ask your hosting provider to increase the values of these parameters (max_execution_time & memory_limit).');
				elseif ($color == '#FAE2E3')
					echo '<img src="../img/admin/error2.png" alt="" style="vertical-align: middle;" /> '.lang('We strongly recommend that you inform your hosting provider to modify the settings before process to the update.');
					
				echo '
				</div><br />';
				
				
				?>
			</div>
		</div>
		
		<div class="sheet" id="sheet_require_update">

			<h2><?php echo lang('System and permissions'); ?></h2>

			<h3><?php echo lang('Required set-up. Please verify the following checklist items are true.'); ?></h3>

			<p>
				<?php echo lang('If you have any questions, please visit our '); ?>
				<a href="http://www.prestashop.com/wiki" target="_blank"><?php echo lang('Documentation Wiki'); ?></a>
				<?php echo lang('and/or'); ?>
				<a href="http://www.prestashop.com/forums/" target="_blank"><?php echo lang('Community Forum'); ?></a><?php echo lang('.'); ?>
			</p>

			<h3 id="resultConfig_update" style="font-size: 20px; text-align: center; padding: 0px; display: none;"></h3>
			<ul id="required_update">
				<li class="title"><?php echo lang('PHP parameters:')?></li>
				<li class="required"><?php echo lang('PHP 5.0 or later installed')?></li>
				<li class="required"><?php echo lang('File upload allowed')?></li>
				<li class="required"><?php echo lang('Create new files and folders allowed')?></li>
				<li class="required"><?php echo lang('GD Library installed')?></li>
				<li class="required"><?php echo lang('MySQL support is on')?></li>
				<li class="title"><?php echo lang('Write permissions on folders:')?></li>
				<li class="required">/config</li>
				<li class="required">/tools/smarty/compile</li>
				<li class="required">/tools/smarty/cache</li>
				<li class="required">/tools/smarty_v2/compile</li>
				<li class="required">/tools/smarty_v2/cache</li>
				<li class="required">/sitemap.xml</li>
				<li class="title"><?php echo lang('Write permissions on folders (and subfolders):')?></li>
				<li class="required">/img</li>
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
			</ul>

			<p><input class="button" value="<?php echo lang('Refresh these settings'); ?>" type="button" id="req_bt_refresh_update"/></p>

		</div>

		<div class="sheet" id="sheet_updateErrors">
			<h2><?php echo lang('Error!'); ?></h2>
			<h3><?php echo lang('One or more errors have occurred...'); ?></h3>
			<p id="resultUpdate"></p>
			<p id="detailsError"></p>
		</div>

		<div class="sheet" id="sheet_end_update" style="padding:0px;">
			<div style="padding:1em;">
				<h1><?php echo lang('Your update is completed!'); ?></h1>
				<h3><?php echo lang('Your shop version is now').' '.INSTALL_VERSION; ?></h3>
				<p class="fail" id="txtErrorUpdateSQL"></p>
				<p><a href="javascript:showUpdateLog()"><?php echo lang('view the log'); ?></a></p>
				<div id="updateLog"></div>
				<p><?php echo lang('You have just updated and configured PrestaShop as your online shop solution. We wish you all the best with the success of your online shop.'); ?></p><br />
				
				<?php
				
					if (@fsockopen('www.prestashop.com', 80, $errno, $errst, 3))
					{
						echo '
						<h2>'.lang('New features in PrestaShop v').INSTALL_VERSION.'</h2>
						<iframe style="width: 595px; margin-top: 5px; padding: 5px; border: 1px solid #BBB;" src="http://www.prestashop.com/download/features.php?lang='.$lm->getIsoCodeSelectedLang().'&version='.INSTALL_VERSION.'">
							<p>Your browser does not support iframes.</p>
						</iframe>';
					}

				?>
				
				<h3 style="margin-top: 15px;"><?php echo lang('WARNING: For more security, you must delete the \'install\' folder and readme files (readme_fr.txt, readme_en.txt, readme_es.txt, readme_de.txt, readme_it.txt, CHANGELOG).'); ?></h3>
				<a href="../" id="access_update" target="_blank">
					<span class="title"><?php echo lang('Front Office'); ?></span>
					<span class="description"><?php echo lang('Find your store as your future customers will see!'); ?></span>
					<span class="message"><?php echo lang('Discover your store'); ?></span>
				</a>
			</div>
			<?php
			if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)): ?>
			<iframe src="http://addons.prestashop.com/psinstall.php?lang=<?php echo $lm->getIsoCodeSelectedLang(); ?>" scrolling="no" id="prestastore_update">
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
	<li><a href="http://www.prestashop.com/forum/" title="<?php echo lang('Official forum'); ?>"><?php echo lang('Official forum'); ?></a> | </li>
	<li><a href="http://www.prestashop.com" title="PrestaShop.com">PrestaShop.com</a> | </li>
	<li><a href="http://www.prestashop.com/contact.php" title="<?php echo lang('Contact us'); ?>"><?php echo lang('Contact us'); ?></a> | </li>
	<li>&copy; 2005-<?php echo date('Y'); ?></li>
</ul>
</body>
</html>


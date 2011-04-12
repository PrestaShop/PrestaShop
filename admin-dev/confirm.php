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

define('PS_ADMIN_DIR', getcwd());

include(PS_ADMIN_DIR.'/../config/config.inc.php');
$cookie = new Cookie('psAdmin');
Tools::setCookieLanguage();

$translations = array(
	'FR' => array(
		'Referer is missing' => 'Vous devez indiquer un "referer"',
		'Confirmation' => 'Confirmation',
		'Yes' => 'Oui',
		'No' => 'Non',
		'close'	=> 'fermer')
);

if (!Tools::getValue('referer')):
	echo '<p>'.Tools::historyc_l('Referer is missing', $translations).'</p>';
	echo '<p><a href="#" onclick="tb_remove()">'.Tools::historyc_l('close', $translations).'</a></p>';
else:
	$referer = Tools::htmlentitiesUTF8(rawurldecode(Tools::getValue('referer')));?>



<h2><?php echo Tools::historyc_l('Confirmation', $translations) ?></h2>
<p>
	<a href="#" class="thickbox confirm_yes" title="" onclick="tb_remove(); window.open('<?php echo $referer ?>', '_self')">
		<?php echo Tools::historyc_l('Yes', $translations) ?>
	</a>
	<a href="#" class="confirm_no" onclick="tb_remove()"><?php echo Tools::historyc_l('No', $translations) ?></a>
</p>

<?php endif; //check if referer exists  
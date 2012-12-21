<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

echo '			</div>
			</div>
			'.Hook::exec('displayBackOfficeFooter').'
			<div id="footer">
				<div style="float:left;margin-left:10px;padding-top:6px">
					<a href="http://www.prestashop.com/" target="_blank" style="font-weight:700;color:#666666">PrestaShop&trade; '._PS_VERSION_.'</a><br />
					<span style="font-size:10px">'.translate('Load time:').' '.number_format(microtime(true) - $timerStart, 3, '.', '').'s</span>
				</div>
				<div style="float:right;height:40px;margin-right:10px;line-height:38px;vertical-align:middle">';
if (strtoupper(Context::getContext()->language->iso_code) == 'FR') echo '<span style="color: #812143; font-weight: bold;">Questions / Renseignements / Formations :</span> <strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h ';

echo '				| <a href="http://www.prestashop.com/en/contact_us/" target="_blank" class="footer_link">'.translate('Contact').'</a>
					| <a href="http://forge.prestashop.com" target="_blank" class="footer_link">'.translate('Bug Tracker').'</a>
					| <a href="http://www.prestashop.com/forums/" target="_blank" class="footer_link">'.translate('Forum').'</a>	
				</div>
			</div>
		</div>
	</div>';

// FrontController::disableParentCalls();
// $fc = new FrontController();
// $fc->displayFooter();

echo '
	</body>
</html>';


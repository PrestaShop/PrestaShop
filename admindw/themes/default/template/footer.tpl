{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 16914 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

					<div style="clear:both;height:0;line-height:0">&nbsp;</div>
					</div>
					<div style="clear:both;height:0;line-height:0">&nbsp;</div>
				</div>
		{if $display_footer}
				{hook h="displayBackOfficeFooter"}
				<div id="footer">
					<div class="footerLeft">
						<a href="http://www.prestashop.com/" target="_blank">PrestaShop&trade; {$ps_version}</a><br />
						<span>{l s='Load time: '}{number_format(microtime(true) - $timer_start, 3, '.', '')}s</span>
					</div>
					<div class="footerRight">
						{if $iso_is_fr}
							<span>Questions / Renseignements / Formations :</span> <strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h
						{/if}
						|&nbsp;<a href="http://www.prestashop.com/en/contact_us/" target="_blank" class="footer_link">{l s='Contact'}</a>
						|&nbsp;<a href="http://forge.prestashop.com" target="_blank" class="footer_link">{l s='Bug Tracker'}</a>
						|&nbsp;<a href="http://www.prestashop.com/forums/" target="_blank" class="footer_link">{l s='Forum'}</a>	
					</div>
				</div>
			</div>
		</div>
		<div id="ajax_confirmation" style="display:none"></div>
		{* ajaxBox allows*}
		<div id="ajaxBox" style="display:none"></div>
		{/if}
		<div id="scrollTop"><a href="#top"></a></div>
	</body>
</html>

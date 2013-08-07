{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

	</div>
</div>
{if $display_footer}
{hook h="displayBackOfficeFooter"}
	<div id="footer">
		<div class="col-lg-4">
			<a href="http://www.prestashop.com/" target="_blank">PrestaShop&trade; {$ps_version}</a><br />
			<span>{l s='Load time: '} {number_format(microtime(true) - $timer_start, 3, '.', '')}s</span>
		</div>
		<div class="col-lg-3">
			<a class="link-social link-twitter" href="#" title="Twitter">
				<i class="icon-twitter"></i>
			</a>
			<a class="link-social link-facebook" href="#" title="Facebook">
				<i class="icon-facebook"></i>
			</a>
			<a class="link-social link-github" href="#" title="Github">
				<i class="icon-github"></i>
			</a>
			<a class="link-social link-google" href="#" title="Google">
				<i class="icon-google-plus"></i>
			</a>
		</div>	

		<div class="col-lg-4">
			<a href="http://www.prestashop.com/en/contact_us/" target="_blank" class="footer_link">{l s='Contact'}</a>
			|&nbsp;<a href="http://forge.prestashop.com" target="_blank" class="footer_link">{l s='Bug Tracker'}</a>
			|&nbsp;<a href="http://www.prestashop.com/forums/" target="_blank" class="footer_link">{l s='Forum'}</a>
			{if $iso_is_fr}
			<p>Questions / Renseignements / Formations : 
				<strong>+33 (0)1.40.18.30.04</strong> de 09h &agrave; 18h
			<p>
			{/if}
		</div>
		<div id="go-top"><i class="icon-arrow-up"></i></div>
	</div>
	<div id="ajax_confirmation" style="display:none"></div>
{* ajaxBox allows*}	<div id="ajaxBox" style="display:none"></div>
{/if}
</body>
</html>
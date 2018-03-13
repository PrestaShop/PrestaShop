{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

	</div>
</div>
{if $display_footer}
<div id="footer" class="bootstrap hide">

	<div class="col-sm-2 hidden-xs">
		<a href="http://www.prestashop.com/" class="_blank">PrestaShop&trade;</a>
		-
		<span id="footer-load-time"><i class="icon-time" title="{l s='Load time: ' d='Admin.Navigation.Footer'}"></i> {number_format(microtime(true) - $timer_start, 3, '.', '')}s</span>
	</div>

	<div class="col-sm-2 hidden-xs">
		<div class="social-networks">
			<a class="link-social link-youtube _blank" href="https://www.youtube.com/user/prestashop" title="Youtube">
				<i class="icon-youtube"></i>
			</a>
			<a class="link-social link-twitter _blank" href="https://twitter.com/PrestaShop" title="Twitter">
				<i class="icon-twitter"></i>
			</a>
			<a class="link-social link-facebook _blank" href="https://www.facebook.com/prestashop" title="Facebook">
				<i class="icon-facebook"></i>
			</a>
			<a class="link-social link-github _blank" href="https://www.prestashop.com/github" title="Github">
				<i class="icon-github"></i>
			</a>
		</div>
	</div>
	<div class="col-sm-5">
		<div class="footer-contact">
			<a href="http://www.prestashop.com/en/contact_us?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-envelope"></i>
				{l s='Contact' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="http://forge.prestashop.com/?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-bug"></i>
				{l s='Bug Tracker' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="https://www.prestashop.com/club/?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-users"></i>
				{l s='User Club' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="http://feedback.prestashop.com/forums/387864-prestashop-1-7-x?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-lightbulb"></i>
				{l s='Feature Requests' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="http://www.prestashop.com/forums/?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-comments"></i>
				{l s='Forum' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="http://addons.prestashop.com/?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-puzzle-piece"></i>
				{l s='Addons' d='Admin.Navigation.Footer'}
			</a>
			/&nbsp;
			<a href="http://www.prestashop.com/en/training-prestashop?utm_source=back-office&amp;utm_medium=footer&amp;utm_campaign=back-office-{$lang_iso|upper}&amp;utm_content={if $host_mode}cloud{else}download{/if}" class="footer_link _blank">
				<i class="icon-book"></i>
				{l s='Training' d='Admin.Navigation.Footer'}
			</a>
			{if $host_mode}
			/&nbsp;
			<a href="http://status.prestashop.com/" class="footer_link _blank">
				<i class="icon-circle status-page-dot"></i>
				<span class="status-page-description"></span>
			</a>
			{/if}
			{if $iso_is_fr && !$host_mode}
			<p>Questions • Renseignements • Formations :
				<strong>+33 (0)1.40.18.30.04</strong>
			</p>
			{/if}
		</div>
	</div>

	<div class="col-sm-3">
		{hook h="displayBackOfficeFooter"}
	</div>

	<div id="go-top" class="hide"><i class="icon-arrow-up"></i></div>
</div>
{/if}
{if isset($php_errors)}
	{include file="error.tpl"}
{/if}

{if isset($modals)}
<div class="bootstrap">
	{$modals}
</div>
{/if}

</body>
</html>

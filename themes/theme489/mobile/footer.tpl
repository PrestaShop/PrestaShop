{*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 6594 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

			<div id="footer">
				<div class="ui-grid-a">
					{hook h="displayMobileFooterChoice"}
				</div><!-- /grid-a -->

				<div id="full-site-section" class="center">
					<a href="{$link->getPageLink('index', true)}?no_mobile_theme" data-ajax="false">{l s='Browse the full site'}</a>
				</div>

				<div data-role="footer" data-theme="a" id="bar_footer">
					<div id="link_bar_footer" class="ui-grid-a">
						<div class="ui-block-a">
							<a href="{$link->getPageLink('index', true)}" data-ajax="false">{$PS_SHOP_NAME}</a>
						</div>
						{if $conditions}
						<div class="ui-block-b">
							<a href="{$link->getCMSLink($id_cgv)}" data-ajax="false">{l s='Terms of service'}</a>
						</div>
						{/if}
					</div>
				</div>
			</div><!-- /footer -->
		</div><!-- /page -->
	</body>
</html>

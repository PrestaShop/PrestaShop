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

		{if !$content_only}
                    </div>
    {if isset($HOOK_RIGHT_COLUMN) && (str_replace(" ","",$HOOK_RIGHT_COLUMN)) !=''}{assign var='RightColumn' value=3}{/if}
    {if isset($RightColumn) && $RightColumn !=0}
    <!-- Right -->
        <div id="right_column" class="col-xs-12 col-sm-3 column">
            {$HOOK_RIGHT_COLUMN}
        </div>
    {/if}
                	</div>
				</div>
            </div>
<!-- Footer -->
			<div class="footer-container">
            	<div class="container">
                    <footer id="footer" class="row">
                        {$HOOK_FOOTER}
                        {if $PS_ALLOW_MOBILE_DEVICE}
                            <p class="text-center"><a href="{$link->getPageLink('index', true)}?mobile_theme_ok">{l s='Browse the mobile site'}</a></p>
                        {/if}
                    </footer>
                </div>
            </div>
		</div>
	{/if}
	</body>
</html>

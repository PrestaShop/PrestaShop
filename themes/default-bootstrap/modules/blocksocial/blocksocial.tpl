{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<section id="social_block">
	<ul>
		{if $facebook_url != ''}
			<li class="facebook">
				<a target="_blank" href="{$facebook_url|escape:html:'UTF-8'}">
					<span>{l s='Facebook' mod='blocksocial'}</span>
				</a>
			</li>
		{/if}
		{if $twitter_url != ''}
			<li class="twitter">
				<a target="_blank" href="{$twitter_url|escape:html:'UTF-8'}">
					<span>{l s='Twitter' mod='blocksocial'}</span>
				</a>
			</li>
		{/if}
		{if $rss_url != ''}
			<li class="rss">
				<a target="_blank" href="{$rss_url|escape:html:'UTF-8'}">
					<span>{l s='RSS' mod='blocksocial'}</span>
				</a>
			</li>
		{/if}
        {if $youtube_url != ''}
        	<li class="youtube">
        		<a target="_blank"  href="{$youtube_url|escape:html:'UTF-8'}">
        			<span>{l s='Youtube' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
        {if $google_plus_url != ''}
        	<li class="google-plus">
        		<a  target="_blank" href="{$google_plus_url|escape:html:'UTF-8'}">
        			<span>{l s='Google Plus' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
        {if $pinterest_url != ''}
        	<li class="pinterest">
        		<a target="_blank"  href="{$pinterest_url|escape:html:'UTF-8'}">
        			<span>{l s='Pinterest' mod='blocksocial'}</span>
        		</a>
        	</li>
        {/if}
	</ul>
    <h4>{l s='Follow us' mod='blocksocial'}</h4>
</section>
<div class="clearfix"></div>

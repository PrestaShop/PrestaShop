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
*  @version  Release: $Revision: 9795 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helpers/form/form.tpl"}

{block name="other_fieldsets"}
	{if $f == 1}
		<br class="clear" />
		<fieldset>
			<legend onclick="$('#tracking_help').slideToggle();" style="cursor:pointer;">
				<img src="../img/admin/help.png" /> {l s='Help'}
			</legend>
			<div id="tracking_help" style="display: none;">
				<p>{l s='Definitions:'}</p>
				<ul style="list-style: disc; margin-left: 20px;">
					<li>
						{l s='The field `http_referer` is the website from which your customers arrive.'}<br />
						{l s='For example, visitors coming from Google will have a `http_referer` like this one: "http://www.google.com/search?q=prestashop".'}<br />
						{l s='If the visitor arrives directly (by typing the URL of your shop or by using their bookmarks, for example), `http_referer` will be empty.'}<br />
						{l s='So if you want all the visitors coming from google, you can type "%google%" in this field, or "%google.fr%" if you want the visitors coming from Google France only.'}<br />
					</li>
					<br />
					<li>
						{l s='The field `request_uri` is the URL from which the customers come to your website.'}<br />
						{l s='For example, if the visitor accesses a product page, the URL will be'} "{$uri}music-ipods/1-ipod-nano.html".<br />
						{l s='This is helpful because you can add some tags or tokens in the links pointing to your website.'}
						{l s='For example, you can post a link "%dindex.php?prestashop" in the forum and get statistics by entering "%prestashop" in the field `request_uri`. You will get all the visitors coming from the forum.'}
						{l s='This method is more reliable than the `http_referer` one, but there is a danger: if a search engine read a page with your link, then it will be displayed in its results and you will have not only the forum visitors, but also the ones from the search engine.'}
					</li>
					<br />
					<li>
						{l s='The `include` fields indicate what has to be included in the URL.'}
					</li>
					<br />
					<li>
						{l s='The `exclude` fields indicate what has to be excluded from the URL.'}
					</li>
					<br />
					<li>
						{l s='When using the simple mode, you can use some generic characters which can replace any characters:'}
						<ul>
							<li>{l s='"_" will replace one character. If you want to use the real "_", you should type'} "\\_".</li>
							<li>{l s='"%" will replace any number of characters. If you want to use the real "%", you should type'} "\\%".</li>
						</ul>
					</li>
					<br />
					<li>
						{l s='The simple mode uses the MySQL "LIKE", but for a higher potency you can use MySQL regular expressions.'}
						<a href="http://dev.mysql.com/doc/refman/5.0/en/regexp.html" target="_blank" style="font-style: italic;">{l s='Take a look at our documentation for more details...'}</a>
					</li>
				</ul>
			</div>
		</fieldset>
	{/if}
{/block}

{block name="other_input"}

	{if $key == 'help'}
		<a style="cursor:pointer;font-style:italic;" onclick="$('#tracking_help').slideToggle();">
			<img src="../img/admin/help.png" /> {l s='Get help!'}
		</a>
	{/if}

{/block}

{block name="label"}

	{if $input.name == 'http_referer_regexp'}
		<div id="tracking_expert" style="display: none;">
	{/if}

	{if isset($input.h3)}
		<h3>{$input.h3}</h3>
	{/if}

	{if isset($input.label)}
		<label>{$input.label} </label>
	{/if}

{/block}

{block name="field"}
	{$smarty.block.parent}
	{if $input.name == 'request_uri_regexp_not'}
		</div>
	{/if}
{/block}

{block name="script"}

	$(document).ready(function() {
		$('fieldset#fieldset_3 legend').css('cursor', 'pointer');
		$('fieldset#fieldset_3 legend').click(function(){
			$('#tracking_expert').slideToggle();
		});
	});

{/block}

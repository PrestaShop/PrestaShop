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
*  @version  Release: $Revision: 8897 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $info}
	<div class="hint clear" style="display:block;">
		<b>{l s ='How to create a new sql query?'}</b>
		<br />
		<ul>
			<li>{l s ='Click "Add new".'}<br /></li>
			<li>{l s ='Fill in the fields and click "Save".'}</li>
			<li>{l s ='You can then view the query results by clicking on the tab: '} <img src="../img/admin/details.gif"></li>
			<li>{l s ='You can then export the query results as a file. Csv file by clicking on the tab: '} <img src="../img/admin/export.gif"></li>
		</ul>
	</div><br />
{/if}

{if $warning}
	<div class="warn"><img src="../img/admin/warn2.png">{l s ='Warning: when saving the query, only the request type "SELECT" are allowed.'}</div>
{/if}

{if isset($view)}
	{if isset($view['error'])}
		<p>{l s ='This query has no result.'}</p>
	{else}
		<h2>{$view['name']}</h2>
		<table cellpadding="0" cellspacing="0" class="table" id="viewRequestSql">
			<tr>
				{foreach $view['key'] AS $key}
					<th align="center">{$key}</th>
				{/foreach}
			</tr>
			{foreach $view['results'] AS $result}
				<tr>
					{foreach $view['key'] AS $name}
						<td>{$result[$name]}</td>
					{/foreach}
				</tr>
			{/foreach}
		</table>
		
		<script type="text/javascript">
			$(function(){
				var width = $('#viewRequestSql').width();
				if (width > 990){
					$('#viewRequestSql').css('display','block').css('overflow-x', 'scroll');
				}
			});
		</script>
	{/if}
{elseif isset($tab_form)}
	<form action="{$tab_form['current']}" method="post">
		{if $tab_form['id']}<input type="hidden" name="id_{$tab_form['table']}" value="{$tab_form['id']}" />{/if}
		<fieldset><legend><img src="../img/admin/subdomain.gif" />{l s ='Request'}</legend>
			<label>{l s ='Name:'} <sup>*</sup></label>
			<div class="margin-form">
				<input type="text" name="name" value="{$tab_form['name']}" size="103" />
			</div>
			<label>{l s ='Request:'} <sup>*</sup></label>
			<div class="margin-form">
				<textarea name="sql" cols="100" rows="10">{$tab_form['sql']}</textarea>
			</div>
			<div class="margin-form">
				<input type="submit" value="{l s ='Save'}" name="submitAdd{$tab_form['table']}" class="button" />
			</div>
			<div class="small"><sup>*</sup> {l s ='Required field'}</div>
		</fieldset>
	</form>
{/if}

{$content}
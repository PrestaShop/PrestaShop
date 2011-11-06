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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{extends file="helper/form/form.tpl"}

{block name="start_field_block"}
	<div class="margin-form">
	{if $input.type == 'special'}
		<p id="{$input.name}"><img src="../img/admin/{$input.img}" alt="" /> {$input.text}</p>
	{/if}
{/block}

{block name=script}

	$(document).ready(function() {
		$('#iso_code').keyup(function() {
			checkLangPack();
		});
	});

{/block}

{block name="other_fieldsets"}

	{if isset($fields['new'])}
		<br /><br />
		<fieldset style="width:572px;">
			{foreach $fields['new'] as $key => $field}
				{if $key == 'legend'}
					<legend>
						{if isset($field.image)}<img src="{$field.image}" alt="{$field.title}" />{/if}
						{$field.title}
					</legend>
					<p>{l s='This language is NOT complete and cannot be used in the Front or Back Office because some files are missing.'}</p>
					<br />
				{elseif $key == 'list_files'}
					{foreach $field as $list}
						<label>{$list.label}</label>
						<div class="margin-form" style="margin-top:4px;">
							{foreach $list.files as $key => $file}
								{if !file_exists($key)}
									<font color="red">
								{/if}
								{$key}
								{if !file_exists($key)}
									</font>
								{/if}
								<br />
							{/foreach}
						</div>
						<br style="clear:both;" />
					{/foreach}
				{/if}
			{/foreach}
			<br />
			<div class="small">{l s='Missing files are marked in red'}</div>
		</fieldset>
	{/if}

{/block}

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

<div class="toolbar-placeholder">
	<div class="toolbarBox toolbarHead">
		<ul class="cc_button">
			{if $add_permission eq '1'}
			<li>
				<a id="desc-module-new" class="toolbar_btn" href="#top_container" onclick="$('#module_install').slideToggle();" title="{l s='Add a new module'}">
					<span class="process-icon-new-module" ></span>
					<div>{l s='Add a new module'}</div>
				</a>
			</li>
			{/if}
		</ul>
		<div class="pageTitle">
			<h3><span id="current_obj" style="font-weight: normal;"><span class="breadcrumb item-0">Module</span> : <span class="breadcrumb item-1">{l s='List of modules'}</span></span></h3>
		</div>
	</div>
</div>

{if $add_permission eq '1'}
	<div id="module_install" style="width:500px;margin-top:5px;{if !isset($smarty.post.downloadflag)}display: none;{/if}">
		<fieldset>
			<legend><img src="../img/admin/add.gif" alt="{l s='Add a new module'}" class="middle" /> {l s='Add a new module'}</legend>
			<p>{l s='The module must either be a zip file or a tarball.'}</p>
			<div style="float:left;margin-right:50px">
				<form action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data">
					<label style="width: 100px">{l s='Module file'}</label>
					<div class="margin-form" style="padding-left: 140px">
						<input type="file" name="file" />
						<p>{l s='Upload a module from your computer.'}</p>
					</div>
					<div class="margin-form" style="padding-left: 140px">
						<input type="submit" name="download" value="{l s='Upload this module'}" class="button" />
					</div>
				</form>
			</div>
		</fieldset>
		<br />
	</div>
{/if}


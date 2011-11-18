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
*  @version  Release: $Revision: 9771 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div id="productBox">
	<div class="toolbar-placeholder">
		<div class="toolbarBox toolbarHead">
	
			<ul class="cc_button">
				<li>
					<a id="desc-module-new" class="toolbar_btn" href="#top_container" onclick="$('#module_install').slideToggle();" title="Add new">
						<span class="process-icon-new-module" ></span>
						<div>Add new module</div>
					</a>
				</li>
				<li>
					<a id="desc-module-addon-new" class="toolbar_btn" href="{$addonsUrl}" title="Add new">
						<span class="process-icon-new-module-addon" ></span>
						<div>Add new via Addons</div>
					</a>
				</li>
			</ul>

			<div class="pageTitle">
				<h3><span id="current_obj" style="font-weight: normal;"><span class="breadcrumb item-0">Module</span> : <span class="breadcrumb item-1">Liste de modules</span></span></h3>
			</div>

		</div>
	</div>


	<div id="module_install" style="width:500px;margin-top:5px;{if !isset($smarty.post.downloadflag)}display: none;{/if}">
		<fieldset>
			<legend><img src="../img/admin/add.gif" alt="{l s='Add a new module'}" class="middle" /> {l s='Add a new module'}</legend>
			<p>{'The module must be either a zip file or a tarball.'}</p>
			<div style="float:left;margin-right:50px">
				<form action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data">
					<label style="width: 100px">{l s='Module file'}</label>
					<div class="margin-form" style="padding-left: 140px">
						<input type="file" name="file" />
						<p>{l s='Upload the module from your computer.'}</p>
					</div>
					<div class="margin-form" style="padding-left: 140px">
						<input type="submit" name="download" value="{l s='Upload this module'}" class="button" />
					</div>
				</form>
			</div>
		</fieldset>
		<br />
	</div>


	<!--start filter module-->
	<div class="filter-module">
		<form id="filternameForm" method="post">
			<input type="text" value="" name="filtername" autocomplete="off" class="ac_input">
			<input type="submit" class="button" value="{l s='Search'}">
		</form>

		<form method="post">

			<div class="select-filter">
				<label class="search-filter">{l s='Sort by'}:</label>

					<select name="module_type">
						<option value="allModules" {if $showTypeModules eq 'allModules'}selected="selected"{/if}>{l s='All Modules'}</option>
						<option value="nativeModules" {if $showTypeModules eq 'nativeModules'}selected="selected"{/if}>{l s='Native Modules'}</option>
						<option value="partnerModules" {if $showTypeModules eq 'partnerModules'}selected="selected"{/if}>{l s='Partners Modules'}</option>
						<optgroup label="{l s='Authors'}">
							{foreach from=$list_modules_authors key=module_author item=status}
								<option value="authorModules[{$module_author}]" {if $status eq "selected"}selected{/if}>{$module_author|truncate:20:'...'}</option>
							{/foreach}
						</optgroup>
						<option value="otherModules" {if $showTypeModules eq 'otherModules'}selected="selected"{/if}>{l s='Others Modules'}</option>
					</select>
					&nbsp;
					<select name="module_install">
						<option value="installedUninstalled" {if $showInstalledModules eq 'installedUninstalled'}selected="selected"{/if}>{l s='Installed & Uninstalled'}</option>
						<option value="installed" {if $showInstalledModules eq 'installed'}selected="selected"{/if}>{l s='Installed Modules'}</option>
						<option value="uninstalled" {if $showInstalledModules eq 'uninstalled'}selected="selected"{/if}>{l s='Uninstalled Modules'}</option>
					</select>
					&nbsp;
					<select name="module_status">
						<option value="enabledDisabled" {if $showEnabledModules eq 'enabledDisabled'}selected="selected"{/if}>{l s='Enabled & Disabled'}</option>
						<option value="enabled" {if $showEnabledModules eq 'enabled'}selected="selected"{/if}>{l s='Enabled Modules'}</option>
						<option value="disabled" {if $showEnabledModules eq 'disabled'}selected="selected"{/if}>{l s='Disabled Modules'}</option>
					</select>
					&nbsp;
					<select name="country_module_value">
						<option value="0" >{l s='All countries'}</option>
						<option value="1" {if $showCountryModules eq 1}selected="selected"{/if}>{l s='Current country:'} {$nameCountryDefault}</option>
					</select>

			</div>

			<div class="button-filter">
				<input type="submit" value="{l s='Reset'}" name="resetFilterModules" class="button" />
				<input type="submit" value="{l s='Filter'}" name="filterModules" class="button" />
			</div>

		</form>

	</div>
	<!--end filter module-->

	<div id="container">
		<!--start sidebar module-->
		<div class="sidebar">
			<div class="categorieTitle">
				<h3>{l s='Categories'}</h3>
				<div class="subHeadline">{$nb_modules}</div>
				<ul class="categorieList">
					{foreach from=$list_modules_categories item=module_category key=module_category_key}
						<li {if isset($categoryFiltered[$module_category_key])}style="background-color:#EBEDF4"{/if}>
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}"><span>{$module_category.name}</span></a></div>
							<div class="count">{$module_category.nb}</div>
						</li>
					{/foreach}
				</ul>
			</div>
			

			<div class="categorieStatus">
				<h3>Etat du module</h3>
				<div class="subHeadline">{$nb_modules}</div>
				<ul class="categorieList">
					<li>
						<div class="categorieWidth"><a href="#"><span>{l s='Installed'}</span></a></div>
						<div class="count">{$nb_modules_installed}</div>
					</li>
					<li>
						<div class="categorieWidth"><a href="#"><span>{l s='Uninstalled'}</span></a></div>
						<div class="count">{$nb_modules_uninstalled}</div>
					</li>
					</ul>
				</div>
				
				<div class="categorieStatus">
					<h3>Etat du module</h3>
					<div class="subHeadline">{$nb_modules}</div>
					<ul class="categorieList">
						<li>
							<div class="categorieWidth"><a href="#"><span>{l s='Activated'}</span></a></div>
							<div class="count">{$nb_modules_activated}</div>
						</li>
						<li>
							<div class="categorieWidth"><a href="#"><span>{l s='Unactivated'}</span></a></div>
							<div class="count">{$nb_modules_unactivated}</div>
						</li>
					</ul>
				</div>
			</div>

			<div id="moduleContainer">
				<table cellspacing="0" cellpadding="0" style="width: 100%; margin-bottom:10px;" class="table" id="">
					<col width="20px">
					<col width="40px">
					<col>
					<col width="150px">
					</colgroup>
					<thead>
						<tr class="nodrag nodrop">
							<th class="center">
								<input type="checkbox" onclick="" class="noborder" name="checkme"><br>
								<!-- TODO
									<a href="#"><img border="0" src="../img/admin/down.gif"></a>
									<a href="#"><img border="0" src="../img/admin/up_d.gif"></a>
								-->
							</th>
							<th class="center"></th>
							<th>{l s='Module name'}</th>
							<th></th>
						</tr>			
					<tbody>
					{foreach from=$modules item=module}
						<tr>
							<td><input type="checkbox" name="modules" value="{$module->name}" class="noborder"></td>
							<td><img class="imgm" alt="" src="../modules/{$module->name}/{$module->logo}"></td>
							<td>
								<div class="moduleDesc" id="anchor{$module->name|ucfirst}">
									<h3>{$module->displayName}{if isset($module->id) && $module->id gt 0}<span class="setup{if isset($module->active) && $module->active eq 0} off{/if}">{l s='Installed'}</span>{else}<span class="setup non-install">{l s='Not installed'}</span>{/if}</h3>
									<div class="metadata">
										{if isset($module->author) && !empty($module->author)}
										<dl class="">
											<dt>{l s='Developed by'} :</dt>
											<dd>{$module->author|truncate:20:'...'}</dd>|
										</dl>
										{/if}
										<dl class="">
											<dt>{l s='Version'} :</dt>
											<dd>{$module->version}</dd>|
										</dl>
										<dl class="">
											<dt>{l s='Category'} :</dt>
											<dd>{$module->categoryName}</dd>
										</dl>
									</div>
									<p class="desc">{l s='Description'} : {$module->description}</p>
									{if isset($module->message)}<div class="conf">{$module->message}</div>{/if}
									<div class="row-actions-module">
										{$module->optionsHtml}
									</div>
								</div>
							</td>
							<td><a href="{if isset($module->id) && $module->id gt 0}{$module->options.uninstall_url}{else}{$module->options.install_url}{/if}" class="button installed"><span>{if isset($module->id) && $module->id gt 0}{l s='Uninstall'}{else}{l s='Install'}{/if}</span></a></td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

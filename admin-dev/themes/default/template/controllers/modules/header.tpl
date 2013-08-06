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

<div class="page-head">
	<h2 class="page-title">
		{l s='List of modules'}
	</h2>
	<div class="page-bar toolbarBox">
		<div class="btn-toolbar">
			<ul class="cc_button nav nav-pills pull-right">
				{if $add_permission eq '1'}
				<li>
					<a id="desc-module-new" class="toolbar_btn" href="#top_container" onclick="$('#module_install').slideToggle();" title="{l s='Add a new module'}">
						<i class="process-icon-new-module" ></i>
						<div>{l s='Add a new module'}</div>
					</a>
				</li>
				{/if}
			</ul>
		</div>
	</div>
</div>

{if $add_permission eq '1'}
	<div id="module_install" class="row" style="{if !isset($smarty.post.downloadflag)}display: none;{/if}">
		<div class="col-lg-12">
			<form action="{$currentIndex}&token={$token}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<legend>{l s='Add a new module'}</legend>
				<p>{l s='The module must either be a zip file or a tarball.'}</p>
				<label class="control-label col-lg-3">
					<span class="label-tooltip" data-toggle="tooltip" title="{l s='Upload a module from your computer.'}">
						{l s='Module file'}
					</span>
				</label>
				<div class="col-lg-9">
					<p>
						<input type="file" name="file" />
					</p>
					<p>
						<button class="btn btn-default" type="submit" name="download">
							<i class="icon-upload-alt" ></i>
							{l s='Upload this module'}
						</button>
					</p>
				</div>
			</form>
		</div>
	</div>
{/if}


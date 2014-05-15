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


<div class="panel">
	<h3>{l s="Add a new carrier"}</h3>
	<div id="" class="row">
		<div class="col-lg-12">
			<form action="" id="configuration_form" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group ">
					<label class="control-label col-lg-3">{l s="Create a new carrier"}</label>
					<a class="btn btn-default" href="{$link->getAdminLink('AdminCarrierWizard')}">{l s="Configure a new carrier"}</a>
					<div class="col-lg-9 col-lg-offset-3">
						<p class="help-block">{l s="Use this option to specify your own shipping. Configure free shipping, flat rates."}</p>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
{$modules_list}
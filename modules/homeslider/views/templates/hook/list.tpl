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
<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Slides list' mod='homeslider'}

        <span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn"
           href="{$link->getAdminLink('AdminModules')}&configure=homeslider&addSlide=1">
            <label>
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Add new"
                      data-html="true">
                    <i class="process-icon-new "></i>
                </span>
            </label>
        </a>
	</span>
    </h3>

    <div id="slidesContent" style="width: 400px; margin-top: 30px;">
        <ul id="slides">
            {foreach from=$slides item=slide}
                <li id="slides_{$slide.id_slide}">
                    <strong>#{$slide.id_slide}</strong> {$slide.title}
                    <p style="float: right">
                        {$slide.status}
                        <a class="btn btn-primary"
                           href="{$link->getAdminLink('AdminModules')}&configure=homeslider&id_slide={$slide.id_slide}"> {l s='Edit' mod='homeslider'}</a>
                        <a class="btn btn-danger"
                           href="{$link->getAdminLink('AdminModules')}&configure=homeslider&delete_id_slide={$slide.id_slide}"> {l s='Delete' mod='homeslider'}</a>
                    </p>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
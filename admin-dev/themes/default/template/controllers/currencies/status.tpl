{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				{l s='Status' d='Admin.Global'}
				<div id="currencyStatus" class="pull-right checkbox titatoggle unchecked-red checkbox-slider--b-flat">
					<label>
						<input type="checkbox" {(1 == $status)?'checked="checked"':''}><span></span>
					</label>
				</div>
				<div class="clearfix"></div>
			</div>
			<span class="status disabled {(0 == $status)?'':'hide'}">{l s="This currency is disabled" d='Admin.International.Feature'}</span>
			<span class="status enabled {(1 == $status)?'':'hide'}">{l s="This currency is enabled" d='Admin.International.Feature'}</span>
		</div>
	</div>
</div>

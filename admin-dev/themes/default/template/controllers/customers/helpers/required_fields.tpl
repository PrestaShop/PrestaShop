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
<a class="btn btn-default" href="#" onclick="if ($('.requiredFieldsParameters:visible').length == 0) $('.requiredFieldsParameters').slideDown('slow'); else $('.requiredFieldsParameters').slideUp('slow'); return false;">
  <i class="icon-plus-sign"></i> {l s='Set required fields for this section' d='Admin.Orderscustomers.Feature'}
</a>
<div class="clearfix">&nbsp;</div>
<div style="display:none" class="panel requiredFieldsParameters">
  <h3><i class="icon-asterisk"></i> {l s='Required Fields' d='Admin.Orderscustomers.Feature'}</h3>
  <form name="updateFields" action="{$current|escape:'html':'UTF-8'}&amp;submitFields=1&amp;token={$token|escape:'html':'UTF-8'}" method="post">
    <div class="alert alert-info">
      {l s='Select the fields you would like to be required for this section.' d='Admin.Orderscustomers.Help'}
      <br/>
      {l s='Please make sure you are complying with the opt-in legislation applicable in your country.' d='Admin.Orderscustomers.Help'}
    </div>
    <div class="row">
      <table class="table">
        <thead>
          <tr>
            <th class="fixed-width-xs">
              <input type="checkbox" onclick="checkDelBoxes(this.form, 'fieldsBox[]', this.checked)" class="noborder" name="checkme">
            </th>
            <th><span class="title_box">{l s='Field Name' d='Admin.Orderscustomers.Feature'}</span></th>
          </tr>
        </thead>
        <tbody>
        {foreach $table_fields as $field}
          {if !in_array($field.name, $required_class_fields)}
          <tr>
            <td class="noborder">
              <input type="checkbox" name="fieldsBox[]" value="{$field.name}" {if in_array($field.name, $required_fields)} checked="checked"{/if} />
            </td>
            <td>
              {$field.label}
            </td>
          </tr>
          {/if}
        {/foreach}
        </tbody>
      </table>
    </div>
    <div class="panel-footer">
      <button name="submitFields" type="submit" class="btn btn-default pull-right">
        <i class="process-icon-save "></i> <span>{l s='Save' d='Admin.Actions'}</span>
      </button>
    </div>
  </form>
</div>

/**
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
 */

import PositionsListHandler from './positions-list-handler';

const $ = window.$;

$(() => {
  new PositionsListHandler();

  //
  // Used for the anchor module page
  //
  $('#hook-module-form').find('select[name="id_module"]').change(function(){
    const $this = $(this);
    const $hookSelect = $("select[name='id_hook']");

    if ($this.val() !== 0) {
      $hookSelect.find("option").remove();

      $.ajax({
        type: 'POST',
        url: 'index.php',
        async: true,
        dataType: 'json',
        data: {
          action: 'getPossibleHookingListForModule',
          tab: 'AdminModulesPositions',
          ajax: 1,
          module_id: $this.val(),
          token: token
        },
        success: function (jsonData) {
          if (jsonData.hasError)
              {
                var errors = '';
                for (var error in jsonData.errors)
                  if (error != 'indexOf')
                    errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
              }
            else
              {
                for (var current_hook = 0; current_hook < jsonData.length; current_hook++)
                  {
                    var hook_description = '';
                    if(jsonData[current_hook].description != '')
                      hook_description = ' ('+jsonData[current_hook].description+')';
                    $hookSelect.append('<option value="'+jsonData[current_hook].id_hook+'">'+jsonData[current_hook].name+hook_description+'</option>');
                  }

                $hookSelect.prop('disabled', false);
              }
          }
        });
      }
  });
});

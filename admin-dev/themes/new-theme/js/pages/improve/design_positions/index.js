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

const $ = window.$;

$(() => {
  if ($("#position-filters").length === 0) {
    return;
  }

  const $panelSelection = $("#modules-position-selection-panel");
  const $panelSelectionSingleSelection = $("#modules-position-single-selection");
  const $panelSelectionMultipleSelection = $("#modules-position-multiple-selection");

  const $panelSelectionOriginalY = $panelSelection.offset().top;
  const $panelSelectionOriginalYTopMargin = 140;
  const $showModules = $("#show-modules");
  const $modulesList = $('.modules-position-checkbox');
  const $hookPosition = $("#hook-position");
  const $hookSearch = $("#hook-search");

  $(window).on('scroll', () => {
    const $scrollTop = $(window).scrollTop();
    $panelSelection.css(
      'top',
      $scrollTop < 20
      ? 0
      : $scrollTop - $panelSelectionOriginalY + $panelSelectionOriginalYTopMargin
    );
  });


  $modulesList.on('change', function () {
    const $checkedCount = $modulesList.filter(':checked').length;
    if ($checkedCount === 0) {
      $panelSelection.hide();
      $panelSelectionSingleSelection.hide();
      $panelSelectionMultipleSelection.hide();
    } else if ($checkedCount === 1) {
      $panelSelection.show();
      $panelSelectionSingleSelection.show();
    } else {
      $panelSelection.show();
      $panelSelectionMultipleSelection.show();
      $('#modules-position-selection-count').html($checkedCount);
    }
  });

  $panelSelection.find('button').click(() => {
    $('button[name="unhookform"]').trigger('click');
  });

  const $hooksList = [];
  $('section.hook-panel .hook-name').each(function () {
    const $this = $(this);
    $hooksList.push({
      'title': $this.html(),
      'element': $this,
      'container': $this.parents('.hook-panel')
    });
  });

  $showModules.select2();
  $showModules.on('change', () => {
    modulesPositionFilterHooks();
  });

  $hookPosition.on('change', () => {
    modulesPositionFilterHooks();
  });

  $hookSearch.on('input', function () {
    modulesPositionFilterHooks();
  });

  const modulesPositionFilterHooks = () => {
    const $hookName = $hookSearch.val();
    const $moduleId = $showModules.val();
    const $position = $hookPosition.prop('checked');
    const $regex = new RegExp(`(${$hookName})`, 'gi');

    for (let $id = 0; $id < $hooksList.length; $id++) {
      $hooksList[$id].container.toggle($hookName === '' && $moduleId === 'all');
      $hooksList[$id].element.html($hooksList[$id].title);
      $hooksList[$id].container.find('.module-item').removeClass('highlight');
    }

    if ($hookName !== '' || $moduleId !== 'all') {
      let $hooksToShowFromModule = $();
      let $hooksToShowFromHookName = $();
      let $currentHooks;
      let $start;

      for (let $id = 0; $id < $hooksList.length; $id++) {
        if ($moduleId !== 'all') {
          $currentHooks = $(`.module-position-${$moduleId}`);
          if ($currentHooks.length > 0) {
            $hooksToShowFromModule = $hooksToShowFromModule.add($hooksList[$id].container);
            $currentHooks.addClass('highlight');
          }
        }

        if ($hookName !== '') {
          $start = $hooksList[$id].title.toLowerCase().search($hookName.toLowerCase());
          if ($start !== -1) {
            $hooksToShowFromHookName = $hooksToShowFromHookName.add($hooksList[$id].container);
            $hooksList[$id].element.html(
              $hooksList[$id].title.replace(
                $regex,
                '<span class="highlight">$1</span>'
              )
            );
          }
        }

        if ($moduleId === 'all' && $hookName !== '') {
          $hooksToShowFromHookName.show();
        } else if ($hookName === '' && $moduleId !== 'all') {
          $hooksToShowFromModule.show();
        } else {
          $hooksToShowFromHookName.filter($hooksToShowFromModule).show();
        }
      }
    }

    if (!$position) {
      for (let $id = 0; $id < $hooksList.length; $id++) {
        if ($hooksList[$id].container.is('.hook-position')) {
          $hooksList[$id].container.hide();
        }
      }
    }
  };

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

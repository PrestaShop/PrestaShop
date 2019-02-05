/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

class PositionsListHandler {
  constructor() {
    if ($("#position-filters").length === 0) {
      return;
    }

    const self = this;
    self.$panelSelection = $("#modules-position-selection-panel");
    self.$panelSelectionSingleSelection = $("#modules-position-single-selection");
    self.$panelSelectionMultipleSelection = $("#modules-position-multiple-selection");

    self.$panelSelectionOriginalY = self.$panelSelection.offset().top;
    self.$showModules = $("#show-modules");
    self.$modulesList = $('.modules-position-checkbox');
    self.$hookPosition = $("#hook-position");
    self.$hookSearch = $("#hook-search");
    self.$modulePositionsForm = $('#module-positions-form');
    self.$moduleUnhookButton = $('#unhook-button-position-bottom');
    self.$moduleButtonsUpdate = $('.module-buttons-update .btn');

    self.handleList();
    self.handleSortable();

    $('input[name="form[general][enable_tos]"]').on('change', () => self.handle());
  }

  /**
   * Handle all events for Design -> Positions List
   */
  handleList() {
    const self = this;

    $(window).on('scroll', () => {
      const $scrollTop = $(window).scrollTop();
      self.$panelSelection.css(
        'top',
        $scrollTop < 20 ? 0 : $scrollTop - self.$panelSelectionOriginalY
      );
    });

    self.$modulesList.on('change', function () {
      const $checkedCount = self.$modulesList.filter(':checked').length;

      if ($checkedCount === 0) {
        self.$moduleUnhookButton.hide();
        self.$panelSelection.hide();
        self.$panelSelectionSingleSelection.hide();
        self.$panelSelectionMultipleSelection.hide();
      } else if ($checkedCount === 1) {
        self.$moduleUnhookButton.show();
        self.$panelSelection.show();
        self.$panelSelectionSingleSelection.show();
        self.$panelSelectionMultipleSelection.hide();
      } else {
        self.$moduleUnhookButton.show();
        self.$panelSelection.show();
        self.$panelSelectionSingleSelection.hide();
        self.$panelSelectionMultipleSelection.show();
        $('#modules-position-selection-count').html($checkedCount);
      }
    });

    self.$panelSelection.find('button').click(() => {
      $('button[name="unhookform"]').trigger('click');
    });

    self.$hooksList = [];
    $('section.hook-panel .hook-name').each(function () {
      const $this = $(this);
      self.$hooksList.push({
        'title': $this.html(),
        'element': $this,
        'container': $this.parents('.hook-panel')
      });
    });

    self.$showModules.select2();
    self.$showModules.on('change', () => {
      self.modulesPositionFilterHooks();
    });

    self.$hookPosition.on('change', () => {
      self.modulesPositionFilterHooks();
    });

    self.$hookSearch.on('input', () => {
      self.modulesPositionFilterHooks();
    });

    $('.hook-checker').on('click', function() {
      $(`.hook${$(this).data('hook-id')}`).prop('checked', $(this).prop('checked'));
    });

    self.$modulesList.on('click', function() {
      $(`#Ghook${$(this).data('hook-id')}`).prop(
        'checked',
        $(`.hook${$(this).data('hook-id')}:not(:checked)`).length === 0
      );
    });

    self.$moduleButtonsUpdate.on('click', function() {
      const $btn = $(this);
      const $current = $btn.closest('.module-item');
      let $destination;

      if ($btn.data('way')) {
        $destination = $current.next('.module-item');
      } else {
        $destination = $current.prev('.module-item');
      }

      if ($destination.length === 0) {
        return false;
      }

      if ($btn.data('way')) {
        $current.insertAfter($destination);
      } else {
        $current.insertBefore($destination);
      }

      self.updatePositions(
        {
          hookId: $btn.data('hook-id'),
          moduleId: $btn.data('module-id'),
          way: $btn.data('way'),
          positions: [],
        },
        $btn.closest('ul')
      );

      return false;
    });
  }

  /**
   * Handle sortable events
   */
  handleSortable() {
    const self = this;

    $('.sortable').sortable({
      forcePlaceholderSize: true,
      start: function(e, ui) {
        $(this).data('previous-index', ui.item.index());
      },
      update: function($event, ui) {
        const [ hookId, moduleId ] = ui.item.attr('id').split('_');

        const $data = {
          hookId,
          moduleId,
          way: ($(this).data('previous-index') < ui.item.index()) ? 1 : 0,
          positions: [],
        };

        self.updatePositions(
          $data,
          $($event.target)
        );
      },
    });
  }

  updatePositions($data, $list) {
    const self = this;
    $.each($list.children(), function(index, element) {
      $data.positions.push($(element).attr('id'));
    });

    $.ajax({
      type: 'POST',
      headers: {'cache-control': 'no-cache'},
      url: self.$modulePositionsForm.data('update-url'),
      data: $data,
      success: () => {
        let start = 0;
        $.each($list.children(), function(index, element) {
          console.log($(element).find('.index-position'));
          $(element).find('.index-position').html(++start);
        });

        window.showSuccessMessage(window.update_success_msg);
      }
    });
  }

  /**
   * Filter hooks / modules search and everything
   * about hooks positions.
   */
  modulesPositionFilterHooks() {
    const self = this;
    const $hookName = self.$hookSearch.val();
    const $moduleId = self.$showModules.val();
    const $regex = new RegExp(`(${$hookName})`, 'gi');

    for (let $id = 0; $id < self.$hooksList.length; $id++) {
      self.$hooksList[$id].container.toggle($hookName === '' && $moduleId === 'all');
      self.$hooksList[$id].element.html(self.$hooksList[$id].title);
      self.$hooksList[$id].container.find('.module-item').removeClass('highlight');
    }

    // Have select a hook name or a module id
    if ($hookName !== '' || $moduleId !== 'all') {
      // Prepare set of matched elements
      let $hooksToShowFromModule = $();
      let $hooksToShowFromHookName = $();
      let $currentHooks;
      let $start;

      for (let $id = 0; $id < self.$hooksList.length; $id++) {
        // Prepare highlight when one module is selected
        if ($moduleId !== 'all') {
          $currentHooks = self.$hooksList[$id].container.find(`.module-position-${$moduleId}`);
          if ($currentHooks.length > 0) {
            $hooksToShowFromModule = $hooksToShowFromModule.add(self.$hooksList[$id].container);
            $currentHooks.addClass('highlight');
          }
        }

        // Prepare highlight when there is a hook name
        if ($hookName !== '') {
          $start = self.$hooksList[$id].title.toLowerCase().search($hookName.toLowerCase());
          if ($start !== -1) {
            $hooksToShowFromHookName = $hooksToShowFromHookName.add(self.$hooksList[$id].container);
            self.$hooksList[$id].element.html(
              self.$hooksList[$id].title.replace(
                $regex,
                '<span class="highlight">$1</span>'
              )
            );
          }
        }
      }

      // Nothing selected
      if ($moduleId === 'all' && $hookName !== '') {
        $hooksToShowFromHookName.show();
      } else if ($hookName === '' && $moduleId !== 'all') { // Have no hook bug have a module
        $hooksToShowFromModule.show();
      } else { // Both selected
        $hooksToShowFromHookName.filter($hooksToShowFromModule).show();
      }
    }

    if (!self.$hookPosition.prop('checked')) {
      for (let $id = 0; $id < self.$hooksList.length; $id++) {
        if (self.$hooksList[$id].container.is('.hook-position')) {
          self.$hooksList[$id].container.hide();
        }
      }
    }
  };
}

export default PositionsListHandler;

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

const {$} = window;

class PositionsListHandler {
  $panelSelection: JQuery;

  $panelSelectionSingleSelection: JQuery;

  $panelSelectionMultipleSelection: JQuery;

  $panelSelectionOriginalY: number;

  $showModules: JQuery;

  $modulesList: JQuery;

  $hookPosition: JQuery;

  $hookSearch: JQuery;

  $modulePositionsForm: JQuery;

  $moduleUnhookButton: JQuery;

  $moduleButtonsUpdate: JQuery;

  $transplantModuleButton: JQuery;

  $hooksList: Array<Record<string, any>>;

  constructor() {
    this.$panelSelection = $('#modules-position-selection-panel');
    this.$panelSelectionSingleSelection = $(
      '#modules-position-single-selection',
    );
    this.$panelSelectionMultipleSelection = $(
      '#modules-position-multiple-selection',
    );
    const $alertMessage = $('#content-message-box + .alert');

    this.$panelSelectionOriginalY = <number> this.$panelSelection.offset()?.top;
    if ($alertMessage.length > 0) {
      this.$panelSelectionOriginalY += <number> $alertMessage.outerHeight();
    }
    this.$showModules = $('#show-modules');
    this.$modulesList = $('.modules-position-checkbox');
    this.$hookPosition = $('#hook-position');
    this.$hookSearch = $('#hook-search');
    this.$modulePositionsForm = $('#module-positions-form');
    this.$moduleUnhookButton = $('#unhook-button-position-bottom');
    this.$moduleButtonsUpdate = $('.module-buttons-update .btn');
    this.$hooksList = [];
    this.$transplantModuleButton = $('.transplant-module-button');

    this.handleList();
    this.handleSortable();

    // Trigger some events for reloading all previous hooks
    this.$modulesList.trigger('change');
    this.$modulesList.trigger('scroll');

    $('input[name="general[enable_tos]"]').on('change', () => {
      this.handleList();
      this.handleSortable();
    });
  }

  /**
   * Handle all events for Design -> Positions List
   */
  handleList(): void {
    const self = this;

    $(window).on('scroll', () => {
      const $scrollTop = <number>$(window).scrollTop();
      self.$panelSelection.css(
        'top',
        $scrollTop < 20 ? 0 : $scrollTop - self.$panelSelectionOriginalY,
      );
    });

    self.$modulesList.on('change', () => {
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
        $('#modules-position-selection-count').html(
          <string>(<unknown>$checkedCount),
        );
      }
    });

    self.$panelSelection.find('button').on('click', () => {
      $('button[name="unhookform"]').trigger('click');
    });

    self.$hooksList = [];
    $('section.hook-panel .hook-name').each(function () {
      const $this = $(this);
      self.$hooksList.push({
        title: $this.html(),
        element: $this,
        container: $this.parents('.hook-panel'),
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

    // Filter modules list on the page load
    self.modulesPositionFilterHooks();

    self.$hookSearch.on('keypress', (e) => {
      const keyCode = e.keyCode || e.which;

      return keyCode !== 13;
    });

    $('.hook-checker').on('click', function () {
      $(`.hook${$(this).data('hook-id')}`).prop(
        'checked',
        $(this).prop('checked'),
      );
    });

    self.$modulesList.on('click', function () {
      $(`#Ghook${$(this).data('hook-id')}`).prop(
        'checked',
        $(`.hook${$(this).data('hook-id')}:not(:checked)`).length === 0,
      );
    });

    self.$moduleButtonsUpdate.on('click', function () {
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
        $btn.closest('ul'),
      );

      return false;
    });
  }

  /**
   * Handle sortable events
   */
  handleSortable(): void {
    const self = this;

    $('.sortable').sortable({
      forcePlaceholderSize: true,
      start(e: JQueryEventObject, ui: Record<string, any>) {
        $(this).data('previous-index', ui.item.index());
      },
      update($event: JQueryEventObject, ui: Record<string, any>) {
        const [hookId, moduleId] = ui.item.attr('id').split('_');

        const $data = {
          hookId,
          moduleId,
          way: $(this).data('previous-index') < ui.item.index() ? 1 : 0,
          positions: [],
        };

        self.updatePositions($data, $($event.target));
      },
    });
  }

  updatePositions($data: Record<string, any>, $list: JQuery<Element>): void {
    const self = this;
    $.each($list.children(), (index, element) => {
      $data.positions.push($(element).attr('id'));
    });

    $.ajax({
      type: 'POST',
      headers: {'cache-control': 'no-cache'},
      url: self.$modulePositionsForm.data('update-url'),
      data: $data,
      success: () => {
        let start = 0;
        $.each($list.children(), (index, element) => {
          start += 1;
          $(element)
            .find('.index-position')
            .html(<string>(<unknown>start));
        });

        window.showSuccessMessage(window.update_success_msg);
      },
    });
  }

  /**
   * Filter hooks / modules search and everything
   * about hooks positions.
   */
  modulesPositionFilterHooks(): void {
    const self = this;
    const $hookName = <string>self.$hookSearch.val();
    const $moduleId = <string>self.$showModules.val();
    const $regex = new RegExp(`(${$hookName})`, 'gi');

    // Update "Transplant module" button
    const transplantModuleHref = new URL(this.$transplantModuleButton.prop('href'));
    transplantModuleHref.searchParams.set('show_modules', $moduleId);
    this.$transplantModuleButton.attr('href', transplantModuleHref.toString());

    const isVisible: boolean = $hookName === '' && $moduleId === 'all';

    for (let $id = 0; $id < self.$hooksList.length; $id += 1) {
      self.$hooksList[$id].container.toggleClass('hook-visible', isVisible);
      self.$hooksList[$id].container.toggle(isVisible);
      self.$hooksList[$id].element.html(self.$hooksList[$id].title);
      self.$hooksList[$id].container
        .find('.module-item')
        .removeClass('highlight');
    }

    // Have select a hook name or a module id
    if ($hookName !== '' || $moduleId !== 'all') {
      // Prepare set of matched elements
      let $hooksToShowFromModule = $();
      let $hooksToShowFromHookName = $();
      let $currentHooks;
      let $start;

      for (let $id = 0; $id < self.$hooksList.length; $id += 1) {
        // Prepare highlight when one module is selected
        if ($moduleId !== 'all') {
          $currentHooks = self.$hooksList[$id].container.find(
            `.module-position-${$moduleId}`,
          );
          if ($currentHooks.length > 0) {
            $hooksToShowFromModule = $hooksToShowFromModule.add(
              self.$hooksList[$id].container,
            );
            $currentHooks.addClass('highlight');
          }
        }

        // Prepare highlight when there is a hook name
        if ($hookName !== '') {
          $start = self.$hooksList[$id].title
            .toLowerCase()
            .search($hookName.toLowerCase());
          if ($start !== -1) {
            $hooksToShowFromHookName = $hooksToShowFromHookName.add(
              self.$hooksList[$id].container,
            );
            self.$hooksList[$id].element.html(
              self.$hooksList[$id].title.replace(
                $regex,
                '<span class="highlight">$1</span>',
              ),
            );
          }
        }
      }

      // Nothing selected
      if ($moduleId === 'all' && $hookName !== '') {
        $hooksToShowFromHookName.toggleClass('hook-visible', true);
        $hooksToShowFromHookName.show();
      } else if ($hookName === '' && $moduleId !== 'all') {
        // Have no hook bug have a module
        $hooksToShowFromModule.toggleClass('hook-visible', true);
        $hooksToShowFromModule.show();
      } else {
        // Both selected
        $hooksToShowFromHookName.filter($hooksToShowFromModule).toggleClass('hook-visible', true);
        $hooksToShowFromHookName.filter($hooksToShowFromModule).show();
      }
    }

    if (!self.$hookPosition.prop('checked')) {
      for (let $id = 0; $id < self.$hooksList.length; $id += 1) {
        if (self.$hooksList[$id].container.is('.hook-position')) {
          self.$hooksList[$id].container.toggleClass('hook-visible', false);
          self.$hooksList[$id].container.hide();
        }
      }
    }
  }
}

export default PositionsListHandler;

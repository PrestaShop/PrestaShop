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
    self.$panelSelectionOriginalYTopMargin = 140;
    self.$showModules = $("#show-modules");
    self.$modulesList = $('.modules-position-checkbox');
    self.$hookPosition = $("#hook-position");
    self.$hookSearch = $("#hook-search");


    self.handleList();
    self.handleSortable();

    $('input[name="form[general][enable_tos]"]').on('change', () => self.handle());
  }

  handleList() {
    const self = this;
    $(window).on('scroll', () => {
      const $scrollTop = $(window).scrollTop();
      self.$panelSelection.css(
        'top',
        $scrollTop < 20 ?
        0 :
        $scrollTop - self.$panelSelectionOriginalY + self.$panelSelectionOriginalYTopMargin
      );
    });


    self.$modulesList.on('change', function () {
      const $checkedCount = self.$modulesList.filter(':checked').length;
      if ($checkedCount === 0) {
        self.$panelSelection.hide();
        self.$panelSelectionSingleSelection.hide();
        self.$panelSelectionMultipleSelection.hide();
      } else if ($checkedCount === 1) {
        self.$panelSelection.show();
        self.$panelSelectionSingleSelection.show();
      } else {
        self.$panelSelection.show();
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
      this.modulesPositionFilterHooks();
    });

    self.$hookPosition.on('change', () => {
      this.modulesPositionFilterHooks();
    });

    self.$hookSearch.on('input', () => {
      this.modulesPositionFilterHooks();
    });
  }

  handleSortable() {
	  $('.sortable').sortable({
		  forcePlaceholderSize: true
	  }).bind('sortupdate', function(e, ui) {
		  var ids = ui.item.attr('id').split('_');
		  var way = (ui.start_index < ui.end_index)? 1 : 0;
		  var data = ids[0]+'[]=';

		  $.each(e.target.children, function(index, element) {
			  data += '&'+ids[0]+'[]='+$(element).attr('id');
		  });

		  $.ajax({
			  type: 'POST',
			  headers: { "cache-control": "no-cache" },
			  async: false,
			  url: currentIndex + '&token=' + token + '&' + 'rand=' + new Date().getTime(),
			  data: data + '&action=updatePositions&id_hook='+ids[0]+'&id_module='+ids[1]+'&way='+way+'&ajax=1' ,
			  success: function(data) {
				  let start = 0;

				  $.each(e.target.children, function(index, element) {
					  $(element).find('.index-position').html(++start);
				  });

				  showSuccessMessage(update_success_msg);
			  }
		  });
	  });
  }

  modulesPositionFilterHooks() {
    const self = this;
    const $hookName = self.$hookSearch.val();
    const $moduleId = self.$showModules.val();
    const $position = self.$hookPosition.prop('checked');
    const $regex = new RegExp(`(${$hookName})`, 'gi');

    for (let $id = 0; $id < self.$hooksList.length; $id++) {
      self.$hooksList[$id].container.toggle($hookName === '' && $moduleId === 'all');
      self.$hooksList[$id].element.html(self.$hooksList[$id].title);
      self.$hooksList[$id].container.find('.module-item').removeClass('highlight');
    }

    if ($hookName !== '' || $moduleId !== 'all') {
      let $hooksToShowFromModule = $();
      let $hooksToShowFromHookName = $();
      let $currentHooks;
      let $start;

      for (let $id = 0; $id < self.$hooksList.length; $id++) {
        if ($moduleId !== 'all') {
          $currentHooks = $(`.module-position-${$moduleId}`);
          if ($currentHooks.length > 0) {
            $hooksToShowFromModule = $hooksToShowFromModule.add(self.$hooksList[$id].container);
            $currentHooks.addClass('highlight');
          }
        }

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
      for (let $id = 0; $id < self.$hooksList.length; $id++) {
        if (self.$hooksList[$id].container.is('.hook-position')) {
          self.$hooksList[$id].container.hide();
        }
      }
    }
  };
}

export default PositionsListHandler;

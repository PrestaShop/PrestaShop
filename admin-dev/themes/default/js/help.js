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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

$(() => {
  let storage = false;
  const psDocsDomain = 'https://help.prestashop-project.org';

  if (typeof (getStorageAvailable) !== 'undefined') {
    // eslint-disable-next-line
    storage = getStorageAvailable();
  }

  window.initHelp = function () {
    $('#main').addClass('helpOpen');
    // first time only
    if ($('#help-container').length === 0) {
      // add css
      $('head').append(`<link href="${psDocsDomain}/css/help.css" rel="stylesheet">`);
      // add container
      $('#main').after('<div id="help-container"></div>');
    }
    // init help (it use a global javascript variable to get actual controller)
    // eslint-disable-next-line
    pushContent(help_class_name);
    $('#help-container').on('click', '.popup', (e) => {
      e.preventDefault();
      if (storage) storage.setItem('helpOpen', false);
      $('.toolbarBox a.btn-help').trigger('click');
      window.open(
        // eslint-disable-next-line
        `index.php?controller=${help_class_name}?token=${token}&ajax=1&action=OpenHelp`,
        'helpWindow',
        'width=450, height=650, scrollbars=yes',
      );
    });
  };

  // init
  $('.toolbarBox a.btn-help').on('click', (e) => {
    e.preventDefault();
    if (!$('#main').hasClass('helpOpen') && document.body.clientWidth > 1200) {
      if (storage) storage.setItem('helpOpen', true);
      $('.toolbarBox a.btn-help i').removeClass('process-icon-help').addClass('process-icon-loading');
      window.initHelp();
    } else if (!$('#main').hasClass('helpOpen') && document.body.clientWidth < 1200) {
      window.open(
        // eslint-disable-next-line
        `index.php?controller=${help_class_name}?token=${token}&ajax=1&action=OpenHelp`,
        'helpWindow',
        'width=450, height=650, scrollbars=yes',
      );
    } else {
      $('#main').removeClass('helpOpen');
      $('#help-container').html('');
      $('.toolbarBox a.btn-help i').removeClass('process-icon-close').addClass('process-icon-help');
      if (storage) storage.setItem('helpOpen', false);
    }
  });

  // Help persistency
  if (storage && storage.getItem('helpOpen') === 'true') {
    $('a.btn-help').trigger('click');
  }

  // get content
  function getHelp(pageController) {
    // eslint-disable-next-line
    const request = encodeURIComponent(`getHelp=${pageController}&version=${_PS_VERSION_}&language=${iso_user}`);
    const d = new $.Deferred();
    $.ajax({
      url: `${psDocsDomain}/api/?request=${request}`,
      jsonp: 'callback',
      dataType: 'jsonp',
      success(data) {
        if (window.isCleanHtml(data)) {
          $('#help-container').html(data);
          d.resolve();
        }
      },
    });
    return d.promise();
  }

  // update content
  function pushContent(target) {
    $('#help-container').removeClass('openHelpNav');
    $('#help-container').html('');
    // @todo: track event
    getHelp(target);
  }
});

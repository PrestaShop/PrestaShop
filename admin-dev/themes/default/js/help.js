/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

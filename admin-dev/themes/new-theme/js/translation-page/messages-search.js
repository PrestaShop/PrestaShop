import Jets from 'jets/jets';
import $ from 'jquery';

export default function () {
  $(() => {
    const searchSelector = '.search-translation';
    $(searchSelector + ' form').submit(function (event) {
      $('#jetsContent form').addClass('hide');

      const keywords = $('#jetsSearch').val().toLowerCase();
      const jetsSelector = '#jetsContent > [data-jets*="' + keywords + '"]';

      if (0 === $(jetsSelector).length) {
        var notificationElement = $(searchSelector + '> .alert')[0];
        $(notificationElement).removeClass('hide');
        setTimeout(function () {
          $(notificationElement).addClass('hide');
        }, 2000);
      } else {
        $(jetsSelector).removeClass('hide');
      }

      event.preventDefault();

      return false;
    });

    $(searchSelector + ' input[type=reset]').click(function () {
      $('#jetsSearch').val('');
      $('#jetsContent form').addClass('hide');
    })
  });

  if ($('#jetsSearch').length > 0) {
    return new Jets({
      searchTag: '#jetsSearch',
      contentTag: '#jetsContent',
      callSearchManually: true,
      manualContentHandling: function (tag) {
        // Search for translation keys and translation values
        return $(tag).find('verbatim')[0].innerText + $(tag).find('textarea')[0].value;
      }
    });
  }
}

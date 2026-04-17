/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import Jets from 'jets/jets';

export default function (): typeof Jets | boolean {
  $(() => {
    const searchSelector = '.search-translation';
    $(`${searchSelector} form`).on('submit', (event) => {
      event.preventDefault();

      $('#jetsContent form').addClass('hide');
      const $jetsSearch = <string>$('#jetsSearch').val();

      const keywords = $jetsSearch?.toLowerCase();
      const jetsSelector = `#jetsContent > [data-jets*="${keywords}"]`;

      if ($(jetsSelector).length === 0) {
        const notificationElement = $(`${searchSelector}> .alert`)[0];
        $(notificationElement).removeClass('hide');
        setTimeout(() => {
          $(notificationElement).addClass('hide');
        }, 2000);
      } else {
        $(jetsSelector).removeClass('hide');
      }

      if ($(jetsSelector).length) {
        $('.js-results')
          .show()
          .addClass('card')
          .find('h2')
          .removeClass('hide');
      }

      return false;
    });

    $(`${searchSelector} input[type=reset]`).on('click', (event) => {
      event.preventDefault();

      $('#jetsSearch').val('');
      $('#jetsContent form').addClass('hide');

      return false;
    });
  });

  if ($('#jetsSearch').length > 0) {
    return new Jets({
      searchTag: '#jetsSearch',
      contentTag: '#jetsContent',
      callSearchManually: true,
      manualContentHandling(tag: HTMLElement) {
        // Search for translation keys and translation values
        return (
          $(tag).find('.verbatim')[0].innerText
          + $(tag).find('textarea')[0].value
        );
      },
    });
  }

  return false;
}

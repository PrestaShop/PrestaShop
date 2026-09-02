/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
// @ts-ignore-next-line
import Jets from 'jets/jets';

export default function (search: typeof Jets): void {
  $('.reset-translation-value').each((buttonIndex, button) => {
    const $editTranslationForm = $(button).parents('form');
    const defaultTranslationValue = $editTranslationForm
      .find('*[name=default]')
      .val();

    $(button).on('click', () => {
      $editTranslationForm
        .find('*[name=translation_value]')
        .val(<string>defaultTranslationValue);
      $editTranslationForm.submit();
    });
  });

  const showFlashMessageOnEdit = (form: HTMLElement) => {
    $(form).on('submit', (event) => {
      event.preventDefault();

      const $editTranslationForm = $(event.target);
      const url = $editTranslationForm.attr('action');

      $.post(<string>url, $editTranslationForm.serialize(), (response) => {
        let flashMessage: JQuery;

        if (response.successful_update) {
          flashMessage = $editTranslationForm.find('.alert-info');

          // Propagate edition
          const hash = $editTranslationForm.data('hash');
          const $editTranslationForms = $(`[data-hash=${hash}]`);
          const $translationValueFields = $(
            $editTranslationForms.find('textarea'),
          );
          $translationValueFields.val(
            <string>$editTranslationForm.find('textarea').val(),
          );

          // Refresh search index
          $editTranslationForms.removeAttr('data-jets');
          search.update();
        } else {
          flashMessage = $editTranslationForm.find('.alert-danger');
        }

        flashMessage.removeClass('hide');

        setTimeout(() => {
          flashMessage.addClass('hide');
        }, 4000);
      });

      return false;
    });
  };

  $('#jetsContent form, .translation-domain form').each((formIndex, form) => {
    showFlashMessageOnEdit(form);
  });
}

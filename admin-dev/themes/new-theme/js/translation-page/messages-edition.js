import $ from 'jquery';

export default function (search) {
  $('.reset-translation-value').each((buttonIndex, button) => {
    let editTranslationForm = $(button).parents('form');
    let defaultTranslationValue = editTranslationForm.find('*[name=default]').val();

    $(button).click(() => {
      editTranslationForm.find('*[name=translation_value]').val(defaultTranslationValue);
      editTranslationForm.submit();
    });
  });

  let showFlashMessageOnEdit = (form) => {
    $(form).submit((event) => {
      let $editTranslationForm = $(event.target);
      let url = $editTranslationForm.attr('action');

      $.post(url, $editTranslationForm.serialize(), (response) => {
        let flashMessage;
        if (response['successful_update']) {
          flashMessage = $editTranslationForm.find('.alert-info');

          // Propagate edition
          let hash = $editTranslationForm.data('hash');
          let $editTranslationForms = $('[data-hash=' + hash + ']');
          let $translationValueFields = $($editTranslationForms.find('textarea'));
          $translationValueFields.val($editTranslationForm.find('textarea').val());
      
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

      event.preventDefault();

      return false;
    })
  };

  $('.search-translation form, .translation-domain form').each((formIndex, form) => {
    showFlashMessageOnEdit(form);
  });
}

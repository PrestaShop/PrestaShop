import $ from 'jquery';

$(() => {
    $('.show-translation-messages').each((buttonIndex, button) => {
        $(button).click((event) => {
            let showTranslationsFormButton = $(event.target);

            let translationDomain = showTranslationsFormButton.parent();
            let editTranslationForms = translationDomain.find('form');
            let hideTranslationsFormsButtons = translationDomain.find('.hide-translation-messages');

            $(editTranslationForms).removeClass('hide');
            $(hideTranslationsFormsButtons).removeClass('hide');
            showTranslationsFormButton.addClass('hide');
        });
    });

    $('.hide-translation-messages').each((buttonIndex, button) => {
        $(button).click((event) => {
            let hideTranslationsFormButton = $(event.target);

            let translationDomain = hideTranslationsFormButton.parent();
            let editTranslationForms = translationDomain.find('form');
            let showTranslationsFormsButtons = translationDomain.find('.show-translation-messages');

            $(editTranslationForms).addClass('hide');
            $(showTranslationsFormsButtons).removeClass('hide');
            hideTranslationsFormButton.addClass('hide');
        });
    });

    $('.reset-translation-value').each((buttonIndex, button) => {
        let editTranslationForm = $(button).parents('form');
        let defaultTranslationValue = editTranslationForm.find('*[name=default]').val();

        $(button).click(() => {
            editTranslationForm.find('*[name=translation_value]').val(defaultTranslationValue);
            editTranslationForm.submit();
        });
    });

    $('.translation-domain form').each((formIndex, form) => {
        $(form).submit((event) => {
            let editTranslationForm = $(event.target);
            let url = editTranslationForm.attr('action');

            $.post(url, editTranslationForm.serialize(), (response) => {
                let flashMessage;
                if (response['successful_update']) {
                   flashMessage = editTranslationForm.find('.alert-info');
                } else {
                   flashMessage = editTranslationForm.find('.alert-danger');
                }

                flashMessage.removeClass('hide');

                setTimeout(() => {
                  flashMessage.addClass('hide');
                }, 4000);
            });

            event.preventDefault();

            return false;
        })
    });
});

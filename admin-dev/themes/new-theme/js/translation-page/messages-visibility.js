import $ from 'jquery';

export default function() {
    var buttonSuffix = 'translation-messages';
    var hideClass = 'hide';

    (() => {
        $('.show-' + buttonSuffix).each((buttonIndex, button) => {
            $(button).click((event) => {
                let showTranslationsFormButton = $(event.target);

                let translationDomain = showTranslationsFormButton.parent();
                let editTranslationForms = translationDomain.find('form');
                let hideTranslationsFormsButtons = translationDomain.find('.hide-' + buttonSuffix);

                $(editTranslationForms).removeClass(hideClass);
                $(hideTranslationsFormsButtons).removeClass(hideClass);
                showTranslationsFormButton.addClass(hideClass);
                translationDomain.find('nav').removeClass(hideClass);
                translationDomain.find('.go-to-pagination-bar').removeClass(hideClass);
            });
        });

        $('.hide-' + buttonSuffix).each((buttonIndex, button) => {
            $(button).click((event) => {
                let hideTranslationsFormButton = $(event.target);

                let translationDomain = hideTranslationsFormButton.parent();
                let editTranslationForms = translationDomain.find('form');
                let showTranslationsFormsButtons = translationDomain.find('.show-' + buttonSuffix);

                $(editTranslationForms).addClass(hideClass);
                $(showTranslationsFormsButtons).removeClass(hideClass);
                hideTranslationsFormButton.addClass(hideClass);
                translationDomain.find('nav').addClass(hideClass);
                translationDomain.find('.go-to-pagination-bar').addClass(hideClass);
            });
        });
    })();
}
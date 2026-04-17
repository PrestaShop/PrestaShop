/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default function (callback: () => void): void {
  const buttonSuffix = 'translation-messages';
  const hideClass = 'hide';

  function hideCurrentTranslationForms(formsContainer: JQuery) {
    const currentTranslationForms = formsContainer.find('.translation-forms');

    if (currentTranslationForms.length > 0) {
      const hiddenFormsContainer = $(
        `[data-parent-of="${currentTranslationForms.attr('id')}"]`,
      );
      currentTranslationForms.find('form').addClass(hideClass);
      hiddenFormsContainer.append(currentTranslationForms);
    }
  }

  function hideCurrentNavigationBar(navigationContainer: JQuery) {
    const currentNavigationBar = navigationContainer.find('nav');

    if (currentNavigationBar.length > 0) {
      const navIdentifier = currentNavigationBar.attr('data-navigation-of');
      const hiddenNavigationBarContainer = $(
        `[data-navigation-parent-of="${navIdentifier}"]`,
      );
      currentNavigationBar.addClass(hideClass);
      hiddenNavigationBarContainer.append(currentNavigationBar);
    }
  }

  function highlightDomainFirstPart(showTranslationsFormButton: JQuery) {
    $('.domain-first-part').removeClass('active');
    const domainFirstPart = $(
      $(showTranslationsFormButton.parents('.subdomains')[0])
        .prevAll()
        .filter('.domain-first-part'),
    )[0];
    $(domainFirstPart).addClass('active');
  }

  function updateDomainTitle(editTranslationForms: JQuery) {
    const domainPart = editTranslationForms
      .parents('.translation-domain')
      .prev();
    const missingTranslationWarning = domainPart.find(
      '.missing-translations-short-message',
    );
    const warningPlaceholder = $('#domain .missing-translations');
    const totalPlaceholder = $('#domain .total-expressions');
    const separator = $('#domain .separator');
    totalPlaceholder.text(editTranslationForms.data('total-translations'));
    if (missingTranslationWarning.length > 0) {
      warningPlaceholder.text(missingTranslationWarning.text());
      separator.removeClass('hide');
    } else {
      warningPlaceholder.text('');
      separator.addClass('hide');
    }
    separator.first().removeClass('hide');

    const domain = $('#domain .name');
    const title = editTranslationForms.attr('data-domain');
    domain.text(<string>title);
  }

  function updateMissingTranslationsMessages(title: JQuery) {
    const missingTranslationsMessage = title.find(
      '.missing-translations-long-message',
    );

    if (missingTranslationsMessage.text().length > 0) {
      $('.translation-domains .missing-translations-paragraph').text(
        missingTranslationsMessage.text(),
      );
    } else {
      $('.translation-domains .missing-translations-paragraph').text('');
    }
  }

  function updateNavigationBar(
    translationDomain: JQuery,
    editTranslationForms: JQuery,
  ) {
    const navigationContainer = $('.navbar-container:first');
    const navigation = translationDomain.find('nav');

    navigation
      .parent()
      .attr(
        'data-navigation-parent-of',
        <string>editTranslationForms.attr('id'),
      );
    navigation.attr(
      'data-navigation-of',
      <string>editTranslationForms.attr('id'),
    );

    hideCurrentNavigationBar(navigationContainer);

    navigationContainer.append(navigation);
    $(navigationContainer.find('nav')).removeClass(hideClass);

    $('.forms-container + .navbar-container').remove();
    $('.forms-container').after(navigationContainer.clone());
  }

  function updateEditTranslationForms(
    formsContainer: JQuery,
    editTranslationForms: JQuery,
  ) {
    hideCurrentTranslationForms(formsContainer);

    formsContainer.append(editTranslationForms);
    $(formsContainer.find('.translation-forms form')).removeClass(hideClass);
  }

  (() => {
    $(`.show-${buttonSuffix}`).each((buttonIndex, button) => {
      $(button).on('click', (event) => {
        const showTranslationsFormButton = $(event.target);

        const translationDomain = showTranslationsFormButton.parent();
        const editTranslationForms = translationDomain.find(
          '.translation-forms',
        );
        const formsContainer = $('.forms-container');

        if (editTranslationForms.length === 0) {
          return false;
        }

        highlightDomainFirstPart(showTranslationsFormButton);

        updateDomainTitle(editTranslationForms);

        updateNavigationBar(translationDomain, editTranslationForms);
        updateEditTranslationForms(formsContainer, editTranslationForms);

        callback();
        return true;
      });
    });

    $('.domain-part .delegate-toggle-messages').each(
      (togglerIndex, toggler) => {
        $(toggler).on('click', (event) => {
          let title = $(event.target);

          if (!$(event.target).hasClass('domain-part')) {
            title = $(event.target).parent();
          }

          updateMissingTranslationsMessages(title);

          const translationDomain = title.next();
          const showMessagesButton = translationDomain.find(
            `.show-${buttonSuffix}`,
          );

          showMessagesButton.click();
        });
      },
    );
  })();
}

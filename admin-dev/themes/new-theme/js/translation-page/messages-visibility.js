/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

export default function(callback) {
  var buttonSuffix = 'translation-messages';
  var hideClass = 'hide';

  function hideCurrentTranslationForms(formsContainer) {
    let currentTranslationForms = formsContainer.find('.translation-forms');
    if (currentTranslationForms.length > 0) {
      let hiddenFormsContainer = $('[data-parent-of="' + currentTranslationForms.attr('id') + '"]');
      currentTranslationForms.find('form').addClass(hideClass);
      hiddenFormsContainer.append(currentTranslationForms);
    }
  }

  function hideCurrentNavigationBar(navigationContainer) {
    let currentNavigationBar = navigationContainer.find('nav');
    if (currentNavigationBar.length > 0) {
      let navIdentifier = currentNavigationBar.attr('data-navigation-of');
      let hiddenNavigationBarContainer = $('[data-navigation-parent-of="' + navIdentifier + '"]');
      currentNavigationBar.addClass(hideClass);
      hiddenNavigationBarContainer.append(currentNavigationBar);
    }
  }

  function highlightDomainFirstPart(showTranslationsFormButton) {
    $('.domain-first-part').removeClass('active');
    let domainFirstPart = $($(showTranslationsFormButton.parents('.subdomains')[0]).prevAll().filter('.domain-first-part'))[0];
    $(domainFirstPart).addClass('active');
  }

  function updateDomainTitle(editTranslationForms) {
    let domainPart = editTranslationForms.parents('.translation-domain').prev();
    let missingTranslationWarning = domainPart.find('.missing-translations-short-message');
    let warningPlaceholder = $('#domain .missing-translations');
    let totalPlaceholder = $('#domain .total-expressions');
    let separator = $('#domain .separator');
    totalPlaceholder.text(editTranslationForms.data('total-translations'));
    if (missingTranslationWarning.length > 0) {
      warningPlaceholder.text(missingTranslationWarning.text());
      separator.removeClass('hide');
    } else {
      warningPlaceholder.text('');
      separator.addClass('hide');
    }
    separator.first().removeClass('hide');

    let domain = $('#domain .name');
    let title = editTranslationForms.attr('data-domain');
    domain.text(title);
  }

  function updateMissingTranslationsMessages(title) {
    let missingTranslationsMessage = title.find('.missing-translations-long-message');
    if (missingTranslationsMessage.text().length > 0) {
      $('.translation-domains .missing-translations-paragraph').text(missingTranslationsMessage.text());
    } else {
      $('.translation-domains .missing-translations-paragraph').text('');
    }
  }

  function updateNavigationBar(translationDomain, editTranslationForms) {
    let navigationContainer = $('.navbar-container:first');
    let navigation = translationDomain.find('nav');

    navigation.parent().attr('data-navigation-parent-of', editTranslationForms.attr('id'));
    navigation.attr('data-navigation-of', editTranslationForms.attr('id'));

    hideCurrentNavigationBar(navigationContainer);

    navigationContainer.append(navigation);
    $(navigationContainer.find('nav')).removeClass(hideClass);

    $('.forms-container + .navbar-container').remove();
    $('.forms-container').after(navigationContainer.clone());

  }

  function updateEditTranslationForms(formsContainer, editTranslationForms) {
    hideCurrentTranslationForms(formsContainer);

    formsContainer.append(editTranslationForms);
    $(formsContainer.find('.translation-forms form')).removeClass(hideClass);
  }

  (() => {
    $('.show-' + buttonSuffix).each((buttonIndex, button) => {
      $(button).click((event) => {
        let showTranslationsFormButton = $(event.target);

        let translationDomain = showTranslationsFormButton.parent();
        let editTranslationForms = translationDomain.find('.translation-forms');
        let formsContainer = $('.forms-container');

        if (editTranslationForms.length === 0) {
          return false;
        }

        highlightDomainFirstPart(showTranslationsFormButton);

        updateDomainTitle(editTranslationForms);

        updateNavigationBar(translationDomain, editTranslationForms);
        updateEditTranslationForms(formsContainer, editTranslationForms);

        callback();
      });
    });

    $('.domain-part .delegate-toggle-messages').each((togglerIndex, toggler) => {
      $(toggler).click((event) => {
        let title = $(event.target);
        if (!$(event.target).hasClass('domain-part')) {
          title = $(event.target).parent();
        }

        updateMissingTranslationsMessages(title);

        let translationDomain = title.next();
        let showMessagesButton = translationDomain.find('.show-' + buttonSuffix);

        showMessagesButton.click();
      });
    });
  })();
}

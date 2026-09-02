/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export default function (): void {
  function updateVisibilityIcons(domainActions: JQuery) {
    const visibilityOffIcon = domainActions.find('.visibility-off');
    const visibilityOnIcon = domainActions.find('.visibility-on');
    const showMessagesButton = domainActions.find('.btn-show-messages');
    const hideMessagesButton = domainActions.find('.btn-hide-messages');
    const expandedMessages = visibilityOffIcon.hasClass('hide');

    if (expandedMessages) {
      visibilityOffIcon.removeClass('hide');
      visibilityOnIcon.addClass('hide');
      showMessagesButton.addClass('hide');
      hideMessagesButton.removeClass('hide');
    } else {
      visibilityOnIcon.removeClass('hide');
      visibilityOffIcon.addClass('hide');
      showMessagesButton.removeClass('hide');
      hideMessagesButton.addClass('hide');
    }
  }

  function updateMissingTranslationsWarning(domainActions: JQuery) {
    const subdomain = domainActions.parent().next('.subdomains');
    const missingTranslations = subdomain.find('[data-missing-translations]');
    let totalMissingTranslations = 0;

    $(missingTranslations).each((index, element) => {
      totalMissingTranslations += parseInt(
        <string>$(element).attr('data-missing-translations'),
        10,
      );
    });

    if (totalMissingTranslations > 0) {
      const missingTranslationsWarning = domainActions.find(
        '.missing-translations',
      );
      let warningMessage = missingTranslationsWarning.text();
      warningMessage = warningMessage.replace(
        '%d',
        <string>(<unknown>totalMissingTranslations),
      );
      missingTranslationsWarning.text(warningMessage);
      missingTranslationsWarning.removeClass('hide');
    }

    return totalMissingTranslations;
  }

  let allDomainsMissingTranslations = 0;

  $('.domain-first-part').each((index, domainToggler) => {
    const domainActions = $(domainToggler).find('.domain-actions');

    allDomainsMissingTranslations += updateMissingTranslationsWarning(
      domainActions,
    );

    $(domainToggler).on('click', (event) => {
      let domainTitle;

      if ($(event.target).hasClass('domain-first-part')) {
        domainTitle = $(event.target);
      } else {
        domainTitle = $(event.target).parent();
      }

      domainTitle.find('i').toggleClass('expanded');
      $(domainTitle.nextAll().filter('.subdomains')[0]).toggleClass('hide');

      updateVisibilityIcons(domainActions);

      event.stopPropagation();

      return false;
    });
  });

  (function defineTranslations(totalTranslations, totalRemainingTranslations) {
    const totalTranslationsTemplate = $('.summary .total-translations').attr(
      'data-template',
    );
    const remainingTranslationsTemplate = <string>(
      $('.summary .total-remaining-translations').attr('data-template')
    );

    if (totalRemainingTranslations > 0) {
      const remainingTranslationsMessage = remainingTranslationsTemplate.replace(
        '%d',
        <string>(<unknown>totalRemainingTranslations),
      );
      $('.total-remaining-translations').text(remainingTranslationsMessage);
      $('.summary .separator').removeClass('hide');
    }

    if (totalTranslationsTemplate) {
      const totalTranslationsMessages = totalTranslationsTemplate.replace(
        '%d',
        <string>(<unknown>totalTranslations),
      );
      $('.summary .total-translations').text(totalTranslationsMessages);
    }
  }($('#jetsContent form').length, allDomainsMissingTranslations));

  $('.domain-actions').on('click', (event) => {
    let domainActions = $(event.target);

    if (!$(event.target).hasClass('domain-actions')) {
      domainActions = $(event.target).parent();
    }

    const domainFirstPart = domainActions.prev();
    domainFirstPart.click();
  });

  $('.btn-expand').on('click', () => {
    $('.domain-first-part').each((index, domainToggler) => {
      const domainTitle = $(domainToggler);
      const isDomainExpanded = domainTitle.find('i').hasClass('expanded');

      if (!isDomainExpanded) {
        $(domainTitle.find('i')).click();
      }
    });
  });

  $('.btn-reduce').on('click', () => {
    $('.domain-first-part').each((index, domainToggler) => {
      const domainTitle = $(domainToggler);
      const isDomainExpanded = domainTitle.find('i').hasClass('expanded');

      if (isDomainExpanded) {
        $(domainTitle.find('i')).click();
      }
    });
  });

  $($('.domain-first-part')[0]).click(); // Expand first domain in tree
  $($('.domain-part .delegate-toggle-messages')[0]).click(); // Show messages of first domain
}

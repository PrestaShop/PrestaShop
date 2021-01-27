/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

export default function (search) {
  $('.reset-translation-value').each((buttonIndex, button) => {
    const $editTranslationForm = $(button).parents('form');
    const defaultTranslationValue = $editTranslationForm.find('*[name=default]').val();

    $(button).click(() => {
      $editTranslationForm.find('*[name=translation_value]').val(defaultTranslationValue);
      $editTranslationForm.submit();
    });
  });

  const showFlashMessageOnEdit = (form) => {
    $(form).submit((event) => {
      event.preventDefault();

      const $editTranslationForm = $(event.target);
      const url = $editTranslationForm.attr('action');

      $.post(url, $editTranslationForm.serialize(), (response) => {
        let flashMessage;

        if (response.successful_update) {
          flashMessage = $editTranslationForm.find('.alert-info');

          // Propagate edition
          const hash = $editTranslationForm.data('hash');
          const $editTranslationForms = $(`[data-hash=${hash}]`);
          const $translationValueFields = $($editTranslationForms.find('textarea'));
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

      return false;
    });
  };

  $('#jetsContent form, .translation-domain form').each((formIndex, form) => {
    showFlashMessageOnEdit(form);
  });
}

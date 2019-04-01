/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const $ = window.$;

export default class SeoPreviewCard {
  constructor(
    seoPreviewCardSelector,
    titleInputSelector,
    urlInputSelector,
    descriptionInputSelector
  ) {
    this.$card = $(seoPreviewCardSelector);

    this.titleInputSelector = titleInputSelector;
    this.urlInputSelector = urlInputSelector;
    this.descriptionInputSelector = descriptionInputSelector;

    $(titleInputSelector).on('input', (event) => this._changeHandler(event));
    $(urlInputSelector).on('input', (event) => this._changeHandler(event));
    $(descriptionInputSelector).on('input', (event) => this._changeHandler(event));
  }

  _changeHandler(event) {
    const langId = $(event.currentTarget).data('lang-id');

    this._refreshCard(langId);
  }

  _refreshCard(langId) {
    this.$card.find('.seo-preview-title').text(this._getTitle(langId));
    this.$card.find('.seo-preview-url').text(this._getUrl(langId));
    this.$card.find('.seo-preview-description').text(this._getDescription(langId));
  }

  _getDescription(langId) {
    let descritpion = this._getLocalizedValue(this.descriptionInputSelector, langId);

    if (descritpion.length > 150) {
      descritpion = descritpion.substr(0, 150) + '...';
    }

    return descritpion;
  }

  _getTitle(langId) {
    let title = this._getLocalizedValue(this.titleInputSelector, langId);

    if (title.length > 70) {
      title = title.substr(0, 70) + '...';
    }

    return title;
  }

  _getUrl(langId) {
    return this._getLocalizedValue(this.urlInputSelector, langId);
  }

  _getLocalizedValue(inputSelector, langId) {
    return $(inputSelector + '[' + langId + ']').val();
  }
}

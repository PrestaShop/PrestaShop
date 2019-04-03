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

/**
 * Component is used to preview SEO results.
 *
 * Usage: Define HTML for SEO card preview with ID and "data-id-lang" attribute
 * that is used as default language in which SEO results are displayed.
 *
 * <div id="mySeoPreviewCard"
 *      class="seo-preview-card"
 *      data-lang-id="1"
 *      data-seo-preview-url-generator="/admin-dev/generate-seo-preview-url"
 * >
 *  <div class="seo-preview-title"></div>
 *  <div class="seo-preview-url"></div>
 *  <div class="seo-preview-description"></div>
 * </div>
 *
 * JS: In Javascript you have to enable component.
 *
 * new SeoPreviewCard(
 *    '#mySeoPreviewCard',                            // SEO preview card selector
 *    'input[name^="category[meta_title]',            // Meta name input selector for SEO preview name
 *    'input[name^="category[link_rewrite]',          // Friendly url input selector for SEO preview url
 *    'textarea[name^="category[meta_description]',   // Meta description input selector for SEO preview title,
 *    'input[name^="category[name]',                  // Name that is used when Meta name is empty
 *    'textarea[name^="category[description]'         // Description is used when Meta description is empty
 * );
 */
export default class SeoPreviewCard {
  constructor(
    seoPreviewCardSelector,
    titleInputSelector,
    urlInputSelector,
    descriptionInputSelector,
    fallbackTitleInputSelector,
    fallbackDescriptionInputSelector,
  ) {
    this.$card = $(seoPreviewCardSelector);

    this.titleInputSelector = titleInputSelector;
    this.urlInputSelector = urlInputSelector;
    this.descriptionInputSelector = descriptionInputSelector;
    this.fallbackTitleInputSelector = fallbackTitleInputSelector;
    this.fallbackDescriptionInputSelector = fallbackDescriptionInputSelector;

    $(titleInputSelector).on('input', (event) => this._changeHandler(event));
    $(urlInputSelector).on('input', (event) => this._changeHandler(event));
    $(descriptionInputSelector).on('input', (event) => this._changeHandler(event));

    $(fallbackTitleInputSelector).on('input', (event) => this._changeHandler(event));
    $(fallbackDescriptionInputSelector).on('input', (event) => this._changeHandler(event));

    this._refreshCard(this.$card.data('lang-id'));

    return {};
  }

  /**
   * Handles even when meta data is changed
   *
   * @param {Event} event
   *
   * @private
   */
  _changeHandler(event) {
    const langId = $(event.currentTarget).data('lang-id');

    this._refreshCard(langId);
  }

  /**
   * Refresh SEO preview card with latest data
   *
   * @param {String} langId
   *
   * @private
   */
  _refreshCard(langId) {
    if (this.request) {
      this.request.abort();
    }

    this.request = $.ajax(this.$card.data('seo-preview-url-generator'), {
      data: {
        'category_id': this.$card.data('resource-id'),
        'lang_id': langId,
        'link_rewrite': this._getUrl(langId),
      },
      contentType: 'json',
    });

    this.request.then((response) => {
      this.$card.find('.seo-preview-title').text(this._getTitle(langId));
      this.$card.find('.seo-preview-url').text(response.preview_url);
      this.$card.find('.seo-preview-description').text(this._getDescription(langId));
    });
  }

  /**
   * Gets description in given language
   *
   * @param {String} langId
   *
   * @private
   */
  _getDescription(langId) {
    let descritpion = this._getLocalizedValue(this.descriptionInputSelector, langId);

    if (0 === descritpion.length) {
      descritpion = this._getLocalizedValue(this.fallbackDescriptionInputSelector, langId);
    }

    if (descritpion.length > 150) {
      descritpion = descritpion.substr(0, 150) + '...';
    }

    return descritpion;
  }

  /**
   * Gets title in given language
   *
   * @param {String} langId
   *
   * @private
   */
  _getTitle(langId) {
    let title = this._getLocalizedValue(this.titleInputSelector, langId);

    if (0 === title.length) {
      title = this._getLocalizedValue(this.fallbackTitleInputSelector, langId);
    }

    if (title.length > 70) {
      title = title.substr(0, 70) + '...';
    }

    return title;
  }

  /**
   * Gets URL for given language
   *
   * @param {String} langId
   *
   * @private
   */
  _getUrl(langId) {
    return this._getLocalizedValue(this.urlInputSelector, langId);
  }

  /**
   *
   *
   * @param {String} inputSelector
   * @param {String} langId
   *
   * @return {String}
   *
   * @private
   */
  _getLocalizedValue(inputSelector, langId) {
    return $(inputSelector + '[' + langId + ']').val();
  }
}

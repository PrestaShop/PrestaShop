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

const {$} = window;

interface TaggableFieldParams {
  tokenFieldSelector: string;
  options: TaggableFieldOptions;
}
interface TaggableFieldOptions {
  /**
   * Tokens (or tags). Can be:
   * - a string with comma-separated values ("one,two,three")
   * - an array of strings (["one","two","three"])
   * - an array of objects ([{ value: "one", label: "Einz" }, { value: "two", label: "Zwei" }])
   * @default []
   */
  tokens?: string | string[],
  /**
   * Maximum number of tokens allowed. 0 = unlimited
   * @default 0
   */
  limit?: number,
  /**
   * Minimum length required for token value.
   * @default 0
   */
  minLength?: number,
  /**
   * Minimum input field width. In pixels.
   * @default 60
   */
  minWidth?: number,
  /**
   * jQuery UI Autocomplete options
   * @default {}
   */
  autocomplete?: any,
  /**
   * Whether to show autocomplete suggestions menu on focus or not. Works only for jQuery UI Autocomplete,
   * as Typeahead has no support for this kind of behavior.
   * @default false
   */
  showAutocompleteOnFocus?: boolean,
  /**
   * Arguments for Twitter Typeahead. The first argument should be an options hash (or null if you want to use the
   * defaults). The second argument should be a dataset. You can add multiple datasets:
   * typeahead: [options, dataset1, dataset2]
   * @default {}
   */
  typeahead?: any,
  /**
   * Whether to turn input into tokens when tokenfield loses focus or not.
   * @default false
   */
  createTokensOnBlur?: boolean,
  /**
   * A character or an array of characters that will trigger token creation on keypress event. Defaults to ',' (comma).
   * Note - this does not affect Enter or Tab keys, as they are handled in the keydown event. The first delimiter will
   * be used as a separator when getting the list of tokens or copy-pasting tokens.
   * @default ','
   */
  delimiter?: string | string[],
  /**
   * Whether to insert spaces after each token when getting a comma-separated list of tokens. This affects both value
   * returned by getTokensList() and the value of the original input field.
   * @default true
   */
  beautify?: boolean,
  /**
   * HTML type attribute for the token input. This is useful for specifying an HTML5 input type like 'email', 'url' or
   * 'tel' which allows mobile browsers to show a specialized virtual keyboard optimized for different types of input.
   * This only sets the type of the visible token input but does not touch the original input field. So you may set
   * the original input to have type="text" but set this inputType option to 'email' if you only want to take advantage
   * of the email style keyboard on mobile, but don't want to enable HTML5 native email validation on the original
   * hidden input.
   * @default 'text'
   */
  inputType?: string,
  /**
   * Limit the number of characters allowed by token.
   * @default 0
   */
  maxCharacters?: number;
}

/**
 * class TaggableField is responsible for providing functionality from bootstrap-tokenfield plugin.
 * It allows to have taggable fields which are split in separate blocks once you click enter. Values originally saved
 * in comma split strings.
 */
export default class TaggableField {
  /**
   * @param {string} tokenFieldSelector -  a selector which is used within jQuery object.
   * @param {object} options - extends basic tokenField behavior with additional options such as minLength, delimiter,
   * allow to add token on focus out action. See bootstrap-tokenfield docs for more information.
   */
  constructor({tokenFieldSelector, options = {}}: TaggableFieldParams) {
    $(tokenFieldSelector).tokenfield(options);

    const maxCharacters: number = options.maxCharacters || 0;

    if (maxCharacters > 0) {
      const $inputFields = $(tokenFieldSelector).siblings('.token-input');
      $inputFields.prop('maxlength', maxCharacters);
    }
  }
}

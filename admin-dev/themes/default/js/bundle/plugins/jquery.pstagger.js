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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

(function ($) {
  let config = null;
  const validateKeyCode = 13;
  let tagsList = [];
  const fullTagsString = null;
  let pstaggerInput = null;
  const defaultConfig = {
    /* Global css config */
    wrapperClassAdditional: '',
    /* Tags part */
    tagsWrapperClassAdditional: '',
    tagClassAdditional: '',
    closingCrossClassAdditionnal: '',
    /* Tag Input part */
    tagInputWrapperClassAdditional: '',
    tagInputClassAdditional: '',
    /* Global configuration */
    delimiter: ' ',
    inputPlaceholder: 'Add tag ...',
    closingCross: true,
    context: null,
    clearAllBtn: false,
    clearAllIconClassAdditional: '',
    clearAllSpanClassAdditional: '',
    /* Callbacks */
    onTagsChanged: null,
    onResetTags: null,
  };
  const immutableConfig = {
    /* Global css config */
    wrapperClass: 'pstaggerWrapper',
    /* Tags part */
    tagsWrapperClass: 'pstaggerTagsWrapper',
    tagClass: 'pstaggerTag',
    /* Tag Input part */
    tagInputWrapperClass: 'pstaggerAddTagWrapper',
    tagInputClass: 'pstaggerAddTagInput',
    clearAllIconClass: '',
    clearAllSpanClass: 'pstaggerResetTagsBtn',
    closingCrossClass: 'pstaggerClosingCross',
  };

  const bindValidationInputEvent = function () {
    // Validate input whenever validateKeyCode is pressed
    pstaggerInput.keypress((event) => {
      if (event.keyCode == validateKeyCode) {
        tagsList = [];
        processInput();
      }
    });
    // If focusout of input, display tagsWrapper if not empty or leave input as is
    pstaggerInput.focusout((event) => {
      // Necessarry to avoid race condition when focusout input because we want to reset :-)
      if ($(`.${immutableConfig.clearAllSpanClass}:hover`).length) {
        return false;
      }
      // Only redisplay tags on focusOut if there's something in tagsList
      if (pstaggerInput.val().length) {
        tagsList = [];
        processInput();
      }
    });
  };

  var processInput = function () {
    const fullTagsStringRaw = pstaggerInput.val();
    const tagsListRaw = fullTagsStringRaw.split(config.delimiter);

    // Check that's not an empty input
    if (fullTagsStringRaw.length) {
      // Loop over each tags we got this round
      for (var key in tagsListRaw) {
        const tagRaw = tagsListRaw[key];

        // No empty values
        if (tagRaw === '') {
          continue;
        }
        // Add tag into persistent list
        tagsList.push(tagRaw);
      }

      let spanTagsHtml = '';

      // Create HTML dom from list of tags we have
      for (key in tagsList) {
        const tag = tagsList[key];
        spanTagsHtml += formatSpanTag(tag);
      }
      //  Delete previous if any, then add recreated html content
      $(`.${immutableConfig.tagsWrapperClass}`).empty().prepend(spanTagsHtml).css('display', 'block');
      // Hide input until user click on tagify_tags_wrapper
      $(`.${immutableConfig.tagInputWrapperClass}`).css('display', 'none');
    } else {
      $(`.${immutableConfig.tagsWrapperClass}`).css('display', 'none');
      $(`.${immutableConfig.tagInputWrapperClass}`).css('display', 'block');
      pstaggerInput.focus();
    }
    // Call the callback ! (if one)
    if (config.onTagsChanged !== null) {
      config.onTagsChanged.call(config.context, tagsList);
    }
  };

  var formatSpanTag = function (tag) {
    let spanTag = `<span class="${immutableConfig.tagClass} ${config.tagClassAdditional}">`
                            + `<span>${
                              $('<div/>').text(tag).html()
                            }</span>`;

    // Add closingCross if set to true
    if (config.closingCross === true) {
      spanTag += `<a class="${immutableConfig.closingCrossClass} ${config.closingCrossClassAdditionnal}" href="#">x</a>`;
    }
    spanTag += '</span>';
    return spanTag;
  };

  const constructTagInputForm = function () {
    // First hide native input
    config.originalInput.css('display', 'none');
    let addClearBtnHtml = '';

    // If reset button required add it following user decription
    if (config.clearAllBtn === true) {
      addClearBtnHtml += `<span class="${immutableConfig.clearAllSpanClass} ${config.clearAllSpanClassAdditional}">`
                                        + `<i class="${immutableConfig.clearAllIconClass} ${config.clearAllIconClassAdditional}">clear</i>`
                                    + '</span>';
      // Bind the click on the reset icon
      bindResetTagsEvent();
    }
    // Add Tagify form after it
    const formHtml = `<div class="${immutableConfig.wrapperClass} ${config.wrapperClassAdditional}">${
      addClearBtnHtml
    }<div class="${immutableConfig.tagsWrapperClass} ${config.tagsWrapperClassAdditional}"></div>`
                        + `<div class="${immutableConfig.tagInputWrapperClass} ${config.tagInputWrapperClassAdditional}">`
                            + `<input class="${immutableConfig.tagInputClass} ${config.tagInputClassAdditional}">`
                        + '</div>'
                        + '</div>';
    // Insert form after the originalInput
    config.originalInput.after(formHtml);
    // Save tagify input in our object
    pstaggerInput = $(`.${immutableConfig.tagInputClass}`);
    // Add placeholder on tagify's input
    pstaggerInput.attr('placeholder', config.inputPlaceholder);
    return true;
  };

  const bindFocusInputEvent = function () {
    // Bind click on tagsWrapper to switch and focus on input
    $(`.${immutableConfig.tagsWrapperClass}`).on('click', (event) => {
      const clickedElementClasses = event.target.className;
      // Regexp to check if not clicked on closingCross to avoid focusing input if so
      const checkClosingCrossRegex = new RegExp(immutableConfig.closingCrossClass, 'g');
      const closingCrossClicked = clickedElementClasses.match(checkClosingCrossRegex);

      if ($(`.${immutableConfig.tagInputWrapperClass}`).is(':hidden') && closingCrossClicked === null) {
        $(`.${immutableConfig.tagsWrapperClass}`).css('display', 'none');
        $(`.${immutableConfig.tagInputWrapperClass}`).css('display', 'block');
        pstaggerInput.focus();
      }
    });
  };

  var bindResetTagsEvent = function () {
    // Use delegate since we bind it before we insert the html in the DOM
    const _this = this;
    $(document).delegate(`.${immutableConfig.clearAllSpanClass}`, 'click', () => {
      resetTags(true);
    });
  };

  var resetTags = function (withCallback) {
    // Empty tags list and tagify input
    tagsList = [];
    pstaggerInput.val('');
    $(`.${immutableConfig.tagsWrapperClass}`).css('display', 'none');
    $(`.${immutableConfig.tagInputWrapperClass}`).css('display', 'block');
    pstaggerInput.focus();
    // Empty existing Tags
    $(`.${immutableConfig.tagClass}`).remove();
    // Call the callback if one !
    if (config.onResetTags !== null && withCallback === true) {
      config.onResetTags.call(config.context);
    }
  };

  const bindClosingCrossEvent = function () {
    $(document).delegate(`.${immutableConfig.closingCrossClass}`, 'click', function (event) {
      const thisTagWrapper = $(this).parent();
      const clickedTagIndex = thisTagWrapper.index();
      // Iterate through tags to reconstruct new pstaggerInput value
      const newInputValue = reconstructInputValFromRemovedTag(clickedTagIndex);
      // Apply new input value
      pstaggerInput.val(newInputValue);
      thisTagWrapper.remove();
      tagsList = [];
      processInput();
    });
  };

  var reconstructInputValFromRemovedTag = function (clickedTagIndex) {
    let finalStr = '';
    $(`.${immutableConfig.tagClass}`).each(function (index, value) {
      // If this is the tag we want to remove then continue else add to return string val
      if (clickedTagIndex == $(this).index()) {
        // jQuery.each() continue;
        return true;
      }
      // Add to return value
      finalStr += ` ${$(this).children().first().text()}`;
    });
    return finalStr;
  };

  const getTagsListOccurencesCount = function () {
    const obj = {};

    for (let i = 0, j = tagsList.length; i < j; i++) {
      obj[tagsList[i]] = (obj[tagsList[i]] || 0) + 1;
    }

    return obj;
  };

  const setConfig = function (givenConfig, originalObject) {
    const finalConfig = {};

    // Loop on each default values, check if one given by user, if so -> override default
    for (const property in defaultConfig) {
      if (givenConfig.hasOwnProperty(property)) {
        finalConfig[property] = givenConfig[property];
      } else {
        finalConfig[property] = defaultConfig[property];
      }
    }
    finalConfig.originalInput = originalObject;
    return finalConfig;
  };

  // jQuery extends function
  $.fn.pstagger = function (_config) {
    config = setConfig(_config, this);
    constructTagInputForm();
    bindValidationInputEvent();
    bindFocusInputEvent();
    bindClosingCrossEvent();

    return {
      resetTags,
    };
  };
}(jQuery));

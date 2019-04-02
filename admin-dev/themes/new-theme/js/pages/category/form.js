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

import TranslatableInput from "../../components/translatable-input";
import ChoiceTable from "../../components/choice-table";
import TextWithLengthCounter from "../../components/form/text-with-length-counter";
import textToLinkRewriteCopier from "../../components/text-to-link-rewrite-copier";
import ChoiceTree from "../../components/form/choice-tree";
import TaggableField from "../../components/taggable-field";
import SeoPreviewCard from "../../components/seo-preview-card";

const $ = window.$;

$(() => {
  const $categoryForm = $('#categoryForm');

  new TranslatableInput();
  new ChoiceTable();
  new TextWithLengthCounter();

  new TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  ['category', 'root_category'].forEach((categoryType) => {
    if ($categoryForm.attr('name') === categoryType) {
      textToLinkRewriteCopier({
        sourceElementSelector: `input[name^="${categoryType}[name]"]`,
        destinationElementSelector: `input[name^="${categoryType}[link_rewrite]"]`,
      });

      new ChoiceTree(`#${categoryType}_id_parent`);
      new ChoiceTree(`#${categoryType}_shop_association`).enableAutoCheckChildren();

      new SeoPreviewCard(
        '#categorySeoPreviewCard',
        `input[name^="${categoryType}[meta_title]`,
        `input[name^="${categoryType}[link_rewrite]`,
        `textarea[name^="${categoryType}[meta_description]`,
        `input[name^="${categoryType}[name]`,
        `textarea[name^="${categoryType}[description]`
      );
    }
  });
});

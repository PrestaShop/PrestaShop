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

import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import MetaPageNameOptionHandler from '@pages/meta/meta-page-name-option-handler';

const {$} = window;

$(() => {
  const meta = new window.prestashop.component.Grid('meta');
  meta.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  meta.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  const helperBlock = new ShowcaseCard('seo-urls-showcase-card');
  helperBlock.addExtension(new ShowcaseCardCloseExtension());

  new window.prestashop.component.TaggableField({
    tokenFieldSelector: 'input.js-taggable-field',
    options: {
      createTokensOnBlur: true,
    },
  });

  new MetaPageNameOptionHandler();

  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
      'TranslatableInput',
      'TextWithRecommendedLengthCounter',
    ],
  );
});

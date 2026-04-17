/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ExchangeRatesUpdateScheduler from '@pages/currency/ExchangeRatesUpdateScheduler';

const {$} = window;

$(() => {
  const currency = new window.prestashop.component.Grid('currency');

  currency.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  currency.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  new ExchangeRatesUpdateScheduler();
});

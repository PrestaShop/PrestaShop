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

import ComponentsMap from '@components/components-map';

import ChangeEvent = JQuery.ChangeEvent;
import TriggeredEvent = JQuery.TriggeredEvent;

export default class DateRange {
  constructor() {
    this.initListeners();
  }

  initListeners(): void {
    $(document).on('change', ComponentsMap.dateRange.unlimitedCheckbox, (e: ChangeEvent) => {
      const $dateRangeContainer = $(e.currentTarget).parents(ComponentsMap.dateRange.container);
      const $endDate = $(ComponentsMap.dateRange.endDate, $dateRangeContainer);
      const {checked} = e.currentTarget as HTMLInputElement;

      if (checked) {
        $endDate.val('');
        $endDate.prop('disabled', true);
      } else {
        if ($endDate.val() === '') {
          $endDate.val($endDate.data('defaultValue'));
        }
        $endDate.prop('disabled', false);
      }
    });

    $(document).on('change dp.change', ComponentsMap.dateRange.endDate, (e: TriggeredEvent) => {
      const $endDate = $(e.currentTarget);
      const $dateRangeContainer = $endDate.parents(ComponentsMap.dateRange.container);
      const $unlimited = $(ComponentsMap.dateRange.unlimitedCheckbox, $dateRangeContainer);
      $unlimited.prop('checked', $endDate.val() === '');
    });
  }
}

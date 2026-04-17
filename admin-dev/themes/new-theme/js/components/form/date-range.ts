/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

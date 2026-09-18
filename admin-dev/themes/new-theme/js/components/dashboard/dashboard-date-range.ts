/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';
import type {PsChart} from '@js/libs/chartjs';

import SubmitEvent = JQuery.SubmitEvent;

export default class DashboardDateRange {
  constructor() {
    $(document).on('submit', ComponentsMap.dashboard.dateRangeForm, (event: SubmitEvent) => this.onSubmit(event));
  }

  private onSubmit(event: SubmitEvent): void {
    const form = event.currentTarget as HTMLFormElement;

    event.preventDefault();

    fetch(form.action, {
      method: form.method,
      body: new FormData(form),
      headers: {'X-Requested-With': 'XMLHttpRequest'},
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Unexpected response status: ${response.status}`);
        }

        return response.text();
      })
      .then((html) => {
        const dashboard = document.querySelector(ComponentsMap.dashboard.container);

        if (dashboard === null) {
          throw new Error('Dashboard container not found');
        }

        dashboard.outerHTML = html;

        const newDashboard = document.querySelector(ComponentsMap.dashboard.container);
        (window as unknown as {psChart: PsChart}).psChart.mountCharts(newDashboard ?? document);
      })
      .catch((error) => {
        // Never leave the date range picker stuck on a failed AJAX refresh (network error,
        // expired CSRF token...): fall back to a normal full-page submit.
        console.warn('[dashboard] AJAX date range refresh failed, falling back to a full submit:', error);
        form.submit();
      });
  }
}

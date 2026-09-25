/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ComponentsMap from '@components/components-map';

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
      // An expired session gets a 302 to the login page rather than a 401; without this, fetch
      // would follow it and resolve with a misleading 200 (the login page HTML). "manual" turns
      // any redirect into an opaque, not-ok response instead, caught by the check below.
      redirect: 'manual',
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
        window.prestashop.instance.dashboardChart.mount(newDashboard ?? document);
      })
      .catch((error) => {
        // Never leave the date range picker stuck on a failed AJAX refresh (network error,
        // expired CSRF token...): fall back to a normal full-page submit.
        console.warn('[dashboard] AJAX date range refresh failed, falling back to a full submit:', error);
        form.submit();
      });
  }
}

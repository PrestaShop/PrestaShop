/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import type {PsChart} from '@js/libs/chartjs';

export default class DashboardChart {
  constructor() {
    (window as unknown as {psChart: PsChart}).psChart.mountCharts();
  }
}

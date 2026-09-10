/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import type {Chart as ChartClass, ChartConfiguration} from 'chart.js';

/**
 * Dashboard chart contract (#41971): a `<canvas data-chart id="X">` auto-mounts from a
 * sibling `<script type="application/json" id="X-data">` holding a plain Chart.js config
 * (`type`/`data`/`options` — see https://www.chartjs.org/docs/latest/), e.g.:
 *
 *   <canvas id="dashtrends-sales" data-chart></canvas>
 *   <script type="application/json" id="dashtrends-sales-data">
 *     {"type": "line", "data": {"labels": [...], "datasets": [{"label": "Sales", "data": [...]}]}}
 *   </script>
 *
 * JSON only, so no callbacks (tooltip/label formatters...). Omit dataset colors to get the
 * PrestaShop palette automatically (psColors, in chartjs.ts). Invalid widgets are skipped
 * with a console.warn, never break the other zones.
 */

interface ChartConfigPayload {
  type: string;
  data: unknown;
  options?: unknown;
}

function isChartConfigPayload(value: unknown): value is ChartConfigPayload {
  return (
    typeof value === 'object'
    && value !== null
    && typeof (value as ChartConfigPayload).type === 'string'
    && (value as ChartConfigPayload).data !== undefined
  );
}

function readConfig(canvas: HTMLCanvasElement): ChartConfiguration | null {
  const script = document.getElementById(`${canvas.id}-data`);

  if (!(script instanceof HTMLScriptElement) || script.type !== 'application/json') {
    console.warn(`[dashboard chart] no JSON config found for canvas #${canvas.id} (expected script#${canvas.id}-data)`);
    return null;
  }

  let payload: unknown;

  try {
    payload = JSON.parse(script.textContent ?? '');
  } catch (error) {
    console.warn(`[dashboard chart] invalid JSON in #${script.id}:`, error);
    return null;
  }

  if (!isChartConfigPayload(payload)) {
    console.warn(`[dashboard chart] #${script.id} is missing "type" or "data"`);
    return null;
  }

  return payload as ChartConfiguration;
}

function mountChart(canvas: HTMLCanvasElement, ChartCtor: typeof ChartClass): void {
  if (!canvas.id) {
    console.warn('[dashboard chart] a [data-chart] canvas is missing an id, skipping');
    return;
  }

  const config = readConfig(canvas);

  if (config === null) {
    return;
  }

  // eslint-disable-next-line no-new
  new ChartCtor(canvas, config);
}

function mountDashboardCharts(ChartCtor: typeof ChartClass): void {
  document
    .querySelectorAll<HTMLCanvasElement>('canvas[data-chart]')
    .forEach((canvas) => mountChart(canvas, ChartCtor));
}

export default mountDashboardCharts;

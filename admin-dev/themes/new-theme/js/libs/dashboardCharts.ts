/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import type {Chart as ChartClass, ChartConfiguration, ChartData} from 'chart.js';

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
 * The canvas id must be unique page-wide (module-prefixed), since the sibling script is
 * looked up with a plain `document.getElementById`. JSON only, so no callbacks (tooltip/label
 * formatters...). Omit dataset colors to get the PrestaShop palette automatically (psColors,
 * in chartjs.ts). Invalid widgets are skipped with a console.warn, never break the other zones.
 */

function isChartConfiguration(value: unknown, ChartCtor: typeof ChartClass): value is ChartConfiguration {
  if (typeof value !== 'object' || value === null) {
    return false;
  }

  const {type, data} = value as {type?: unknown; data?: unknown};

  if (typeof type !== 'string') {
    return false;
  }

  try {
    // Throws on any string that isn't a controller Chart.js actually registered.
    ChartCtor.registry.getController(type);
  } catch {
    return false;
  }

  return typeof data === 'object' && data !== null && Array.isArray((data as ChartData).datasets);
}

function readConfig(canvas: HTMLCanvasElement, ChartCtor: typeof ChartClass): ChartConfiguration | null {
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

  if (!isChartConfiguration(payload, ChartCtor)) {
    console.warn(`[dashboard chart] #${script.id} is not a valid Chart.js configuration`
      + ' (missing/unknown "type" or "data.datasets")');
    return null;
  }

  return payload;
}

function mountChart(canvas: HTMLCanvasElement, ChartCtor: typeof ChartClass): void {
  if (!canvas.id) {
    console.warn('[dashboard chart] a [data-chart] canvas is missing an id, skipping');
    return;
  }

  const config = readConfig(canvas, ChartCtor);

  if (config === null) {
    return;
  }

  // Destroy any previous instance first: a page that re-mounts this canvas after refreshing
  // its HTML (e.g. an AJAX date-range reload) would otherwise hit Chart.js's "Canvas is
  // already in use" error.
  ChartCtor.getChart(canvas)?.destroy();

  try {
    // eslint-disable-next-line no-new
    new ChartCtor(canvas, config);
  } catch (error) {
    console.warn(`[dashboard chart] Chart.js rejected the config of #${canvas.id}:`, error);
  }
}

function mountDashboardCharts(ChartCtor: typeof ChartClass, root: ParentNode = document): void {
  root
    .querySelectorAll<HTMLCanvasElement>('canvas[data-chart]')
    .forEach((canvas) => mountChart(canvas, ChartCtor));
}

export default mountDashboardCharts;

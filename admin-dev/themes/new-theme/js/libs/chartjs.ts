/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import Chart from 'chart.js/auto';
import type {Plugin} from 'chart.js';

/**
 * PrestaShop chart palette, mirroring the modern PrestaShop branding as published on
 * prestashop.com: one entry per `--color-N` custom property backing the `.b-color-*`
 * blocks, in the same order. Most brand colors already exist as design-kit tokens
 * shipped by prestakit (some as the exact hex, some as the nearest step), so values
 * are read from the --cdk-* custom properties and follow the design kit; the hex
 * fallbacks mirror the current token values. Colors with no design-kit counterpart
 * (`paleGray`, `teal`) are hard-coded until a token exists.
 *
 * The pastel brand colors sit below the usual contrast thresholds for thin marks but
 * are kept for brand fidelity: charts must keep legends and tooltips enabled
 * (Chart.js defaults) so series identity never relies on color alone, and line
 * strokes should prefer the darker `teal` / `gray` tokens.
 */
const TOKENS = {
  white: ['--cdk-white', '#ffffff'], // prestashop.com --color-1
  black: ['--cdk-black', '#000000'], // prestashop.com --color-2
  blue: ['--cdk-ocean-blue-500', '#a4dbe8'], // prestashop.com --color-3
  green: ['--cdk-green-100', '#bde9c9'], // prestashop.com --color-4
  purple: ['--cdk-purple-500', '#decde7'], // prestashop.com --color-5
  yellow: ['--cdk-amber-500', '#f8e08e'], // prestashop.com --color-6
  lightGray: ['--cdk-primary-200', '#f7f7f7'], // prestashop.com --color-7 (#f4f4f4)
  gray: ['--cdk-primary-600', '#5e5e5e'], // prestashop.com --color-8 (#595959)
  paleGray: ['', '#dadada'], // prestashop.com --color-9
  teal: ['', '#268095'], // prestashop.com --color-10
  lightBlue: ['--cdk-ocean-blue-50', '#e4f4f8'], // prestashop.com --color-11 (#e8f6f9)
  midGray: ['--cdk-primary-500', '#bbbbbb'], // prestashop.com --color-12
  offWhite: ['--cdk-primary-100', '#fafafa'], // prestashop.com --color-13 (#f7fbfc)
  borderGray: ['--cdk-primary-400', '#dddddd'], // prestashop.com --color-14
} as const;

type TokenName = keyof typeof TOKENS;
type PaletteColors = Record<TokenName, string>;

// Categorical ramp used to auto-color chart series: the brand pastels interleaved
// with the darker accents so every adjacent pair stays distinguishable for
// color-blind readers — the prestashop.com slider sequence (--color-4, -5, -3, -6)
// puts purple next to blue, which protanopia cannot tell apart on chart marks.
// Series identity must still never rely on color alone (legends and tooltips
// stay enabled).
const SERIES_ORDER: TokenName[] = ['green', 'teal', 'blue', 'yellow', 'gray', 'purple'];

let cachedColors: PaletteColors | null = null;

function getColors(): PaletteColors {
  if (cachedColors === null) {
    const styles = getComputedStyle(document.documentElement);
    const colors: Partial<PaletteColors> = {};

    (Object.keys(TOKENS) as TokenName[]).forEach((name) => {
      const [cssVar, fallback] = TOKENS[name];
      colors[name] = (cssVar && styles.getPropertyValue(cssVar).trim()) || fallback;
    });
    cachedColors = colors as PaletteColors;
  }

  return cachedColors;
}

function getSeries(): string[] {
  const colors = getColors();

  return SERIES_ORDER.map((name) => colors[name]);
}

function withAlpha(color: string, alpha: number): string {
  const match = color.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i);

  if (!match) {
    return color;
  }

  let hex = match[1];

  if (hex.length === 3) {
    hex = hex.replace(/./g, '$&$&');
  }

  const red = parseInt(hex.slice(0, 2), 16);
  const green = parseInt(hex.slice(2, 4), 16);
  const blue = parseInt(hex.slice(4, 6), 16);

  return `rgba(${red}, ${green}, ${blue}, ${alpha})`;
}

/**
 * Applies the PrestaShop palette to every chart whose datasets do not define their
 * own colors, mirroring the semantics of the built-in `colors` plugin it replaces:
 * doughnut-like charts are colored per data point, other types per dataset.
 */
/* eslint-disable no-param-reassign */
const psColors: Plugin = {
  id: 'psColors',
  beforeLayout(chart) {
    const {datasets} = chart.config.data;

    if (datasets.some((dataset) => dataset.backgroundColor !== undefined || dataset.borderColor !== undefined)) {
      return;
    }

    const series = getSeries();
    let colorIndex = 0;

    datasets.forEach((dataset, datasetIndex) => {
      const {type} = chart.getDatasetMeta(datasetIndex);

      if (type === 'doughnut' || type === 'pie' || type === 'polarArea') {
        dataset.backgroundColor = dataset.data.map(() => {
          const color = series[colorIndex % series.length];
          colorIndex += 1;

          return color;
        });
      } else {
        const color = series[colorIndex % series.length];
        colorIndex += 1;

        if (type === 'line' || type === 'radar') {
          dataset.borderColor = color;
          dataset.backgroundColor = withAlpha(color, 0.15);
        } else {
          dataset.backgroundColor = color;
          dataset.borderColor = color;
        }
      }
    });
  },
};
/* eslint-enable no-param-reassign */

Chart.defaults.set('plugins.colors', {enabled: false});
Chart.register(psColors);

Chart.defaults.color = getColors().gray;
Chart.defaults.borderColor = getColors().borderGray; // grid lines and axis borders
Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;

const psChart = {
  get colors(): PaletteColors {
    return {...getColors()};
  },
  get series(): string[] {
    return getSeries();
  },
  withAlpha,
};

export type PsChart = typeof psChart;

const globalScope = window as unknown as {Chart: typeof Chart; psChart: PsChart};
globalScope.Chart = Chart;
globalScope.psChart = psChart;

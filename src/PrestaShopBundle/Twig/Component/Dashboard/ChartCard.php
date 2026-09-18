<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Dashboard;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A Card specialized for a Chart.js chart: takes a plain Chart.js config array (not a JSON
 * string), so the caller never has to think about JSON_HEX_TAG/JSON_HEX_AMP escaping itself.
 * `maintainAspectRatio: false` is forced so `height` (px) actually takes effect: Chart.js is
 * responsive by default and otherwise recomputes the height from `aspectRatio`.
 *
 * {{ component('ChartCard', {chartId: 'my-chart', config: chartConfig, height: 300, title: 'Sales'}) }}
 */
#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Dashboard/chart_card.html.twig')]
class ChartCard extends AbstractCard
{
    public string $chartId;
    public string $chartConfig;
    public int $height;

    public function mount(
        string $chartId,
        array $config,
        int $height = 200,
        string $title = '',
        ?string $icon = null,
        ?string $configUrl = null,
        ?string $configLabel = null,
    ): void {
        $this->mountCard($title, $icon, $configUrl, $configLabel);

        $this->chartId = $chartId;
        $this->height = $height;

        $config['options'] = array_merge($config['options'] ?? [], ['maintainAspectRatio' => false]);
        $this->chartConfig = json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP);
    }
}

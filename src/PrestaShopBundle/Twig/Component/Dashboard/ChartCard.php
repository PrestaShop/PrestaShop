<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Dashboard;

use PrestaShopBundle\Twig\Component\AbstractCard;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * A Card specialized for a Chart.js chart: takes a plain Chart.js config array (not a JSON
 * string), so the caller never has to think about JSON_HEX_TAG/JSON_HEX_AMP escaping itself.
 * When `height` (px) is set, `maintainAspectRatio: false` is forced so it actually takes
 * effect (Chart.js is responsive by default and otherwise recomputes the height from
 * `aspectRatio`); leave it out for a chart that sizes itself from its container instead.
 *
 * {{ component('ChartCard', {chartId: 'my-chart', config: chartConfig, height: 300, title: 'Sales'}) }}
 */
#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Dashboard/chart_card.html.twig')]
class ChartCard extends AbstractCard
{
    public string $chartId;
    public string $chartConfig;
    public ?int $height = null;

    public function mount(
        string $chartId,
        array $config,
        ?int $height = null,
        string $title = '',
        ?string $icon = null,
        ?string $configUrl = null,
        ?string $configLabel = null,
    ): void {
        $this->mountCard($title, $icon, $configUrl, $configLabel);

        $this->chartId = $chartId;
        $this->height = $height;

        if (null !== $height) {
            $config['options'] = array_merge($config['options'] ?? [], ['maintainAspectRatio' => false]);
        }

        // INVALID_UTF8_SUBSTITUTE keeps a widget with a garbled string (a product name, a
        // referrer host...) rendering instead of throwing and taking the whole dashboard down;
        // THROW_ON_ERROR still surfaces any other encoding failure instead of silently
        // producing `false`, which json_encode() would otherwise assign to this string property.
        $this->chartConfig = json_encode(
            $config,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR
        );
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

/**
 * Shared header props (title/icon/optional "Configure" link) for card components. Not a
 * component itself: extended by Card and its specializations (Dashboard\ChartCard, ...) so
 * their templates can all extend card.html.twig and rely on the same header markup.
 */
abstract class AbstractCard
{
    public string $title = '';
    public ?string $icon = null;
    public ?string $configUrl = null;
    public ?string $configLabel = null;

    protected function mountCard(string $title, ?string $icon, ?string $configUrl, ?string $configLabel): void
    {
        $this->title = $title;
        $this->icon = $icon;
        $this->configUrl = $configUrl;
        $this->configLabel = $configLabel;
    }
}

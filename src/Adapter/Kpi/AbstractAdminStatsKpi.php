<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base class for KPI tiles whose value is computed by the legacy
 * `AdminStatsController` ajax endpoint and cached in `ConfigurationKPI`.
 *
 * Concrete subclasses only describe the tile (id, icon, color, translation
 * keys, configuration keys); the common HelperKpi wiring lives here so the
 * subclasses do not duplicate `render()`.
 */
abstract class AbstractAdminStatsKpi implements KpiInterface
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly ConfigurationInterface $kpiConfiguration,
        protected readonly string $sourceUrl,
    ) {
    }

    public function render(): string
    {
        $helper = new HelperKpi();
        $helper->id = $this->getId();
        $helper->icon = $this->getIcon();
        $helper->color = $this->getColor();
        $helper->title = $this->getTitle();

        $subtitle = $this->getSubtitle();
        if (null !== $subtitle) {
            $helper->subtitle = $subtitle;
        }

        $value = $this->kpiConfiguration->get($this->getValueConfigurationKey());
        if (false !== $value) {
            $helper->value = $value;
        }

        $helper->source = $this->sourceUrl;

        $expireTimestamp = $this->kpiConfiguration->get($this->getExpireConfigurationKey());
        $helper->refresh = false === $expireTimestamp || (int) $expireTimestamp < time();

        return $helper->generate();
    }

    abstract protected function getId(): string;

    abstract protected function getIcon(): string;

    abstract protected function getColor(): string;

    abstract protected function getTitle(): string;

    abstract protected function getValueConfigurationKey(): string;

    abstract protected function getExpireConfigurationKey(): string;

    /**
     * Optional subtitle (e.g. "30 days"). Override when the KPI needs one.
     */
    protected function getSubtitle(): ?string
    {
        return null;
    }
}

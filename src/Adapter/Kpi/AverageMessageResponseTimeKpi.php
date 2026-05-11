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
 * Renders the average time elapsed between customer messages and employee replies over the last 30 days.
 */
final class AverageMessageResponseTimeKpi implements KpiInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ConfigurationInterface $kpiConfiguration,
        private readonly string $sourceUrl,
    ) {
    }

    public function render(): string
    {
        $helper = new HelperKpi();
        $helper->id = 'box-age';
        $helper->icon = 'access_time';
        $helper->color = 'color2';
        $helper->title = $this->translator->trans('Average Response Time', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('AVG_MSG_RESPONSE_TIME')) {
            $helper->value = $this->kpiConfiguration->get('AVG_MSG_RESPONSE_TIME');
        }

        $helper->source = $this->sourceUrl;
        $expireTimestamp = $this->kpiConfiguration->get('AVG_MSG_RESPONSE_TIME_EXPIRE');
        $helper->refresh = false === $expireTimestamp || (int) $expireTimestamp < time();

        return $helper->generate();
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

/**
 * Renders the average time elapsed between customer messages and employee replies over the last 30 days.
 */
final class AverageMessageResponseTimeKpi extends AbstractAdminStatsKpi
{
    protected function getId(): string
    {
        return 'box-age';
    }

    protected function getIcon(): string
    {
        return 'access_time';
    }

    protected function getColor(): string
    {
        return 'color2';
    }

    protected function getTitle(): string
    {
        return $this->translator->trans('Average Response Time', [], 'Admin.Catalog.Feature');
    }

    protected function getSubtitle(): ?string
    {
        return $this->translator->trans('30 days', [], 'Admin.Global');
    }

    protected function getValueConfigurationKey(): string
    {
        return 'AVG_MSG_RESPONSE_TIME';
    }

    protected function getExpireConfigurationKey(): string
    {
        return 'AVG_MSG_RESPONSE_TIME_EXPIRE';
    }
}

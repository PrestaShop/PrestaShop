<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

/**
 * Renders the average number of messages exchanged per customer service thread over the last 30 days.
 */
final class MessagesPerThreadKpi extends AbstractAdminStatsKpi
{
    protected function getId(): string
    {
        return 'box-messages-per-thread';
    }

    protected function getIcon(): string
    {
        return 'content_copy';
    }

    protected function getColor(): string
    {
        return 'color3';
    }

    protected function getTitle(): string
    {
        return $this->translator->trans('Messages per Thread', [], 'Admin.Catalog.Feature');
    }

    protected function getSubtitle(): ?string
    {
        return $this->translator->trans('30 days', [], 'Admin.Global');
    }

    protected function getValueConfigurationKey(): string
    {
        return 'MESSAGES_PER_THREAD';
    }

    protected function getExpireConfigurationKey(): string
    {
        return 'MESSAGES_PER_THREAD_EXPIRE';
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi;

/**
 * Renders the count of customer service threads still awaiting a reply.
 */
final class PendingDiscussionThreadsKpi extends AbstractAdminStatsKpi
{
    protected function getId(): string
    {
        return 'box-pending-messages';
    }

    protected function getIcon(): string
    {
        return 'mail';
    }

    protected function getColor(): string
    {
        return 'color1';
    }

    protected function getTitle(): string
    {
        return $this->translator->trans('Pending Discussion Threads', [], 'Admin.Catalog.Feature');
    }

    protected function getValueConfigurationKey(): string
    {
        return 'PENDING_MESSAGES';
    }

    protected function getExpireConfigurationKey(): string
    {
        return 'PENDING_MESSAGES_EXPIRE';
    }
}

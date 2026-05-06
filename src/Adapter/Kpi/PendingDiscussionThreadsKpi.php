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
 * Renders the count of customer service threads still awaiting a reply.
 */
final class PendingDiscussionThreadsKpi implements KpiInterface
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
        $helper->id = 'box-pending-messages';
        $helper->icon = 'mail';
        $helper->color = 'color1';
        $helper->title = $this->translator->trans('Pending Discussion Threads', [], 'Admin.Catalog.Feature');

        if (false !== $this->kpiConfiguration->get('PENDING_MESSAGES')) {
            $helper->value = $this->kpiConfiguration->get('PENDING_MESSAGES');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('PENDING_MESSAGES_EXPIRE') < time();

        return $helper->generate();
    }
}

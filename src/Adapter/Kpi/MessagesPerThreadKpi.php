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
 * Renders the average number of messages exchanged per customer service thread over the last 30 days.
 */
final class MessagesPerThreadKpi implements KpiInterface
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
        $helper->id = 'box-messages-per-thread';
        $helper->icon = 'content_copy';
        $helper->color = 'color3';
        $helper->title = $this->translator->trans('Messages per Thread', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('MESSAGES_PER_THREAD')) {
            $helper->value = $this->kpiConfiguration->get('MESSAGES_PER_THREAD');
        }

        $helper->source = $this->sourceUrl;
        $expireTimestamp = $this->kpiConfiguration->get('MESSAGES_PER_THREAD_EXPIRE');
        $helper->refresh = false === $expireTimestamp || (int) $expireTimestamp < time();

        return $helper->generate();
    }
}

<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class NetProfitPerVisitKpi implements KpiInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ConfigurationInterface $configuration,
        private readonly LegacyContext $contextAdapter
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visit';
        $helper->icon = 'account_box';
        $helper->color = 'color3';
        $helper->title = $this->translator->trans('Net Profit per Visit', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Orderscustomers.Feature');

        if ($this->configuration->get('NETPROFIT_VISIT') !== false) {
            $helper->value = $this->configuration->get('NETPROFIT_VISIT');
        }

        $helper->source = $this->contextAdapter->getAdminLink('AdminStats', true, [
            'ajax' => 1,
            'action' => 'getKpi',
            'kpi' => 'netprofit_visit',
        ]);
        $helper->refresh = $this->configuration->get('NETPROFIT_VISIT_EXPIRE') < time();

        return $helper->generate();
    }
}

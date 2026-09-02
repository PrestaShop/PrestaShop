<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class AverageOrderValueKpi implements KpiInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ShopConfigurationInterface $configuration,
        private readonly LegacyContext $contextAdapter
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'account_balance_wallet';
        $helper->color = 'color4';
        $helper->title = $this->translator->trans('Average Order Value', [], 'Admin.Global');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        if ($this->configuration->get('AVG_ORDER_VALUE') !== false) {
            $helper->value = $this->translator->trans(
                '%amount% tax excl.',
                ['%amount%' => $this->configuration->get('AVG_ORDER_VALUE')],
                'Admin.Orderscustomers.Feature'
            );
        }

        $helper->source = $this->contextAdapter->getAdminLink('AdminStats', true, [
            'ajax' => 1,
            'action' => 'getKpi',
            'kpi' => 'average_order_value',
        ]);
        $helper->refresh = $this->configuration->get('AVG_ORDER_VALUE_EXPIRE') < time();

        return $helper->generate();
    }
}

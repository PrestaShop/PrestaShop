<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Renders amount of orders per customer.
 */
final class OrdersPerCustomerKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $kpiConfiguration;

    /**
     * @var string
     */
    private $sourceUrl;

    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-orders';
        $helper->icon = 'shopping_basket';
        $helper->color = 'color4';

        $helper->title = $this->translator->trans('Orders per Customer', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->translator->trans('All time', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('ORDERS_PER_CUSTOMER')) {
            $helper->value = $this->kpiConfiguration->get('ORDERS_PER_CUSTOMER');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('ORDERS_PER_CUSTOMER_EXPIRE') < time();

        return $helper->generate();
    }
}

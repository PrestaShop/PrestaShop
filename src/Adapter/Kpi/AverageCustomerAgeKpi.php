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
 * Renders average age of all customers.
 */
final class AverageCustomerAgeKpi implements KpiInterface
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
        $helper->id = 'box-age';
        $helper->icon = 'calendar_today';
        $helper->color = 'color2';

        $helper->title = $this->translator->trans('Average Age', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->translator->trans('All time', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('AVG_CUSTOMER_AGE')) {
            $helper->value = $this->kpiConfiguration->get('AVG_CUSTOMER_AGE');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('AVG_CUSTOMER_AGE_EXPIRE') < time();

        return $helper->generate();
    }
}

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
 * Renders percent of most common customers gender.
 */
final class MostCommonCustomersGenderKpi implements KpiInterface
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

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     */
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
        $helper->id = 'box-gender';
        $helper->icon = 'person';
        $helper->color = 'color1';
        $helper->title = $this->translator->trans('Customers', [], 'Admin.Global');
        $helper->subtitle = $this->translator->trans('All time', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER')) {
            $helper->value = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER_EXPIRE') < time();

        return $helper->generate();
    }
}

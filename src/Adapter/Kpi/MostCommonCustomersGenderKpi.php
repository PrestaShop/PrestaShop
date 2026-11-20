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
     * @var int
     */
    private $employeeIdLang;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl,
        int $employeeIdLang
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
        $this->employeeIdLang = $employeeIdLang;
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

        $customerMainGender = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER');
        if (isset($customerMainGender[$this->employeeIdLang])) {
            $helper->value = $customerMainGender[$this->employeeIdLang];
        }

        $helper->source = $this->sourceUrl;
        $customerMainGenderExpire = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER_EXPIRE');
        $helper->refresh = ($customerMainGenderExpire[$this->employeeIdLang] ?? false) < time();

        return $helper->generate();
    }
}

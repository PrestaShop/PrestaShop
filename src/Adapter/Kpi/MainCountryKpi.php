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
 * Class MainCountryKpi is an implementation for main countries KPI.
 */
final class MainCountryKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var string
     */
    private $sourceLink;

    /**
     * @var int
     */
    private $employeeIdLang;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     * @param string $sourceLink a link to refresh KPI
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        $sourceLink,
        int $employeeIdLang
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->sourceLink = $sourceLink;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $mainCountry = $this->configuration->get('MAIN_COUNTRY');

        $kpi = new HelperKpi();
        $kpi->context->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'new-theme/template/');
        $kpi->id = 'box-country';
        $kpi->icon = 'home';
        $kpi->color = 'color2';
        $kpi->title = $this->translator->trans('Main Country', [], 'Admin.International.Feature');
        $kpi->subtitle = $this->translator->trans('30 Days', [], 'Admin.Global');

        if (isset($mainCountry[$this->employeeIdLang])) {
            $kpi->value = $mainCountry[$this->employeeIdLang];
        }

        $kpi->source = $this->sourceLink;
        $mainCountryExpire = $this->configuration->get('MAIN_COUNTRY_EXPIRE');
        $kpi->refresh = ($mainCountryExpire[$this->employeeIdLang] ?? false) < time();

        return $kpi->generate();
    }
}

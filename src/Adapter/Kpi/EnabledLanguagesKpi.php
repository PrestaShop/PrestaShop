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
 * Class EnabledLanguagesKpi is an implementation for enabled languages KPI.
 */
final class EnabledLanguagesKpi implements KpiInterface
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
    private $clickLink;

    /**
     * @var string
     */
    private $sourceLink;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     * @param string $clickLink a link for clicking on the KPI
     * @param string $sourceLink a link to refresh KPI
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        $clickLink,
        $sourceLink
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->clickLink = $clickLink;
        $this->sourceLink = $sourceLink;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $enabledLanguages = $this->configuration->get('ENABLED_LANGUAGES');

        $kpi = new HelperKpi();
        $kpi->context->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'new-theme/template/');
        $kpi->id = 'box-languages';
        $kpi->icon = 'mic';
        $kpi->color = 'color1';
        $kpi->href = $this->clickLink;
        $kpi->title = $this->translator->trans('Enabled Languages', [], 'Admin.International.Feature');

        if (false !== $enabledLanguages) {
            $kpi->value = $enabledLanguages;
        }

        $kpi->source = $this->sourceLink;
        $kpi->refresh = $this->configuration->get('ENABLED_LANGUAGES_EXPIRE') < time();

        return $kpi->generate();
    }
}

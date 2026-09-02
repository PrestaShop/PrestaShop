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
 * Class TranslationsKpi is an implementation for translations KPI.
 */
final class TranslationsKpi implements KpiInterface
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
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     * @param string $sourceLink a link to refresh KPI
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        $sourceLink
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->sourceLink = $sourceLink;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $frontOfficeTranslations = $this->configuration->get('FRONTOFFICE_TRANSLATIONS');

        $kpi = new HelperKpi();
        $kpi->context->smarty->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'new-theme/template/');
        $kpi->id = 'box-translations';
        $kpi->icon = 'list';
        $kpi->color = 'color3';
        $kpi->title = $this->translator->trans('Front office Translations', [], 'Admin.International.Feature');

        if (false !== $frontOfficeTranslations) {
            $kpi->value = $frontOfficeTranslations;
        }

        $kpi->source = $this->sourceLink;
        $kpi->refresh = $this->configuration->get('FRONTOFFICE_TRANSLATIONS_EXPIRE') < time();

        return $kpi->generate();
    }
}

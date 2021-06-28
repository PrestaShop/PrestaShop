<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

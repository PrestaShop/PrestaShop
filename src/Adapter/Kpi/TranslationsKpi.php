<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TranslationsKpi is an implementation for translations KPI
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
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        LegacyContext $legacyContext,
        TranslatorInterface $translator,
        ConfigurationInterface $configuration
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $frontOfficeTranslations = $this->configuration->get('FRONTOFFICE_TRANSLATIONS');

        $kpi = new HelperKpi();
        $kpi->id = 'box-translations';
        $kpi->icon = 'list';
        $kpi->color = 'color3';
        $kpi->title = $this->translator->trans('Front office Translations', [], 'Admin.International.Feature');

        if (false !== $frontOfficeTranslations) {
            $kpi->value = $frontOfficeTranslations;
        }

        $params = [
            'ajax' => 1,
            'action' => 'getKpi',
            'kpi' => 'frontoffice_translations',
        ];
        $kpi->source = $this->legacyContext->getAdminLink('AdminStats', true, $params);
        $kpi->refresh = $this->configuration->get('FRONTOFFICE_TRANSLATIONS_EXPIRE') < time();

        return $kpi->generate();
    }
}

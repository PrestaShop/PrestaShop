<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
        $helper->subtitle = $this->translator->trans('All Time', [], 'Admin.Global');

        if (false !== $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER')) {
            $helper->value = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('CUSTOMER_MAIN_GENDER_EXPIRE') < time();

        return $helper->generate();
    }
}

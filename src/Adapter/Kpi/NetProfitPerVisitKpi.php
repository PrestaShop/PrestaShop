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
use PrestaShopBundle\Translation\TranslatorInterface;

/**
 * @internal
 */
final class NetProfitPerVisitKpi implements KpiInterface
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
     * @param string $sourceLink
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        string $sourceLink
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
        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visit';
        $helper->icon = 'account_box';
        $helper->color = 'color3';
        $helper->title = $this->translator->trans('Net Profit per Visit', [], 'Admin.Orderscustomers.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Orderscustomers.Feature');

        if ($this->configuration->get('NETPROFIT_VISIT') !== false) {
            $helper->value = $this->configuration->get('NETPROFIT_VISIT');
        }

        $helper->source = $this->sourceLink;
        $helper->refresh = (bool) ($this->configuration->get('NETPROFIT_VISIT_EXPIRE') < time());

        return $helper->generate();
    }
}

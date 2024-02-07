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
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Cart\CartStatus;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class AbandonedCartKpi implements KpiInterface
{
    public function __construct(
        private readonly LegacyContext $contextAdapter,
        private readonly TranslatorInterface $translator,
        private readonly ConfigurationInterface $configuration,
        private readonly LanguageContext $languageContext,
        private readonly UrlGeneratorInterface $router,
        private readonly FeatureFlagStateCheckerInterface $flagStateChecker,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $dateFormat = $this->languageContext->getDateFormat();

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'remove_shopping_cart';
        $helper->color = 'color2';
        $helper->title = $this->translator->trans('Abandoned Carts', [], 'Admin.Global');
        $helper->subtitle = $this->translator->trans('From %date1% to %date2%', [
            '%date1%' => date($dateFormat, strtotime('-2 day')),
            '%date2%' => date($dateFormat, strtotime('-1 day')),
        ], 'Admin.Orderscustomers.Feature');

        if ($this->flagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CARTS)) {
            $helper->href = $this->router->generate('admin_carts_index', [
                'cart[filters][status]' => CartStatus::ABANDONED_CART,
            ]);
        } else {
            $helper->href = $this->contextAdapter->getAdminLink('AdminCarts', true, [
                'action' => 'filterOnlyAbandonedCarts',
            ]);
        }

        if ($this->configuration->get('ABANDONED_CARTS') !== false) {
            $helper->value = $this->configuration->get('ABANDONED_CARTS');
        }

        $helper->source = $this->contextAdapter->getAdminLink('AdminStats', true, [
            'ajax' => 1,
            'action' => 'getKpi',
            'kpi' => 'abandoned_cart',
        ]);
        $helper->refresh = $this->configuration->get('ABANDONED_CARTS_EXPIRE') < time();

        return $helper->generate();
    }
}

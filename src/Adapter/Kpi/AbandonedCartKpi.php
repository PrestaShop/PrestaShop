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
use PrestaShop\PrestaShop\Core\Domain\Cart\CartStatusType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class AbandonedCartKpi implements KpiInterface
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
    private $contextAdapter;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var FeatureFlagRepository
     */
    private $featureFlag;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $configuration
     * @param LegacyContext $contextAdapter
     * @param UrlGeneratorInterface $router
     * @param FeatureFlagRepository $featureFlag
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $configuration,
        LegacyContext $contextAdapter,
        UrlGeneratorInterface $router,
        FeatureFlagRepository $featureFlag
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
        $this->contextAdapter = $contextAdapter;
        $this->router = $router;
        $this->featureFlag = $featureFlag;
        $this->dateFormat = $this->contextAdapter->getLanguage()->date_format_lite;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'remove_shopping_cart';
        $helper->color = 'color2';
        $helper->title = $this->translator->trans('Abandoned Carts', [], 'Admin.Global');
        $helper->subtitle = $this->translator->trans('From %date1% to %date2%', [
            '%date1%' => date($this->dateFormat, strtotime('-2 day')),
            '%date2%' => date($this->dateFormat, strtotime('-1 day')),
        ], 'Admin.Orderscustomers.Feature');
        $helper->href = $this->contextAdapter->getAdminLink('AdminCarts', true, [
            'action' => 'filterOnlyAbandonedCarts',
        ]);

        if ($this->featureFlag->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CARTS_INDEX)) {
            $helper->href = $this->router->generate('admin_carts_index', [
                'cart[filters][status]' => CartStatusType::ABANDONED_CART,
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
        $helper->refresh = (bool) ($this->configuration->get('ABANDONED_CARTS_EXPIRE') < time());

        return $helper->generate();
    }
}

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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin;

use Doctrine\ORM\EntityManager;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Service\Multistore\CustomizedConfigurationChecker;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class is responsible for preparing multistore elements that will be displayed in the BO
 * It does not control or render a BO page, the items being output are used inside other BO pages.
 */
class MultistoreController extends FrameworkBundleAdminController
{
    /**
     * @var MultistoreFeature
     */
    public $multiStoreFeature;

    /**
     * @var Context
     */
    public $multiStoreContext;

    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * @var ProductRepository
     */
    public $productRepository;

    /**
     * This method returns a Response object containing the multistore header displayed at the top of migrated pages
     *
     * @param bool $lockedToAllShopContext
     *
     * @return Response
     */
    public function header(bool $lockedToAllShopContext): Response
    {
        if (!$this->multiStoreFeature->isUsed()) {
            return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
                'isMultistoreUsed' => false,
            ]);
        }

        $groupList = [];
        if (!$lockedToAllShopContext) {
            $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);
        }

        return $this->renderHeader('@PrestaShop/Admin/Multistore/header.html.twig', [
            'groupList' => $groupList,
            'lockedToAllShopContext' => $lockedToAllShopContext,
        ]);
    }

    /**
     * This method returns a Response object containing the multistore header displayed at the top of product page
     *
     * @param int $productId
     *
     * @return Response
     */
    public function productHeader(int $productId): Response
    {
        $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);

        // Filter shops that are not associated to product
        $productShops = $this->productRepository->getAssociatedShopIds(new ProductId($productId));

        if (!empty($productShops)) {
            $productShopIds = array_map(function (ShopId $shopId) {
                return $shopId->getValue();
            }, $productShops);

            /** @var ShopGroup $shopGroup */
            foreach ($groupList as $shopGroup) {
                /** @var Shop $shop */
                foreach ($shopGroup->getShops() as $shop) {
                    if (!in_array($shop->getId(), $productShopIds)) {
                        $shopGroup->getShops()->removeElement($shop);
                    }
                }
            }
        }

        return $this->renderHeader('@PrestaShop/Admin/Multistore/product_header.html.twig', [
            'groupList' => $groupList,
            'productId' => $productId,
        ]);
    }

    /**
     * @param ShopConfigurationInterface $configuration
     * @param string $configurationKey
     *
     * @return Response
     */
    public function configurationDropdown(ShopConfigurationInterface $configuration, string $configurationKey): Response
    {
        $shopGroups = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);
        $shopCustomizationChecker = $this->get('prestashop.multistore.customized_configuration_checker');

        if ($this->multiStoreContext->isAllShopContext()) {
            $dropdownData = $this->allShopDropdown($shopCustomizationChecker, $shopGroups, $configurationKey);
        } else {
            $dropdownData = $this->groupShopDropdown($shopCustomizationChecker, $shopGroups, $configurationKey);
        }

        if (!$dropdownData['shouldDisplayDropdown']) {
            // no dropdown is displayed if no shop overrides this configuration value, so we return an empty response.
            return new Response();
        }

        return $this->render('@PrestaShop/Admin/Multistore/dropdown.html.twig', $dropdownData['templateData']);
    }

    /**
     * Gathers data for multistore dropdown in group shop context
     *
     * @param CustomizedConfigurationChecker $shopCustomizationChecker
     * @param array $shopGroups
     * @param string $configurationKey
     *
     * @return array
     */
    private function groupShopDropdown(CustomizedConfigurationChecker $shopCustomizationChecker, array $shopGroups, string $configurationKey): array
    {
        $groupList = [];
        $shouldDisplayDropdown = false;

        foreach ($shopGroups as $key => $group) {
            if ($this->shouldIncludeGroupShop($group)) {
                $groupList[] = $group;
            }
            if (
                $group->getId() === $this->multiStoreContext->getContextShopGroup()->id
                && !$shouldDisplayDropdown
            ) {
                foreach ($group->getShops() as $shop) {
                    if ($shopCustomizationChecker->isConfigurationCustomizedForThisShop($configurationKey, $shop, true)) {
                        $shouldDisplayDropdown = true;
                        break;
                    }
                }
            }
        }

        return [
            'shouldDisplayDropdown' => $shouldDisplayDropdown,
            'templateData' => [
                'groupList' => $groupList,
                'shopCustomizationChecker' => $shopCustomizationChecker,
                'configurationKey' => $configurationKey,
                'isGroupShopContext' => true,
            ],
        ];
    }

    /**
     * Gathers data for multistore dropdown in all shop context
     *
     * @param CustomizedConfigurationChecker $shopCustomizationChecker
     * @param array $shopGroups
     * @param string $configurationKey
     *
     * @return array
     */
    private function allShopDropdown(CustomizedConfigurationChecker $shopCustomizationChecker, array $shopGroups, string $configurationKey): array
    {
        $groupList = [];
        $shouldDisplayDropdown = false;
        foreach ($shopGroups as $key => $group) {
            if ($this->shouldIncludeGroupShop($group)) {
                $groupList[] = $group;
            }
            if ($shouldDisplayDropdown) {
                continue;
            }
            foreach ($group->getShops() as $shop) {
                if ($shopCustomizationChecker->isConfigurationCustomizedForThisShop($configurationKey, $shop, false)) {
                    $shouldDisplayDropdown = true;
                    break;
                }
            }
        }

        return [
            'shouldDisplayDropdown' => $shouldDisplayDropdown,
            'templateData' => [
                'groupList' => $groupList,
                'shopCustomizationChecker' => $shopCustomizationChecker,
                'configurationKey' => $configurationKey,
                'isGroupShopContext' => false,
            ],
        ];
    }

    /**
     * @param ShopGroup $group
     *
     * @return bool
     */
    private function shouldIncludeGroupShop(ShopGroup $group): bool
    {
        // group shop is only included if we are in all shop context or in group context when this group is the current context
        if (count($group->getShops()) > 0
            && (
                $this->multiStoreContext->isAllShopContext()
                || (
                    $this->multiStoreContext->isGroupShopContext()
                    && $group->getId() === $this->multiStoreContext->getContextShopGroup()->id
                )
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param string $headerTemplate Header template to use
     * @param array $templateVars Additional template variables that can add new vars or overwrite default ones
     *
     * @return Response
     */
    private function renderHeader(string $headerTemplate, array $templateVars): Response
    {
        $colorBrightnessCalculator = $this->get(ColorBrightnessCalculator::class);
        $isAllShopContext = $this->multiStoreContext->isAllShopContext();
        $isShopContext = $this->multiStoreContext->isShopContext();
        $colorConfigLink = false;

        if ($isShopContext) {
            $currentContext = $this->entityManager->getRepository(Shop::class)->findOneBy(['id' => $this->multiStoreContext->getContextShopID()]);
            $colorConfigLink = $this->getAdminLink('AdminShop', ['shop_id' => $currentContext->getId(), 'updateshop' => true]);
        } elseif (!$isAllShopContext) {
            $shopGroupLegacy = $this->multiStoreContext->getContextShopGroup();
            $currentContext = $this->entityManager->getRepository(ShopGroup::class)->findOneBy(['id' => $shopGroupLegacy->id]);
            $colorConfigLink = $this->getAdminLink('AdminShopGroup', ['id_shop_group' => $currentContext->getId(), 'updateshop_group' => true]);
        } else {
            // use ShopGroup object as the container for "all shops" context so that it can be used transparently in twig
            $currentContext = new ShopGroup();
            $currentContext->setName($this->trans('All stores', 'Admin.Global'));
            $currentContext->setColor('');
        }

        return $this->render($headerTemplate, array_merge([
            'isMultistoreUsed' => $this->multiStoreFeature->isUsed(),
            'currentContext' => $currentContext,
            'groupList' => [],
            'isShopContext' => $isShopContext,
            'link' => $this->getContext()->link,
            'isTitleDark' => empty($currentContext->getColor()) ? true : $colorBrightnessCalculator->isBright($currentContext->getColor()),
            'isAllShopContext' => $isAllShopContext,
            'isGroupContext' => $this->multiStoreContext->isGroupShopContext(),
            'lockedToAllShopContext' => false,
            'colorConfigLink' => !$isAllShopContext && empty($currentContext->getColor()) ? $colorConfigLink : false,
        ], $templateVars));
    }
}

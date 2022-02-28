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
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
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
    public $multistoreFeature;

    /**
     * @var Context
     */
    public $multistoreContext;

    /**
     * @var EntityManager
     */
    public $entityManager;

    /**
     * This methods returns a Response object containing the multistore header displayed at the top of migrated pages
     *
     * @param bool $lockedToAllShopContext
     *
     * @return Response
     */
    public function header(bool $lockedToAllShopContext): Response
    {
        if (!$this->multistoreFeature->isUsed()) {
            return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
                'isMultistoreUsed' => false,
            ]);
        }

        $isAllShopContext = $this->multistoreContext->isAllShopContext();
        $isShopContext = $this->multistoreContext->isShopContext();
        $colorConfigLink = false;

        if ($isShopContext) {
            $currentContext = $this->entityManager->getRepository(Shop::class)->findOneBy(['id' => $this->multistoreContext->getContextShopID()]);
            $colorConfigLink = $this->getAdminLink('AdminShop', ['shop_id' => $currentContext->getId(), 'updateshop' => true]);
        } elseif (!$isAllShopContext) {
            $shopGroupLegacy = $this->multistoreContext->getContextShopGroup();
            $currentContext = $this->entityManager->getRepository(ShopGroup::class)->findOneBy(['id' => $shopGroupLegacy->id]);
            $colorConfigLink = $this->getAdminLink('AdminShopGroup', ['id_shop_group' => $currentContext->getId(), 'updateshop_group' => true]);
        } else {
            // use ShopGroup object as a the container for "all shops" context so that it can be used transparently in twig
            $currentContext = new ShopGroup();
            $currentContext->setName($this->trans('All shops', 'Admin.Global'));
            $currentContext->setColor('');
        }

        $groupList = [];
        if (!$lockedToAllShopContext) {
            $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);
        }

        $colorBrightnessCalculator = $this->get('prestashop.core.util.color_brightness_calculator');

        return $this->render('@PrestaShop/Admin/Multistore/header.html.twig', [
            'isMultistoreUsed' => $this->multistoreFeature->isUsed(),
            'currentContext' => $currentContext,
            'groupList' => $groupList,
            'isShopContext' => $isShopContext,
            'link' => $this->getContext()->link,
            'isTitleDark' => empty($currentContext->getColor()) ? true : $colorBrightnessCalculator->isBright($currentContext->getColor()),
            'isAllShopContext' => $isAllShopContext,
            'isGroupContext' => $this->multistoreContext->isGroupShopContext(),
            'lockedToAllShopContext' => $lockedToAllShopContext,
            'colorConfigLink' => !$isAllShopContext && empty($currentContext->getColor()) ? $colorConfigLink : false,
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

        if ($this->multistoreContext->isAllShopContext()) {
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
                $group->getId() === $this->multistoreContext->getContextShopGroup()->id
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
                $this->multistoreContext->isAllShopContext()
                || (
                    $this->multistoreContext->isGroupShopContext()
                    && $group->getId() === $this->multistoreContext->getContextShopGroup()->id
                )
            )
        ) {
            return true;
        }

        return false;
    }
}

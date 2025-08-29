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

namespace PrestaShopBundle\Service\Form;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Service\Multistore\CustomizedConfigurationChecker;
use Twig\Environment;

/**
 * Renders the multishop configuration dropdown for a specific configuration key, the dropdown content
 * is dynamic depending on which configuration is passed and if it has been overridden in group shop or shops.
 */
class MultistoreConfigurationDropdownRenderer
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ShopContext $shopContext,
        private readonly CustomizedConfigurationChecker $customizedConfigurationChecker,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function renderDropdown(string $configurationKey): string
    {
        $shopGroups = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);

        if ($this->shopContext->getShopConstraint()->forAllShops()) {
            $dropdownData = $this->allShopDropdown($this->customizedConfigurationChecker, $shopGroups, $configurationKey);
        } else {
            $dropdownData = $this->groupShopDropdown($this->customizedConfigurationChecker, $shopGroups, $configurationKey);
        }

        if (!$dropdownData['shouldDisplayDropdown']) {
            // No dropdown is displayed if no shop overrides this configuration value, so we return an empty response.
            return '';
        }

        return $this->twig->render('@PrestaShop/Admin/Component/MultiShop/dropdown.html.twig', $dropdownData['templateData']);
    }

    /**
     * Gathers data for multistore dropdown in group shop context
     *
     * @param CustomizedConfigurationChecker $shopCustomizationChecker
     * @param ShopGroup[] $shopGroups
     * @param string $configurationKey
     *
     * @return array
     */
    private function groupShopDropdown(CustomizedConfigurationChecker $shopCustomizationChecker, array $shopGroups, string $configurationKey): array
    {
        $groupList = [];
        $shouldDisplayDropdown = false;

        foreach ($shopGroups as $group) {
            if ($this->shouldIncludeGroupShop($group)) {
                $groupList[] = $group;
            }
            if ($group->getId() === $this->shopContext->getShopConstraint()->getShopGroupId()?->getValue() && !$shouldDisplayDropdown) {
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
     * @param ShopGroup[] $shopGroups
     * @param string $configurationKey
     *
     * @return array
     */
    private function allShopDropdown(CustomizedConfigurationChecker $shopCustomizationChecker, array $shopGroups, string $configurationKey): array
    {
        $groupList = [];
        $shouldDisplayDropdown = false;
        foreach ($shopGroups as $group) {
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

    private function shouldIncludeGroupShop(ShopGroup $group): bool
    {
        // group shop is only included if we are in all shop context or in group context when this group is the current context
        if (count($group->getShops()) > 0
            && (
                $this->shopContext->getShopConstraint()->forAllShops()
                || $group->getId() === $this->shopContext->getShopConstraint()->getShopGroupId()?->getValue()
            )
        ) {
            return true;
        }

        return false;
    }
}

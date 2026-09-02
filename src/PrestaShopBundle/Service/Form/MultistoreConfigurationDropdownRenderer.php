<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

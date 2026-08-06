<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Service\Form;

use PrestaShop\PrestaShop\Core\Feature\ShopModeFeature;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Contracts\Service\ResetInterface;

final class ImprovedB2bTabsToggler
{
    /**
     * List of tabs that should be enabled/disabled depending on the feature flag 'improved_b2b'.
     */
    public const TAB_CLASS_NAMES = [
        'AdminBusinessEntity',
        'AdminBusinessEntities',
        'AdminCustomersB2B',
    ];

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
        private readonly ShopModeFeature $shopModeFeature,
        private readonly TabRepository $tabRepository,
    ) {
    }

    public function sync(): void
    {
        if ($this->featureFlagChecker instanceof ResetInterface) {
            $this->featureFlagChecker->reset(); // refresh FeatureFlagChecker cache
        }

        $shouldEnable =
            $this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_B2B)
            && $this->shopModeFeature->isB2BShopModeEnable();

        $this->setTabsActive($shouldEnable);
    }

    private function setTabsActive(bool $active): void
    {
        foreach (self::TAB_CLASS_NAMES as $tabClassName) {
            $this->tabRepository->changeStatusByClassName($tabClassName, $active);
        }
    }
}

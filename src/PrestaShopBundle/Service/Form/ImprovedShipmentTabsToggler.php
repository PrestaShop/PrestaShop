<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Form;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Keeps the "Improve > Shipping > Shipments" menu entry in sync with the improved shipment feature
 * flag: no shipment is ever created while the flag is off, so the listing would only ever be empty.
 */
final class ImprovedShipmentTabsToggler
{
    /**
     * List of tabs that should be enabled/disabled depending on the feature flag 'improved_shipment'.
     */
    public const TAB_CLASS_NAMES = [
        'AdminShipments',
    ];

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
        private readonly TabRepository $tabRepository,
    ) {
    }

    /**
     * @throws OptimisticLockException if a tab was concurrently modified
     * @throws ORMException if the tabs cannot be persisted
     */
    public function sync(): void
    {
        if ($this->featureFlagChecker instanceof ResetInterface) {
            $this->featureFlagChecker->reset(); // refresh FeatureFlagChecker cache
        }

        $this->setTabsActive($this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT));
    }

    /**
     * @throws OptimisticLockException if a tab was concurrently modified
     * @throws ORMException if the tabs cannot be persisted
     */
    private function setTabsActive(bool $active): void
    {
        foreach (self::TAB_CLASS_NAMES as $tabClassName) {
            $this->tabRepository->changeStatusByClassName($tabClassName, $active);
        }
    }
}

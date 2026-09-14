<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Service\Form\ImprovedShipmentTabsToggler;

/**
 * Mirrors ImprovedB2bFeatureFlagListener: toggling the flag re-syncs the tabs it governs.
 */
final class ImprovedShipmentFeatureFlagListener
{
    public function __construct(
        private readonly ImprovedShipmentTabsToggler $toggler,
    ) {
    }

    /**
     * The tab sync flushes, and postUpdate is dispatched from inside the enclosing flush. That nested
     * flush ends on UnitOfWork::postCommitCleanup(), which wipes the change sets the outer flush has
     * not consumed yet, so any entity still queued behind this one would silently skip its UPDATE.
     * Feature flags are saved one per flush (FeatureFlagRepository::enable()/disable()), which is what
     * keeps this safe — a caller flushing a feature flag alongside other dirty entities would not be.
     *
     * @throws OptimisticLockException if a tab was concurrently modified
     * @throws ORMException if the tabs cannot be persisted
     */
    public function postUpdate(FeatureFlag $featureFlag, PostUpdateEventArgs $event): void
    {
        if ($featureFlag->getName() !== FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT) {
            return;
        }

        $this->toggler->sync();
    }
}

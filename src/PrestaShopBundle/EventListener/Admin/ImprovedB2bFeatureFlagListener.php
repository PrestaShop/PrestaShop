<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Admin;

use Doctrine\ORM\Event\PostUpdateEventArgs;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Service\Form\ImprovedB2bTabsToggler;

final class ImprovedB2bFeatureFlagListener
{
    public function __construct(
        private readonly ImprovedB2bTabsToggler $toggler,
    ) {
    }

    public function postUpdate(FeatureFlag $featureFlag, PostUpdateEventArgs $event): void
    {
        if ($featureFlag->getName() !== FeatureFlagSettings::FEATURE_FLAG_IMPROVED_B2B) {
            return;
        }

        $this->toggler->sync();
    }
}

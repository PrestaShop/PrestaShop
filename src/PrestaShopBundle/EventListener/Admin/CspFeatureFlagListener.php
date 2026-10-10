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
use PrestaShopBundle\Service\Csp\CspTabToggler;

final class CspFeatureFlagListener
{
    public function __construct(
        private readonly CspTabToggler $toggler,
    ) {
    }

    public function postUpdate(FeatureFlag $featureFlag, PostUpdateEventArgs $event): void
    {
        if ($featureFlag->getName() !== FeatureFlagSettings::FEATURE_FLAG_CSP) {
            return;
        }

        $this->toggler->sync();
    }
}

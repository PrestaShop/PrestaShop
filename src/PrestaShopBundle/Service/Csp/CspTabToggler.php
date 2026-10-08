<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Csp;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Enables or disables the "Content Security Policy" tab depending on the 'csp' feature flag.
 */
final class CspTabToggler
{
    public const TAB_CLASS_NAME = 'AdminSecurityHeaders';

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
        private readonly TabRepository $tabRepository,
    ) {
    }

    public function sync(): void
    {
        if ($this->featureFlagChecker instanceof ResetInterface) {
            $this->featureFlagChecker->reset(); // refresh FeatureFlagChecker cache
        }

        $this->tabRepository->changeStatusByClassName(
            self::TAB_CLASS_NAME,
            $this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)
        );
    }
}
